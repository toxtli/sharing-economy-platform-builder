<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Products Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   1.0.0
 */

class WCFM_Products_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$wcfm_products_manage_form_data = array();
	  parse_str($_POST['wcfm_products_manage_form'], $wcfm_products_manage_form_data);
	  //print_r($wcfm_products_manage_form_data);
	  $wcfm_products_manage_messages = get_wcfm_products_manager_messages();
	  $has_error = false;
	  
	  if(isset($wcfm_products_manage_form_data['title']) && !empty($wcfm_products_manage_form_data['title'])) {
	  	$is_update = false;
	  	$is_publish = false;
	  	$current_user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
	  	                  
	  	if(isset($_POST['status']) && ($_POST['status'] == 'draft')) {
	  		$product_status = 'draft';
	  	} else {
	  		if( current_user_can( 'publish_products' ) )
	  			$product_status = 'publish';
	  		else
	  		  $product_status = 'pending';
			}
	  	
	  	// Creating new product
			$new_product = apply_filters( 'wcfm_product_content_before_save', array(
				'post_title'   => wc_clean( $wcfm_products_manage_form_data['title'] ),
				'post_status'  => $product_status,
				'post_type'    => 'product',
				'post_excerpt' => apply_filters( 'wcfm_editor_content_before_save', stripslashes( html_entity_decode( $_POST['excerpt'], ENT_QUOTES, 'UTF-8' ) ) ),
				'post_content' => apply_filters( 'wcfm_editor_content_before_save', stripslashes( html_entity_decode( $_POST['description'], ENT_QUOTES, 'UTF-8' ) ) ),
				'post_author'  => $current_user_id
				//'post_name' => sanitize_title($wcfm_products_manage_form_data['title'])
			), $wcfm_products_manage_form_data );
			
			if(isset($wcfm_products_manage_form_data['pro_id']) && $wcfm_products_manage_form_data['pro_id'] == 0) {
				if ($product_status != 'draft') {
					$is_publish = true;
				}
				$new_product_id = wp_insert_post( $new_product, true );
			} else { // For Update
				$is_update = true;
				$new_product['ID'] = $wcfm_products_manage_form_data['pro_id'];
				unset( $new_product['post_author'] );
				if( get_post_status( $new_product['ID'] ) == 'publish' ) {
					if( apply_filters( 'wcfm_is_allow_publish_live_products', true ) ) {
						$new_product['post_status'] = 'publish';
					}
				} else if( (get_post_status( $new_product['ID'] ) == 'draft') && ($product_status != 'draft') ) {
					$is_publish = true;
				}
				$new_product_id = wp_update_post( $new_product, true );
			}
			
			if(!is_wp_error($new_product_id)) {
				// For Update
				if($is_update) $new_product_id = $wcfm_products_manage_form_data['pro_id'];
				
				// Set Product SKU
				if(isset($wcfm_products_manage_form_data['sku']) && !empty($wcfm_products_manage_form_data['sku'])) {
					update_post_meta( $new_product_id, '_sku', $wcfm_products_manage_form_data['sku'] );
					if( !$is_update ) {
						$unique_sku = wc_product_has_unique_sku( $new_product_id, $wcfm_products_manage_form_data['sku'] );
						if ( ! $unique_sku ) {
							update_post_meta( $new_product_id, '_sku', '' );
							echo '{"status": false, "message": "' . $wcfm_products_manage_messages['sku_unique'] . '", "id": "' . $new_product_id . '", "redirect": "' . get_permalink( $new_product_id ) . '"}';
							$has_error = true;
						}
					}
				} else {
				  update_post_meta( $new_product_id, '_sku', '' );
				}
				  
				// Set Product Type
				wp_set_object_terms( $new_product_id, $wcfm_products_manage_form_data['product_type'], 'product_type' );
				
				// Group Products
				$grouped_products = isset( $wcfm_products_manage_form_data['grouped_products'] ) ? array_filter( array_map( 'intval', (array) $wcfm_products_manage_form_data['grouped_products'] ) ) : array();
				
				// Attributes
				$pro_attributes = array();
				$default_attributes = array();
				if(isset($wcfm_products_manage_form_data['attributes']) && !empty($wcfm_products_manage_form_data['attributes'])) {
					foreach($wcfm_products_manage_form_data['attributes'] as $attributes) {
						if(!empty($attributes['name']) && !empty($attributes['value'])) {
							
							$attribute_name = ( $attributes['term_name'] ) ? $attributes['term_name'] : $attributes['name'];
							
							$is_visible = 0;
							if(isset($attributes['is_visible'])) $is_visible = 1;
							
							$is_variation = 0;
							if(isset($attributes['is_variation'])) $is_variation = 1;
							if( ( $wcfm_products_manage_form_data['product_type'] != 'variable' ) && ( $wcfm_products_manage_form_data['product_type'] != 'variable-subscription' ) ) $is_variation = 0;
							
							$is_taxonomy = 0;
							if($attributes['is_taxonomy'] == 1) $is_taxonomy = 1;
							
							$term_name = '';
							if( $is_taxonomy == 1 ) $term_name = $attributes['term_name'];
							
							$attribute_id   = wc_attribute_taxonomy_id_by_name( $term_name );
							$options = isset( $attributes['value'] ) ? $attributes['value'] : '';
							
							if ( is_array( $options ) ) {
								// Term ids sent as array.
								$options = wp_parse_id_list( $options );
							} else {
								// Terms or text sent in textarea.
								$options = 0 < $attribute_id ? wc_sanitize_textarea( wc_sanitize_term_text_based( $options ) ) : wc_sanitize_textarea( $options );
								$options = wc_get_text_attributes( $options );
							}
			
							if ( empty( $options ) ) {
								continue;
							}
							
							$attribute = new WC_Product_Attribute();
							$attribute->set_id( $attribute_id );
							$attribute->set_name( wc_clean( $attribute_name ) );
							$attribute->set_options( $options );
							//$attribute->set_position( $attribute_position );
							$attribute->set_visible( $is_visible );
							$attribute->set_variation(  $is_variation );
							$pro_attributes[] = $attribute;
							
							if( $is_variation ) {
								//$attribute_key = $attribute_name;
								//$value                        = $attribute->is_taxonomy() ? sanitize_title( $value ) : wc_clean( $value ); // Don't use wc_clean as it destroys sanitized characters in terms.
								//$default_attributes[ $attribute_key ] = $value;
							}
						}
					}
				}
				
				// Set default Attributes
				if( isset( $wcfm_products_manage_form_data['default_attributes'] ) && !empty( $wcfm_products_manage_form_data['default_attributes'] ) ) {
					$default_attributes = array();
					if ( $pro_attributes ) {
						foreach ( $pro_attributes as $p_attribute ) {
							if ( $p_attribute->get_variation() ) {
								$attribute_key = sanitize_title( $p_attribute->get_name() );
								
								$value = isset( $wcfm_products_manage_form_data['default_attributes'][ "attribute_" . $attribute_key ] ) ? stripslashes( $wcfm_products_manage_form_data['default_attributes'][ "attribute_" . $attribute_key ] ) : '';
			
								$value                        = $p_attribute->is_taxonomy() ? sanitize_title( $value ) : wc_clean( $value ); // Don't use wc_clean as it destroys sanitized characters in terms.
								$default_attributes[ $attribute_key ] = $value;
							}
						}
					}
				}
				
				// Process product type first so we have the correct class to run setters.
				$product_type = empty( $wcfm_products_manage_form_data['product_type'] ) ? WC_Product_Factory::get_product_type( $new_product_id ) : sanitize_title( stripslashes( $wcfm_products_manage_form_data['product_type'] ) );
				$classname    = WC_Product_Factory::get_product_classname( $new_product_id, $product_type ? $product_type : 'simple' );
				$product      = new $classname( $new_product_id );
				$errors       = $product->set_props( apply_filters( 'wcfm_product_data_factory', array(
					'virtual'            => isset( $wcfm_products_manage_form_data['is_virtual'] ),
					'sku'                => isset( $wcfm_products_manage_form_data['sku'] ) ? wc_clean( $wcfm_products_manage_form_data['sku'] ) : null,
					'tax_status'         => isset( $wcfm_products_manage_form_data['tax_status'] ) ? wc_clean( $wcfm_products_manage_form_data['tax_status'] ) : null,
					'tax_class'          => isset( $wcfm_products_manage_form_data['tax_class'] ) ? wc_clean( $wcfm_products_manage_form_data['tax_class'] ) : null,
					'weight'             => wc_clean( $wcfm_products_manage_form_data['weight'] ),
					'length'             => wc_clean( $wcfm_products_manage_form_data['length'] ),
					'width'              => wc_clean( $wcfm_products_manage_form_data['width'] ),
					'height'             => wc_clean( $wcfm_products_manage_form_data['height'] ),
					'shipping_class_id'  => absint( $wcfm_products_manage_form_data['shipping_class'] ),
					'sold_individually'  => ! empty( $wcfm_products_manage_form_data['sold_individually'] ),
					'upsell_ids'         => isset( $wcfm_products_manage_form_data['upsell_ids'] ) ? array_map( 'intval', (array) $wcfm_products_manage_form_data['upsell_ids'] ) : array(),
					'cross_sell_ids'     => isset( $wcfm_products_manage_form_data['crosssell_ids'] ) ? array_map( 'intval', (array) $wcfm_products_manage_form_data['crosssell_ids'] ) : array(),
					'regular_price'      => wc_clean( $wcfm_products_manage_form_data['regular_price'] ),
					'sale_price'         => wc_clean( $wcfm_products_manage_form_data['sale_price'] ),
					'manage_stock'       => ! empty( $wcfm_products_manage_form_data['manage_stock'] ),
					'backorders'         => wc_clean( $wcfm_products_manage_form_data['backorders'] ),
					'stock_status'       => wc_clean( $wcfm_products_manage_form_data['stock_status'] ),
					'stock_quantity'     => wc_stock_amount( $wcfm_products_manage_form_data['stock_qty'] ),
					'product_url'        => esc_url_raw( $wcfm_products_manage_form_data['product_url'] ),
					'button_text'        => wc_clean( $wcfm_products_manage_form_data['button_text'] ),
					'children'           => 'grouped' === $product_type ? $grouped_products : null,
					'attributes'         => $pro_attributes,
					'default_attributes' => $default_attributes,
					'reviews_allowed'    => 'enable'
				), $new_product_id, $product, $wcfm_products_manage_form_data ) );
		
				if ( is_wp_error( $errors ) ) {
					if( !$has_error )
						echo '{"status": false, "message": "' . $errors->get_error_message() . '", "id": "' . $new_product_id . '", "redirect": "' . get_permalink( $new_product_id ) . '"}';
					$has_error = true;
				}
				
				
				/**
				 * @since WC 3.0.0 to set props before save.
				 */
				//do_action( 'woocommerce_admin_process_product_object', $product );
				$product->save();
				
				// Set Product Category
				if(isset($wcfm_products_manage_form_data['product_cats']) && !empty($wcfm_products_manage_form_data['product_cats'])) {
					$is_first = true;
					foreach($wcfm_products_manage_form_data['product_cats'] as $product_cats) {
						if($is_first) {
							$is_first = false;
							wp_set_object_terms( $new_product_id, (int)$product_cats, 'product_cat' );
						} else {
							wp_set_object_terms( $new_product_id, (int)$product_cats, 'product_cat', true );
						}
					}
				}
				
				// Set Product Custom Taxonomies
				if(isset($wcfm_products_manage_form_data['product_custom_taxonomies']) && !empty($wcfm_products_manage_form_data['product_custom_taxonomies'])) {
					foreach($wcfm_products_manage_form_data['product_custom_taxonomies'] as $taxonomy => $taxonomy_values) {
						if( !empty( $taxonomy_values ) ) {
							$is_first = true;
							foreach( $taxonomy_values as $taxonomy_value ) {
								if($is_first) {
									$is_first = false;
									wp_set_object_terms( $new_product_id, (int)$taxonomy_value, $taxonomy );
								} else {
									wp_set_object_terms( $new_product_id, (int)$taxonomy_value, $taxonomy, true );
								}
							}
						}
					}
				}
				
				// Set Product Tags
				if(isset($wcfm_products_manage_form_data['product_tags']) && !empty($wcfm_products_manage_form_data['product_tags'])) {
					wp_set_post_terms( $new_product_id, $wcfm_products_manage_form_data['product_tags'], 'product_tag' );
				}
				
				// Set Product Custom Taxonomies Flat
				if(isset($wcfm_products_manage_form_data['product_custom_taxonomies_flat']) && !empty($wcfm_products_manage_form_data['product_custom_taxonomies_flat'])) {
					foreach($wcfm_products_manage_form_data['product_custom_taxonomies_flat'] as $taxonomy => $taxonomy_values) {
						if( !empty( $taxonomy_values ) ) {
							wp_set_post_terms( $new_product_id, $taxonomy_values, $taxonomy );
						}
					}
				}
				
				// For Update
				/*if($is_update) {
					// Remove post thumbnail
					$post_thumbnail_id = get_post_thumbnail_id( $new_product_id );
					if($post_thumbnail_id) wp_delete_attachment( $post_thumbnail_id );
					
					// Clean Img Gallery
					$galleries = get_post_meta( $new_product_id, '_product_image_gallery', true );
					if($galleries) $gallery_arr = explode(',', $galleries);
					if(!empty($gallery_arr)) {
						foreach($gallery_arr as $gallery_img_id) {
							wp_delete_attachment( $gallery_img_id );
						}
					}
				}*/
				
				// Set Product Featured Image
				if(isset($wcfm_products_manage_form_data['featured_img']) && !empty($wcfm_products_manage_form_data['featured_img'])) {
					$featured_img_id = $WCFM->wcfm_get_attachment_id($wcfm_products_manage_form_data['featured_img']);
					set_post_thumbnail( $new_product_id, $featured_img_id );
				} elseif(isset($wcfm_products_manage_form_data['featured_img']) && empty($wcfm_products_manage_form_data['featured_img'])) {
					delete_post_thumbnail( $new_product_id );
				}
				
				// Set product basic options for simple and external products
				if( ( $wcfm_products_manage_form_data['product_type'] == 'variable' ) || ( $wcfm_products_manage_form_data['product_type'] == 'variable-subscription' ) ) {
					// Create Variable Product Variations
					if(isset($wcfm_products_manage_form_data['variations']) && !empty($wcfm_products_manage_form_data['variations'])) {
					  foreach($wcfm_products_manage_form_data['variations'] as $variations) {
					  	$variation_status     = isset( $variations['enable'] ) ? 'publish' : 'private';
					  	$variation_id = absint ( $variations['id'] );
					  	
					  	// Generate a useful post title
					  	$variation_post_title = sprintf( __( 'Variation #%s of %s', 'woocommerce' ), absint( $variation_id ), esc_html( get_the_title( $new_product_id ) ) );
					  	
					  	if ( ! $variation_id ) { // Adding New Variation
								$variation = array(
									'post_title'   => $variation_post_title,
									'post_content' => '',
									'post_status'  => $variation_status,
									'post_author'  => $current_user_id,
									'post_parent'  => $new_product_id,
									'post_type'    => 'product_variation'
								);
						
								$variation_id = wp_insert_post( $variation );
							}
							
							// Only continue if we have a variation ID
							if ( ! $variation_id ) {
								continue;                                   
							}
							
							// Set Variation Thumbnail
							$variation_img_id = 0;
							if(isset($variations['image']) && !empty($variations['image'])) {
								$variation_img_id = $WCFM->wcfm_get_attachment_id($variations['image']);
							}
							
							// Update Attributes
							$updated_attribute_keys = array();
							$var_attributes = array();
							if ( $pro_attributes ) {
								foreach ( $pro_attributes as $p_attribute ) {
									if ( $p_attribute->get_variation() ) {
										$attribute_key = sanitize_title( $p_attribute->get_name() );
										
										$updated_attribute_keys[] = "attribute_" . $attribute_key;
										$value = isset( $variations[ "attribute_" . $attribute_key ] ) ? stripslashes( $variations[ "attribute_" . $attribute_key ] ) : '';
					
										$value                        = $p_attribute->is_taxonomy() ? sanitize_title( $value ) : wc_clean( $value ); // Don't use wc_clean as it destroys sanitized characters in terms.
										$var_attributes[ $attribute_key ] = $value;
									}
								}
							}
							
							$wc_variation    = new WC_Product_Variation( $variation_id );
							$errors       = $wc_variation->set_props( apply_filters( 'wcfm_product_variation_data_factory', array(
								//'status'            => 'publish' //isset( $variations['enable'] ) ? 'publish' : 'private',
								'menu_order'        => wc_clean( $variations['menu_order'] ),
								'regular_price'     => wc_clean( $variations['regular_price'] ),
								'sale_price'        => wc_clean( $variations['sale_price'] ),
								'manage_stock'      => isset( $variations['manage_stock'] ),
								'stock_quantity'    => wc_clean( $variations['stock_qty'] ),
								'backorders'        => wc_clean( $variations['backorders'] ),
								'stock_status'      => wc_clean( $variations['stock_status'] ),
								'image_id'          => wc_clean( $variation_img_id ),
								'attributes'        => $var_attributes,
								'sku'               => isset( $variations['sku'] ) ? wc_clean( $variations['sku'] ) : '',
							), $new_product_id, $variation_id, $variations, $wcfm_products_manage_form_data ) );
			
							if ( is_wp_error( $errors ) ) {
								if( !$has_error ) 
									echo '{"status": false, "message": "' . $errors->get_error_message() . '", "id": "' . $new_product_id . '", "redirect": "' . get_permalink( $new_product_id ) . '"}';
								$has_error = true;
							}
			
							$wc_variation->save();
							
							do_action( 'after_wcfm_product_variation_meta_save', $new_product_id, $variation_id, $variations, $wcfm_products_manage_form_data );
						}
					}
					
					// Remove Variations
					if(isset($_POST['removed_variations']) && !empty($_POST['removed_variations'])) {
						foreach($_POST['removed_variations'] as $removed_variations) {
							wp_delete_post($removed_variations, true);
						}
					}
					
					$product->get_data_store()->sync_variation_names( $product, wc_clean( $wcfm_products_manage_form_data['title'] ), wc_clean( $wcfm_products_manage_form_data['title'] ) );
				}
				
				do_action( 'after_wcfm_products_manage_meta_save', $new_product_id, $wcfm_products_manage_form_data );
				
				// Notify Admin on New Product Creation
				if( $is_publish ) {
					// Have to test before adding action
				} 
				
				if(!$has_error) {
					if( get_post_status( $new_product_id ) == 'publish' ) {
						if(!$has_error) echo '{"status": true, "message": "' . apply_filters( 'product_published_message', $wcfm_products_manage_messages['product_published'], $new_product_id ) . '", "redirect": "' . apply_filters( 'wcfm_product_save_publish_redirect', get_permalink( $new_product_id ), $new_product_id ) . '", "id": "' . $new_product_id . '", "title": "' . get_the_title( $new_product_id ) . '"}';	
					} elseif( get_post_status( $new_product_id ) == 'pending' ) {
						if(!$has_error) echo '{"status": true, "message": "' . apply_filters( 'product_pending_message', $wcfm_products_manage_messages['product_pending'], $new_product_id ) . '", "redirect": "' . apply_filters( 'wcfm_product_save_pending_redirect', get_wcfm_products_url( 'pending' ), $new_product_id ) . '", "id": "' . $new_product_id . '", "title": "' . get_the_title( $new_product_id ) . '"}';
					} else {
						if(!$has_error) echo '{"status": true, "message": "' . apply_filters( 'product_saved_message', $wcfm_products_manage_messages['product_saved'], $new_product_id ) . '", "redirect": "' . apply_filters( 'wcfm_product_save_draft_redirect', get_wcfm_edit_product_url( $new_product_id ), $new_product_id ) . '", "id": "' . $new_product_id . '"}';
					}
				}
				die;
			}
		} else {
			echo '{"status": false, "message": "' . $wcfm_products_manage_messages['no_title'] . '"}';
		}
	  die;
	}
}