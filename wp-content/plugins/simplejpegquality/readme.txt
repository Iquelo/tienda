=== SimpleJPEGQuality ===
Contributors: brokenlibrarian
Donate link: http://brokenlibrarian.org/tinyplugins/
Tags: thumbnails, images, jpeg
Requires at least: 3.3.1
Tested up to: 4.0
Stable tag: 1.0
License: Apache v2

== Description ==

SimpleJPEGQuality is a plugin that adds a JPEG quality setting to the WordPress media settings page, which is used to determine the image quality of thumbnails, post images, headers, and other images which have been uploaded _and then resized_ by WordPress. It will have no effect on the quality of the original uploaded images, or that of any images that were resized before this plugin was installed.

This plugin will also affect the quality level of JPEGs resized by plugins that use the image functions of WordPress (such as Auto Post Thumbnail). Image plugins that use their own code (such as Lazyest Gallery, Ungallery, and Post Thumbnail Editor) will not be affected.

The plugin has no extra requirements and has been tested with the twentyten through twentyfourteen themes. 

Development on this plugin has halted. Other developers should feel free to use the Apache-licensed code for their own projects.

http://brokenlibrarian.org/tinyplugins/
brokenlibrarian@gmail.com  
09/28/14

== Installation ==

1. Upload the SimpleJPEGQuality folder to your _/wp-content/plugins/_ folder and activate it.
2. Go to the media settings page in your WordPress admin panel and set the JPEG quality level.
3. No further configuration is required.

== Frequently Asked Questions ==

= How can I change the quality of my existing thumbnails and post images? =

You can either delete and re-upload your images, or use a plugin like this one:
https://wordpress.org/extend/plugins/regenerate-thumbnails/

= Why is the setting normally restricted to numbers between 25 and 95? =

At very low quality levels and on slow servers, timeouts can sometimes occur during the creation of thumbnails. This appears to be a problem with the PHP image libraries. To cause these timeouts to occur, one has to set the JPEG quality to a comically low number, one which would never normally be used on an actual site, but the option has been provided.

JPEG image qualities higher than 95 will not normally be perceptible to viewers. Those higher qualities cause the image size to ramp up very quickly, with very little image quality improvement in return. JPEG quality settings over 95 may be useful for archival purposes but are not appropriate for thumbnails. However, the option has been provided for people who want it.

The JPEG quality cannot be set below zero or above one hundred and will be rounded to a whole number.

= What is the default setting for WordPress without this plugin? =

The default JPEG quality used by WordPress is 90.

== Screenshots ==

1. The new settings on the Media page

== Changelog ==

= 1.0 =
* Final version with WordPress v4.0 testing

= 0.4.1 =
* update for WordPress 3.8 compatibility testing

= 0.4 =
* update for WordPress 3.5 compatibility testing

= 0.3 =
* initial release

== Upgrade Notice ==

= 1.0 =
* Final version with WordPress v4.0 testing

= 0.4.1 =
* update for WordPress 3.8 compatibility testing

= 0.4 =
* update for WordPress 3.5 compatibility testing

= 0.3 =
* initial release

== License ==

   Copyright 2014 Christian Wagner

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.