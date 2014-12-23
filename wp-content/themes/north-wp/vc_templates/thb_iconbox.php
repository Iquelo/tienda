<?php function thb_iconbox( $atts, $content = null ) {
    extract(shortcode_atts(array(
    	'type'			 => '',
       	'image'      => '',
       	'color'      => '',
       	'icon'			 => 'fa',
       	'icon_color' => false,
       	'content_color' => false,
       	'heading'		 => '',
       	'heading_color' => false,
       	'use_btn'				 => false,
       	'btn_color'      => '',
       	'btn_target_blank' => false,
       	'btn_link'       => '#',
       	'btn_size'			 => 'small',
       	'btn_icon'			 => false,
       	'btn_content'		 => false,
       	'btn_style'       => false,
       	'animation'	 => false
    ), $atts));
	$btn = '';
	
	// Image & Icon
	if ($image) {
		$img_id = preg_replace('/[^\d]/', '', $image);
		$img = wp_get_attachment_image($img_id, 'full', false, array(
			'alt'   => trim(strip_tags( get_post_meta($img_id, '_wp_attachment_image_alt', true) )),
		));
  } else {
  	$icon = '<i class="fa '.$icon.'"></i>';
  }
  
  // Button
  if ($use_btn) {
	  if($btn_icon) { $btn_content = '<span class="icon"><i class="fa '.$btn_icon.'"></i></span>'. $btn_content; }
	  
	  $btn = '<a class="btn '.$btn_color.' '.$btn_size.' '.$btn_style.'" href="'.$btn_link.'" ' . ($btn_target_blank ? ' target="_blank"' : '') .' role="button">' .$btn_content. '</a>';
	}

	// Content
	
	$out = '<div class="iconbox '.$type.' '.$animation.'">';

	$out .= '<span' . ($image ? ' class="img"' : '') .' ' . ($icon_color ? ' style="color: '.$icon_color.'"' : '') .'>' . ($image ? $img : $icon) .'</span>';

		
		
	$out .= '<div class="content">';
	
	
	$out .= '<h6' . ($heading_color ? ' style="color: '.$heading_color.'"' : '').'>'.$heading.'</h6>';
	$out .= '<div' . ($content_color ? ' style="color: '.$content_color.'"' : '').'>'.$content.'</div>';
	$out .= $btn;
	$out .= '</div>
	</div>';
  return $out;
}
add_shortcode('thb_iconbox', 'thb_iconbox');
