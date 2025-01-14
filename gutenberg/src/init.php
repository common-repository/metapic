<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
function metapic_gutenberg_cgb_block_assets() { // phpcs:ignore
	// Styles.
	wp_enqueue_style(
		'metapic_gutenberg-cgb-style-css', // Handle.
		plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ), // Block style CSS.
		array( 'wp-editor' ) // Dependency to include the CSS after it.
		// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: File modification time.
	);
}

// Hook: Frontend assets.
add_action( 'enqueue_block_assets', 'metapic_gutenberg_cgb_block_assets' );

/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
function metapic_gutenberg_cgb_editor_assets() { // phpcs:ignore
	// Scripts.

    wp_enqueue_script(
        'metapicAdmin', // Handle.
        plugins_url( '../js/metapic-admin.js', dirname( __FILE__ ) ), // metapic-admin.js
        ['wp-element', 'wp-i18n', 'wp-editor', 'wp-hooks'], // Dependencies, defined above.
        true // Enqueue the script in the footer.
    );
	$mtpcShowCollageAndImage=get_site_option( 'mtpc_show_collage_image_taging', true);
	if($mtpcShowCollageAndImage) {
		wp_enqueue_script(
			'metapic_gutenberg-cgb-block-js', // Handle.
			plugins_url('/dist/blocks.build.js', dirname(__FILE__)),
			// Block.build.js: We register the block here. Built with Webpack.
			['wp-element', 'wp-i18n', 'wp-editor', 'wp-hooks'], // Dependencies, defined above.
			// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: File modification time.
			true // Enqueue the script in the footer.
		);
	}
	// Styles.
	wp_enqueue_style(
		'metapic_gutenberg-cgb-block-editor-css', // Handle.
		plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ), // Block editor CSS.
		array( 'wp-edit-blocks' ) // Dependency to include the CSS after it.
		// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: File modification time.
	);
}

// Hook: Editor assets.
add_action( 'enqueue_block_editor_assets', 'metapic_gutenberg_cgb_editor_assets' );
