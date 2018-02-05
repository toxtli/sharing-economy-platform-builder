<?php
global $wp, $WCFM, $wc_product_attributes;

$wcfm_is_allow_manage_products = apply_filters( 'wcfm_is_allow_manage_products', true );
if( !current_user_can( 'edit_products' ) || !$wcfm_is_allow_manage_products ) {
	wcfm_restriction_message_show( "Products" );
	return;
}

if( isset( $wp->query_vars['wcfm-products-manage'] ) && empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	if( !apply_filters( 'wcfm_is_allow_add_products', true ) ) {
		wcfm_restriction_message_show( "Add Product" );
		return;
	}
	if( !apply_filters( 'wcfm_is_allow_product_limit', true ) ) {
		wcfm_restriction_message_show( "Product Limit Reached" );
		return;
	}
} elseif( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$wcfm_products_single = get_post( $wp->query_vars['wcfm-products-manage'] );
	if( $wcfm_products_single->post_status == 'publish' ) {
		if( !current_user_can( 'edit_published_products' ) || !apply_filters( 'wcfm_is_allow_edit_products', true ) ) {
			wcfm_restriction_message_show( "Edit Product" );
			return;
		}
	}
	if( wcfm_is_vendor() ) {
		$is_product_from_vendor = $WCFM->wcfm_vendor_support->wcfm_is_product_from_vendor( $wp->query_vars['wcfm-products-manage'] );
		if( !$is_product_from_vendor ) {
			wcfm_restriction_message_show( "Restricted Product" );
			return;
		}
	}
}

$product_id = 0;
$product = array();
$product_type = apply_filters( 'wcfm_default_product_type', '' );
$is_virtual = '';
$title = '';
$sku = '';
$visibility = 'visible';
$excerpt = '';
$description = '';
$regular_price = '';
$sale_price = '';
$sale_date_from = '';
$sale_date_upto = '';
$product_url = '';
$button_text = '';
$is_downloadable = '';
$children = array();

$featured_img = '';
$gallery_img_ids = array();
$gallery_img_urls = array();
$categories = array();
$product_tags = '';
$manage_stock = '';
$stock_qty = 0;
$backorders = '';
$stock_status = ''; 
$sold_individually = '';
$weight = '';
$length = '';
$width = '';
$height = '';
$shipping_class = '';
$tax_status = '';
$tax_class = '';
$attributes = array();
$default_attributes = '';
$attributes_select_type = array();
$variations = array();

