<?php
/*
	Plugin Name: Metapic
	Plugin URI: http://metapic.com/
	Description: Metapic image tagging
	Version: 1.9.4
	Author: Metapic
	Author URI: http://metapic.com/
	License: GPL v2
	Text Domain: metapic
	Network: true
	@package Metapic
*/

call_user_func( function () {
	global $wp_mtpc;
	$plugin_dir = __DIR__;
	$plugin_url = plugins_url() . '/' . basename( $plugin_dir );
	
	if ( ! class_exists( '\MetaPic\ApiClient' ) ) {
		include $plugin_dir . '/vendor/autoload.php';
	}
	
	require_once $plugin_dir . '/classes/WP_MTPC_Utilities.php';
	require_once $plugin_dir . '/classes/WP_MTPC.php';
	require_once $plugin_dir . '/classes/WP_MTPC_Account.php';
	require_once $plugin_dir . '/classes/WP_MTPC_Menus.php';
	require_once $plugin_dir . '/classes/WP_MTPC_Settings.php';
	require_once $plugin_dir . '/classes/WP_MTPC_Notices.php';
	
	$wp_mtpc = new WP_MTPC( $plugin_dir, $plugin_url );
	
	require_once $plugin_dir . '/gutenberg/plugin.php';
	
	register_activation_hook( __FILE__, function () use ( $wp_mtpc ) {
		$wp_mtpc->activate();
	} );
} );
