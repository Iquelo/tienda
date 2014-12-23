<?php
/*
Plugin Name: WooCommerce Product Filter
Plugin URI: http://www.mihajlovicnenad.com/product-filter
Description: Advanced product filter for any Wordpress template! - mihajlovicnenad.com
Author: Mihajlovic Nenad
Version: 1.5.0
Author URI: http://www.mihajlovicnenad.com
*/


/**
 * Check if WooCommerce is installed
 */
define( "PRDCTFLTR_MULTISITE", ( is_multisite() ? true : false ) );
$using_woo = false;
if ( PRDCTFLTR_MULTISITE === true ) {
	if ( array_key_exists( 'woocommerce/woocommerce.php', maybe_unserialize( get_site_option( 'active_sitewide_plugins') ) ) ) {
		$using_woo = true;
	}
	elseif ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ? 'active' : '' ) {
		$using_woo = true;
	}
}
elseif ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ? 'active' : '' ) {
	$using_woo = true;
}
define( "PRDCTFLTR_WOOCOMMERCE", $using_woo );

if ( $using_woo === false )
	return;

/**
 * Product Filter Translation
 */
function prdctfltr_plugin_setup() {

		$locale = apply_filters( 'plugin_locale', get_locale(), 'prdctfltr' );
		$dir    = trailingslashit( WP_LANG_DIR );

		load_textdomain( 'prdctfltr', $dir . 'plugins/prdctfltr/prdctfltr-' . $locale . '.mo' );
		load_plugin_textdomain( 'prdctfltr', false, $dir . 'plugins' );

}
add_action('init', 'prdctfltr_plugin_setup');

/**
 * Product Filter Action
 */
function prdctfltr_get_filter() {

	ob_start();

	wc_get_template( 'loop/orderby.php');

	$out = ob_get_clean();
	
	echo $out;
}
add_action('prdctfltr_output', 'prdctfltr_get_filter', 10);

/**
 * Product Filter Basic
 */
$curr_path = dirname( __FILE__ );
$curr_name = basename( $curr_path );
$curr_url = plugins_url( "/$curr_name/" );

define('PRDCTFLTR_URL', $curr_url);
function prdctfltr_path() {
	return untrailingslashit( plugin_dir_path( __FILE__ ) );
}
include_once ( $curr_path.'/lib/pf-attribute-thumbnails.php' );

/*
 * Product Filter Load Scripts
*/
if ( !function_exists('prdctfltr_scripts') ) :
function prdctfltr_scripts() {
	$curr_style = get_option( 'wc_settings_prdctfltr_style_preset', 'pf_default' );
	if ( $curr_style !== 'pf_disable' ) {
		wp_enqueue_style( 'prdctfltr-main-css', PRDCTFLTR_URL .'lib/css/prdctfltr.css');
	}
	$curr_scrollbar = get_option( 'wc_settings_prdctfltr_custom_scrollbar', 'yes' );

	if ( $curr_scrollbar == 'yes' ) {
		wp_enqueue_style( 'prdctfltr-scrollbar-css', PRDCTFLTR_URL .'lib/css/jquery.mCustomScrollbar.css');
		wp_enqueue_script( 'prdctfltr-scrollbar-js', PRDCTFLTR_URL .'lib/js/jquery.mCustomScrollbar.concat.min.js', array( 'jquery' ), '1.0', true);
	}
	wp_enqueue_style( 'prdctfltr-font-css', PRDCTFLTR_URL .'lib/font/styles.css');
	wp_enqueue_script( 'prdctfltr-main-js', PRDCTFLTR_URL .'lib/js/prdctfltr_main.js', array( 'jquery' ), '1.0', true);

	$curr_args = array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'siteurl'=> home_url('/'),
		'always_visible' => get_option( 'wc_settings_prdctfltr_always_visible', 'no' ),
		'click_filter' => get_option( 'wc_settings_prdctfltr_click_filter', 'no' ),
		'custom_scrollbar' => $curr_scrollbar,
		'columns' => get_option( 'wc_settings_prdctfltr_max_columns', 6 )
	);
	wp_localize_script( 'prdctfltr-main-js', 'prdctfltr', $curr_args );
}
endif;
add_action( 'wp_enqueue_scripts', 'prdctfltr_scripts' );

