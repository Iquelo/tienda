<?php global $post; ?>
<?php if (is_singular('portfolio')) {
		$layout = get_post_meta($post->ID, 'portfolio_layout', true);
	} else {
		$layout = false;
	}?>
<figure class="post-gallery fresco">
	<?php
	    $image_id = get_post_thumbnail_id();
	    $image_link = wp_get_attachment_image_src($image_id,'full');
	    $image_title = esc_attr( get_the_title($post->ID) );
	?>
	<?php
		if ($masonry) {
			$image = aq_resize( $image_link[0], 370, null, true, false, true); 
		} else {
			$image = aq_resize( $image_link[0], 1170, 550, true, false);  // Blog 
		}
	?>
	<a href="<?php the_permalink(); ?>"><img src="<?php echo $image[0]; ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" alt="<?php echo $image_title; ?>" /></a>
</figure>