<?php
$vc_is_wp_version_3_6_more = version_compare(preg_replace('/^([\d\.]+)(\-.*$)/', '$1', get_bloginfo('version')), '3.6') >= 0;

// Shortcodes 
$shortcodes = THB_THEME_ROOT_ABS.'/vc_templates/';
$files = glob($shortcodes.'/thb_?*.php');
foreach ($files as $filename)
{
	require_once($shortcodes.basename($filename));
}

/* Visual Composer Mappings */

// Adding animation to columns
vc_add_param("vc_column", array(
	"type" => "dropdown",
	"class" => "",
	"heading" => "Animation",
	"admin_label" => true,
	"param_name" => "animation",
	"value" => array(
		"None" => "",
		"Left" => "animation right-to-left",
		"Right" => "animation left-to-right",
		"Top" => "animation bottom-to-top",
		"Bottom" => "animation top-to-bottom",
		"Scale" => "animation scale",
		"Fade" => "animation fade-in"
	),
	"description" => ""
));

vc_add_param("vc_column_inner", array(
	"type" => "dropdown",
	"class" => "",
	"heading" => "Animation",
	"admin_label" => true,
	"param_name" => "animation",
	"value" => array(
		"None" => "",
		"Left" => "animation right-to-left",
		"Right" => "animation left-to-right",
		"Top" => "animation bottom-to-top",
		"Bottom" => "animation top-to-bottom",
		"Scale" => "animation scale",
		"Fade" => "animation fade-in"
	),
	"description" => ""
));

// Add parameters to rows
vc_add_param("vc_row", array(
	"type" => "textfield",
	"class" => "",
	"heading" => "ID",
	"param_name" => "row_id",
	"value" => "",
	"description" => "The ID of this row. Remember, you should always use a unique ID for each row. This is used for One Page Navigation."
));
vc_add_param("vc_row", array(
	"type" => "dropdown",
	"class" => "",
	"heading" => "Type",
	"param_name" => "type",
	"value" => array(
		"In Container" => "in_container",
		"Full Width Background" => "full_width_background",
		"Full Width Content" => "full_width_content"		
	)
));
vc_add_param("vc_row", array(
	"type" => "checkbox",
	"class" => "",
	"heading" => "Disable Column Padding",
	"param_name" => "column_padding",
	"value" => array(
		"" => "false"
	),
	"description" => "You can have columns without spaces using this option"
));
vc_add_param("vc_row", array(
	"type" => "checkbox",
	"class" => "",
	"heading" => "Equal-height Columns",
	"param_name" => "equal_height",
	"value" => array(
		"" => "true"
	),
	"description" => "You can have columns with same height using this option"
));
vc_add_param("vc_row", array(
	"type" => "checkbox",
	"class" => "",
	"heading" => "Full Height Row",
	"param_name" => "full_height",
	"value" => array(
		"" => "true"
	),
	"description" => "If enabled, this will cause this row to always fill the height of the window."
));
vc_add_param("vc_row_inner", array(
	"type" => "checkbox",
	"class" => "",
	"heading" => "Disable Column Padding",
	"param_name" => "column_padding",
	"value" => array(
		"" => "false"
	),
	"description" => "You can have columns without spaces using this option"
));
vc_add_param("vc_row_inner", array(
	"type" => "checkbox",
	"class" => "",
	"heading" => "Equal-height Columns",
	"param_name" => "equal_height",
	"value" => array(
		"" => "true"
	),
	"description" => "You can have columns with same height using this option"
));
vc_add_param("vc_row", array(
	"type" => "checkbox",
	"class" => "",
	"heading" => "Enable parallax",
	"param_name" => "enable_parallax",
	"value" => array(
		"" => "false"
	)
));
vc_add_param("vc_row", array(
	"type" => "textfield",
	"class" => "",
	"heading" => "Parallax Speed",
	"param_name" => "parallax_speed",
	"value" => "1",
	"dependency" => array(
		"element" => "enable_parallax",
		"not_empty" => true
	),
	"description" => "A value between 1 and 10 is recommended. Larger values are slower."
));
vc_add_param("vc_row", array(
	"type" => "dropdown",
	"class" => "",
	"heading" => "Parallax Direction",
	"param_name" => "parallax_direction",
	"value" => array(
		"Up" => "up",
		"Down" => "down"
	),
	"dependency" => array(
		"element" => "enable_parallax",
		"not_empty" => true
	),
	"description" => "Do you want the image to move up or down?"
));
vc_add_param("vc_row", array(
	"type" => "textfield",
	"class" => "",
	"heading" => "Video background (mp4)",
	"param_name" => "bg_video_src_mp4",
	"value" => "",
	"description" => "You must include the ogv & the mp4 format to render your video with cross browser compatibility. OGV is optional. Video must be in a 16:9 aspect ratio. The row background image will be used in mobile devices."
));
vc_add_param("vc_row", array(
	"type" => "textfield",
	"class" => "",
	"heading" => "Video background (ogv)",
	"param_name" => "bg_video_src_ogv",
	"value" => ""
));
vc_add_param("vc_row", array(
	"type" => "textfield",
	"class" => "",
	"heading" => "Video background (webm)",
	"param_name" => "bg_video_src_webm",
	"value" => ""
));
vc_add_param("vc_row", array(
	"type" => "colorpicker",
	"class" => "",
	"heading" => "Video Overlay Color",
	"param_name" => "bg_video_overlay_color",
	"value" => "",
	"description" => "If you want, you can select an overlay color."
));

