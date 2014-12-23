<?php
	global $post, $product, $woocommerce;
	$attachment_ids = $product->get_gallery_attachment_ids();
	$catalog_mode = ot_get_option('shop_catalog_mode', 'off');
?>

<article itemscope itemtype="http://schema.org/Product" id="product-<?php the_ID(); ?>" <?php post_class('post product-page'); ?>>
	<div class="product_nav prev">
		<?php be_previous_post_link( '%link', '<i class="icon-budicon-439"></i>', true,'', 'product_cat' ); ?>
	</div>
	<div class="product_nav next">
		<?php be_next_post_link( '%link', '<i class="icon-budicon-447"></i>', true,'', 'product_cat' ); ?>
	</div>
	<div class="row">      
	  <div class="small-12 medium-6 columns">        
	  	<div class="product-gallery">
	  		<?php if (thb_out_of_stock()) {
	  			echo '<span class="badge out-of-stock">' . __( 'Out of Stock', THB_THEME_NAME ) . '</span>';
	  		} else if ( $product->is_on_sale() ) {
	  			echo apply_filters('woocommerce_sale_flash', '<span class="badge onsale">'.__( 'Sale',THB_THEME_NAME ).'</span>', $post, $product);
	  		} ?>
	  		<div class="carousel-container">
		  		<div id="lightbox-images" class="carousel owl product-images" data-columns="1" data-navigation="true">
						<?php if ( has_post_thumbnail() ) : ?>
				        	
							<?php
								$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), false, '' );
								$src_small = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID),'shop_single');
								$image_title = esc_attr( get_the_title( $post->ID ) );
							?>
		       
		          <figure itemprop="image" class="easyzoom">
		          	<img src="<?php echo $src_small[0]; ?>" title="<?php echo $image_title; ?>" />
		          </figure>
						
						<?php endif; ?>	
				            
						<?php if ( $attachment_ids ) {						
								
								foreach ( $attachment_ids as $attachment_id ) {
						
									$image_link = wp_get_attachment_url( $attachment_id );
									
									$src = wp_get_attachment_image_src( $attachment_id, false, '' );
									$src_small = wp_get_attachment_image_src( $attachment_id,  apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ));
									
									$image_title = esc_attr( get_the_title( $attachment_id ) );
									?>
										<figure itemprop="image" class="easyzoom">
											<img src="<?php echo $src_small[0]; ?>" title="<?php echo $image_title; ?>" />
										</figure>
									
									<?php
								}
							}
						?>
				</div>
				</div>
	  	</div><!-- end product images -->
	  </div>
	  <div class="small-12 medium-6 columns product-information">
	  	
	  	<header class="post-title">
	  		<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
	  			<span class="sku_wrapper"><?php _e( 'SKU:',THB_THEME_NAME ); ?> <span class="sku" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : __( 'N/A',THB_THEME_NAME ); ?></span>.</span>
	  		<?php endif; ?>
	  		<h1 itemprop="name" class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
	  	</header>
	  	<?php woocommerce_template_single_price(); ?>
	  	<?php if ( $post->post_excerpt ) { ?>
	  	<div itemprop="description" class="short-description">
	  		<?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ?>
	  	</div>
	  	<?php } ?>
	  	<?php if ($catalog_mode != 'on') { ?>
	  		<?php woocommerce_template_single_add_to_cart(); ?>
	  	<?php } ?>
	  </div>
	</div><!-- end row -->
</article><!-- #product-<?php the_ID(); ?> -->