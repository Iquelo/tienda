<?php 
	$newsletter = ot_get_option('newsletter');
	$footer_static = ot_get_option('footer_static');
	$footer_products = ot_get_option('footer_products');
 	$footer_products_radio = (isset($_GET['footer_products_radio']) ? htmlspecialchars($_GET['footer_products_radio']) : ot_get_option('footer_products_radio'));
	$header_style = (isset($_GET['header_style']) ? htmlspecialchars($_GET['header_style']) : ot_get_option('header_style')); 
	$menu_mobile_toggle_view = (isset($_GET['menu_mobile_toggle_view']) ? htmlspecialchars($_GET['menu_mobile_toggle_view']) : ot_get_option('menu_mobile_toggle_view', 'style1'));
?>
<?php

	if (is_404() || (class_exists('woocommerce') && is_account_page())) {
		$footer_static = 'off';
	}
?>
		</div><!-- End role["main"] -->
		
		<?php if (ot_get_option('footer') != 'off') { ?>
		<!-- Start Footer -->
		<?php if ($footer_products != 'off' && $footer_static == 'on') { do_action('thb_footer_products'); } ?>
		<footer id="footer" role="contentinfo"<?php if ($footer_static == 'on') {?> class="static"<?php } ?>>
			<div class="footer_inner row full-width-row">
				<div class="small-12 medium-6 large-4 columns footer-menu">
					<?php if ((ot_get_option('footer_cs') == 'on') && shortcode_exists('currency_switcher')) { ?>
					<div class="select-wrapper currency_switcher"><?php do_action('currency_switcher'); ?></div>
					<?php } ?>
					<?php if (ot_get_option('footer_ls') == 'on') { do_action( 'thb_language_switcher' ); } ?>
					<p><?php echo ot_get_option('copyright','Copyright 2014 NORTH ONLINE SHOPPING THEME. All RIGHTS RESERVED.'); ?> </p>
				</div>
				<div class="small-12 large-4 columns footer-toggle show-for-large-up">
					<?php if ($footer_products != 'off' && $footer_static != 'on') { ?>
					<a href="#" id="footer-toggle"><i class="fa fa-circle-o"></i> <i class="fa fa-circle-o"></i> <i class="fa fa-circle-o"></i><br><?php _e('QUICK SHOP', THB_THEME_NAME); ?></a>
					<?php } ?>
				</div>
				<div class="small-12 medium-6 large-4 columns social-links hide-for-small">
					<?php if (ot_get_option('social-payment') == 'social') {?>
					<?php do_action( 'thb_social' ); ?>
					<?php } else if (ot_get_option('social-payment') == 'payment') { ?>
					<?php do_action( 'thb_payment' ); ?>
					<?php } ?>
				</div>
				<?php if ($footer_products != 'off' && $footer_static != 'on') { do_action('thb_footer_products'); } ?>
			</div>
		</footer>
		<!-- End Footer -->
		<?php } ?>
	
	</section> <!-- End #content-container -->

</div> <!-- End #wrapper -->
<?php if ($newsletter != 'off') { do_action( 'thb_newsletter' ); } ?>
<?php echo ot_get_option('ga'); ?>

<?php 
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */
	 wp_footer(); 
?>
</body>
</html>