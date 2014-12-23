<?php
add_theme_support('nav-menus');
add_action('init','register_my_menus');

function register_my_menus() {
	register_nav_menus(
		array(
			'nav-menu' => __( 'Navigation Menu',THB_THEME_NAME ),
			'acc-menu' => __( 'Account Menu',THB_THEME_NAME ),
			'mobile-menu' => __( 'Mobile Menu',THB_THEME_NAME ),
			'mobile-secondary-menu' => __( 'Mobile Secondary Menu',THB_THEME_NAME )
		)
	);
}

/* CUSTOM NAV WALKER */
class thb_navDropdown extends Walker_Nav_Menu
{
	
    function start_lvl( &$output, $depth = 0, $args = array() ) {
        $display_depth = ($depth + 1);
        if($display_depth == '1'){$class_names = 'dropdown';}
        else {$class_names = 'dropdown-column';}
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<div class=".$class_names."><ul>\n";
    }

    function end_lvl( &$output, $depth = 1, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul></div>\n";
    }

    function start_el ( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

	    global $wp_query;
	    $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
	
	    $class_names = $value = '';
	
	    $classes = empty( $item->classes ) ? array() : (array) $item->classes;
	    $classes[] = 'menu-item-' . $item->ID;
	
	    $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
	    $class_names = ' class="' . esc_attr( $class_names ) . '"';
	
	    $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
	    $id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';
	
	    $output .= $indent . '<li' . $id . $value . $class_names .'>';
	
	    $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
	    $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
	    $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
	    $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
	
			$heading = '';
	    if ( $depth == 0 ) {
	        if(strpos($class_names,'heading') !== false && $item->description !== ''){$heading = '<h6>'.$item->description.'</h6>';}
	    }
	
	    $description = '';
	    if(strpos($class_names,'image') !== false){$description = '<img src="'.$item->description.'" alt=" "/>';}
	

		    $item_output = $args->before;
		    $item_output .= '<a'. $attributes .'>';
		    $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		    $item_output .= $heading.$description;
		    $item_output .= '</a>';
		    $item_output .= $args->after;
	
	    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  	} 

}
class thb_mobileDropdown extends Walker_Nav_Menu {

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;
		
		if($depth === 0) {
			$classes[] = 'animation right-to-left animate';
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class=" ' . esc_attr( $class_names ) . '"' : '';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names .'>';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}
		$children = get_posts(array('post_type' => 'nav_menu_item', 'nopaging' => true, 'numberposts' => 1, 'meta_key' => '_menu_item_menu_item_parent', 'meta_value' => $item->ID));
		$icon = (!empty($children) ? 'icon-budicon-559' : 'icon-budicon-575');
		$item_output = $args->before;
		if ($depth == 0) {
			$item_output .= '<a'. $attributes .'>'. (!empty($children) ? '<span><i class="fa fa-caret-right"></i></span>' : '');
		} else {
			$item_output .= '<a'. $attributes .'>';
		}
		
		/** This filter is documented in wp-includes/post-template.php */
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;


		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}

/* Mega Menu */
class rc_thb_custom_menu {

    /*--------------------------------------------*
     * Constructor
     *--------------------------------------------*/

    /**
     * Initializes the plugin by setting localization, filters, and administration functions.
     */
    function __construct() {

        
        // add custom menu fields to menu
        add_filter( 'wp_setup_nav_menu_item', array( $this, 'rc_scm_add_custom_nav_fields' ) );

        // save menu custom fields
        add_action( 'wp_update_nav_menu_item', array( $this, 'rc_scm_update_custom_nav_fields'), 10, 3 );
        
        // edit menu walker
        add_filter( 'wp_edit_nav_menu_walker', array( $this, 'rc_scm_edit_walker'), 10, 2 );

    } // end constructor
    

    /**
     * Add custom fields to $item nav object
     * in order to be used in custom Walker
     *
     * @access      public
     * @since       1.0 
     * @return      void
    */
    function rc_scm_add_custom_nav_fields( $menu_item ) {
    
        $menu_item->menuicon = get_post_meta( $menu_item->ID, '_menu_item_menuicon', true );
        $menu_item->megamenu = get_post_meta( $menu_item->ID, '_menu_item_megamenu', true );
        return $menu_item;
        
    }
    
