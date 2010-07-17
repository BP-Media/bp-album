<?php

/* Define a constant that can be checked to see if the component is installed or not. */
define ( 'BP_ALBUM_IS_INSTALLED', 1 );

/* Define a constant that will hold the database version number that can be used for upgrading the DB
 *
 * NOTE: When table defintions change and you need to upgrade,
 * make sure that you increment this constant so that it runs the install function again.
*/

define ( 'BP_ALBUM_DB_VERSION', '0.2' );


/* Translation support */
load_textdomain( 'bp-album', dirname( __FILE__ ) . '/languages/bp-album-' . get_locale() . '.mo' );

/**
 * The next step is to include all the files you need for your component.
 * You should remove or comment out any files that you don't need.
 */

/* The classes file should hold all database access classes and functions */
require ( dirname( __FILE__ ) . '/bp-album-classes.php' );

/* The screens file hold the screens functions */
require ( dirname( __FILE__ ) . '/bp-album-screens.php' );

/* The ajax file should hold all functions used in AJAX queries */
//require ( dirname( __FILE__ ) . '/bp-album-ajax.php' );

/* The cssjs file should set up and enqueue all CSS and JS files used by the component */
require ( dirname( __FILE__ ) . '/bp-album-cssjs.php' );

/* The templatetags file should contain classes and functions designed for use in template files */
require ( dirname( __FILE__ ) . '/bp-album-templatetags.php' );

/* The widgets file should contain code to create and register widgets for the component */
//require ( dirname( __FILE__ ) . '/bp-album-widgets.php' );

/* The notifications file should contain functions to send email notifications on specific user actions */
require ( dirname( __FILE__ ) . '/bp-album-notifications.php' );

/* The filters file should create and apply filters to component output functions. */
require ( dirname( __FILE__ ) . '/bp-album-filters.php' );

require_once( ABSPATH . '/wp-admin/includes/image.php' );
require_once( ABSPATH . '/wp-admin/includes/file.php' );



/**
 * bp_album_setup_globals()
 *
 * Sets up global variables for your component.
 */
function bp_album_setup_globals() {
	global $bp, $wpdb;
	
	if ( !defined( 'BP_ALBUM_UPLOAD_PATH' ) )
		define ( 'BP_ALBUM_UPLOAD_PATH', bp_album_upload_path() );
	
	$bp->album = new stdClass();
	
	/* For internal identification */
	$bp->album->id = 'album';
	$bp->album->table_name = $wpdb->base_prefix . 'bp_album';
	$bp->album->format_notification_function = 'bp_album_format_notifications';
	$bp->album->slug = get_site_option( 'bp_album_slug' );
	$bp->album->pictures_slug = 'pictures';
	$bp->album->single_slug = 'picture';
	$bp->album->upload_slug = 'upload';
	$bp->album->delete_slug = 'delete';
	$bp->album->edit_slug = 'edit';

        // Site configuration constants have been replaced with entries in the $bp->album global

        $bp->album->bp_album_max_pictures = get_site_option( 'bp_album_max_pictures' );
        $bp->album->bp_album_max_priv0_pictures = get_site_option( 'bp_album_max_priv0_pictures' );
        $bp->album->bp_album_max_priv2_pictures = get_site_option( 'bp_album_max_priv2_pictures' );
        $bp->album->bp_album_max_priv4_pictures = get_site_option( 'bp_album_max_priv4_pictures' );
        $bp->album->bp_album_max_priv6_pictures = get_site_option( 'bp_album_max_priv6_pictures' );
        $bp->album->bp_album_keep_original = get_site_option( 'bp_album_keep_original' );
        $bp->album->bp_album_require_description = get_site_option( 'bp_album_require_description' );
        $bp->album->bp_album_enable_comments = get_site_option( 'bp_album_enable_comments' );
        $bp->album->bp_album_enable_wire = get_site_option( 'bp_album_enable_wire' );
        $bp->album->bp_album_middle_size = get_site_option( 'bp_album_middle_size' );
        $bp->album->bp_album_thumb_size = get_site_option( 'bp_album_thumb_size' );
        $bp->album->bp_album_per_page = get_site_option( 'bp_album_per_page' );

	/* Register this in the active components array */
	$bp->active_components[$bp->album->slug] = $bp->album->id;
	
	if ( $bp->current_component == $bp->album->slug && $bp->album->upload_slug != $bp->current_action  ){
		bp_album_query_pictures();
	}	
}
/***
 * In versions of BuddyPress 1.2.2 and newer you will be able to use:
 * add_action( 'bp_setup_globals', 'bp_album_setup_globals' );
 */