/*
 * Product Filter Pre Get Posts
*/
if ( !function_exists( 'prdctfltr_wc_meta_query' ) ) :
function prdctfltr_wc_meta_query($query) {

		if ( ( !is_admin() && $query->is_main_query() && !is_page() ) || ( isset($query->query['prdctfltr']) && $query->query['prdctfltr'] == 'active' ) ) {
			$curr_args = array();

/**
 * WooCommerce 2.2 Pagination Fix
 */
if ($query->is_main_query()) {
	function prdctfltr_pagination( $args ) {
		global $wp_query;
		$args = array(
			'base'         => str_replace( 999999999, '%#%', get_pagenum_link( 999999999 ) ),
			'format'       => '',
			'current'      => max( 1, get_query_var( 'paged' ) ),
			'total'        => $wp_query->max_num_pages,
			'prev_text'    => '&larr;',
			'next_text'    => '&rarr;',
			'type'         => 'list',
			'end_size'     => 3,
			'mid_size'     => 3
		);
		return $args;
	}
	add_filter( 'woocommerce_pagination_args', 'prdctfltr_pagination' );
}


			if ( isset($_GET['orderby']) ) {
				if ( $_GET['orderby'] == 'price' || $_GET['orderby'] == 'price-desc' ) {
					$orderby = 'meta_value_num';
					$order = ( $_GET['orderby'] == 'price-desc' ? 'DESC' : 'ASC' );
					$curr_args = array_merge( $curr_args, array(
							'meta_key' => '_price',
							'orderby' => $orderby,
							'order' => $order
						) );
				}
				else if ( $_GET['orderby'] == 'rating' ) {
					add_filter( 'posts_clauses', array( WC()->query, 'order_by_rating_post_clauses' ) );
				}
				else if ( $_GET['orderby'] == 'popularity' ) {
					$orderby = 'meta_value_num';
					$order = 'DESC';
					$curr_args = array_merge( $curr_args, array(
							'meta_key' => 'total_sales',
							'orderby' => $orderby,
							'order' => $order
						) );
				}
				else {
					$orderby = $_GET['orderby'];
					$order = ( isset($_GET['order']) ? $_GET['order'] : 'DESC' );
					$curr_args = array_merge( $curr_args, array(
							'orderby' => $orderby,
							'order' => $order
						) );
				}
			}
			else if ( isset($query->query['orderby']) ) {
				$curr_args = array_merge( $curr_args, array(
						'orderby' => $query->query['orderby'],
						'order' => ( isset($query->query['order']) ? $query->query['order'] : 'DESC' )
					) );
			}

			if ( isset($_GET['product_cat']) && $_GET['product_cat'] !== '' ) {
				$curr_args = array_merge( $curr_args, array(
							'product_cat' => $_GET['product_cat']
					) );
			}
			else if ( isset($query->query['product_cat']) ) {
				$curr_args = array_merge( $curr_args, array(
							'product_cat' => $query->query['product_cat']
					) );
			}

			if ( isset($_GET['product_tag']) && $_GET['product_tag'] !== '' ) {
				$curr_args = array_merge( $curr_args, array(
							'product_tag' => $_GET['product_tag']
					) );
			}
			else if ( isset($query->query['product_tag']) ) {
				$curr_args = array_merge( $curr_args, array(
							'product_tag' => $query->query['product_tag']
					) );
			}

			if ( isset($_GET['characteristics']) && $_GET['characteristics'] !== '' ) {
				$curr_args = array_merge( $curr_args, array(
							'characteristics' => $_GET['characteristics']
					) );
			}
			else if ( isset($query->query['product_characteristics']) ) {
				$curr_args = array_merge( $curr_args, array(
							'characteristics' => $query->query['product_characteristics']
					) );
			}

			if ( isset($_GET['min_price']) && $_GET['min_price'] !== '' ) {
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
				if ( isset($_GET['max_price']) ) {
					$curr_args = array_merge( $curr_args, array(
								'meta_key' => '_price',
								'meta_value' => array( floatval($_GET['min_price']), floatval($_GET['max_price'])),
								'meta_type' => 'numeric',
								'meta_compare' => 'BETWEEN'
						) );
				}
				else {
					$max = ceil( $wpdb->get_var(
						$wpdb->prepare('
							SELECT max(meta_value + 0)
							FROM %1$s
							LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
							WHERE meta_key = \'%3$s\'
							AND meta_value != ""
						', $wpdb->posts, $wpdb->postmeta, '_price' )
					) );
					$curr_args = array_merge( $curr_args, array(
								'meta_key' => '_price',
								'meta_value' => array( $_GET['min_price'], $max ),
								'meta_type' => 'numeric',
								'meta_compare' => 'BETWEEN'
						) );
				}
			}
			else if ( isset($query->query['min_price']) ) {
				$curr_args = array_merge( $curr_args, array(
							'meta_key' => '_price',
							'meta_value' => array( floatval($query->query['min_price']), floatval($query->query['max_price'])),
							'meta_type' => 'numeric',
							'meta_compare' => 'BETWEEN'
					) );
			}

			foreach( $_GET as $k => $v ){
				if ( strpos($k,'pa_') !== false && $v !== '' ) {
						$curr_args = array_merge( $curr_args, array(
								$k => $v
							) );
				}
			}

			if ( isset($_GET['sale_products']) && $_GET['sale_products'] !== '' ) {
				if ( is_page() ) {
					global $wpdb;
					$min = floor( $wpdb->get_var(
						$wpdb->prepare('
							SELECT min(meta_value + 0)
							FROM %1$s
							LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
							WHERE ( meta_key = \'%3$s\' OR meta_key = \'%4$s\' )
							AND meta_value != ""
							', $wpdb->posts, $wpdb->postmeta, '_sale_price', '_min_variation_sale_price' )
						)
					);

					if ( isset($_GET['max_price']) ) {
						$curr_args = array_merge( $curr_args, array(
									'meta_key' => '_sale_price',
									'meta_value' => array( ( floatval($_GET['min_price']) == 0 ? $min : floatval($_GET['min_price']) ), floatval($_GET['max_price'])),
									'meta_type' => 'numeric',
									'meta_compare' => 'BETWEEN'
							) );
					}
					else {
						$max = ceil( $wpdb->get_var(
							$wpdb->prepare('
								SELECT max(meta_value + 0)
								FROM %1$s
								LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
								WHERE meta_key = \'%3$s\'
							', $wpdb->posts, $wpdb->postmeta, '_sale_price' )
						) );
						$curr_args = array_merge( $curr_args, array(
									'meta_key' => '_sale_price',
									'meta_value' => array(( isset($_GET['min_price']) == 0 ? $min : floatval($_GET['min_price']) ), $max),
									'meta_type' => 'numeric',
									'meta_compare' => 'BETWEEN'
							) );
					}
				}
				else {
					add_filter( 'posts_join' , 'prdctfltr_join_posts');
					add_filter( 'posts_where' , 'prdctfltr_sale_filter' );
				}

			}

			if ( isset($query->query['http_query']) ) {
				parse_str(html_entity_decode($query->query['http_query']), $curr_http_args);
				$curr_args = array_merge( $curr_args, $curr_http_args );
			}

		foreach ( $curr_args as $k => $v ) {
			$query->set($k,$v);
		}

}

}
endif;
add_filter('pre_get_posts','prdctfltr_wc_meta_query');

/*
 * Product Filter Sale Filter
*/
function prdctfltr_sale_filter ( $where ) {
	global $wpdb;

	$where = str_replace(".meta_key = '_price'", ".meta_key = '_sale_price'", $where);

	$where = str_replace("AND ( ($wpdb->postmeta.meta_key = '_visibility' AND CAST($wpdb->postmeta.meta_value AS CHAR) IN ('visible','catalog')) )", "", $where);

	if ( isset($_GET['min_price'] ) ) {

		$min = $_GET['min_price'];

		if ( isset($_GET['max_price']) ) {
			$max = $_GET['max_price'];
		}
		else {
			$max = ceil( $wpdb->get_var(
				$wpdb->prepare('
					SELECT max(meta_value + 0)
					FROM %1$s
					LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
					WHERE meta_key = \'%3$s\'
				', $wpdb->posts, $wpdb->postmeta, '_price' )
			) );
		}
			$where .= " AND ( ( ($wpdb->postmeta.meta_key LIKE '_sale_price' AND $wpdb->postmeta.meta_value > $min ) AND ($wpdb->postmeta.meta_key LIKE '_sale_price' AND $wpdb->postmeta.meta_value < $max ) ) OR ( ($wpdb->postmeta.meta_key LIKE '_min_variation_sale_price' AND $wpdb->postmeta.meta_value > $min ) AND ($wpdb->postmeta.meta_key LIKE '_min_variation_sale_price' AND $wpdb->postmeta.meta_value < $max ) ) ) ";
	}
	else {
		$where .= " AND ( ( ($wpdb->postmeta.meta_key LIKE '_sale_price' AND $wpdb->postmeta.meta_value > 0 ) OR ($wpdb->postmeta.meta_key LIKE '_min_variation_sale_price' AND $wpdb->postmeta.meta_value > 0 ) ) )";
	}

	remove_filter( 'posts_join' , 'prdctfltr_join_posts');
	remove_filter( 'posts_where' , 'prdctfltr_sale_filter' );

	return $where;
	
	}

/*
 * Product Filter Join Tables
*/
function prdctfltr_join_posts($join){
	global $wpdb;
	$join .= " LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id ";
	return $join;
}

/*
 * Product Filter Register Characteristics
*/
$curr_char = get_option( 'wc_settings_prdctfltr_custom_tax', 'no' );
if ( $curr_char == 'yes' ) {
	function prdctfltr_characteristics() {

		$labels = array(
			'name'                       => _x( 'Characteristics', 'taxonomy general name', 'prdctfltr' ),
			'singular_name'              => _x( 'Characteristics', 'taxonomy singular name', 'prdctfltr' ),
			'search_items'               => __( 'Search Characteristics', 'prdctfltr' ),
			'popular_items'              => __( 'Popular Characteristics', 'prdctfltr' ),
			'all_items'                  => __( 'All Characteristics', 'prdctfltr' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Characteristics', 'prdctfltr' ),
			'update_item'                => __( 'Update Characteristics', 'prdctfltr' ),
			'add_new_item'               => __( 'Add New Characteristic', 'prdctfltr' ),
			'new_item_name'              => __( 'New Characteristic Name', 'prdctfltr' ),
			'separate_items_with_commas' => __( 'Separate Characteristics with commas', 'prdctfltr' ),
			'add_or_remove_items'        => __( 'Add or remove characteristics', 'prdctfltr' ),
			'choose_from_most_used'      => __( 'Choose from the most used characteristics', 'prdctfltr' ),
			'not_found'                  => __( 'No characteristics found.', 'prdctfltr' ),
			'menu_name'                  => __( 'Characteristics', 'prdctfltr' ),
		);

		$args = array(
			'hierarchical'          => false,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'characteristics' ),
		);

		register_taxonomy( 'characteristics', array('product'), $args );
	}
	add_action( 'init', 'prdctfltr_characteristics', 0 );
}

$curr_disable = get_option( 'wc_settings_prdctfltr_enable', 'yes' );

if ( $curr_disable == 'yes') {

	/*
	 * Product Filter Override WooCommerce Template
	*/
	function prdctrfltr_add_filter ( $template, $slug, $name ) {

		if ( $name ) {
			$path = plugin_dir_path( __FILE__ ) . WC()->template_path() . "{$slug}-{$name}.php";
		} else {
			$path = plugin_dir_path( __FILE__ ) . WC()->template_path() . "{$slug}.php";
		}

		return file_exists( $path ) ? $path : $template;

	}
	add_filter( 'wc_get_template_part', 'prdctrfltr_add_filter', 10, 3 );

	function prdctrfltr_add_loop_filter ( $template, $template_name, $template_path ) {

		$path = plugin_dir_path( __FILE__ ) . $template_path . $template_name;
		return file_exists( $path ) ? $path : $template;

	}
	add_filter( 'woocommerce_locate_template', 'prdctrfltr_add_loop_filter', 10, 3 );


}

/*
 * Product Filter Search Variable Products
*/
function prdctrfltr_search_array($array, $attrs) {
	$results = array();

	foreach ($array as $subarray) {

		if ( isset($subarray['attributes'])) {
			if ( in_array($attrs, $subarray['attributes']) ) {
				$results[] = $subarray;
			}
			else {
				foreach ( $attrs as $k => $v ) {
					if (in_array($v, $subarray['attributes'])) {
						$results[] = $subarray;
					}
				}
			}
		}
	}

	return $results;
}

/*
 * Product Filter Get Variable Product
*/
$curr_variable = get_option( 'wc_settings_prdctfltr_use_variable_images', 'no' );
if ( $curr_variable == 'yes' ) {

	if ( function_exists('runkit_function_rename') && function_exists( 'woocommerce_get_product_thumbnail' ) ) :
		runkit_function_rename ( 'woocommerce_get_product_thumbnail', 'old_woocommerce_get_product_thumbnail' );
	endif;

	if ( !function_exists( 'woocommerce_get_product_thumbnail' ) ) :
	function woocommerce_get_product_thumbnail( $size = 'shop_catalog', $placeholder_width = 0, $placeholder_height = 0  ) {
		global $post;

		$product = get_product($post->ID);

		$attrs = array();
		foreach($_GET as $k => $v){
			if (strpos($k,'pa_') !== false) {
				$attrs = $attrs + array(
					$k => $v
				);
			}
		}

		if ( count($attrs) > 0 ) {

			if ( $product->is_type( 'variable' ) ) {
				$curr_var = $product->get_available_variations();
				$si = prdctrfltr_search_array($curr_var, $attrs);
			}
		}

		if ( isset($si[0]) && $si[0]['variation_id'] && has_post_thumbnail( $si[0]['variation_id'] ) ) {
			$image = get_the_post_thumbnail( $si[0]['variation_id'], $size );
		} elseif ( has_post_thumbnail( $product->id ) ) {
			$image = get_the_post_thumbnail( $product->id, $size );
		} elseif ( ( $parent_id = wp_get_post_parent_id( $product->id ) ) && has_post_thumbnail( $parent_id ) ) {
			$image = get_the_post_thumbnail( $product, $size );
		} else {
			$image = wc_placeholder_img( $size );
		}

		return $image;

	}
	endif;
}

/*
 * Product Filter Settings Class
*/
class WC_Settings_Prdctfltr {

	public static function init() {
		add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::prdctfltr_add_settings_tab', 50 );
		add_action( 'woocommerce_settings_tabs_settings_products_filter', __CLASS__ . '::prdctfltr_settings_tab' );
		add_action( 'woocommerce_update_options_settings_products_filter', __CLASS__ . '::prdctfltr_update_settings' );
	}

	public static function prdctfltr_add_settings_tab( $settings_tabs ) {
		$settings_tabs['settings_products_filter'] = __( 'Product Filter', 'prdctfltr' );
		return $settings_tabs;
	}

	public static function prdctfltr_settings_tab() {
		woocommerce_admin_fields( self::prdctfltr_get_settings() );
	}

	public static function prdctfltr_update_settings() {
		woocommerce_update_options( self::prdctfltr_get_settings() );
	}

	public static function prdctfltr_get_settings() {

		if ( $attribute_taxonomies = wc_get_attribute_taxonomies() ) {
			$curr_attr = array();
			foreach ( $attribute_taxonomies as $tax ) {

				$curr_label = ! empty( $tax->attribute_label ) ? $tax->attribute_label : $tax->attribute_name;

				$curr_attr['pa_' . $tax->attribute_name] = $curr_label;

			}
		}

		$catalog_categories = get_terms( 'product_cat' );
		$curr_cats = array();
		if ( !empty( $catalog_categories ) && !is_wp_error( $catalog_categories ) ){
			foreach ( $catalog_categories as $term ) {
				$curr_cats[$term->slug] = $term->name;
			}
		}

		$catalog_tags = get_terms( 'product_tag' );
		$curr_tags = array();
		if ( !empty( $catalog_tags ) && !is_wp_error( $catalog_tags ) ){
			foreach ( $catalog_tags as $term ) {
				$curr_tags[$term->slug] = $term->name;
			}
		}

		$catalog_chars = ( taxonomy_exists('characteristics') ? get_terms( 'characteristics' ) : array() );
		$curr_chars = array();
		if ( !empty( $catalog_chars ) && !is_wp_error( $catalog_chars ) ){
			foreach ( $catalog_chars as $term ) {
				$curr_chars[$term->slug] = $term->name;
			}
		}


		$settings = array(
			'section_enable_title' => array(
				'name'     => __( 'Product Filter Basic Settings', 'prdctfltr' ),
				'type'     => 'title',
				'desc'     => __( 'Setup you Product Filter appearance.', 'prdctfltr' ),
				'id'       => 'wc_settings_prdctfltr_enable_title'
			),
			'prdctfltr_enable' => array(
				'name' => __( 'Enable/Disable Shop Template', 'prdctfltr' ),
				'type' => 'checkbox',
				'desc' => __( 'Uncheck this option in order to disable Product Filter template override on Shop page.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_enable',
				'default' => 'yes',
			),
			'prdctfltr_always_visible' => array(
				'name' => __( 'Always Visible', 'prdctfltr' ),
				'type' => 'checkbox',
				'desc' => __( 'This option will make Product Filter visible without the slide up/down animation at all times.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_always_visible',
				'default' => 'no',
			),
			'prdctfltr_click_filter' => array(
				'name' => __( 'Instant Filtering', 'prdctfltr' ),
				'type' => 'checkbox',
				'desc' => __( 'Check this option to disable the filter button and use instant product filtering.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_click_filter',
				'default' => 'no',
			),
			'prdctfltr_limit_max_height' => array(
				'name' => __( 'Limit Max Height', 'prdctfltr' ),
				'type' => 'checkbox',
				'desc' => __( 'Check this option to limit the Max Height of the filter. This way the filter will be shown in a single row with horizontal and vertical scroll bars.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_limit_max_height',
				'default' => 'no',
			),
			'prdctfltr_max_height' => array(
				'name' => __( 'Max Height', 'prdctfltr' ),
				'type' => 'number',
				'desc' => __( 'Set the Max Height value.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_max_height',
				'default' => 150,
				'custom_attributes' => array(
					'min' 	=> 100,
					'max' 	=> 300,
					'step' 	=> 1
				)
			),
			'prdctfltr_custom_scrollbar' => array(
				'name' => __( 'Use Custom Scroll Bars', 'prdctfltr' ),
				'type' => 'checkbox',
				'desc' => __( 'Check this option to override default browser scroll bars with javascrips scrollbars in Max Height mode.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_custom_scrollbar',
				'default' => 'yes',
			),
			'prdctfltr_max_columns' => array(
				'name' => __( 'Max Columns', 'prdctfltr' ),
				'type' => 'number',
				'desc' => __( 'This option sets the number of columns for the filter. If the Max Height is set to 0 the filters will be added in the next row when the Max Columns number is reached.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_max_columns',
				'default' => 6,
				'custom_attributes' => array(
					'min' 	=> 1,
					'max' 	=> 6,
					'step' 	=> 1
				)
			),
			'prdctfltr_adoptive' => array(
				'name' => __( 'Enable/Disable Adoptive Filtering', 'prdctfltr' ),
				'type' => 'checkbox',
				'desc' => __( 'Check this option to enable the adoptive filtering.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_adoptive',
				'default' => 'no',
			),
			'prdctfltr_use_variable_images' => array(
				'name' => __( 'Use Variable Images', 'prdctfltr' ),
				'type' => 'checkbox',
				'desc' => __( 'Check this option to use variable images override on shop and archive pages. CAUTION This setting does not work on all servers by default. Additional server setup might be needed.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_use_variable_images',
				'default' => 'no',
			),
			'prdctfltr_disable_bar' => array(
				'name' => __( 'Disable Top Bar', 'prdctfltr' ),
				'type' => 'checkbox',
				'desc' => __( 'Check this option to hide the Product Filter top bar. This option will also make the filter always visible.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_disable_bar',
				'default' => 'no',
			),
			'prdctfltr_disable_sale' => array(
				'name' => __( 'Disable Sale Button', 'prdctfltr' ),
				'type' => 'checkbox',
				'desc' => __( 'Check this option to hide the Product Filter sale button.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_disable_sale',
				'default' => 'no',
			),
			'prdctfltr_noproducts' => array(
				'name' => __( 'Override No Products Action', 'prdctfltr' ),
				'type' => 'textarea',
				'desc' => __( 'Input HTML/Shortcode to override the default action when no products are found. Default action means that random products will be shown when there are no products within the filter query.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_noproducts',
				'default' => '',
				'css' 		=> 'min-width:600px;margin-top:12px;',
			),
			'section_enable_end' => array(
				'type' => 'sectionend',
				'id' => 'wc_settings_prdctfltr_enable_end'
			),
			'section_style_title' => array(
				'name'     => __( 'Product Filter Style', 'prdctfltr' ),
				'type'     => 'title',
				'desc'     => __( 'Select style preset to use. Use custom preset for your own style. Use Disable CSS to disable all CSS for product filter.', 'prdctfltr' ),
				'id'       => 'wc_settings_prdctfltr_style_title'
			),
			'prdctfltr_style_preset' => array(
				'name' => __( 'Select Style Preset', 'prdctfltr' ),
				'type' => 'select',
				'desc' => __( 'Select style preset to use or use Disable CSS option for custom settings.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_style_preset',
				'options' => array(
					'pf_disable' => __( 'Disable CSS', 'prdctfltr' ),
					'pf_arrow' => __( 'Arrow', 'prdctfltr' ),
					'pf_arrow_inline' => __( 'Arrow Inline', 'prdctfltr' ),
					'pf_default' => __( 'Default', 'prdctfltr' ),
					'pf_default_inline' => __( 'Default Inline', 'prdctfltr' ),
					'pf_select' => __( 'Use Select Box', 'prdctfltr' ),
				),
				'default' => 'pf_default'
			),
			'prdctfltr_icon' => array(
				'name' => __( 'Override Default Icon', 'prdctfltr' ),
				'type' => 'text',
				'desc' => __( 'Input the icon class in order to override default Product Filter icon.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_icon',
				'default' => '',
			),
			'section_style_end' => array(
				'type' => 'sectionend',
				'id' => 'wc_settings_prdctfltr_style_end'
			),
			'section_title' => array(
				'name'     => __( 'Select Product Filters', 'prdctfltr' ),
				'type'     => 'title',
				'desc'     => __( 'Select product filters to use.', 'prdctfltr' ),
				'id'       => 'wc_settings_prdctfltr_section_title'
			),
			'prdctfltr_selected' => array(
				'name' => __( 'Select Filters', 'prdctfltr' ),
				'type' => 'multiselect',
				'desc' => __( 'Select filters. Use CTRL+Click to select multiple filters.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_selected',
				'options' => array(
					'sort' => __('Sort By', 'prdctfltr'),
					'price' => __('By Price', 'prdctfltr'),
					'cat' => __('By Categories', 'prdctfltr'),
					'tag' => __('By Tags', 'prdctfltr'),
					'char' => __('By Characteristics', 'prdctfltr')
				),
				'default' => array('sort','price','cat')
			),
			'prdctfltr_attributes' => array(
				'name' => __( 'Select Attributes', 'prdctfltr' ),
				'type' => 'multiselect',
				'desc' => __( 'Select your attributes. Use CTRL+Click to select multiple attributes.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_attributes',
				'options' => $curr_attr,
				'default' => array()
			),
			'section_end' => array(
				'type' => 'sectionend',
				'id' => 'wc_settings_prdctfltr_section_end'
			),
			'section_price_filter_title' => array(
				'name'     => __( 'By Price Filter Settings', 'prdctfltr' ),
				'type'     => 'title',
				'desc'     => __( 'Setup by price filter.', 'prdctfltr' ),
				'id'       => 'wc_settings_prdctfltr_price_filter_title'
			),
			'prdctfltr_price_range' => array(
				'name' => __( 'Price Range Filter Initial', 'prdctfltr' ),
				'type' => 'number',
				'desc' => __( 'Input basic initial price.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_price_range',
				'default' => 100,
				'custom_attributes' => array(
					'min' 	=> 0.5,
					'max' 	=> 9999999,
					'step' 	=> 0.1
				)
			),
			'prdctfltr_price_range_add' => array(
				'name' => __( 'Price Range Filter Price Add', 'prdctfltr' ),
				'type' => 'number',
				'desc' => __( 'Input the price to add. E.G. You have set the initial value to 99.9, and you now wish to add a 100 more on the next price options to achieve filtering from 0-99.9, 99.9-199.9, 199.9- 299.9 and so on...', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_price_range_add',
				'default' => 100,
				'custom_attributes' => array(
					'min' 	=> 0.5,
					'max' 	=> 9999999,
					'step' 	=> 0.1
				)
			),
			'prdctfltr_price_range_limit' => array(
				'name' => __( 'Price Range Filter Price Limit', 'prdctfltr' ),
				'type' => 'number',
				'desc' => __( 'Input the number of price intervals you wish to use.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_price_range_limit',
				'default' => 6,
				'custom_attributes' => array(
					'min' 	=> 2,
					'max' 	=> 20,
					'step' 	=> 1
				)
			),
			'prdctfltr_price_adoptive' => array(
				'name' => __( 'Use Adoptive Filtering', 'prdctfltr' ),
				'type' => 'checkbox',
				'desc' => __( 'Check this option to use adoptive filtering on prices.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_price_adoptive',
				'default' => 'no',
			),
			'section_price_filter_end' => array(
				'type' => 'sectionend',
				'id' => 'wc_settings_prdctfltr_price_filter_end'
			),
			'section_cat_filter_title' => array(
				'name'     => __( 'By Category Filter Settings', 'prdctfltr' ),
				'type'     => 'title',
				'desc'     => __( 'Setup by category filter.', 'prdctfltr' ),
				'id'       => 'wc_settings_prdctfltr_cat_filter_title'
			),
			'prdctfltr_include_cats' => array(
				'name' => __( 'Select Categories', 'prdctfltr' ),
				'type' => 'multiselect',
				'desc' => __( 'Select categories to include. Use CTRL+Click to select multiple categories or deselect all.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_include_cats',
				'options' => $curr_cats,
				'default' => array()
			),
			'prdctfltr_cat_limit' => array(
				'name' => __( 'Limit Categories', 'prdctfltr' ),
				'type' => 'number',
				'desc' => __( 'Limit number of categories to be shown. If limit is set categories with most posts will be shown first.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_cat_limit',
				'default' => 0,
				'custom_attributes' => array(
					'min' 	=> 0,
					'max' 	=> 100,
					'step' 	=> 1
				)
			),
			'prdctfltr_cat_hierarchy' => array(
				'name' => __( 'Use Category Hierarchy', 'prdctfltr' ),
				'type' => 'checkbox',
				'desc' => __( 'Check this option to enable category hierarchy.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_cat_hierarchy',
				'default' => 'no',
			),
			'prdctfltr_cat_multi' => array(
				'name' => __( 'Use Multi Select', 'prdctfltr' ),
				'type' => 'checkbox',
				'desc' => __( 'Check this option to enable multi-select on categories.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_cat_multi',
				'default' => 'no',
			),
			'prdctfltr_cat_adoptive' => array(
				'name' => __( 'Use Adoptive Filtering', 'prdctfltr' ),
				'type' => 'checkbox',
				'desc' => __( 'Check this option to use adoptive filtering on categories.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_cat_adoptive',
				'default' => 'no',
			),
			'section_cat_filter_end' => array(
				'type' => 'sectionend',
				'id' => 'wc_settings_prdctfltr_cat_filter_end'
			),
			'section_tag_filter_title' => array(
				'name'     => __( 'By Tag Filter Settings', 'prdctfltr' ),
				'type'     => 'title',
				'desc'     => __( 'Setup by tag filter.', 'prdctfltr' ),
				'id'       => 'wc_settings_prdctfltr_tag_filter_title'
			),
			'prdctfltr_include_tags' => array(
				'name' => __( 'Select Tags', 'prdctfltr' ),
				'type' => 'multiselect',
				'desc' => __( 'Select tags to include. Use CTRL+Click to select multiple tags or deselect all.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_include_tags',
				'options' => $curr_tags,
				'default' => array()
			),
			'prdctfltr_tag_limit' => array(
				'name' => __( 'Limit Tags', 'prdctfltr' ),
				'type' => 'number',
				'desc' => __( 'Limit number of tags to be shown. If limit is set tags with most posts will be shown first.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_tag_limit',
				'default' => 0,
				'custom_attributes' => array(
					'min' 	=> 0,
					'max' 	=> 100,
					'step' 	=> 1
				)
			),
			'prdctfltr_tag_multi' => array(
				'name' => __( 'Use Multi Select', 'prdctfltr' ),
				'type' => 'checkbox',
				'desc' => __( 'Check this option to enable multi-select on tags.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_tag_multi',
				'default' => 'no',
			),
			'prdctfltr_tag_adoptive' => array(
				'name' => __( 'Use Adoptive Filtering', 'prdctfltr' ),
				'type' => 'checkbox',
				'desc' => __( 'Check this option to use adoptive filtering on tags.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_tag_adoptive',
				'default' => 'no',
			),
			'section_tag_filter_end' => array(
				'type' => 'sectionend',
				'id' => 'wc_settings_prdctfltr_tag_filter_end'
			),
			'section_char_filter_title' => array(
				'name'     => __( 'By Characteristics Filter Settings', 'prdctfltr' ),
				'type'     => 'title',
				'desc'     => __( 'Setup by characteristics filter.', 'prdctfltr' ),
				'id'       => 'wc_settings_prdctfltr_char_filter_title'
			),
			'prdctfltr_custom_tax' => array(
				'name' => __( 'Use Characteristics', 'prdctfltr' ),
				'type' => 'checkbox',
				'desc' => __( 'Enable this option to get custom characteristics product meta box.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_custom_tax',
				'default' => 'yes',
			),
			'prdctfltr_include_chars' => array(
				'name' => __( 'Select Characteristics', 'prdctfltr' ),
				'type' => 'multiselect',
				'desc' => __( 'Select characteristics to include. Use CTRL+Click to select multiple characteristics or deselect all.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_include_chars',
				'options' => $curr_chars,
				'default' => array()
			),
			'prdctfltr_custom_tax_limit' => array(
				'name' => __( 'Limit Characteristics', 'prdctfltr' ),
				'type' => 'number',
				'desc' => __( 'Limit number of characteristics to be shown. If limit is set characteristics with most posts will be shown first.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_custom_tax_limit',
				'default' => 0,
				'custom_attributes' => array(
					'min' 	=> 0,
					'max' 	=> 100,
					'step' 	=> 1
				)
			),
			'prdctfltr_chars_multi' => array(
				'name' => __( 'Use Multi Select', 'prdctfltr' ),
				'type' => 'checkbox',
				'desc' => __( 'Check this option to enable multi-select on characteristics.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_chars_multi',
				'default' => 'no',
			),
			'prdctfltr_chars_adoptive' => array(
				'name' => __( 'Use Adoptive Filtering', 'prdctfltr' ),
				'type' => 'checkbox',
				'desc' => __( 'Check this option to use adoptive filtering on characteristics.', 'prdctfltr' ),
				'id'   => 'wc_settings_prdctfltr_chars_adoptive',
				'default' => 'no',
			),
			'section_char_filter_end' => array(
				'type' => 'sectionend',
				'id' => 'wc_settings_prdctfltr_char_filter_end'
			),

		);

		$attribute_taxonomies = wc_get_attribute_taxonomies();
		if ($attribute_taxonomies) {
			$settings = $settings + array (
				
			);
			foreach ($attribute_taxonomies as $tax) {

				$catalog_attrs = get_terms( 'pa_' . $tax->attribute_name );
				$curr_attrs = array();
				if ( !empty( $catalog_attrs ) && !is_wp_error( $catalog_attrs ) ){
					foreach ( $catalog_attrs as $term ) {
						$curr_attrs[$term->slug] = $term->name;
					}
				}

				$settings = $settings + array(
					'section_pa_'.$tax->attribute_name.'_title' => array(
						'name'     => __( 'By', 'prdctfltr' ) . ' ' . $tax->attribute_label . ' ' . __( 'Filter Settings', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => __( 'Select options for the current attribute.', 'prdctfltr' ),
						'id'       => 'wc_settings_prdctfltr_pa_'.$tax->attribute_name.'_title'
					),
					'prdctfltr_include_pa_'.$tax->attribute_name => array(
						'name' => __( 'Select Terms', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => __( 'Select terms to include. Use CTRL+Click to select multiple terms or deselect all.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_include_pa_'.$tax->attribute_name,
						'options' => $curr_attrs,
						'default' => array()
					),
					'prdctfltr_pa_'.$tax->attribute_name => array(
						'name' => __( 'Appearance', 'prdctfltr' ),
						'type' => 'select',
						'desc' => __( 'Select style preset to use with the attribute.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_pa_'.$tax->attribute_name,
						'options' => array(
							'pf_attr_text' => __( 'Text', 'prdctfltr' ),
							'pf_attr_imgtext' => __( 'Thumbnails with text', 'prdctfltr' ),
							'pf_attr_img' => __( 'Thumbnails only', 'prdctfltr' )
						),
						'default' => 'pf_attr_text'
					),
					'prdctfltr_pa_'.$tax->attribute_name.'_multi' => array(
						'name' => __( 'Use Multi Select', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to enable multi-select on current attribute.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_pa_'.$tax->attribute_name.'_multi',
						'default' => 'no',
					),
					'prdctfltr_pa_'.$tax->attribute_name.'_adoptive' => array(
						'name' => __( 'Use Adoptive Filtering', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => __( 'Check this option to use adoptive filtering on current attribute.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_pa_'.$tax->attribute_name.'_adoptive',
						'default' => 'no',
					),
					'section_pa_'.$tax->attribute_name.'_end' => array(
						'type' => 'sectionend',
						'id' => 'wc_settings_prdctfltr_pa_'.$tax->attribute_name.'_end'
					),
					
				);
			}
		}

		return apply_filters( 'wc_settings_products_filter_settings', $settings );
	}

}

WC_Settings_Prdctfltr::init();

// Sort hierarchicaly
function prdctfltr_sort_terms_hierarchicaly( Array &$cats, Array &$into, $parentId = 0 ) {
	foreach ($cats as $i => $cat) {
		if ($cat->parent == $parentId) {
			$into[$cat->term_id] = $cat;
			unset($cats[$i]);
		}
	}

	foreach ($into as $topCat) {
		$topCat->children = array();
		prdctfltr_sort_terms_hierarchicaly($cats, $topCat->children, $topCat->term_id);
	}
}

// [prdctfltr_sc_products]
function prdctfltr_sc_products( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'ids' => '',
		'rows' => 4,
		'columns' => '4',
		'ajax' => 'yes',
		'pagination' => 'yes',
		'use_filter' => 'yes',
		'no_products' => 'no',
		'min_price' => '',
		'max_price' => '',
		'orderby' => '',
		'order' => '',
		'meta_key'=> '',
		'product_cat'=> '',
		'product_tag'=> '',
		'product_characteristics'=> '',
		'product_attributes'=> '',
		'http_query' => '',
		'action' => '',
		'bot_margin' => 36,
		'class' => '',
		'shortcode_id' => ''
	), $atts ) );

	if ( $ids == '' ) $ids = '0';
	$exploded = explode(',', $ids);

	global $paged;
	$args = array();
	if ( empty( $paged ) ) $paged = 1;

	if ( $no_products == 'no' ) {
		$args = $args + array (
			'prdctfltr' => 'active'
		);
	}
	else {
		$use_filter = 'no';
		$pagination = 'no';
		$orderby = 'rand';
	}

	global $prdctfltr_global;

	$prdctfltr_global['posts_per_page'] = $columns*$rows;
	if ( $action !== '' ) {
		$prdctfltr_global['action'] = $action;
	}

	$args = $args + array (
		'post_type'				=> 'product',
		'post_status'			=> 'publish',
		'posts_per_page' 		=> $prdctfltr_global['posts_per_page'],
		'paged' 				=> $paged,
		'meta_query' 			=> array(
			array(
				'key' 			=> '_visibility',
				'value' 		=> array('catalog', 'visible'),
				'compare' 		=> 'IN'
			)
		)
	);

	if ( $orderby !== '' ) {
		$args['orderby'] = $orderby;
	}
	if ( $order !== '' ) {
		$args['order'] = $order;
	}
	if ( $order !== '' ) {
		$args['meta_key'] = $meta_key;
	}
	if ( $min_price !== '' ) {
		$args['min_price'] = $min_price;
	}
	if ( $max_price !== '' ) {
		$args['max_price'] = $max_price;
	}
	if ( $product_cat !== '' ) {
		$args['product_cat'] = $product_cat;
	}
	if ( $product_tag !== '' ) {
		$args['product_tag'] = $product_tag;
	}
	if ( $product_characteristics !== '' ) {
		$args['product_characteristics'] = $product_characteristics;
	}
	if ( $product_attributes !== '' ) {
		$args['product_attributes'] = $product_attributes;
	}
	if ( $http_query !== '' ) {
		$args['http_query'] = $http_query;
	}

	$query_string_ajax = http_build_query($args);

	$bot_margin = (int)$bot_margin;
	$margin = " style='margin-bottom".$bot_margin."px'";

	$out = '';

	global $woocommerce, $woocommerce_loop;
	
	$woocommerce_loop['columns'] = $columns;

	$products = new WP_Query( $args );

	ob_start();

	if ( $products->have_posts() ) : ?>

		<?php
			if ( $use_filter == 'yes' ) {
				wc_get_template( 'loop/orderby.php' );
			}
		?>

		<?php woocommerce_product_loop_start(); ?>

			<?php while ( $products->have_posts() ) : $products->the_post(); ?>

				<?php wc_get_template_part( 'content', 'product' ); ?>

			<?php endwhile; ?>

		<?php woocommerce_product_loop_end(); ?>

	<?php
	
	else :
		wc_get_template( 'loop/no-products-found.php' );
	endif;

	wp_reset_postdata();
	
	$shortcode = ob_get_clean();

	$out .= '<div' . ( $shortcode_id != '' ? ' id="'.$shortcode_id.'"' : '' ) . ' class="prdctfltr_sc_products woocommerce' . ( $class != '' ? ' '.$class.'' : '' ) . '"'.$margin.'>';
	$out .= do_shortcode($shortcode);

	if ( $pagination == 'yes' ) {

		ob_start();
		?>
		<nav class="woocommerce-pagination">
			<?php
				echo paginate_links( apply_filters( 'woocommerce_pagination_args', array(
					'base'         => str_replace( 999999999, '%#%', get_pagenum_link( 999999999 ) ),
					'format'       => '',
					'current'      => $paged,
					'total'        => $products->max_num_pages,
					'prev_text'    => '&larr;',
					'next_text'    => '&rarr;',
					'type'         => 'list',
					'end_size'     => 3,
					'mid_size'     => 3
				) ) );
			?>
		</nav>
		<?php
		$pagination = ob_get_clean();

		$out .= $pagination;

	}


	$out .= '</div>';
	return $out;

}
add_shortcode( 'prdctfltr_sc_products', 'prdctfltr_sc_products' );

// [prdctfltr_sc_get_filter]
function prdctfltr_sc_get_filter( $atts, $content = null ) {
	return prdctfltr_get_filter();
}
add_shortcode( 'prdctfltr_sc_get_filter', 'prdctfltr_sc_get_filter' );




class prdctfltr extends WP_Widget {

	function prdctfltr() {
		$widget_ops = array(
			'classname' => 'prdctfltr-widget',
			'description' => __( 'Product Filter widget version.', 'wdgtcstmzr' )
		);
		$this->WP_Widget( 'prdctfltr', '+ Product Filter', $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		$curr_pf = array (
			'preset' => $instance['preset'],
		);

		echo $before_widget;

		global $wp, $prdctfltr_global;

		if ( isset($prdctfltr_global['active']) && $prdctfltr_global['active'] == 'true' ) {
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
			( $curr_pf['preset'] !== 'pf_disable' ? ' ' . $curr_pf['preset'] : '' ),
			( get_option( 'wc_settings_prdctfltr_always_visible', 'no' ) == 'no' && get_option( 'wc_settings_prdctfltr_disable_bar', 'no' ) == 'no' ? 'prdctfltr_slide' : 'prdctfltr_always_visible' ),
			( get_option( 'wc_settings_prdctfltr_click_filter', 'no' ) == 'no' ? 'prdctfltr_click' : 'prdctfltr_click_filter' ),
			( get_option( 'wc_settings_prdctfltr_limit_max_height', 'no' ) == 'no' ? 'prdctfltr_rows' : 'prdctfltr_maxheight' ),
			( get_option( 'wc_settings_prdctfltr_custom_scrollbar', 'yes' ) == 'no' ? '' : 'prdctfltr_scroll_active' )
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
		<div id="prdctfltr_woocommerce" class="prdctfltr_woocommerce woocommerce<?php echo implode( $curr_styles, ' ' ); ?>">
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
					'posts_per_page' 		=> (isset($prdctfltr_global['posts_per_page']) ? $prdctfltr_global['posts_per_page'] : 10 ),
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

					query_posts(http_build_query($pf_query->query).'&posts_per_page='.$total);

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
			<input type="hidden" name="filter_results" value="true" />
			<input type="hidden" name="widget_search" value="true" /><?php
		?>
			<div class="prdctfltr_filter_wrapper<?php echo $curr_columns_class; ?>">
				<div class="prdctfltr_filter_inner">
				<?php

					foreach ( $curr_elements as $k => $v ) :
				
						switch ( $v ) :

						case 'sort' : ?>

							<div class="prdctfltr_filter prdctfltr_orderby">
								<input name="orderby" type="hidden"<?php echo ( isset($_GET['orderby'] ) ? ' value="'.$_GET['orderby'].'"' : '' );?>>
								<?php echo $before_title; ?>
								<?php _e('Sort by', 'prdctfltr'); ?>
								<span class="prdctfltr_widget_title">
									<?php
										if ( isset($_GET['orderby'] ) ) {
											echo ' / <span>'.$catalog_orderby[$_GET['orderby']] . '</span> <a href="#" data-key="prdctfltr_orderby"><i class="prdctfltr-delete"></i></a>';
										}
									?>
									<i class="prdctfltr-down"></i>
								</span>
								<?php echo $after_title; ?>
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
							<?php echo $before_title; ?>
							<?php _e('Price range', 'prdctfltr'); ?>
							<span class="prdctfltr_widget_title">
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
								?>
								<i class="prdctfltr-down"></i>
							</span>
							<?php echo $after_title; ?>
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
							<?php echo $before_title; ?>
							<?php _e('Categories', 'prdctfltr'); ?>
							<span class="prdctfltr_widget_title">
								<?php
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
								?>
								<i class="prdctfltr-down"></i>
							</span>
							<?php echo $after_title; ?>
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
							<?php echo $before_title; ?>
							<?php _e('Tags', 'prdctfltr'); ?>
							<span class="prdctfltr_widget_title">
								<?php
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
								?>
								<i class="prdctfltr-down"></i>
							</span>
							<?php echo $after_title; ?>
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
							<?php echo $before_title; ?>
							<?php _e('Characteristics', 'prdctfltr'); ?>
							<span class="prdctfltr_widget_title">
								<?php
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
								?>
								<i class="prdctfltr-down"></i>
							</span>
							<?php echo $after_title; ?>
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
							<?php echo $before_title; ?>
							<?php echo $curr_term->label; ?>
							<span class="prdctfltr_widget_title">
								<?php
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
								?>
								<i class="prdctfltr-down"></i>
							</span>
							<?php echo $after_title; ?>
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

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['preset'] =  $new_instance['preset'];

		return $instance;
	}

	function form( $instance ) {
		$vars = array( 'preset' => '' );
		$instance = wp_parse_args( (array) $instance, $vars );

		$preset = strip_tags($instance['preset']);

?>
		<div>
			<p class="prdctfltr-box">
			<label for="<?php echo $this->get_field_id('preset'); ?>" class="prdctfltr-label"><?php _e('Preset:'); ?></label>
			<select name="<?php echo $this->get_field_name('preset'); ?>" id="<?php echo $this->get_field_id('preset'); ?>">
				<option value="pf_default_inline"<?php echo ( $preset == 'pf_default_inline' ? ' selected="selected"' : '' ); ?>><?php _e('Flat Inline', 'prdctfltr'); ?></option>
				<option value="pf_default"<?php echo ( $preset == 'pf_default' ? ' selected="selected"' : '' ); ?>><?php _e('Flat Block', 'prdctfltr'); ?></option>
				<option value="pf_default_select"<?php echo ( $preset == 'pf_default_select' ? ' selected="selected"' : '' ); ?>><?php _e('Flat Select', 'prdctfltr'); ?></option>
			</select>
			</p>
		</div>

<?php
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("prdctfltr");' ) );


?>