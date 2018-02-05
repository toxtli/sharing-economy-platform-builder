<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Catalog_Enquiry_Email' ) ) :

/**
 * Email to Admin for customer enquiry
 *
 * An email will be sent to the admin when customer enquiry about a product.
 *
 * @class 		WC_Catalog_Enquiry_Email
 * @version		3.0.2
 * @author 		WC Marketplace
 * @extends 	WC_Email
 */
class WC_Catalog_Enquiry_Email extends WC_Email {
	
	public $product_id;
	public $attachments;
	public $enquiry_data;
	public $cust_name;
	public $cust_email;

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		
		global $WC_Woocommerce_Catalog_Enquiry;
		
		$this->id 				= 'wc_catalog_enquiry_admin';
		$this->title 			= __( 'WC Catalog Enquiry admin', 'woocommerce-catalog-enquiry' );
		$this->description		= __( 'Admin will get an email when customer enquiry about a product', 'woocommerce-catalog-enquiry' );

		$this->template_html 	= 'emails/wc_catalog_enquiry_admin.php';
		$this->template_plain 	= 'emails/plain/wc_catalog_enquiry_admin.php';

		$this->template_base = $WC_Woocommerce_Catalog_Enquiry->plugin_path . 'templates/';
		
		// Call parent constuctor
		parent::__construct();
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 */
	function trigger( $recipient, $enquiry_data ) {
		
		$this->recipient = $recipient;
		$this->product_id = $enquiry_data['product_id'];
		$this->enquiry_data = $enquiry_data;
		$this->cust_name = $enquiry_data['cust_name'];
		$this->cust_email = $enquiry_data['cust_email'];
		$this->customer_email = $this->cust_email;
		
		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$product = wc_get_product( $this->product_id );

		$this->find[ ]      = '{PRODUCT_NAME}';
		$this->replace[ ]   = $product->get_title();

		$this->find[ ]      = '{USER_NAME}';
		$this->replace[ ]   = $enquiry_data['cust_name'];

		// Set email attachments
		if(is_array($enquiry_data['attachments']) && count($enquiry_data['attachments']) > 0){
			$this->attachments = $enquiry_data['attachments'];
		}
		
		$send = $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		return $send;
	}

	/**
	 * Get email subject.
	 *
	 * @since  1.4.7
	 * @return string
	 */
	public function get_default_subject() {
		return apply_filters( 'wc_catalog_enquiry_admin_email_subject', __( 'Product Enquiry for {PRODUCT_NAME} by {USER_NAME}', 'woocommerce-catalog-enquiry'), $this->object );
	}

	/**
	 * Get email heading.
	 *
	 * @since  1.4.7
	 * @return string
	 */
	public function get_default_heading() {
		return apply_filters( 'wc_catalog_enquiry_admin_email_heading', __( 'Enquiry for {PRODUCT_NAME}', 'woocommerce-catalog-enquiry'),$this->object );
	}


	/**
     * Get email attachments.
     *
     * @return string
     */
    public function get_attachments() {
        return apply_filters( 'wc_catalog_enquiry_admin_email_attachments', $this->attachments, $this->id, $this->object );
    }

	/**
	 * Get email headers.
	 *
	 * @return string
	 */
	public function get_headers() {
		$header = "Content-Type: " . $this->get_content_type() . "\r\n";
		$header .= 'Reply-to: ' . $this->cust_name . ' <' . $this->cust_email . ">\r\n";
		return apply_filters( 'wc_catalog_enquiry_admin_email_headers', $header, $this->id, $this->object );
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
			'email_heading' => $this->get_heading(),
			'product_id' => $this->product_id,
			'enquiry_data' => $this->enquiry_data,
			'customer_email' => $this->customer_email,
			'sent_to_admin' => true,
			'plain_text' => false
		), '', $this->template_base);
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
			'email_heading' => $this->get_heading(),
			'product_id' => $this->product_id,
			'enquiry_data' => $this->enquiry_data,
			'customer_email' => $this->customer_email,
			'sent_to_admin' => true,
			'plain_text' => true
		) ,'', $this->template_base );
		return ob_get_clean();
	}
	
}

endif;

return new WC_Catalog_Enquiry_Email();
