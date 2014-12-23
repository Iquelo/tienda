<?php 
	$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
	require_once( $parse_uri[0] . 'wp-load.php' );
	require_once('../../inc/ot-functions.php');
	$id = (isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '');
	
	Header("Content-type: text/css");
?>
<?php 
echo thb_google_webfont();
?>
/* Options set in the admin page */
body { 
	<?php thb_typeecho(ot_get_option('body_type'), false, 'Hind'); ?>
	color: <?php echo ot_get_option('text_color'); ?>;
}
.header:hover,
.header.hover  {
	<?php thb_bgecho(ot_get_option('header_bg')); ?>
}
#my-account-main .account-icon-box.image {
	<?php thb_bgecho(ot_get_option('myaccount-ad-bg')); ?>
}
#footer:hover,
#footer.hover,
#footer.active {
	<?php thb_bgecho(ot_get_option('footer_bg')); ?>
}
<?php if(ot_get_option('title_type')) { ?>
h1,h2,h3,h4,h5,h6 {
	<?php thb_typeecho(ot_get_option('title_type')); ?>	
}
<?php } ?>

/* Accent Color */
<?php if (ot_get_option('accent_color')) { ?>
a:hover, #nav .sf-menu > li > a:hover, .post .post-meta ul li a, .post .post-title a:hover, .more-link, #comments ol.commentlist .comment-reply-link, .price ins, .price > .amount, .product_meta p a, .shopping_bag tbody tr td.order-status.approved, .shopping_bag tbody tr td.product-name .posted_in a, .shopping_bag tbody tr td.product-quantity .wishlist-in-stock, .lost_password, #my-account-main .account-icon-box:hover, .lookbook-container .look .info a .amount {
  color: <?php echo ot_get_option('accent_color'); ?>;
}

.product-category > a:after, .sticky .post-title h2 a,
.product-thumbnails .owl-item.active.center img {
  border-color: <?php echo ot_get_option('accent_color'); ?>;
}

.badge.onsale, .price_slider .ui-slider-range {
	background: <?php echo ot_get_option('accent_color'); ?>;	
}

<?php } ?>
<?php if (ot_get_option('overlay_color')) { ?>
.fresco .overlay {
	<?php $rgb = thb_hex2rgb(ot_get_option('overlay_color')); ?>
	<?php if(ot_get_option('overlay_opacity')) { 
		echo "background: rgba(".$rgb.", ".ot_get_option('overlay_opacity').");";
		} else { 
		echo "background: rgb(".$rgb.");";
		}?>
}
<?php } ?>
<?php if ($overlay_border = ot_get_option('overlay_border_color')) { ?>
.fresco .overlay {
	<?php echo "border-color: ".$overlay_border.";";?>
}
<?php } ?>

/* Menu */
<?php if ($menu_margin = ot_get_option('menu_margin')) { ?>
#nav .sf-menu > li > a {
	margin-right: <?php echo $menu_margin[0].$menu_margin[1]; ?>;
}
<?php } ?>
<?php if ($menu_left = ot_get_option('menu_left_type')) { ?>
#nav .sf-menu > li > a {
	<?php thb_typeecho($menu_left); ?>	
}
<?php } ?>
<?php if ($submenu_left = ot_get_option('menu_left_submenu_type')) { ?>
#nav ul.sub-menu li a {
	<?php thb_typeecho($submenu_left); ?>	
}
<?php } ?>
<?php if ($menu_right = ot_get_option('menu_right_type')) { ?>
.account-holder ul li a {
	<?php thb_typeecho($menu_right); ?>	
}
<?php } ?>
/* Mobile Menu */
<?php if ($menu_mobile = ot_get_option('menu_mobile_type')) { ?>
.mobile-menu li a {
	<?php thb_typeecho($menu_mobile); ?>	
}
<?php } ?>
<?php if ($submenu_mobile = ot_get_option('menu_mobile_submenu_type')) { ?>
.mobile-menu .sub-menu li a {
	<?php thb_typeecho($submenu_mobile); ?>	
}
<?php } ?>
/* Menu Colors for dark/light backgrounds */
<?php if ($menu_color_light = ot_get_option('menu_color_light')) { ?>
#nav .sf-menu > li > a,
.account-holder ul li a,
.account-holder > a,
.header.background--dark.hover #nav .sf-menu > li > a, .header.background--dark.hover .account-holder ul li a, .header.background--dark.hover .account-holder > a, .header.background--dark.hover .account-holder .float_count, .header.background--dark.hover .menu-holder > a, .header.background--dark:hover #nav .sf-menu > li > a, .header.background--dark:hover .account-holder ul li a, .header.background--dark:hover .account-holder > a, .header.background--dark:hover .account-holder .float_count, .header.background--dark:hover .menu-holder > a{
	color: <?php echo $menu_color_light; ?>;
}
#nav .sf-menu > li > a:after,
.header.background--dark.hover #nav .sf-menu > li.current-menu-item > a:after, 
.header.background--dark:hover #nav .sf-menu > li.current-menu-item > a:after{
	background-color: <?php echo $menu_color_light; ?>;
}
.account-holder #cart-icon .icon-fill,
.header.background--dark.hover .account-holder #cart-icon .icon-fill,
.header.background--dark:hover .account-holder #cart-icon .icon-fill{
	fill: <?php echo $menu_color_light; ?>;
}
.header.background--dark.hover .account-holder .float_count,
.header.background--dark:hover .account-holder .float_count{
	color: #fff;
}
<?php } ?>

<?php if ($menu_color_dark = ot_get_option('menu_color_dark')) { ?>
.header.background--dark #nav .sf-menu > li > a, .header.background--dark .account-holder ul li a, .header.background--dark .account-holder > a, .header.background--dark .menu-holder > a{
	color: <?php echo $menu_color_dark; ?>;
}
.header.background--dark #nav .sf-menu > li.current-menu-item > a:after {
	background-color: <?php echo $menu_color_dark; ?>;
}
.header.background--dark .account-holder #cart-icon .icon-fill{
	fill:  <?php echo $menu_color_dark; ?>;
}

<?php } ?>
/* Newsletter */
<?php if ($newsletter_bg = ot_get_option('newsletter_bg')) { ?>
#newsletter-popup {
	<?php thb_bgecho($newsletter_bg); ?>
}
<?php } ?>
/* Shop Badges */
<?php if ($badge_sale = ot_get_option('badge_sale')) { ?>
.badge.onsale {
	background: <?php echo $badge_sale;?>;
}
<?php } ?>
<?php if ($badge_outofstock = ot_get_option('badge_outofstock')) { ?>
.badge.out-of-stock {
	background: <?php echo $badge_outofstock;?>;
}
<?php } ?>
<?php if ($badge_justarrived= ot_get_option('badge_justarrived')) { ?>
.badge.new{
	background: <?php echo $badge_justarrived;?>;
}
<?php } ?>
/* 404 Page */
<?php if ($bg404 = ot_get_option('404-bg')) { ?>
.content404 {
	background-image: url('<?php echo $bg404;?>');
}
<?php } ?>
/* Extra CSS */
<?php 
echo ot_get_option('extra_css');
?>