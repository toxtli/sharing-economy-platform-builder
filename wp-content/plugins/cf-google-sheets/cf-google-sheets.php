<?php
/**
 * Plugin Name: Google Sheets Integration for Caldera Forms
 * Description: Send data to Google Sheets on form submission
 * Version:     1.5
 * Author:      Alex Agranov
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
   exit; // Exit if accessed directly
}

// Declare some global constants
define( 'CFGS_CONNECTOR_VERSION', '1.5' );
define( 'CFGS_CONNECTOR_ROOT', dirname( __FILE__ ) );
define( 'CFGS_CONNECTOR_URL', plugins_url( '/', __FILE__ ) );
define( 'CFGS_CONNECTOR_BASE_FILE', basename( dirname( __FILE__ ) ) . '/cf-google-sheets.php' );
define( 'CFGS_CONNECTOR_BASE_NAME', plugin_basename( __FILE__ ) );
define( 'CFGS_CONNECTOR_PATH', plugin_dir_path( __FILE__ ) ); //use for include files to other files
define( 'CFGS_CONNECTOR_PRODUCT_NAME', 'Google Sheets Integration for Caldera Forms' );
define( 'CFGS_CONNECTOR_CURRENT_THEME', get_stylesheet_directory() );

/*
 * include utility classes
 */
if ( ! class_exists( 'Cfgs_Connector_Utility' ) ) {
   include( CFGS_CONNECTOR_ROOT . '/includes/class-cfgs-utility.php' );
}

if ( ! class_exists( 'Cfgs_Connector_Service' ) ) {
   include( CFGS_CONNECTOR_ROOT . '/includes/class-cfgs-service.php' );
}

require_once CFGS_CONNECTOR_ROOT . '/lib/php-google-oauth/Google_Client.php';

/*
 * Main GS connector class
 * @class Gs_Connector_Init
 * @since 1.0
 */

class Cfgs_Connector_Init {

   /**
    *  Set things up.
    *  @since 1.0
    */
   public function __construct() {
      //run on activation of plugin
      register_activation_hook( __FILE__, array( $this, 'cfgs_connector_activate' ) );

      //run on deactivation of plugin
      register_deactivation_hook( __FILE__, array( $this, 'cfgs_connector_deactivate' ) );

      //run on uninstall
      register_uninstall_hook( __FILE__, array( 'Cfgs_Connector_Init', 'cfgs_connector_uninstall' ) );

      // validate is caldera forms plugin exist
      add_action( 'admin_init', array( $this, 'validate_parent_plugin_exists' ) );

      // register admin menu under "Contact" > "Integration"
      add_action( 'admin_menu', array( $this, 'register_cfgs_menu_pages' ) );

      // load the js and css files
      add_action( 'init', array( $this, 'load_css_and_js_files' ) );

      // Add custom link for our plugin
      add_filter( 'plugin_action_links_' . CFGS_CONNECTOR_BASE_NAME , array( $this, 'cfgs_connector_plugin_action_links' ) );
   }

   /**
    * Do things on plugin activation
    * @since 1.0
    */
   public function cfgs_connector_activate() {
      if ( ! get_option( 'gs_access_code' ) ) {
         update_option( 'gs_access_code', '' );
      }
      if ( ! get_option( 'gs_verify' ) ) {
         update_option( 'gs_verify', 'invalid' );
      }
      if ( ! get_option( 'gs_token' ) ) {
         update_option( 'gs_token', '' );
      }
   }

   /**
     *  Runs on plugin uninstall.
     *  a static class method or function can be used in an uninstall hook
     *
     *  @since 1.0
     */

    public static function cfgs_connector_uninstall() {
      delete_option( 'gs_access_code' );
      delete_option( 'gs_verify' );
      delete_option( 'gs_token' );
    }

   /**
    * Validate parent Plugin Caldera Forms exist and activated
    * @access public
    * @since 1.0
    */
   public function validate_parent_plugin_exists() {
      $plugin = plugin_basename( __FILE__ );
      if ( ( ! defined( CFCORE_VER  ) ) && ( ! defined( 'CFGS_CONNECTOR_VERSION' ) ) ) {
         add_action( 'admin_notices', array( $this, 'caldera_forms_missing_notice' ) );
         deactivate_plugins( $plugin );
         if ( isset( $_GET[ 'activate' ] ) ) {
            // Do not sanitize it because we are destroying the variables from URL
            unset( $_GET[ 'activate' ] );
         }
      }
   }

   /**
    * If Caldera Forms plugin is not installed or activated then throw the error
    *
    * @access public
    * @return mixed error_message, an array containing the error message
    *
    * @since 1.0 initial version
    */
   public function caldera_forms_missing_notice() {
      $plugin_error = Cfgs_Connector_Utility::instance()->admin_notice( array(
          'type' => 'error',
          'message' => 'Google Sheets Integration Add-on requires Caldera Forms plugin to be installed and activated.'
              ) );
      echo $plugin_error;
   }

