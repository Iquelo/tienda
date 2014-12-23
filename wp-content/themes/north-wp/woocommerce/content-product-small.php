<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

global $product, $woocommerce_loop;

$attachment_ids = $product->get_gallery_attachment_ids();

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Ensure visibilty
if ( ! $product->is_visible() )
	return;

// Increase loop count
$woocommerce_loop['loop']++;
$catalog_mode = ot_get_option('shop_catalog_mode', 'off');
?>


<article <?php post_class("small-5 medium-3 columns"); ?>>

<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

	<figure class="fresco">
		
		<?php
			$image_html = "";
			
			if (thb_out_of_stock()) {
				echo '<span class="badge out-of-stock">' . __( 'Out of Stock', THB_THEME_NAME ) . '</span>';
			} else if ( $product->is_on_sale() ) {
				echo apply_filters('woocommerce_sale_flash', '<span class="badge onsale">'.__( 'Sale',THB_THEME_NAME ).'</span>', $post, $product);
			}

			if ( has_post_thumbnail() ) {
				$image_html = wp_get_attachment_image( get_post_thumbnail_id(), 'shop_catalog' );					
			}
		?>
		<?php echo $image_html; ?>			
		<div class="overlay">
			<div class="buttons">
				<?php echo thb_wishlist_button(); ?>
				<a class="quick quick-view" data-id="<?php echo $post->ID; ?>" href="#"><i class="icon-budicon-545"></i></a>
			</div>
		</div>
			
	</figure>
	
	<div class="post-title">
		<a href="<?php the_permalink(); ?>"><?php echo thb_ShortenText(get_the_title($post->ID), 22);?></a>
		<?php if ($catalog_mode != 'on') { ?>
			<?php
				/**
				 * woocommerce_after_shop_loop_item_title hook
				 *
				 * @hooked woocommerce_template_loop_price - 10
				 */
				do_action( 'woocommerce_after_shop_loop_item_title' );
			?>
			<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
		<?php } ?>
	</div>
</article><!-- end product -->