// Banner shortcode
vc_map( array(
	"name" => __("Banner", THB_THEME_NAME),
	"base" => "thb_banner",
	"icon" => "thb_vc_ico_banner",
	"class" => "thb_vc_sc_banner",
	"category" => "by Fuel Themes",
	"params" => array(
		array(
			"type" => "attach_image", //attach_images
			"class" => "",
			"heading" => "Select Background Image",
			"param_name" => "banner_bg",
			"description" => ""
		),
		array(
		  "type" => "textfield",
		  "heading" => "Banner Height",
		  "param_name" => "banner_height",
		  "description" => "Enter height of the banner in px."
		),
		
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Banner Effect",
			"param_name" => "type",
			"value" => array(
				"Lily" => "effect-lily",
				"Sadie" => "effect-sadie",
				"Honey" => "effect-honey",
				"layla" => "effect-layla",
				"Marley" => "effect-marley",
				"Ruby" => "effect-ruby",
				"Roxy" => "effect-roxy",
				"Bubba" => "effect-bubba",
				"Romeo" => "effect-romeo",
				"Dexter" => "effect-dexter",
				"Sarah" => "effect-sarah",
				"Chico" => "effect-chico",
				"Milo" => "effect-milo"
			),
			"description" => "You can see the effects here: http://themes.fuelthemes.net/skillful/banners/"
		),

		array(
		  "type" => "textfield",
		  "heading" => "Title",
		  "param_name" => "title",
		  "admin_label" => true,
		),
		array(
		  "type" => "textfield",
		  "heading" => "Sub Title",
		  "param_name" => "subtitle"
		),
		array(
		  "type" => "textfield",
		  "heading" => "Link",
		  "param_name" => "overlay_link"
		)
	),
	"description" => "Display different banner styles"
) );

