# Page Templater For Elementor #
**Contributors:** [themeisle](https://profiles.wordpress.org/themeisle), [codeinwp](https://profiles.wordpress.org/codeinwp)  
**Tags:** elementor, pagebuilder, page builder, page builder template, page builder templates, actions, storefront, twentysixteen, genesis, template builder, builder templates  
**Requires at least:** 4.4  
**Tested up to:** 4.9  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

A helper plugin for users of Elementor Page Builder

## Description ##
This plugin does one and one thing only - adds page templates (plus css) to any theme for use with the [Elementor Page Builder](https://wordpress.org/plugins/elementor/)  
 
Now includes support for custom menu to be used for anchor points on the full width blank template.

NEW: Support for custom post templates now included. Your theme/child theme must have custom post templates with in it for this function to work.
   
See the [FAQ](https://wordpress.org/plugins/elementor-templater/faq/) for details and a download link for an Actions Elementor ready child theme. 

Adds 2 new templates for complete full width experience while using the page builder - support for a number of popular themes is built-in  
- Template 1: Full width with header and footer : Builder Fullwidth Standard   
- Template 2: Full width and no header or footer : Builder Fullwidth Blank   
	
## Supported Themes ##
The following themes are currently supported out of the box - if your desired theme is not list you may need to add some custom css.   

* [Hestia](https://wordpress.org/themes/hestia/) - by ThemeIsle
* [Hestia Pro](https://themeisle.com/themes/hestia-pro/) - by Themeisle
* [Edge](https://wordpress.org/themes/edge/) - By themefreesia  
* [Experon](https://wordpress.org/themes/experon/) - ThinkUpThemes  
* [Genesis](http://my.studiopress.com/themes/genesis/) - By StudioPress  
* [GeneratePress](https://wordpress.org/themes/generatepress/) - By Tom Usborne   
* [Storefront](https://wordpress.org/themes/storefront/) - by WooThemes/Automattic  
* [TwentyFourteen](https://wordpress.org/themes/twentyfourteen/) - by WordPress.org  
* [TwentyFifteen](https://wordpress.org/themes/twentyfifteen/) - by WordPress.org  
* [TwentySixteen](https://wordpress.org/themes/twentysixteen/) - by WordPress.org  
* [TwentyThirteen](https://wordpress.org/themes/twentythirteen/) - by WordPress.org  
* [TwentySeventeen](https://wordpress.org/themes/twentyseventeen/) - by WordPress.org   
* [Vantage](https://wordpress.org/themes/vantage/) - by Greg Priday  
* [Virtue](https://wordpress.org/themes/virtue/) - by Kadence Themes   
* [Enlightenment](https://wordpress.org/themes/enlightenment/) - by Daniel Tara
* [Actions](https://wordpress.org/themes/actions/) - by WPDevHQ
* [ActionsPro](https://wpdevhq.com/themes/actions-pro/) - by WPDevHQ

If you are a theme author and would like to have your theme added to our supported list please provide details and we'll see what we can do.

If you find any issues with your particular theme not playing nice with the templates please let us know so that we can do our best
to accommodate you.

## Installation ##
* These instructions assumes you already have a WordPress site and the Elementor plugin installed and activated.

1. Install using the WordPress built-in Plugin installer, or Extract the zip file and drop the contents in the `wp-content/plugins/` directory of your WordPress installation.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Pages > Add New
4. Select the desired template from the Page Attributes section
4. Press the 'Edit with Elementor' button.
5. Now you can drag and drop widgets from the left panel onto the content area, as well as add new sections and columns that make up the page structure which will be render in a full width layout.

## Frequently Asked Questions ##

**With this plugin do I still need Elementor and a theme?**

Yes. This plugin acts as a helper or go between the Elementor Page builder and your current theme (including child themes) by facilitating the missing true full width templates.

**Will the templates work with any theme?**
Yes and No. The short answer is yes, you will be able to select the included templates. However, not all themes are supported in terms of the required styling and you may therefore need to add some custom css for your desired theme.

**Why a blank template?**
Ever wanted to build Landing pages, sales pages or display specific pages without the clutter and distraction of header items, sidebar widgets and footer items? Now you can.

**I see that support for anchor menu has been added, what is this?**
Anchors a point on a template that are attached to a specific menu item - upon click on the menu item the anchor position will scroll in to the viewport.

**How do I use this new feature?**
Please visit [Build With Elementor: Anchor Menus](http://buildwithelementor.com/custom-menu/) to get an over view of how it all works.

** I've update to version 1.0.2 but don't see the templates for posts, why?**
As with the current state of WordPress, custom post templates are only supported via themes and not plugins.   
To be able to use the new feature your theme needs to have a template with the tag "Post Template Name: TemplateName".   
If your theme does not have any templates you can copy the sample templates provided in the folder templates/sample-post-templates in this plugin.   
To understand how this works please download the free child theme for Actions from here: [Actions Child](http://buildwithelementor.com/blog/actions-elementor-child-theme/) - this can be used out of the box if desired.   

Further details will be available near the time of the release.

## Screenshots ##

1. Fullwidth with Header and Footer

2. Fullwidth no Header no Footer

## Changelog ##
### 1.2.2 - 2017-11-27  ###

* Fixed TGM strings for recommended plugins.


### 1.2.1 - 2017-10-11  ###

* Added Recommendation for Elementor Addons & Widgets.


### 1.2.0 - 2017-09-29  ###

* ---
* Added Themeisle SDK.
* Added Continuous Integration.
* Changed contributors.


### 1.1.3 ###
* Enabled support for Elementor Library Post Type on the sample post templates.

### 1.1.2 ###
* FIXED: JS Bug where the mobile menu toggle was not working for certain themes

### 1.1.1 ###
* Plugin name change due to a stipulation on Elementor's Terms & Conditions
* Added GeneratePress to the supported themes list

### 1.1.0 ###
* CSS adjustment to support for Hello Pro - Genesis child theme.

### 1.0.9 ###
* Adjustments to Genesis CSS full width support
* Adjustments to TwentySeventeen CSS full width support

### 1.0.8 ###
* Fixed issue with template loading for WordPress 4.7 due to functional changes to accommodate the new template for post types.
* Added support for the Enlightenment theme.
* Fixed: Appearance of site border
 caused by .site {margin:21px} in TwentySixteen theme after updating to Elementor 0.11.1 [Topic: 8450675](https://wordpress.org/support/topic/elementor-fullwidth-standard-padding-errer/)
* Wrapped custom post type function with a conditional check on WP version due to the upcoming post type template support - support to be deprecated!   

### 1.0.7 ###
* Further CSS enhancement for TwentySixteen - resolve the issue with top/bottom background showing through on both templates

### 1.0.6 ###
* Added strict check for GeneratePress as the active theme for previous update in order to avoid fatal error on title check

### 1.0.5 ###
* NEW: Added support for GeneratePress page title options - title is now shown by default on the full width template and can be disabled via GeneratePress page options.

### 1.0.4 ###
* FIXED: Bug on hidden Elementor section selector tabs while in edit mode [See This Forum Topic](https://wordpress.org/support/topic/column-and-section-tabs-missing/)

### 1.0.3 ###
* FIXED: Class clash with the Template for Custom Post Types plugin

### 1.0.2 ###
* NEW: Added support for custom post templates - supports all custom post types including Elementor Library.

### 1.0.1 ###
* NEW: Added support to use the custom menu widget on full width blank template - ideal for anchor menus

### 1.0.0 ###
* Initial release.