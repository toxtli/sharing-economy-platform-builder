=== Google Sheets Integration for Caldera Forms ===
Contributors: alexagr, westerndeal, sooskriszta
Donate link: https://paypal.me/alexagr
Tags: Caldera Forms, Google Sheets, Google, Sheets
Requires at least: 3.6
Tested up to: 4.7
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Send your Caldera Forms data directly to your Google Sheets spreadsheet.

== Description ==

This plugin provides integration between [Caldera Forms](https://wordpress.org/plugins/caldera-forms/) and [Google Sheets](https://www.google.com/sheets/).
It adds new processor to Caldera Forms that enables sending of submitted forms to Google Sheets.  

= Connecting to Google Sheets =

* Go to plugin 'Settings' screen (or to 'Admin Panel > Caldera Forms > Google Sheets' screen)
* Click 'Get Code' button - you will be redirected to Google Sheets authorization page
* Connect to your Google Sheets account (enter credentials / authorize the application)
* Copy generated Access Code to the clipboard
* Paste Access Code into the plugin 'Settings' screen
* Click 'Save' button

= Using The Plugin =

*In Google Sheets*  

* Create a new Sheet and name it  
* Rename the tab on which you want to capture the data

If you wish to record all of your Caldera Form fields, proceed to the next step and enable "Automatically generate header" option in processor settings.

If you wish to record only selected fields (or for some reason automatic header generation doesn't work for you) manually enter column names in the first row as follows:

* Enter "id" in the the first column name
* Enter "date" in the the second column name
* Enter slug names of your Caldera Form fields as the following column names

*In Caldera Forms*  

* Add Google Sheets processor to your form
* Configure Google Sheets sheet and tab name
* If you didn't manually create spreadsheet header in the previous step, check "Automatically generate header" checkbox 
* Click 'Save Form' to save processor settings
* Test your form submit and verify that the data shows up in your Google Sheet

= Automatic Header Generation = 

When "automatic header generation" is enabled, the plugin verifies spreadsheet header on each new form submission and adds new fields to it if needed. Note that it never deletes fields from the header - as this would also delete some submission data - though you can do it manually. You may also manually reorder columns as you wish.

It should be noted that header verification takes some time and may slow down form submission for large forms. Therefore consider using it only for your "test submissions" - after initial form creation and/or modification - and turning it off afterwards. 

= Important Notes = 

* Your slug names should be in english only (use only the following ASCII characters - [A-Za-z0-9])
* Your slug names should be lowercase only (no capital letters)
* If your slug names have underscores, replace them with dashes in Google Sheet column names (e.g. for 'name_english' slug use 'name-english' column name)

= Acknowledgements =

The plugin is heavily based on [CF7 Google Sheets Connector](https://wordpress.org/plugins/cf7-google-sheets-connector/)

== Screenshots ==

1. Connecting to Google Sheets account
2. Adding Google Sheets processor in Caldera Forms

== Installation ==

1. Upload 'cf-google-sheets' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' screen in WordPress.  

== Frequently Asked Questions ==

= Why isn't the data send to spreadsheet? Caldera Forms Submit is just Spinning. =
Sometimes it can take a while of spinning before it goes through. But if the entries never show up in your Sheet then one of these things might be the reason:

1. Wrong access code (check debug log)
2. Wrong Sheet name or tab name
3. Wrong Column name mapping (column names are the Caldera Forms slugs; they cannot have underscore or any special characters)

== Changelog ==

= 1.5 =
* Prevent fatal error when Caldera Forms is removed

= 1.4 =
* Added support for automatic spreadsheet header generation

= 1.3 =
* Added support for "id" column that records entry id

= 1.2 =
* Refactor Google PHP API lib to prevent 'clash' with other plugins

= 1.1 =
* Bug fixes

= 1.0 =
* Initial version