$upsell_ids = array();
$crosssell_ids = array();

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	
	$product = wc_get_product( $wp->query_vars['wcfm-products-manage'] );
	// Fetching Product Data
	if($product && !empty($product)) {
		$product_id = $wp->query_vars['wcfm-products-manage'];
		$wcfm_products_single = get_post($product_id);
		$product_type = $product->get_type();
		$title = $product->get_title();
		$sku = $product->get_sku();
		//$visibility = get_post_meta( $product_id, '_visibility', true);
		$excerpt = wpautop( $product->get_short_description() );
		$description = wpautop( $product->get_description() );
		$regular_price = $product->get_regular_price();
		$sale_price = $product->get_sale_price();
		
		$rich_editor = apply_filters( 'wcfm_is_allow_rich_editor', 'rich_editor' );
		if( !$rich_editor ) {
			$breaks = apply_filters( 'wcfm_editor_newline_generators', array("<br />","<br>","<br/>") ); 
			
			$excerpt = str_ireplace( $breaks, "\r\n", $excerpt );
			$excerpt = strip_tags( $excerpt );
			
			$description = str_ireplace( $breaks, "\r\n", $description );
			$description = strip_tags( $description );
		}
		
		// External product option
		$product_url = get_post_meta( $product_id, '_product_url', true);
		$button_text = get_post_meta( $product_id, '_button_text', true);
		
		// Virtual
		$is_virtual = ( get_post_meta( $product_id, '_virtual', true) == 'yes' ) ? 'enable' : '';
		
		// Download ptions
		$is_downloadable = ( get_post_meta( $product_id, '_downloadable', true) == 'yes' ) ? 'enable' : '';
		if( $product_type != 'simple' ) $is_downloadable = '';
		
		// Product Images
		$featured_img = ($product->get_image_id()) ? $product->get_image_id() : '';
		if($featured_img) $featured_img = wp_get_attachment_url($featured_img);
		if(!$featured_img) $featured_img = '';
		$gallery_img_ids = $product->get_gallery_image_ids();
		if(!empty($gallery_img_ids)) {
			foreach($gallery_img_ids as $gallery_img_id) {
				$gallery_img_urls[]['image'] = wp_get_attachment_url($gallery_img_id);
			}
		}
		
		// Product Categories
		$pcategories = get_the_terms( $product_id, 'product_cat' );
		if( !empty($pcategories) ) {
			foreach($pcategories as $pkey => $pcategory) {
				$categories[] = $pcategory->term_id;
			}
		} else {
			$categories = array();
		}
		
		// Product Tags
		$product_tag_list = wp_get_post_terms($product_id, 'product_tag', array("fields" => "names"));
		$product_tags = implode(',', $product_tag_list);
		
		// Product Stock options
		$manage_stock = $product->managing_stock() ? 'enable' : '';
		$stock_qty = $product->get_stock_quantity();
		$backorders = $product->get_backorders();
		$stock_status = $product->get_stock_status(); 
		$sold_individually = $product->is_sold_individually() ? 'enable' : '';
		
		// Product Shipping Data
		$weight = $product->get_weight();
		$length = $product->get_length();
		$width = $product->get_width();
		$height = $product->get_height();
		$shipping_class = $product->get_shipping_class_id();
		
		// Product Tax Data
		$tax_status = $product->get_tax_status();
		$tax_class = $product->get_tax_class();
		
		// Product Attributes
		$wcfm_attributes = get_post_meta( $product_id, '_product_attributes', true );
		if(!empty($wcfm_attributes)) {
			$acnt = 0;
			foreach($wcfm_attributes as $wcfm_attribute) {
				
				if ( $wcfm_attribute['is_taxonomy'] ) {
					$att_taxonomy = $wcfm_attribute['name'];

					if ( ! taxonomy_exists( $att_taxonomy ) ) {
						continue;
					}
					
					$attribute_taxonomy = $wc_product_attributes[ $att_taxonomy ];
					
					$attributes[$acnt]['term_name'] = $att_taxonomy;
					$attributes[$acnt]['name'] = wc_attribute_label( $att_taxonomy );
					$attributes[$acnt]['attribute_taxonomy'] = $attribute_taxonomy;
					$attributes[$acnt]['tax_name'] = $att_taxonomy;
					$attributes[$acnt]['is_taxonomy'] = 1;
					
					if ( 'select' === $attribute_taxonomy->attribute_type ) {
						$args = array(
										'orderby'    => 'name',
										'hide_empty' => 0
									);
						$all_terms = get_terms( $att_taxonomy, apply_filters( 'wcfm_product_attribute_terms', $args ) );
						$attributes_option = array();
						if ( $all_terms ) {
							foreach ( $all_terms as $term ) {
								$attributes_option[$term->term_id] = esc_attr( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) );
							}
						}
						$attributes[$acnt]['attribute_type'] = 'select';
						$attributes[$acnt]['option_values'] = $attributes_option;
						$attributes[$acnt]['value'] = wp_get_post_terms( $product_id, $att_taxonomy, array( 'fields' => 'ids' ) );
					} else {
						$attributes[$acnt]['attribute_type'] = 'text';
						$attributes[$acnt]['value'] = esc_attr( implode( ' ' . WC_DELIMITER . ' ', wp_get_post_terms( $product_id, $att_taxonomy, array( 'fields' => 'names' ) ) ) );
					}
				} else {
					$attributes[$acnt]['term_name'] = apply_filters( 'woocommerce_attribute_label', $wcfm_attribute['name'], $wcfm_attribute['name'], $product );
					$attributes[$acnt]['name'] = apply_filters( 'woocommerce_attribute_label', $wcfm_attribute['name'], $wcfm_attribute['name'], $product );
					$attributes[$acnt]['value'] = $wcfm_attribute['value'];
					$attributes[$acnt]['tax_name'] = '';
					$attributes[$acnt]['is_taxonomy'] = 0;
					$attributes[$acnt]['attribute_type'] = 'text';
				}
				
				$attributes[$acnt]['is_visible'] = $wcfm_attribute['is_visible'] ? 'enable' : '';
				$attributes[$acnt]['is_variation'] = $wcfm_attribute['is_variation'] ? 'enable' : '';
				
				if( 'select' === $attributes[$acnt]['attribute_type'] ) {
					$attributes_select_type[$acnt] = $attributes[$acnt];
					unset($attributes[$acnt]);
				}
				$acnt++;
			}
		}
		
		// Product Default Attributes
		$default_attributes = json_encode( (array) get_post_meta( $product_id, '_default_attributes', true ) );
		
		// Variable Product Variations
		$variation_ids = $product->get_children();
		if(!empty($variation_ids)) {
			foreach($variation_ids as $variation_id_key => $variation_id) {
				$variation_data = new WC_Product_Variation($variation_id);
				
				$variations[$variation_id_key]['id'] = $variation_id;
				$variations[$variation_id_key]['enable'] = $variation_data->is_purchasable() ? 'enable' : '';
				$variations[$variation_id_key]['sku'] = $variation_data->get_sku();
				
				// Variation Image
				$variation_img = $variation_data->get_image_id();
				if($variation_img) $variation_img = wp_get_attachment_url($variation_img);
				else $variation_img = '';
				$variations[$variation_id_key]['image'] = $variation_img;
				
				// Variation Price
				$variations[$variation_id_key]['regular_price'] = $variation_data->get_regular_price();
				$variations[$variation_id_key]['sale_price'] = $variation_data->get_sale_price();
				
				// Variation Stock Data
				$variations[$variation_id_key]['manage_stock'] = $variation_data->managing_stock() ? 'enable' : '';
				$variations[$variation_id_key]['stock_status'] = $variation_data->get_stock_status();
				$variations[$variation_id_key]['stock_qty'] = $variation_data->get_stock_quantity();
				$variations[$variation_id_key]['backorders'] = $variation_data->get_backorders();
				
				// Variation Virtual Data
				$variations[$variation_id_key]['is_virtual'] = ( 'yes' == get_post_meta($variation_id, '_virtual', true) ) ? 'enable' : '';
				
				// Variation Downloadable Data
				$variations[$variation_id_key]['is_downloadable'] = ( 'yes' == get_post_meta($variation_id, '_downloadable', true) ) ? 'enable' : '';
				$variations[$variation_id_key]['downloadable_files'] = get_post_meta($variation_id, '_downloadable_files', true);
				$variations[$variation_id_key]['download_limit'] = ( -1 == get_post_meta($variation_id, '_download_limit', true) ) ? '' : get_post_meta($variation_id, '_download_limit', true);
				$variations[$variation_id_key]['download_expiry'] = ( -1 == get_post_meta($variation_id, '_download_expiry', true) ) ? '' : get_post_meta($variation_id, '_download_expiry', true);
				if(!empty($variations[$variation_id_key]['downloadable_files'])) {
					foreach($variations[$variation_id_key]['downloadable_files'] as $variations_downloadable_files) {
						$variations[$variation_id_key]['downloadable_file'] = $variations_downloadable_files['file'];
						$variations[$variation_id_key]['downloadable_file_name'] = $variations_downloadable_files['name'];
					}
				}
				
				// Variation Shipping Data
				$variations[$variation_id_key]['weight'] = $variation_data->get_weight();
				$variations[$variation_id_key]['length'] = $variation_data->get_length();
				$variations[$variation_id_key]['width'] = $variation_data->get_width();
				$variations[$variation_id_key]['height'] = $variation_data->get_height();
				$variations[$variation_id_key]['shipping_class'] = $variation_data->get_shipping_class_id();
				
				// Variation Tax
				$variations[$variation_id_key]['tax_class'] = $variation_data->get_tax_class();
				
				// Variation Attributes
				$variations[$variation_id_key]['attributes'] = json_encode( $variation_data->get_variation_attributes() );
				
				// Description
				$variations[$variation_id_key]['description'] = get_post_meta($variation_id, '_variation_description', true);
				
				$variations = apply_filters( 'wcfm_variation_edit_data', $variations, $variation_id, $variation_id_key );
			}
		}
		
		$upsell_ids = get_post_meta( $product_id, '_upsell_ids', true ) ? get_post_meta( $product_id, '_upsell_ids', true ) : array();
		$crosssell_ids = get_post_meta( $product_id, '_crosssell_ids', true ) ? get_post_meta( $product_id, '_crosssell_ids', true ) : array();
		$children = get_post_meta( $product_id, '_children', true ) ? get_post_meta( $product_id, '_children', true ) : array();
	}
}

$current_user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );

// Shipping Class List
$product_shipping_class = get_terms( 'product_shipping_class', array('hide_empty' => 0));
$product_shipping_class = apply_filters( 'wcfm_product_shipping_class', $product_shipping_class );
$variation_shipping_option_array = array('-1' => __('Same as parent', 'wc-frontend-manager'));
$shipping_option_array = array('_no_shipping_class' => __('No shipping class', 'wc-frontend-manager'));
if( $product_shipping_class && !empty( $product_shipping_class ) ) {
	foreach($product_shipping_class as $product_shipping) {
		$variation_shipping_option_array[$product_shipping->term_id] = $product_shipping->name;
		$shipping_option_array[$product_shipping->term_id] = $product_shipping->name;
	}
}

// Tax Class List
$tax_classes         = WC_Tax::get_tax_classes();
$classes_options     = array();
$variation_tax_classes_options['parent'] = __( 'Same as parent', 'wc-frontend-manager' );
$variation_tax_classes_options[''] = __( 'Standard', 'wc-frontend-manager' );
$tax_classes_options[''] = __( 'Standard', 'wc-frontend-manager' );

if ( ! empty( $tax_classes ) ) {

	foreach ( $tax_classes as $class ) {
		$tax_classes_options[ sanitize_title( $class ) ] = esc_html( $class );
		$variation_tax_classes_options[ sanitize_title( $class ) ] = esc_html( $class );
	}
}

$args = array(
	'posts_per_page'   => -1,
	'offset'           => 0,
	'category'         => '',
	'category_name'    => '',
	'orderby'          => 'date',
	'order'            => 'DESC',
	'include'          => '',
	'exclude'          => '',
	'meta_key'         => '',
	'meta_value'       => '',
	'post_type'        => 'product',
	'post_mime_type'   => '',
	'post_parent'      => '',
	//'author'	   => get_current_user_id(),
	'post_status'      => array('publish'),
	'suppress_filters' => 0 
);
$args = apply_filters( 'wcfm_products_args', $args );

