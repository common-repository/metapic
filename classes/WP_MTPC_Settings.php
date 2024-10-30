<?php

use MetaPic\ApiClient;

class WP_MTPC_Settings {
	
	use WP_MTPC_Utilities;
	
	/**
	 * @var ApiClient
	 */
	private $client;
	
	public function __construct( ApiClient $lient ) {
		$this->client = $lient;
	}
	
	public function handle_admin_ms_settings( $api_url ) {
		add_action( 'admin_post_mtpc_site_settings',
			function () use ( $api_url ) {

				check_admin_referer( 'mtpc_site_settings', 'mtpc' );
				$auto_link_default        = get_site_option( 'mtpc_deeplink_auto_default' );
				$auto_link_default_update = (bool) $_POST['mtpc_deeplink_auto_default'];
				update_site_option( 'mtpc_deeplink_auto_default',
					$auto_link_default_update );
				update_site_option( 'mtpc_registration_auto',
					(bool) $_POST['mtpc_registration_auto'] );

                if($_POST['mtpc_show_collage_image_taging']){
                    update_site_option( 'mtpc_show_collage_image_taging',
                        1 );
                }else{
                    update_site_option( 'mtpc_show_collage_image_taging',
                        0 );
                }
               update_site_option( 'mtpc_show_collage_image_taging',
					(bool) $_POST['mtpc_show_collage_image_taging'] );

				update_site_option( 'mtpc_force_ssl',
					(bool) $_POST['mtpc_force_ssl'] );
				update_site_option( 'mtpc_commercial_interest_message',
					(bool) $_POST['mtpc_commercial_interest_message'] );
				
				$posted_api_url = isset( $_POST['mtpc_api_url'] )
					? trim( $_POST['mtpc_api_url'] ) : '';
				$api_url        = $posted_api_url !== '' ? $posted_api_url
					: $api_url;
				update_site_option( 'mtpc_api_url', $posted_api_url );
				
				$is_valid_client = get_site_option( 'mtpc_valid_client' );
				
				$current_api_key    = get_site_option( 'mtpc_api_key' );
				$current_secret_key = get_site_option( 'mtpc_secret_key' );
				
				$new_api_key    = $_POST['api_key'];
				$new_secret_key = $_POST['secret_key'];
				
				$reset_blogs              = false;
				$update_auto_link_default = ( $auto_link_default !== $auto_link_default_update );
				
				if ( $current_api_key !== $new_api_key
				     || $current_secret_key !== $new_secret_key
				     || ! $is_valid_client
				) {
					update_site_option( 'mtpc_api_key', $new_api_key );
					update_site_option( 'mtpc_secret_key',
						$new_secret_key );
					
					$this->client = new ApiClient( $api_url,
						$new_api_key, $new_secret_key );
					
					$is_valid_client = $this->client->checkClient( $new_api_key );
					
					if ( is_array( $is_valid_client ) ) {
						$status = $is_valid_client['status'] === 200;
						update_site_option( 'mtpc_valid_client',
							$status );
						
						if ( $status ) {
							update_site_option( 'mtpc_client_name',
								$is_valid_client['name'] );
						}
					}
					
					$reset_blogs = true;
				}
				
				if ( $reset_blogs || $update_auto_link_default ) {
					$current_site = get_current_blog_id();
					if ( ! wp_is_large_network() ) {
						foreach ( get_sites() as $site ) {
							/* @var WP_Site $site */
							switch_to_blog( $site->blog_id );
							if ( $reset_blogs ) {
								$this->deactivate_account();
							}
							
							if ( $update_auto_link_default ) {
								update_option( 'mtpc_deeplink_auto_default', $auto_link_default_update );
							}
						}
						
						switch_to_blog( $current_site );
					}
					
				}
				
				//$referrer = add_query_arg( 'mtpc-settings-saved', 1, wp_get_referer() );
				wp_safe_redirect( add_query_arg( 'mtpc-settings-saved', 1,
					$this->get_admin_referer() ) );
				exit;
			} );
	}
	
