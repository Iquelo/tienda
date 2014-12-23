<?php
/**
 * Single Product Up-Sells
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce, $woocommerce_loop;

$upsells = $product->get_upsells();

if ( sizeof( $upsells ) == 0 ) return;

$meta_query = $woocommerce->query->get_meta_query();

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => 3,
	'orderby'             => 'rand',
	'post__in'            => $upsells,
	'post__not_in'        => array( $product->id ),
	'meta_query'          => $meta_query
);

$products = new WP_Query( $args );

?>


<?php

if ( $products->have_posts() ) : ?>
<aside id="upsell-popup" class="mfp-hide theme-popup">
	<div class="products">

		<h2><?php _e('You Might Also Like', THB_THEME_NAME); ?></h2>

		<div class="products cf">

			<?php while ( $products->have_posts() ) : $products->the_post(); ?>

				<?php wc_get_template_part( 'content', 'product' ); ?>

			<?php endwhile; // end of the loop. ?>

		</div>
		<a href="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" class="btn"><?php _e('View Shopping Bag', THB_THEME_NAME); ?></a>

	</div>

</aside>
<a href="#upsell-popup" id="upsell-trigger" rel="inline" data-class="upsell-popup" class="mfp-hide"></a>
<?php endif;

wp_reset_postdata();
