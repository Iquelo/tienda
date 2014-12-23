<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wp, $prdctfltr_global;

if ( isset($prdctfltr_global['active']) && $prdctfltr_global['active'] == 'true' ) {
	return;
}
else if ( isset($_GET['widget_search']) ) {
	return;
}

$prdctfltr_global['active'] = 'true';

if ( is_product_category()) {
	$_GET['product_cat'] = get_query_var('product_cat');
}
else if ( is_product_tag() ) {
	$_GET['product_tag'] = get_query_var('product_tag');
}
else if ( is_product_taxonomy() ) {
	$_GET[get_query_var('taxonomy')] = get_query_var('term');
}

$curr_style = get_option( 'wc_settings_prdctfltr_style_preset', 'pf_default' );

$curr_styles = array(
	( $curr_style !== 'pf_disable' ? ' ' . $curr_style : '' ),
	( get_option( 'wc_settings_prdctfltr_always_visible', 'no' ) == 'no' && get_option( 'wc_settings_prdctfltr_disable_bar', 'no' ) == 'no' ? 'prdctfltr_slide' : 'prdctfltr_always_visible' ),
	( get_option( 'wc_settings_prdctfltr_click_filter', 'no' ) == 'no' ? 'prdctfltr_click' : 'prdctfltr_click_filter' ),
	( get_option( 'wc_settings_prdctfltr_limit_max_height', 'no' ) == 'no' ? 'prdctfltr_rows' : 'prdctfltr_maxheight' ),
	( get_option( 'wc_settings_prdctfltr_custom_scrollbar', 'yes' ) == 'no' ? '' : 'prdctfltr_scroll_active' ),
	( get_option( 'wc_settings_prdctfltr_disable_bar', 'no' ) == 'no' ? '' : 'prdctfltr_disable_bar' )
);

$curr_maxheight = ( $curr_styles[3] == 'prdctfltr_maxheight' ? ' style="height:'.get_option( 'wc_settings_prdctfltr_max_height', 'no' ).'px;"' : '' );

$catalog_orderby = apply_filters( 'prdctfltr_catalog_orderby', array(
	'' => __( 'None', 'prdctfltr' ),
	'menu_order' => __( 'Default', 'prdctfltr' ),
	'popularity' => __( 'Popularity', 'prdctfltr' ),
	'rating'     => __( 'Average rating', 'prdctfltr' ),
	'date'       => __( 'Newness', 'prdctfltr' ),
	'price'      => __( 'Price: low to high', 'prdctfltr' ),
	'price-desc' => __( 'Price: high to low', 'prdctfltr' )
) );

