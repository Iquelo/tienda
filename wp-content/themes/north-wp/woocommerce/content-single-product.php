<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php
	global $post, $product;
?>
<?php $boxed_product_image = get_post_meta($post->ID, 'boxed_product_image', true);  ?>
<article itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class('post product-page'); ?>>	 
	  <div class="product-gallery carousel-container<?php if ($boxed_product_image == 'on') { ?> boxed<?php } ?>">        
	    <?php
	        /**
	         * woocommerce_show_product_images hook
	         *
	         * @hooked woocommerce_show_product_sale_flash - 10
	         * @hooked woocommerce_show_product_images - 20
	         * 
	         */
	        do_action( 'woocommerce_before_single_product_summary' );
	    ?>
	    <?php
          /**
           * woocommerce_after_single_product_summary hook
           *
           * @hooked woocommerce_output_related_products - 20
           */
          do_action( 'woocommerce_after_single_product_summary' );
      ?>
	  </div>
	  <div class="product-information">
	  	<section class="custom_scroll">
		  	<?php
		  		/**
		  		 * woocommerce_before_single_product hook
		  		 *
		  		 * @hooked woocommerce_show_messages - 10
		  		 */
		  		 do_action( 'woocommerce_before_single_product' );
		  	?>  
		    <?php
	        /**
	        	 * woocommerce_single_product_summary hook
	        	 *
	        	 * @hooked woocommerce_template_single_title - 5
	        	 * @hooked woocommerce_template_single_rating - 10
	        	 * @hooked woocommerce_template_single_price - 10
	        	 * @hooked woocommerce_template_single_excerpt - 20
	        	 * @hooked woocommerce_template_single_add_to_cart - 30
	        	 * @hooked woocommerce_template_single_meta - 40
	        	 * @hooked woocommerce_template_single_sharing - 50
	        	 */
	        do_action( 'woocommerce_single_product_summary' );
		    ?>
	    </section>
	  </div>
	<meta itemprop="url" content="<?php the_permalink(); ?>" />
</article><!-- #product-<?php the_ID(); ?> -->
<?php do_action( 'woocommerce_after_single_product' ); ?>   