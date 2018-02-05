<?php

/*
 * Utilities class for Google Sheet Integration for Caldera Forms
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
   exit;
}

/**
 * Utilities class - singleton class
 * @since 1.0
 */
class Cfgs_Connector_Utility {
    
    private function __construct() {
      // Do Nothing
    }
    
    /**
    * Get the singleton instance of the Cfgs_Connector_Utility class
    *
    * @return singleton instance of Cfgs_Connector_Utility
    */
   public static function instance() {

      static $instance = NULL;
      if ( is_null( $instance ) ) {
         $instance = new Cfgs_Connector_Utility();
      }
      return $instance;
   }
   
   /**
    * Prints message (string or array) in the debug.log file
    *
    * @param mixed $message
    */
    public function logger( $message ) {
      if ( WP_DEBUG === true ) {
         if ( is_array( $message ) || is_object( $message ) ) {
            error_log( print_r( $message, true ) );
         } else {
            error_log( $message );
         }
      }
    }
    
   /**
    * Display error or success message in the admin section
    *
    * @param array $data containing type and message
    * @return string with html containing the error message
    * 
    * @since 1.0
    */
   public function admin_notice( $data = array() ) {
      // extract message and type from the $data array
      $message = isset( $data['message'] ) ? $data['message'] : "";
      $message_type = isset( $data['type'] ) ? $data['type'] : "";
      switch ( $message_type ) {
         case 'error':
            $admin_notice = '<div id="message" class="error notice is-dismissible">';
            break;
         case 'update':
            $admin_notice = '<div id="message" class="updated notice is-dismissible">';
            break;
         case 'update-nag':
            $admin_notice = '<div id="message" class="update-nag">';
            break;
         default:
            $message = __( 'There\'s something wrong with your code...', 'gsconnector' );
            $admin_notice = "<div id=\"message\" class=\"error\">\n";
            break;
      }

      $admin_notice .= "    <p>" . __( $message, 'gsconnector' ) . "</p>\n";
      $admin_notice .= "</div>\n";
      return $admin_notice;
   }
   
   /**
    * Utility function to get the current user's role
    *
    * @since 1.0
    */
    public function get_current_user_role() {
      global $wp_roles;
      foreach ( $wp_roles->role_names as $role => $name ) :
         if ( current_user_can( $role ) )
            return $role;
      endforeach;
    }
    
    /**
    * Utility function to get the current user's role
    *
    * @since 1.0
    */
    public static function debug_log($error){
        try {    
            if( ! is_dir( CFGS_CONNECTOR_PATH.'logs' ) ) {
                mkdir( CFGS_CONNECTOR_PATH . 'logs', 0755, true );
            }
        } catch (Exception $e) {

        }
        try {
            $log = fopen( CFGS_CONNECTOR_PATH . "logs/log.txt", 'a');
            if ( is_array( $error ) ) {
                fwrite($log, print_r(date_i18n( 'j F Y H:i:s', current_time( 'timestamp' ) )." \t PHP ".phpversion(), TRUE));
                fwrite( $log, print_r($error, TRUE));   
            } else {
                $result = fwrite($log, print_r(date_i18n( 'j F Y H:i:s', current_time( 'timestamp' ) )." \t PHP ".phpversion()." \t $error \r\n", TRUE));
            }
            fclose( $log );
        } catch (Exception $e) {
            
        }
    }  
}