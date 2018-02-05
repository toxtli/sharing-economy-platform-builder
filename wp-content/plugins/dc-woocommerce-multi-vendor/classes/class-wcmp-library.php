<?php
/**
 * WCMp Library Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		WC Marketplace
 */
 
class WCMp_Library {
  
  public $lib_path;
  
  public $lib_url;
  
  public $php_lib_path;
  
  public $php_lib_url;
  
  public $jquery_lib_path;
  
  public $jquery_lib_url;

	public function __construct() {
		
	  global $WCMp;
	  
	  $this->lib_path = $WCMp->plugin_path . 'lib/';

    $this->lib_url = $WCMp->plugin_url . 'lib/';
    
    $this->php_lib_path = $this->lib_path . 'php/';
    
    $this->php_lib_url = $this->lib_url . 'php/';
    
    $this->jquery_lib_path = $this->lib_path . 'jquery/';
    
    $this->jquery_lib_url = $this->lib_url . 'jquery/';
    
    $this->css_lib_path = $this->lib_path . 'css/';
    
    $this->css_lib_url = $this->lib_url . 'css/';
	}
	
	/**
	 * PHP WP fields Library
	*/
	public function load_wp_fields() {
	  global $WCMp;
	  require_once ($this->php_lib_path . 'class-dc-wp-fields.php');
	  $DC_WP_Fields = new WCMp_WP_Fields(); 
	  return $DC_WP_Fields;
	}
        
        public function load_scss_lib(){
            require_once ($this->php_lib_path . 'scss.inc.php');
            $scss = new scssc();
            return $scss;
        }

                /**
	 * Jquery qTip library
	*/
	public function load_qtip_lib() {
	  global $WCMp;
	  wp_enqueue_script('qtip_js', $this->jquery_lib_url . 'qtip/qtip.js', array('jquery'), $WCMp->version, true);
		wp_enqueue_style('qtip_css',  $this->jquery_lib_url . 'qtip/qtip.css', array(), $WCMp->version);
	}
	
	/**
	 * WP Media library
	*/
	public function load_upload_lib() {
	  global $WCMp;
	  wp_enqueue_media();
	  wp_enqueue_script('upload_js', $this->jquery_lib_url . 'upload/media-upload.js', array('jquery'), $WCMp->version, true);
	  wp_enqueue_style('upload_css',  $this->jquery_lib_url . 'upload/media-upload.css', array(), $WCMp->version);
	}
	
	/**
	 * WP ColorPicker library
	*/
	public function load_colorpicker_lib() {
	  global $WCMp;
	  wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_script( 'colorpicker_init', $this->jquery_lib_url . 'colorpicker/colorpicker.js', array( 'jquery', 'wp-color-picker' ), $WCMp->version, true );
    wp_enqueue_style( 'wp-color-picker' );
	}
	
	/**
	 * WP DatePicker library
	*/
	public function load_datepicker_lib() {
	  global $WCMp;
	  wp_enqueue_script('jquery-ui-datepicker');
	  wp_enqueue_style( 'jquery-ui-style' );
	}
	
	/**
	 * Font awesome include
	*/
	public function font_awesome_lib() {
	  global $WCMp;
	  //wp_enqueue_style('font_awesome_css', $this->css_lib_url . 'font-awesome/css/font-awesome.min.css');
	  wp_enqueue_style('font_awesome_css', 'http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css');
	}

	/**
	 * Jquery style library
	*/
	public function load_jquery_style_lib() {
	  if(!wp_style_is( 'jquery-ui-style', 'registered' )){
	  	$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.11.4';
		wp_register_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.min.css', array(), $jquery_version );
	  }
	}
}
