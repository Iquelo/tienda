<?php
add_theme_support( 'woocommerce' );

/* Hide Admin bar for users */
function remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin()) {
	  	show_admin_bar(false);
	}
}

add_action('after_setup_theme', 'remove_admin_bar');

/* Footer Products */
function thb_footer_products() {
 if(class_exists('woocommerce')) {
	 $footer_products_radio = (isset($_GET['footer_products_radio']) ? htmlspecialchars($_GET['footer_products_radio']) : ot_get_option('footer_products_radio'));
	 $footer_products_sections = ot_get_option('footer_products_sections');
	 $footer_products_cat = ot_get_option('footer_products_categories');
	 $footer_products_count = ot_get_option('footer_products_count',6);
 ?>
	<section id="footer_products" class="footer_products cf">
		<?php if ($footer_products_radio == 'wid') { ?>
			<aside class="sidebar">
				<?php if (ot_get_option('footer_columns') == 'fourcolumns') { ?>
				<div class="small-12 medium-3 columns">
					<?php dynamic_sidebar('footer1'); ?>
				</div>
				<div class="small-12 medium-3 columns">
					<?php dynamic_sidebar('footer2'); ?>
				</div>
				<div class="small-12 medium-3 columns">
					<?php dynamic_sidebar('footer3'); ?>
				</div>
				<div class="small-12 medium-3 columns">
					<?php dynamic_sidebar('footer4'); ?>
				</div>
				<?php } elseif (ot_get_option('footer_columns') == 'threecolumns') { ?>
				<div class="small-12 medium-4 columns">
					<?php dynamic_sidebar('footer1'); ?>
				</div>
				<div class="small-12 medium-4 columns">
					<?php dynamic_sidebar('footer2'); ?>
				</div>
				<div class="small-12 medium-4 columns">
					<?php dynamic_sidebar('footer3'); ?>
				</div>
				<?php } elseif (ot_get_option('footer_columns') == 'twocolumns') { ?>
				<div class="small-12 medium-6 columns">
					<?php dynamic_sidebar('footer1'); ?>
				</div>
				<div class="small-12 medium-6 columns">
					<?php dynamic_sidebar('footer2'); ?>
				</div>
				<?php } elseif (ot_get_option('footer_columns') == 'doubleleft') { ?>
				<div class="small-12 medium-6 columns">
					<?php dynamic_sidebar('footer1'); ?>
				</div>
				<div class="small-12 medium-3 columns">
					<?php dynamic_sidebar('footer2'); ?>
				</div>
				<div class="small-12 medium-3 columns">
					<?php dynamic_sidebar('footer3'); ?>
				</div>
				<?php } elseif (ot_get_option('footer_columns') == 'doubleright') { ?>
				<div class="small-12 medium-3 columns">
					<?php dynamic_sidebar('footer1'); ?>
				</div>
				<div class="small-12 medium-3 columns">
					<?php dynamic_sidebar('footer2'); ?>
				</div>
				<div class="small-12 medium-6 columns">
					<?php dynamic_sidebar('footer3'); ?>
				</div>
				<?php } elseif (ot_get_option('footer_columns') == 'fivecolumns') { ?>
				<div class="small-12 medium-2 columns">
					<?php dynamic_sidebar('footer1'); ?>
				</div>
				<div class="small-12 medium-3 columns">
					<?php dynamic_sidebar('footer2'); ?>
				</div>
				<div class="small-12 medium-2 columns">
					<?php dynamic_sidebar('footer3'); ?>
				</div>
				<div class="small-12 medium-3 columns">
					<?php dynamic_sidebar('footer4'); ?>
				</div>
				<div class="small-12 medium-2 columns">
					<?php dynamic_sidebar('footer5'); ?>
				</div>
				<?php }?>
			</aside>
		<?php } else if ($footer_products_radio == 'just') { ?>
			<?php if (is_array($footer_products_sections)) { ?>
				<aside id="footer_tabs" class="footer_tabs">
					<ul>
						<?php if (in_array('just-arrived',$footer_products_sections)) { ?>
						<li><a href="#" class="active" data-type="latest-products"><?php _e('Just Arrived',THB_THEME_NAME); ?></a></li>
						<?php } ?>
						<?php if (in_array('best-sellers',$footer_products_sections)) { ?>
						<li><a href="#" <?php if (!in_array('just-arrived',$footer_products_sections)) { ?>class="active" <?php } ?>data-type="best-sellers"><?php _e('Best Sellers',THB_THEME_NAME); ?></a></li>
						<?php } ?>
						<?php if (in_array('featured',$footer_products_sections)) { ?>
						<li><a href="#" <?php if (!in_array('just-arrived',$footer_products_sections) && !in_array('best-sellers',$footer_products_sections) ) { ?>class="active" <?php } ?>data-type="featured-products"><?php _e('Featured',THB_THEME_NAME); ?></a></li>
						<?php } ?>
					</ul>
				</aside>
			<?php } ?>
			<?php if (!empty($footer_products_sections)) { ?>
				<?php 
					global $post;
					$catalog_mode = ot_get_option('shop_catalog_mode', 'off');
					$shop_product_listing = ot_get_option('shop_product_listing', 'style1');
					$args = array(
						'post_type' => 'product',
						'post_status' => 'publish',
						'ignore_sticky_posts'   => 1,
						'posts_per_page' => $footer_products_count,
						'no_found_rows' => true,
						'suppress_filters' => 0
					);

					$products = new WP_Query( $args );
				?>
				<div class="carousel-container">
					<div class="carousel products no-padding owl row" data-columns="6" data-navigation="true" data-loop="true" data-bgcheck="false">
						<?php while ( $products->have_posts() ) : $products->the_post(); ?>
								<?php $product = get_product( $products->post->ID ); ?>
								<article itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" <?php post_class("post small-6 medium-4 large-2 columns product " . $shop_product_listing); ?>>

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
								</article><!-- end product -->

							<?php endwhile; // end of the loop. ?>

					</div>
					<div class="ai-dotted ai-indicator"><span class="ai-inner1"></span><span class="ai-inner2"></span><span class="ai-inner3"></span></div>
				</div>
			<?php } ?>
		<?php } else if ($footer_products_radio == 'cat') { ?>
			<?php if (is_array($footer_products_cat)) { ?>
				<aside id="footer_tabs" class="footer_tabs">
					<ul>
						<?php $i = 0; foreach($footer_products_cat as $cat) { ?>
						<?php $category = get_term_by('id',$cat,'product_cat'); ?>
						<li><a href="#"<?php if ($i == 0) { echo ' class="active"'; } ?> data-type="<?php echo $cat; ?>"><?php echo $category->name; ?></a></li>
						<?php $i++; } ?>
					</ul>
				</aside>
			<?php } ?>
			<?php if (!empty($footer_products_cat)) { ?>
			
				<?php
					global $post;
					$catalog_mode = ot_get_option('shop_catalog_mode', 'off');
	 				$category = get_term_by('id',reset($footer_products_cat),'product_cat'); 
	 				$args = array(
						'post_type' => 'product',
						'post_status' => 'publish',
						'ignore_sticky_posts'   => 1,
						'product_cat' => $category->slug,
						'posts_per_page' => $footer_products_count,
						'no_found_rows' => true,
						'suppress_filters' => 0
					);	
					$products = new WP_Query( $args );
				?>
				<div class="carousel-container">
					<div class="carousel products no-padding owl row" data-columns="6" data-navigation="true" data-bgcheck="false">
						<?php while ( $products->have_posts() ) : $products->the_post(); ?>
								<?php $product = get_product( $products->post->ID ); ?>
								<article itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" <?php post_class("post small-6 medium-4 large-2 columns product"); ?>>

								<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

									<figure class="fresco">

											<?php
												$image_html = "";

												do_action( 'thb_product_badge');

												if ( has_post_thumbnail() ) {
													$image_html = wp_get_attachment_image( get_post_thumbnail_id(), 'shop_catalog' );					
												}
											?>
											<?php echo $image_html; ?>			
											<div class="overlay">

												<a class="quick quick-view" data-id="<?php echo $post->ID; ?>" href="#"><i class="icon-budicon-545"></i></a>
											</div>
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
								</article><!-- end product -->

							<?php endwhile; // end of the loop. ?>

					</div>
					<div class="ai-dotted ai-indicator"><span class="ai-inner1"></span><span class="ai-inner2"></span><span class="ai-inner3"></span></div>
				</div>
			<?php } ?>
		<?php } // $footer_products_radio ?>
	</section>
<?php }
}
add_action( 'thb_footer_products', 'thb_footer_products',3 );

