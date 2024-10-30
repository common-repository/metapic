(function ($) {
	$(document).ready(function () {
		var editContainer = $(".mtpc-deeplinking"),
			editLink = $(".edit-deeplink-status"),
			checkValue,
			checkBox = $("#deeplink-status-check"),
			hidden = $("#deeplink-status-auto"),
			originalValue = (checkBox.is(":checked")) ? 1 : 0;

		editLink.on("click", function (e) {
			e.preventDefault();
			$(this).hide();
			$($(this).attr("href")).slideDown("fast");
		});

		$(".save-deeplink-status, .cancel-deeplink-status").on("click", function (e) {
			e.preventDefault();
			var clickedElement = $(this);
			checkValue = (checkBox.is(":checked")) ? 1 : 0;

			$($(this).attr("href")).slideUp("fast", function () {
			});

			editLink.show();
			if (clickedElement.hasClass("save-deeplink-status")) {
				editContainer.find(".deeplink-status-text").hide();
				editContainer.find(".status-" + checkValue.toString()).show();
				hidden.val(checkValue);
				originalValue = checkValue;
			} else {
				if (clickedElement.hasClass("cancel-deeplink-status")) {
					hidden.val(originalValue);
					checkBox.prop("checked", originalValue);
				}
			}
		});

		$("#metapic-help-button").on("click", function (e) {
			e.preventDefault();
			var tokenUrl = $(this).data("tokenUrl");
			var token = $(this).data("accessToken");
			var baseUrl = $(this).data("baseUrl");

			$.event.trigger({
				type: "metapic",
				baseUrl: baseUrl,
				startPage: "dashboard",
				hideSidebar: true,
				randomKey: token
			});
		});

		$('.collapse_block').click(function(){
			$(this).find('.collapse').slideToggle();
		});

		function change_href(dom, link){
			var links = dom.select('a');
			links.forEach(function (el) {
				dom.removeClass(el, 'mtpc-load');
				dom.addClass(el, 'mtpc-link');
				dom.setAttrib(el, 'href', link.after);
				dom.setAttrib(el, 'data-metapic-link-url', link.after);
			});
		}

		function tagContent(id, editor, client_id) {
			var links, hrefs, dom, editor_content;

			if (!editors[id].taggingContent) {

				if(wp_mtpc.wpVersion >= '5.2'){
					editor_content = editor.attributes.content;
					let links = editor_content.match(/<a href="(.*?)">(.*?)<\/a>/);
					if(links){
						hrefs = [links[1]];
					}
				} else {
					dom = editor.dom;
					var dom_select = dom.select('a');

					links = $(dom_select).filter('a:not(.mtpc-link,.mtpc-fail,.mtpc-fail-temp)');
					links = links.filter(function (index) {
						var href = $.trim($(this).attr('href'));
						return href !== '' && href.indexOf('_wp_link_placeholder') === -1
					});
					console.log("TAGGING CONTENT", editors[id].taggingContent, dom_select);
					if (links.length > 0) {
						editors[id].taggingContent = true;

						hrefs = links.map(function (index, element) {
							return element.href
						}).get();
					}
				}
				if (hrefs) {
					$.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {action: 'mtpc_deeplink', links: hrefs},
						dataType: 'json',
						beforeSend: function () {
							if (links) {
								links.addClass('mtpc-load');
							}
							wp.data.dispatch('core/editor').updateBlockAttributes(client_id, {className: 'mtpc-load'});
						}
					})
						.done(function (responseLinks) {
							const client_id = wp.data.select('core/editor').getSelectedBlock().clientId;
							wp.data.dispatch('core/editor').updateBlockAttributes(client_id, {className: ''});

							var previewLink = $('.wp-link-preview a');
							var components_external = $('.components-external-link');
							var href = '';
							if (Array.isArray(responseLinks.content)) {

								responseLinks.content.forEach(function (link) {
									href = hrefs[0];
									link.before = decodeURI(link.before.toLowerCase());
									link.before = link.before.replace(/\/\s*$/, "");
									href = href.replace(/\/\s*$/, "");
									if (link.before === href) {
										console.log("Link match!");
										if (link.status === 'success') {
											if(wp_mtpc.wpVersion < '5.2') {
												change_href(dom, link);
												links.removeClass('mtpc-load');
											}

											previewLink.attr('href', link.after);
											previewLink.text(link.after.replace('http://', ''));

											components_external.attr('href', link.after);
											components_external.text(link.after.replace('http://', ''));

											const content = wp.data.select('core/editor').getSelectedBlock().attributes.content;
											const content_replace = content.replace(/<a\s(?:class\=\"mtpc-load\"\s|)href="(.*?)"/g, '<a class="mtpc-link" data-metapic-link-url="' + link.after + '" url="' + link.after + '" href="' + link.after + '"');
											wp.data.dispatch('core/editor').updateBlockAttributes(client_id, {content: content_replace});

										} else {
											links.forEach(function (el) {
												console.log("Linking failed");
												dom.setAttrib(el, 'data-metapic-link-url', link.before);
												dom.addClass(el, 'mtpc-fail')
											})
										}

									}

								})
								//tinyEditor.fire('change')
							} else {
								links.forEach(function (el) {
									dom.addClass(el, 'mtpc-fail')
								});
								//links.addClass('mtpc-fail-temp')
							}
						})
						.fail(function () {
							links.addClass('mtpc-fail-temp')
						})
						.always(function () {
							if(wp_mtpc.wpVersion < '5.2') {
								links.removeClass('mtpc-load');
							}
							editors[id].taggingContent = false
						})
				}
			}
		}

		var editors = {};

		if (typeof tinymce !== 'undefined') {
			setInterval(function () {
				tinymce.editors.forEach(function (editor) {
					if (editor.inline === true && !editors.hasOwnProperty(editor.id)) {
						console.log("Adding editor");
						editors[editor.id] = {
							editor: editor,
							taggingContent: false
						};
						setInterval(function () {
							tagContent(editor.id, editor);
						}, 300);

						tagContent(editor.id, editor);
					}
				});

				if(wp_mtpc.wpVersion >= '5.2.3'){
					let client_id = wp.data.select( 'core/editor' ).getBlockSelectionStart();
					if(client_id){
						let editor_blocks = wp.data.select( 'core/editor' ).getBlocksByClientId(client_id);
						editor_blocks.forEach(function (editor) {
							editors[editor.clientId] = {
								editor: editor.attributes.content,
								taggingContent: false
							};
							tagContent(editor.clientId, editor, client_id);
						})
					}
				}
			}, 2000);
		}
	});
})(jQuery);