?>
<div id="prdctfltr_woocommerce" class="prdctfltr_woocommerce<?php echo implode( $curr_styles, ' ' ); ?>">
<?php

	if ( !is_page() || is_shop() ) {
		global $wp_the_query;

		$paged    = max( 1, $wp_the_query->get( 'paged' ) );
		$per_page = $wp_the_query->get( 'posts_per_page' );
		$total    = $wp_the_query->found_posts;
		$first    = ( $per_page * $paged ) - $per_page + 1;
		$last     = min( $total, $wp_the_query->get( 'posts_per_page' ) * $paged );

	}
	else {

		$args = array();

		$args = $args + array(
			'prdctfltr'				=> 'active',
			'post_type'				=> 'product',
			'post_status' 			=> 'publish',
			'posts_per_page' 		=> $prdctfltr_global['posts_per_page'],
			'meta_query' 			=> array(
				array(
					'key' 			=> '_visibility',
					'value' 		=> array('catalog', 'visible'),
					'compare' 		=> 'IN'
				)
			)
		);

		$products = new WP_Query( $args );

		$paged    = max( 1, $products->get( 'paged' ) );
		$per_page = $products->get( 'posts_per_page' );
		$total    = $products->found_posts;
		$first    = ( $per_page * $paged ) - $per_page + 1;
		$last     = min( $total, $products->get( 'posts_per_page' ) * $paged );
	}

	$pf_query = ( isset($products) ? $products : $wp_the_query );

	if ( get_option( 'wc_settings_prdctfltr_disable_bar', 'no' ) == 'no' ) {
	$prdctfltr_icon = get_option( 'wc_settings_prdctfltr_icon', '' );
?>
<a id="prdctfltr_woocommerce_filter" href="#"><i class="<?php echo ( $prdctfltr_icon == '' ? 'prdctfltr-bars' : $prdctfltr_icon ); ?>"></i></a>
<span>
<?php _e('Filter products', 'prdctfltr'); ?>
<?php echo ( isset($_GET['orderby'] ) ? ' / <span>'.$catalog_orderby[$_GET['orderby']] . '</span> <a href="#" data-key="prdctfltr_orderby"><i class="prdctfltr-delete"></i></a>' : '' );?>
<?php

	if ( isset($_GET['min_price']) && $_GET['min_price'] !== '' ) {

		$num_decimals = absint( get_option( 'woocommerce_price_num_decimals' ) );
		$currency = isset( $args['currency'] ) ? $args['currency'] : '';
		$currency_symbol = get_woocommerce_currency_symbol($currency);
		$decimal_sep = wp_specialchars_decode( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ), ENT_QUOTES );
		$thousands_sep = wp_specialchars_decode( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ), ENT_QUOTES );

		$min_price = apply_filters( 'formatted_woocommerce_price', number_format( $_GET['min_price'], $num_decimals, $decimal_sep, $thousands_sep ), $_GET['min_price'], $num_decimals, $decimal_sep, $thousands_sep );

		if ( isset($_GET['max_price']) && $_GET['max_price'] !== '' ) {
			$curr_max_price = $_GET['max_price'];
			$max_price = apply_filters( 'formatted_woocommerce_price', number_format( $curr_max_price, $num_decimals, $decimal_sep, $thousands_sep ), $curr_max_price, $num_decimals, $decimal_sep, $thousands_sep );
		}
		else {
			$max_price = '+';
		}

		if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $num_decimals > 0 ) {
			$min_price = wc_trim_zeros( $min_price );
			$max_price = ($max_price !== '+' ? wc_trim_zeros( $max_price ) : '' );
		}

		$min_price = sprintf( get_woocommerce_price_format(), $currency_symbol, $min_price );
		$max_price = ( $max_price !== '+' ?sprintf( get_woocommerce_price_format(), $currency_symbol, $max_price ) : '');
		echo ' / <span>' . $min_price . ' - ' . $max_price . '</span> <a href="#" data-key="prdctfltr_byprice"><i class="prdctfltr-delete"></i></a>';
	}

	if ( isset($_GET['product_cat']) ) {
		$curr_selected = ( !is_shop() && is_product_category() ? array($_GET['product_cat']) : explode(',', $pf_query->query_vars['product_cat']) );
		echo ' / <span> ';
		$i=0;
		foreach( $curr_selected as $selected ) {
			$curr_term = get_term_by('slug', $selected, 'product_cat');
			echo ( $i !== 0 ? ', ' : '' ) . $curr_term->name;
			$i++;
		}
		echo '</span> <a href="#" data-key="prdctfltr_cat"><i class="prdctfltr-delete"></i></a>';
	}

	if ( isset($_GET['product_tag']) ) {
		$curr_selected = explode(',', $pf_query->query_vars['product_tag']);
		echo ' / <span> ';
		$i=0;
		foreach( $curr_selected as $selected ) {
			$curr_term = get_term_by('slug', $selected, 'product_tag');
			echo ( $i !== 0 ? ', ' : '' ) . $curr_term->name;
			$i++;
		}
		echo '</span> <a href="#" data-key="prdctfltr_tag"><i class="prdctfltr-delete"></i></a>';
	}

	if ( isset($_GET['characteristics']) ) {
		$curr_selected = explode(',', $pf_query->query_vars['characteristics']);
		echo ' / <span> ';
		$i=0;
		foreach( $curr_selected as $selected ) {
			$curr_term = get_term_by('slug', $selected, 'characteristics');
			echo ( $i !== 0 ? ', ' : '' ) . $curr_term->name;
			$i++;
		}
		echo '</span> <a href="#" data-key="prdctfltr_characteristics"><i class="prdctfltr-delete"></i></a>';
	}

	if ( $attribute_taxonomies = wc_get_attribute_taxonomies() ) {
		$curr_attr = array();
		$n = 0;

		foreach ( $attribute_taxonomies as $tax ) {
			$n++;
			if ( isset($_GET['pa_' . $tax->attribute_name]) ) {
				$curr_selected = explode(',', $pf_query->query_vars['pa_' . $tax->attribute_name]);
				echo ' / <span>' . $tax->attribute_label . ' - ';
				$i=0;
				foreach( $curr_selected as $selected ) {
					$curr_term = get_term_by('slug', $selected, 'pa_' . $tax->attribute_name);
					echo ( $i !== 0 ? ', ' : '' ) . $curr_term->name;
					$i++;
				}
				echo '</span> <a href="#" data-key="prdctfltr_pa_' . $tax->attribute_name.'"><i class="prdctfltr-delete"></i></a>';
			}
		}
	}
?>
 / 
