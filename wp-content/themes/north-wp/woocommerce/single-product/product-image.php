<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.14
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $woocommerce, $product;
$attachment_ids = $product->get_gallery_attachment_ids();
?>
<?php $boxed_product_image = get_post_meta($post->ID, 'boxed_product_image', true);  ?>
<?php $thumbnail_product_image = get_post_meta($post->ID, 'thumbnail_product_image', true);  ?>
<?php $extended_product_page = get_post_meta($post->ID, 'extended_product_page', true);  ?>
<?php if( $product->has_child() && $product->is_type( 'variable' )) { 
			$available_variations = $product->get_available_variations();
		}
?>
<div id="product-images" class="carousel owl product-images" data-loop="false" data-navigation="true" data-autoplay="false" rel="gallery" data-columns="1">
            
		<?php if ( $attachment_ids ) {						
				
				foreach ( $attachment_ids as $attachment_id ) {
					$var_img = false;
					$image_link = wp_get_attachment_url( $attachment_id );
					$image_src_link = wp_get_attachment_image_src($attachment_id,'full');
					$src = wp_get_attachment_image_src( $attachment_id, false, '' );
					$src_small = wp_get_attachment_image_src( $attachment_id,  apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ));
					
					if ($boxed_product_image == 'on' &&  $extended_product_page !== 'no') {
						$src_small = aq_resize( $src[0], 1000, 690, true, false);
					}
					$image_title = esc_attr( get_the_title( $attachment_id ) );
					if (isset($available_variations)) {
						foreach($available_variations as $prod_variation) {
						  if ($image_src_link[0] == $prod_variation['image_link']) {
						  	$var_img = $prod_variation['attributes']['attribute_pa_color'];
						  }
						}
					}
					?>
						<figure itemprop="image" class="easyzoom" data-variation-color="<?php echo $var_img; ?>">
							<a href="<?php echo $src[0]; ?>" itemprop="image"><img src="<?php echo $src_small[0]; ?>" title="<?php echo $image_title; ?>" /></a>
						</figure>
					
					<?php
				}
			}
		?>
</div>
<?php if ($thumbnail_product_image == 'on') { ?>
<div id="product-thumbnails" class="carousel owl product-thumbnails" data-autowidth="true" data-loop="true" data-navigation="false" data-autoplay="false" data-columns="5" data-center="true">
	<?php if ( $attachment_ids ) {						
			
			foreach ( $attachment_ids as $attachment_id ) {
				$var_img = false;
				$image_link = wp_get_attachment_url( $attachment_id );
				$image_src_link = wp_get_attachment_image_src($attachment_id,'full');
				$src = wp_get_attachment_image_src( $attachment_id, false, '' );
				$src_small = aq_resize( $src[0], 78, 74, true, false);

				$image_title = esc_attr( get_the_title( $attachment_id ) );
				?>
				<img src="<?php echo $src_small[0]; ?>" title="<?php echo $image_title; ?>" />
				
				<?php
			}
		}
	?>
</div>

<?php } ?>