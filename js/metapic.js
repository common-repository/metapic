(function ($) {
	var pluginName = 'metapic';
	var dom, tinyDoc, tinyEditor, currentImage, currentMode,
		autoTagging = false,
		deepLinking = $('#deeplink-status-check'),
		deepLinkToggle = $('.save-deeplink-status'),
		taggingContent = false,
		currentSelection = null,
		bookmark = null,
		isGutenberg = (wp_mtpc.isGutenberg === "true");
	
	if (isGutenberg) {
		autoTagging = true;
	}
	
	function mobilecheck() {
		return (function (a, b) {
			return (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4)))
		})(navigator.userAgent || navigator.vendor || window.opera);
	}
	
	var getTagLink = function (tag) {
		return '<a target="_blank" rel="noopener nofollow" class="mtpc-link" href="' + tag.mtpc_url + '" data-metapic-link-url="' + tag.mtpc_url + '">' + tag.text + '</a>'
	};
	
	function checkIframe(editor) {
		if (editor.iframeElement !== undefined) {
			editor.iframeElement.blur();
		} else {
			editor.getBody().blur();
		}
	}
	
	var getTagsAsList = function (id, tags) {
		var list = $('<p></p>');
		var separator = '';
		list.addClass('mtpc-product-list');
		list.css('display', 'none');
		list.css('text-align', 'center');
		tinyDoc.find('ul[data-metapic-id=' + id + ']').remove(); // Legacy
		tinyDoc.find('p[data-metapic-id=' + id + ']').remove();
		list.attr('data-metapic-id', id);
		for (var i = 0; i < tags.length; i++) {
			if (i > 0) {
				separator = $('<span></span>');
				separator.addClass('mtpc-separator');
				separator.text('//');
				list.append(separator)
			}
			list.append(getTagLink(tags[i]))
		}
		return list
	};
	
	function checkfailedLinks(editor) {
		var links = $(editor.dom.select('a')).filter('a.mtpc-fail,a.mtpc-link').get();
		//var links = tinyDoc.find('a.mtpc-fail,a.mtpc-link').get();
		links.forEach(function (el) {
			var linkUrl = editor.dom.getAttrib(el, 'href');
			var testUrl = editor.dom.getAttrib(el, 'data-metapic-link-url');
			console.log(linkUrl, testUrl);
			if (linkUrl != testUrl) {
				console.log('No match!');
				editor.dom.removeClass(el, 'mtpc-fail');
				editor.dom.removeClass(el, 'mtpc-link')
			}
		})
	}
	
	function tagContent(editor) {
		var links, hrefs;

		console.log("TAGGING CONTENT", autoTagging, taggingContent);
		if (autoTagging && !taggingContent) {
			checkfailedLinks(editor);
			links = $(editor.dom.select('a')).filter('a:not(.mtpc-link,.mtpc-fail,.mtpc-fail-temp)');
			links = links.filter(function (index) {
				var href = $.trim($(this).attr('href'));
				return href !== '' && href.indexOf('_wp_link_placeholder') === -1
			});
			if (links.length > 0) {
				taggingContent = true;
				links.addClass('mtpc-load');
				hrefs = links.map(function (index, element) {
					return element.href
				}).get();
				$.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {action: 'mtpc_deeplink', links: hrefs},
					dataType: 'json'
				})
					.done(function (responseLinks) {
						var links = editor.dom.select('a');
						var previewLink = $('.wp-link-preview a');
						if (Array.isArray(responseLinks.content)) {
							responseLinks.content.forEach(function (link) {
								links.forEach(function (el) {
									var href = decodeURI(editor.dom.getAttrib(el, 'href'));
									link.before = decodeURI(link.before.toLowerCase());
									link.before = link.before.replace(/\/\s*$/, "");
									href = href.replace(/\/\s*$/, "").toLowerCase();
									
									if (link.before === href) {
										editor.dom.removeClass(el, 'mtpc-load');
										if (link.status === 'success') {
											editor.dom.setAttrib(el, 'href', link.after);
											editor.dom.setAttrib(el, 'data-metapic-link-url', link.after);
											previewLink.attr('href', link.after);
											previewLink.text(link.after.replace('http://', ''));
											editor.dom.addClass(el, 'mtpc-link')
										} else {
											editor.dom.setAttrib(el, 'data-metapic-link-url', link.before);
											editor.dom.addClass(el, 'mtpc-fail')
										}
									}
								})
							})
							//tinyEditor.fire('change')
						} else {
							links.addClass('mtpc-fail-temp')
						}
					})
					.fail(function () {
						links.addClass('mtpc-fail-temp')
					})
					.always(function () {
						links.removeClass('mtpc-load');
						taggingContent = false
					})
			}
		}
	}

	tinymce.PluginManager.add(pluginName, function (editor, url) {
		editor.on('init', function () {
			var links;
			dom = editor.dom;
			tinyDoc = $(dom.doc);
			links = editor.dom.select('a');
			autoTagging = (deepLinking.is(':checked') || isGutenberg);
			tinyEditor = editor;

			console.log("METAPIC INIT", editor.dom.select('a').length);
			/**/
			$.each(links, function (index, el) {
				if (el.href.indexOf('mtpc') !== -1) {
					$(el).addClass('mtpc-link')
				}
			});
			tagContent(editor);
			deepLinkToggle.on('click', function () {
				autoTagging = deepLinking.is(':checked');
				tagContent(editor)
			})
		});

		editor.on('change', function (e) {

			console.log("A CHANGE IS HAPPENING", editor.dom.select('a').length);
			tagContent(editor)
		});

		$('#publish').on('click', function () {
			var links = tinyDoc.find('.mtpc-fail-temp');
			taggingContent = true;
			links.removeClass('mtpc-fail-temp')
			//links.removeClass('mtpc-load')
		});

		$(document).on('metapicClose', function () {
			editor.focus();
		});

		$(document).on('metapicReturn', function (data) {
			var returnData = data.text,
				selection = $(editor.selection.getNode());

			var tags, tagList;
			editor.selection.moveToBookmark(bookmark);
			switch (currentMode) {
				case 'text':
					editor.selection.setContent(returnData);

					break;
				case 'image':
					returnData = $(returnData);
					selection = $(editor.selection.getNode());
					selection.attr('data-metapic-id', returnData.attr('data-metapic-id'));
					selection.attr('data-metapic-tags', returnData.attr('data-metapic-tags'));
					selection.addClass('mtpc-img');
					tagList = getTagsAsList(returnData.attr('data-metapic-id'), JSON.parse(returnData.attr('data-metapic-tags')));
					tagList.addClass('mtpc-product-list-img');
					selection.after(tagList);

					editor.selection.select(editor.getBody(), true);
					editor.selection.collapse(false);

					break;
				default:
					selection = $(editor.selection.getNode());
					if (selection.is('img')) {//image tag
						if (selection.parent().is('a')) {
							selection.parent().replaceWith(returnData)
						} else {//text link
							selection.replaceWith(returnData)
						}
					} else {//collage
						editor.insertContent(returnData);
						tagList = getTagsAsList($(returnData).attr('data-metapic-id'), JSON.parse($(returnData).attr('data-metapic-tags')));
						tagList.addClass('mtpc-product-list-collage');
						editor.insertContent(tagList.prop('outerHTML'))
					}

					break
			}
			editor.focus()
		});
		if (!isGutenberg) {
			var metapicButton;
			var linkButton = pluginName + 'link';
			editor.addButton(linkButton, {
				title: 'Link content',
				image: editor.settings.mtpc_plugin_url + '/images/tag_text_color.svg',
				icon: false,
				onclick: function () {
					currentMode = 'text';
					var selection = $(editor.selection.getContent());
					var contentConfig = (selection.is('img')) ? {} : {format: 'text'};
					bookmark = editor.selection.getBookmark(2, true);

					checkIframe(editor);

					$.event.trigger({
						type: 'metapic',
						text: editor.selection.getContent(contentConfig),
						startPage: 'find/default',
						hideSidebar: true,
						baseUrl: editor.settings.mtpc_base_url,
						randomKey: editor.settings.mtpc_access_token
					})
				},
				onPostRender: function () {
					editor.on('nodechange', setupTextButton);
					editor.on('click', setupTextButton)
				}
			});

			function setupTextButton(event) {
				var editorContent = $.trim(editor.selection.getContent());
				var isEmpty = (editorContent === '');
				var isMobile = mobilecheck();

				if (!isMobile) {
					if (isEmpty) {
						editor.controlManager.setDisabled(linkButton, true);
						editor.controlManager.setActive(linkButton, false)
					} else {
						editor.controlManager.setDisabled(linkButton, false)
					}
				}

				if (editor.selection.getNode().nodeName === 'A') {
					editor.controlManager.setActive(linkButton, true)
				} else {
					editor.controlManager.setActive(linkButton, false)
				}
			}

			if(editor.settings.mtpc_show_collage_image_taging) {
				console.log("is in if");
				console.log(editor.settings.mtpc_show_collage_image_taging);
				var imageButton = pluginName + 'img';
				editor.addButton(imageButton, {
					title: 'Tag image',
					image: editor.settings.mtpc_plugin_url + '/images/tag_image_color.svg',
					onclick: function () {
						currentMode = 'image';
						checkIframe(editor);
						var src = $(editor.selection.getNode()).attr('src');
						bookmark = editor.selection.getBookmark(2, true);
						$.event.trigger({
							type: 'metapic',
							startPage: 'tag-editor',
							hideSidebar: true,
							imgSrc: src,
							//text:editor.selection.getNode(),
							baseUrl: editor.settings.mtpc_base_url,
							randomKey: editor.settings.mtpc_access_token
						})
					},
					onPostRender: function () {
						metapicButton = this;
						editor.on('click', setupImageButton);
						editor.on('nodechange', setupImageButton)
					}
				});


				function setupImageButton(event) {
					if (editor.selection.getNode().nodeName !== 'IMG') {
						editor.controlManager.setDisabled(imageButton, true)
					} else {
						editor.controlManager.setDisabled(imageButton, false)
					}

					if ($(editor.selection.getNode()).attr('data-metapic-id')) {
						editor.controlManager.setActive(imageButton, true)

					} else {
						editor.controlManager.setActive(imageButton, false)
					}
				}

				var collageButton = pluginName + 'collage';
				editor.addButton(collageButton, {
					title: 'Add collage',
					image: editor.settings.mtpc_plugin_url + '/images/create_collage_color.svg',
					onclick: function () {
						currentMode = 'collage';
						checkIframe(editor);
						var src = $(editor.selection.getNode()).attr('src');
						bookmark = editor.selection.getBookmark(2, true);
						$.event.trigger({
							type: 'metapic',
							startPage: 'collage',
							imgSrc: src,
							hideSidebar: true,
							baseUrl: editor.settings.mtpc_base_url,
							randomKey: editor.settings.mtpc_access_token
						})
					},
					onPostRender: function () {
						metapicButton = this;
						if (!mobilecheck()) {
							editor.on('click', setupCollageButton);
							editor.on('nodechange', setupCollageButton)
						}
					}
				});

				function setupCollageButton(event) {
					if ($.trim(editor.selection.getContent()) != '') {
						editor.controlManager.setDisabled(collageButton, true)
					} else {
						editor.controlManager.setDisabled(collageButton, false)
					}
				}
			}

			function removeButtonClasses(button) {
				button.removeClass('metapic-new').removeClass('metapic-image').removeClass('metapic-text');
				return button
			}
		}
	})

})(jQuery);