add_action( 'wp', 'bp_album_setup_globals', 2 );
add_action( 'admin_menu', 'bp_album_setup_globals', 2 );

/**
 * bp_album_add_admin_menu()
 *
 * This function will add a WordPress wp-admin admin menu for your component under the
 * "BuddyPress" menu.
 */
function bp_album_add_admin_menu() {
	global $bp;

	if ( !$bp->loggedin_user->is_site_admin )
		return false;

	require ( dirname( __FILE__ ) . '/bp-album-admin.php' );

	add_submenu_page( 'bp-general-settings', __( 'BP Album+', 'bp-album' ), __( 'BP Album+', 'bp-album' ), 'manage_options', 'bp-album-settings', 'bp_album_admin' );
}
add_action( 'admin_menu', 'bp_album_add_admin_menu' );

/**
 * bp_album_setup_nav()
 *
 * Sets up the user profile navigation items for the component. This adds the top level nav
 * item and all the sub level nav items to the navigation array. This is then
 * rendered in the template.
 */
function bp_album_setup_nav() {
	global $bp,$pictures_template;

	/* Add 'Example' to the main user profile navigation */
	bp_core_new_nav_item( array(
		'name' => __( 'Album', 'bp-album' ),
		'slug' => $bp->album->slug,
		'position' => 80,
		'screen_function' => 'bp_album_screen_pictures',
		'default_subnav_slug' => $bp->album->pictures_slug
	) );
	
	$album_link = ($bp->displayed_user->id ? $bp->displayed_user->domain : $bp->loggedin_user->domain) . $bp->album->slug . '/';
	$album_link_title = ($bp->displayed_user->id) ? bp_word_or_name( __( "My pictures", 'bp-album' ), __( "%s's pictures", 'bp-album' ) ,false,false) : __( "My pictures", 'bp-album' );
	
	bp_core_new_subnav_item( array(
		'name' => $album_link_title,
		'slug' => $bp->album->pictures_slug,
		'parent_slug' => $bp->album->slug,
		'parent_url' => $album_link,
		'screen_function' => 'bp_album_screen_pictures',
		'position' => 10
	) );

	if($bp->current_component == $bp->album->slug  && $bp->current_action == $bp->album->single_slug ){
		add_filter( 'bp_get_displayed_user_nav_' . $bp->album->single_slug, 'bp_album_single_subnav_filter' ,10,2);
		bp_core_new_subnav_item( array(
			'name' => isset($pictures_template->pictures[0]->id) ? bp_album_get_picture_title_truncate(20) :  __( 'Picture', 'bp-album' ),
			'slug' => $bp->album->single_slug,
			'parent_slug' => $bp->album->slug,
			'parent_url' => $album_link,
			'screen_function' => 'bp_album_screen_single',
			'position' => 20
		) );
	}

	bp_core_new_subnav_item( array(
		'name' => __( 'Upload picture', 'bp-album' ),
		'slug' => $bp->album->upload_slug,
		'parent_slug' => $bp->album->slug,
		'parent_url' => $album_link,
		'screen_function' => 'bp_album_screen_upload',
		'position' => 30,
		'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
	) );
}

function bp_album_single_subnav_filter($link,$user_nav_item){
	global $bp,$pictures_template;
	
	if(isset($pictures_template->pictures[0]->id))
		$link = str_replace  ( '/'. $bp->album->single_slug .'/' , '/'. $bp->album->single_slug .'/'.$pictures_template->pictures[0]->id .'/',$link );
		
	return $link;
}