	public function handle_mtpc_user_my_account( $is_debug = false ) {
		add_action( 'admin_post_mtpc_user_my_account',
			function () use ( $is_debug ) {
				check_admin_referer( 'mtpc_user_my_account', 'mtpc' );
				$return_url = $this->get_admin_referer();
				$return_url = remove_query_arg( 'mtpc-account-activated',
					$return_url );
				
				if ( isset( $_POST['deactivate'] )
				     || isset( $_POST['logout'] )
				) {
					
					$this->deactivate_account();
					
					$query_arg = isset( $_POST['deactivate'] )
						? 'mtpc-account-deactivated'
						: 'mtpc-user-logout';
					
					$url = isset( $_POST['deactivate'] )
						? 'admin.php?page=metapic'
						: 'admin.php?page=metapic-login';
					
					$return_url = add_query_arg( $query_arg, 1,
						admin_url( $url ) );
					
				} else {
                    update_option( 'mtpc_access_token',
                        @$_POST['mtpc_access_token'] );

					update_option( 'mtpc_deeplink_auto_default',
						@$_POST['mtpc_deeplink_auto_default'] );
					
					update_option( 'mtpc_commercial_interest_message',
						@$_POST['mtpc_commercial_interest_message'] );
					
					if ( $is_debug ) {
						update_option( 'mtpc_uri_string',
							@$_POST['mtpc_uri_string'] );
						
						update_option( 'mtpc_user_api_uri_string',
							@$_POST['mtpc_user_api_uri_string'] );
						
						update_option( 'mtpc_cdn_uri_string',
							@$_POST['mtpc_cdn_uri_string'] );
						
						$return_url = add_query_arg( 'mtpc-settings-saved', 1,
							$return_url );
					}
				}
				
				wp_safe_redirect( $return_url );
				
				exit;
			} );
	}
	
	public function handle_mtpc_user_new() {
		add_action( 'admin_post_mtpc_user_new',
			function () {
				check_admin_referer( 'mtpc_user_new', 'mtpc' );
				
				$user_email = $_POST['mtpc_email'];
				$this->reactivate_or_new_account( $user_email );
			}
		);
	}
	
	public function handle_mtpc_user_reactivate() {
		add_action( 'admin_post_mtpc_user_reactivate',
			function () {
				check_admin_referer( 'mtpc_user_reactivate', 'mtpc' );
				
				$user_email = isset( $_POST['mtpc_email'] )
					? $_POST['mtpc_email'] : wp_get_current_user()->user_email;
				
				$this->reactivate_or_new_account( $user_email );
			}
		);
	}
	
	public function reactivate_or_new_account( $user_email ) {
		$wp_user = get_user_by( 'email', $user_email );
		
		$ref
			= admin_url( 'admin.php?page=metapic-settings' );
		$fail_ref
			= admin_url( 'admin.php?page=metapic' );
		if ( $wp_user ) {
			$user = $this->client->activateUser( $wp_user->user_email );
			
			// New API User
			if ( $user['access_token'] === null ) {
				$this->client->createUser( [
					'email'    => $wp_user->user_email,
					'username' => get_home_url(),
				] );
				
				if ( is_array( $user ) ) {
					$user
						 = $this->client->activateUser( $wp_user->user_email );
					$ref = add_query_arg( 'mtpc-account-created', 1, $ref );
				} else {
					$ref = add_query_arg( 'mtpc-account-create-failed', 1,
						$fail_ref );
				}
			} else {
				$ref = add_query_arg( 'mtpc-account-activated', 1, $ref );
			}
			
			$this->activate_account( $user['id'], $wp_user->user_email,
				$user['access_token']['access_token'] );
			
			add_option( 'mtpc_deeplink_auto_default',
				get_site_option( 'mtpc_deeplink_auto_default' ) );
			
		} else {
			$ref = add_query_arg( 'mtpc-account-not-found', 1, $fail_ref );
		}
		
		wp_safe_redirect( $ref );
		exit;
	}
	
}