// Button shortcode
vc_map( array(
	"name" => __("Button", THB_THEME_NAME),
	"base" => "thb_button",
	"icon" => "thb_vc_ico_button",
	"class" => "thb_vc_sc_button",
	"category" => "by Fuel Themes",
	"params" => array(
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => "Caption",
			"admin_label" => true,
			"param_name" => "content",
			"value" => "",
			"description" => ""
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => "Link URL",
			"param_name" => "link",
			"value" => "",
			"description" => ""
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Icon",
			"param_name" => "icon",
			"value" => thb_getIconArray(),
			"description" => ""
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Open link in",
			"param_name" => "target_blank",
			"value" => array(
				"Same window" => "",
				"New window" => "true"
			),
			"description" => ""
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Size",
			"param_name" => "size",
			"value" => array(
				"Small button" => "small",
				"Medium button" => "medium",
				"Big button" => "large"
			),
			"description" => ""
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Button color",
			"param_name" => "color",
			"value" => array(
				"Accent Color" => "accent",
				"Black" => "black",
				"White" => "white"
			),
			"description" => ""
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Animation",
			"param_name" => "animation",
			"value" => array(
				"None" => "",
				"Left" => "animation right-to-left",
				"Right" => "animation left-to-right",
				"Top" => "animation bottom-to-top",
				"Bottom" => "animation top-to-bottom",
				"Scale" => "animation scale",
				"Fade" => "animation fade-in"
			),
			"description" => ""
		)
	),
	"description" => "Add an animated button"
) );

// Divider Shortcode
vc_map( array(
	"name" => __("Dividers", THB_THEME_NAME),
	"base" => "thb_dividers",
	"icon" => "thb_vc_ico_dividers",
	"class" => "thb_vc_sc_dividers",
	"category" => "by Fuel Themes",
	"show_settings_on_create" => true,
	"params" => array(
		array(
		    "type" => "dropdown",
		    "heading" => "Style",
		    "param_name" => "style",
		    "admin_label" => true,
		    "value" => array(
		    	'Style 1' => "style1",
		    	'Style 2' => "style2",
		    	'Style 3' => "style3",
		    	'Style 4' => "style4",
		    	'Style 5' => "style5",
		    	'Style 6' => "style6",
		    	'Style 7' => "style7",
		    	'Style 8' => "style8"
		    ),
		    "description" => "This changes the style of the dividers"
		),
	),
	"description" => "Divide your content with different divider styles."
) );

// Gap shortcode
vc_map( array(
	"name" => __("Gap", THB_THEME_NAME),
	"base" => "thb_gap",
	"icon" => "thb_vc_ico_gap",
	"class" => "thb_vc_sc_gap",
	"category" => "by Fuel Themes",
	"params" => array(
		array(
		  "type" => "textfield",
		  "heading" => "Gap Height",
		  "param_name" => "height",
		  "admin_label" => true,
		  "description" => "Enter height of the gap in px."
		)
	),
	"description" => "Add a gap to seperate elements"
) );

// Icon List shortcode
vc_map( array(
	"name" => __("Icon List", THB_THEME_NAME),
	"base" => "thb_iconlist",
	"icon" => "thb_vc_ico_iconlist",
	"class" => "thb_vc_sc_iconlist",
	"category" => "by Fuel Themes",
	"params" => array(
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Icon",
			"param_name" => "icon",
			"value" => thb_getIconArray(),
			"description" => ""
		),
		array(
			"type" => "colorpicker",
			"class" => "",
			"heading" => "Icon color",
			"param_name" => "color",
			"value" => "",
			"description" => ""
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Animation",
			"param_name" => "animation",
			"value" => array(
				"None" => "",
				"Left" => "animation right-to-left",
				"Right" => "animation left-to-right",
				"Top" => "animation bottom-to-top",
				"Bottom" => "animation top-to-bottom",
				"Scale" => "animation scale",
				"Fade" => "animation fade-in"
			),
			"description" => ""
		),
		array(
			"type" => "exploded_textarea",
			"class" => "",
			"heading" => "List Items",
			"admin_label" => true,
			"param_name" => "content",
			"value" => "",
			"description" => "Every new line will be treated as a list item"
		)
	),
	"description" => "Add lists with icons"
) );

