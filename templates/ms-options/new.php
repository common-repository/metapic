<h2><?= __('Metapic settings page', 'metapic') ?></h2>
<form action="<?= esc_url( admin_url('admin-post.php') ); ?>" method="post">
	<?php wp_nonce_field( 'mtpc_user_new', 'mtpc' ); ?>
	<input type="hidden" name="action" value="mtpc_user_new" />
	
	<h3 class="title"><?= __('Create account', 'metapic') ?></h3>

	<p>
		<?= __('Metapic has been activated on your site by the network administrator.', 'metapic') ?>

	</p>
	<?php if(is_super_admin()): ?>
		<p>
			<?= __("As the network administrator you can connect this blog to any account on this site.<br/>Please select an account from the list.", 'metapic')?>
		</p>
		<?php $this->getTemplate('ms-options/partials/user-list') ?>
		<?php else: ?>
		<p>
			<?php //printf(__("In order to start using Metapic to link content, tag images and make collages you first have to accept our terms of use.<br/>An account will be created connected to the email address: <strong>%s</strong>.", 'metapic'), $wp_user->user_email) ?>
			<?php printf(__("In order to start using Metapic to link content, tag images and make collages to earn money just click on the button below.<br/>An account will be created connected to the email address: <strong>%s</strong>.", 'metapic'), $wp_user->user_email) ?>
		</p>
		<!--table class="form-table">
			<tbody>
			<tr>
				<th scope="row"><label for="accept_terms">
						<input type="checkbox" value="1" id="accept_terms"
						       name="accept_terms"> <?= __('I accept the terms.', 'metapic') ?>
					</label>
				</th>

			</tr>
			</tbody>
		</table-->
		<input type="hidden" name="mtpc_email" value="<?= $wp_user->user_email ?>">
	<?php endif; ?>
	<p class="submit"><input type="submit" value="<?php esc_attr_e($submitText, 'metapic'); ?>"
	                         class="button button-primary" id="submit" name="submit"></p>
</form>
<script>
	(function($) {
		$("#mtpc_reactivate").on("submit", function() {
			var mtpcTerms = $("#accept_terms");
			if (mtpcTerms.length > 0 && !mtpcTerms.is(":checked")) {
				alert("<?= __("You must accept the terms.", "metapic") ?>");
				return false;
			}
		});
	})(jQuery);
</script>