/* Side Cart */
function thb_side_cart() {
 if(class_exists('woocommerce')) {
 ?>
 	<nav id="side-cart" class="custom_scroll">
 	 	<header class="animation right-to-left">
 	 		<h6><?php _e('YOUR SHOPPING BAG',THB_THEME_NAME); ?></h6>
 	 	</header>
 		<?php if (sizeof(WC()->cart->cart_contents)>0) : ?>
 			<ul>
 		<?php foreach (WC()->cart->cart_contents as $cart_item_key => $cart_item) :
 		    $_product = $cart_item['data'];
 		    if ($_product->exists() && $cart_item['quantity']>0) :?>
 			<li class="animation right-to-left">
 				<figure>
 					<?php   echo '<a class="cart_list_product_img" href="'.get_permalink($cart_item['product_id']).'">' . $_product->get_image().'</a>'; ?>
 				</figure>
 	
 				<?php echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf('<a href="%s" class="remove" title="%s">×</a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __('Remove this item',THB_THEME_NAME) ), $cart_item_key ); ?>
 	
 				<div class="list_content">
 					<?php
 					 $product_title = $_product->get_title();
 				       echo '<h5><a href="'.get_permalink($cart_item['product_id']).'">' . apply_filters('woocommerce_cart_widget_product_title', $product_title, $_product) . '</a></h5>';
 				       echo '<div class="quantity">'.$cart_item['quantity'].'</div><span class="cross">×</span>';
 				       echo '<div class="price">'.woocommerce_price($_product->get_price()).'</div>';
 					?>
 				</div>
 			</li>
 		<?php endif; endforeach; ?>
 			</ul>
 			<div class="subtotal animation right-to-left">
 			    <?php _e('Subtotal', THB_THEME_NAME); ?><span><?php echo WC()->cart->get_cart_total(); ?></span>
 			</div>
 			<div class="buttons">
 				<a href="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" class="btn large full animation right-to-left"><?php _e('View Shopping Bag', THB_THEME_NAME); ?></a>
 		
 				<a href="<?php echo esc_url( WC()->cart->get_checkout_url() ); ?>" class="btn large full black animation right-to-left"><span class="icon"><i class="icon-budicon-423"></i></span><?php _e('Checkout', THB_THEME_NAME); ?></a>
 			</div>
 		<?php else: ?>
 			<div class="cart-empty animation fade-in text-center">
				<figure></figure>
				<p class="message"><?php _e( 'Your cart is currently empty.', THB_THEME_NAME) ?></p>
				<?php do_action( 'woocommerce_cart_is_empty' ); ?>
				
				<p class="return-to-shop"><a class="button wc-backward" href="<?php echo apply_filters( 'woocommerce_return_to_shop_redirect', get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"><?php _e( 'Return To Shop', THB_THEME_NAME ) ?></a></p>
 			</div>
 		<?php endif; ?>
 	</nav>
<?php }
}
add_action( 'thb_side_cart', 'thb_side_cart',3 );

/* Side Cart Update */
function thb_woocomerce_side_cart_update($fragments) {
		ob_start();
		?>
			<nav id="side-cart" class="custom_scroll">
			 	<header class="animation right-to-left">
			 		<h6><?php _e('YOUR SHOPPING BAG',THB_THEME_NAME); ?></h6>
			 	</header>
				<?php if (sizeof(WC()->cart->cart_contents)>0) : ?>
					<ul>
				<?php foreach (WC()->cart->cart_contents as $cart_item_key => $cart_item) :
				    $_product = $cart_item['data'];
				    if ($_product->exists() && $cart_item['quantity']>0) :?>
					<li class="animation right-to-left">
						<figure>
							<?php   echo '<a class="cart_list_product_img" href="'.get_permalink($cart_item['product_id']).'">' . $_product->get_image().'</a>'; ?>
						</figure>
			
						<?php echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf('<a href="%s" class="remove" title="%s">×</a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __('Remove this item',THB_THEME_NAME) ), $cart_item_key ); ?>
			
						<div class="list_content">
							<?php
							 $product_title = $_product->get_title();
						       echo '<h5><a href="'.get_permalink($cart_item['product_id']).'">' . apply_filters('woocommerce_cart_widget_product_title', $product_title, $_product) . '</a></h5>';
						       echo '<div class="quantity">'.$cart_item['quantity'].'</div><span class="cross">×</span>';
						       echo '<div class="price">'.woocommerce_price($_product->get_price()).'</div>';
							?>
						</div>
					</li>
				<?php endif; endforeach; ?>
					</ul>
					<div class="subtotal animation right-to-left">
					    <?php _e('Subtotal', THB_THEME_NAME); ?><span><?php echo WC()->cart->get_cart_total(); ?></span>
					</div>
					<div class="buttons">
						<a href="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" class="btn large full animation right-to-left"><?php _e('View Shopping Bag', THB_THEME_NAME); ?></a>
				
						<a href="<?php echo esc_url( WC()->cart->get_checkout_url() ); ?>" class="btn large full black animation right-to-left"><span class="icon"><i class="icon-budicon-423"></i></span><?php _e('Checkout', THB_THEME_NAME); ?></a>
					</div>
				<?php else: ?>
					<div class="cart-empty animation fade-in text-center">
						<figure></figure>
						<p class="message"><?php _e( 'Your cart is currently empty.', THB_THEME_NAME) ?></p>
						<?php do_action( 'woocommerce_cart_is_empty' ); ?>
						
						<p class="return-to-shop"><a class="button wc-backward" href="<?php echo apply_filters( 'woocommerce_return_to_shop_redirect', get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"><?php _e( 'Return To Shop', THB_THEME_NAME ) ?></a></p>
					</div>
				<?php endif; ?>
			</nav>
		<?php
		$fragments['#side-cart'] = ob_get_clean();
		return $fragments;

}
add_filter('add_to_cart_fragments', 'thb_woocomerce_side_cart_update');

/* Header Cart */
function thb_quick_cart() {
 if(class_exists('woocommerce')) {
 ?>
	<a id="quick_cart" data-target="open-cart" href="<?php echo WC()->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart',THB_THEME_NAME); ?>">
		<svg version="1.1" id="cart-icon"
			 xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="26px" height="31px"
			 viewBox="0 0 26 31" enable-background="new 0 0 26 31" xml:space="preserve">
		<g id="Rounded_Rectangle_4_copy">
			<g>
				<path class="icon-fill" fill-rule="evenodd" clip-rule="evenodd" fill="#151515" d="M9.497,6.556c0-2.979,0.469-4.403,3.503-4.403
					s3.502,1.424,3.502,4.403v0.527h2.197V6.556c0-4.169-1.453-6.561-5.699-6.561S7.3,2.387,7.3,6.556v0.527h2.197V6.556z M26,28
					l-1.186-9.485l-0.82-7.433c0-0.952-0.761-1.726-1.715-1.785C21.891,9.112,21.46,9,21,9H5C4.54,9,4.109,9.112,3.721,9.297
					c-0.955,0.06-1.715,0.833-1.715,1.785l-0.824,7.466L0,28c0,0.217,0.025,0.427,0.068,0.63l-0.063,0.576
					c0,0.991,0.82,1.798,1.83,1.798h22.328c1.009,0,1.83-0.807,1.83-1.798l-0.063-0.575C25.975,28.428,26,28.217,26,28z"/>
			</g>
		</g>
		</svg>
		<span class="float_count" id="float_count"><?php echo WC()->cart->cart_contents_count; ?></span>
	</a>
<?php }
}
add_action( 'thb_quick_cart', 'thb_quick_cart',3 );

/* Header Wishlist */
function thb_quick_wishlist() {
 global $yith_wcwl;
 ?>
	<?php if ($yith_wcwl) { ?>
		<a href="<?php echo $yith_wcwl->get_wishlist_url(); ?>" title="<?php _e('Wishlist', THB_THEME_NAME); ?>" id="quick_wishlist"><i class="fa fa-heart-o"></i></a>
	<?php } ?>
<?php
}
add_action( 'thb_quick_wishlist', 'thb_quick_wishlist',3 );

/* Product Badges */
function thb_product_badge() {
 global $post, $product;
 	if (thb_out_of_stock()) {
		echo '<span class="badge out-of-stock">' . __( 'Out of Stock', THB_THEME_NAME ) . '</span>';
	} else if ( $product->is_on_sale() ) {
		echo apply_filters('woocommerce_sale_flash', '<span class="badge onsale">'.__( 'Sale',THB_THEME_NAME ).'</span>', $post, $product);
	}  else {
		$postdate 		= get_the_time( 'Y-m-d' );			// Post date
		$postdatestamp 	= strtotime( $postdate );			// Timestamped post date
		$newness = ot_get_option('shop_newness', 7);
		if ( ( time() - ( 60 * 60 * 24 * $newness ) ) < $postdatestamp) { // If the product was published within the newness time frame display the new badge
			echo '<span class="badge new">' . __( 'Just Arrived', THB_THEME_NAME ) . '</span>';
		}
		
	}
}
add_action( 'thb_product_badge', 'thb_product_badge',3 );

/* WOOCOMMERCE CART LINK */
function thb_woocomerce_ajax_cart_update($fragments) {
	if(class_exists('woocommerce')) {

		ob_start();
		?>
			<span class="float_count" id="float_count"><?php echo WC()->cart->cart_contents_count; ?></span>
			
			<script type="text/javascript">// <![CDATA[
			jQuery(function($){
				window.favicon.badge(<?php echo WC()->cart->cart_contents_count; ?>);
			});// ]]>
			</script>
		<?php
		$fragments['#float_count'] = ob_get_clean();
		return $fragments;
	}
}
add_filter('add_to_cart_fragments', 'thb_woocomerce_ajax_cart_update');


/* The Quickview Ajax Output */
function quickview() {
	global $post, $product;
	$id =  $_POST["id"];
	$post = get_post($id);
	$product = get_product($id);

	ob_start();

	wc_get_template( 'content-single-product-lightbox.php');

	$output = ob_get_contents();
	ob_end_clean();
	echo $output;
	die();
}
add_action('wp_ajax_quickview', 'quickview');
add_action('wp_ajax_nopriv_quickview', 'quickview');

/* Image Dimensions */
global $pagenow;
if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' ) add_action( 'init', 'thb_woocommerce_image_dimensions', 1 );

function thb_woocommerce_image_dimensions() {
  	$catalog = array(
		'width' 	=> '360',	// px
		'height'	=> '450',	// px
		'crop'		=> 1 		// true
	);

	$single = array(
		'width' 	=> '1000',	// px
		'height'	=> '1000',	// px
		'crop'		=> 1 		// true
	);

	$thumbnail = array(
		'width' 	=> '90',	// px
		'height'	=> '90',	// px
		'crop'		=> 1 		// false
	);

	// Image sizes
	update_option( 'shop_catalog_image_size', $catalog ); 		// Product category thumbs
	update_option( 'shop_single_image_size', $single ); 		// Single product image
	update_option( 'shop_thumbnail_image_size', $thumbnail ); 	// Image gallery thumbs
}

/* Products per Page */
function thb_ppp_setup() {

	if( isset( $_GET['show_products']) ){
		$getproducts = $_GET['show_products'];
		if ($getproducts == "all") {
	    	add_filter( 'loop_shop_per_page', create_function( '$cols', 'return -1;' ) );
	    } else {
	    	add_filter( 'loop_shop_per_page', create_function( '$cols', 'return '.$getproducts.';' ) );
	    }
	} else {
	    $products_per_page = ot_get_option('shop_product_count', 12);
	    add_filter( 'loop_shop_per_page', create_function( '$cols', 'return ' . $products_per_page . ';' ), 20 );
	}
}
add_action( 'after_setup_theme', 'thb_ppp_setup' );

/* Product Page - Move Tabs/Accordion next to image */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 31 );

/* Product Page - Remove breadcrumbs */
remove_action( 'woocommerce_before_main_content','woocommerce_breadcrumb', 20, 0);

/* Product Page - Remove Sale Flash */
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash' , 10);

/* Product Page - Remove Tabs */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs' , 10);

/* Product Page - Remove Related Products */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

/* Product Page - Move Upsells */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
add_action( 'woocommerce_after_single_product', 'woocommerce_upsell_display', 70 );

/* Product Page - Move Sharing to top */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 35 );

