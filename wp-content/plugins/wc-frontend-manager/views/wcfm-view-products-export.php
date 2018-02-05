<?php
/**
 * WCFM plugin view
 *
 * WCFM Product Export view
 *
 * @author 		WC Lovers
 * @package 	wcfm/views
 * @version   2.4.2
 */
 
global $WCFM;

$wcfm_is_allow_products_export = apply_filters( 'wcfm_is_allow_products_export', true );
$wcfm_is_allow_manage_products = apply_filters( 'wcfm_is_allow_manage_products', true );
if( !current_user_can( 'edit_products' ) || !$wcfm_is_allow_products_export || !$wcfm_is_allow_manage_products ) {
	wcfm_restriction_message_show( "Products Export" );
	return;
}

include_once( WC_ABSPATH . 'includes/export/class-wc-product-csv-exporter.php' );
$exporter        = new WC_Product_CSV_Exporter();
$product_count   = wp_count_posts( 'product' );
$variation_count = wp_count_posts( 'product' );
$total_rows      = $product_count->publish + $product_count->private + $variation_count->publish + $variation_count->private;

?>

<div class="collapse wcfm-collapse" id="wcfm_products_listing">
	
	<div class="wcfm-page-headig">
		<span class="fa fa-download"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Products Export', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php esc_html_e( 'Export products to a CSV file', 'woocommerce' ); ?></h2>
			
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('edit.php?post_type=product&page=product_exporter'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager' ); ?>"><span class="fa fa-wordpress"></span></a>
				<?php
			}
			
			if( $is_allow_products_import = apply_filters( 'wcfm_is_allow_products_import', true ) ) {
				if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
					if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
						?>
						<a class="wcfm_import_export text_tip" href="#" onclick="return false;" data-tip="<?php wcfmu_feature_help_text_show( 'Products Import', false, true ); ?>"><span class="fa fa-upload"></span></a>
						<?php
					}
				} else {
					?>
					<a class="wcfm_import_export text_tip" href="<?php echo get_wcfm_import_product_url(); ?>" data-tip="<?php _e( 'Products Import', 'wc-frontend-manager' ); ?>"><span class="fa fa-upload"></span></a>
					<?php
				}
			}
			
			if( $has_new = apply_filters( 'wcfm_add_new_product_sub_menu', true ) ) {
				echo '<a id="add_new_product_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_edit_product_url().'" data-tip="' . __('Add New Product', 'wc-frontend-manager') . '"><span class="fa fa-cube"></span><span class="text">' . __( 'Add New', 'wc-frontend-manager') . '</span></a>';
			}
			?>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'before_wcfm_products' ); ?>
	  
		<div class="wcfm-container">
			<div id="wcfm_products_export_expander" class="wcfm-content">
			
			  <div class="woocommerce-exporter-wrapper">
					<form class="woocommerce-exporter">
						<header>
							<span class="spinner is-active"></span>
							<p><?php esc_html_e( 'This tool allows you to generate and download a CSV file containing a list of all products.', 'woocommerce' ); ?></p>
						</header>
						<section>
							<table class="form-table woocommerce-exporter-options">
								<tbody>
									<tr>
										<th scope="row">
											<label for="woocommerce-exporter-columns" class="wcfm_title"><?php esc_html_e( 'Which columns should be exported?', 'woocommerce' ); ?></label>
										</th>
										<td>
											<select id="woocommerce-exporter-columns" class="woocommerce-exporter-columns wc-enhanced-select" style="width:100%;" multiple data-placeholder="<?php esc_attr_e( 'Export all columns', 'woocommerce' ); ?>">
												<?php
													foreach ( $exporter->get_default_column_names() as $column_id => $column_name ) {
														echo '<option value="' . esc_attr( $column_id ) . '">' . esc_html( $column_name ) . '</option>';
													}
												?>
												<option value="downloads"><?php esc_html_e( 'Downloads', 'woocommerce' ); ?></option>
												<option value="attributes"><?php esc_html_e( 'Attributes', 'woocommerce' ); ?></option>
											</select>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="woocommerce-exporter-types" class="wcfm_title"><?php esc_html_e( 'Which product types should be exported?', 'woocommerce' ); ?></label>
										</th>
										<td>
											<select id="woocommerce-exporter-types" class="woocommerce-exporter-types wc-enhanced-select" style="width:100%;" multiple data-placeholder="<?php esc_attr_e( 'Export all products', 'woocommerce' ); ?>">
												<?php
													foreach ( wc_get_product_types() as $value => $label ) {
														echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $label ) . '</option>';
													}
												?>
												<option value="variation"><?php esc_html_e( 'Product variations', 'woocommerce' ); ?></option>
											</select>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="woocommerce-exporter-meta" class="wcfm_title"><?php esc_html_e( 'Export custom meta?', 'woocommerce' ); ?></label>
										</th>
										<td>
											<input type="checkbox" id="woocommerce-exporter-meta" value="1" />
											<label for="woocommerce-exporter-meta" class="wcfm_title"><?php esc_html_e( 'Yes, export all custom meta', 'woocommerce' ); ?></label>
										</td>
									</tr>
								</tbody>
							</table>
							<progress class="woocommerce-exporter-progress" max="100" value="0"></progress>
						</section>
						<div class="wc-actions">
							<input type="submit" class="woocommerce-exporter-button wcfm_submit_button" value="<?php esc_attr_e( 'Generate CSV', 'woocommerce' ); ?>" />
						</div>
					</form>
				</div>

				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_products_export' );
		?>
	</div>
</div>