// Iconbox shortcode
vc_map( array(
	"name" => __("Iconbox", THB_THEME_NAME),
	"base" => "thb_iconbox",
	"icon" => "thb_vc_ico_iconbox",
	"class" => "thb_vc_sc_iconbox",
	"category" => "by Fuel Themes",
	"params" => array(
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Icon",
			"param_name" => "icon",
			"value" => thb_getIconArray(),
			"description" => ""
		),
		array(
		  "type"              => "colorpicker",
		  "holder"            => "div",
		  "class"             => "",
		  "heading"           => "Icon Color",
		  "param_name"        => "icon_color",
		  "description"       => ""
		),
		array(
			"type" => "attach_image", //attach_images
			"class" => "",
			"heading" => "Image",
			"param_name" => "image",
			"description" => "Use image instead of icon? Image uploaded should be 100*100"
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => "Heading",
			"param_name" => "heading",
			"value" => "",
			"admin_label" => true,
			"description" => ""
		),
		array(
			"type" => "colorpicker",
			"class" => "",
			"heading" => "Heading Color",
			"param_name" => "heading_color",
			"value" => "",
			"description" => "You can change the heading color from here"
		),
		array(
			"type" => "textarea",
			"class" => "",
			"heading" => "Content",
			"param_name" => "content",
			"value" => "",
			"description" => ""
		),
		array(
		  "type"              => "colorpicker",
		  "holder"            => "div",
		  "class"             => "",
		  "heading"           => "Content Color",
		  "param_name"        => "content_color",
		  "description"       => ""
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Animation",
			"param_name" => "animation",
			"value" => array(
				"None" => "",
				"Left" => "animation right-to-left",
				"Right" => "animation left-to-right",
				"Top" => "animation bottom-to-top",
				"Bottom" => "animation top-to-bottom",
				"Scale" => "animation scale",
				"Fade" => "animation fade-in"
			),
			"description" => ""
		),
		array(
			"type" => "checkbox",
			"class" => "",
			"heading" => "Add Button?",
			"param_name" => "use_btn",
			"value" => array(
				"" => "true"
			),
			"description" => "Check if you want to add a button."
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => "Button Caption",
			"param_name" => "btn_content",
			"value" => "",
			"description" => "",
			"dependency" => Array('element' => "use_btn", 'not_empty' => true)
		),
		array(
			"type" => "textfield",
			"class" => "",
			"heading" => "Button Link URL",
			"param_name" => "btn_link",
			"value" => "",
			"description" => "",
			"dependency" => Array('element' => "use_btn", 'not_empty' => true)
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Button Icon",
			"param_name" => "btn_icon",
			"value" => thb_getIconArray(),
			"description" => "",
			"dependency" => Array('element' => "use_btn", 'not_empty' => true)
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Button Open link in",
			"param_name" => "btn_target_blank",
			"value" => array(
				"Same window" => "",
				"New window" => "true"
			),
			"description" => "",
			"dependency" => Array('element' => "use_btn", 'not_empty' => true)
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Button Size",
			"param_name" => "btn_size",
			"value" => array(
				"Small button" => "small",
				"Medium button" => "medium",
				"Big button" => "big"
			),
			"description" => "",
			"dependency" => Array('element' => "use_btn", 'not_empty' => true)
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Button Style",
			"param_name" => "btn_style",
			"value" => array(
				"Fill" => "",
				"Outline" => "outline"
			),
			"description" => "",
			"dependency" => Array('element' => "use_btn", 'not_empty' => true)
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Button color",
			"param_name" => "btn_color",
			"value" => array(
				"Accent" => "accent",
				"Black" => "black",
				"White" => "white"
			),
			"description" => "",
			"dependency" => Array('element' => "use_btn", 'not_empty' => true)
		)
	),
	"description" => "Iconboxes with different animations"
) );