/***
 * In versions of BuddyPress 1.2.2 and newer you will be able to use:
 * add_action( 'bp_setup_nav', 'bp_album_setup_nav' );
 */
add_action( 'wp', 'bp_album_setup_nav', 2 );
add_action( 'admin_menu', 'bp_album_setup_nav', 2 );

/**
 * bp_album_load_template_filter()
 *
 * You can define a custom load template filter for your component. This will allow
 * you to store and load template files from your plugin directory.
 *
 * This will also allow users to override these templates in their active theme and
 * replace the ones that are stored in the plugin directory.
 *
 * If you're not interested in using template files, then you don't need this function.
 *
 * This will become clearer in the function bp_album_screen_one() when you want to load
 * a template file.
 */
function bp_album_load_template_filter( $found_template, $templates ) {
	global $bp;

	/**
	 * Only filter the template location when we're on the example component pages.
	 */
	if ( $bp->current_component != $bp->album->slug )
		return $found_template;

	foreach ( (array) $templates as $template ) {
		if ( file_exists( STYLESHEETPATH . '/' . $template ) )
			$filtered_templates[] = STYLESHEETPATH . '/' . $template;
		elseif ( file_exists( TEMPLATEPATH . '/' . $template ) )
			$filtered_templates[] = TEMPLATEPATH . '/' . $template;
		else
			$filtered_templates[] = dirname( __FILE__ ) . '/templates/' . $template;
	}

	$found_template = $filtered_templates[0];

	return apply_filters( 'bp_album_load_template_filter', $found_template );
}
add_filter( 'bp_located_template', 'bp_album_load_template_filter', 10, 2 );

function bp_album_load_subtemplate( $template_name ) {
	if ( file_exists(STYLESHEETPATH . '/' . $template_name . '.php')) {
		$located = STYLESHEETPATH . '/' . $template_name . '.php';
	} else if ( file_exists(TEMPLATEPATH . '/' . $template_name . '.php') ) {
		$located = TEMPLATEPATH . '/' . $template_name . '.php';
	} else{
		$located = dirname( __FILE__ ) . '/templates/' . $template_name . '.php';
	}
	include ($located);
}

function bp_album_upload_path(){
	if ( bp_core_is_multisite() )
		$path = ABSPATH . get_blog_option( BP_ROOT_BLOG, 'upload_path' );
	else {
		$upload_path = get_option( 'upload_path' );
		$upload_path = trim($upload_path);
		if ( empty($upload_path) || 'wp-content/uploads' == $upload_path) {
			$path = WP_CONTENT_DIR . '/uploads';
		} else {
			$path = $upload_path;
			if ( 0 !== strpos($path, ABSPATH) ) {
				// $dir is absolute, $upload_path is (maybe) relative to ABSPATH
				$path = path_join( ABSPATH, $path );
			}
		}
	}
	
	$path .= '/album';

	return apply_filters( 'bp_album_upload_path', $path );

}

function bp_album_privacy_level_permitted(){
	global $bp;
	
	if(!is_user_logged_in())
		return 0;
	elseif(is_site_admin())
		return 10;
	elseif ( ($bp->displayed_user->id && $bp->displayed_user->id == $bp->loggedin_user->id) )
		return 6;
	elseif ( ($bp->displayed_user->id && function_exists('friends_check_friendship') && friends_check_friendship($bp->displayed_user->id,$bp->loggedin_user->id) ) )
		return 4;
	else
		return 2;
}

