<?php
/**
 * Cart Page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;
 
?>

<section class="my_woocommerce_page my_cart">
	<?php do_action( 'woocommerce_before_cart' ); ?>
	<div class="row full-width-row">
		<form action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post" data-equal=">.columns" class="row no-padding">
			<div class="small-12 medium-8 columns">
			<?php wc_print_notices(); ?>
			<?php do_action( 'woocommerce_before_cart_table' ); ?>
			
			<table class="shopping_bag cart" cellspacing="0">
				<tbody>
					<?php do_action( 'woocommerce_before_cart_contents' ); ?>
			
					<?php
							foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
								$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
								$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
					
								if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
									?>
								<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
									
			
									<!-- The thumbnail -->
									<td class="product-thumbnail">
										<?php
											$image_id = get_post_thumbnail_id($product_id);
											$image_link = wp_get_attachment_image_src($image_id,'full');
											$image = aq_resize( $image_link[0], 190, 240, true, false);
											$image_title = esc_attr( get_the_title($product_id) );
											
											$image_src = '<img  src="'.$image[0].'" width="'.$image[1].'" height="'.$image[2].'" title="'.$image_title.'" />';
										
											$thumbnail = apply_filters( 'woocommerce_in_cart_product_thumbnail', $image_src, $cart_item, $cart_item_key );
			
											if ( ! $_product->is_visible() || ( ! empty( $_product->variation_id ) && ! $_product->parent_is_visible() ) )
												echo $thumbnail;
											else
												printf('<a href="%s">%s</a>', $_product->get_permalink(), $thumbnail );
												
										?>
									</td>
			
									<!-- Product Name -->
									<td class="product-name">
										<?php
											$size = sizeof( get_the_terms( $product_id, 'product_cat' ) );
											echo $_product->get_categories( ', ', '<span class="posted_in">' . _n( '', '', $size,THB_THEME_NAME ) . ' ', '</span>' );
										?>
										<?php
											if ( ! $_product->is_visible() )
												echo '<h6>'.apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ).'</h6>';
											else
												echo '<h6>'.apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', $_product->get_permalink(), $_product->get_title() ), $cart_item, $cart_item_key ).'</h6>';
				
											// Meta data
											echo WC()->cart->get_item_data( $cart_item );
				
				               				// Backorder notification
				               				if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) )
				               					echo '<p class="backorder_notification">' . __( 'Available on backorder',THB_THEME_NAME ) . '</p>';
										?>
									</td>
			
									<!-- Quantity inputs -->
									<td class="product-quantity">
										<?php
											if ( $_product->is_sold_individually() ) {
												$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
											} else {
												$product_quantity = woocommerce_quantity_input( array(
													'input_name'  => "cart[{$cart_item_key}][qty]",
													'input_value' => $cart_item['quantity'],
													'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
												), $_product, false );
											}
				
											echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key );
										?>
									</td>
			
									<!-- Product subtotal -->
									<td class="product-subtotal">
										<?php
											echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
										?>
									</td>
									<!-- Remove from cart link -->
									<td class="product-remove">
										<?php
											echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="remove" title="%s">&times;</i></a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __( 'Remove this item',THB_THEME_NAME ) ), $cart_item_key );
										?>
									</td>
								</tr>
								<?php
						}
					}
			
					do_action( 'woocommerce_cart_contents' );
					?>
					<?php do_action( 'woocommerce_after_cart_contents' ); ?>
				</tbody>
			</table>
				
			<?php do_action( 'woocommerce_after_cart_table' ); ?>
			</div>
			<div class="small-12 medium-4 columns">
				<aside class="cart-collaterals">
						<div class="inner">
							<a href="<?php echo apply_filters( 'woocommerce_return_to_shop_redirect', get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">&larr; <?php _e( 'Continue Shopping',THB_THEME_NAME ); ?></a>
						</div>
						<div class="inner">
							<input type="submit" class="update-button button black" name="update_cart" value="<?php _e( 'Update',THB_THEME_NAME ); ?>" />
							<input type="submit" class="checkout-button button wc-forward" name="proceed" value="<?php _e( 'Proceed to Checkout',THB_THEME_NAME ); ?>" />
							<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
							<?php wp_nonce_field( 'woocommerce-cart' ); ?>
							
							<?php do_action('woocommerce_cart_collaterals'); ?>
							
							<?php woocommerce_cart_totals(); ?>
	
							<?php if ( WC()->cart->coupons_enabled() ) { ?>
								<input type="text" name="coupon_code" id="coupon_code" value="" class="full" placeholder="<?php _e( 'Enter Coupon Code', THB_THEME_NAME ); ?>"/>
								<input type="submit" class="apply_coupon" name="apply_coupon" value="<?php _e( 'Apply',THB_THEME_NAME ); ?>" />
								<?php do_action('woocommerce_cart_coupon'); ?>
							<?php } ?>
							<?php woocommerce_shipping_calculator(); ?>
						</div>
				</aside>
			</div>
		</form>
	</div>
	<?php do_action( 'woocommerce_after_cart' ); ?>
</section>
