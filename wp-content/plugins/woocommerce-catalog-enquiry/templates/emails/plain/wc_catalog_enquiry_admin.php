<?php
/**
 * WC Catalog Enquiry Admin
 *
 * @author 	WC Marketplace
 * @version   3.0.2
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $WC_Woocommerce_Catalog_Enquiry;

echo $email_heading . "\n\n";

echo sprintf( __( "Dear Admin", 'woocommerce-catalog-enquiry' ) ) . "\n\n";
echo sprintf( __( "Please find the product enquiry, details are given below", 'woocommerce-catalog-enquiry' ) ) . "\n\n";

echo "\n****************************************************\n\n";

$product_obj = wc_get_product( $product_id );

echo "\n Product Name : ".$product_obj->get_name();

if($product_obj->get_type() == 'variation'){
	if(isset($enquiry_data['variations']) && count($enquiry_data['variations']) > 0 ){
            foreach ($enquiry_data['variations'] as $label => $value) {
                $label = str_replace( 'attribute_pa_', '', $label );
                $label = str_replace( 'attribute_', '', $label );
                echo "\n".ucfirst($label).": ".ucfirst($value);
            } 
        }else{
            if($product_obj->get_attributes()){
                foreach ($product_obj->get_attributes() as $label => $value) {
                  echo "\n".ucfirst(wc_attribute_label($label)).": ".ucfirst($value);
                }
            }
        }
}

echo "\n\n Product link : ".$product_obj->get_permalink();
if($product_obj->get_sku())
	echo "\n\n Product SKU : ".$product_obj->get_sku();

echo "\n\n\n****************************************************\n\n";

echo "\n Customer Details : ";

echo "\n\n\n Name : ".$enquiry_data['cust_name'];

echo "\n\n Email : ".$enquiry_data['cust_email'];
if(isset($enquiry_data['phone']))
	echo "\n\n User Phone : ".$enquiry_data['phone'];
if(isset($enquiry_data['address']))
	echo "\n\n User Address : ".$enquiry_data['address'];
if(isset($enquiry_data['subject']))
	echo "\n\n User Subject : ".$enquiry_data['subject'];
if(isset($enquiry_data['comment']))
	echo "\n\n User Comments : ".$enquiry_data['comment'];

echo "\n\n\n****************************************************\n\n";

echo apply_filters('wc_catalog_enquiry_email_footer_text', sprintf( __( '%s - Powered by WC Catalog Enquiry', 'woocommerce-catalog-enquiry' ), get_bloginfo( 'name', 'display' ) ) );
