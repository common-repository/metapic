<?php
/**
 * @var $mtpcEmail
 * @var $submitText
 */
?>
<h2><?= __('Metapic settings page', 'metapic') ?></h2>
<form action="<?= esc_url( admin_url('admin-post.php') ); ?>" method="post">
	<?php wp_nonce_field( 'mtpc_user_my_account', 'mtpc' ); ?>
	<input type="hidden" name="action" value="mtpc_user_my_account" />
	<h3 class="title"><?= __('My settings', 'metapic') ?></h3>

	<p><?= __('Metapic has been activated on your site by the network administrator.<br/>You have an active account and you can use the service to tag images and create collages.', 'metapic') ?></p>
	<p><?= __('The account is connected to the following email address:', 'metapic') ?> <strong><?= $mtpcEmail ?></strong></p>
	<p><label for="mtpc-autolink-default">
			<input type="hidden" name="mtpc_deeplink_auto_default" value="0">
			<input type="checkbox" <?php checked(get_option("mtpc_deeplink_auto_default")) ?> value="1" id="mtpc-autolink-default" name="mtpc_deeplink_auto_default">
			<?= __('Activate auto linking by default', 'metapic') ?></label></p>
    
    <div class="collapse_block">
        <div class="collapse_button"><?= __('Advanced settings', 'metapic') ?></div>
        <div class="collapse">
            <p><label for="mtpc-user-token"><?= __( 'User token','metapic' ) ?>
                <input type="text" class="regular-text" id="mtpc_access_token" value="<?= get_option( 'mtpc_access_token' ); ?>" name="mtpc_access_token">
                </label></p>
        </div>
    </div>
    
	<p class="submit">
		<input type="submit" value="<?php esc_attr_e('Save Changes'); ?>"
	                         class="button button-primary" id="submit" name="submit" style="margin-right:10px;">
		<input type="submit" value="<?php esc_attr_e($submitText, 'metapic'); ?>"
	                         class="button" id="submit" name="deactivate"></p>
</form>