<?php
/**
 * Checkout Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;
?>
<section class="my_woocommerce_page my_cart">
	<ul id="shippingsteps" class="row full-width-row no-padding">
		<li class="first <?php if (!is_user_logged_in()) { echo 'active'; } ?> small-12 medium-6 large-3 columns"><span>1</span><a href="#" <?php if (!is_user_logged_in()) { echo 'data-target="checkout_login"'; } ?>><?php _e('Checkout Method', THB_THEME_NAME); ?></a></li>
		<li class="<?php if (is_user_logged_in()) { echo 'active '; } ?>small-12 medium-6 large-3 columns"><span>2</span><a href="#" data-target="billing_shipping"><?php _e('Billing &amp; Shipping', THB_THEME_NAME); ?></a></li>
		<li class="small-12 medium-6 large-3 columns"><span>3</span><a href="#" data-target="order_review"><?php _e('Your Order &amp; Payment', THB_THEME_NAME); ?></a></li>
		<li class="small-12 medium-6 large-3 columns"><span>4</span><a href="#"><?php _e('Confirmation', THB_THEME_NAME); ?></a></li>
	</ul>
	
	<?php 
		wc_print_notices(); 
		// do_action( 'woocommerce_before_checkout_form', $checkout );
	?>
	
	<?php if (!is_user_logged_in()) : ?>
		<section class="section" id="checkout_login">
			<div class="row full-width-row no-padding">
		    <div class="small-12 medium-4 columns">
		    	<div class="login-section">
		    		<div class="smalltitle"><?php _e('Returning customers', THB_THEME_NAME); ?></div>
		        <?php
						wc_get_template('checkout/form-login-checkout.php', array(
							'message' => __('If you have shopped with us before, please enter your details in the boxes below.', THB_THEME_NAME),
							'redirect' => get_permalink(wc_get_page_id('checkout'))
						));
						?>
					</div>
		    </div>
		    <div class="small-12 medium-4 columns newcustomers">
		    	<div class="login-section">
		    	<div class="smalltitle"><?php _e('New customers', THB_THEME_NAME); ?></div>
		    		<?php if (get_option('woocommerce_enable_guest_checkout')=="yes") { ?>
		    		<p><?php _e('You can checkout without creating an account. You will have a chance to create an account later.', THB_THEME_NAME); ?></p>
		        
		        <a href="#" class="btn large" id="guestcheckout" role="button"><?php _e('Checkout as Guest', THB_THEME_NAME); ?></a>
		        <?php } ?>
		        <?php if (get_option('woocommerce_enable_signup_and_login_from_checkout')=="yes") { ?>
		        <a href="#" class="btn large black" id="createaccount" role="button"><?php _e('Create Account', THB_THEME_NAME); ?></a>
		        
		        <div id="checkout_register">
		        <form method="post" class="register">
		        	<?php do_action( 'woocommerce_register_form_start' ); ?>
		        	<div class="smalltitle"><?php _e('Create Account', THB_THEME_NAME); ?></div>
		        	<div class="row">
		        		<div class="small-12 columns">
									<?php if ( get_option( 'woocommerce_registration_generate_username' ) === 'no' ) : ?>
									
										<p class="form-row form-row-wide">
											<label for="reg_username"><?php _e( 'Username', THB_THEME_NAME ); ?> <span class="required">*</span></label>
											<input type="text" class="input-text full" name="username" id="reg_username" value="<?php if ( ! empty( $_POST['username'] ) ) esc_attr( $_POST['username'] ); ?>" />
										</p>
						
									<?php endif; ?>
						
									<p class="form-row form-row-wide">
										<label for="reg_email"><?php _e( 'Email address', THB_THEME_NAME ); ?> <span class="required">*</span></label>
										<input type="email" class="input-text full" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) esc_attr( $_POST['email'] ); ?>" />
									</p>
						
									<p class="form-row form-row-wide">
										<label for="reg_password"><?php _e( 'Password', THB_THEME_NAME ); ?> <span class="required">*</span></label>
										<input type="password" class="input-text" name="password" id="reg_password" value="<?php if ( ! empty( $_POST['password'] ) ) esc_attr( $_POST['password'] ); ?>" />
									</p>
	
			            <!-- Spam Trap -->
			            <div style="left:-999em; position:absolute;"><label for="trap"><?php _e( 'Anti-spam', THB_THEME_NAME ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>
			            <?php wp_nonce_field( 'woocommerce-register' ); ?>
			            <input type="submit" class="button_create_account_continue button small" name="register" value="<?php _e('Continue &raquo;', THB_THEME_NAME); ?>" />
			            <input type="hidden" name="redirect" value="<?php echo get_permalink( wc_get_page_id( 'checkout' ) ); ?>" />
			            <input type="hidden" name="_wp_http_referer" value="<?php echo get_permalink( wc_get_page_id( 'checkout' ) ); ?>" />
			            <?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
	            	</div>
	            </div>
	            <?php do_action( 'woocommerce_register_form_end' ); ?>
		        </form>
	        	</div>
		        <?php } ?>
		    	</div>
		    </div>
		    <div class="small-12 medium-4 columns">
		    	<div class="login-section">
			    	<aside class="coupon-container">
				    	<div class="smalltitle"><?php _e('Have a coupon?', THB_THEME_NAME); ?></div>
							<?php
								wc_get_template('checkout/form-coupon.php', array(
									'message' => __(' ', THB_THEME_NAME),
									'redirect' => get_permalink(wc_get_page_id('checkout'))
								));
							?> 
					</aside>
				</div>
		    </div>
			</div>
		</section>
	<?php endif; ?>
	
	<?php
	// If checkout registration is disabled and not logged in, the user cannot checkout
	if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
		echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', THB_THEME_NAME ) );
		return;
	}
	
	// filter hook for include new pages inside the payment method
	$get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', WC()->cart->get_checkout_url() ); ?>
	
	<?php if ( WC()->cart->coupons_enabled() ) { ?>
		<div class="login-section">
			<aside class="coupon-container margin">
				<div class="smalltitle"><?php _e('Have a coupon?', THB_THEME_NAME); ?></div>
					<?php
						wc_get_template('checkout/form-coupon.php', array(
							'message' => __(' ', THB_THEME_NAME),
							'redirect' => get_permalink(wc_get_page_id('checkout'))
						));
					?> 
			</aside>
		</div>
	<?php } ?>
	<form name="checkout" method="post" class="checkout" action="<?php echo esc_url( $get_checkout_url ); ?>">
		<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>
			<section class="section" id="billing_shipping" <?php if (is_user_logged_in()) { echo 'style="display:block;"'; } ?>>
				<?php do_action( 'woocommerce_checkout_before_customer_details'); ?>
				<div class="row full-width-row no-padding">
		    	<div class="small-12 medium-6 columns billing">
		    		<div class="login-section">
							<?php do_action('woocommerce_checkout_billing'); ?>
						</div>
					</div>
					<div class="small-12 medium-6 columns shipping">
						<div class="login-section">
							<?php do_action('woocommerce_checkout_shipping'); ?>
						</div>
					</div>
				</div>
				<?php do_action( 'woocommerce_checkout_after_customer_details'); ?>
			</section>
		<?php endif; ?>
	
		<?php do_action('woocommerce_checkout_order_review'); ?>
	</form>
	
	<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
</section>