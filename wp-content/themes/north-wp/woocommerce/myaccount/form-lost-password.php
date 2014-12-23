<?php
/**
 * Lost password form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<div class="my_woocommerce_page page-padding">
	<div class="custom_scroll">
		<div class="row" id="customer_login">
			<?php wc_print_notices();  ?>
			<div class="small-12 small-centered  medium-10 large-8 columns text-center">
				<?php if( 'lost_password' == $args['form'] ) : ?>
					<h3><?php echo apply_filters( 'woocommerce_lost_password_message', __( 'Lost your password? ',THB_THEME_NAME ) ); ?></h3>
				<?php else : ?>
					<h3><?php echo apply_filters( 'woocommerce_reset_password_message', __( 'Enter a new password below.',THB_THEME_NAME) ); ?></h3>
				<?php endif; ?>
				
				<form method="post" class="row lost_reset_password">
					<div class="small-12 medium-8 small-centered columns">
					<?php	if( 'lost_password' == $args['form'] ) : ?>

						<p class="form-row form-row-first"><label for="user_login"><?php _e( 'Username or email',THB_THEME_NAME ); ?></label> <input class="input-text" type="text" name="user_login" id="user_login" /></p>

					<?php else : ?>

						<p class="form-row form-row-first">
							<label for="password_1"><?php _e( 'New password',THB_THEME_NAME ); ?> <span class="required">*</span></label>
							<input type="password" class="input-text" name="password_1" id="password_1" />
						</p>
						<p class="form-row form-row-last">
							<label for="password_2"><?php _e( 'Re-enter new password',THB_THEME_NAME ); ?> <span class="required">*</span></label>
							<input type="password" class="input-text" name="password_2" id="password_2" />
						</p>

						<input type="hidden" name="reset_key" value="<?php echo isset( $args['key'] ) ? $args['key'] : ''; ?>" />
						<input type="hidden" name="reset_login" value="<?php echo isset( $args['login'] ) ? $args['login'] : ''; ?>" />
					<?php endif; ?>

					<p class="form-row"><input type="submit" class="button" name="wc_reset_password" value="<?php echo 'lost_password' == $args['form'] ? __( 'Reset Password',THB_THEME_NAME ) : __( 'Save',THB_THEME_NAME ); ?>" /></p>
					<?php wp_nonce_field( $args['form'] ); ?>
					</div>
				</form>
				</div>
		</div>
	</div>
</div>