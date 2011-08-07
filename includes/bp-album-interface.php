<?php
/**
 * BP-Media Loader
 *
 * Loads BP-Media core
 *
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

echo "FILE INCLUDED";

class BPM_core extends BP_Component {

	/**
	 * Start component creation process
	 *
	 * @since 1.5
	 */
	function __construct() {
		parent::start(
			'bp-media',
			__( 'BP-Media', 'buddypress' ),
			BPM_PATH_BASE
		);
	}

	/**
	 * Include files
	 */
	function includes() {
		// Files to include
//		$includes = array(
//			'actions',
//			'screens',
//			'filters',
//			'classes',
//			'activity',
//			'template',
//			'functions',
//			'notifications',
//		);

		$includes = array();

		parent::includes( $includes );
	}

	/**
	 * Setup globals
	 *
	 * @since 1.5
	 * @global obj $bp
	 */
	function setup_globals() {

		global $bp;

		// All globals for the component.
		// Note that global_tables is included in this array.
		$globals = array(
			'path'                  => BPM_PATH_BASE,
			'slug'                  => 'bp-media-slug',
			'has_directory'         => true,
			'search_string'         => __( 'Search Media...', 'buddypress' ),
			'notification_callback' => 'media_format_notifications',
			'global_tables'         => null
		);

		parent::setup_globals( $globals );
	}

	/**
	 * Setup BuddyBar navigation
	 *
	 * @global obj $bp
	 */
	function setup_nav() {

		global $bp;

		// Add component to the main navigation
		$main_nav = array(
			'name'                => sprintf( __( 'BP-Media <span>%d</span>', 'buddypress' ), friends_get_total_friend_count() ),
			'slug'                => 'bp-media1',
			'position'            => 80,
			'screen_function' => 'bp_album_screen_pictures',
			'default_subnav_slug' => $bp->album->pictures_slug,
			'item_css_id'         => $bp->friends->id
		);

		$friends_link = trailingslashit( $bp->loggedin_user->domain . bp_get_friends_slug() );

		// Add the subnav items to the main nav item
		$sub_nav[] = array(
			'name' => $album_link_title,
			'slug' => 'bp-media2',
			'parent_slug' => $bp->album->slug,
			'parent_url' => $album_link,
			'screen_function' => 'bp_album_screen_pictures',
			'position' => 10
		);

		if($bp->current_component == $bp->album->slug  && $bp->current_action == $bp->album->single_slug ){

			add_filter( 'bp_get_displayed_user_nav_' . $bp->album->single_slug, 'bp_album_single_subnav_filter' ,10,2);

			$sub_nav[] = array(
				'name' => isset($pictures_template->pictures[0]->id) ? bp_album_get_picture_title_truncate(20) :  __( 'Picture', 'bp-album' ),
				'slug' => 'bp-media_3',
				'parent_slug' => $bp->album->slug,
				'parent_url' => $album_link,
				'screen_function' => 'bp_album_screen_single',
				'position' => 20
			);
		}

		$sub_nav[] = array(
			'name' => __( 'Upload picture', 'bp-album' ),
			'slug' => 'bp-media_4',
			'parent_slug' => $bp->album->slug,
			'parent_url' => $album_link,
			'screen_function' => 'bp_album_screen_upload',
			'position' => 30,
			'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
		);

		parent::setup_nav( $main_nav, $sub_nav );
	}

	/**
	 * Set up the admin bar
	 *
	 * @global obj $bp
	 */
	function setup_admin_bar() {
		global $bp;

		// Prevent debug notices
		$wp_admin_nav = array();

		parent::setup_admin_bar( $wp_admin_nav );
	}

	/**
	 * Sets up the title for pages and <title>
	 *
	 * @global obj $bp
	 */
	function setup_title() {
	    
		global $bp;

		// Adjust title
		if ( bp_is_current_component( 'bp-media' ) ) {

			if ( bp_is_my_profile() ) {

				$bp->bp_options_title = __( 'Medias', 'buddypress' );

			} 
			else {

				$bp->bp_options_avatar = bp_core_fetch_avatar( array(
					'item_id' => $bp->displayed_user->id,
					'type'    => 'thumb'
				) );

				$bp->bp_options_title  = $bp->displayed_user->fullname;
			}
			
		}

		parent::setup_title();
	}
}




	echo "BPM CORE ATTACH RUN";

	global $bp;
	// Create the component
	$bp->bp_media = new BPM_core();
	
	

?>