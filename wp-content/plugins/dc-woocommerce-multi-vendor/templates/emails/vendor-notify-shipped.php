<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/emails/vendor-notify-shipped.php
 *
 * @author 		WC Marketplace
 * @package 	dc-product-vendor/Templates
 * @version   0.0.1
 */
 
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
global  $WCMp;
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<p><?php _e( 'Some of the items you had ordered have been shipped. The items that have been shipped are as follows:',  'dc-woocommerce-multi-vendor' ); ?></p>

<?php do_action( 'woocommerce_email_before_order_table', $order, true ); ?>

<h2><?php printf( __( 'Order: %s',  'dc-woocommerce-multi-vendor' ), $order->get_order_number() ); ?> (<?php printf( '<time datetime="%s">%s</time>', date_i18n( 'c', strtotime( $order->get_date_created() ) ), date_i18n( wc_date_format(), strtotime( $order->get_date_created() ) ) ); ?>)</h2>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Product',  'dc-woocommerce-multi-vendor' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Quantity',  'dc-woocommerce-multi-vendor' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Price',  'dc-woocommerce-multi-vendor' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php 
		 	$vendor = get_wcmp_vendor_by_term($vendor_id);
			$vendor_items_dtl = $vendor->vendor_order_item_table($order, $vendor_id, true); 
			echo $vendor_items_dtl;
		?>
	</tbody>
</table>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<?php
		if ( $totals = $order->get_order_item_totals() ) {
			$i = 0;
			foreach ( $totals as $total ) {
				$i++;
				?><tr>
					<th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; <?php if ( $i == 1 ) echo 'border-top-width: 1px;'; ?>"><?php echo $total['label']; ?></th>
					<td style="text-align:left; border: 1px solid #eee; <?php if ( $i == 1 ) echo 'border-top-width: 1px;'; ?>"><?php echo $total['value']; ?></td>
				</tr><?php
			}
		}
	?>
</table>
<?php do_action('woocommerce_email_after_order_table', $order, true); ?>

<?php do_action( 'woocommerce_email_order_meta', $order, true ); ?>

<h2><?php _e( 'Customer Details',  'dc-woocommerce-multi-vendor' ); ?></h2>

<?php if ( $order->get_billing_email() ) : ?>
	<p><strong><?php _e( 'Email:',  'dc-woocommerce-multi-vendor' ); ?></strong> <?php echo $order->get_billing_email(); ?></p>
<?php endif; ?>
<?php if ( $order->get_billing_phone() ) : ?>
	<p><strong><?php _e( 'Telephone:',  'dc-woocommerce-multi-vendor' ); ?></strong> <?php echo $order->get_billing_phone(); ?></p>
<?php endif; ?>
        
<h2><?php _e( 'Shipment Tracking Details',  'dc-woocommerce-multi-vendor' ); ?></h2>
<p><strong><?php _e( 'Tracking Url:',  'dc-woocommerce-multi-vendor' ); ?></strong> <?php echo $tracking_url; ?></p>
<p><strong><?php _e( 'Tracking Id:',  'dc-woocommerce-multi-vendor' ); ?></strong> <?php echo $tracking_id; ?></p>

<?php wc_get_template( 'emails/email-addresses.php', array( 'order' => $order ) ); ?>

<?php do_action( 'wcmp_email_footer' ); ?>