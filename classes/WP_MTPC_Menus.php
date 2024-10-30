<?php

use MetaPic\ApiClient;

/**
 * Created by PhpStorm.
 * User: marcusdalgren
 * Date: 2019-01-11
 * Time: 19:50
 *
 * @property  $activeAccount
 * @property  $auto_register
 */
class WP_MTPC_Menus {
	use WP_MTPC_Utilities;
	
	public function register_admin_menu( WP_MTPC $mtpc ) {
		global $submenu;
		$menu_cap = 'edit_posts';
		$isValidClient = get_site_option( 'mtpc_valid_client' );
		if ( ! is_multisite() || ( is_multisite() && $isValidClient ) ) {
			add_menu_page( 'Metapic', 'Metapic', $menu_cap,
			  'metapic',
			  function () use ( $mtpc ) {
				  if ( is_multisite() ) {
					  $mtpc->getTemplate( 'metapic-options-ms' );
				  } else {
					  $mtpc->getTemplate( 'metapic-options' );
				  }
			  },
			  $this->get_menu_icon()
			);
			
			if ( $this->has_active_account() && $this->needsBankInfo( $mtpc->getClient() ) ) {
				add_submenu_page( 'metapic',
				  __( 'My Account', 'metapic' ),
				  __( 'My Account', 'metapic' ), $menu_cap,
				  'metapic-account',
				  function () use ( $mtpc ) {
					  $mtpc->getTemplate( 'metapic-iframe', [
					    'view' => 'settings',
					    'height' => '520px'
					  ] );
				  }
				);
			}
			
			if ( ! is_multisite() && !$this->has_active_account() ) {
				add_submenu_page( 'metapic', __( 'Login', 'metapic' ),
				  __( 'Login', 'metapic' ), $menu_cap,
					'metapic-login',
				  function () use ( $mtpc ) {
					  $mtpc->getTemplate( 'login' );
				  }
				);

				add_submenu_page( 'metapic', __( 'Register', 'metapic' ),
				  __( 'Register', 'metapic' ), $menu_cap,
					'metapic-register',
				  function () use ( $mtpc ) {
					  $mtpc->getTemplate( 'register' );
				  }
				);
			}
			
			if ( $this->has_active_account() ) {
				add_submenu_page( 'metapic',
				  __( 'Metapic Settings', 'metapic' ),
				  __( 'Settings', 'metapic' ), $menu_cap,
				  'metapic-settings',
				  function () use ( $mtpc ) {
					  if ( is_multisite() ) {
						  $mtpc->getTemplate( 'metapic-options-ms' );
					  } else {
						  $mtpc->getTemplate( 'metapic-options' );
					  }
				  }
				);
				
				add_submenu_page( 'metapic',
				  __( 'Statistics', 'metapic' ),
				  __( 'Statistics', 'metapic' ), $menu_cap,
				  'metapic-stats',
				  function () use ( $mtpc ) {
					  $mtpc->getTemplate( 'metapic-iframe', [
						'view' => 'stats',
						'height' => '520px'
					  ] );
				  }
				);
			}

			if(isset($submenu['metapic']) && !empty($submenu['metapic'])) {
			    array_shift( $submenu['metapic'] );
            }
		}
	}
	
	public function register_network_admin_menu( WP_MTPC $mtpc ) {
		
		global $submenu;
		
		add_menu_page( 'Metapic', 'Metapic', 'manage_network',
		  'metapic',
		  '',
		  $this->get_menu_icon()
		);
		
		add_submenu_page( 'metapic',
		  __( 'Metapic Settings', 'metapic' ),
		  __( 'Settings', 'metapic' ), 'manage_network',
		  'metapic-settings',
		  function () use ( $mtpc ) {
			  $mtpc->getTemplate( 'metapic-site-options',
				[ 'debugMode' => $mtpc->debugMode ] );
		  }
		);
		
		array_shift( $submenu['metapic'] );
	}
	
	private function needsBankInfo( ApiClient $client ) {
		$needs_bankinfo = get_option( 'mtpc_needs_bankinfo' );
		if ( $needs_bankinfo === false ) {
			$bank_info = $client->get( 'extraUserInfo',
			  [ 'accessToken' => get_option( 'mtpc_access_token' ) ] );
			if ( isset( $bank_info['own_paymentsystem'] ) ) {
				$needs_bankinfo = (int) ! $bank_info['own_paymentsystem'];
				add_option( 'mtpc_needs_bankinfo', $needs_bankinfo );
			}
		}
		
		return (bool) $needs_bankinfo;
	}
}