<?php

	}

	if ( get_option( 'wc_settings_prdctfltr_disable_bar', 'no' ) == 'no' ) {

		if ( $total == 0 ) {
			_e('No products found but you might like these&hellip;', 'prdctfltr');
		} elseif ( $total == 1 ) {
			_e( 'Showing the single result', 'prdctfltr' );
		} elseif ( $total <= $per_page || -1 == $per_page ) {
			printf( __( 'Showing all %d results', 'prdctfltr' ), $total );
		} else {
			printf( __( 'Showing %1$d - %2$d of %3$d results', 'prdctfltr' ), $first, $last, $total );
		}

?>
</span>
<?php

	}

	$curr_elements = get_option( 'wc_settings_prdctfltr_selected', array('sort','price','cat') );
	$curr_attrs = get_option( 'wc_settings_prdctfltr_attributes', array() );

	$curr_columns = get_option( 'wc_settings_prdctfltr_max_columns', 6 );

	if ( ( strpos( $curr_style, 'inline' ) !== true ) ) {
		$curr_mix_count = ( count($curr_elements) + count($curr_attrs) );
		$curr_columns_class = ' prdctfltr_columns_' . ( $curr_mix_count < $curr_columns ? $curr_mix_count : $curr_columns );
	}
	else {
		$curr_columns_class = '';
	}

	if ( ( isset($_GET['filter_results']) && get_option( 'wc_settings_prdctfltr_adoptive', 'no' ) == 'yes' ) || ( ( is_product_category() || is_product_tag() || is_product_taxonomy() ) && get_option( 'wc_settings_prdctfltr_adoptive', 'no' ) == 'yes' ) ) {

		if ( $pf_query->have_posts() ) {

			$i = 0;

			query_posts(http_build_query($pf_query->query_vars).'&posts_per_page='.$total);

			$output_terms = array();

			while ( $pf_query->have_posts() ) {

				$pf_query->the_post();

						if ( get_option( 'wc_settings_prdctfltr_cat_adoptive', 'no' ) == 'yes' ) {
							$output_terms = $output_terms + array(
								'product_cat'=>array()
							);
							$adopt_terms = get_the_terms(get_the_ID(), 'product_cat');
							if ( is_array( $adopt_terms ) ) {
								foreach ( $adopt_terms as $k => $v ) {
									if ( !in_array( $k, $output_terms['product_cat'], TRUE ) ) {
										$output_terms['product_cat'][$k] = $v;
									}
								}
							}
						}

						if ( get_option( 'wc_settings_prdctfltr_tag_adoptive', 'no' ) == 'yes' ) {
							$output_terms = $output_terms + array(
								'product_tag'=>array()
							);
							$adopt_terms = get_the_terms(get_the_ID(), 'product_tag');
							if ( is_array( $adopt_terms ) ) {
								foreach ( $adopt_terms as $k => $v ) {
									if ( !in_array( $k, $output_terms['product_tag'], TRUE ) ) {
										$output_terms['product_tag'][$k] = $v;
									}
								}
							}
						}
						if ( get_option( 'wc_settings_prdctfltr_char_adoptive', 'no' ) == 'yes' ) {
							$output_terms = $output_terms + array(
								'characteristics'=>array()
							);
							$adopt_terms = get_the_terms(get_the_ID(), 'characteristics');
							if ( is_array( $adopt_terms ) ) {
								foreach ( $adopt_terms as $k => $v ) {
									if ( !in_array( $k, $output_terms['characteristics'], TRUE ) ) {
										$output_terms['characteristics'][$k] = $v;
									}
								}
							}
						}

						foreach ( $curr_attrs as $k => $attr ) {
							if ( get_option( 'wc_settings_prdctfltr_'.$attr.'_adoptive', 'no' ) == 'yes' ) {
								$output_terms = $output_terms + array(
									$attr=>array()
								);
								$adopt_terms = get_the_terms(get_the_ID(), $attr);
								if ( is_array( $adopt_terms ) ) {
									foreach ( $adopt_terms as $k => $v ) {
										if ( !in_array( $k, $output_terms[$attr], TRUE ) ) {
											$output_terms[$attr][$k] = $v;
										}
									}
								}
							}
						}

				if ( get_option( 'wc_settings_prdctfltr_price_adoptive', 'no' ) == 'yes' ) {
					global $product;
					$curr_price = $product->get_price();
					if ( $i == 0 ) {
						$output_terms['min_price'] = $curr_price;
						$output_terms['max_price'] = $curr_price;
					}
					else {
						$output_terms['min_price'] = ( $curr_price < $output_terms['min_price'] ? $curr_price : $output_terms['min_price'] );
						$output_terms['max_price'] = ( $curr_price > $output_terms['max_price'] ? $curr_price : $output_terms['max_price'] );
					}
				}

				$i++;
			}

		}

		wp_reset_query();
	}

	if ( !isset($prdctfltr_global['action']) ) {
		if ( !is_shop() && is_product_taxonomy() ) {
			$curr_action = get_permalink( woocommerce_get_page_id( 'shop' ) );
			if ( $curr_action == home_url('/') ) {
				$post = get_post(woocommerce_get_page_id( 'shop' ));
				$curr_action = get_permalink( woocommerce_get_page_id( 'shop' ) ) . $post->post_name;
			}
		}
		else {
			$curr_action = get_permalink( woocommerce_get_page_id( 'shop' ) );
			if ( $curr_action == home_url('/') && is_shop() ) {
				$post = get_post(woocommerce_get_page_id( 'shop' ));
				$curr_action = get_permalink( woocommerce_get_page_id( 'shop' ) ) . $post->post_name;
			}
			else {
				if ( get_option( 'permalink_structure' ) == '' )
					$curr_action = remove_query_arg( array( 'page', 'paged' ), add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) );
				else
					$curr_action = preg_replace( '%\/page/[0-9]+%', '', home_url( $wp->request ) );
			}
		}
	}
	else {
		$curr_action = $prdctfltr_global['action'];
	}


?>
<form action="<?php echo $curr_action; ?>" class="prdctfltr_woocommerce_ordering" method="get">
	<input type="hidden" name="filter_results" value="true" /><?php
