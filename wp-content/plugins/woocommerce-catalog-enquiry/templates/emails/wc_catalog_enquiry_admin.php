<?php
/**
 * WC Catalog Enquiry Admin
 *
 * @author 	WC Marketplace
 * @version   3.0.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $WC_Woocommerce_Catalog_Enquiry;

do_action( 'woocommerce_email_header', $email_heading ); ?>

<p><?php echo __('Dear Admin','woocommerce-catalog-enquiry');?>,</p>
<p><?php echo __('Please find the product enquiry, details are given below','woocommerce-catalog-enquiry');?>.</p>

<?php
$product_obj = wc_get_product( $product_id );

?>
<h3><?php _e( 'Product Details', 'woocommerce-catalog-enquiry' ); ?></h3>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
		<tr>
			<th scope="col"><?php _e( 'Product', 'woocommerce-catalog-enquiry' ); ?></th>
			<th scope="col"><?php _e( 'Product Url', 'woocommerce-catalog-enquiry' ); ?></th>
			<?php if($product_obj->get_sku()){ ?>
			<th scope="col"><?php _e( 'Product SKU', 'woocommerce-catalog-enquiry' ); ?></th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td scope="col"><?php echo $product_obj->get_name(); ?>
			<?php 
                        if($product_obj->get_type() == 'variation'){
                            if(isset($enquiry_data['variations']) && count($enquiry_data['variations']) > 0 ){
                                foreach ($enquiry_data['variations'] as $label => $value) {
                                    $label = str_replace( 'attribute_pa_', '', $label );
                                    $label = str_replace( 'attribute_', '', $label );
                                    echo "<br>".ucfirst($label).": ".ucfirst($value);
                                } 
                            }else{
                                if($product_obj->get_attributes()){
                                    foreach ($product_obj->get_attributes() as $label => $value) {
                                      echo "<br>".ucfirst(wc_attribute_label($label)).": ".ucfirst($value);
                                    }
                                }
                            }
                        } ?>
			</td>
			<td scope="col"><a href="<?php echo $product_obj->get_permalink(); ?>" target="_blank"><?php echo $product_obj->get_title(); ?></a></td>
			<?php if($product_obj->get_sku()){ ?>
			<td scope="col"><?php echo $product_obj->get_sku(); ?></td>
			<?php } ?>
		</tr>
	</tbody>
</table>

<h3><?php _e( 'Customer Details', 'woocommerce-catalog-enquiry' ); ?></h3>
<p>
	<strong><?php _e( 'Name', 'woocommerce-catalog-enquiry' ); ?> : </strong>
	<?php echo $enquiry_data['cust_name']; ?>
</p>
<p>
	<strong><?php _e( 'Email', 'woocommerce-catalog-enquiry' ); ?> : </strong>
	<a target="_blank" href="mailto:<?php echo $enquiry_data['cust_email']; ?>"><?php echo $enquiry_data['cust_email']; ?></a>
</p>
<?php if(isset($enquiry_data['phone'])){ ?>
<p>
	<strong><?php _e("User Phone : ",'woocommerce-catalog-enquiry'); ?> </strong>
	<?php echo $enquiry_data['phone']; ?>
</p>
<?php } ?>
<?php if(isset($enquiry_data['address'])){ ?>
<p>
	<strong><?php _e( "User Address : ",'woocommerce-catalog-enquiry' ); ?> </strong>
	<?php echo $enquiry_data['address']; ?>
</p>
<?php } ?>
<?php if(isset($enquiry_data['subject'])){ ?>
<p>
	<strong><?php _e( "User Subject : ",'woocommerce-catalog-enquiry' ); ?> </strong>
	<?php echo $enquiry_data['subject']; ?>
</p>
<?php } ?>
<?php if(isset($enquiry_data['comment'])){ ?>
<p>
	<strong><?php _e( "User Comments : ",'woocommerce-catalog-enquiry' ); ?> </strong>
	<?php echo $enquiry_data['comment']; ?>
</p>
<?php } ?>

<table cellspacing="0" cellpadding="10" border="0" width="100%">
	<tbody>
		<tr>
			<td colspan="2" valign="middle" align="center">
			<p><?php echo apply_filters('wc_catalog_enquiry_email_footer_text', sprintf( __( '%s - Powered by WC Catalog Enquiry', 'woocommerce-catalog-enquiry' ), get_bloginfo( 'name', 'display' ) ) );?></a>.</p>
			</td>
		</tr>
	</tbody>
</table>