<?php

/***
 * This file is used to add site administration menus to the WordPress backend.
 *
 * If you need to provide configuration options for your component that can only
 * be modified by a site administrator, this is the best place to do it.
 *
 * However, if your component has settings that need to be configured on a user
 * by user basis - it's best to hook into the front end "Settings" menu.
 */

/**
 * bp_album_admin()
 *
 * Checks for form submission, saves component settings and outputs admin screen HTML.
 */
function bp_album_admin() {
	global $bp;

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit'] ) && check_admin_referer('example-settings') ) {
		update_site_option( 'bp_album_slug', $_POST['bp_album_slug'] );
		update_site_option( 'bp_album_max_pictures', $_POST['bp_album_max_pictures']=='' ? false : intval($_POST['bp_album_max_pictures']) );
		
		foreach(array(0,2,4,6) as $i){
			$option_name = "bp_album_max_priv{$i}_pictures";
			$option_value = $_POST[$option_name]=='' ? false : intval($_POST[$option_name]);
			update_site_option($option_name , $option_value);
		}
		
                update_site_option( 'bp_album_keep_original', $_POST['bp_album_keep_original'] );
                update_site_option( 'bp_album_require_description', $_POST['bp_album_require_description'] );
                update_site_option( 'bp_album_enable_comments', $_POST['bp_album_enable_comments'] );
                update_site_option( 'bp_album_enable_wire', $_POST['bp_album_enable_wire'] );
                update_site_option( 'bp_album_middle_size', $_POST['bp_album_middle_size'] );
                update_site_option( 'bp_album_thumb_size', $_POST['bp_album_thumb_size'] );
                update_site_option( 'bp_album_per_page', $_POST['bp_album_per_page'] );

		$updated = true;
	}

        $bp_album_slug = get_site_option( 'bp_album_slug' );
        $bp_album_max_pictures = get_site_option( 'bp_album_max_pictures' );
        $bp_album_max_priv0_pictures = get_site_option( 'bp_album_max_priv0_pictures' );
        $bp_album_max_priv2_pictures = get_site_option( 'bp_album_max_priv2_pictures' );
        $bp_album_max_priv4_pictures = get_site_option( 'bp_album_max_priv4_pictures' );
        $bp_album_max_priv6_pictures = get_site_option( 'bp_album_max_priv6_pictures' );
        $bp_album_keep_original = get_site_option( 'bp_album_keep_original' );
        $bp_album_require_description = get_site_option( 'bp_album_require_description' );
        $bp_album_enable_comments = get_site_option( 'bp_album_enable_comments' );
        $bp_album_enable_wire = get_site_option( 'bp_album_enable_wire' );
        $bp_album_middle_size = get_site_option( 'bp_album_middle_size' );
        $bp_album_thumb_size = get_site_option( 'bp_album_thumb_size' );
        $bp_album_per_page = get_site_option( 'bp_album_per_page' );