// Image shortcode
vc_map( array(
	"name" => "Image",
	"base" => "thb_image",
	"icon" => "thb_vc_ico_image",
	"class" => "thb_vc_sc_image",
	"category" => "by Fuel Themes",
	"params" => array(
		array(
			"type" => "attach_image", //attach_images
			"class" => "",
			"heading" => "Select Image",
			"param_name" => "image",
			"description" => ""
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Animation",
			"param_name" => "animation",
			"value" => array(
				"None" => "",
				"Left" => "animation right-to-left",
				"Right" => "animation left-to-right",
				"Top" => "animation bottom-to-top",
				"Bottom" => "animation top-to-bottom",
				"Scale" => "animation scale",
				"Fade" => "animation fade-in"
			),
			"description" => ""
		),
		array(
		  "type" => "textfield",
		  "heading" => "Image size",
		  "param_name" => "img_size",
		  "description" => "Enter image size. Example: thumbnail, medium, large, full or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use 'thumbnail' size."
		),
		array(
		  "type" => "dropdown",
		  "heading" => "Image alignment",
		  "param_name" => "alignment",
		  "value" => array("Align left" => "left", "Align right" => "right", "Align center" => "center"),
		  "description" => "Select image alignment."
		),
		array(
			"type" => "checkbox",
			"class" => "",
			"heading" => "Link to Full-Width Image?",
			"param_name" => "lightbox",
			"value" => array(
				"" => "true"
			)
		),
		array(
		  "type" => "textfield",
		  "heading" => "Image link",
		  "param_name" => "img_link",
		  "description" => "Enter url if you want this image to have link.",
		  "dependency" => Array('element' => "lightbox", 'is_empty' => true)
		),
		array(
		  "type" => "dropdown",
		  "heading" => "Link Target",
		  "param_name" => "img_link_target",
		  "value" => array(
		  	"Same window" => "",
		  	"New window" => "true"
		  ),
		  "dependency" => Array('element' => "lightbox", 'is_empty' => true)
		)
	),
	"description" => "Add an animated image"
) );

// Image Slider
vc_map( array(
	"name" => __("Image Slider", THB_THEME_NAME),
	"base" => "thb_slider",
	"icon" => "thb_vc_ico_slider",
	"class" => "thb_vc_sc_slider",
	"category" => "by Fuel Themes",
	"params" => array(
		array(
			"type" => "attach_images", //attach_images
			"class" => "",
			"heading" => "Select Images",
			"param_name" => "images",
			"admin_label" => true,
			"description" => ""
		),
		array(
		  "type" => "textfield",
		  "heading" => "Width",
		  "param_name" => "width",
		  "description" => "Enter the width of the images. The slider will fill the width of the container, so make sure you size the columns accordingly."
		),
		array(
		  "type" => "textfield",
		  "heading" => "Height",
		  "param_name" => "height",
		  "description" => "Enter the height of the images."
		),
		array(
			"type" => "checkbox",
			"class" => "",
			"heading" => "Navigation Arrows",
			"param_name" => "navigation",
			"value" => array(
				"" => "true"
			),
			"description" => "Check this if you want to show navigation arrows."
		)
	),
	"description" => "Add an image slider"
) );

// Products
vc_map( array(
	"name" => __("Instagram", THB_THEME_NAME),
	"base" => "thb_instagram",
	"icon" => "thb_vc_ico_instagram",
	"class" => "thb_vc_sc_instagram",
	"category" => "by Fuel Themes",
	"params"	=> array(
	  
	  array(
	      "type" => "textfield",
	      "heading" => "Username",
	      "param_name" => "username",
	      "description" => "Instagram Username"
	  ),
	  array(
	      "type" => "textfield",
	      "heading" => "Number of Photos",
	      "param_name" => "number",
	      "description" => "Number of Instagram Photos to retrieve"
	  ),
		array(
			"type" => "dropdown",
			"heading" => "Columns",
			"param_name" => "columns",
			"value" => array(
				'Six Columns' => "6",
				'Five Columns' => "5",
				'Four Columns' => "4",
				'Three Columns' => "3",
				'Two Columns' => "2"
			)
		),
	  array(
	      "type" => "checkbox",
	      "heading" => "Link Photos to Instagram?",
	      "param_name" => "link",
	      "value" => array(
				"" => "true"
			),
	      "description" => "Do you want to link the Instagram photos to instagram.com website?"
	  )
	),
	"description" => "Add Instagram Photos"
) );

