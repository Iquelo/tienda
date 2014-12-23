<?php get_header(); ?>
<?php 
 	if (is_page()) {
 		$id = $wp_query->get_queried_object_id();
		$page_padding = get_post_meta($id, 'page_padding', true);
		$fullwidth = get_post_meta($id, 'page_fullwidth', true);
 		$sidebar = get_post_meta($id, 'sidebar_set', true);
 		$sidebar_pos = get_post_meta($id, 'sidebar_position', true);
 		$snap_scroll = (get_post_meta($id, 'snap_scroll', true) !== 'on' ? false : 'snap_scroll');
 	}
?>

<?php if($snap_scroll) { ?>
	<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
		<?php the_content(); ?>
	<?php endwhile; else : endif; ?>
<?php } else if( class_exists('woocommerce') && (is_account_page() || is_cart() || is_checkout())) { ?>
	<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
		<?php the_content(); ?>
	<?php endwhile; else : endif; ?>
<?php } else { ?>
		<?php if($post->post_content != "") { ?>
			<section class="<?php if($page_padding == 'on') { ?>page-padding<?php } ?>">
				<div class="row<?php if($fullwidth == 'on') { ?> full-width-row<?php } ?>">
					<section class="<?php if($sidebar) { echo 'small-12 medium-9';} else { echo 'small-12'; } ?> columns <?php if ($sidebar && ($sidebar_pos == 'left'))  { echo 'medium-push-3'; } ?>">
					  <?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
						  <article <?php post_class('post'); ?> id="post-<?php the_ID(); ?>">
						    <div class="post-content">
						    	<?php the_content('Read More'); ?>
						    </div>
						  </article>
					  <?php endwhile; else : endif; ?>
					</section>
					<?php if($sidebar) { get_sidebar('page'); } ?>
				</div>
			</section>
		<?php } ?>
<?php } ?>
<?php get_footer(); ?>