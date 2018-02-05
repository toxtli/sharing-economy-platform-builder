=== NavMenu Addon For Elementor ===

Contributors: themeisle, codeinwp
Tags: elementor, pagebuilder, page builder, page builder menu, page builder navmenu, menu builder, builder navigation menus, navigation, menus, navmenu, nav menu  
Requires at least: 4.4  
Tested up to: 4.9
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html   

Adds new NavMenus to the Elementor Page Builder plugin. Now with Site Branding options

== Description ==
Custom WordPress navmenu specifically designed for the [Elementor Page Builder](https://wordpress.org/plugins/elementor/) - Now with Site Branding options, search box, basic MegaMenu and Fullscreen Menu Overlay

== Installation ==
* These instructions assumes you already have a WordPress site and the Elementor plugin installed and activated. Also, it is assumed that you already have at least a menu created.

1. Install using the WordPress built-in Plugin installer, or Extract the zip file and drop the contents in the `wp-content/plugins/` directory of your WordPress installation.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Pages > Add New
4. While in the Elementor Editor, drag and drop the NavMenu widget to the location of your choice
4. Configure as desired and save.
5. Done! Enjoy :)

== Frequently Asked Questions ==

**I've added the menu element but I do not see the menus in the dropdown box**    

Make sure that you have created your menus under Appearance >> Menus

**In the Branding module, I've selected the logo but it does not appear**    

Make sure a) your theme supports the custom logo options and b) that a logo has been set in the theme's customizer.   

**How do I make a split navigation with the Site title/logo in the middle?**

a: Go to Appearance >> Menus (if not already done so) and create two sets of menus - for brevity will call them Left Menu and Right Menu   
b: Go to create a page (if not already done so), give it a title say Navigation Bar 1 and click Edit with Elementor. It is recommended to use a blank template for this - if your theme does not have you can use the Page Template plugin.   
c: In the Edit mode insert a 3 column section into the page, adjust the middle column to be slight narrower than the 2 outer ones.   
e: Drag and drop the NavMenu widget in to each of the outer columns and adjust setting as desired. Now drag and drop the Branding element into the middle column - adjust settings accordingly and save.   
Done! :)  

== Screenshots ==

1. Header With Twin Navigation.
2. Fullscreen Overlay Menu
3. MegaMenu Structure On the Menu edit screen.
4. MegaMenu Frontend

== Known Issues ==

* Currently both the Overlay and MegaMenu content are not viewable while in Edit Mode - this is due to both having an on.Click event to display content which does not seem to work in the Editor.
* For the time being, any changes made to both of the above can be viewed on the frontend of the site. A fix is being sought and will be implemented as soon as a viable solution is found!

== Changelog ==
= 1.1.2 - 2017-11-27  = 

* Fix TGM strings for recommended plugins.


= 1.1.1 - 2017-11-16  = 

* Add recommendation for Elementor Addons & Widgets. 
* Tested up to 4.9.


= 1.1.0 - 2017-09-28  = 

* Added Themeisle SDK.
* Added Continuous Integration.
* Changed contributors.


= 1.0.7 =
* FIXED: Missing widget icon due to updated icons in Elementor   
* FIXED: Disbaled the _content_template() functions as they are not in use.   
* TWEAK: Moved Branding and Search widgets into their own modules for future enhancements

= 1.0.6 =
* FIXED: Bug on the new UI column selectors in Elementor V1.5.0 - removed the z-index hack as this will now be taken care of by core!

= 1.0.5 =
* FIXED: Fatal error when active theme is Element Theme!

= 1.0.4 =
* NEW: Added options for alignment and padding for submenu items

= 1.0.3 =
* FIXED: Bug that cause drop down menu to fall behind elements in some designs.

= 1.0.2 =
* Enhanced and improved how menus are styled.
* Changed behavior of dropdown menu floats - by default they now float to the right. So left and center aligned menu will float right and right aligned menus will float left.
* NEW: Added option for mobile menu item alignment.
* NEW: Added background setting for the navbar
* NEW: Added a Fullscreen Overlay Menu
* NEW: Added a search box widget - this is still a work in progress
* NEW: Added a basic MegaMenu options
* TWEAKES: Added and tweaked some styling options for the Default NavMenu module.

= 1.0.1 =
* NEW: Revised the plugin structure to me more modular and facilitate more element additions.
* NEW: Created a plugin specific elements category.
* NEW: Added option to select a menu location (up to 2) with both being mobile ready!
* NEW: Added a site branding module - you can now insert either the Site Title or the set Custom Logo in your headers.
* TWEAKS: Tiny but significant tweaks to make the whole experience more user friendly :)

= 1.0.0 =
* Initial release.