?>
	<div class="prdctfltr_filter_wrapper<?php echo $curr_columns_class; ?>">
		<div class="prdctfltr_filter_inner">
		<?php

			foreach ( $curr_elements as $k => $v ) :
		
				switch ( $v ) :

				case 'sort' : ?>

					<div class="prdctfltr_filter prdctfltr_orderby">
					<input name="orderby" type="hidden"<?php echo ( isset($_GET['orderby'] ) ? ' value="'.$_GET['orderby'].'"' : '' );?>>
					<span>
						<?php _e('Sort by', 'prdctfltr'); ?>
						<?php
							if ( $curr_styles[5] == 'prdctfltr_disable_bar' && isset($_GET['orderby'] ) ) {
								echo ' / <span>'.$catalog_orderby[$_GET['orderby']] . '</span> <a href="#" data-key="prdctfltr_orderby"><i class="prdctfltr-delete"></i></a>';
							}
						?>
						<i class="prdctfltr-down"></i>
					</span>
						<div class="prdctfltr_checkboxes"<?php echo $curr_maxheight; ?>>
						<?php
							if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' )
								unset( $catalog_orderby['rating'] );

							foreach ( $catalog_orderby as $id => $name ) {
								printf('<label%4$s><input type="checkbox" value="%1$s" %2$s /><span>%3$s</span></label>', esc_attr( $id ), ( isset($_GET['orderby']) && $_GET['orderby'] == $id ? 'checked' : '' ), esc_attr( $name ), ( isset($_GET['orderby']) && $_GET['orderby'] == $id ? ' class="prdctfltr_active"' : '' ) );
							}
						?>
						</div>
					</div>

				<?php break;

				case 'price' :
				 ?>

					<div class="prdctfltr_filter prdctfltr_byprice">
					<input name="min_price" type="hidden"<?php echo ( isset($_GET['min_price'] ) ? ' value="'.$_GET['min_price'].'"' : '' );?>>
					<input name="max_price" type="hidden"<?php echo ( isset($_GET['max_price'] ) ? ' value="'.$_GET['max_price'].'"' : '' );?>>
					<span>
						<?php _e('Price range', 'prdctfltr'); ?>
						<?php
							if ( $curr_styles[5] == 'prdctfltr_disable_bar' && isset($_GET['min_price']) && $_GET['min_price'] !== '' ) {

								$num_decimals = absint( get_option( 'woocommerce_price_num_decimals' ) );
								$currency = isset( $args['currency'] ) ? $args['currency'] : '';
								$currency_symbol = get_woocommerce_currency_symbol($currency);
								$decimal_sep = wp_specialchars_decode( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ), ENT_QUOTES );
								$thousands_sep = wp_specialchars_decode( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ), ENT_QUOTES );

								$min_price = apply_filters( 'formatted_woocommerce_price', number_format( $_GET['min_price'], $num_decimals, $decimal_sep, $thousands_sep ), $_GET['min_price'], $num_decimals, $decimal_sep, $thousands_sep );

								if ( isset($_GET['max_price']) && $_GET['max_price'] !== '' ) {
									$curr_max_price = $_GET['max_price'];
									$max_price = apply_filters( 'formatted_woocommerce_price', number_format( $curr_max_price, $num_decimals, $decimal_sep, $thousands_sep ), $curr_max_price, $num_decimals, $decimal_sep, $thousands_sep );
								}
								else {
									$max_price = '+';
								}

								if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $num_decimals > 0 ) {
									$min_price = wc_trim_zeros( $min_price );
									$max_price = ($max_price !== '+' ? wc_trim_zeros( $max_price ) : '' );
								}

								$min_price = sprintf( get_woocommerce_price_format(), $currency_symbol, $min_price );
								$max_price = ( $max_price !== '+' ?sprintf( get_woocommerce_price_format(), $currency_symbol, $max_price ) : '');
								echo ' / <span>' . $min_price . ' - ' . $max_price . '</span> <a href="#" data-key="prdctfltr_byprice"><i class="prdctfltr-delete"></i></a>';
							}
						?>
						<i class="prdctfltr-down"></i>
					</span>
					<?php
						$curr_price = ( isset($_GET['min_price']) ? $_GET['min_price'].'-'.( isset($_GET['max_price']) ? $_GET['max_price'] : '' ) : '' );
						
						$curr_price_set = get_option( 'wc_settings_prdctfltr_price_range', 100 );
						$curr_price_add = get_option( 'wc_settings_prdctfltr_price_range_add', 100 );
						$curr_price_limit = get_option( 'wc_settings_prdctfltr_price_range_limit', 6 );

						$curr_prices = array();
						$curr_prices_currency = array();
						global $wpdb;
						$min = floor( $wpdb->get_var(
							$wpdb->prepare('
								SELECT min(meta_value + 0)
								FROM %1$s
								LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
								WHERE ( meta_key = \'%3$s\' OR meta_key = \'%4$s\' )
								AND meta_value != ""
								', $wpdb->posts, $wpdb->postmeta, '_price', '_min_variation_price' )
							)
						);
						$max = ceil( $wpdb->get_var(
							$wpdb->prepare('
								SELECT max(meta_value + 0)
								FROM %1$s
								LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
								WHERE meta_key = \'%3$s\'
							', $wpdb->posts, $wpdb->postmeta, '_price' )
						) );


						for ($i = 0; $i < $curr_price_limit; $i++) {
							if ( $i !== 0 ) {
								$min_price = ($curr_price_add*$i);
							}
							else {
								$min_price = $min;
								$remember_max = $curr_price_set;
							}

							$max_price = ($remember_max+($curr_price_add*$i));

							$curr_prices[$i] = $min_price.'-'.( ($i+1) == $curr_price_limit ? '' : $max_price );
						}

						$num_decimals = absint( get_option( 'woocommerce_price_num_decimals' ) );
						$currency = isset( $args['currency'] ) ? $args['currency'] : '';
						$currency_symbol = get_woocommerce_currency_symbol($currency);
						$decimal_sep = wp_specialchars_decode( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ), ENT_QUOTES );
						$thousands_sep = wp_specialchars_decode( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ), ENT_QUOTES );

						for ($i = 0; $i < $curr_price_limit; $i++) {

							if ( $i == 0 ) {
								$max = $curr_price_set;
								$remember_min = 0;
								$remember_max = $max;
							}
							else {
								$min = (($curr_price_add*$i));
								$max = ($remember_max+($curr_price_add*$i));
							}

							$min_price = apply_filters( 'formatted_woocommerce_price', number_format( $min, $num_decimals, $decimal_sep, $thousands_sep ), $min, $num_decimals, $decimal_sep, $thousands_sep );
							$max_price = apply_filters( 'formatted_woocommerce_price', number_format( $max, $num_decimals, $decimal_sep, $thousands_sep ), $max, $num_decimals, $decimal_sep, $thousands_sep );

							if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $num_decimals > 0 ) {
								$min_price = wc_trim_zeros( $min_price );
								$max_price = wc_trim_zeros( $max_price );
							}

							$curr_prices_currency[$i] = sprintf( get_woocommerce_price_format(), $currency_symbol, $min_price ) . ( ($i+1) == $curr_price_limit ? '+' : ' - ' . sprintf( get_woocommerce_price_format(), $currency_symbol, $max_price ) );
						}

						$catalog_ready_price = array();

						for ($i = 0; $i < $curr_price_limit; $i++) {
							if ( $i == 0 ) {
								$catalog_ready_price = array(
									'-' => __( 'None', 'prdctfltr' )
								);
							}
							$catalog_ready_price = $catalog_ready_price + array(
								$curr_prices[$i] => $curr_prices_currency[$i]
							);
						}

						$catalog_price = apply_filters( 'prdctfltr_catalog_price', $catalog_ready_price );

						if ( isset($output_terms['min_price']) ) {
							foreach ( $catalog_price as $k => $v ) {
								$curr_exploded = explode('-', $k);
								if ( !($output_terms['min_price'] < floatval($curr_exploded[1]) && $output_terms['max_price'] > floatval($curr_exploded[0])) ) unset($catalog_price[$k]);
							}
						}
						$catalog_price = array(
							'-' => __( 'None', 'prdctfltr' )
						) + $catalog_price;
					?>
					<div class="prdctfltr_checkboxes"<?php echo $curr_maxheight; ?>>
						<?php
							foreach ( $catalog_price as $id => $name ) {
								printf('<label%4$s><input type="checkbox" value="%1$s" %2$s /><span>%3$s</span></label>',
									esc_attr( $id ),
									( $curr_price == $id ? 'checked' : '' ),
									esc_attr( $name ),
									( $curr_price == $id ? ' class="prdctfltr_active"' : '' )
								);
							}
						?>
						</div>
					</div>

				<?php break;

				case 'cat' : ?>

					<?php
						if ( isset($output_terms['product_cat']) && !empty($output_terms['product_cat']) ) {
							$curr_limit = intval( get_option( 'wc_settings_prdctfltr_cat_limit', 0 ) );
							if ( $curr_limit !== 0 ) {
								$catalog_categories = array_slice($output_terms['product_cat'], 0, $curr_limit );
							}
							else {
								$catalog_categories = $output_terms['product_cat'];
							}

						}
						else {
							$curr_limit = intval( get_option( 'wc_settings_prdctfltr_cat_limit', 0 ) );
							if ( $curr_limit !== 0 ) {
								$catalog_categories = get_terms( 'product_cat', array('hide_empty' => 1, 'number' => $curr_limit ) );
							}
							else {
								$catalog_categories = get_terms( 'product_cat', array('hide_empty' => 1 ) );

								if ( get_option( 'wc_settings_prdctfltr_cat_hierarchy', 'no' ) == 'yes' ) {
									$catalog_categories_sorted = array();
									prdctfltr_sort_terms_hierarchicaly($catalog_categories, $catalog_categories_sorted);
									$catalog_categories = $catalog_categories_sorted;
								}
							}
						}

						if ( !empty( $catalog_categories ) && !is_wp_error( $catalog_categories ) ){
						$curr_term_multi = ( get_option( 'wc_settings_prdctfltr_cat_multi', 'no' ) == 'yes' ? ' prdctfltr_multi' : ' prdctfltr_single' );
					?>
					<div class="prdctfltr_filter prdctfltr_cat <?php echo $curr_term_multi; ?>">
					<input name="product_cat" type="hidden"<?php echo ( isset($_GET['product_cat'] ) ? ' value="'.$_GET['product_cat'].'"' : '' );?>>
					<span>
						<?php _e('Categories', 'prdctfltr'); ?>
						<?php
							if ( $curr_styles[5] == 'prdctfltr_disable_bar' ) {
								if ( isset($_GET['product_cat']) ) {
									$curr_selected = ( !is_shop() && is_product_category() ? array($_GET['product_cat']) : explode(',', $pf_query->query_vars['product_cat']) );
									echo ' / <span> ';
									$i=0;
									foreach( $curr_selected as $selected ) {
										$curr_term = get_term_by('slug', $selected, 'product_cat');
										echo ( $i !== 0 ? ', ' : '' ) . $curr_term->name;
										$i++;
									}
									echo '</span> <a href="#" data-key="prdctfltr_cat"><i class="prdctfltr-delete"></i></a>';
								}
							}
						?>
						<i class="prdctfltr-down"></i>
					</span>
					<div class="prdctfltr_checkboxes"<?php echo $curr_maxheight; ?>>
						<?php
							$curr_include = get_option( 'wc_settings_prdctfltr_include_cats', array() );
							printf('<label><input type="checkbox" value="" /><span>%1$s</span></label>', __('None' , 'prdctfltr') );
							foreach ( $catalog_categories as $term ) {
								if ( !empty($curr_include) && !in_array($term->slug, $curr_include) ) {
									continue;
								}

								printf('<label%4$s><input type="checkbox" value="%1$s" %3$s /><span>%2$s</span>%5$s</label>', $term->slug, $term->name, ( isset($_GET['product_cat']) && $_GET['product_cat'] == $term->slug ? 'checked' : '' ), ( isset($_GET['product_cat']) && in_array( $term->slug, ( !is_shop() && is_product_category() ? array($_GET['product_cat']) : explode(',', $pf_query->query_vars['product_cat']) ) ) ? ' class="prdctfltr_active"' : '' ), ( !empty($term->children) ? '<i class="prdctfltr-plus"></i>' : '' ) );

								if ( get_option( 'wc_settings_prdctfltr_cat_hierarchy', 'no' ) == 'yes' && !empty($term->children) ) {
									printf( '<div class="prdctfltr_sub" data-sub="%1$s">', $term->slug );
									foreach( $term->children as $sub ) {
										printf('<label%4$s><input type="checkbox" value="%1$s" %3$s /><span>%2$s</span>%5$s</label>', $sub->slug, $sub->name, ( isset($_GET['product_cat']) && $_GET['product_cat'] == $sub->slug ? 'checked' : '' ), ( isset($_GET['product_cat']) && in_array( $sub->slug, ( !is_shop() && is_product_category() ? array($_GET['product_cat']) : explode(',', $pf_query->query_vars['product_cat']) ) ) ? ' class="prdctfltr_active"' : '' ), ( !empty($sub->children) ? '<i class="prdctfltr-plus"></i>' : '' ) );
										if ( !empty($sub->children) ) {
											printf( '<div class="prdctfltr_sub" data-sub="%1$s">', $sub->slug );
											foreach( $sub->children as $subsub ) {
												printf('<label%4$s><input type="checkbox" value="%1$s" %3$s /><span>%2$s</span></label>', $subsub->slug, $subsub->name, ( isset($_GET['product_cat']) && $_GET['product_cat'] == $subsub->slug ? 'checked' : '' ), ( isset($_GET['product_cat']) && in_array( $subsub->slug, ( !is_shop() && is_product_category() ? array($_GET['product_cat']) : explode(',', $pf_query->query_vars['product_cat']) ) ) ? ' class="prdctfltr_active"' : '' ) );
											}
											echo '</div>';
										}
									}
									echo '</div>';
								}
							}
						?>
						</div>
					</div>
					<?php
					}
					?>

				<?php break;

				case 'tag' : ?>

					<?php
						if ( isset($output_terms['product_tag']) && !empty($output_terms['product_tag']) ) {
							$curr_limit = intval( get_option( 'wc_settings_prdctfltr_tag_limit', 0 ) );
							if ( $curr_limit !== 0 ) {
								$catalog_tags = array_slice($output_terms['product_tag'], 0, $curr_limit );
							}
							else {
								$catalog_tags = $output_terms['product_tag'];
							}
						}
						else {
							$curr_limit = intval( get_option( 'wc_settings_prdctfltr_tag_limit', 0 ) );
							if ( $curr_limit !== 0 ) {
								$catalog_tags = get_terms( 'product_tag', array('hide_empty' => 1, 'orderby' => 'count', 'number' => $curr_limit ) );
							}
							else {
								$catalog_tags = get_terms( 'product_tag', array('hide_empty' => 1 ) );
							}
						}

						if ( !empty( $catalog_tags ) && !is_wp_error( $catalog_tags ) ){
						$curr_term_multi = ( get_option( 'wc_settings_prdctfltr_tag_multi', 'no' ) == 'yes' ? ' prdctfltr_multi' : ' prdctfltr_single' );
					?>
					<div class="prdctfltr_filter prdctfltr_tag <?php echo $curr_term_multi; ?>">
					<input name="product_tag" type="hidden"<?php echo ( isset($_GET['product_tag'] ) ? ' value="'.$_GET['product_tag'].'"' : '' );?>>
					<span>
						<?php _e('Tags', 'prdctfltr'); ?>
						<?php
							if ( $curr_styles[5] == 'prdctfltr_disable_bar' ) {
								if ( isset($_GET['product_tag']) ) {
									$curr_selected = explode(',', $pf_query->query_vars['product_tag']);
									echo ' / <span> ';
									$i=0;
									foreach( $curr_selected as $selected ) {
										$curr_term = get_term_by('slug', $selected, 'product_tag');
										echo ( $i !== 0 ? ', ' : '' ) . $curr_term->name;
										$i++;
									}
									echo '</span> <a href="#" data-key="prdctfltr_tag"><i class="prdctfltr-delete"></i></a>';
								}
							}
						?>
						<i class="prdctfltr-down"></i>
					</span>
					<div class="prdctfltr_checkboxes"<?php echo $curr_maxheight; ?>>
						<?php
							$curr_include = get_option( 'wc_settings_prdctfltr_include_tags', array() );
							printf('<label><input type="checkbox" value="" /><span>%1$s</span></label>', __('None' , 'prdctfltr') );
							foreach ( $catalog_tags as $term ) {
								if ( !empty($curr_include) && !in_array($term->slug, $curr_include) ) {
									continue;
								}
								printf('<label%4$s><input type="checkbox" value="%1$s" %3$s /><span>%2$s</span></label>', $term->slug, $term->name, ( isset($_GET['product_tag']) && $_GET['product_tag'] == $term->slug ? 'checked' : '' ), ( isset($pf_query->query_vars['product_tag']) && in_array( $term->slug, explode(',', $pf_query->query_vars['product_tag']) ) ? ' class="prdctfltr_active"' : '' ) );
							}
						?>
						</div>
					</div>
					<?php
					}
					?>

				<?php break;

				case 'char' : ?>

					<?php
						if ( isset($output_terms['characteristics']) && !empty($output_terms['characteristics']) ) {
							$curr_limit = intval( get_option( 'wc_settings_prdctfltr_custom_tax_limit', 0 ) );
							if ( $curr_limit !== 0 ) {
								$catalog_characteristics = array_slice($output_terms['characteristics'], 0, $curr_limit );
							}
							else {
								$catalog_characteristics = $output_terms['characteristics'];
							}
						}
						else {
							$curr_limit = intval( get_option( 'wc_settings_prdctfltr_custom_tax_limit', 0 ) );
							if ( $curr_limit !== 0 ) {
								$catalog_characteristics = get_terms( 'characteristics', array('hide_empty' => 1, 'orderby' => 'count', 'number' => $curr_limit ) );
							}
							else {
								$catalog_characteristics = get_terms( 'characteristics', array('hide_empty' => 1 ) );
							}
						}

						if ( !empty( $catalog_characteristics ) && !is_wp_error( $catalog_characteristics ) ){
						$curr_term_multi = ( get_option( 'wc_settings_prdctfltr_chars_multi', 'no' ) == 'yes' ? ' prdctfltr_multi' : ' prdctfltr_single' );
					?>
					<div class="prdctfltr_filter prdctfltr_characteristics <?php echo $curr_term_multi; ?>">
					<input name="characteristics" type="hidden"<?php echo ( isset($_GET['characteristics'] ) ? ' value="'.$_GET['characteristics'].'"' : '' );?>>
					<span>
						<?php _e('Characteristics', 'prdctfltr'); ?>
						<?php
							if ( $curr_styles[5] == 'prdctfltr_disable_bar' ) {
								if ( isset($_GET['characteristics']) ) {
									$curr_selected = explode(',', $pf_query->query_vars['characteristics']);
									echo ' / <span> ';
									$i=0;
									foreach( $curr_selected as $selected ) {
										$curr_term = get_term_by('slug', $selected, 'characteristics');
										echo ( $i !== 0 ? ', ' : '' ) . $curr_term->name;
										$i++;
									}
									echo '</span> <a href="#" data-key="prdctfltr_characteristics"><i class="prdctfltr-delete"></i></a>';
								}
							}
						?>
						<i class="prdctfltr-down"></i>
					</span>
						<div class="prdctfltr_checkboxes"<?php echo $curr_maxheight; ?>>
						<?php
							$curr_include = get_option( 'wc_settings_prdctfltr_include_chars', array() );
							printf('<label><input type="checkbox" value="" /><span>%1$s</span></label>', __('None' , 'prdctfltr') );
							foreach ( $catalog_characteristics as $term ) {
								if ( !empty($curr_include) && !in_array($term->slug, $curr_include) ) {
									continue;
								}
								printf('<label%4$s><input type="checkbox" value="%1$s" %3$s /><span>%2$s</span></label>', $term->slug, $term->name, ( isset($_GET['characteristics']) && $_GET['characteristics'] == $term->slug ? 'checked' : '' ), ( isset($pf_query->query_vars['characteristics']) && in_array( $term->slug, explode(',', $pf_query->query_vars['characteristics']) ) ? ' class="prdctfltr_active"' : '' ) );
							}
						?>
						</div>
					</div>
					<?php
					}
					?>

				<?php break;

				default :
				break;

				endswitch;
			
			endforeach;

			$n = 0;
			foreach ( $curr_attrs as $k => $attr ) :

				$n++;

				if ( isset($output_terms[$attr]) && !empty($output_terms[$attr]) ) {
					$curr_attributes = $output_terms[$attr];
				}
				else {
					$curr_attributes = get_terms( $attr );
				}

				$curr_term = get_taxonomy( $attr );
				$curr_term_style = get_option( 'wc_settings_prdctfltr_' . $attr, 'pf_attr_text' );
				$curr_term_multi = ( get_option( 'wc_settings_prdctfltr_' . $attr . '_multi', 'no' ) == 'yes' ? ' prdctfltr_multi' : ' prdctfltr_single' );

		?>
					<div class="prdctfltr_filter prdctfltr_attributes prdctfltr_<?php echo $attr; ?> <?php echo $curr_term_style; ?> <?php echo $curr_term_multi; ?>">
					<input name="<?php echo $attr; ?>" type="hidden"<?php echo ( isset( $pf_query->query_vars[$attr] ) ? ' value="'.$pf_query->query_vars[$attr].'"' : '' );?>>
					<span>
						<?php echo $curr_term->label; ?>
						<?php
							if ( $curr_styles[5] == 'prdctfltr_disable_bar' ) {
								$n++;
								if ( isset($_GET[$attr]) ) {
									$curr_selected = explode(',', $pf_query->query_vars[$attr]);
									echo ' / <span>';
									$i=0;
									foreach( $curr_selected as $selected ) {
										$curr_sterm = get_term_by('slug', $selected, $attr);
										echo ( $i !== 0 ? ', ' : '' ) . $curr_sterm->name;
										$i++;
									}
									echo '</span> <a href="#" data-key="prdctfltr_' . $attr . '"><i class="prdctfltr-delete"></i></a>';
								}
							}
						?>
						<i class="prdctfltr-down"></i>
					</span>
					<div class="prdctfltr_checkboxes"<?php echo $curr_maxheight; ?>>
						<?php
							$curr_include = get_option( 'wc_settings_prdctfltr_include_' . $attr, array() );
							switch ( $curr_term_style ) {
								case 'pf_attr_text':
									$curr_blank_element = __('None' , 'prdctfltr');
								break;
								case 'pf_attr_imgtext':
									$curr_blank_element = '<img src="' . PRDCTFLTR_URL . '/lib/images/pf-transparent.gif" />';
									$curr_blank_element .= __('None' , 'prdctfltr');
								break;
								case 'pf_attr_img':
									$curr_blank_element = '<img src="' . PRDCTFLTR_URL . '/lib/images/pf-transparent.gif" />';
								break;
								default :
									$curr_blank_element = __('None' , 'prdctfltr');
								break;
							}
							printf('<label><input type="checkbox" value="" /><span>%1$s</span></label>', $curr_blank_element );
							foreach ( $curr_attributes as $attribute ) {
								if ( !empty($curr_include) && !in_array($attribute->slug, $curr_include) ) {
									continue;
								}
								switch ( $curr_term_style ) {
									case 'pf_attr_text':
										$curr_attr_element = $attribute->name;
									break;
									case 'pf_attr_imgtext':
										$curr_attr_element = wp_get_attachment_image( get_woocommerce_term_meta($attribute->term_id, $attr.'_thumbnail_id_photo', true), 'shop_thumbnail' );
										$curr_attr_element .= $attribute->name;
									break;
									case 'pf_attr_img':
										$curr_attr_element = wp_get_attachment_image( get_woocommerce_term_meta($attribute->term_id, $attr.'_thumbnail_id_photo', true), 'shop_thumbnail' );
									break;
									default :
										$curr_attr_element = $attribute->name;
									break;
								}
								printf('<label%4$s><input type="checkbox" value="%1$s" %3$s /><span>%2$s</span></label>', $attribute->slug, $curr_attr_element, ( isset($_GET[$attr]) && $_GET[$attr] == $attribute->slug ? 'checked' : '' ), ( isset($pf_query->query_vars[$attr]) && in_array( $attribute->slug, explode(',', $pf_query->query_vars[$attr]) ) ? ' class="prdctfltr_active"' : '' ) );
							}
						?>
						</div>
					</div>
					<?php
			endforeach;
		?>
		<div class="prdctfltr_clear"></div>
	</div>
