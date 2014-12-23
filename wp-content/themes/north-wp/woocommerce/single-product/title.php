<?php
/**
 * Single Product title
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $post, $product;
?>
<header class="post-title">
<?php echo thb_wishlist_button(); ?>
<h1 itemprop="name" class="entry-title"><?php the_title(); ?></h1>
</header>