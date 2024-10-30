<?php

/* @var WP_MTPC $this */
$activeAccount = get_option("mtpc_active_account");
$options = get_option('metapic_options');

$action = ($activeAccount) ? 'mtpc_user_my_account' : 'mtpc_user_login';
$template = ($activeAccount) ? 'my-account' : 'login'
?>
<div class="wrap">
	<h2><?= __('Metapic settings page', 'metapic') ?></h2>

	<form method="post" action="<?= esc_url( admin_url('admin-post.php') ); ?>">
		<?php wp_nonce_field( $action, 'mtpc' ); ?>
		<input type="hidden" name="action" value="<?= $action  ?>" />
		<?php $this->getTemplate($template); ?>

		<?php if ($this->debugMode): ?>
		<h3><?= __('Advanced settings', 'metapic') ?></h3>
		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row"><?= __('Server address', 'metapic') ?></th>
				<td><input type="text" value="<?= get_option('mtpc_uri_string', '') ?>" size="40" name="mtpc_uri_string"
				           id="plugin_text_string"></td>

			</tr>
            <tr>
                <th scope="row"><?= __('Userserver address', 'metapic') ?></th>
                <td><input type="text" value="<?= get_option('mtpc_user_api_uri_string', '') ?>" size="40" name="mtpc_user_api_uri_string"
                           id="plugin_text_string"></td>

            </tr>
            <tr>
                <th scope="row"><?= __('CDN address', 'metapic') ?></th>
                <td><input type="text" value="<?= get_option('mtpc_cdn_uri_string', '') ?>" size="40" name="mtpc_cdn_uri_string"
                           id="plugin_text_string"></td>

            </tr>


			</tbody>
		</table>

		<p class="submit"><input type="submit" value="<?php esc_attr_e('Save Changes'); ?>"
		                         class="button button-primary" id="submit" name="submit"></p>
		<?php endif; ?>
	</form>
</div>