    /**
     * Save menu custom fields
     *
     * @access      public
     * @since       1.0 
     * @return      void
    */
    function rc_scm_update_custom_nav_fields( $menu_id, $menu_item_db_id, $args ) {
    
        // Check if element is properly sent

        if (!empty($_REQUEST['edit-menu-item-menuicon']) && is_array( $_REQUEST['edit-menu-item-menuicon']) ) {
            $menu_icon_value = $_REQUEST['edit-menu-item-menuicon'][$menu_item_db_id];
            update_post_meta( $menu_item_db_id, '_menu_item_menuicon', $menu_icon_value );
        }

        if (!isset($_REQUEST['edit-menu-item-megamenu'][$menu_item_db_id])) {
            $_REQUEST['edit-menu-item-megamenu'][$menu_item_db_id] = '';
            
        }
        $menu_mega_enabled_value = $_REQUEST['edit-menu-item-megamenu'][$menu_item_db_id];        
        update_post_meta( $menu_item_db_id, '_menu_item_megamenu', $menu_mega_enabled_value );
    }
    
    /**
     * Define new Walker edit
     *
     * @access      public
     * @since       1.0 
     * @return      void
    */
    function rc_scm_edit_walker($walker,$menu_id) {
    
        return 'Walker_Nav_Menu_Edit_Custom'; 
    }
}

// instantiate plugin's class
$GLOBALS['thb_custom_menu'] = new rc_thb_custom_menu();








/**
 *  /!\ This is a copy of Walker_Nav_Menu_Edit class in core
 * 
 * Create HTML list of nav menu input items.
 *
 * @package WordPress
 * @since 3.0.0
 * @uses Walker_Nav_Menu
 */
class Walker_Nav_Menu_Edit_Custom extends Walker_Nav_Menu  {
        /**
         * @see Walker::$tree_type
         * @var string
         */
        var $tree_type = array( 'post_type', 'taxonomy', 'custom' );
    
        /**
         * @see Walker::$db_fields
         * @todo Decouple this.
         * @var array
         */
        var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );
    

        /**
         * @see Walker_Nav_Menu::start_lvl()
         * @since 3.0.0
         *
         * @param string $output Passed by reference.
         */
        function start_lvl(&$output, $depth = 0, $args = array()) {  
        }
        
