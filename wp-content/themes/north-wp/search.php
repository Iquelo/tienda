<?php get_header(); ?>
<div class="row">
<section class="small-12 columns cf blog-section">
  	<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
	<article itemscope itemtype="http://schema.org/BlogPosting" <?php post_class('post'); ?> id="post-<?php the_ID(); ?>" role="article">
		<header class="post-title">
			<h2 itemprop="headline"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
		</header>
		<?php get_template_part( 'inc/postformats/post-meta' ); ?>
		<?php 
			$format = get_post_format();
			$masonry = 0;
			if ($format) {
				include(locate_template( 'inc/postformats/'.$format.'.php' ));
			} else {
				include(locate_template( 'inc/postformats/standard.php' ));
			}
		?>
		<div class="small-12 medium-6 medium-centered columns post-content bold-text text-center">
			<?php echo thb_excerpt(300, '...'); ?>
			<a href="<?php the_permalink(); ?>" class="more-link"><?php _e( 'Read More', THB_THEME_NAME ); ?></a>
		</div>
	</article>
  <?php endwhile; ?>
  	<?php theme_pagination(); ?>
  <?php else : ?>
    <p><?php _e( 'Please add posts from your WordPress admin page.', THB_THEME_NAME ); ?></p>
  <?php endif; ?>
</section>
</div>
<?php get_footer(); ?>