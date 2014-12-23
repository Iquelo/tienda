<?php global $post; ?>
<?php 
	$quote = get_post_meta($post->ID, 'post_quote', true);
	$author = get_post_meta($post->ID, 'post_quote_author', true);
	$avatar = get_post_meta($post->ID, 'post_quote_avatar', true);
	$image = $avatar ? aq_resize( $avatar, 80, 80, true, false) : false;
?>
<div class="post-gallery quote">
	<div class="row">
		<blockquote class="small-12 medium-8 small-6 small-centered columns">
			<?php if($avatar) { echo '<img src="'.$image[0].'" class="avatar" />'; }?>
			<a href="<?php the_permalink(); ?>"><?php if($quote) { echo '<p>'.$quote.'</p>'; } else { echo 'Please enter a quote using the metaboxes'; } ?></a>
			<?php if($author) { echo '<cite>'.$author.'</cite>'; }?>
		</blockquote>
	</div>
</div>