   /**
    * Create/Register menu items for the plugin.
    * @since 1.0
    */
   public function register_cfgs_menu_pages() {
      add_submenu_page( 'caldera-forms', __( 'Google Sheets', 'cfgsconnector' ), __( 'Google Sheets', 'cfgsconnector' ), 'manage_options', 'caldera-forms-google-sheet-config', array( $this, 'google_sheet_config' ) );
   }

   /**
    * Google Sheets page action.
    * This method is called when the menu item "Google Sheets" is clicked.
    *
    * @since 1.0
    */
   public function google_sheet_config() {
      ?>
      <div class="wrap gs-form">
         <h1><?php echo esc_html( __( 'Caldera Forms - Google Sheets Integration', 'cfgsconnector' ) ); ?></h1>
         <div class="card" id="googlesheet">
            <h2 class="title"><?php echo esc_html( __( 'Google Sheets', 'cfgsconnector' ) ); ?></h2>

            <br class="clear">

            <div class="inside">
               <p class="gs-alert"> <?php echo esc_html( __( 'Click "Get code" to retrieve your code from Google Drive to allow us to access your spreadsheets. And paste the code in the below textbox. ', 'cfgsconnector' ) ); ?></p>
               <p>
                  <label><?php echo esc_html( __( 'Google Access Code', 'cfgsconnector' ) ); ?></label>
                  <input type="text" name="gs-code" id="gs-code" value="" placeholder="<?php if ( get_option('gs_token') !== '' ) { echo esc_html( __( 'Currently Active', 'cfgsconnector' ) ); } ?>"/>

                  <a href="https://accounts.google.com/o/oauth2/auth?access_type=offline&approval_prompt=force&client_id=333801235716-8n98am0peiuknmov5bu65ajfvckcolh2.apps.googleusercontent.com&redirect_uri=urn%3Aietf%3Awg%3Aoauth%3A2.0%3Aoob&response_type=code&scope=https%3A%2F%2Fspreadsheets.google.com%2Ffeeds%2F" target="_blank" class="button">Get Code</a>
               </p>

               <p> <input type="button" name="save-gs-code" id="save-gs-code" value="<?php _e( 'Save', 'cfgsconnector' ); ?>"
                          class="button button-primary" />
                  <span class="loading-sign">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>

               <p>
                  <label><?php echo esc_html( __( 'Debug Log', 'cfgsconnector' ) ); ?></label>
                  <label><a href= "<?php echo plugins_url( '/logs/log.txt', __FILE__ ); ?>" target="_blank" class="debug-view" >View</a></label>
               </p>
                <p id="gs-validation-message"></p>
               <!-- set nonce -->
               <input type="hidden" name="gs-ajax-nonce" id="gs-ajax-nonce" value="<?php echo wp_create_nonce( 'gs-ajax-nonce' ); ?>" />

            </div>
         </div>
      </div>
   <?php
   }

   public function load_css_and_js_files() {
      add_action( 'admin_print_styles', array( $this, 'add_css_files' ) );
      add_action( 'admin_print_scripts', array( $this, 'add_js_files' ) );
   }

   /**
    * enqueue CSS files
    * @since 1.0
    */
   public function add_css_files() {
      if ( is_admin() && ( isset( $_GET[ 'page' ] ) && ( $_GET[ 'page' ] == 'caldera-forms-google-sheet-config' ) ) ) {
         wp_enqueue_style( 'cf-google-sheets-style', CFGS_CONNECTOR_URL . 'assets/css/cf-google-sheets.css', array(), CFGS_CONNECTOR_VERSION, true);
      }
   }

   /**
    * enqueue JS files
    * @since 1.0
    */
   public function add_js_files() {
      if ( is_admin() && ( isset( $_GET[ 'page' ] ) && ( $_GET[ 'page' ] == 'caldera-forms-google-sheet-config' ) ) ) {
         wp_enqueue_script( 'cf-google-sheets', CFGS_CONNECTOR_URL . 'assets/js/cf-google-sheets.js', array("jquery"), CFGS_CONNECTOR_VERSION, true);
      }
   }

   /**
    * Add custom link for the plugin beside activate/deactivate links
    * @param array $links Array of links to display below our plugin listing.
    * @return array Amended array of links.    *
    * @since 1.0
    */
   public function cfgs_connector_plugin_action_links( $links ) {
      // Add our custom links to the returned array value.
      return array_merge( array(
          '<a href="' . admin_url( 'admin.php?page=caldera-forms-google-sheet-config' ) . '">' . __( 'Settings', 'cfgsconnector' ) . '</a>'
              ), $links );
   }
}

// Initialize the google sheet connector class
$init = new Cfgs_Connector_Init();
