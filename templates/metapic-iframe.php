<?php /* @var WP_MTPC $this */ ?>
<div class="wrap" style="margin: 0 0 0 -20px">
    <?php if($view === 'settings'): ?>
        <iframe id="metapic-popup"
                src="https://app.metapic.com/#/settings?accessToken=<?php echo get_option('mtpc_access_token') ?>&hideSidebar=true&hideNavigation=true"
                frameborder="0" width="100%" style="height: 1000px;"></iframe>
    <?php else: ?>
    <iframe id="metapic-popup" src="https://app.metapic.com/#/dashboard?accessToken=<?= get_option( 'mtpc_access_token' ) ?>&hideNavigation=true"
            frameborder="0" width="100%"></iframe>
    <?php endif; ?>
</div>

<script type="application/javascript">
	(function($) {
		$(document).on('ready', function() {
			var iframeHeight = $(window).height() -
				(65 + $("#wpfooter").outerHeight());
			$('#metapic-popup').css('height', iframeHeight + 'px');

			/*
			setTimeout(function() {
				console.log("Adjusting height");
				$('#metapic-popup').css('height', $("#wpbody-content").outerHeight() + 'px');
			}, 300);
			*/
		})
	})(jQuery);
	
</script>