<?php global $woocommerce, $yith_wcwl; ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=1">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-touch-fullscreen" content="yes">
	<meta http-equiv="cleartype" content="on">
	<meta name="HandheldFriendly" content="True">
	<?php if( $favicon = ot_get_option('favicon')){ ?>
		<?php if (is_ssl()) {
		    $favicon_image_img = str_replace("http://", "https://", $favicon);		
		} else {
		    $favicon_image_img = $favicon;
		}
		?>
	<link rel="shortcut icon" href="<?php echo $favicon_image_img; ?>">
	<?php } else {?>
	<link rel="shortcut icon" href="<?php echo THB_THEME_ROOT; ?>/assets/img/favicon.ico">
	<?php } ?>
	<?php do_action( 'thb_handhelded_devices' ); ?>
	<?php 
		$id = get_queried_object_id();
		$page_menu = (get_post_meta($id, 'page_menu', true) !== '' ? get_post_meta($id, 'page_menu', true) : false);
		$page_scroll = (get_post_meta($id, 'page_scroll', true) == 'on' ? 'page_scroll' : '');
		$extended_product = (get_post_meta($id, 'extended_product_page', true) == 'on' ? 'extended_product_page' : '');
		$snap_scroll = (get_post_meta($id, 'snap_scroll', true) == 'on' ? 'snap_scroll' : '');
		$rev_slider_alias = get_post_meta($id, 'rev_slider_alias', true);
		$header_style = (isset($_GET['header_style']) ? htmlspecialchars($_GET['header_style']) : ot_get_option('header_style', 'style1'));
		$arrow_style = ot_get_option('arrow_style', 'arrow_pointer');
		$header_cart = ot_get_option('header_cart');
		$header_search = ot_get_option('header_search');
		$header_wishlist = ot_get_option('header_wishlist');
		$header_menu_color = '';
		$smooth_scroll = (ot_get_option('smooth_scroll') != 'off' ? 'smooth_scroll' : '');
		$menu_mobile_toggle_view = (isset($_GET['menu_mobile_toggle_view']) ? htmlspecialchars($_GET['menu_mobile_toggle_view']) : ot_get_option('menu_mobile_toggle_view'));
		if (get_post_meta($id, 'header_override', true) == 'on') {
			$header_cart = get_post_meta($id, 'header_cart', true);
			$header_menu_color = get_post_meta($id, 'header_menu_color', true);
		}
		if(class_exists('woocommerce')) {
			if (is_shop()) {
				$header_menu_color =  (isset($_GET['shop_menu_color']) ? htmlspecialchars($_GET['shop_menu_color']) : ot_get_option('shop_menu_color', 'light'));
			}
		}
	?>
	<?php
		$class = array();
	 	array_push($class, $page_scroll);
	 	array_push($class, $extended_product);
	 	array_push($class, $arrow_style);
	 	if(!empty($snap_scroll)) { 
	 		array_push($class, 'snap');
	 	}
		array_push($class, $smooth_scroll);
	?>
	<?php 
		/* Always have wp_head() just before the closing </head>
		 * tag of your theme, or you will break many plugins, which
		 * generally use this hook to add elements to <head> such
		 * as styles, scripts, and meta tags.
		 */
		wp_head(); 
	?>
