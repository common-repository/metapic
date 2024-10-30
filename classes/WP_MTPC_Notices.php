<?php

class WP_MTPC_Notices {
	private $notices;
	
	public function register_notice_messages( array $notices ) {
		$this->notices = $notices;
		add_filter( 'removable_query_args', function($query_args) use ($notices) {
			foreach ( $notices as $notice ) {
				$query_args[] = $notice['type'];
			}
			return $query_args;
		});
	}
	
	public function register_notices() {
		$action = is_network_admin() ? 'network_admin_notices'
		  : 'admin_notices';
		add_action( $action, function () {
			$message = '';
			$status = 'success';
			foreach ( $this->notices as $notice ) {
				
				if ( isset( $_GET[ $notice['type'] ] ) ) {
					$message = $notice['message'];
					if (isset($notice['status'])) {
						$status = $notice['status'];
					}
					break;
				}
			}
			
			if ( $message == '' ) {
				return;
			}
			?>
			<div class="notice notice-<?= $status ?> is-dismissible">
				<p><?= $message ?></p>
			</div>
			<?php
		} );
	}
}