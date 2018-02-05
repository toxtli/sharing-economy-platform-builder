<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Email_Rejected_New_Vendor_Account' ) ) :

/**
 * Customer New Account
 *
 * An email sent to the customer when they create an account.
 *
 * @class 		WC_Email_Approved_New_Vendor_Account
 * @version		2.0.0
 * @package		WooCommerce/Classes/Emails
 * @author 		WooThemes
 * @extends 	WC_Email
 */
class WC_Email_Rejected_New_Vendor_Account extends WC_Email {

	var $user_login;
	var $user_email;
	var $user_pass;

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		global $WCMp;
		$this->id 				= 'rejected_vendor_new_account';
		$this->title 			= __( 'Rejected Vendor Account', 'dc-woocommerce-multi-vendor' );
		$this->description		= __( 'Vendor new account emails are sent when site admin approves a pending vendor.', 'dc-woocommerce-multi-vendor' );

		$this->template_html 	= 'emails/rejected-vendor-account.php';
		$this->template_plain 	= 'emails/plain/rejected-vendor-account.php';

		$this->subject 			= __( 'Your account on {site_title}', 'dc-woocommerce-multi-vendor');
		$this->heading      	= __( 'Welcome to {site_title}', 'dc-woocommerce-multi-vendor');
		$this->template_base = $WCMp->plugin_path . 'templates/';
		// Call parent constuctor
		parent::__construct();
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 */
	function trigger( $user_id, $user_pass = '', $password_generated = false ) {

		if ( $user_id ) {
			$this->object 		= new WP_User( $user_id );

			$this->user_pass          = $user_pass;
			$this->user_login         = stripslashes( $this->object->user_login );
			$this->user_email         = stripslashes( $this->object->user_email );
			$this->recipient          = $this->user_email;
			$this->password_generated = $password_generated;
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() )
			return;

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}
	
	/**
	 * get_subject function.
	 *
	 * @access public
	 * @return string
	 */
	function get_subject() {
			return apply_filters( 'woocommerce_email_subject_rejected_vendor_new_account', $this->format_string( $this->subject ), $this->object );
	}

	/**
	 * get_heading function.
	 *
	 * @access public
	 * @return string
	 */
	function get_heading() {
			return apply_filters( 'woocommerce_email_heading_rejected_vendor_new_account', $this->format_string( $this->heading ), $this->object );
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html() {
		ob_start();
		wc_get_template( $this->template_html, array(
			'email_heading'      => $this->get_heading(),
			'user_login'         => $this->user_login,
			'user_pass'          => $this->user_pass,
			'blogname'           => $this->get_blogname(),
			'password_generated' => $this->password_generated,
			'sent_to_admin' => false,
			'plain_text'    => false
		), 'dc-product-vendor/', $this->template_base);
		return ob_get_clean();
	}

	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_plain() {
		ob_start();
		wc_get_template( $this->template_plain, array(
			'email_heading'      => $this->get_heading(),
			'user_login'         => $this->user_login,
			'user_pass'          => $this->user_pass,
			'blogname'           => $this->get_blogname(),
			'password_generated' => $this->password_generated,
			'sent_to_admin' => false,
			'plain_text'    => true
		) ,'dc-product-vendor/', $this->template_base );
		return ob_get_clean();
	}
}

endif;
