<?php global $post; ?>
<?php 
	$attachments = get_post_meta($post->ID, 'pp_gallery_slider', TRUE);
	$attachment_array = explode(',', $attachments);
	$rev_slider_alias = get_post_meta($post->ID, 'rev_slider_alias', TRUE);
?>
<?php if ($rev_slider_alias) {?>
	<div class="post-gallery">
		<?php putRevSlider($rev_slider_alias); ?>
	</div>
<?php  } else { ?>
	<div class="post-gallery">
		<div class="carousel owl post-carousel rand-<?php echo rand(0,100); ?>" data-columns="1" data-bgcheck="true" data-navigation="true" rel="gallery">
				<?php foreach ($attachment_array as $attachment) : ?>
				    <?php
				        $image_link = wp_get_attachment_image_src($attachment,'full');
				        $image_title = esc_attr( get_the_title($post->ID) );
				    ?>
				    <?php
			   			if ($masonry) {
							$image = aq_resize( $image_link[0], 370, 390, true, false, true); 
						} else {
							$image = aq_resize( $image_link[0], 1170, 550, true, false, true); 
						}
				    	
				    ?>
				    <img src="<?php echo $image[0]; ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" alt="<?php echo $image_title; ?>" />
				<?php endforeach; ?>
			</div>
	</div>
<?php } ?>