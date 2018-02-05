<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/widget/vendor-info.php
 *
 * @author 		WC Marketplace
 * @package 	dc-product-vendor/Templates
 * @version     0.0.1
 */

global $WCMp;
?>

<h4><?php echo $vendor->user_data->display_name; ?> </h4>
<?php 
	$description = strip_tags($vendor->description);
	if (strlen($description) > 250) {
		// truncate string
		$stringCut = substr($description, 0, 250);

		// make sure it ends in a word so assassinate doesn't become ass...
		$description = substr($stringCut, 0, strrpos($stringCut, ' ')).'...'; 
	}
?>
<p><?php echo $description; ?> </p>
<p>
	<a href="<?php echo esc_attr( $vendor->permalink ); ?>" title="'<?php echo sprintf( __( 'More Products from %1$s', 'dc-woocommerce-multi-vendor' ), $vendor->user_data->display_name ); ?> '">
		<?php echo sprintf( __( 'More Products from %1$s', 'dc-woocommerce-multi-vendor' ), $vendor->user_data->display_name );?>
	</a>
</p>