/* Product Page - Add Sizing Guide */
add_action( 'woocommerce_after_add_to_cart_button', 'thb_sizing_guide', 30 );

function thb_sizing_guide() {
	$sizing_guide = get_post_meta(get_the_ID(), 'sizing_guide', true);
	$sizing_guide_content = get_post_meta(get_the_ID(), 'sizing_guide_content', true);
	
	if ($sizing_guide == 'on') {
		echo '<a href="#sizing-popup" rel="inline" class="sizing_guide" data-class="upsell-popup">'.__("VIEW SIZING GUIDE", THB_THEME_NAME).'</a>';
		
		?>
		<aside id="sizing-popup" class="mfp-hide theme-popup text-left">
				<?php echo do_shortcode($sizing_guide_content); ?>
		</aside>
		<?php
	}
}

/* Product Page - Catalog Mode */
function thb_catalog_setup() {
	$catalog_mode = ot_get_option('shop_catalog_mode', 'off');
	if ($catalog_mode == 'on') {
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	}
}
add_action( 'after_setup_theme', 'thb_catalog_setup' );

/* Custom Metabox for Category Pages */
if(function_exists('get_term_meta')){
	function thb_taxonomy_meta_field($term) {

		$t_id = $term->term_id;
		$term_meta = get_term_meta($t_id,'cat_meta');
		if(!$term_meta){$term_meta = add_term_meta($t_id, 'cat_meta', '');}
		 ?>
		<tr>
		<th scope="row" valign="top"><label for="term_meta[cat_header]"><?php _e( 'Category Header', THB_THEME_NAME ); ?></label></th>
			<td>
					<?php
					$content = esc_attr( $term_meta[0]['cat_header'] ) ? esc_attr( $term_meta[0]['cat_header'] ) : '';
					
					wp_editor(
					  $content,
					  "term_meta[cat_header]",
					  array(
					    'wpautop'       => true,
					    'media_buttons' => true,
					    'textarea_name' => "term_meta[cat_header]",
					    'textarea_rows' => "6",
					    'tinymce'       => true
					  )
					);
				  ?>
				<p class="description"><?php _e( 'This content will be displayed at the top of this category. You can use your shortcodes here. <small>You can create your content using visual composer and then copy its text here</small>',THB_THEME_NAME ); ?></p>
			</td>
		</tr>
	<?php
	}
	add_action( 'product_cat_edit_form_fields', 'thb_taxonomy_meta_field', 10, 2 );

	/* Save Custom Meta Data */
	function thb_save_taxonomy_custom_meta( $term_id ) {
		if ( isset( $_POST['term_meta'] ) ) {
			$t_id = $term_id;
			$term_meta = get_term_meta($t_id,'cat_meta');
			$cat_keys = array_keys( $_POST['term_meta'] );
			foreach ( $cat_keys as $key ) {
				if ( isset ( $_POST['term_meta'][$key] ) ) {
					$term_meta[$key] = $_POST['term_meta'][$key];
				}
			}
			update_term_meta($term_id, 'cat_meta', $term_meta);

		}
	}
	add_action( 'edited_product_cat', 'thb_save_taxonomy_custom_meta', 10, 2 );
}