?>
	<div class="wrap">
		<h2><?php _e( 'BP Album+ Settings', 'bp-album' ) ?> | Version <?php echo BP_ALBUM_VERSION ?></h2>
		<br />

		<?php if ( isset($updated) ) : ?><?php echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-album' ) . "</p></div>" ?><?php endif; ?>

		<form action="<?php echo site_url() . '/wp-admin/admin.php?page=bp-album-settings' ?>" name="example-settings-form" id="example-settings-form" method="post">

                    <h3><?php _e( 'General', 'bp-album' ) ?></h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="target_uri"><?php _e( 'Name of BP Album+ slug', 'bp-album' ) ?></label></th>
					<td>
						<input name="bp_album_slug" type="text" id="bp_album_slug" value="<?php echo attribute_escape($bp_album_slug ); ?>" size="10" />
					</td>
				</tr>
                                <tr>
					<th scope="row"><?php _e( 'Force members to enter a description for each image', 'bp-album' ) ?></th>
					<td>
						<input type="radio" name="bp_album_require_description" type="text" id="bp_album_require_description"<?php if ($bp_album_require_description == true ) : ?> checked="checked"<?php endif; ?>  value="1" /> <?php _e( 'Yes', 'bp-album' ) ?> &nbsp;
						<input type="radio" name="bp_album_require_description" type="text" id="bp_album_require_description"<?php if ($bp_album_require_description == false) : ?> checked="checked"<?php endif; ?>  value="0" /> <?php _e( 'No', 'bp-album' ) ?>
					</td>
				</tr>
                                <tr>
					<th scope="row"><?php _e( 'Allow site members to post comments on album images', 'bp-album' ) ?></th>
					<td>
						<input type="radio" name="bp_album_enable_comments" type="text" id="bp_album_enable_comments"<?php if ($bp_album_enable_comments == true ) : ?> checked="checked"<?php endif; ?>  value="1" /> <?php _e( 'Yes', 'bp-album' ) ?> &nbsp;
						<input type="radio" name="bp_album_enable_comments" type="text" id="bp_album_enable_comments"<?php if ($bp_album_enable_comments == false) : ?> checked="checked"<?php endif; ?>  value="0" /> <?php _e( 'No', 'bp-album' ) ?>
					</td>
				</tr>
                                <tr>
					<th scope="row"><?php _e( 'Post image thumbnails to members activity stream', 'bp-album' ) ?></th>
					<td>
						<input type="radio" name="bp_album_enable_wire" type="text" id="bp_album_enable_wire"<?php if ($bp_album_enable_wire == true ) : ?> checked="checked"<?php endif; ?>  value="1" /> <?php _e( 'Yes', 'bp-album' ) ?> &nbsp;
						<input type="radio" name="bp_album_enable_wire" type="text" id="bp_album_enable_wire"<?php if ($bp_album_enable_wire == false) : ?> checked="checked"<?php endif; ?>  value="0" /> <?php _e( 'No', 'bp-album' ) ?>
					</td>
				</tr>

			</table>

                    <h3><?php _e( 'Album Size Limits', 'bp-album' ) ?></h3>
                    <p><?php _e( "Accepted values: empty (that means no limit), an integer (0 means disabled). First option doesn't accept 0. Last option only accept a number.", 'bp-album' ) ?></p>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="target_uri"><?php _e( 'Max total images allowed in a members album', 'bp-album' ) ?></label></th>
					<td>
						<input name="bp_album_max_pictures" type="text" id="example-setting-one" value="<?php echo attribute_escape( $bp_album_max_pictures ); ?>" size="10" />
					</td>
				</tr>
                                <tr>
					<th scope="row"><label for="target_uri"><?php _e( 'Max images visible to public allowed in a members album', 'bp-album' ) ?></label></th>
					<td>
						<input name="bp_album_max_priv0_pictures" type="text" id="bp_album_max_priv0_pictures" value="<?php echo attribute_escape( $bp_album_max_priv0_pictures ); ?>" size="10" />
					</td>
				</tr>
                                <tr>
					<th scope="row"><label for="target_uri"><?php _e( 'Max images visible only to members in a members album', 'bp-album' ) ?></label></th>
					<td>
						<input name="bp_album_max_priv2_pictures" type="text" id="bp_album_max_priv2_pictures" value="<?php echo attribute_escape( $bp_album_max_priv2_pictures ); ?>" size="10" />
					</td>
				</tr>
                                 <tr>
					<th scope="row"><label for="target_uri"><?php _e( 'Max images visible only to friends in a members album', 'bp-album' ) ?></label></th>
					<td>
						<input name="bp_album_max_priv4_pictures" type="text" id="bp_album_max_priv4_pictures" value="<?php echo attribute_escape( $bp_album_max_priv4_pictures ); ?>" size="10" />
					</td>
				</tr>
                                <tr>
					<th scope="row"><label for="target_uri"><?php _e( 'Max private images in a members album', 'bp-album' ) ?></label></th>
					<td>
						<input name="bp_album_max_priv6_pictures" type="text" id="bp_album_max_priv6_pictures" value="<?php echo attribute_escape( $bp_album_max_priv6_pictures ); ?>" size="10" />
					</td>
				</tr>
                                <tr>
					<th scope="row"><label for="target_uri"><?php _e( 'Images per album page', 'bp-album' ) ?></label></th>
					<td>
						<input name="bp_album_per_page" type="text" id="bp_album_per_page" value="<?php echo attribute_escape( $bp_album_per_page ); ?>" size="10" />
					</td>
				</tr>
			</table>

                 <h3><?php _e( 'Image Size Limits', 'bp-album' ) ?></h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="target_uri"><?php _e( 'Album Image Size', 'bp-album' ) ?></label></th>
					<td>
						<input name="bp_album_middle_size" type="text" id="bp_album_middle_size" value="<?php echo attribute_escape( $bp_album_middle_size ); ?>" size="10" />
					</td>
				</tr>
                                <tr>
					<th scope="row"><label for="target_uri"><?php _e( 'Thumbnail Image Size', 'bp-album' ) ?></label></th>
					<td>
						<input name="bp_album_thumb_size" type="text" id="bp_album_thumb_size" value="<?php echo attribute_escape( $bp_album_thumb_size ); ?>" size="10" />
					</td>
				</tr>
                                <tr>
					<th scope="row"><?php _e( 'Keep original image files', 'buddypress' ) ?></th>
					<td>
						<input type="radio" name="bp_album_keep_original" type="text" id="bp_album_keep_original"<?php if ( $bp_album_keep_original == true ) : ?> checked="checked"<?php endif; ?> id="bp-disable-account-deletion" value="1" /> <?php _e( 'Yes', 'bp-album' ) ?> &nbsp;
						<input type="radio" name="bp_album_keep_original" type="text" id="bp_album_keep_original"<?php if ($bp_album_keep_original == false) : ?> checked="checked"<?php endif; ?> id="bp-disable-account-deletion" value="0" /> <?php _e( 'No', 'bp-album' ) ?>
					</td>
				</tr>

			</table>
			<p class="submit">
				<input type="submit" name="submit" value="<?php _e( 'Save Settings', 'bp-album' ) ?>"/>
			</p>

			<?php
			/* This is very important, don't leave it out. */
			wp_nonce_field( 'example-settings' );
			?>
		</form>
	</div>
<?php
}
?>