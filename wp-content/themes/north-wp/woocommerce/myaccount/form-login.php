<?php
/**
 * Login Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.6
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php do_action('woocommerce_before_customer_login_form'); ?>

<div class="my_woocommerce_page page-padding">
	<div class="custom_scroll">
		<div class="row" id="customer_login">
			<?php wc_print_notices();  ?>
			<div class="small-12 small-centered  medium-10 large-8 columns">
				<div class="thb_tabs full <?php if (get_option('woocommerce_enable_myaccount_registration')!=='yes') : ?>center<?php endif; ?>" data-interval="0">
					<dl class="tabs">
						<dd class="active">
							<a href="#1392839304-1-48" class="active"><?php _e( 'Login',THB_THEME_NAME ); ?></a>
						</dd>
						<?php if (get_option('woocommerce_enable_myaccount_registration')=='yes') : ?>
						<dd>
							<a href="#1392839304-2-8"><?php _e( 'Register',THB_THEME_NAME ); ?></a>
						</dd>
						<?php endif; ?>
					</dl>
					<ul class="tabs-content cf">	
						<li id="1392839304-1-48Tab" class="active">
							<div class="small-12 medium-8 small-centered columns">
								<h3><?php _e( "I'm an existing customer and <br>would like to login." ,THB_THEME_NAME ); ?></h3>
								<form method="post" class="login row text-center">
								<div class="small-12 columns">
									<label for="username"><?php _e( 'Username or email',THB_THEME_NAME ); ?> <span class="required">*</span></label>
									<input type="text" class="input-text full" name="username" id="username" />
								</div>
								<div class="small-12 columns">
									<label for="password"><?php _e( 'Password',THB_THEME_NAME ); ?> <span class="required">*</span></label>
									<input class="input-text full" type="password" name="password" id="password" />
								</div>
								<div class="small-6 columns">
									<div class="remember">
										<input name="rememberme" type="checkbox" id="rememberme" value="forever" class="custom_check"/> <label for="rememberme" class="checkbox custom_label"><?php _e( 'Remember me',THB_THEME_NAME ); ?></label>
									</div>
								</div>
								<div class="small-6 columns">
									<a class="lost_password" href="<?php echo esc_url( wc_lostpassword_url() ); ?>"><?php _e( 'Lost Password?',THB_THEME_NAME ); ?></a>
								</div>
								<div class="small-12 columns">
									<?php wp_nonce_field( 'woocommerce-login' ); ?>
									<input type="submit" class="button" name="login" value="<?php _e( 'Login',THB_THEME_NAME ); ?>" />
									<?php if($_SERVER['HTTP_HOST'] === 'north.fuelthemes.net') {?>
									<p>Try our demo account -  <strong>username:</strong> demo <strong>password</strong> demo</p>
									<?php } ?>
								</div>
							</form>
							</div>
						</li>
						<?php if (get_option('woocommerce_enable_myaccount_registration')=='yes') : ?>
						<li id="1392839304-2-8Tab">
							<div class="small-12 medium-8 small-centered columns">
								<h3><?php _e( "I'm a new customer and <br>would like to register." ,THB_THEME_NAME ); ?></h3>
								<form method="post" class="register row text-center">
								<?php do_action( 'woocommerce_register_form_start' ); ?>
								<?php if (get_option('woocommerce_registration_generate_username')=='no') : ?>
									<div class="small-12 columns">
										<label for="reg_username"><?php _e( 'Username',THB_THEME_NAME ); ?> <span class="required">*</span></label>
										<input type="text" class="input-text full" name="username" id="reg_username" value="<?php if (isset($_POST['username'])) echo esc_attr($_POST['username']); ?>" />
									</div>

								<?php else : endif; ?>
								<div class="small-12 columns">
									<label for="reg_email"><?php _e( 'Email',THB_THEME_NAME ); ?> <span class="required">*</span></label>
									<input type="email" class="input-text full" name="email" id="reg_email" value="<?php if (isset($_POST['email'])) echo esc_attr($_POST['email']); ?>" />
								</div>
								<?php if (get_option('woocommerce_registration_generate_password')=='no') : ?>
								<div class="small-12 columns">
									<label for="reg_password"><?php _e( 'Password',THB_THEME_NAME ); ?> <span class="required">*</span></label>
									<input type="password" class="input-text full" name="password" id="reg_password" value="<?php if (isset($_POST['password'])) echo esc_attr($_POST['password']); ?>" />
								</div>
								<?php endif; ?>
								<!-- Spam Trap -->
								<div style="left:-999em; position:absolute;"><label for="trap"><?php _e( 'Anti-spam',THB_THEME_NAME ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

								<?php do_action( 'woocommerce_register_form' ); ?>
								<?php do_action( 'register_form' ); ?>

								<div class="small-12 columns">
									<?php wp_nonce_field( 'woocommerce-register' ); ?>
									<input type="submit" class="button" name="register" value="<?php _e( 'Register',THB_THEME_NAME ); ?>" />
								</div>
								<?php do_action( 'woocommerce_register_form_end' ); ?>
							</form>
							</div>
						</li>
						<?php endif; ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<?php do_action('woocommerce_after_customer_login_form'); ?>