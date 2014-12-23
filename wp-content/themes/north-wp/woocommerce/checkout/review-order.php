<?php
/**
 * Review order form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.8
 */


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<?php if ( ! is_ajax() ) : ?><section class="section" id="order_review"><?php endif; ?>
	<div class="row full-width-row no-padding">
		<div class="small-12 medium-6 columns">
			<div class="login-section">
			<div class="smalltitle"><?php _e( 'Your Order',THB_THEME_NAME ); ?></div>
			<table class="shopping_bag order_table">
				<thead>
					<tr>
						<th class="product-name" colspan="3"><?php _e( 'Product',THB_THEME_NAME ); ?></th>
						<th class="product-subtotal"><?php _e( 'Total',THB_THEME_NAME ); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr class="cart-subtotal">
						<th colspan="3"><?php _e( 'Cart Subtotal',THB_THEME_NAME ); ?></th>
						<td class="product-subtotal"><?php wc_cart_totals_subtotal_html(); ?></td>
					</tr>
			
			<?php foreach ( WC()->cart->get_coupons( 'cart' ) as $code => $coupon ) : ?>
				<tr class="cart-discount coupon-<?php echo esc_attr( $code ); ?>">
					<th colspan="3"><?php _e( 'Coupon:',THB_THEME_NAME ); ?> <?php wc_cart_totals_coupon_label( $coupon ); ?></th>
					<td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
				</tr>
			<?php endforeach; ?>
			
			<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
			
		<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
		
		<?php wc_cart_totals_shipping_html(); ?>
		
		<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
		
		<?php endif; ?>
			
			<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
				<tr class="fee">
					<th colspan="3"><?php echo esc_html( $fee->name ); ?></th>
					<td><?php wc_cart_totals_fee_html( $fee ); ?></td>
				</tr>
			<?php endforeach; ?>
			
			<?php if ( WC()->cart->tax_display_cart === 'excl' ) : ?>
				<?php if ( get_option( 'woocommerce_tax_total_display' ) === 'itemized' ) : ?>
					<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
						<tr class="tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
							<th colspan="3"><?php echo esc_html( $tax->label ); ?></th>
							<td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr class="tax-total">
						<th colspan="3"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
						<td><?php echo wc_price( WC()->cart->get_taxes_total() ); ?></td>
					</tr>
				<?php endif; ?>
			<?php endif; ?>
			
			<?php foreach ( WC()->cart->get_coupons( 'order' ) as $code => $coupon ) : ?>
				<tr class="order-discount coupon-<?php echo esc_attr( $code ); ?>">
					<th colspan="3"><?php _e( 'Coupon:',THB_THEME_NAME ); ?> <?php echo esc_html( $code ); ?></th>
					<td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
				</tr>
			<?php endforeach; ?>
			
			<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>
			
			<tr class="order-total">
			<th colspan="3"><?php _e( 'Order Total',THB_THEME_NAME ); ?></th>
			<td><?php wc_cart_totals_order_total_html(); ?></td>
			</tr>
			
			<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
			
			</tfoot>
			<tbody>
			<?php
			do_action( 'woocommerce_review_order_before_cart_contents' );
			
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			
			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
			?>
			
			
			<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
			<td class="product-thumbnail">
				<?php
					$product_id = $cart_item['product_id'];
					$image_id = get_post_thumbnail_id($product_id);
					$image_link = wp_get_attachment_image_src($image_id,'full');
					$image = aq_resize( $image_link[0], 190, 240, true, false);
					$image_title = esc_attr( get_the_title($product_id) );
					
					$image_src = '<img  src="'.$image[0].'" width="'.$image[1].'" height="'.$image[2].'" title="'.$image_title.'" />';
				
					$thumbnail = apply_filters( 'woocommerce_in_cart_product_thumbnail', $image_src, $cart_item, $cart_item_key );

						echo $thumbnail;
						
				?>
			</td>
			<td class="product-name" colspan="2">
			<?php
				$size = sizeof( get_the_terms( $product_id, 'product_cat' ) );
				echo $_product->get_categories( ', ', '<span class="posted_in">' . _n( '', '', $size,THB_THEME_NAME ) . ' ', '</span>' );
			?>
			<h6><?php echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ); ?>
			<?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <span class="product-quantity">x ' . sprintf( '%s', $cart_item['quantity'] ) . '</span>', $cart_item, $cart_item_key ); ?></h6>
			<?php echo WC()->cart->get_item_data( $cart_item ); ?> 
			</td>
			<td class="product-total">
			<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
			</td>
			</tr>
			<?php
			}
			}
			
			do_action( 'woocommerce_review_order_after_cart_contents' );
			?>
			</tbody>
			</table>
		</div>
	</div>
	<div class="small-12 medium-6 columns">
		<div class="login-section">
			<?php if ( WC()->cart->coupons_enabled() ) { ?>
				<aside class="notification-box information" role="alert">
					<div class="icon"></div>
					<div class="content">
							<?php _e( 'Have a coupon?', 'woocommerce' ); ?> <a href="#" id="have_coupon"><?php _e( 'Click here to enter your code', 'woocommerce' ); ?></a><br>
					</div>
					<a href="#" class="close">×</a>
				</aside>
			<?php } ?>
	<?php do_action( 'woocommerce_review_order_before_payment' ); ?>
	
		<div id="payment">
			<?php if ( WC()->cart->needs_payment() ) : ?>
			<div class="smalltitle"><?php _e( 'Payment Method',THB_THEME_NAME ); ?></div>
			<ul class="payment_methods methods">
				<?php
					$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
					if ( ! empty( $available_gateways ) ) {
	
						// Chosen Method
						if ( isset( WC()->session->chosen_payment_method ) && isset( $available_gateways[ WC()->session->chosen_payment_method ] ) ) {
							$available_gateways[ WC()->session->chosen_payment_method ]->set_current();
						} elseif ( isset( $available_gateways[ get_option( 'woocommerce_default_gateway' ) ] ) ) {
							$available_gateways[ get_option( 'woocommerce_default_gateway' ) ]->set_current();
						} else {
							current( $available_gateways )->set_current();
						}
	
						foreach ( $available_gateways as $gateway ) {
							?>
							<li class="payment_method_<?php echo $gateway->id; ?>">
								<input id="payment_method_<?php echo $gateway->id; ?>" type="radio" class="custom_check input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />
								<label for="payment_method_<?php echo $gateway->id; ?>" class="custom_label"><?php echo $gateway->get_title(); ?> <?php echo $gateway->get_icon(); ?></label>
								<?php
									if ( $gateway->has_fields() || $gateway->get_description() ) {
										echo '<div class="payment_box payment_method_' . $gateway->id . '" ' . ( $gateway->chosen ? '' : 'style="display:none;"' ) . '>';
										$gateway->payment_fields();
										echo '</div>';
									}
								?>
							</li>
							<?php
						}
					} else {
	
						if ( ! WC()->customer->get_country() )
							$no_gateways_message = __( 'Please fill in your details above to see available payment methods.', THB_THEME_NAME );
						else
							$no_gateways_message = __( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', THB_THEME_NAME );
	
						echo '<p>' . apply_filters( 'woocommerce_no_available_payment_methods_message', $no_gateways_message ) . '</p>';
	
					}
				?>
			</ul>
			<?php endif; ?>		</div>
	
		<?php do_action( 'woocommerce_review_order_after_payment' ); ?>

			<div class="form-row place-order">
	
				<?php wp_nonce_field( 'woocommerce-process_checkout' ); ?>
	
				
				
				<?php if ( wc_get_page_id( 'terms' ) > 0 && apply_filters( 'woocommerce_checkout_show_terms', true ) ) {
					$terms_is_checked = apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) );
					?>
					
						<div class="termscontainer">
							<input type="checkbox" class="custom_check" name="terms" <?php checked( $terms_is_checked, true ); ?> id="terms" />
							<label for="terms" class="custom_label"><?php _e( 'I have read and accept the', THB_THEME_NAME ); ?> <a href="<?php echo esc_url( get_permalink(wc_get_page_id('terms')) ); ?>" target="_blank"><?php _e( 'terms &amp; conditions', THB_THEME_NAME ); ?></a></label>
						</div>
				<?php } ?>
					<?php do_action( 'woocommerce_review_order_before_submit' ); ?>
					
					<?php
					$order_button_text = apply_filters( 'woocommerce_order_button_text', __( 'Place order',THB_THEME_NAME ) );
		
					echo apply_filters( 'woocommerce_order_button_html', '<p><input type="submit" class="button large full" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '" /></p>' );
					?>
				<?php if ( wc_get_page_id( 'terms' ) > 0 && apply_filters( 'woocommerce_checkout_show_terms', true ) ) { ?>
				
				<?php } ?>
				
				<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

			</div>
		</div>
	</div>
</div>
<?php if ( ! is_ajax() ) : ?></section><?php endif; ?>