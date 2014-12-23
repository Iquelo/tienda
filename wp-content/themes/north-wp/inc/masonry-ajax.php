<?php
add_action("wp_ajax_nopriv_thb_ajax", "load_more_posts");
add_action("wp_ajax_thb_ajax", "load_more_posts");

function load_more_posts() {
	$count = $_POST['count'];
	$page = $_POST['page'];
	$blog_type = $_POST['style'];

	global $post;
	
	  $args = array(
	  		'paged'	=> $page,
		  	'no_found_rows' => true,
			'suppress_filters' => 0
	  );
	
	$query = new WP_Query( $args );
	if ($query->have_posts()) :  while ($query->have_posts()) : $query->the_post(); ?>
		<?php if ($blog_type == 'style1') { ?>
			<article itemscope itemtype="http://schema.org/BlogPosting" <?php post_class('post'); ?> id="post-<?php the_ID(); ?>" role="article">
					<header class="post-title">
						<h2 itemprop="headline"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
					</header>
					<?php get_template_part( 'inc/postformats/post-meta' ); ?>
					<?php 
						$format = get_post_format();
						if ($format) {
							include(locate_template( 'inc/postformats/'.$format.'.php' ));
						} else {
							include(locate_template( 'inc/postformats/standard.php' ));
						}
					?>
				<div class="row">
					<div class="small-12 medium-6 medium-centered columns post-content text-center">
						<?php echo thb_excerpt(300, '...'); ?>
						<a href="<?php the_permalink(); ?>" class="more-link"><?php _e( 'Read More', THB_THEME_NAME ); ?></a>
					</div>
				</div>
			</article>
		<?php } else if ($blog_type == 'style2') { ?>
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
		<?php } ?>
	<?php
	endwhile; else : endif; 
	die();
}

add_action("wp_ajax_nopriv_thb_product_ajax", "load_products");
add_action("wp_ajax_thb_product_ajax", "load_products");

function load_products() {
	$type = isset($_POST['type']) ? $_POST['type'] : "latest-products"; 
	$footer_products_count = ot_get_option('footer_products_count',6);

	if ($type == "latest-products") {
		
		$args = array(
			'post_type' => 'product',
			'post_status' => 'publish',
			'ignore_sticky_posts'   => 1,
			'posts_per_page' => $footer_products_count,
			'no_found_rows' => true,
			'suppress_filters' => 0
		);
	} else if ($type == "featured-products") {			
		$args = array(
	    	'post_type'	=> 'product',
			'post_status' => 'publish',
			'ignore_sticky_posts'	=> 1,
			'posts_per_page' => $footer_products_count,
			'meta_query' => array(
				array(
					'key' => '_visibility',
					'value' => array('catalog', 'visible'),
					'compare' => 'IN'
				),
				array(
					'key' => '_featured',
					'value' => 'yes'
				)
			),
			'no_found_rows' => true,
			'suppress_filters' => 0
		);
	} else if ($type == "best-sellers") {
		$args = array(
			'post_type' => 'product',
			'post_status' => 'publish',
			'ignore_sticky_posts'   => 1,
			'posts_per_page' => $footer_products_count,
			'meta_key' 		 => 'total_sales',
			'orderby' 		 => 'meta_value',
			'meta_query' => array(
				array(
					'key' => '_visibility',
					'value' => array( 'catalog', 'visible' ),
					'compare' => 'IN'
				)
			),
			'no_found_rows' => true,
			'suppress_filters' => 0
		);
	} else {
		$category = get_term_by('id',$type,'product_cat'); 
		$args = array(
			'post_type' => 'product',
			'post_status' => 'publish',
			'ignore_sticky_posts'   => 1,
			'product_cat' => $category->slug,
			'posts_per_page' => $footer_products_count,
			'no_found_rows' => true,
			'suppress_filters' => 0
		);		
	}
	$products = new WP_Query( $args );
	

	$catalog_mode = ot_get_option('shop_catalog_mode', 'off');
	$shop_product_listing = ot_get_option('shop_product_listing', 'style1');
	global $post;
	
	
	if ( $products->have_posts() ) { ?>
		<div class="carousel products no-padding owl row" data-columns="6" data-navigation="true" data-loop="true" data-bgcheck="false">	
	    <?php while ( $products->have_posts() ) { $products->the_post(); ?>
	    	<?php $product = get_product( $products->post->ID ); ?>
	    	<article itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" <?php post_class("post small-6 medium-4 large-2 columns product ".$shop_product_listing); ?>>
	    	
	    		<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
	    	
	    		<?php
    				$image_html = "";
    		
    				if ( has_post_thumbnail() ) {
    					$image_html = wp_get_attachment_image( get_post_thumbnail_id(), 'shop_catalog' );					
    				}
    			?>
    			<?php if ($shop_product_listing == 'style1') { ?>
    				<figure class="fresco">
    					<?php do_action( 'thb_product_badge'); ?>
    					<?php echo $image_html; ?>			
    					<div class="overlay"></div>
    					<div class="buttons">
    						<?php echo thb_wishlist_button(); ?>
    						<div class="post-title<?php if ($catalog_mode == 'on') { echo ' catalog-mode'; } ?>">
    							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    						</div>
    						<?php if ($catalog_mode != 'on') { ?>
    							<?php
    								/**
    								 * woocommerce_after_shop_loop_item_title hook
    								 *
    								 * @hooked woocommerce_template_loop_price - 10
    								 */
    								do_action( 'woocommerce_after_shop_loop_item_title' );
    							?>
    							<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
    						<?php } ?>
    					</div>
    				</figure>
    			<?php } else if ($shop_product_listing == 'style2') { ?>
    				<figure class="fresco">
    					<?php do_action( 'thb_product_badge'); ?>
    					<a href="<?php the_permalink(); ?>"><?php echo $image_html; ?></a>
    				</figure>
    				<div class="post-title<?php if ($catalog_mode == 'on') { echo ' catalog-mode'; } ?>">
    					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    				</div>
    				<?php if ($catalog_mode != 'on') { ?>
    					<?php
    						/**
    						 * woocommerce_after_shop_loop_item_title hook
    						 *
    						 * @hooked woocommerce_template_loop_price - 10
    						 */
    						do_action( 'woocommerce_after_shop_loop_item_title' );
    					?>
    					<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
    				<?php } ?>
    			<?php } ?>
	  	    </article>
	    	
	    <?php } ?>
	    
		</div>
		<div class="ai-dotted ai-indicator"><span class="ai-inner1"></span><span class="ai-inner2"></span><span class="ai-inner3"></span></div>
	<?php
	}
	wp_reset_query();
	wp_reset_postdata();
	die();
}
 ?>