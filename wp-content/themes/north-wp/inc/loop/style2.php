<?php $blog_header = ot_get_option('blog_header'); ?>
<section class="blog-section row masonry<?php if ($blog_header) { ?> low-top-padding<?php } ?>" id="infinitescroll" data-count="<?php echo get_option('posts_per_page'); ?>" data-total="<?php echo $wp_query->max_num_pages; ?>" data-type="style2">
  	<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
	<article itemscope itemtype="http://schema.org/BlogPosting" <?php post_class('small-12 medium-4 item post columns'); ?> id="post-<?php the_ID(); ?>" role="article">
		<?php 
			$format = get_post_format();
			$masonry = 1;
			if ($format) {
				include(locate_template( 'inc/postformats/'.$format.'.php' ));
			} else {
				include(locate_template( 'inc/postformats/standard.php' ));
			}
		?>
		<header class="post-title">
			<h2 itemprop="headline"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
		</header>
		<?php get_template_part( 'inc/postformats/post-meta' ); ?>
		
		<div class="small-12 columns post-content bold-text text-center">
			<?php echo thb_excerpt(200, '...'); ?>
		</div>
	</article>
  <?php endwhile; else : ?>
    <p><?php _e( 'Please add posts from your WordPress admin page.', THB_THEME_NAME ); ?></p>
  <?php endif; ?>
</section>
<?php get_footer(); ?>