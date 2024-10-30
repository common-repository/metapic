<div class="wrap">
	<h2><?= __( 'Metapic Login', "metapic" ) ?></h2>

	<p><?= __( 'Please login to your Metapic account', 'metapic' ) ?></p>
	<form method="post"
	      action="<?= esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<?php wp_nonce_field( 'mtpc_user_login', 'mtpc' ); ?>
		<input type="hidden" name="action" value="mtpc_user_login"/>

		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row"><?= __( 'Email', "metapic" ) ?></th>
				<td><input type="text" value="" size="40"
				           name="email"
				           id="email"></td>
			</tr>
			<tr>
				<th scope="row"><?= __( 'Password', "metapic" ) ?></th>
				<td><input type="password" value="" size="40"
				           name="password"
				           id="password">
				</td>
			</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="login" id="login"
		                         class="button primary"
		                         value="<?= __( 'Log in', "metapic" ) ?>"></p>
		<p><?= __( 'Don\'t have an account?', 'metapic' ) ?> <a
					href="<?= admin_url('admin.php?page=metapic-register') ?>"
					style="text-decoration: none;"><?= __( 'Click here to register',
				  'metapic' ) ?></a></p>
	</form>
</div>