        /**
         * @see Walker_Nav_Menu::end_lvl()
         * @since 3.0.0
         *
         * @param string $output Passed by reference.
         */
        function end_lvl(&$output, $depth = 0, $args = array()) {
        }
    /**
     * @see Walker::start_el()
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item Menu item data object.
     * @param int $depth Depth of menu item. Used for padding.
     * @param object $args
     */
    function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id = 0) {
        global $_wp_nav_menu_max_depth;
       
        $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;
    
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
    
        ob_start();
        $item_id = esc_attr( $item->ID );
        $removed_args = array(
            'action',
            'customlink-tab',
            'edit-menu-item',
            'menu-item',
            'page-tab',
            '_wpnonce',
        );


    
        $original_title = '';
        if ( 'taxonomy' == $item->type ) {
            $original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
            if ( is_wp_error( $original_title ) )
                $original_title = false;
        } elseif ( 'post_type' == $item->type ) {
            $original_object = get_post( $item->object_id );
            $original_title = $original_object->post_title;
        }
    
        $classes = array(
            'menu-item menu-item-depth-' . $depth,
            'menu-item-' . esc_attr( $item->object ),
            'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
        );
    
        $title = $item->title;
    
        if ( ! empty( $item->_invalid ) ) {
            $classes[] = 'menu-item-invalid';
            /* translators: %s: title of menu item which is invalid */
            $title = sprintf( __( '%s (Invalid)', THB_THEME_NAME), $item->title );
        } elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
            $classes[] = 'pending';
            /* translators: %s: title of menu item in draft status */
            $title = sprintf( __('%s (Pending)', THB_THEME_NAME), $item->title );
        }
    
        $title = empty( $item->label ) ? $title : $item->label;
       
        ?>
        <li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes ); ?>">
            <dl class="menu-item-bar">
                <dt class="menu-item-handle">
                    <span class="item-title"><?php echo esc_html( $title ); ?></span>
                    <span class="item-controls">
                        <span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
                        <span class="item-order hide-if-js">
                            <a href="<?php
                                echo wp_nonce_url(
                                    add_query_arg(
                                        array(
                                            'action' => 'move-up-menu-item',
                                            'menu-item' => $item_id,
                                        ),
                                        remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                                    ),
                                    'move-menu_item'
                                );
                            ?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up', THB_THEME_NAME); ?>">&#8593;</abbr></a>
                            |
                            <a href="<?php
                                echo wp_nonce_url(
                                    add_query_arg(
                                        array(
                                            'action' => 'move-down-menu-item',
                                            'menu-item' => $item_id,
                                        ),
                                        remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                                    ),
                                    'move-menu_item'
                                );
                            ?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down',THB_THEME_NAME); ?>">&#8595;</abbr></a>
                        </span>
                        <a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php esc_attr_e('Edit Menu Item', THB_THEME_NAME); ?>" href="<?php
                            echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
                        ?>"><?php _e( 'Edit Menu Item', THB_THEME_NAME); ?></a>
                    </span>
                </dt>
            </dl>
    
            <div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
                <?php if( 'custom' == $item->type ) : ?>
                    <p class="field-url description description-wide">
                        <label for="edit-menu-item-url-<?php echo $item_id; ?>">
                            <?php _e( 'URL', THB_THEME_NAME ); ?><br />
                            <input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
                        </label>
                    </p>
                <?php endif; ?>
                <p class="description description-thin">
                    <label for="edit-menu-item-title-<?php echo $item_id; ?>">
                        <?php _e( 'Navigation Label', THB_THEME_NAME ); ?><br />
                        <input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
                    </label>
                </p>
                <p class="description description-thin">
                    <label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
                        <?php _e( 'Title Attribute', THB_THEME_NAME ); ?><br />
                        <input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
                    </label>
                </p>
                <p class="field-link-target description">
                    <label for="edit-menu-item-target-<?php echo $item_id; ?>">
                        <input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank" name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
                        <?php _e( 'Open link in a new window/tab', THB_THEME_NAME ); ?>
                    </label>
                </p>
                <p class="field-css-classes description description-thin">
                    <label for="edit-menu-item-classes-<?php echo $item_id; ?>">
                        <?php _e( 'CSS Classes (optional)', THB_THEME_NAME ); ?><br />
                        <input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
                    </label>
                </p>
                <p class="field-xfn description description-thin">
                    <label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
                        <?php _e( 'Link Relationship (XFN)', THB_THEME_NAME  ); ?><br />
                        <input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
                    </label>
                </p>
                <p class="field-description description description-wide">
                    <label for="edit-menu-item-description-<?php echo $item_id; ?>">
                        <?php _e( 'Description', THB_THEME_NAME ); ?><br />
                        <textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
                        <span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.', THB_THEME_NAME); ?></span>
                    </label>
                </p>
				<?php do_action( 'wp_nav_menu_item_custom_fields', $item_id, $item, $depth, $args );?>

                <div class="clearboth"></div>
                <div class="thb_menu_options">
                	<h2><?php _e('Fuel Themes Menu Options', THB_THEME_NAME); ?></h2>   
	                <p class="field-link-mega">
	                  <h3><?php _e( 'Menu Item Icon', THB_THEME_NAME ); ?></h3>
	                  <?php $saved = get_post_meta( $item_id, '_menu_item_menuicon', true);?>
	                  <select id="edit-menu-item-menuicon-<?php echo $item_id; ?>" name="edit-menu-item-menuicon[<?php echo $item_id; ?>]">
	                  	
	                  	<?php foreach (thb_getIconArray() as $key => $value) { ?>
	                  		<?php $selected = ($value === $saved ? " selected" : ""); ?>
	                  		<option value="<?php echo $key; ?>"<?php echo $selected; ?>><?php echo $value; ?></option>
	                  	<?php } ?>
	                  </select>
	                </p>

	            	</div>
           

                <div class="menu-item-actions description-wide submitbox">
                    <?php if( 'custom' != $item->type && $original_title !== false ) : ?>
                        <p class="link-to-original">
                            <?php printf( __('Original: %s', THB_THEME_NAME), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
                        </p>
                    <?php endif; ?>
                    <a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
                    echo wp_nonce_url(
                        add_query_arg(
                            array(
                                'action' => 'delete-menu-item',
                                'menu-item' => $item_id,
                            ),
                            remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                        ),
                        'delete-menu_item_' . $item_id
                    ); ?>"><?php _e('Remove', THB_THEME_NAME ); ?></a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-<?php echo $item_id; ?>" href="<?php echo esc_url( add_query_arg( array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) ) );
                        ?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Cancel', THB_THEME_NAME); ?></a>
                </div>
    
                <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
                <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
                <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
                <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
                <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
                <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
            </div><!-- .menu-item-settings-->
            <ul class="menu-item-transport"></ul>
        <?php
        
        $output .= ob_get_clean();

        }
}




