<div class="wrap">
	<h2><?= __('Metapic registration', 'metapic') ?></h2>

	<form action="<?= esc_url( admin_url('admin-post.php') ); ?>" method="post">
		<?php wp_nonce_field( 'mtpc_user_register', 'mtpc' ); ?>
		<input type="hidden" name="action" value="mtpc_user_register" />

		<p><?= __('Enter your email address and your password to start using Metapic!', 'metapic') ?></p>
		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row"><?= __('Email', "metapic") ?></th>
				<td><input type="text" value="" size="40" name="email"
				           required></td>
			</tr>
			<tr>
				<th scope="row"><?= __('Password', "metapic") ?></th>
				<td><input type="password" value="" size="40" name="password"
				           required>
				</td>
			</tr>

			<tr>
				<th scope="row"><?= __('Country', "metapic") ?></th>
				<td>
					<select name="client">
						<option value="516249158032824">Sweden</option>
						<option value="905816344653311">Norway</option>
						<option value="521434391367307">Denmark</option>
						<option value="806944579910158">Finland</option>
						<option value="992373052100827">Italy</option>
						<option value="691846283721488">Poland</option>
						<option value="609926949991750">Germany</option>
						<option value="707265933641644">Spain</option>
						<option value="584057068435703">France</option>
					</select>
				</td>
			</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="register" id="register" class="button button-primary"
		                         value="<?= __('Create account', "metapic") ?>"></p>
	</form>
</div>