$products_objs = get_posts( $args );
$products_array = array();
if( !empty($products_objs) ) {
	foreach( $products_objs as $products_obj ) {
		$product_data      = wc_get_product( $products_obj->ID );
		$products_array[esc_attr( $products_obj->ID )] = (!empty($product_data)) ? wp_kses_post( $product_data->get_formatted_name() ) : $products_obj->ID;
	}
}
$product_types = apply_filters( 'wcfm_product_types', array('simple' => __('Simple Product', 'wc-frontend-manager'), 'variable' => __('Variable Product', 'wc-frontend-manager'), 'grouped' => __('Grouped Product', 'wc-frontend-manager'), 'external' => __('External/Affiliate Product', 'wc-frontend-manager') ) );
$product_categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0&parent=0' );

$product_type_class = '';
if( count( $product_types ) == 0 ) {
	$product_types = array('simple' => __('Simple Product', 'wc-frontend-manager') );
	$product_type_class = 'wcfm_custom_hide';
} elseif( count( $product_types ) == 1 ) {
	$product_type_class = 'wcfm_custom_hide';
}
?>

<div class="collapse wcfm-collapse" id="">
  <div class="wcfm-page-headig">
		<span class="fa fa-cube"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Manage Product', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		<?php do_action( 'before_wcfm_product_simple' ); ?>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php if( $product_id ) { _e('Edit Product', 'wc-frontend-manager' ); } else { _e('Add Product', 'wc-frontend-manager' ); } ?></h2>
			<?php
			if( $product_id ) {
				?>
				<span class="product-status product-status-<?php echo $wcfm_products_single->post_status; ?>"><?php if( $wcfm_products_single->post_status == 'publish' ) { _e( 'Published', 'wc-frontend-manager' ); } else { echo ucfirst( $wcfm_products_single->post_status ); } ?></span>
				<?php
				if( $wcfm_products_single->post_status == 'publish' ) {
					echo '<a target="_blank" href="' . get_permalink( $wcfm_products_single->ID ) . '">';
					?>
					<span class="view_count"><span class="fa fa-eye text_tip" data-tip="<?php _e( 'Views', 'wc-frontend-manager' ); ?>"></span>
					<?php
					echo get_post_meta( $wcfm_products_single->ID, '_wcfm_product_views', true ) . '</span></a>';
				} else {
					echo '<a target="_blank" href="' . get_permalink( $wcfm_products_single->ID ) . '">';
					?>
					<span class="view_count"><span class="fa fa-eye text_tip" data-tip="<?php _e( 'Preview', 'wc-frontend-manager' ); ?>"></span>
					<?php
					echo '</a>';
				}
			}
			
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('post-new.php?post_type=product'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager' ); ?>"><span class="fa fa-wordpress"></span></a>
				<?php
			}
			
			if( $has_new = apply_filters( 'wcfm_add_new_product_sub_menu', true ) ) {
				echo '<a id="add_new_product_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_edit_product_url().'" data-tip="' . __('Add New Product', 'wc-frontend-manager') . '"><span class="fa fa-cube"></span><span class="text">' . __( 'Add New', 'wc-frontend-manager') . '</span></a>';
			}
			?>
			
			<div class="wcfm-clearfix"></div>
		</div>
		<div class="wcfm-clearfix"></div><br />
		
		<form id="wcfm_products_manage_form" class="wcfm">
		
			<?php do_action( 'begin_wcfm_products_manage_form' ); ?>
			
			<!-- collapsible -->
			<div class="wcfm-container simple variable external grouped booking">
				<div id="wcfm_products_manage_form_general_expander" class="wcfm-content">
				  <div class="wcfm_product_manager_general_fields">
						<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_product_manage_fields_general', array(
																																																"product_type" => array('type' => 'select', 'options' => $product_types, 'class' => 'wcfm-select wcfm_ele wcfm_product_type simple variable external grouped booking ' . $product_type_class, 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $product_type ),
																																																"is_virtual" => array('desc' => __('Virtual', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele wcfm_half_ele_checkbox simple booking non-variable-subscription non-job_package non-resume_package non-auction non-redq_rental non-accommodation-booking', 'desc_class' => 'wcfm_title wcfm_ele virtual_ele_title checkbox_title simple booking non-variable-subscription non-job_package non-resume_package non-auction non-redq_rental non-accommodation-booking', 'value' => 'enable', 'dfvalue' => $is_virtual),
																																																"title" => array( 'placeholder' => __('Product Title', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_product_title wcfm_full_ele simple variable external grouped booking', 'value' => $title),
																																																//"visibility"     => array('label' => __('Visibility', 'wc-frontend-manager'), 'type' => 'select', 'options' => array('visible' => __('Catalog/Search', 'wc-frontend-manager'), 'catalog' => __('Catalog', 'wc-frontend-manager'), 'search' => __('Search', 'wc-frontend-manager'), 'hidden' => __('Hidden', 'wc-frontend-manager')), 'class' => 'wcfm-select wcfm_ele wcfm_half_ele wcfm_half_ele_right simple variable external', 'label_class' => 'wcfm_ele wcfm_half_ele_title wcfm_title simple variable external', 'value' => $visibility, 'hints' => __('Choose where this product should be displayed in your catalog. The product will always be accessible directly.', 'wc-frontend-manager'))
																																													), $product_id, $product_type ) );
							
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_product_manage_fields_pricing', array(
																																																"product_url" => array('label' => __('URL', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_half_ele external', 'label_class' => 'wcfm_ele wcfm_half_ele_title wcfm_title external', 'value' => $product_url, 'hints' => __( 'Enter the external URL to the product.', 'wc-frontend-manager' )),
																																																"button_text" => array('label' => __('Button Text', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_half_ele wcfm_half_ele_right external', 'label_class' => 'wcfm_ele wcfm_half_ele_title wcfm_title external', 'value' => $button_text, 'hints' => __( 'This text will be shown on the button linking to the external product.', 'wc-frontend-manager' )),
																																																"regular_price" => array('label' => __('Price', 'wc-frontend-manager') . '(' . get_woocommerce_currency_symbol() . ')', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_half_ele simple external non-subscription non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_half_ele_title wcfm_title simple external non-subscription non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'value' => $regular_price ),
																																																"sale_price" => array('label' => __('Sale Price', 'wc-frontend-manager') . '(' . get_woocommerce_currency_symbol() . ')', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_half_ele wcfm_half_ele_right simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_half_ele_title wcfm_title simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking', 'value' => $sale_price, 'desc_class' => 'wcfm_ele simple external non-variable-subscription non-auction non-redq_rental non-accommodation-booking sales_schedule', 'desc' => __( 'schedule', 'wc-frontend-manager' ) ),
																																													), $product_id, $product_type ) );		
							
							// Sales scheduling missing message
							if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
								if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
									echo '<div class="sales_schedule_ele">';
									wcfmu_feature_help_text_show( __( 'Sales scheduling', 'wc-frontend-manager' ) );
									echo '</div>';
								}
							}
																																																
							
						?>
						<div class="wcfm_clearfix"></div>
						
						<?php if( !$wcfm_is_category_checklist = apply_filters( 'wcfm_is_category_checklist', true ) ) { ?>
						  <?php if( $wcfm_is_allow_category = apply_filters( 'wcfm_is_allow_category', true ) ) { $catlimit = apply_filters( 'wcfm_catlimit', -1 ); ?>
								<p class="wcfm_title"><strong><?php _e( 'Categories', 'wc-frontend-manager' ); ?></strong></p><label class="screen-reader-text" for="product_cats"><?php _e( 'Categories', 'wc-frontend-manager' ); ?></label>
								<select id="product_cats" name="product_cats[]" class="wcfm-select wcfm_ele simple variable external grouped booking" multiple="multiple" data-catlimit="<?php echo $catlimit; ?>" style="width: 100%; margin-bottom: 10px;">
									<?php
										if ( $product_categories ) {
											$this->generateTaxonomyHTML( 'product_cat', $product_categories, $categories );
										}
									?>
								</select>
							
								<?php
								if( $wcfm_is_allow_custom_taxonomy = apply_filters( 'wcfm_is_allow_custom_taxonomy', true ) ) {
									$product_taxonomies = get_object_taxonomies( 'product', 'objects' );
									if( !empty( $product_taxonomies ) ) {
										foreach( $product_taxonomies as $product_taxonomy ) {
											if( !in_array( $product_taxonomy->name, array( 'product_cat', 'product_tag', 'wcpv_product_vendors' ) ) ) {
												if( $product_taxonomy->public && $product_taxonomy->show_ui && $product_taxonomy->meta_box_cb && $product_taxonomy->hierarchical ) {
													// Fetching Saved Values
													$taxonomy_values_arr = array();
													if($product && !empty($product)) {
														$taxonomy_values = get_the_terms( $product_id, $product_taxonomy->name );
														if( !empty($taxonomy_values) ) {
															foreach($taxonomy_values as $pkey => $ptaxonomy) {
																$taxonomy_values_arr[] = $ptaxonomy->term_id;
															}
														}
													}
													?>
													<p class="wcfm_title"><strong><?php _e( $product_taxonomy->label, 'wc-frontend-manager' ); ?></strong></p><label class="screen-reader-text" for="<?php echo $product_taxonomy->name; ?>"><?php _e( $product_taxonomy->label, 'wc-frontend-manager' ); ?></label>
													<select id="<?php echo $product_taxonomy->name; ?>" name="product_custom_taxonomies[<?php echo $product_taxonomy->name; ?>][]" class="wcfm-select product_taxonomies wcfm_ele simple variable external grouped booking" multiple="multiple" style="width: 100%; margin-bottom: 10px;">
														<?php
															$product_taxonomy_terms   = get_terms( $product_taxonomy->name, 'orderby=name&hide_empty=0&parent=0' );
															if ( $product_taxonomy_terms ) {
																$this->generateTaxonomyHTML( $product_taxonomy->name, $product_taxonomy_terms, $taxonomy_values_arr );
															}
														?>
													</select>
													<?php
												}
											}
										}
									}
								}
							}
							
							if( $wcfm_is_allow_tags = apply_filters( 'wcfm_is_allow_tags', true ) ) {
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_simple_fields_tag', array(  "product_tags" => array('label' => __('Tags', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele wcfm_full_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_full_ele', 'value' => $product_tags, 'placeholder' => __('Separate Product Tags with commas', 'wc-frontend-manager'))
																																														) ) );
								
								if( $wcfm_is_allow_custom_taxonomy = apply_filters( 'wcfm_is_allow_custom_taxonomy', true ) ) {
									$product_taxonomies = get_object_taxonomies( 'product', 'objects' );
									if( !empty( $product_taxonomies ) ) {
										foreach( $product_taxonomies as $product_taxonomy ) {
											if( !in_array( $product_taxonomy->name, array( 'product_cat', 'product_tag', 'wcpv_product_vendors' ) ) ) {
												if( $product_taxonomy->public && $product_taxonomy->show_ui && $product_taxonomy->meta_box_cb && !$product_taxonomy->hierarchical ) {
													// Fetching Saved Values
													$taxonomy_values_arr = wp_get_post_terms($product_id, $product_taxonomy->name, array("fields" => "names"));
													$taxonomy_values = implode(',', $taxonomy_values_arr);
													$WCFM->wcfm_fields->wcfm_generate_form_field( array(  $product_taxonomy->name => array( 'label' => $product_taxonomy->label, 'name' => 'product_custom_taxonomies_flat[' . $product_taxonomy->name . '][]', 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele wcfm_full_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_full_ele', 'value' => $taxonomy_values, 'placeholder' => __('Separate Product ' . $product_taxonomy->label . ' with commas', 'wc-frontend-manager') )
																																			) );
												}
											}
										}
									}
								}
							}
							?>
						<?php } ?>
						<?php if( $wcfm_is_category_checklist = apply_filters( 'wcfm_is_category_checklist', true ) ) { ?>
							<div class="wcfm_clearfix"></div><br />
							<div class="wcfm_product_manager_content_fields">
								<?php
								$rich_editor = apply_filters( 'wcfm_is_allow_rich_editor', 'rich_editor' );
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_product_manage_fields_content', array(
																																																			"excerpt" => array('label' => __('Short Description', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele wcfm_full_ele simple variable external grouped booking ' . $rich_editor , 'label_class' => 'wcfm_title wcfm_full_ele', 'value' => $excerpt),
																																																			"description" => array('label' => __('Description', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele wcfm_full_ele simple variable external grouped booking ' . $rich_editor, 'label_class' => 'wcfm_title wcfm_full_ele', 'value' => $description),
																																																			"pro_id" => array('type' => 'hidden', 'value' => $product_id)
																																															), $product_id, $product_type ) );
								?>
							</div>
						<?php } ?>
					</div>
					<div class="wcfm_product_manager_gallery_fields">
					  <?php
					  if( $wcfm_is_allow_featured = apply_filters( 'wcfm_is_allow_featured', true ) ) {
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_product_manage_fields_gallery', array(  "featured_img" => array( 'type' => 'upload', 'class' => 'wcfm-product-feature-upload wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'prwidth' => 250, 'value' => $featured_img)
																																													), $gallery_img_urls ) );
							
							// Product Gallary missing message
							if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
								if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
									wcfmu_feature_help_text_show( __( 'Image Gallery', 'wc-frontend-manager' ), false, true );
								}
							}
						}
						?>
					
						<?php if( $wcfm_is_category_checklist = apply_filters( 'wcfm_is_category_checklist', true ) ) { ?>
							<?php 
							if( $wcfm_is_allow_category = apply_filters( 'wcfm_is_allow_category', true ) ) { 
								$catlimit = apply_filters( 'wcfm_catlimit', -1 ); 
								?>
								<div class="wcfm_clearfix"></div>
								<div class="wcfm_product_manager_cats_checklist_fields">
									<p class="wcfm_title wcfm_full_ele"><strong><?php _e( 'Categories', 'wc-frontend-manager' ); ?></strong></p><label class="screen-reader-text" for="product_cats"><?php _e( 'Categories', 'wc-frontend-manager' ); ?></label>
									<ul id="product_cats_checklist" class="product_taxonomy_checklist wcfm_ele simple variable external grouped booking" data-catlimit="<?php echo $catlimit; ?>">
										<?php
											if ( $product_categories ) {
												$this->generateTaxonomyHTML( 'product_cat', $product_categories, $categories, '', true );
											}
										?>
									</ul>
								</div>
								<?php
								if( $wcfm_is_allow_custom_taxonomy = apply_filters( 'wcfm_is_allow_custom_taxonomy', true ) ) {
									$product_taxonomies = get_object_taxonomies( 'product', 'objects' );
									if( !empty( $product_taxonomies ) ) {
										foreach( $product_taxonomies as $product_taxonomy ) {
											if( !in_array( $product_taxonomy->name, array( 'product_cat', 'product_tag', 'wcpv_product_vendors' ) ) ) {
												if( $product_taxonomy->public && $product_taxonomy->show_ui && $product_taxonomy->meta_box_cb && $product_taxonomy->hierarchical ) {
													// Fetching Saved Values
													$taxonomy_values_arr = array();
													if($product && !empty($product)) {
														$taxonomy_values = get_the_terms( $product_id, $product_taxonomy->name );
														if( !empty($taxonomy_values) ) {
															foreach($taxonomy_values as $pkey => $ptaxonomy) {
																$taxonomy_values_arr[] = $ptaxonomy->term_id;
															}
														}
													}
													?>
													<div class="wcfm_clearfix"></div>
													<div class="wcfm_product_manager_cats_checklist_fields wcfm_product_taxonomy_<?php echo $product_taxonomy->name; ?>">
														<p class="wcfm_title wcfm_full_ele"><strong><?php _e( $product_taxonomy->label, 'wc-frontend-manager' ); ?></strong></p><label class="screen-reader-text" for="<?php echo $product_taxonomy->name; ?>"><?php _e( $product_taxonomy->label, 'wc-frontend-manager' ); ?></label>
														<ul id="<?php echo $product_taxonomy->name; ?>" class="product_taxonomy_checklist wcfm_ele simple variable external grouped booking">
															<?php
																$product_taxonomy_terms   = get_terms( $product_taxonomy->name, 'orderby=name&hide_empty=0&parent=0' );
																if ( $product_taxonomy_terms ) {
																	$this->generateTaxonomyHTML( $product_taxonomy->name, $product_taxonomy_terms, $taxonomy_values_arr, '', true, true );
																}
															?>
														</ul>
													</div>
													<?php
												}
											}
										}
									}
								}
							}
							
							if( $wcfm_is_allow_tags = apply_filters( 'wcfm_is_allow_tags', true ) ) {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_simple_fields_tag', array(  "product_tags" => array('label' => __('Tags', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele wcfm_full_ele product_tags_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_full_ele product_tags_ele', 'value' => $product_tags, 'placeholder' => __('Separate Product Tags with commas', 'wc-frontend-manager'))
																																															) ) );
									
									if( $wcfm_is_allow_custom_taxonomy = apply_filters( 'wcfm_is_allow_custom_taxonomy', true ) ) {
										$product_taxonomies = get_object_taxonomies( 'product', 'objects' );
										if( !empty( $product_taxonomies ) ) {
											foreach( $product_taxonomies as $product_taxonomy ) {
												if( !in_array( $product_taxonomy->name, array( 'product_cat', 'product_tag', 'wcpv_product_vendors' ) ) ) {
													if( $product_taxonomy->public && $product_taxonomy->show_ui && $product_taxonomy->meta_box_cb && !$product_taxonomy->hierarchical ) {
														// Fetching Saved Values
														$taxonomy_values_arr = wp_get_post_terms($product_id, $product_taxonomy->name, array("fields" => "names"));
														$taxonomy_values = implode(',', $taxonomy_values_arr);
														$WCFM->wcfm_fields->wcfm_generate_form_field( array(  $product_taxonomy->name => array( 'label' => $product_taxonomy->label, 'name' => 'product_custom_taxonomies_flat[' . $product_taxonomy->name . '][]', 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele wcfm_full_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_full_ele', 'value' => $taxonomy_values, 'placeholder' => __('Separate Product ' . $product_taxonomy->label . ' with commas', 'wc-frontend-manager') )
																																				) );
													}
												}
											}
										}
									}
								}
							?>
						<?php } ?>
						
						<?php do_action( 'wcfm_product_manager_gallery_fields_end', $product_id ); ?>
					</div>
				</div>
				
				<?php if( !$wcfm_is_category_checklist = apply_filters( 'wcfm_is_category_checklist', true ) ) { ?>
					<div class="wcfm-content">
						<div class="wcfm_product_manager_content_fields">
							<?php
							$rich_editor = apply_filters( 'wcfm_is_allow_rich_editor', 'rich_editor' );
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_product_manage_fields_content', array(
																																																		"excerpt" => array('label' => __('Short Description', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele wcfm_full_ele simple variable external grouped booking ' . $rich_editor , 'label_class' => 'wcfm_title wcfm_full_ele', 'value' => $excerpt),
																																																		"description" => array('label' => __('Description', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele wcfm_full_ele simple variable external grouped booking ' . $rich_editor, 'label_class' => 'wcfm_title wcfm_full_ele', 'value' => $description),
																																																		"pro_id" => array('type' => 'hidden', 'value' => $product_id)
																																														), $product_id, $product_type ) );
							?>
						</div>
					</div>
				<?php } ?>
			</div>
			<!-- end collapsible -->
			<div class="wcfm_clearfix"></div><br />
			
			<!-- wrap -->
			<div class="wcfm-tabWrap">
				<?php if( apply_filters( 'wcfm_is_allow_inventory', true ) ) { ?>
				<!-- collapsible 2 -->
				<div class="page_collapsible products_manage_inventory simple variable grouped external non-job_package non-resume_package non-auction non-appointment non-accommodation-booking" id="wcfm_products_manage_form_inventory_head"><label class="fa fa-database"></label><?php _e('Inventory', 'wc-frontend-manager'); ?><span></span></div>
				<div class="wcfm-container simple variable grouped external non-job_package non-resume_package non-auction non-appointment non-accommodation-booking">
					<div id="wcfm_products_manage_form_inventory_expander" class="wcfm-content">
						<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_product_fields_stock', array(
																																																		"sku" => array('label' => __('SKU', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text', 'label_class' => 'wcfm_title', 'value' => $sku, 'hints' => __( 'SKU refers to a Stock-keeping unit, a unique identifier for each distinct product and service that can be purchased.', 'wc-frontend-manager' )),
																																																		"manage_stock" => array('label' => __('Manage Stock?', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele simple variable manage_stock_ele non-job_package non-resume_package non-auction non-redq_rental non-appointment non-accommodation-booking', 'value' => 'enable', 'label_class' => 'wcfm_title wcfm_ele checkbox_title simple variable non-job_package non-resume_package non-auction non-redq_rental non-appointment non-accommodation-booking', 'hints' => __('Enable stock management at product level', 'wc-frontend-manager'), 'dfvalue' => $manage_stock),
																																																		"stock_qty" => array('label' => __('Stock Qty', 'wc-frontend-manager') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele simple variable non_manage_stock_ele non-job_package non-resume_package non-auction non-redq_rental non-appointment non-accommodation-booking non-accommodation-booking', 'label_class' => 'wcfm_title wcfm_ele simple variable non_manage_stock_ele non-job_package non-resume_package non-auction non-redq_rental non-appointment non-accommodation-booking', 'value' => $stock_qty, 'hints' => __( 'Stock quantity. If this is a variable product this value will be used to control stock for all variations, unless you define stock at variation level.', 'wc-frontend-manager' )),
																																																		"backorders" => array('label' => __('Allow Backorders?', 'wc-frontend-manager') , 'type' => 'select', 'options' => array('no' => __('Do not Allow', 'wc-frontend-manager'), 'notify' => __('Allow, but notify customer', 'wc-frontend-manager'), 'yes' => __('Allow', 'wc-frontend-manager')), 'class' => 'wcfm-select wcfm_ele simple variable non_manage_stock_ele non-job_package non-resume_package non-auction non-redq_rental non-appointment non-accommodation-booking', 'label_class' => 'wcfm_title wcfm_ele simple variable non_manage_stock_ele non-job_package non-resume_package non-auction non-redq_rental non-appointment non-accommodation-booking', 'value' => $backorders, 'hints' => __( 'If managing stock, this controls whether or not backorders are allowed. If enabled, stock quantity can go below 0.', 'wc-frontend-manager' )),
																																																		"stock_status" => array('label' => __('Stock status', 'wc-frontend-manager') , 'type' => 'select', 'options' => array('instock' => __('In stock', 'wc-frontend-manager'), 'outofstock' => __('Out of stock', 'wc-frontend-manager')), 'class' => 'wcfm-select wcfm_ele simple variable grouped non-variable-subscription non-job_package non-resume_package non-auction non-redq_rental non-appointment non-accommodation-booking', 'label_class' => 'wcfm_ele wcfm_title simple variable grouped non-variable-subscription non-job_package non-resume_package non-auction non-redq_rental non-appointment non-accommodation-booking', 'value' => $stock_status, 'hints' => __( 'Controls whether or not the product is listed as "in stock" or "out of stock" on the frontend.', 'wc-frontend-manager' )),
																																																		"sold_individually" => array('label' => __('Sold Individually', 'wc-frontend-manager') , 'type' => 'checkbox', 'value' => 'enable', 'class' => 'wcfm-checkbox wcfm_ele simple variable non-job_package non-resume_package non-auction non-redq_rental non-appointment non-accommodation-booking', 'hints' => __('Enable this to only allow one of this item to be bought in a single order', 'wc-frontend-manager'), 'label_class' => 'wcfm_title wcfm_ele simple variable checkbox_title non-job_package non-resume_package non-auction non-redq_rental non-appointment non-accommodation-booking', 'dfvalue' => $sold_individually)
																																														), $product_id, $product_type ) );
						?>
					</div>
				</div>
				<!-- end collapsible -->
				<div class="wcfm_clearfix"></div>
				<?php } ?>
				
				<?php do_action( 'after_wcfm_products_manage_general', $product_id, $product_type ); ?>
				
				<!-- collapsible 3 - Grouped Product -->
				<div class="page_collapsible products_manage_grouped grouped" id="wcfm_products_manage_form_grouped_head"><label class="fa fa-object-group"></label><?php _e('Grouped Products', 'wc-frontend-manager'); ?><span></span></div>
				<div class="wcfm-container grouped">
					<div id="wcfm_products_manage_form_grouped_expander" class="wcfm-content">
						<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_manage_fields_grouped', array(  
																																																"grouped_products" => array('label' => __('Grouped products', 'wc-frontend-manager') , 'type' => 'select', 'attributes' => array( 'multiple' => 'multiple', 'style' => 'width: 60%;' ), 'class' => 'wcfm-select wcfm_ele grouped', 'label_class' => 'wcfm_title wcfm_ele grouped', 'options' => $products_array, 'value' => $children, 'hints' => __( 'This lets you choose which products are part of this group.', 'wc-frontend-manager' ))
																																											)) );
						?>
					</div>
				</div>
				<!-- end collapsible -->
				<div class="wcfm_clearfix"></div>
				
				<?php if( $allow_shipping = apply_filters( 'wcfm_is_allow_shipping', true ) ) { ?>
				<!-- collapsible 4 -->
				<div class="page_collapsible products_manage_shipping simple variable nonvirtual booking non-accommodation-booking" id="wcfm_products_manage_form_shipping_head"><label class="fa fa-truck"></label><?php _e('Shipping', 'wc-frontend-manager'); ?><span></span></div>
				<div class="wcfm-container simple variable nonvirtual booking non-accommodation-booking">
					<div id="wcfm_products_manage_form_shipping_expander" class="wcfm-content">
						<?php do_action( 'wcfm_product_manage_fields_shipping_before', $product_id ); ?>
						<div class="wcfm_clearfix"></div>
						<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_product_manage_fields_shipping', array(  "weight" => array( 'label' => __('Weight', 'wc-frontend-manager') . ' ('.get_option( 'woocommerce_weight_unit', 'kg' ).')' , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable booking', 'label_class' => 'wcfm_title', 'value' => $weight),
																																																	"length" => array( 'label' => __('Dimensions', 'wc-frontend-manager') . ' ('.get_option( 'woocommerce_dimension_unit', 'cm' ).')', 'placeholder' => __('Length', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable booking', 'label_class' => 'wcfm_title', 'value' => $length),
																																																	"width" => array( 'placeholder' => __('Width', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable booking', 'label_class' => 'wcfm_title', 'value' => $width),
																																																	"height" => array( 'placeholder' => __('Height', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable booking', 'label_class' => 'wcfm_title', 'value' => $height),
																																																	"shipping_class" => array('label' => __('Shipping class', 'wc-frontend-manager') , 'type' => 'select', 'options' => $shipping_option_array, 'class' => 'wcfm-select wcfm_ele simple variable booking', 'label_class' => 'wcfm_title', 'value' => $shipping_class)
																																												), $product_id ) );
						?>
						<div class="wcfm_clearfix"></div>
						<?php do_action( 'wcfm_product_manage_fields_shipping_after', $product_id ); ?>
					</div>
				</div>
				<!-- end collapsible -->
				<div class="wcfm_clearfix"></div>
				<?php } ?>
				
				<?php if( $allow_tax = apply_filters( 'wcfm_is_allow_tax', true ) ) { ?>
				<?php if ( wc_tax_enabled() ) { ?>
				<!-- collapsible 5 -->
				<div class="page_collapsible products_manage_tax simple variable" id="wcfm_products_manage_form_tax_head"><label class="fa fa-paypal"></label><?php _e('Tax', 'wc-frontend-manager'); ?><span></span></div>
				<div class="wcfm-container simple variable">
					<div id="wcfm_products_manage_form_tax_expander" class="wcfm-content">
						<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_simple_fields_tax', array(  
																																																	"tax_status" => array('label' => __('Tax Status', 'wc-frontend-manager') , 'type' => 'select', 'options' => array( 'taxable' => __( 'Taxable', 'wc-frontend-manager' ), 'shipping' => __( 'Shipping only', 'wc-frontend-manager' ), 'none' => _x( 'None', 'Tax status', 'wc-frontend-manager' ) ), 'class' => 'wcfm-select wcfm_ele simple variable', 'label_class' => 'wcfm_title', 'value' => $tax_status, 'hints' => __( 'Define whether or not the entire product is taxable, or just the cost of shipping it.', 'wc-frontend-manager' )),
																																																	"tax_class" => array('label' => __('Tax Class', 'wc-frontend-manager') , 'type' => 'select', 'options' => $tax_classes_options, 'class' => 'wcfm-select wcfm_ele simple variable', 'label_class' => 'wcfm_title', 'value' => $tax_class, 'hints' => __( 'Choose a tax class for this product. Tax classes are used to apply different tax rates specific to certain types of product.', 'wc-frontend-manager' ))
																																												)) );
						?>
					</div>
				</div>
				<!-- end collapsible -->
				<div class="wcfm_clearfix"></div>
				<?php } ?>
				<?php } ?>
				
				<?php if( $allow_attribute = apply_filters( 'wcfm_is_allow_attribute', true ) ) { ?>
				<!-- collapsible 6 -->
				<div class="page_collapsible products_manage_attribute simple variable external grouped booking" id="wcfm_products_manage_form_attribute_head"><label class="fa fa-server"></label><?php _e('Attributes', 'wc-frontend-manager'); ?><span></span></div>
				<div class="wcfm-container simple variable external grouped booking">
					<div id="wcfm_products_manage_form_attribute_expander" class="wcfm-content">
						<?php 
						if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
							?>
							<p>
								<select name="wcfm_attribute_taxonomy" class="wcfm-select wcfm_attribute_taxonomy">
									<option value="add_attribute"><?php _e( 'Add attribute', 'wc-frontend-manager' ); ?></option>
									<?php if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) { ?>
									  <option value="_blank" disabled><?php wcfmu_feature_help_text_show( __( 'Custom Attributes', 'wc-frontend-manager' ), false, true ); ?></option>
									<?php } ?>
								</select>
								<button type="button" class="button wcfm_add_attribute"><?php _e( 'Add', 'wc-frontend-manager' ); ?></button>
							</p>
							<?php
						} else {
							do_action( 'wcfm_products_manage_attributes' ); 
						}
						?>
						<div class="wcfm_clearfix"></div><br />
						<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_simple_fields_attributes', array(  
																																															"attributes" => array( 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'has_dummy' => true, 'label_class' => 'wcfm_title', 'value' => $attributes, 'options' => array(
																																																	"term_name" => array('type' => 'hidden'),
																																																	"name" => array('label' => __('Name', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title'),
																																																	"value" => array('label' => __('Value(s):', 'wc-frontend-manager'), 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele simple variable external grouped booking', 'placeholder' => __('Enter some text, some attributes by "|" separating values.', 'wc-frontend-manager'), 'label_class' => 'wcfm_title'),
																																																	"is_visible" => array('label' => __('Visible on the product page', 'wc-frontend-manager'), 'type' => 'checkbox', 'value' => 'enable', 'class' => 'wcfm-checkbox wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title checkbox_title'),
																																																	"is_variation" => array('label' => __('Use as Variation', 'wc-frontend-manager'), 'type' => 'checkbox', 'value' => 'enable', 'class' => 'wcfm-checkbox wcfm_ele variable variable-subscription', 'label_class' => 'wcfm_title checkbox_title wcfm_ele variable variable-subscription'),
																																																	"tax_name" => array('type' => 'hidden'),
																																																	"is_taxonomy" => array('type' => 'hidden')
																																															))
																																										)) );
							
							if( !empty($attributes_select_type) ) {
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_simple_fields_attributes', array(  
																																															"select_attributes" => array( 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'value' => $attributes_select_type, 'options' => array(
																																																	"term_name" => array('type' => 'hidden'),
																																																	"name" => array('label' => __('Name', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title'),
																																																	"value" => array('label' => __('Value(s):', 'wc-frontend-manager'), 'type' => 'select', 'attributes' => array( 'multiple' => 'multiple', 'style' => 'width: 60%;' ), 'class' => 'wcfm-select wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title'),
																																																	"is_visible" => array('label' => __('Visible on the product page', 'wc-frontend-manager'), 'type' => 'checkbox', 'value' => 'enable', 'class' => 'wcfm-checkbox wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title checkbox_title'),
																																																	"is_variation" => array('label' => __('Use as Variation', 'wc-frontend-manager'), 'type' => 'checkbox', 'value' => 'enable', 'class' => 'wcfm-checkbox wcfm_ele variable variable-subscription', 'label_class' => 'wcfm_title checkbox_title wcfm_ele variable variable-subscription'),
																																																	"tax_name" => array('type' => 'hidden'),
																																																	"is_taxonomy" => array('type' => 'hidden')
																																															))
																																										)) );
							}
						?>
					</div>
				</div>
				<!-- end collapsible -->
				<div class="wcfm_clearfix"></div>
				<?php } ?>
				
				<?php if( $allow_attribute = apply_filters( 'wcfm_is_allow_variable', true ) ) { ?>
				<!-- collapsible 7 -->
				<div class="page_collapsible products_manage_variations variable variations variable-subscription" id="wcfm_products_manage_form_variations_head"><label class="fa fa-tasks"></label><?php _e('Variations', 'wc-frontend-manager'); ?><span></span></div>
				<div class="wcfm-container variable variable-subscription">
				  <div id="wcfm_products_manage_form_variations_empty_expander" class="wcfm-content">
				    <?php printf( __( 'Before you can add a variation you need to add some variation attributes on the Attributes tab. %sLearn more%s', 'wc-frontend-manager' ), '<br /><h2><a href="https://docs.woocommerce.com/document/variable-product/">', '</a></h2>' ); ?>
				  </div>
					<div id="wcfm_products_manage_form_variations_expander" class="wcfm-content">
					  <p>
							<div class="default_attributes_holder">
							  <p class="wcfm_title selectbox_title"><strong><?php _e( 'Default Form Values:', 'wc-frontend-manager' ); ?></strong></p>
								<input type="hidden" name="default_attributes_hidden" data-name="default_attributes_hidden" value="<?php echo esc_attr( $default_attributes ); ?>" />
							</div>
						</p>
						<p>
						  <p class="variations_options wcfm_title"><strong><?php _e('Variations Bulk Options', 'wc-frontend-manager'); ?></strong></p>
						  <label class="screen-reader-text" for="variations_options"><?php _e('Variations Bulk Options', 'wc-frontend-manager'); ?></label>
						  <select id="variations_options" name="variations_options" class="wcfm-select wcfm_ele variable-subscription variable">
						    <option value="" selected="selected"><?php _e( 'Choose option', 'wc-frontend-manager' ); ?></option>
						    <optgroup label="<?php _e( 'Pricing', 'wc-frontend-manager' ); ?>">
									<option value="set_regular_price"><?php _e( 'Regular prices', 'wc-frontend-manager' ); ?></option>
									<option value="regular_price_increase"><?php _e( 'Regular price increase', 'wc-frontend-manager' ); ?></option>
									<option value="regular_price_decrease"><?php _e( 'Regular price decrease', 'wc-frontend-manager' ); ?></option>
									<option value="set_sale_price"><?php _e( 'Sale prices', 'wc-frontend-manager' ); ?></option>
									<option value="sale_price_increase"><?php _e( 'Sale price increase', 'wc-frontend-manager' ); ?></option>
									<option value="sale_price_decrease"><?php _e( 'Sale price decrease', 'wc-frontend-manager' ); ?></option>
								</optgroup>
								<optgroup label="<?php _e( 'Shipping', 'wc-frontend-manager' ); ?>">
								  <option value="set_length"><?php _e( 'Length', 'wc-frontend-manager' ); ?></option>
								  <option value="set_width"><?php _e( 'Width', 'wc-frontend-manager' ); ?></option>
								  <option value="set_height"><?php _e( 'Height', 'wc-frontend-manager' ); ?></option>
								  <option value="set_weight"><?php _e( 'Weight', 'wc-frontend-manager' ); ?></option>
								</optgroup>
						  </select>
						</p>
						<?php
						 $WCFM->wcfm_fields->wcfm_generate_form_field( array(  
																																	"variations" => array('type' => 'multiinput', 'class' => 'wcfm_ele variable variable-subscription', 'label_class' => 'wcfm_title', 'value' => $variations, 'options' => 
																																			apply_filters( 'wcfm_product_manage_fields_variations', array(
																																			"id" => array('type' => 'hidden', 'class' => 'variation_id'),
																																			"enable" => array('label' => __('Enable', 'wc-frontend-manager'), 'type' => 'checkbox', 'value' => 'enable', 'dfvalue' => 'enable', 'class' => 'wcfm-checkbox wcfm_ele variable variable-subscription', 'label_class' => 'wcfm_title checkbox_title'),
																																			"manage_stock" => array('label' => __('Manage Stock', 'wc-frontend-manager'), 'type' => 'checkbox', 'value' => 'enable', 'value' => 'enable', 'class' => 'wcfm-checkbox wcfm_ele variable variable-subscription variation_manage_stock_ele', 'label_class' => 'wcfm_title checkbox_title'),
																																			"image" => array('label' => __('Image', 'wc-frontend-manager'), 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele variable variable-subscription', 'label_class' => 'wcfm_title wcfm_half_ele_upload_title'),
																																			"regular_price" => array('label' => __('Regular Price', 'wc-frontend-manager') . '(' . get_woocommerce_currency_symbol() . ')', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_half_ele variable', 'label_class' => 'wcfm_title wcfm_ele wcfm_half_ele_title variable'),
																																			"sale_price" => array('label' => __('Sale Price', 'wc-frontend-manager') . '(' . get_woocommerce_currency_symbol() . ')', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_half_ele variable variable-subscription', 'label_class' => 'wcfm_title wcfm_ele wcfm_half_ele_title variable variable-subscription'),
																																			"stock_qty" => array('label' => __('Stock Qty', 'wc-frontend-manager') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele wcfm_half_ele variable variable-subscription variation_non_manage_stock_ele', 'label_class' => 'wcfm_title wcfm_half_ele_title variation_non_manage_stock_ele'),
																																			"backorders" => array('label' => __('Backorders?', 'wc-frontend-manager') , 'type' => 'select', 'options' => array('no' => __('Do not Allow', 'wc-frontend-manager'), 'notify' => __('Allow, but notify customer', 'wc-frontend-manager'), 'yes' => __('Allow', 'wc-frontend-manager')), 'class' => 'wcfm-select wcfm_ele wcfm_half_ele variable variable-subscription variation_non_manage_stock_ele', 'label_class' => 'wcfm_title wcfm_half_ele_title variation_non_manage_stock_ele'),
																																			"sku" => array('label' => __('SKU', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele wcfm_half_ele variable variable-subscription', 'label_class' => 'wcfm_title wcfm_half_ele_title'),
																																			"stock_status" => array('label' => __('Stock status', 'wc-frontend-manager') , 'type' => 'select', 'options' => array('instock' => __('In stock', 'wc-frontend-manager'), 'outofstock' => __('Out of stock', 'wc-frontend-manager')), 'class' => 'wcfm-select wcfm_ele wcfm_half_ele variable variable-subscription', 'label_class' => 'wcfm_title wcfm_half_ele_title'), 
																																			"attributes" => array('type' => 'hidden')
																																	), $variations, $variation_shipping_option_array, $variation_tax_classes_options, $products_array ) )
																												) );
						?>
					</div>
				</div>
				<!-- end collapsible -->
				<div class="wcfm_clearfix"></div>
				<?php } ?>
				
				<?php if( $allow_advanced = apply_filters( 'wcfm_is_allow_linked', true ) ) { ?>
					<!-- collapsible 8 - Linked Product -->
					<div class="page_collapsible products_manage_linked simple variable external grouped" id="wcfm_products_manage_form_linked_head"><label class="fa fa-link"></label><?php _e('Linked', 'wc-frontend-manager'); ?><span></span></div>
					<div class="wcfm-container simple variable external grouped">
						<div id="wcfm_products_manage_form_linked_expander" class="wcfm-content">
							<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_manage_fields_linked', array(  
																																																	"upsell_ids" => array('label' => __('Up-sells', 'wc-frontend-manager') , 'type' => 'select', 'attributes' => array( 'multiple' => 'multiple', 'style' => 'width: 60%;' ), 'class' => 'wcfm-select wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'options' => $products_array, 'value' => $upsell_ids, 'hints' => __( 'Up-sells are products which you recommend instead of the currently viewed product, for example, products that are more profitable or better quality or more expensive.', 'wc-frontend-manager' )),
																																																	"crosssell_ids" => array('label' => __('Cross-sells', 'wc-frontend-manager') , 'type' => 'select', 'attributes' => array( 'multiple' => 'multiple', 'style' => 'width: 60%;' ), 'class' => 'wcfm-select wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'options' => $products_array, 'value' => $crosssell_ids, 'hints' => __( 'Cross-sells are products which you promote in the cart, based on the current product.', 'wc-frontend-manager' ))
																																												), $product_id, $products_array ) );
							?>
						</div>
					</div>
					<!-- end collapsible -->
					<div class="wcfm_clearfix"></div>
				<?php } ?>
				
				<?php do_action( 'end_wcfm_products_manage', $product_id ); ?>
			
			</div> <!-- tabwrap -->
			
			<div id="wcfm_products_simple_submit" class="wcfm_form_simple_submit_wrapper">
			  <div class="wcfm-message" tabindex="-1"></div>
			  
			  <?php if( $product_id && ( $wcfm_products_single->post_status == 'publish' ) ) { ?>
				  <input type="submit" name="submit-data" value="<?php if( apply_filters( 'wcfm_is_allow_publish_live_products', true ) ) { _e( 'Submit', 'wc-frontend-manager' ); } else { _e( 'Submit for Review', 'wc-frontend-manager' ); } ?>" id="wcfm_products_simple_submit_button" class="wcfm_submit_button" />
				<?php } else { ?>
					<input type="submit" name="submit-data" value="<?php if( current_user_can( 'publish_products' ) && apply_filters( 'wcfm_is_allow_publish_products', true ) ) { _e( 'Submit', 'wc-frontend-manager' ); } else { _e( 'Submit for Review', 'wc-frontend-manager' ); } ?>" id="wcfm_products_simple_submit_button" class="wcfm_submit_button" />
				<?php } ?>
				<?php if( apply_filters( 'wcfm_is_allow_draft_published_products', true ) && apply_filters( 'wcfm_is_allow_add_products', true ) ) { ?>
				  <input type="submit" name="draft-data" value="<?php _e( 'Draft', 'wc-frontend-manager' ); ?>" id="wcfm_products_simple_draft_button" class="wcfm_submit_button" />
				<?php } ?>
				
				<?php
				if( $product_id && ( $wcfm_products_single->post_status != 'publish' ) ) {
					echo '<a target="_blank" href="' . get_permalink( $wcfm_products_single->ID ) . '">';
					?>
					<input type="button" class="wcfm_submit_button" value="<?php _e( 'Preview', 'wc-frontend-manager' ); ?>" />
					<?php
					echo '</a>';
				} elseif( $product_id && ( $wcfm_products_single->post_status == 'publish' ) ) {
					echo '<a target="_blank" href="' . get_permalink( $wcfm_products_single->ID ) . '">';
					?>
					<input type="button" class="wcfm_submit_button" value="<?php _e( 'View', 'wc-frontend-manager' ); ?>" />
					<?php
					echo '</a>';
				}
				?>
			</div>
		</form>
		<?php
		do_action( 'after_wcfm_products_manage' );
		?>
	</div>
</div>