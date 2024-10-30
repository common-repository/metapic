<h2><?= __('Activate Metapic account', 'metapic') ?></h2>
<form action="<?= esc_url( admin_url('admin-post.php') ); ?>" method="post" id="mtpc_user_reactivate">
	<?php wp_nonce_field( 'mtpc_user_reactivate', 'mtpc' ); ?>
	<input type="hidden" name="action" value="mtpc_user_reactivate" />

	<p>
		<?php printf(__('Metapic has been activated on your site by the network administrator.<br/>There is an existing account connected to your email address, <strong>%s</strong>.', 'metapic'), $wp_user->user_email) ?>

	</p>

	<p>
		<?= __("This could mean that you're either already using Metapic on a blog in the network or that you have used Metapic before.<br/>Total clicks will be pooled from all blogs using your account in the network", 'metapic') ?>
	</p>
	<?php if(is_super_admin()):
		?>
		<p>
			<?= __("As the network administrator you can connect this blog to any account on this site.<br/>Please select an account from the list.", 'metapic')?>
		</p>
		<?php $this->getTemplate('ms-options/partials/user-list') ?>
	<?php endif; ?>
	<p class="submit"><input type="submit" value="<?php _e('Activate account', 'metapic'); ?>"
	                         class="button button-primary" id="submit" name="submit"></p>
</form>
<script>
	(function($) {
		$("#mtpc_reactivate").on("submit", function() {
			var mtpcEmail = $("#mtpc_email");
			if (mtpcEmail.length > 0 && mtpcEmail.val() == "") {
				alert("<?= __("You must select a user.", "metapic") ?>");
				return false;
			}
		});
	})(jQuery);
</script>