<?php get_header('buddypress'); ?>

	<div id="content">
		<div class="padder">

			<div id="item-header">
				<?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
			</div>

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_displayed_user_nav(); ?>
					</ul>
				</div>
			</div>

			<div id="item-body">

				<div class="item-list-tabs no-ajax" id="subnav">
					<ul>
						<?php bp_get_options_nav(); ?>
					</ul>
				</div>

					<?php if (bp_album_has_pictures() ) : bp_album_the_picture();?>

				<div class="picture-single activity">
					<h3><?php bp_album_picture_title(); ?></h3>

                	<div class="picture-outer-container">
                		<div class="picture-inner-container">
			                <div class="picture-middle">
				                <img src="<?php bp_album_picture_middle_url(); ?>" />
				                <?php bp_album_adjacent_links(); ?>
			                </div>
		                </div>
	                </div>

					<p class="picture-description"><?php bp_album_picture_desc(); ?></p>
	                <p class="picture-meta">
	                <?php bp_album_picture_edit_link();  ?>
	                <?php bp_album_picture_delete_link();  ?></p>

				<?php bp_album_load_subtemplate( apply_filters( 'bp_album_template_screen_comments', 'album/comments' ) ); ?>
			
				</div>
					<?php else : ?>

				<div id="message" class="info">
					<p><?php echo bp_word_or_name( __( "This url is not valid.", 'bp-album' ), __( "Either this url is not valid or picture has restricted access.", 'bp-album' ),false,false ) ?></p>
				</div>

				<?php endif; ?>

			</div><!-- #item-body -->

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer('buddypress'); ?>