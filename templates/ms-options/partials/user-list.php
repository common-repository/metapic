<?php
/* @var WPDB $wpdb */
global $wpdb;
$blog_users = get_users(["orderby" => "display_name"]);
$users = get_users(["blog_id" => null, "orderby" => "display_name"]);
?>
<table class="form-table">
	<tr>
		<th scope="row"><label for="mtpc_email"><?php _e('Select user', 'metapic') ?></label></th>
		<td>
			<select name="mtpc_email" id="mtpc_email">
				<option value=""><?= __( '&mdash; Select &mdash;' ) ?></option>
				<optgroup label="<?= __( 'Users from this blog', 'metapic' ) ?>">
					<?php foreach ($blog_users as $user):  ?>
						<option value="<?= $user->user_email ?>"><?= $user->display_name ?> - <?= $user->user_email ?></option>
					<?php endforeach; ?>
				</optgroup>
				<optgroup label="<?= __( 'All users', 'metapic' ) ?>">
					<?php foreach ($users as $user):  ?>
						<option value="<?= $user->user_email ?>"><?= $user->display_name ?> - <?= $user->user_email ?></option>
					<?php endforeach; ?>
				</optgroup>
			</select>
		</td>
	</tr>
</table>