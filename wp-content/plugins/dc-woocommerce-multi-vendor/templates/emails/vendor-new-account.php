<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/emails/vendor-new-account.php
 *
 * @author 		WC Marketplace
 * @package 	dc-product-vendor/Templates
 * @version   0.0.1
 */


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
global  $WCMp;
?>
<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<p><?php printf( __( "Thanks for creating an account on %s. We will process your application and revert shortly.",  'dc-woocommerce-multi-vendor' ), esc_html( $blogname ), esc_html( $user_login ) ); ?></p>
<?php if ( get_option( 'woocommerce_registration_generate_password' ) == 'yes' && $password_generated ) : ?>
<p><?php printf( __( "Your password has been automatically generated: <strong>%s</strong>",  'dc-woocommerce-multi-vendor' ), esc_html( $user_pass ) ); ?></p>
<?php endif; ?>
<p><?php printf( __( 'You can access your account area here: %s.',  'dc-woocommerce-multi-vendor' ), get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?></p>

<?php do_action( 'wcmp_email_footer' ); ?>