function bp_album_limits_info(){
	global $bp,$pictures_template;
	
	$owner_id = isset($pictures_template) ? $pictures_template->picture->owner_id : $bp->loggedin_user->id;
	
	$results = bp_album_get_picture_count(array('owner_id'=> $owner_id, 'privacy'=>'all', 'priv_override'=>true,'groupby'=>'privacy'));
	
	$return = array();
	$tot_count = 0;
	$tot_remaining = false;
	
	foreach(array(0,2,4,6,10) as $i){
		$return[$i]['count'] = 0;
		foreach ($results as $r){
			if($r->privacy == $i){
				$return[$i]['count'] = $r->count;
				break;
			}
		}
	
		if( isset($pictures_template) && $i==$pictures_template->picture->privacy )
			$return[$i]['current'] = true;
		else
			$return[$i]['current'] = false;
		
		if ($i==10){
			$return[$i]['enabled'] = is_site_admin();
			$return[$i]['remaining'] = $return[$i]['enabled'];
		} else {
                        // TODO: Refactor this, and the bp_album_max_privXX variable as an array.
                        switch ($i) {
                            case "0": $pic_limit = $bp->album->bp_album_max_priv0_pictures; break;
                            case "1": $pic_limit = $bp->album->bp_album_max_priv1_pictures; break;
                            case "2": $pic_limit = $bp->album->bp_album_max_priv2_pictures; break;
                            case "3": $pic_limit = $bp->album->bp_album_max_priv3_pictures; break;
                            case "4": $pic_limit = $bp->album->bp_album_max_priv4_pictures; break;
                            case "5": $pic_limit = $bp->album->bp_album_max_priv5_pictures; break;
                            case "6": $pic_limit = $bp->album->bp_album_max_priv6_pictures; break;
                            case "7": $pic_limit = $bp->album->bp_album_max_priv7_pictures; break;
                            case "8": $pic_limit = $bp->album->bp_album_max_priv8_pictures; break;
                            case "9": $pic_limit = $bp->album->bp_album_max_priv9_pictures; break;
                            default: $pic_limit = null;
                        }
			
			$return[$i]['enabled'] = $pic_limit !== 0 ? true : false;
				
			$return[$i]['remaining'] = $pic_limit === false ? true : ($pic_limit > $return[$i]['count'] ? $pic_limit - $return[$i]['count'] : 0 );
		}
		
		$tot_count += $return[$i]['count'];
		$tot_remaining = $tot_remaining || $return[$i]['remaining'];
	}
	$return['all']['count'] = $tot_count;
	$return['all']['remaining'] = $bp->album->bp_album_max_pictures === false ? true : ($bp->album->bp_album_max_pictures > $tot_count ? $bp->album->bp_album_max_pictures - $tot_count : 0 );
	$return['all']['remaining'] = $tot_remaining ? $return['all']['remaining'] : 0;
	$return['all']['enabled'] = true;
	
	return $return;
}

function bp_album_get_pictures($args = ''){
	return BP_Album_Picture::query_pictures($args);
}

function bp_album_get_picture_count($args = ''){
	return BP_Album_Picture::query_pictures($args,true);
}
function bp_album_get_next_picture($args = ''){
	$result = BP_Album_Picture::query_pictures($args,false,'next');
	return ($result)?$result[0]:false;
}
function bp_album_get_prev_picture($args = ''){
	$result = BP_Album_Picture::query_pictures($args,false,'prev');
	return ($result)?$result[0]:false;
}

function bp_album_add_picture($owner_type,$owner_id,$title,$description,$priv_lvl,$date_uploaded,$pic_org_url,$pic_org_path,$pic_mid_url,$pic_mid_path,$pic_thumb_url,$pic_thumb_path){
	global $bp;
	
	$pic = new BP_Album_Picture();
	
	$pic->owner_type = $owner_type;
	$pic->owner_id = $owner_id;
	$pic->title = $title;
	$pic->description = $description;
	$pic->privacy = $priv_lvl;
	$pic->date_uploaded = $date_uploaded;
	$pic->pic_org_url = $pic_org_url;
	$pic->pic_org_path = $pic_org_path;
	$pic->pic_mid_url = $pic_mid_url;
	$pic->pic_mid_path = $pic_mid_path;
	$pic->pic_thumb_url = $pic_thumb_url;
	$pic->pic_thumb_path = $pic_thumb_path;
	
    return $pic->save() ? $pic->id : false;

}

