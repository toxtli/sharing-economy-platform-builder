<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/emails/plain/admin-new-vendor-account.php
 *
 * @author 		WC Marketplace
 * @package 	dc-product-vendor/Templates
 * @version   0.0.1
 */
 
global $WCMp;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

echo "= " . $email_heading . " =\n\n";

echo sprintf( __( "A new user has applied to be a vendor on %s. His/her email is <strong>%s</strong>.", 'dc-woocommerce-multi-vendor' ), esc_html( $blogname ), esc_html( $user_email ) );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'wcmp_email_footer_text', get_option( 'wcmp_email_footer_text' ) );