<?php function thb_post( $atts, $content = null ) {
    extract(shortcode_atts(array(
       	'carousel' => 'no',
       	'item_count' => '9',
       	'columns' => '4'
    ), $atts));
    
	$args = array(
		'showposts' => $item_count, 
		'nopaging' => 0, 
		'post_type'=>'post', 
		'post_status' => 'publish', 
		'ignore_sticky_posts' => 1,
		'no_found_rows' => true,
		'suppress_filters' => 0
	);
	
	$posts = new WP_Query( $args );
 	
 	ob_start();
 	
	if ( $posts->have_posts() ) { ?>
	  <?php switch($columns) {
	  	case 2:
	  		$col = 'medium-6';
	  		$w = '570';
	  		break;
	  	case 3:
	  		$col = 'medium-4';
	  		$w = '370';
	  		break;
	  	case 4:
	  		$col = 'medium-3';
	  		$w = '270';
	  		break;
	  } ?>
		<?php if ($carousel == "yes") { ?>
			
				<div class="carousel posts owl row" data-columns="<?php echo $columns; ?>" data-navigation="true" data-bgcheck="false">				
					
					<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
						<article <?php post_class('post '.$col.' columns'); ?> id="post-<?php the_ID(); ?>">
							<?php
							  $masonry = 0;
							  include(locate_template( 'inc/postformats/image.php' ));
							?>
							  <div class="post-title">
							  	<h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
							  </div>
							  <div class="post-content">
							  	<?php echo thb_ShortenText(get_the_content(), 200); ?>
							  </div>
								<?php get_template_part( 'inc/postformats/post-meta-masonry' ); ?>
						</article>
					<?php endwhile; // end of the loop. ?>	 
										
				</div>
			
		<?php } else {  ?> 
		<div class="masonry posts row" data-equal="article">
		
			<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
				<article <?php post_class('small-4 columns post item'); ?> id="post-<?php the_ID(); ?>">
				  <?php
					$masonry = 0;
				    include(locate_template( 'inc/postformats/image.php' ));
				  ?>
				    <div class="post-title">
				    	<h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
				    </div>
				    <div class="post-content">
				    	<?php echo thb_ShortenText(get_the_content(), 200); ?>
				    </div>
				  	<?php get_template_part( 'inc/postformats/post-meta-masonry' ); ?>
				</article>
			<?php endwhile; // end of the loop. ?>
		 
		</div>
		
		<?php } ?>
	   
	<?php }

   $out = ob_get_contents();
   if (ob_get_contents()) ob_end_clean();
   
   wp_reset_query();
   wp_reset_postdata();
     
  return $out;
}
add_shortcode('thb_post', 'thb_post');