/* Redirect to Homepage when customer log out */
add_filter('logout_url', 'new_logout_url', 10, 2);
function new_logout_url($logouturl, $redir) {
	$redirect = get_option('siteurl');
	return $logouturl . '&amp;redirect_to=' . urlencode($redirect);
}
/* Add Prices inside variation dropdown */
function thb_vp_setup() {
	if ( ot_get_option('variation_dropdown_prices') == 'on') {	

		/* Add Prices inside variation dropdown */

		add_filter( 'woocommerce_variation_option_name', 'display_price_in_variation_option_name' );

		function display_price_in_variation_option_name( $term ) {
			global $wpdb, $product;

			$result = $wpdb->get_col( "SELECT slug FROM {$wpdb->prefix}terms WHERE name = '$term'" );

			$term_slug = ( !empty( $result ) ) ? $result[0] : $term;

			$query = "SELECT postmeta.post_id AS product_id
			FROM {$wpdb->prefix}postmeta AS postmeta
			LEFT JOIN {$wpdb->prefix}posts AS products ON ( products.ID = postmeta.post_id )
			WHERE postmeta.meta_key LIKE 'attribute_%'
			AND postmeta.meta_value = '$term_slug'
			AND products.post_parent = $product->id";

			$variation_id = $wpdb->get_col( $query );

			$parent = wp_get_post_parent_id( $variation_id[0] );

			if ( $parent > 0 ) {
				$_product = new WC_Product_Variation( $variation_id[0] );
				
				//this is where you can actually customize how the price is displayed
				return wp_strip_all_tags($term . ' (' . woocommerce_price( $_product->get_price() ) . ')', true);
			}
			return wp_strip_all_tags($term, true);

		}
	}
}
add_action( 'after_setup_theme', 'thb_vp_setup' );

/* Disable Variations when Sold out? */
function thb_dd_setup() {
	if ( ot_get_option('variation_dropdown_soldout') == 'on') {
		add_action( 'woocommerce_after_add_to_cart_form', 'woocommerce_sold_out_filter' );
		function woocommerce_sold_out_filter() {
		  ?>
		<script type="text/javascript">
		(function($) {
		   // disable and add 'sold out' to product variations
			var product_variations = $('form.variations_form').data('product_variations');
			if (product_variations) {
				var attribute_name = $('form.variations_form').find('select').attr('name');
				$.each(product_variations, function(key, value) {
					if (!value.is_in_stock) {
						var variation_text = $(".variations option[value='" + value.attributes[attribute_name] + "']").text();
						$(".variations option[value='" + value.attributes[attribute_name] + "']").attr('disabled', 'disabled').text(variation_text + ' - Sold Out');
					}
				});
			}
		})(jQuery);
		</script><?php
		}
	}
}
add_action( 'after_setup_theme', 'thb_dd_setup' );

?>
