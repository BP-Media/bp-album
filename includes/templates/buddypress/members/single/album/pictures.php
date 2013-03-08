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