// Notification shortcode
vc_map( array(
	"name" => __("Notification", THB_THEME_NAME),
	"base" => "thb_notification",
	"icon" => "thb_vc_ico_notification",
	"class" => "thb_vc_sc_notification",
	"category" => "by Fuel Themes",
	"params" => array(
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Type",
			"param_name" => "type",
			"value" => array(
				"Information" => "information",
				"Success" => "success",
				"Warning" => "warning",
				"Error" => "error"
			),
			"description" => ""
		),
		array(
			"type" => "textarea",
			"class" => "",
			"heading" => "Content",
			"admin_label" => true,
			"param_name" => "content",
			"value" => "",
			"description" => ""
		)
	),
	"description" => "Display Notifications"
) );

// Single Product
vc_map( array(
	"name" => __("Single Product", THB_THEME_NAME),
	"base" => "thb_product_single",
	"icon" => "thb_vc_ico_product_single",
	"class" => "thb_vc_sc_product_single",
	"category" => "by Fuel Themes",
	"params"	=> array(
	  array(
	      "type" => "textfield",
	      "heading" => "Product ID",
	      "param_name" => "product_id",
	      "admin_label" => true,
	      "description" => "Enter the products ID you would like to display"
	  )
	),
	"description" => "Add WooCommerce product"
) );

// Products
vc_map( array(
	"name" => __("Products", THB_THEME_NAME),
	"base" => "thb_product",
	"icon" => "thb_vc_ico_product",
	"class" => "thb_vc_sc_product",
	"category" => "by Fuel Themes",
	"params"	=> array(
	  array(
	      "type" => "dropdown",
	      "heading" => "Product Sort",
	      "param_name" => "product_sort",
	      "value" => array(
	      	'Best Sellers' => "best-sellers",
	      	'Latest Products' => "latest-products",
	      	'Top Rated' => "top-rated",
			'Featured Products' => "featured-products",
	      	'Sale Products' => "sale-products",
	      	'By Category' => "by-category",
	      	'By Product ID' => "by-id",
	      	),
	      "description" => "Select the order of the products you'd like to show."
	  ),
	  array(
	      "type" => "checkbox",
	      "heading" => "Product Category",
	      "param_name" => "cat",
	      "value" => thb_productCategories(),
	      "description" => "Select the order of the products you'd like to show.",
	      "dependency" => Array('element' => "product_sort", 'value' => array('by-category'))
	  ),
	  array(
	      "type" => "textfield",
	      "heading" => "Product IDs",
	      "param_name" => "product_ids",
	      "description" => "Enter the products IDs you would like to display seperated by comma",
	      "dependency" => Array('element' => "product_sort", 'value' => array('by-id'))
	  ),
	  array(
	      "type" => "dropdown",
	      "heading" => "Carousel",
	      "param_name" => "carousel",
	      "value" => array(
	      	'Yes' => "yes",
	      	'No' => "no",
	      	),
	      "description" => "Select yes to display the products in a carousel."
	  ),
	  array(
	      "type" => "textfield",
	      "class" => "",
	      "heading" => "Number of Items",
	      "param_name" => "item_count",
	      "value" => "4",
	      "description" => "The number of products to show.",
	      "dependency" => Array('element' => "product_sort", 'value' => array('by-category', 'sale-products', 'top-rated', 'latest-products', 'best-sellers'))
	  ),
	  array(
	      "type" => "dropdown",
	      "heading" => "Columns",
	      "param_name" => "columns",
	      "admin_label" => true,
	      "value" => array(
	      	'Four Columns' => "4",
	      	'Three Columns' => "3",
	      	'Two Columns' => "2"
	      ),
	      "description" => "Select the layout of the products."
	  ),
	),
	"description" => "Add WooCommerce products"
) );

