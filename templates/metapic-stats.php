<?php /* @var WP_MTPC $this */ ?>
<div class="wrap">
	<?php
	$iframe_token = $this->getClient()->getIframeToken(get_option('mtpc_id'));
	?>
	<iframe id="metapic-popup" style="height:95vh;"
	  src="//app.metapic.com/#/dashboard?accessToken==<?= $iframe_token['random_token'] ?>&hideNavigation=true"
	  frameborder="0" width="100%"></iframe>
</div>