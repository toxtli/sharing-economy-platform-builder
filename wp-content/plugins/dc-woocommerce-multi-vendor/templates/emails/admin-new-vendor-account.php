<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/emails/admin-new-vendor-account.php
 *
 * @author 		WC Marketplace
 * @package 	dc-product-vendor/Templates
 * @version   0.0.1
 */
 
global $WCMp;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
?>
<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<p><?php printf( __( "A new user has applied to be a vendor on %s. His/her email is <strong>%s</strong>.", 'dc-woocommerce-multi-vendor' ), esc_html( $blogname ), esc_html( $user_email ) ); ?></p>

<?php do_action( 'wcmp_email_footer' ); ?>