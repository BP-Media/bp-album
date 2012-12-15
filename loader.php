<?php
/*
Plugin Name: BP-Album
Plugin URI: http://code.google.com/p/buddypress-media/
Description: Photo Albums for BuddyPress. Includes Posts to Wire, Member Comments, and Gallery Privacy Controls. Works with the current BuddyPress theme and includes Easy To Skin Templates.
Version: 0.1.8.14
Revision Date: December 15, 2012
Requires at least: 3.5
Tested up to: WP 3.5, BP 1.6.1, PHP 5.4.4
License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
Author: The BP-Media Team
Author URI: https://github.com/BP-Media
Network: True
Text Domain: bp-album
*/

/**
 * Attaches Bp-Album to Buddypress.
 *
 * This function is REQUIRED to prevent WordPress from white-screening if Bp-Album is activated on a
 * system that does not have an active copy of BuddyPress.
 *
 * @version 0.1.8.12
 * @since 0.1.8.0
 */
function bpa_init() {

	require( dirname( __FILE__ ) . '/includes/bpa.core.php' );

}
add_action( 'bp_loaded', 'bpa_init' );

?>