function bp_album_edit_picture($id,$title,$description,$priv_lvl,$enable_comments){
	global $bp;
	
	$pic = new BP_Album_Picture($id);

	if(!empty($pic->id)){
		if ( $pic->title != $title || $pic->description != $description || $pic->privacy != $priv_lvl){
		    $pic->title = $title;
		    $pic->description = $description;
		    $pic->privacy = $priv_lvl;
		    
		    $save_res = $pic->save();
		}else{
		    $save_res = true;	
		}
	    
	    if(bp_is_active('activity')){
	    	if ($enable_comments) 
	    		bp_album_record_activity($pic);
	    	else{
	    		bp_album_delete_activity($pic->id);
	    	}
	    }
	    
	    return $save_res;
    
	}else
		return false;
}

function bp_album_delete_picture($id=false){
	global $bp;
	if(!$id) return false;
	
	$pic = new BP_Album_Picture($id);
	
	if(!empty($pic->id)){
	
		@unlink($pic->pic_org_path);
		@unlink($pic->pic_mid_path);
		@unlink($pic->pic_thumb_path);
		
		bp_album_delete_activity( $pic->id );
		
		return $pic->delete();
	
	}else
		return false;
}


function bp_album_delete_by_user_id($user_id,$remove_files = true){
	global $bp;
	
	if($remove_files){
		$pics = BP_Album_Picture::query_pictures(array(
					'owner_type' => 'user',
					'owner_id' => $user_id,
					'per_page' => false,
					'id' => false
			));
		
		if($pics) foreach ($pics as $pic){
		
			@unlink($pic->pic_org_path);
			@unlink($pic->pic_mid_path);
			@unlink($pic->pic_thumb_path);
		
		}
	}
	   
	bp_activity_delete(array('component' => $bp->album->id,'user_id' => $id));
	
	return BP_Album_Picture::delete_by_user_id($user_id);
}


/********************************************************************************
 * Activity & Notification Functions
 *
 * These functions handle the recording, deleting and formatting of activity and
 * notifications for the user and for this specific component.
 */
 
 
function bp_album_record_activity($pic_data) {
	global $bp;

	if ( !function_exists( 'bp_activity_add' ) || !$bp->album->bp_album_enable_wire)
		return false;
		
	$id = bp_activity_get_activity_id(array('component'=> $bp->album->id,'item_id' => $pic_data->id));

	$primary_link = bp_core_get_user_domain($pic_data->owner_id) . $bp->album->slug . '/'.$bp->album->single_slug.'/'.$pic_data->id . '/';
	
	$title = $pic_data->title;
	$desc = $pic_data->description;
	$title = ( strlen($title)<= 20 ) ? $title : substr($title, 0 ,20-1).'&#8230;';
	$desc = ( strlen($desc)<= 400 ) ? $desc : substr($desc, 0 ,400-1).'&#8230;';
	
	$action = sprintf( __( '%s uploaded a new picture: %s', 'bp-album' ), bp_core_get_userlink($pic_data->owner_id), '<a href="'. $primary_link .'">'.$title.'</a>' );
	
	$content = '<p> <a href="'. $primary_link .'" class="picture-activity-thumb" title="'.$title.'"><img src="'.bp_get_root_domain().$pic_data->pic_thumb_url.'" /></a>'.$desc.'</p>';
	
	$type = 'bp_album_picture';
	$item_id = $pic_data->id;
	$hide_sitewide = $pic_data->privacy != 0;

	return bp_activity_add( array( 'id' => $id, 'user_id' => $pic_data->owner_id, 'action' => $action, 'content' => $content, 'primary_link' => $primary_link, 'component' => $bp->album->id, 'type' => $type, 'item_id' => $item_id, 'recorded_time' => $pic_data->date_uploaded , 'hide_sitewide' => $hide_sitewide ) );
	
	/* if hide_sidewide shold check here if has previus comments and put them hide_sitewide too */
	
}

function bp_album_delete_activity( $id ) {
	global $bp;
	
	if ( !function_exists( 'bp_activity_delete' ) )
		return false;
		
	return bp_activity_delete(array('component' => $bp->album->id,'item_id' => $id));
}

