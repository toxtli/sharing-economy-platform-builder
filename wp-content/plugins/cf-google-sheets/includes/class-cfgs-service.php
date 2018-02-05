<?php

/**
 * Service class for Google Sheets Integration for Caldera Forms
 * @since 1.0
 */
if ( !defined( 'ABSPATH' ) ) {
   exit; // Exit if accessed directly
}

/**
 * Cfgs_Connector_Service Class
 *
 * @since 1.0
 */
class Cfgs_Connector_Service {
    /**
     *  Set things up.
     *  @since 1.0
     */
    public function __construct() {
        add_action( 'wp_ajax_verify_gs_integation', array( $this, 'verify_gs_integation' ) );

        add_filter('caldera_forms_get_form_processors', array( $this, 'cfgs_register_processor' ) );
    }

    /**
     * AJAX function - verifies the token
     *
     * @since 1.0
     */
     public function verify_gs_integation() {
         // nonce check
         check_ajax_referer( 'gs-ajax-nonce', 'security' );

         /* sanitize incoming data */
         $Code = sanitize_text_field( $_POST[ "code" ] );

         update_option( 'gs_access_code', $Code );

         if ( get_option( 'gs_access_code') != '' ) {
             try {
                 include_once( CFGS_CONNECTOR_ROOT . '/lib/google-sheets.php');
                 cfgooglesheet::preauth( get_option('gs_access_code' ) );
                 update_option( 'gs_verify', 'valid' );
                 wp_send_json_success();
             } catch (Exception $e) {
                 $data['ERROR_MSG'] = $e->getMessage();
                 $data['TRACE_STK'] = $e->getTraceAsString();
                 Cfgs_Connector_Utility::debug_log($data);
             }
         } else {
             update_option( 'gs_verify', 'invalid' );
             wp_send_json_error();
         }
     }

     /**
      * Register Calder Forms processor
      *
      * @since 1.0
      */
     function cfgs_register_processor($pr) {
         $pr['sheets'] = array(
             "name"              =>  __('Google Sheets', 'cfgsconnector'),
             "description"       =>  __("Send form to Google Sheets on submission", 'cfgsconnector'),
             "author"            =>  'Alex Agranov',
             "icon"              =>	 plugin_dir_url(__FILE__) . '../assets/img/sheets-logo.png',
             "processor"         =>   array( $this, 'cfgs_publish' ),
             "template"          =>  CFGS_CONNECTOR_ROOT . '/includes/config.php'
         );

         return $pr;
     }

     /**
      * Send form to Google Sheets
      *
      * @since 1.0
      *
      * @param array $config Processor config
      * @param array $form Form config
      */
     function cfgs_publish($config, $form) {
         try {
             include_once( CFGS_CONNECTOR_ROOT . "/lib/google-sheets.php" );
             $doc = new cfgooglesheet();
             $doc->auth();
             $doc->settitleSpreadsheet( $config['sheet-name'] );
             $doc->settitleWorksheet( $config['sheet-tab-name'] );
             $data['id'] = Caldera_Forms::get_field_data( '_entry_id', $form );
             $data['date'] = date_i18n( 'j F Y H:i:s', current_time( 'timestamp' ) );
             foreach( $form['fields'] as $field ){
                 if( $field['type'] === 'html' || $field['type'] === 'button' ){
                     continue;
                 }
                 $key = str_replace( '_', '-', strtolower( $field['slug'] ) );
                 $value = Caldera_Forms::get_field_data( $field['ID'], $form );
                 if (is_array($value)) {
                     $data[$key] = implode(', ', $value);
                 } else {
                     $data[$key] = $value;
                 }
             }

             $doc->add_row($data, !empty( $config['header'] ) );
         } catch (Exception $e) {
             $data['ERROR_MSG'] = $e->getMessage();
             $data['TRACE_STK'] = $e->getTraceAsString();
             Cfgs_Connector_Utility::debug_log($data);
             return array(
                 'type' => 'error',
                 'note' => $e->getMessage()
             );
         }
    }
}

$cfgs_connector_service = new Cfgs_Connector_Service();
