<?php
/**
 * Order details
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

$order = wc_get_order( $order_id );
?>
<?php if (!is_checkout()) { ?>
<section class="my_woocommerce_page page-padding">
	<div class="custom_scroll">
<?php } ?>
		<div class="row full-width-row no-padding">
			<div class="text-center"><a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" class="back_to_account"><?php _e("<small>Back to</small> My Account", THB_THEME_NAME); ?></a></div>
			<div class="small-12 text-center columns">
				<?php wc_print_notices(); ?>
			</div>
			<div class="small-12 medium-6 columns">
			<div class="login-section">
			<div class="smalltitle"><?php _e( 'Order Details',THB_THEME_NAME ); ?></div>
			<p><?php echo $order->post->post_title; ?></p>
			<p class="order-info"><?php printf( __( 'Order #<mark class="order-number">%s</mark> was placed on <mark class="order-date">%s</mark> and is currently <mark class="order-status">%s</mark>.', 'woocommerce' ), $order->get_order_number(), date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ), wc_get_order_status_name( $order->get_status() ) ); ?></p>
			
			<table class="shopping_bag order_table">
				<thead>
					<tr>
						<th class="product-name"><?php _e( 'Product',THB_THEME_NAME ); ?></th>
						<th class="product-subtotal"><?php _e( 'Total',THB_THEME_NAME ); ?></th>
					</tr>
				</thead>
				<tfoot>
				<?php
					if ( $totals = $order->get_order_item_totals() ) foreach ( $totals as $total ) :
						?>
						<tr>
							<th scope="row"><?php echo $total['label']; ?></th>
							<td><?php echo $total['value']; ?></td>
						</tr>
						<?php
					endforeach;
				?>
				</tfoot>
				<tbody>
					<?php
					if ( sizeof( $order->get_items() ) > 0 ) {

						foreach( $order->get_items() as $item ) {
							$_product     = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
							$item_meta    = new WC_Order_Item_Meta( $item['item_meta'], $_product );

							?>
							<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
								<td class="product-name">
									<?php
										if ( $_product && ! $_product->is_visible() )
											echo apply_filters( 'woocommerce_order_item_name', $item['name'], $item );
										else
											echo apply_filters( 'woocommerce_order_item_name', sprintf( '<h6><a href="%s">%s</a></h6>', get_permalink( $item['product_id'] ), $item['name'] ), $item );

										echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times; %s', $item['qty'] ) . '</strong>', $item );

										$item_meta->display();

										if ( $_product && $_product->exists() && $_product->is_downloadable() && $order->is_download_permitted() ) {

											$download_files = $order->get_item_downloads( $item );
											$i              = 0;
											$links          = array();

											foreach ( $download_files as $download_id => $file ) {
												$i++;

												$links[] = '<small><a href="' . esc_url( $file['download_url'] ) . '">' . sprintf( __( 'Download file%s',THB_THEME_NAME ), ( count( $download_files ) > 1 ? ' ' . $i . ': ' : ': ' ) ) . esc_html( $file['name'] ) . '</a></small>';
											}

											echo '<br/>' . implode( '<br/>', $links );
										}
									?>
								</td>
								<td class="product-total">
									<?php echo $order->get_formatted_line_subtotal( $item ); ?>
								</td>
							</tr>
							<?php

							if ( $order->has_status( array( 'completed', 'processing' ) ) && ( $purchase_note = get_post_meta( $_product->id, '_purchase_note', true ) ) ) {
								?>
								<tr class="product-purchase-note">
									<td colspan="3"><?php echo wpautop( do_shortcode( $purchase_note ) ); ?></td>
								</tr>
								<?php
							}
						}
					}

					do_action( 'woocommerce_order_items_table', $order );
					?>
				</tbody>
			</table>

			<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
				</div>
			</div>
				<div class="small-12 medium-6 columns">
					<div class="login-section">
			<div class="smalltitle"><?php _e( 'Customer details',THB_THEME_NAME ); ?></div>


			<dl class="customer_details">
			<?php
				if ( $order->billing_email ) echo '<dt>' . __( 'Email:', THB_THEME_NAME) . '</dt><dd>' . $order->billing_email . '</dd>';
				if ( $order->billing_phone ) echo '<dt>' . __( 'Telephone:', THB_THEME_NAME) . '</dt><dd>' . $order->billing_phone . '</dd>';

				// Additional customer details hook
				do_action( 'woocommerce_order_details_after_customer_details', $order );
			?>
			</dl>

			<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) : ?>

			<div class="row addresses">

				<div class="small-12 medium-6 columns address">

			<?php endif; ?>

					<h3><?php _e( 'Billing Address',THB_THEME_NAME ); ?></h3>
					<address><p>
						<?php
							if ( ! $order->get_formatted_billing_address() ) _e( 'N/A',THB_THEME_NAME ); else echo $order->get_formatted_billing_address();
						?>
					</p></address>

			<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) : ?>

				</div><!-- /.col-1 -->

				<div class="small-12 medium-6 columns address">

					<h3><?php _e( 'Shipping Address',THB_THEME_NAME ); ?></h3>
					<address><p>
						<?php
							if ( ! $order->get_formatted_shipping_address() ) _e( 'N/A',THB_THEME_NAME ); else echo $order->get_formatted_shipping_address();
						?>
					</p></address>

				</div><!-- /.col-2 -->

			</div><!-- /.col2-set -->

			<?php endif; ?>
					</div>
				</div>
		</div>
		</div>
<?php if (!is_checkout()) { ?>
	</div>
</section>
<?php } ?>
<?php do_action( 'woocommerce_view_order', $order_id ); ?>