// Product List
vc_map( array(
	"name" => __("Product List", THB_THEME_NAME),
	"base" => "thb_product_list",
	"icon" => "thb_vc_ico_product_list",
	"class" => "thb_vc_sc_product_list",
	"category" => "by Fuel Themes",
	"params"	=> array(
		array(
		    "type" => "textfield",
		    "class" => "",
		    "heading" => "Title",
		    "param_name" => "title",
		    "value" => "",
		    "admin_label" => true,
		    "description" => "Title of the widget"
		),
	  array(
	      "type" => "dropdown",
	      "heading" => "Product Sort",
	      "param_name" => "product_sort",
	      "value" => array(
	      	'Best Sellers' => "best-sellers",
	      	'Latest Products' => "latest-products",
	      	'Top Rated' => "top-rated",
	      	'Sale Products' => "sale-products",
	      	'By Product ID' => "by-id"
	      	),
	      "admin_label" => true,
	      "description" => "Select the order of the products you'd like to show."
	  ),
	  array(
	      "type" => "textfield",
	      "heading" => "Product IDs",
	      "param_name" => "product_ids",
	      "description" => "Enter the products IDs you would like to display seperated by comma",
	      "dependency" => Array('element' => "product_sort", 'value' => array('by-id'))
	  ),
	  array(
	      "type" => "textfield",
	      "class" => "",
	      "heading" => "Number of Items",
	      "param_name" => "item_count",
	      "value" => "4",
	      "description" => "The number of products to show.",
	      "dependency" => Array('element' => "product_sort", 'value' => array('by-category', 'sale-products', 'top-rated', 'latest-products', 'best-sellers'))
	  )
	),
	"description" => "Add WooCommerce products in a list"
) );

// Shop Grid
vc_map( array(
	"name" => __("Shop Grid", THB_THEME_NAME),
	"base" => "thb_grid",
	"icon" => "thb_vc_ico_grid",
	"class" => "thb_vc_sc_grid",
	"category" => "by Fuel Themes",
	"params"	=> array(
		array(
		  "type" => "dropdown",
		  "heading" => "Type",
		  "param_name" => "type",
		  "value" => array(
			'Categories' => "categories",
			'Products' => "products",
			),
		  "description" => "Select what you want to show inside the grid"
		),
		array(
		  "type" => "checkbox",
		  "heading" => "Product Category",
		  "param_name" => "cat",
		  "value" => thb_productCategories(),
		  "description" => "Select the categories you would like to display",
	      "dependency" => Array('element' => "type", 'value' => array('categories'))
		),
		array(
	      "type" => "textfield",
	      "heading" => "Product IDs",
	      "param_name" => "product_ids",
	      "description" => "Enter the products IDs you would like to display seperated by comma",
	      "dependency" => Array('element' => "type", 'value' => array('products'))
	  	),
		array(
		  "type" => "dropdown",
		  "heading" => "Style",
		  "param_name" => "style",
		  "admin_label" => true,
		  "value" => array(
			'Style 1' => "style1",
			'Style 2' => "style2"
		  ),
		  "description" => "This applies different grid structures"
		),
		array(
			"type" => "dropdown",
			"class" => "",
			"heading" => "Animation",
			"param_name" => "animation",
			"value" => array(
				"None" => "",
				"Left" => "animation right-to-left",
				"Right" => "animation left-to-right",
				"Top" => "animation bottom-to-top",
				"Bottom" => "animation top-to-bottom",
				"Scale" => "animation scale",
				"Fade" => "animation fade-in"
			),
			"description" => ""
		)
	),
	"description" => "Add WooCommerce Grids"
) );

