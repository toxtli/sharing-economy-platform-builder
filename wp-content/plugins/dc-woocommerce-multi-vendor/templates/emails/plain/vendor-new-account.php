<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/emails/plain/vendor-new-account.php
 *
 * @author 		WC Marketplace
 * @package 	dc-product-vendor/Templates
 * @version   0.0.1
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global  $WCMp;

echo $email_heading . "\n\n";

echo sprintf( __( "Thanks for creating an account with %s. We have received your application for vendor registration. We will verify the information provided by you and inform you via email. Your username is <strong>%s</strong>.",  'dc-woocommerce-multi-vendor' ), $blogname, $user_login ) . "\n\n";

if ( get_option( 'woocommerce_registration_generate_password' ) === 'yes' && $password_generated )
	echo sprintf( __( "Your password is <strong>%s</strong>.",  'dc-woocommerce-multi-vendor' ), $user_pass ) . "\n\n";

echo sprintf( __( 'You can access your account area here: %s.',  'dc-woocommerce-multi-vendor' ), get_permalink( wc_get_page_id( 'myaccount' ) ) ) . "\n\n";

echo "\n****************************************************\n\n";

echo apply_filters( 'wcmp_email_footer_text', get_option( 'wcmp_email_footer_text' ) );