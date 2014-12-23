<?php
/**
 * Thankyou page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce; ?>
<section class="my_woocommerce_page my_cart">
	<ul id="shippingsteps" class="row full-width-row no-padding">
		<li class="first small-12 medium-6 large-3 columns"><span>1</span><a href="#"><?php _e('Checkout Method', THB_THEME_NAME); ?></a></li>
		<li class="small-12 medium-6 large-3 columns"><span>2</span><a href="#"><?php _e('Billing &amp; Shipping', THB_THEME_NAME); ?></a></li>
		<li class="small-12 medium-6 large-3 columns"><span>3</span><a href="#"><?php _e('Your Order &amp; Payment', THB_THEME_NAME); ?></a></li>
		<li class="small-12 medium-6 large-3 columns active"><span>4</span><a href="#"><?php _e('Confirmation', THB_THEME_NAME); ?></a></li>
	</ul>
<section class="section text-center" id="checkout_thankyou">
<?php if ( $order ) : ?>
	<div class="row">
	<?php if ( $order->has_status( 'failed' ) ) : ?>
		<div class="small-12 columns">
			<div class="smalltitle"><?php _e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction.',THB_THEME_NAME ); ?></div>
	
			<p><?php
				if ( is_user_logged_in() )
					_e( 'Please attempt your purchase again or go to your account page.',THB_THEME_NAME );
				else
					_e( 'Please attempt your purchase again.',THB_THEME_NAME );
			?></p>
	
			<p>
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php _e( 'Pay',THB_THEME_NAME ) ?></a>
				<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>" class="button pay"><?php _e( 'My Account',THB_THEME_NAME ); ?></a>
				<?php endif; ?>
			</p>
		</div>
	<?php else : ?>
		<div class="small-12 columns">
			<div class="smalltitle"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.',THB_THEME_NAME  ), $order ); ?></div>
			
			<ul class="order_details">
				<li class="order">
					<?php _e( 'Order:',THB_THEME_NAME ); ?>
					<strong><?php echo $order->get_order_number(); ?></strong>
				</li>
				<li class="date">
					<?php _e( 'Date:',THB_THEME_NAME ); ?>
					<strong><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></strong>
				</li>
				<li class="total">
					<?php _e( 'Total:',THB_THEME_NAME ); ?>
					<strong><?php echo $order->get_formatted_order_total(); ?></strong>
				</li>
				<?php if ( $order->payment_method_title ) : ?>
				<li class="method">
					<?php _e( 'Payment method:',THB_THEME_NAME ); ?>
					<strong><?php echo $order->payment_method_title; ?></strong>
				</li>
				<?php endif; ?>
			</ul>
		</div>
	<?php endif; ?>
		
		<div class="small-12 columns text-center">
			<?php do_action( 'woocommerce_thankyou_' . $order->payment_method, $order->id ); ?>
		</div>
	</div>
<?php else : ?>

	<div class="small-12 columns text-center">
		<div class="smalltitle"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', __( 'Thank you. Your order has been received.',THB_THEME_NAME  ), null ); ?></div>
	</div>
<?php endif; ?>
</section>