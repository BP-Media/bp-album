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

					<?php if ( bp_album_has_pictures() ) : ?>

				<div class="picture-pagination">
					<?php bp_album_picture_pagination(); ?>
				</div>

				<div class="picture-gallery">
						<?php while ( bp_album_has_pictures() ) : bp_album_the_picture(); ?>

				<div class="picture-thumb-box">

	                <a href="<?php bp_album_picture_url() ?>" class="picture-thumb"><img src='<?php bp_album_picture_thumb_url() ?>' /></a>
	                <a href="<?php bp_album_picture_url() ?>"  class="picture-title"><?php bp_album_picture_title_truncate() ?></a>
				</div>

						<?php endwhile; ?>
				</div>
					<?php else : ?>

				<div id="message" class="info">
					<p><?php echo bp_word_or_name( __("You don't have any photos yet. Why not upload some!", 'bp-album' ), __( "Either %s hasn't uploaded any pictures yet or they have restricted access", 'bp-album' )  ,false,false) ?></p>
				</div>

				<?php endif; ?>

			</div><!-- #item-body -->

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer('buddypress'); ?>