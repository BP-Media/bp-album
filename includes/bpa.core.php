<?php

/**
 * BP-ALBUM CORE
 * Handles the overall operations of the plugin
 *
 * @version 0.1.8.11
 * @since 0.1.8
 * @package BP-Album
 * @subpackage Main
 * @license GPL v2.0
 * @link http://code.google.com/p/buddypress-media/
 *
 * ========================================================================================================
 */

define ( 'BP_ALBUM_IS_INSTALLED', 1 );
define ( 'BP_ALBUM_DB_VERSION', '0.2' );
define ( 'BP_ALBUM_VERSION', '0.1.8.11' );

load_textdomain( 'bp-album', dirname( __FILE__ ) . '/languages/bp-album-' . get_locale() . '.mo' );

require ( dirname( __FILE__ ) . '/bpa.classes.php' );
require ( dirname( __FILE__ ) . '/bpa.screens.php' );
require ( dirname( __FILE__ ) . '/bpa.cssjs.php' );
require ( dirname( __FILE__ ) . '/bpa.template.tags.php' );
require ( dirname( __FILE__ ) . '/bpa.filters.php' );

require_once( ABSPATH . '/wp-admin/includes/image.php' );
require_once( ABSPATH . '/wp-admin/includes/file.php' );


/**
 * bp_album_setup_globals()
 *
 * Sets up BP-Album's global variables.
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_setup_globals() {
    
	global $bp, $wpdb;
	
	if ( !defined( 'BP_ALBUM_UPLOAD_PATH' ) )
		define ( 'BP_ALBUM_UPLOAD_PATH', bp_album_upload_path() );
	
	$bp->album = new stdClass();

	$bp->album->id = 'album';
	$bp->album->table_name = $wpdb->base_prefix . 'bp_album';
	$bp->album->format_notification_function = 'bp_album_format_notifications';
	$bp->album->slug = get_site_option( 'bp_album_slug' );
	$bp->album->pictures_slug = 'pictures';
	$bp->album->single_slug = 'picture';
	$bp->album->upload_slug = 'upload';
	$bp->album->delete_slug = 'delete';
	$bp->album->edit_slug = 'edit';

        $bp->album->bp_album_max_pictures = get_site_option( 'bp_album_max_pictures' );
        $bp->album->bp_album_max_upload_size = get_site_option( 'bp_album_max_upload_size' );	
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
	$bp->album->bp_album_url_remap = get_site_option( 'bp_album_url_remap' );
	$bp->album->bp_album_base_url = get_site_option( 'bp_album_base_url' );

	$bp->active_components[$bp->album->slug] = $bp->album->id;
	
	if ( $bp->current_component == $bp->album->slug && $bp->album->upload_slug != $bp->current_action  ){
		bp_album_query_pictures();
	}	
}
add_action( 'wp', 'bp_album_setup_globals', 2 );

add_action( 'bp_setup_globals', 'bp_album_setup_globals', 2 );
add_action( 'admin_menu', 'bp_album_setup_globals', 2 );

/**
 * Adds the BuddyPress Album admin menu to the wordpress "Site" admin menu
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_add_admin_menu() {

	if( is_multisite()  ){
		return;
	}
	
	else 
	    {
		if ( !is_super_admin() ){
		    return false;
	    }

	require ( dirname( __FILE__ ) . '/admin/bpa.admin.local.php' );

	add_menu_page(__( 'BP Album', 'bp-album' ), __( 'BP Album', 'bp-album' ), 'administrator', 'bp-album-settings', 'bp_album_admin' );
		
	}
}
add_action( 'admin_menu', 'bp_album_add_admin_menu' );

/**
 * Adds the BP-Album admin menu to the wordpress "Network" admin menu.
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_add_network_menu() {
    
	if ( !is_super_admin() ){
		    return false;
	    }

	require ( dirname( __FILE__ ) . '/admin/bpa.admin.network.php' );

	add_menu_page(__( 'BP Album', 'bp-album' ), __( 'BP Album', 'bp-album' ), 'administrator', 'bp-album-settings', 'bp_album_admin' );

}
add_action( 'network_admin_menu', 'bp_album_add_network_menu' );

/**
 * bp_album_setup_nav()
 *
 * Sets up the user profile navigation items for the component. This adds the top level nav
 * item and all the sub level nav items to the navigation array. This is then
 * rendered in the template.
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_setup_nav() {
    
	global $bp,$pictures_template;

	$nav_item_name = apply_filters( 'bp_album_nav_item_name', __( 'Album', 'bp-album' ) );

	bp_core_new_nav_item( array(
		'name' => $nav_item_name,
		'slug' => $bp->album->slug,
		'position' => 80,
		'screen_function' => 'bp_album_screen_pictures',
		'default_subnav_slug' => $bp->album->pictures_slug,
		'show_for_displayed_user' => true
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
add_action( 'bp_setup_nav', 'bp_album_setup_nav' );

/**
 * bp_album_single_subnav_filter()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_single_subnav_filter($link,$user_nav_item){
	global $bp,$pictures_template;
	
	if(isset($pictures_template->pictures[0]->id))
		$link = str_replace  ( '/'. $bp->album->single_slug .'/' , '/'. $bp->album->single_slug .'/'.$pictures_template->pictures[0]->id .'/',$link );
		
	return $link;
}

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
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_load_template_filter( $found_template, $templates ) {
    
	global $bp;

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

/**
 * bp_album_load_subtemplate()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_album_load_subtemplate( $template_name ) {
    
	if ( file_exists(STYLESHEETPATH . '/' . $template_name . '.php')) {
		$located = STYLESHEETPATH . '/' . $template_name . '.php';
	} 
	else if ( file_exists(TEMPLATEPATH . '/' . $template_name . '.php') ) {
		$located = TEMPLATEPATH . '/' . $template_name . '.php';
	} 
	else{
		$located = dirname( __FILE__ ) . '/templates/' . $template_name . '.php';
	}
	include ($located);
}

/**
 * bp_album_upload_path()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_album_upload_path(){
    
	if ( is_multisite() )
		$path = ABSPATH . get_blog_option( BP_ROOT_BLOG, 'upload_path' );
	else {
		$upload_path = get_option( 'upload_path' );
		$upload_path = trim($upload_path);
		if ( empty($upload_path) || '/wp-content/uploads' == $upload_path) {
			$path = WP_CONTENT_DIR . '/uploads';
		} 
		else {
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

/**
 * bp_album_privacy_level_permitted()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_album_privacy_level_permitted(){
	global $bp;
	
	if(!is_user_logged_in())
		return 0;
	elseif(is_super_admin())
		return 10;
	elseif ( ($bp->displayed_user->id && $bp->displayed_user->id == $bp->loggedin_user->id) )
		return 6;
	elseif ( ($bp->displayed_user->id && function_exists('friends_check_friendship') && friends_check_friendship($bp->displayed_user->id,$bp->loggedin_user->id) ) )
		return 4;
	else
		return 2;
}

/**
 * bp_album_limits_info()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
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
			$return[$i]['enabled'] = is_super_admin();
			$return[$i]['remaining'] = $return[$i]['enabled'];
		} 
		else {
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

/**
 * bp_album_get_pictures()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_album_get_pictures($args = ''){
	return BP_Album_Picture::query_pictures($args);
}

/**
 * bp_album_get_picture_count()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_album_get_picture_count($args = ''){
	return BP_Album_Picture::query_pictures($args,true);
}

/**
 * bp_album_get_next_picture()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_album_get_next_picture($args = ''){
	$result = BP_Album_Picture::query_pictures($args,false,'next');
	return ($result)?$result[0]:false;
}

/**
 * bp_album_get_prev_picture()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_album_get_prev_picture($args = ''){
	$result = BP_Album_Picture::query_pictures($args,false,'prev');
	return ($result)?$result[0]:false;
}

/**
 * bp_album_add_picture()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_album_add_picture($owner_type,$owner_id,$title,$description,$priv_lvl,$date_uploaded,$pic_org_url,$pic_org_path,$pic_mid_url,$pic_mid_path,$pic_thumb_url,$pic_thumb_path){
	
    global $bp;
	
	$pic = new BP_Album_Picture();
	
	$pic->owner_type = $owner_type;

	$title = esc_attr( strip_tags($title) );
	$description = esc_attr( strip_tags($description) );

	$title = apply_filters( 'bp_album_title_before_save', $title );
	$description = apply_filters( 'bp_album_description_before_save', $description);
		
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

/**
 * bp_album_edit_picture()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_album_edit_picture($id,$title,$description,$priv_lvl,$enable_comments){
    
	global $bp;
	
	$pic = new BP_Album_Picture($id);

	if(!empty($pic->id)){

	    	$title = esc_attr( strip_tags($title) );
		$description = esc_attr( strip_tags($description) );

		$title = apply_filters( 'bp_album_title_before_save', $title );
		$description = apply_filters( 'bp_album_description_before_save', $description);

		if ( $pic->title != $title || $pic->description != $description || $pic->privacy != $priv_lvl){
		    $pic->title = $title;
		    $pic->description = $description;
		    $pic->privacy = $priv_lvl;
		    
		    $save_res = $pic->save();
		}
		else{
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
    
	}
	else
		return false;
}

/**
 * bp_album_delete_picture()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
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
	
	}
	else
		return false;
}

/**
 * bp_album_delete_by_user_id()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
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
	   
	if (function_exists('bp_activity_delete')) {
	
	bp_activity_delete(array('component' => $bp->album->id,'user_id' => $user_id));
	
	}
	
	return BP_Album_Picture::delete_by_user_id($user_id);
}

/********************************************************************************
 * Activity & Notification Functions
 *
 * These functions handle the recording, deleting and formatting of activity and
 * notifications for the user and for this specific component.
 */
 
 /**
 * bp_album_record_activity()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_album_record_activity($pic_data) {

	global $bp;

	if ( !function_exists( 'bp_activity_add' ) || !$bp->album->bp_album_enable_wire) {
		return false;
	}
		
	$id = bp_activity_get_activity_id(array('component'=> $bp->album->id,'item_id' => $pic_data->id));

	$primary_link = bp_core_get_user_domain($pic_data->owner_id) . $bp->album->slug . '/'.$bp->album->single_slug.'/'.$pic_data->id . '/';
	
	$title = $pic_data->title;
	$desc = $pic_data->description;

	// Using mb_strlen adds support for unicode (asian languages). Unicode uses TWO bytes per character, and is not
	// accurately counted in most string length functions
	// ========================================================================================================

	if ( function_exists( 'mb_strlen' ) ) {

	    $title = ( mb_strlen($title)<= 20 ) ? $title : mb_substr($title, 0 ,20-1).'&#8230;';
	    $desc = ( mb_strlen($desc)<= 400 ) ? $desc : mb_substr($desc, 0 ,400-1).'&#8230;';

	} 
	else {

	    $title = ( strlen($title)<= 20 ) ? $title : substr($title, 0 ,20-1).'&#8230;';
	    $desc = ( strlen($desc)<= 400 ) ? $desc : substr($desc, 0 ,400-1).'&#8230;';
	}
	
	$action = sprintf( __( '%s uploaded a new picture: %s', 'bp-album' ), bp_core_get_userlink($pic_data->owner_id), '<a href="'. $primary_link .'">'.$title.'</a>' );

	// Image path workaround for virtual servers that do not return correct base URL
	// ===========================================================================================================

	if($bp->album->bp_album_url_remap == true){

	    $filename = substr( $pic_data->pic_thumb_url, strrpos($pic_data->pic_thumb_url, '/') + 1 );
	    $owner_id = $pic_data->owner_id;
	    $image_path = $bp->album->bp_album_base_url . '/' . $owner_id . '/' . $filename;
	}
	else {

	    $image_path = bp_get_root_domain().$pic_data->pic_thumb_url;
	}

	// ===========================================================================================================

	$content = '<p> <a href="'. $primary_link .'" class="picture-activity-thumb" title="'.$title.'"><img src="'. $image_path .'" /></a>'.$desc.'</p>';
	
	$type = 'bp_album_picture';
	$item_id = $pic_data->id;
	$hide_sitewide = $pic_data->privacy != 0;

	return bp_activity_add( array( 'id' => $id, 'user_id' => $pic_data->owner_id, 'action' => $action, 'content' => $content, 'primary_link' => $primary_link, 'component' => $bp->album->id, 'type' => $type, 'item_id' => $item_id, 'recorded_time' => $pic_data->date_uploaded , 'hide_sitewide' => $hide_sitewide ) );	
}

 /**
 * bp_album_delete_activity()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_album_delete_activity( $user_id ) {
    
	global $bp;
	
	if ( !function_exists( 'bp_activity_delete' ) ) {
		return false;
	}
		
	return bp_activity_delete(array('component' => $bp->album->id,'item_id' => $user_id));
}

/**
 * bp_album_remove_data()
 *
 * It's always wise to clean up after a user has been deleted. This stops the database from filling up with
 * redundant information.
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_delete_user_data( $user_id ) {
	
	bp_album_delete_by_user_id( $user_id );
	
	do_action( 'bp_album_delete_user_data', $user_id );
}

add_action( 'wpmu_delete_user', 'bp_album_delete_user_data', 1 );
add_action( 'delete_user', 'bp_album_delete_user_data', 1 );



/**
 * Dumps an entire object or array to a html based page in human-readable format.
 *
 * @author http://ca2.php.net/manual/en/function.var-dump.php#92594
 * @param pointer &$var | Variable to be dumped
 * @param string $info | Text to add to dumped variable html block, when dumping multiple variables.
 */

