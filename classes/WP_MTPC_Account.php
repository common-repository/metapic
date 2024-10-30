<?php

use MetaPic\ApiClient;

/**
 * Created by PhpStorm.
 * User: marcusdalgren
 * Date: 2019-01-11
 * Time: 20:07
 */
class WP_MTPC_Account {
	
	use WP_MTPC_Utilities;
	/* @var ApiClient $client */
	private $client;
	
	public function __construct( ApiClient $client ) {
		$this->client = $client;
	}
	
	public function check_account( $active_account ) {
		if ( ! $active_account ) {
			$currentUser = wp_get_current_user();
			if ( $currentUser ) {
				$blogUsers
					   = get_users( [ 'orderby' => 'display_name' ] );
				$match = array_filter( $blogUsers,
					function ( $user ) use ( $currentUser ) {
						return $user->ID === $currentUser->ID;
					} );
				if ( count( $match ) > 0 ) {
					return $this->registerCurrentUser();
				}
			}
		}
		
		return null;
	}
	
	public function handle_mtpc_user_login() {
		add_action( 'admin_post_mtpc_user_login',
			function () {
				check_admin_referer( 'mtpc_user_login', 'mtpc' );
				
				$user = $this->client->login( $_POST['email'],
					$_POST['password'] );
				
				if ( $user ) {
					$this->client->activateUser( $user['email'] );
					$this->activate_account( $user['id'], $user['email'],
						$user['access_token']['access_token'] );
					
					wp_safe_redirect( add_query_arg( 'mtpc-user-login', 1,
						admin_url( 'admin.php?page=metapic-settings' ) ) );
				} else {
					wp_safe_redirect( add_query_arg( 'mtpc-user-login-fail', 1,
						admin_url( 'admin.php?page=metapic-login' ) ) );
				}
				
				exit;
			}
		);
	}
	
	public function handle_mtpc_user_register() {
		add_action( 'admin_post_mtpc_user_register',
			function () {
				check_admin_referer( 'mtpc_user_register', 'mtpc' );
				
				$user = $this->client->register( $_POST['email'],
					$_POST['password'], $_POST['client'],
					get_home_url() );
				
				if ( $user ) {
					$this->client->activateUser( $user['email'] );
					$this->activate_account( $user['id'], $user['email'],
						$user['access_token']['access_token'] );
					
					wp_safe_redirect( add_query_arg( 'mtpc-user-register', 1,
						admin_url( 'admin.php?page=metapic-settings' ) ) );
				} else {
					wp_safe_redirect( add_query_arg( 'mtpc-user-register-fail',
						1,
						admin_url( 'admin.php?page=metapic-register' ) ) );
				}
				
				exit;
			} );
	}
	
	private function registerCurrentUser() {
		$wp_user = wp_get_current_user();
		$user    = $this->client->activateUser( $wp_user->user_email );
		if ( $user['access_token'] === null ) {
			$this->client->createUser( [
				'email'    => $wp_user->user_email,
				'username' => $wp_user->user_login,
			] );
			$user = $this->client->activateUser( $wp_user->user_email );
		}
		
		$this->activate_account( $user['id'], $wp_user->user_email,
			$user['access_token']['access_token'] );
		
		return $user;
	}
}