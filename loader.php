<?php
/*
Plugin Name: BuddyPress Album
Plugin URI: http://code.google.com/p/buddypress-media/
Description: Photo Albums for BuddyPress. Includes Posts to Wire, Member Comments, and Gallery Privacy Controls. Works with the current BuddyPress theme and includes Easy To Skin Templates.
Version: 0.1.8.11
Revision Date: September 21, 2011
Requires at least: 3.2
Tested up to: WP 3.2.1, BP 1.5, PHP 5.3.6
License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
Author: The BP-Media Team
Author URI: http://code.google.com/p/buddypress-media/people/list
Network: True
*/

/**
 * Attaches BuddyPress Album to Buddypress.
 *
 * This function is REQUIRED to prevent WordPress from white-screening if BuddyPress Album is activated on a
 * system that does not have an active copy of BuddyPress.
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bpa_init() {
	
	require( dirname( __FILE__ ) . '/includes/bpa.core.php' );
	
	do_action('bpa_init');
	
}
add_action( 'bp_include', 'bpa_init' );

/**
 * bp_album_install()
 *
 *  @version 0.1.8.11
 *  @since 0.1.8.0
 */
function bp_album_install(){
	global $bp,$wpdb;

	if ( !empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";

    $sql[] = "CREATE TABLE {$wpdb->base_prefix}bp_album (
	            id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	            owner_type varchar(10) NOT NULL,
	            owner_id bigint(20) NOT NULL,
	            date_uploaded datetime NOT NULL,
	            title varchar(250) NOT NULL,
	            description longtext NOT NULL,
	            privacy tinyint(2) NOT NULL default '0',
	            pic_org_url varchar(250) NOT NULL,
	            pic_org_path varchar(250) NOT NULL,
	            pic_mid_url varchar(250) NOT NULL,
	            pic_mid_path varchar(250) NOT NULL,
	            pic_thumb_url varchar(250) NOT NULL,
	            pic_thumb_path varchar(250) NOT NULL,
	            KEY owner_type (owner_type),
	            KEY owner_id (owner_id),
	            KEY privacy (privacy)
	            ) {$charset_collate};";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	dbDelta($sql);

	update_site_option( 'bp-album-db-version', BP_ALBUM_DB_VERSION  );

        if (!get_site_option( 'bp_album_slug' ))
            update_site_option( 'bp_album_slug', 'album');
	
        if ( !get_site_option( 'bp_album_max_upload_size' ))
            update_site_option( 'bp_album_max_upload_size', 6 ); // 6mb

        if (!get_site_option( 'bp_album_max_pictures' ))
            update_site_option( 'bp_album_max_pictures', false);

        if (!get_site_option( 'bp_album_max_priv0_pictures' ))
            update_site_option( 'bp_album_max_priv0_pictures', false);

        if (!get_site_option( 'bp_album_max_priv2_pictures' ))
            update_site_option( 'bp_album_max_priv2_pictures', false);
        
        if (!get_site_option( 'bp_album_max_priv4_pictures' ))
            update_site_option( 'bp_album_max_priv4_pictures', false);
        
        if (!get_site_option( 'bp_album_max_priv6_pictures' ))
            update_site_option( 'bp_album_max_priv6_pictures', false);

        if(!get_site_option( 'bp_album_keep_original' ))
            update_site_option( 'bp_album_keep_original', true);
        
        if(!get_site_option( 'bp_album_require_description' ))
            update_site_option( 'bp_album_require_description', false);

        if(!get_site_option( 'bp_album_enable_comments' ))
            update_site_option( 'bp_album_enable_comments', true);

        if(!get_site_option( 'bp_album_enable_wire' ))
            update_site_option( 'bp_album_enable_wire', true);

        if(!get_site_option( 'bp_album_middle_size' ))
            update_site_option( 'bp_album_middle_size', 600);

        if(!get_site_option( 'bp_album_thumb_size' ))
            update_site_option( 'bp_album_thumb_size', 150);
        
        if(!get_site_option( 'bp_album_per_page' ))
            update_site_option( 'bp_album_per_page', 20 );

        if(!get_site_option( 'bp_album_url_remap' ))
	    update_site_option( 'bp_album_url_remap', false);

        if(true) {
		$path = bp_get_root_domain() . '/wp-content/uploads/album';
		update_site_option( 'bp_album_base_url', $path );
	}

}
register_activation_hook( __FILE__, 'bp_album_install' );

/**
 * bp_album_check_installed()
 *
 *  @version 0.1.8.11
 *  @since 0.1.8.0
 */
function bp_album_check_installed() {
	global $wpdb, $bp;

	if ( !current_user_can('install_plugins') )
		return;

	if (!defined('BP_VERSION') || version_compare(BP_VERSION, '1.2','<')){
		add_action('admin_notices', 'bp_album_compatibility_notices' );
		return;
	}

	if ( get_site_option( 'bp-album-db-version' ) < BP_ALBUM_DB_VERSION )
		bp_album_install();
}
add_action( 'admin_menu', 'bp_album_check_installed' );

/**
 * bp_album_compatibility_notices() 
 *
 *  @version 0.1.8.11
 *  @since 0.1.8.0
 */
function bp_album_compatibility_notices() {

	if (!defined('BP_VERSION')){    
		$message .= ' BP Album needs BuddyPress 1.2 or later to work. Please install Buddypress';
		
		echo '<div class="error fade"><p>'.$message.'</p></div>';
		
	}elseif(version_compare(BP_VERSION, '1.2','<') ){
		$message .= 'BP Album needs BuddyPress 1.2 or later to work. Your current version is '.BP_VERSION.' please upgrade.';
		
		echo '<div class="error fade"><p>'.$message.'</p></div>';
	}
}

/**
 * bp_album_activate()
 *
 *  @version 0.1.8.11
 *  @since 0.1.8.0
 */
function bp_album_activate() {
	bp_album_check_installed();

	do_action( 'bp_album_activate' );
}
register_activation_hook( __FILE__, 'bp_album_activate' );

/**
 * bp_album_deactivate()
 *
 *  @version 0.1.8.11
 *  @since 0.1.8.0
 */
function bp_album_deactivate() {
	do_action( 'bp_album_deactivate' );
}
register_deactivation_hook( __FILE__, 'bp_album_deactivate' );

?>