/**
 * Custom Walker
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
class thb_MegaMenu extends Walker_Nav_Menu {
  /**
   * @var int $columns 
   */
  var $columns = 0 ;
  var $max_columns = 0;
  var $active_megamenu = 0;
  var $rows = 1;
  var $rowsCounter = array();



  /**
   * @see Walker::start_lvl()
   *
   * @param string $output Passed by reference. Used to append additional content.
   * @param int $depth Depth of page. Used for padding.
   */
  function start_lvl(&$output, $depth = 0, $args = array()) {
      $indent = str_repeat("\t", $depth);
      if($depth === 0) $output .= "\n{replace_one}\n";
      $output .= "\n$indent<ul class=\"sub-menu ".(($depth === 0) ? "{locate_class}": "")."\">\n";
  }

  /**
   * @see Walker::end_lvl()
   *
   * @param string $output Passed by reference. Used to append additional content.
   * @param int $depth Depth of page. Used for padding.
   */
  function end_lvl(&$output, $depth = 0, $args = array()) {
      $indent = str_repeat("\t", $depth);
      $output .= "$indent</ul>\n";
      
      if($depth === 0) 
      {
          if($this->active_megamenu)
          {
							$output .= "\n</div>\n";
							$output = str_replace("{replace_one}", "<div class='thb_mega_menu_holder'>", $output);
							
              $output = str_replace("{locate_class}", "thb_mega_menu row", $output);
              
              foreach($this->rowsCounter as $row => $columns)
              {
              		$columns = 12 / $columns;
              		
              		if ($columns == 2.4) { $col = 'thb-five columns'; } else { $col = 'small-'.$columns.' columns';}
                  $output = str_replace("{current_row_".$row."}", $col, $output);
              }
              
              $this->columns = 0;
              $this->max_columns = 0;
              $this->rowsCounter = array();
              
          }
          else
          {
              $output = str_replace("{locate_class}", "", $output);
              $output = str_replace("{replace_one}", "", $output);
          }
      }
  }    

  function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id = 0) {
      global $wp_query;
      
			
      
      $item_output = $li_text_block_class = $column_class = "";
      
      $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
      $class_names = $value = '';

      $classes = empty( $item->classes ) ? array() : (array) $item->classes;

      $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
      
      if($depth === 0)
      {   
          $this->active_megamenu = get_post_meta( $item->ID, '_menu_item_megamenu', true);
          
      }
      
      
      if($depth === 1 && $this->active_megamenu)
      {
      		
          $this->columns ++;

          $this->rowsCounter[$this->rows] = $this->columns;
          
          if($this->max_columns < $this->columns) $this->max_columns = $this->columns;
          
          $title = apply_filters( 'the_title', $item->title, $item->ID );
					
					
					
          if($title != "-" && $title != '"-"')
          {
              $menu_icon_tag  = ! empty( $item->menuicon ) ? '<i class="fa '.esc_attr( $item->menuicon ).'"></i>' : '';
              $attributes = ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';      
          
              $item_output .= $args->before;
              $item_output .= '<div class="megamenu-title"'. $attributes .'>';
              $item_output .= $menu_icon_tag;
              $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ). $args->link_after;
              $item_output .= '</div>';
              $item_output .= $args->after;
          }
          
          $column_class  = ' {current_row_'.$this->rows.'}';
      }
      else
      {
          $menu_icon_tag  = ! empty( $item->menuicon ) ? '<i class="fa '.esc_attr( $item->menuicon ).'"></i>' : '';
          $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
          $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
          $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
          $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';            
      		
      		$description = '';
      		if(strpos($class_names,'image') !== false){$description = '<img src="'.$item->description.'" alt="'.$item->title.'"/>';}
      		
          $item_output .= $args->before;
          $item_output .= '<a'. $attributes .'>';
          $item_output .= $menu_icon_tag;
          $item_output .= $args->link_before . ($description !== '' ? $description : apply_filters( 'the_title', $item->title, $item->ID ) ). $args->link_after;
          $item_output .= '</a>';
          $item_output .= $args->after;
      }
      
      
      
      if($depth === 0 && $this->active_megamenu) $class_names .= " menu-item-mega-parent ";
      $class_names = ' class="'.$li_text_block_class. esc_attr( $class_names ) . $column_class.'"';

      $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
      
      
      
      
      $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }
}