/**
 * bp_album_format_notifications()
 *
 * The format notification function will take DB entries for notifications and format them
 * so that they can be displayed and read on the screen.
 *
 * Notifications are "screen" notifications, that is, they appear on the notifications menu
 * in the site wide navigation bar. They are not for email notifications.
 *
 *
 * The recording is done by using bp_core_add_notification() which you can search for in this file for
 * examples of usage.
 *
function bp_album_format_notifications( $action, $item_id, $secondary_item_id, $total_items ) {
	global $bp;

	switch ( $action ) {
		case 'new_high_five':
			/* In this case, $item_id is the user ID of the user who sent the high five. *

			/***
			 * We don't want a whole list of similar notifications in a users list, so we group them.
			 * If the user has more than one action from the same component, they are counted and the
			 * notification is rendered differently.
			 *
			if ( (int)$total_items > 1 ) {
				return apply_filters( 'bp_album_multiple_new_high_five_notification', '<a href="' . $bp->loggedin_user->domain . $bp->album->slug . '/screen-one/" title="' . __( 'Multiple high-fives', 'bp-album' ) . '">' . sprintf( __( '%d new high-fives, multi-five!', 'bp-album' ), (int)$total_items ) . '</a>', $total_items );
			} else {
				$user_fullname = bp_core_get_user_displayname( $item_id, false );
				$user_url = bp_core_get_userurl( $item_id );
				return apply_filters( 'bp_album_single_new_high_five_notification', '<a href="' . $user_url . '?new" title="' . $user_fullname .'\'s profile">' . sprintf( __( '%s sent you a high-five!', 'bp-album' ), $user_fullname ) . '</a>', $user_fullname );
			}
		break;
	}

	do_action( 'bp_album_format_notifications', $action, $item_id, $secondary_item_id, $total_items );

	return false;
}*/

/**
 * bp_album_remove_data()
 *
 * It's always wise to clean up after a user is deleted. This stops the database from filling up with
 * redundant information.
 */
function bp_album_delete_user_data( $user_id ) {
	/* You'll want to run a function here that will delete all information from any component tables
	   for this $user_id */
	

	bp_album_delete_by_user_id( $user_id );
	
	/* Remember to remove usermeta for this component for the user being deleted */
	//delete_usermeta( $user_id, 'bp_album_some_setting' );

	do_action( 'bp_album_delete_user_data', $user_id );
}
add_action( 'wpmu_delete_user', 'bp_album_delete_user_data', 1 );
add_action( 'delete_user', 'bp_album_delete_user_data', 1 );

/***
 * Object Caching Support ----
 *
 * It's a good idea to implement object caching support in your component if it is fairly database
 * intensive. This is not a requirement, but it will help ensure your component works better under
 * high load environments.
 *
 * In parts of this example component you will see calls to wp_cache_get() often in template tags
 * or custom loops where database access is common. This is where cached data is being fetched instead
 * of querying the database.
 *
 * However, you will need to make sure the cache is cleared and updated when something changes. For example,
 * the groups component caches groups details (such as description, name, news, number of members etc).
 * But when those details are updated by a group admin, we need to clear the group's cache so the new
 * details are shown when users view the group or find it in search results.
 *
 * We know that there is a do_action() call when the group details are updated called 'groups_settings_updated'
 * and the group_id is passed in that action. We need to create a function that will clear the cache for the
 * group, and then add an action that calls that function when the 'groups_settings_updated' is fired.
 *
 * Example:
 *
 *   function groups_clear_group_object_cache( $group_id ) {
 *	     wp_cache_delete( 'groups_group_' . $group_id );
 *	 }
 *	 add_action( 'groups_settings_updated', 'groups_clear_group_object_cache' );
 *
 * The "'groups_group_' . $group_id" part refers to the unique identifier you gave the cached object in the
 * wp_cache_set() call in your code.
 *
 * If this has completely confused you, check the function documentation here:
 * http://codex.wordpress.org/Function_Reference/WP_Cache
 *
 * If you're still confused, check how it works in other BuddyPress components, or just don't use it,
 * but you should try to if you can (it makes a big difference). :)
 */

?>