<?php

trait WP_MTPC_Utilities {
	
	public function get_client_id() {
		return get_site_option( 'mtpc_api_key' );
	}
	
	public function get_admin_referer($url = null) {
		$url = ($url === null) ? wp_get_referer() : $url;
		return remove_query_arg(wp_removable_query_args(), $url);
	}
	
	public function has_active_account() {
		return ( get_option( 'mtpc_active_account' )
		         && get_option( 'mtpc_access_token' )
		         && get_option( 'mtpc_id' ) );
	}
	
	public function activate_account( $id, $email, $token ) {
		update_option( 'mtpc_active_account', true );
		update_option( 'mtpc_id', $id );
		update_option( 'mtpc_email', $email );
		update_option( 'mtpc_access_token', $token );
		
		if ( is_multisite() ) {
			update_option( 'mtpc_deeplink_auto_default',
				get_site_option( 'mtpc_deeplink_auto_default' ) );
		} else {
			add_option( 'mtpc_deeplink_auto_default', true );
		}
	}
	
	public function deactivate_account() {
		delete_option( 'mtpc_active_account' );
		delete_option( 'mtpc_access_token' );
		delete_option( 'mtpc_email' );
		delete_option( 'mtpc_id' );
		delete_option( 'mtpc_deeplink_auto_default' );
		delete_option( 'mtpc_user_belongs_to_blog' );
	}
	
	public function get_menu_icon() {
		
		$image     = plugins_url('metapic') . '/assets/icon.svg';
		$imageData = base64_encode( file_get_contents( $image ) );
		
		return 'data:image/svg+xml;base64,' . $imageData;
	}
	
	
}