</head>
<body <?php body_class($class); ?> data-url="<?php echo home_url(); ?>" data-cart-count="<?php if($woocommerce) { echo $woocommerce->cart->cart_contents_count; } ?>" data-sharrreurl="<?php echo THB_THEME_ROOT; ?>/inc/sharrre.php" data-revslider="<?php echo $rev_slider_alias; ?>">
<div id="wrapper" class="open">
	
	<!-- Start Mobile Menu -->
	<nav id="mobile-menu" class="custom_scroll">
		<?php if(has_nav_menu('mobile-menu')) { ?>
		  <?php wp_nav_menu( array( 'theme_location' => 'mobile-menu', 'depth' => 2, 'container' => false, 'menu_class' => 'mobile-menu', 'walker' => new thb_mobileDropdown ) ); ?>
		<?php } else { ?>
			<ul class="mobile-menu">
				<li><a href="<?php echo get_admin_url().'nav-menus.php'; ?>">Please assign a menu from Appearance -> Menus</a></li>
			</ul>
		<?php } ?>
		<?php if(has_nav_menu('mobile-secondary-menu')) { ?>
		  <?php wp_nav_menu( array( 'theme_location' => 'mobile-secondary-menu', 'depth' => 1, 'container' => false, 'menu_class' => 'mobile-secondary-menu', 'walker' => new thb_mobileDropdown ) ); ?>
		<?php } ?>
		<div class="social-links animation right-to-left animate">
			<?php do_action( 'thb_social' ); ?>
		</div>
	</nav>
	<!-- End Mobile Menu -->
	
	<!-- Start Quick Cart -->
	<?php do_action( 'thb_side_cart' ); ?>
	<!-- End Quick Cart -->
	
	<!-- Start Content Container -->
	<section id="content-container">
		<!-- Start Content Click Capture -->
		<div class="click-capture"></div>
		<!-- End Content Click Capture -->
		
		<!-- Start Header -->
		<header class="header row <?php echo $header_style; if ($header_menu_color) { echo ' background--'.$header_menu_color; } ?>" data-offset="0" data-stick-class="hover" data-unstick-class="unhover" data-equal=">.columns" role="banner">
			<?php if ($header_style == 'style1') {  ?>
				<div class="small-4 columns menu-holder <?php if ($menu_mobile_toggle_view == 'style2') { echo 'mobile-view'; } ?>">
						<a href="#" data-target="open-menu" class="mobile-toggle"><i class="fa fa-bars"></i></a>
						<nav id="nav" role="navigation">
							<?php if ($page_menu) { ?>
								<?php wp_nav_menu( array( 'menu' => $page_menu, 'depth' => 3, 'container' => false, 'menu_class' => 'sf-menu', 'walker' => new thb_MegaMenu  ) ); ?>
							<?php } else if (has_nav_menu('nav-menu')) { ?>
							  <?php wp_nav_menu( array( 'theme_location' => 'nav-menu', 'depth' => 3, 'container' => false, 'menu_class' => 'sf-menu', 'walker' => new thb_MegaMenu  ) ); ?>
							<?php } else { ?>
								<ul class="sf-menu">
									<li><a href="<?php echo get_admin_url().'nav-menus.php'; ?>">Please assign a menu</a></li>
								</ul>
							<?php } ?>
						</nav>
				</div>
				<div class="small-12 medium-4 columns logo">
					<?php if (ot_get_option('logo')) { $logo = ot_get_option('logo'); } else { $logo = THB_THEME_ROOT. '/assets/img/logo-light.png'; } ?>
					<?php if (ot_get_option('logo_dark')) { $logo_dark = ot_get_option('logo_dark'); } else { $logo_dark = THB_THEME_ROOT. '/assets/img/logo-dark.png'; } ?>
					<a href="<?php echo home_url(); ?>" class="logolink">
						<img src="<?php echo $logo; ?>" class="logoimg bg--light" alt="<?php bloginfo('name'); ?>"/>
						<img src="<?php echo $logo_dark; ?>" class="logoimg bg--dark" alt="<?php bloginfo('name'); ?>"/>
					</a>
				</div>
				<div class="small-12 medium-4 columns account-holder">
						<?php if (has_nav_menu('acc-menu')) { ?>
						  <?php wp_nav_menu( array( 'theme_location' => 'acc-menu', 'depth' => 1, 'container' => false, 'walker' => new thb_MegaMenu  ) ); ?>
						<?php } else { ?>
							<ul class="menu">
								<li><a href="<?php echo get_admin_url().'nav-menus.php'; ?>">Please assign a menu</a></li>
							</ul>
						<?php } ?>
					<a href="#" data-target="open-menu" class="mobile-toggle"><i class="fa fa-bars"></i></a>
					<?php if ($header_wishlist != 'off') { do_action( 'thb_quick_wishlist' ); } ?>
					<?php if ($header_search != 'off') { do_action( 'thb_quick_search' ); } ?>
					<?php if ($header_cart != 'off') { do_action( 'thb_quick_cart' ); } ?>
				</div>
			<?php } else if ($header_style == 'style2') {  ?>
				<div class="small-12 medium-3 columns logo">
					<?php if (ot_get_option('logo')) { $logo = ot_get_option('logo'); } else { $logo = THB_THEME_ROOT. '/assets/img/logo-light.png'; } ?>
					<?php if (ot_get_option('logo_dark')) { $logo_dark = ot_get_option('logo_dark'); } else { $logo_dark = THB_THEME_ROOT. '/assets/img/logo-dark.png'; } ?>
					<a href="<?php echo home_url(); ?>" class="logolink">
						<img src="<?php echo $logo; ?>" class="logoimg bg--light" alt="<?php bloginfo('name'); ?>"/>
						<img src="<?php echo $logo_dark; ?>" class="logoimg bg--dark" alt="<?php bloginfo('name'); ?>"/>
					</a>
				</div>
				<div class="small-12 medium-9 columns">
					<div class="account-holder">
						<a href="#" data-target="open-menu" class="mobile-toggle"><i class="fa fa-bars"></i></a>
						<?php if ($header_wishlist != 'off') { do_action( 'thb_quick_wishlist' ); } ?>
						<?php if ($header_search != 'off') { do_action( 'thb_quick_search' ); } ?>
						<?php if ($header_cart != 'off') { do_action( 'thb_quick_cart' ); } ?>
					</div>
					<div class="menu-holder <?php if ($menu_mobile_toggle_view == 'style2') { echo 'mobile-view'; } ?>">
						<a href="#" data-target="open-menu" class="mobile-toggle"><i class="fa fa-bars"></i></a>
						<nav id="nav" role="navigation">
							<?php if ($page_menu) { ?>
								<?php wp_nav_menu( array( 'menu' => $page_menu, 'depth' => 3, 'container' => false, 'menu_class' => 'sf-menu', 'walker' => new thb_MegaMenu  ) ); ?>
							<?php } else if (has_nav_menu('nav-menu')) { ?>
							  <?php wp_nav_menu( array( 'theme_location' => 'nav-menu', 'depth' => 3, 'container' => false, 'menu_class' => 'sf-menu', 'walker' => new thb_MegaMenu  ) ); ?>
							<?php } else { ?>
								<ul class="sf-menu">
									<li><a href="<?php echo get_admin_url().'nav-menus.php'; ?>">Please assign a menu</a></li>
								</ul>
							<?php } ?>
						</nav>
					</div>
					
				</div>
				
			<?php } ?>
		</header>
		<!-- End Header -->
		<?php if (is_page() && $rev_slider_alias) {?>
		<div id="home-slider">
			<?php putRevSlider($rev_slider_alias); ?>
		</div>
		<?php  } ?>
		
		<div role="main" class="<?php echo $snap_scroll; ?>">
			<?php if(!empty($snap_scroll)) { ?><div class="ai-dotted ai-indicator"><span class="ai-inner1"></span><span class="ai-inner2"></span><span class="ai-inner3"></span></div><?php } ?>