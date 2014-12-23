<?php get_header(); ?>
<div class="blog-section">
<div class="row">
	<section class="small-12 columns cf">
  <?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
	  <article itemscope itemtype="http://schema.org/BlogPosting" <?php post_class('post blog-post'); ?> id="post-<?php the_ID(); ?>" role="article">
	  	<header class="post-title small-12 small-centered medium-8 columns">
	  		<h2 itemprop="headline"><?php the_title(); ?></h2>
	  	</header>
	  	<?php get_template_part( 'inc/postformats/post-meta' ); ?>
	    <?php
	      // The following determines what the post format is and shows the correct file accordingly
	      $format = get_post_format();
	      if ($format) {
		      $masonry = 0;
		      include(locate_template( 'inc/postformats/'.$format.'.php' ));
	      } else {
	      	include(locate_template( 'inc/postformats/standard.php' ));
	      }
	    ?>

	    <div class="row">
	    	<div class="small-12 medium-2 columns">
	    		<?php get_template_part( 'inc/postformats/post-social' ); ?>
	    	</div>
	    	<div class="small-12 medium-8 columns">
	    		<div class="post-content single-text">
			    	<?php the_content(); ?>
			    	<?php if ( is_single()) { wp_link_pages(); } ?>
    			</div>
    			<?php get_template_part( 'inc/postformats/post-prevnext' ); ?>
    		</div>
    		<div class="small-12 medium-2 columns"></div>
	    </div>
	  </article>
  <?php endwhile; else : endif; ?>
	</section>
</div>
<!-- Start #comments -->
<section id="comments" class="cf full">
	<div class="row">
		<div class="small-12 medium-8 small-centered columns">
  		<?php comments_template('', true ); ?>
  	</div>
  </div>
</section>
<!-- End #comments -->
</div>
<?php get_footer(); ?>
