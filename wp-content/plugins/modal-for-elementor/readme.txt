=== PopBox For Elementor ===
Contributors: norewp, LuisBeonline, bashari
Donate link: https://www.paypal.me/NoreMarketing/5
Tags: PopBox, Modal, Popup, Elementor
Requires at least: 4.4
Tested up to: 4.9
Stable tag: 1.0.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add a modal widget for Elementor Page Builder.

== Description ==

NOTE: Modal For Elementor is now PopBox For Elementor and development will now be continued by Zulfikar Nore of NoreWP :) 

PopBox for Elementor allows the creation of beautiful templates with Elementor Page Builder for use with the included Popbox overlay script.

An Admin menu will be added on your Wordpress Dashboard sidepane named PopBoxes. This is the custom post type (CPT) you'll use to create the content of the PopBox.

A custom module will also be added to Elementor Page Builder edit screen to be used for the customization of the trigger button embedded on your page.
Simply select one of the PopBox content created via the CPT to be shown when the trigger button is clicked.

Brief video on setup (Sorry for the lack of sound): https://youtu.be/M3B9aLLTXKY


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

Go to settings > permalinks and click save. (you need to do that in order to register the popup post types.)

== Changelog ==
= 1.0.5 =
* FIX: PHP Error of undefined variable $close

= 1.0.4 =
* NEW: Added controls for close button text.
* TWEAK: Elevated PopBox's z-index when modal is open.
* TWEAK: Refactored Button style options and moved the border controls into their own settings tab.
* TWEAK: Button Typography moved to button content options for better arrangements 

= 1.0.3 =
* FIX: Double scrollbar on sites running on Astra & OceanWP themes.    

= 1.0.2 =
* FIXED: Left window shift when modal is open on some themes - Fixes: https://wordpress.org/support/topic/bug-with-a-moving-button-and-extra-gap/.    
* NEW: Added option to switch close button position.    
* NEW: Added option to increase/decrease close button size.    
* NEW: Added option for close button padding - see ineditor description on limitations.   
* TWEAK: Moved close button color control to the Style tab under Modal content.    

= 1.0.1 =
* FIXED: Bug with non Elementor authored content not showing in the PopBox - Fixes: https://wordpress.org/support/topic/not-working-video/

= 1.0.0 =
* FIXED: Issue when video contained in a popup did not stop playing after the closing - See topic: https://wordpress.org/support/topic/videos-in-popups/
* Code tidyup
* NEW: Added `modal_for_elementor_fail_load()` function to handle graceful fallback when Elementor is not active.    
* NEW: Added `modal_for_elementor_fail_load_out_of_date()` function to handle graceful fallback when Elementor version is out of date - Required version set to v1.8.5.    
* NEW: Background color control for modal content window.    
* NEW: Border and Border radius controls for modal content window.   
* TWEAK: Changed widget category to norewp-elements (NoreWP's Elementor Modules) for better representation and future enhancements.    
* TWEAK: Reorganized Controls for better workflow
* TWEAK: Modal Max Width control moved to Style > Modal Container for better grouping.    
* TWEAK: Moved button typography to below text padding for better workflow.
* TWEAK: Removed hardcoded content window box shadow and added Shadow Control for maximum control
* TWEAK: Minor CSS adjustments
* Renamed plugin to PopBox For Elementor.      

= 0.1.6 =
* Minor bug fixes

= 0.1.5 =
* Plugin initial release