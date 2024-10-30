<h3><?= __('Your account', "metapic") ?></h3>
<p><?= sprintf(__('You are currently logged in as: <strong>%s</strong>', 'metapic'), get_option("mtpc_email")) ?></p>
<p>
    <label>
        <input type="hidden" name="mtpc_settings_my_account" value="1">
        <input type="hidden" name="mtpc_deeplink_auto_default" value="0">
        <input type="checkbox" <?php checked(get_option('mtpc_deeplink_auto_default')) ?> value="1" name="mtpc_deeplink_auto_default">
        <?= __('Activate auto linking by default', 'metapic'); ?>
    </label>
</p>
<p>
    <label>
        <input type="checkbox" <?php checked(get_option('mtpc_commercial_interest_message')) ?> value="1" name="mtpc_commercial_interest_message">
        <?= __('Display commercial interest message', 'metapic'); ?>
    </label>
</p>

<p class="submit">
	<?php if (!$this->debugMode): ?><input type="submit" value="<?php esc_attr_e('Save Changes'); ?>"
	                                       class="button button-primary" id="submit" name="my-account" style="margin-right:10px;"><?php endif; ?>
	<input type="submit" value="<?= __('Log out', 'metapic') ?>" class="button" id="logout" name="logout">
</p>

