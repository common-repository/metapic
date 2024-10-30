<?php
/**
 * Plugin Name: Metapic Gutenberg Blocks
 * Description: Blocks for tagging images and creating collages
 *
 * Author: Metapic
 * Author URI: https://metapic.com/
 * Version: 1.0.0
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 * Network: true
 * Plugin URI: https://metapic.com/
 *
 * @package Metapic
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Block Initializer.
 */
require_once plugin_dir_path( __FILE__ ) . 'src/init.php';
