<?php
/**
 * BP-ALBUM CSSJS
 * Handles the BP-Album CSS and JS includes
 *
 * @version 0.1.8.9
 * @since 0.1.8.9
 * @package BP-Album
 * @subpackage CSSJS
 * @license GPL v2.0
 * @link http://code.google.com/p/buddypress-media/
 *
 * ========================================================================================================
 */

/**
 * NOTE: You should always use the wp_enqueue_script() and wp_enqueue_style() functions to include
 * javascript and css files.
 */

/**
 * bp_album_add_js()
 *
 * This function will enqueue the components javascript file, so that you can make
 * use of any javascript you bundle with your component within your interface screens.
 */
function bp_album_add_js() {
	global $bp;

	if ( $bp->current_component == $bp->album->slug )
		wp_enqueue_script( 'bp-album-js', WP_PLUGIN_URL .'/bp-album/includes/js/general.js' );
}
//add_action( 'template_redirect', 'bp_album_add_js', 1 );

function bp_album_add_css() {
	global $bp;

		wp_enqueue_style( 'bp-album-css', WP_PLUGIN_URL .'/bp-album/includes/css/general.css' );
		wp_print_styles();
		
}
add_action( 'wp_head', 'bp_album_add_css' );

?>