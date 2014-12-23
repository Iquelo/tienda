<?php
/*
Plugin Name: SimpleJPEGQuality
Plugin URI: http://brokenlibrarian.org/tinyplugins/simplejpegquality/
Description: Allows setting the default JPEG quality for resized images
Version: 1.0
Author: Christian Wagner
Author URI: http://brokenlibrarian.org/tinyplugins/
License: Apache v2
*/
?>
<?php
/*
   Copyright 2012 Christian Wagner

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/
?>
<?php

add_action( 'admin_init', 'simple_JPEG_quality_register' );

function simple_JPEG_quality_register() {
	register_setting( 'media' , 'simple-JPEG-settings' , 'simple_JPEG_validate' ); 

	add_settings_section( 'simple-JPEG-quality-settings' , 'JPEG Resizing' , 'show_simple_JPEG_quality_settings' , 'media' );
	
    add_settings_field( 'simple-JPEG-quality', 'JPEG Quality', 'show_simple_JPEG_quality', 'media', 'simple-JPEG-quality-settings' );
	add_settings_field( 'simple-JPEG-override', 'Allow unusual JPEG quality value?', 'show_simple_JPEG_override', 'media', 'simple-JPEG-quality-settings' );
}

// settings are in an array named 'simple-JPEG-settings' with the fields
// 'simple-JPEG-quality' (the JPEG quality level) and 'simple-JPEG-override' (a
// flag that lets the user set unusual values for the quality).

// rather than place a set of default values into the settings array at
// plugin install or initialization, the plugin functions assume that the
// settings may not exist and will generate defaults if need be.

function simple_JPEG_validate($input) {
	if (!isset($input['simple-JPEG-override']))
		$input['simple-JPEG-override'] = false; // Browsers like to not send checkbox data at all if it's not checked, so make sure the setting exists and if it doesn't, make it false
	if (is_numeric($input['simple-JPEG-quality'])) { // if the quality input is numeric, sanitize it
		if ( $input['simple-JPEG-override'] ) // if the override is on, quality is 0-100
			{ $max_quality = 100; $min_quality = 0; }
		else // if the override is off, quality is 25-95
			{ $max_quality = 95; $min_quality = 25; }
		$new_options['simple-JPEG-quality'] = min( max( $min_quality, absint($input['simple-JPEG-quality']) ) , $max_quality);
	}
	else { // if the quality input is garbage, just put the old one back (or set it to the default of 90 if no old value exists)
		if (($old_options = get_option( 'simple-JPEG-settings' )) && (array_key_exists('simple-JPEG-quality',$old_options))) $new_options['simple-JPEG-quality'] = $old_options['simple-JPEG-quality'];
		else $new_options['simple-JPEG-quality'] = 90;
	}
	$new_options['simple-JPEG-override'] = $input['simple-JPEG-override'];
	return $new_options;
}

// the validator not only checks for valid input, but it also checks the
// value of the override setting and restricts the quality setting
// accordingly

function show_simple_JPEG_quality_settings() { } // stub, since a callback function is required for a new section but nothing needs to be echoed

function show_simple_JPEG_quality() {
	if (($old_options = get_option( 'simple-JPEG-settings' )) && (array_key_exists('simple-JPEG-quality',$old_options))) $old_setting = $old_options['simple-JPEG-quality'];
		else $old_setting = 90;
	echo('<input id="simple-JPEG-quality" type="text" size="3" name="simple-JPEG-settings[simple-JPEG-quality]" value="');
	echo(esc_attr($old_setting));
	echo('" />');
}

function show_simple_JPEG_override() {
	if (($old_options = get_option( 'simple-JPEG-settings' )) && (array_key_exists('simple-JPEG-override',$old_options))) $old_setting = $old_options['simple-JPEG-override'];
		else $old_setting = false;
	echo('<input id="simple-JPEG-override" type="checkbox" name="simple-JPEG-settings[simple-JPEG-override]" value="1" ');
	checked (1, $old_setting);
	echo(' />');
	echo(' <em>If unchecked, JPEG quality values will be restricted to between 25 and 95. See plugin readme for details.</em> ');
}

// show_simple_JPEG_quality() and show_simple_JPEG_override() both check for the
// existence of the settings array, and then for the presence of their
// particular field in that array. if one or neither is present, a
// default value is used.

function simple_JPEG_quality () {
	if (($settings = get_option( 'simple-JPEG-settings' )) && (array_key_exists('simple-JPEG-quality' , $settings)))
		$output = absint($settings['simple-JPEG-quality']); 
	else
		$output = 90;
	return $output;
}

add_filter('jpeg_quality','simple_JPEG_quality',1,10);

// like the forms on the settings page, the actual filter function
// checks for the settings array and field, and uses a default if
// one or neither exist.

// it also does one final sanitization with absint() - worst case is
// that there is somehow still garbage in the quality field, this will
// make sure that WordPress passes an integer of *some* sort to
// imagejpeg(). this value would be either zero or one, which would
// result in comically bad JPEGs until the setting is fixed, but at
// least it won't cause an error. this condition should not be possible
// unless something happens to the database itself.

?>