<?php
/**
 * Single Product Share
 *
 * Sharing plugins can hook into here or you can add your own code directly.
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post;
?>
<?php $boxed = false; include(locate_template( 'inc/postformats/sharing.php' )); ?>
<?php do_action('woocommerce_share'); // Sharing plugins can hook into here ?>