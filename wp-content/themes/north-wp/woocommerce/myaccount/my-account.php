<?php
/**
 * My Account page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce, $yith_wcwl;

?>
<?php 
$myaccount_ad_link = ot_get_option('myaccount-ad-link') ? ot_get_option('myaccount-ad-link') : '#';
$myaccount_subscriptions = ot_get_option('myaccount-subscriptions', 'off');
$column = $yith_wcwl ? 'large-4' : 'large-6';
?>
<div id="my-account-main" class="page-padding">
	<div class="account_wrapper custom_scroll">
		<div class="row full-width-row">
			<?php if ($myaccount_subscriptions == 'off') { ?>
			<div class="small-12 medium-6 large-4 columns">
				<a href="<?php echo $myaccount_ad_link; ?>" class="account-icon-box image">
				</a>
			</div>
			<?php } ?>
			<div class="small-12 medium-6 large-4 columns">
				<a href="#my-orders" class="account-icon-box">
					<div>
						<span class="icon my-orders"></span><br>
						<?php _e("MY ORDERS", THB_THEME_NAME); ?>
					</div>
				</a>
			</div>
			<?php if ($yith_wcwl) { ?>
			<div class="small-12 medium-6 large-4 columns">
				<a href="#my-wishlist" class="account-icon-box">
					<div>
						<span class="icon wishlist"></span><br>
						<?php _e("MY WISHLIST", THB_THEME_NAME); ?>
					</div>
				</a>
			</div>
			<?php } ?>
			<?php if ($myaccount_subscriptions == 'on') { ?>
			<div class="small-12 medium-6 large-4 columns">
				<a href="#my-subscriptions" class="account-icon-box">
					<div>
						<span class="icon my-subscriptions"></span><br>
						<?php _e("MY SUBSCRIPTIONS", THB_THEME_NAME); ?>
					</div>
				</a>
			</div>
			<?php } ?>
			<div class="small-12 medium-6 large-4 columns">
				<a href="#address-book" class="account-icon-box">
					<div>
						<span class="icon my-adresses"></span><br>
						<?php _e("MY ADDRESSES", THB_THEME_NAME); ?>
					</div>
				</a>
			</div>
			<div class="small-12 medium-6 <?php echo $column; ?> columns">
				<a href="#edit-account" class="account-icon-box">
					<div>
						<span class="icon my-account"></span><br>
						<?php _e("MY ACCOUNT", THB_THEME_NAME); ?>
					</div>
				</a>
			</div>
			<div class="small-12 medium-6 <?php echo $column; ?> columns">
				<a href="<?php echo wp_logout_url(); ?>" class="account-icon-box">
					<div>
						<span class="icon logout"></span><br>
						<?php _e("LOGOUT", THB_THEME_NAME); ?>
					</div>
				</a>
			</div>
		</div>
	</div>
</div>
<section class="my_woocommerce_page page-padding">
	<div class="tab-pane custom_scroll" id="my-orders">
	
		<div class="text-center"><a href="#" class="back_to_account"><?php _e("<small>Back to</small> My Account", THB_THEME_NAME); ?></a></div>
		<?php wc_get_template( 'myaccount/my-orders.php', array( 'order_count' => $order_count ) ); ?>
	
	</div>
	
	<div class="tab-pane custom_scroll" id="address-book">
	
		<div class="text-center"><a href="#" class="back_to_account"><?php _e("<small>Back to</small> My Account", THB_THEME_NAME); ?></a></div>
		<?php wc_get_template( 'myaccount/my-address.php' ); ?>
	
	</div>	
	
	<div class="tab-pane custom_scroll" id="edit-account">
	
		<div class="text-center"><a href="#" class="back_to_account"><?php _e("<small>Back to</small> My Account", THB_THEME_NAME); ?></a></div>
		<?php wc_get_template( 'myaccount/form-edit-account.php' ); ?>
	
	</div>
	<?php if ($yith_wcwl) { ?>
	<div class="tab-pane custom_scroll" id="my-wishlist">
	
		<div class="text-center"><a href="#" class="back_to_account"><?php _e("<small>Back to</small> My Account", THB_THEME_NAME); ?></a></div>
		<?php wc_get_template( 'wishlist.php' ); ?>
	
	</div>
	<?php } ?>
	<?php if ($myaccount_subscriptions == 'on') { ?>
	<div class="tab-pane custom_scroll" id="my-subscriptions">
	
		<div class="text-center"><a href="#" class="back_to_account"><?php _e("<small>Back to</small> My Account", THB_THEME_NAME); ?></a></div>
		<?php if (class_exists('WC_Subscriptions')) { WC_Subscriptions::get_my_subscriptions_template(); } ?>
	
	</div>
	<?php } ?>
</section>	