// Product Categories
vc_map( array(
	"name" => __("Product Categories", THB_THEME_NAME),
	"base" => "thb_product_categories",
	"icon" => "thb_vc_ico_product_categories",
	"class" => "thb_vc_sc_product_categories",
	"category" => "by Fuel Themes",
	"params"	=> array(
	  array(
	      "type" => "checkbox",
	      "heading" => "Product Category",
	      "param_name" => "cat",
	      "value" => thb_productCategories(),
	      "description" => "Select the categories you would like to display"
	  ),
	  array(
	      "type" => "dropdown",
	      "heading" => "Carousel",
	      "param_name" => "carousel",
	      "value" => array(
	      	'Yes' => "yes",
	      	'No' => "no",
	      	),
	      "description" => "Select yes to display the categories in a carousel."
	  ),
	  array(
	      "type" => "dropdown",
	      "heading" => "Columns",
	      "param_name" => "columns",
	      "admin_label" => true,
	      "value" => array(
	      	'Four Columns' => "4",
	      	'Three Columns' => "3",
	      	'Two Columns' => "2"
	      ),
	      "description" => "Select the layout of the products."
	  ),
	),
	"description" => "Add WooCommerce product categories"
) );

// Posts
vc_map( array(
	"name" => __("Posts", THB_THEME_NAME),
	"base" => "thb_post",
	"icon" => "thb_vc_ico_post",
	"class" => "thb_vc_sc_post",
	"category" => "by Fuel Themes",
	"params"	=> array(
	  array(
	      "type" => "dropdown",
	      "heading" => "Carousel",
	      "param_name" => "carousel",
	      "value" => array(
	      	'Yes' => "yes",
	      	'No' => "no",
	      	),
	      "description" => "Select yes to display the products in a carousel."
	  ),
	  array(
	      "type" => "textfield",
	      "class" => "",
	      "heading" => "Number of posts",
	      "param_name" => "item_count",
	      "value" => "4",
	      "description" => "The number of posts to show."
	  ),
	  array(
	      "type" => "dropdown",
	      "heading" => "Columns",
	      "param_name" => "columns",
	      "admin_label" => true,
	      "value" => array(
	      	'Four Columns' => "4",
	      	'Three Columns' => "3",
	      	'Two Columns' => "2"
	      ),
	      "description" => "Select the layout of the posts."
	  ),
	),
	"description" => "Display Posts from your blog"
) );

// Team Member shortcode
vc_map( array(
	"name" => "Team Member",
	"base" => "thb_teammember",
	"icon" => "thb_vc_ico_teammember",
	"class" => "thb_vc_sc_teammember",
	"category" => "by Fuel Themes",
	"params" => array(
		array(
			"type" => "attach_image", //attach_images
			"class" => "",
			"heading" => "Select Team Member Image",
			"param_name" => "image",
			"description" => "Minimum size is 270x270 pixels"
		),
		array(
		  "type" => "textfield",
		  "heading" => "Name",
		  "param_name" => "name",
		  "admin_label" => true,
		  "description" => "Enter name of the team member"
		),
		array(
		  "type" => "textfield",
		  "heading" => "Position",
		  "param_name" => "position",
		  "description" => "Enter position/title of the team member"
		),
		array(
		  "type" => "textfield",
		  "heading" => "Facebook",
		  "param_name" => "facebook",
		  "description" => "Enter Facebook Link"
		),
		array(
		  "type" => "textfield",
		  "heading" => "Twitter",
		  "param_name" => "twitter",
		  "description" => "Enter Twitter Link"
		),
		array(
		  "type" => "textfield",
		  "heading" => "Pinterest",
		  "param_name" => "pinterest",
		  "description" => "Enter Pinterest Link"
		),
		array(
		  "type" => "textfield",
		  "heading" => "Linkedin",
		  "param_name" => "linkedin",
		  "description" => "Enter Linkedin Link"
		)
	),
	"description" => "Display your team members in a stylish way"
) );

// Thumbnail Gallery Shortcode
vc_map( array(
	"name" => "Thumbnail Gallery",
	"base" => "thb_thumbnail_gallery",
	"icon" => "thb_vc_ico_thumbnail_gallery",
	"class" => "thb_vc_sc_thumbnail_gallery",
	"category" => "by Fuel Themes",
	"params" => array(
		array(
			"type" => "attach_images", //attach_images
			"class" => "",
			"heading" => "Select Images",
			"param_name" => "images",
			"admin_label" => true,
			"description" => ""
		)
	),
	"description" => "Add a thumbnail carousel"
) );