</div>
<?php
	if ( get_option( 'wc_settings_prdctfltr_click_filter', 'no' ) == 'no' ) {
?>
	<a id="prdctfltr_woocommerce_filter_submit" class="button" href="#"><?php _e('Filter selected', 'prdctfltr'); ?></a>
<?php
	}
	if ( get_option( 'wc_settings_prdctfltr_disable_sale', 'no' ) == 'no' ) {
?>
<span class="prdctfltr_sale">
	<?php
	printf('<label%2$s><input name="sale_products" type="checkbox"%3$s/><span>%1$s</span></label>', __('Show only products on sale' , 'prdctfltr'), ( isset($_GET['sale_products']) ? ' class="prdctfltr_active"' : '' ), ( isset($_GET['sale_products']) ? ' checked' : '' ) );
	?>
</span>
<?php
	}
	if ( isset($_GET['s']) || isset($_GET['post_type']) ) {
?>
	<div class="prdctfltr_add_inputs">
	<?php
		if ( isset($_GET['s']) ) {
			echo '<input type="hidden" name="s" value="' . $_GET['s'] . '" />';
		}
		if ( isset($_GET['post_type']) ) {
			echo '<input type="hidden" name="post_type" value="' . $_GET['post_type'] . '" />';
		}
	?>
	</div>
<?php
	}
?>
</form>
</div>
<?php
	if ( isset($total) && $total == 0 ) {
		$curr_override = get_option( 'wc_settings_prdctfltr_noproducts', '' );
		if ( $curr_override == '' ) {
			echo do_shortcode('[prdctfltr_sc_products no_products="yes"]');
		}
		else {
			echo do_shortcode($curr_override);
		}
	}
?>