function bp_album_dump(&$var, $info = FALSE) {
    
    $scope = false;
    $prefix = 'unique';
    $suffix = 'value';
    
    if($scope) $vals = $scope;
    else $vals = $GLOBALS;

    $old = $var;
    $var = $new = $prefix.rand().$suffix; $vname = FALSE;
    foreach($vals as $key => $val) if($val === $new) $vname = $key;
    $var = $old;

    echo "<pre style='margin: 0px 0px 10px 0px; display: block; background: white; color: black; font-family: Verdana; border: 1px solid #cccccc; padding: 5px; font-size: 10px; line-height: 13px;'>";
    if($info != FALSE) echo "<b style='color: red;'>$info:</b><br>";
    bp_album_do_dump($var, '$'.$vname);
    echo "</pre>";
}

/**
 * Recursive iterator function used by bp_album_dump()
 *
 * @author http://ca2.php.net/manual/en/function.var-dump.php#92594
 * @see bp_album_dump()
 */

function bp_album_do_dump(&$var, $var_name = NULL, $indent = NULL, $reference = NULL) {
    
    $do_dump_indent = "<span style='color:#eeeeee;'>|</span> &nbsp;&nbsp; ";
    $reference = $reference.$var_name;
    $keyvar = 'the_do_dump_recursion_protection_scheme'; $keyname = 'referenced_object_name';

    if (is_array($var) && isset($var[$keyvar])) {

        $real_var = &$var[$keyvar];
        $real_name = &$var[$keyname];
        $type = ucfirst(gettype($real_var));
        echo "$indent$var_name <span style='color:#a2a2a2'>$type</span> = <span style='color:#e87800;'>&amp;$real_name</span><br>";
    }
    else {
	
        $var = array($keyvar => $var, $keyname => $reference);
        $avar = &$var[$keyvar];

        $type = ucfirst(gettype($avar));
        if($type == "String") $type_color = "<span style='color:green'>";
        elseif($type == "Integer") $type_color = "<span style='color:red'>";
        elseif($type == "Double"){ $type_color = "<span style='color:#0099c5'>"; $type = "Float"; }
        elseif($type == "Boolean") $type_color = "<span style='color:#92008d'>";
        elseif($type == "NULL") $type_color = "<span style='color:black'>";

        if(is_array($avar)) {
	    
            $count = count($avar);
            echo "$indent" . ($var_name ? "$var_name => ":"") . "<span style='color:#a2a2a2'>$type ($count)</span><br>$indent(<br>";
            $keys = array_keys($avar);
            foreach($keys as $name)
            {
                $value = &$avar[$name];
                bp_album_do_dump($value, "['$name']", $indent.$do_dump_indent, $reference);
            }
            echo "$indent)<br>";
        }
        elseif(is_object($avar)) {
	    
            echo "$indent$var_name <span style='color:#a2a2a2'>$type</span><br>$indent(<br>";
            foreach($avar as $name=>$value) bp_album_do_dump($value, "$name", $indent.$do_dump_indent, $reference);
            echo "$indent)<br>";
        }
        elseif(is_int($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color$avar</span><br>";
        elseif(is_string($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color\"$avar\"</span><br>";
        elseif(is_float($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color$avar</span><br>";
        elseif(is_bool($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color".($avar == 1 ? "TRUE":"FALSE")."</span><br>";
        elseif(is_null($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> {$type_color}NULL</span><br>";
        else echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $avar<br>";

        $var = $var[$keyvar];
    }
}

/**
 * Adds *all* images in the database which users have marked as *public* to the user and site activity streams. Distributes the created activity
 * stream posts over the entire history of site to make the posts look natural. Detects images that already exist in the activity stream and
 * does not create posts for them.
 *
 * This function marks the activity stream posts it creates with secondary_item_id = 999 so that they can be deleted easily if it is necessary to
 * undo the changes. Do not try to remove created using an SQL query as it will not delete comments that users have added to the created posts, use
 * bp_activity_delete() in bp-activity.php
 *
 */

/**
 * bp_album_rebuild_activity()
 *
 * It's always wise to clean up after a user has been deleted. This stops the database from filling up with
 * redundant information.
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_rebuild_activity() {

	global $bp, $wpdb;

	// Handle users that try to run the function when the activity stream is disabled
	// ------------------------------------------------------------------------------
	if ( !function_exists( 'bp_activity_add' ) || !$bp->album->bp_album_enable_wire) {
		return false;
	}

	// Fetch all "public" images from the database
	$sql =  $wpdb->prepare( "SELECT * FROM {$bp->album->table_name} WHERE privacy = 0") ;
	$results = $wpdb->get_results( $sql );

	// Handle users that decide to run the function on sites with no uploaded content.
	//--------------------------------------------------------------------------------
	if(!$results){
	    return;
	}

	// Create an activity stream post for each image, with a special secondary_item_id so we can easily find our posts
	// ===============================================================================================================

	foreach($results as $pic_data){

		// Check if the item *already* has an activity stream post

		$sql = $wpdb->prepare( "SELECT id FROM {$bp->activity->table_name} WHERE component = '{$bp->album->id}' AND item_id = {$pic_data->id}");
		$has_post = $wpdb->get_var( $sql );

		// Create activity stream post

		if( !$has_post){

			$primary_link = bp_core_get_user_domain($pic_data->owner_id) . $bp->album->slug . '/'.$bp->album->single_slug.'/'.$pic_data->id . '/';

			$title = $pic_data->title;
			$desc = $pic_data->description;
			$title = ( strlen($title)<= 20 ) ? $title : substr($title, 0 ,20-1).'&#8230;';
			$desc = ( strlen($desc)<= 400 ) ? $desc : substr($desc, 0 ,400-1).'&#8230;';

			$action = sprintf( __( '%s uploaded a new picture: %s', 'bp-album' ), bp_core_get_userlink($pic_data->owner_id), '<a href="'. $primary_link .'">'.$title.'</a>' );

			// Image path workaround for virtual servers that do not return correct base URL
			// ===========================================================================================================

			if($bp->album->bp_album_url_remap == true){

			    $filename = substr( $pic_data->pic_thumb_url, strrpos($pic_data->pic_thumb_url, '/') + 1 );
			    $owner_id = $pic_data->owner_id;
			    $image_path = $bp->album->bp_album_base_url . '/' . $owner_id . '/' . $filename;
			}
			else {

			    $image_path = bp_get_root_domain().$pic_data->pic_thumb_url;
			}

			// ===========================================================================================================

			$content = '<p> <a href="'. $primary_link .'" class="picture-activity-thumb" title="'.$title.'"><img src="'. $image_path .'" /></a>'.$desc.'</p>';

			$type = 'bp_album_picture';
			$item_id = $pic_data->id;
			$hide_sitewide = $pic_data->privacy != 0;

			bp_activity_add( array('user_id' => $pic_data->owner_id, 'action' => $action, 'content' => $content, 'primary_link' => $primary_link, 'component' => $bp->album->id, 'type' => $type, 'item_id' => $item_id, 'secondary_item_id' => 999,'recorded_time' => $pic_data->date_uploaded , 'hide_sitewide' => $hide_sitewide ) );

		}

	}
	unset($results); unset($pic_data);

	// Find the site's oldest activity stream post, get its date, and convert it into a unix integer timestamp. Note this handles
	// sites with *zero* activity stream posts, because we added (potentially thousands of) them in the previous step.
	// ================================================================================================================================

	$sql = $wpdb->prepare( "SELECT date_recorded FROM {$bp->activity->table_name} ORDER BY date_recorded ASC LIMIT 1");
	$oldest_post_date = $wpdb->get_var( $sql );

	$full = explode(' ', $oldest_post_date);
	$date = explode('-', $full[0]);
	    $time = explode(':', $full[1]);

	    $year = $date[0];
	    $month = $date[1];
	    $day = $date[2];

	    $hour = $time[0];
	    $minute = $time[1];
	    $second = $time[2];

	    $oldest_unix_date = mktime($hour, $minute, $second, $month, $day, $year);
	$current_date = time();

	// Set each of our marked activity stream items to a random date between the first activity stream post date and the current date
	// ================================================================================================================================

	$sql = $wpdb->prepare( "SELECT id FROM {$bp->activity->table_name} WHERE component = '{$bp->album->id}' AND secondary_item_id = 999");
	$results = $wpdb->get_results( $sql );

	foreach($results as $post){

		$new_date = gmdate( "Y-m-d H:i:s", rand($oldest_unix_date, $current_date) );

		$sql = $wpdb->prepare( "UPDATE {$bp->activity->table_name} SET date_recorded = '{$new_date}' WHERE id = {$post->id}");
		$wpdb->query( $sql );	    
	}
	unset($results); unset($post);
	
}

/**
 * Removes all posts that were created by bp_album_rebuild_activity() from the activity stream.
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_album_undo_rebuild_activity() {

	global $bp, $wpdb;


	// Handle users that try to run the function when the activity stream is disabled
	if ( !function_exists( 'bp_activity_delete' ) || !$bp->album->bp_album_enable_wire) {
		return false;
	}

	return bp_activity_delete(array('component' => $bp->album->id,'secondary_item_id' => 999));

}

?>