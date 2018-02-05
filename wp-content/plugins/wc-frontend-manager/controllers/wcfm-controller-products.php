<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Products Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   1.0.0
 */

class WCFM_Products_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
		
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$args = array(
							'posts_per_page'   => $length,
							'offset'           => $offset,
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
							'post_status'      => array('draft', 'pending', 'publish'),
							'suppress_filters' => 0 
						);
		$for_count_args = $args;
		
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) {
			$args['s'] = $_POST['search']['value'];
		}
		
		if( isset($_POST['product_status']) && !empty($_POST['product_status']) ) $args['post_status'] = $_POST['product_status'];
  	
  	if( isset($_POST['product_type']) && !empty($_POST['product_type']) ) {
			if ( 'downloadable' == $_POST['product_type'] ) {
				$args['meta_value']    = 'yes';
				$args['meta_key']      = '_downloadable';
			} elseif ( 'virtual' == $_POST['product_type'] ) {
				$args['meta_value']    = 'yes';
				$args['meta_key']      = '_virtual';
			} elseif ( 'variable' == $_POST['product_type'] || 'simple' == $_POST['product_type'] ) {
				$args['tax_query'][] = array(
																		'taxonomy' => 'product_type',
																		'field' => 'slug',
																		'terms' => array($_POST['product_type']),
																		'operator' => 'IN'
																	);
			} else {
				$args['tax_query'][] = array(
																		'taxonomy' => 'product_type',
																		'field' => 'slug',
																		'terms' => array($_POST['product_type']),
																		'operator' => 'IN'
																	);
			}
		}
		
		if( isset($_POST['product_cat']) && !empty($_POST['product_cat']) ) {
			$args['tax_query'][] = array(
																		'taxonomy' => 'product_cat',
																		'field' => 'term_id',
																		'terms' => array($_POST['product_cat']),
																		'operator' => 'IN'
																	);
		}
		
		// Vendor Filter
		if( isset($_POST['product_vendor']) && !empty($_POST['product_vendor']) ) {
			$is_marketplace = wcfm_is_marketplace();
			if( $is_marketplace ) {
				if( !wcfm_is_vendor() ) {
					if( $is_marketplace == 'wcpvendors' ) {
						$args['tax_query'][] = array(
																					'taxonomy' => WC_PRODUCT_VENDORS_TAXONOMY,
																					'field' => 'term_id',
																					'terms' => $_POST['product_vendor'],
																				);
					} elseif( $is_marketplace == 'wcvendors' ) {
						$args['author'] = $_POST['product_vendor'];
					} elseif( $is_marketplace == 'wcmarketplace' ) {
						$vendor_term = absint( get_user_meta( $_POST['product_vendor'], '_vendor_term_id', true ) );
						$args['tax_query'][] = array(
																					'taxonomy' => 'dc_vendor_shop',
																					'field' => 'term_id',
																					'terms' => $vendor_term,
																				);
					} elseif( $is_marketplace == 'dokan' ) {
						$args['author'] = $_POST['product_vendor'];
					}
				}
			}
		}
		
		$args = apply_filters( 'wcfm_products_args', $args );
		
		$wcfm_products_array = get_posts( $args );
		
		$pro_count = 0;
		$filtered_pro_count = 0;
		if( wcfm_is_vendor() ) {
			// Get Product Count
			$for_count_args['posts_per_page'] = -1;
			$for_count_args['offset'] = 0;
			$for_count_args = apply_filters( 'wcfm_products_args', $for_count_args );
			$wcfm_products_count = get_posts( $for_count_args );
			$pro_count = count($wcfm_products_count);
			
			// Get Filtered Post Count
			$args['posts_per_page'] = -1;
			$args['offset'] = 0;
			$wcfm_filterd_products_array = get_posts( $args );
			$filtered_pro_count = count($wcfm_filterd_products_array);
		} else {
			// Get Product Count
			$wcfm_products_counts = wp_count_posts('product');
			foreach($wcfm_products_counts as $wcfm_products_count ) {
				$pro_count += $wcfm_products_count;
			}
			
			// Get Filtered Post Count
			$filtered_pro_count = $pro_count; 
		}
		
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) {
			unset( $args['s'] );
			
			$search_ids = array();
			$terms      = explode( ',', $_POST['search']['value'] );
	
			foreach ( $terms as $term ) {
				if ( is_numeric( $term ) ) {
					$search_ids[] = $term;
				}
	
				// Attempt to get a SKU
				$sku_to_id = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_parent FROM {$wpdb->posts} LEFT JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id WHERE meta_key='_sku' AND meta_value LIKE %s;", '%' . $wpdb->esc_like( wc_clean( $term ) ) . '%' ) );
				$sku_to_id = array_merge( wp_list_pluck( $sku_to_id, 'ID' ), wp_list_pluck( $sku_to_id, 'post_parent' ) );
	
				if ( ( $sku_to_id != 0 ) && sizeof( $sku_to_id ) > 0 ) {
					$search_ids = array_merge( $search_ids, $sku_to_id );
				}
			}
			
			if( !empty( $search_ids ) ) {
				if( ( !is_array( $args['include'] ) && $args['include'] == '' ) || ( is_array($args['include']) && empty( $args['include'] ) ) ) {
					$args['include'] = $search_ids;
				} elseif( is_array($args['include']) && !empty( $args['include'] ) ) {
					$args['include'] = array_merge( $args['include'], $search_ids );
				}
			
				$wcfm_sku_search_products_array = get_posts( $args );
				
				if( count( $wcfm_sku_search_products_array ) > 0 ) {
					$wcfm_products_array = array_merge( $wcfm_products_array, $wcfm_sku_search_products_array );
					$filtered_pro_count += count( $wcfm_sku_search_products_array );
				}
			}
		}
		
		
		// Generate Products JSON
		$wcfm_products_json = '';
		$wcfm_products_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $pro_count . ',
															"recordsFiltered": ' . $filtered_pro_count . ',
															"data": ';
		if(!empty($wcfm_products_array)) {
			$index = 0;
			$wcfm_products_json_arr = array();
			foreach($wcfm_products_array as $wcfm_products_single) {
				$the_product = wc_get_product( $wcfm_products_single );
				
				// Bulk Action Checkbox
				if( apply_filters( 'wcfm_is_allow_bulk_edit', true ) && WCFM_Dependencies::wcfmu_plugin_active_check() ) {
					$wcfm_products_json_arr[$index][] =  '<input type="checkbox" class="wcfm-checkbox bulk_action_checkbox_single" name="bulk_action_checkbox[]" value="' . $wcfm_products_single->ID . '" />';
				} else {
					$wcfm_products_json_arr[$index][] =  '';
				}
				
				// Thumb
				if( current_user_can( 'edit_published_products' ) && apply_filters( 'wcfm_is_allow_edit_products', true ) ) {
					$wcfm_products_json_arr[$index][] =  '<a href="' . get_wcfm_edit_product_url($wcfm_products_single->ID, $the_product) . '">' . $the_product->get_image( 'thumbnail' ) . '</a>';
				} else {
					$wcfm_products_json_arr[$index][] =  $the_product->get_image( 'thumbnail' );
				}
				
				// Title
				if( current_user_can( 'edit_published_products' ) && apply_filters( 'wcfm_is_allow_edit_products', true ) ) {
					$wcfm_products_json_arr[$index][] =  '<a href="' . get_wcfm_edit_product_url($wcfm_products_single->ID, $the_product) . '" class="wcfm_product_title">' . $wcfm_products_single->post_title . '</a>';
				} else {
					if( $wcfm_products_single->post_status == 'publish' ) {
						$wcfm_products_json_arr[$index][] =  apply_filters( 'wcfm_product_title_dashboard', $wcfm_products_single->post_title, $wcfm_products_single->ID );
					} elseif( apply_filters( 'wcfm_is_allow_edit_products', true ) ) {
						$wcfm_products_json_arr[$index][] =  apply_filters( 'wcfm_product_title_dashboard', '<a href="' . get_wcfm_edit_product_url($wcfm_products_single->ID, $the_product) . '" class="wcfm_product_title">' . $wcfm_products_single->post_title . '</a>', $wcfm_products_single->ID );
					} else {
						$wcfm_products_json_arr[$index][] =  apply_filters( 'wcfm_product_title_dashboard', $wcfm_products_single->post_title, $wcfm_products_single->ID );
					}
				}
				
				// SKU
				$wcfm_products_json_arr[$index][] =  ( get_post_meta($wcfm_products_single->ID, '_sku', true) ) ? get_post_meta($wcfm_products_single->ID, '_sku', true) : '-';
				
				// Status
				if( $wcfm_products_single->post_status == 'publish' ) {
					$wcfm_products_json_arr[$index][] =  '<span class="product-status product-status-' . $wcfm_products_single->post_status . '">' . __( 'Published', 'wc-frontend-manager' ) . '</span>';
				} else {
					$wcfm_products_json_arr[$index][] =  '<span class="product-status product-status-' . $wcfm_products_single->post_status . '">' . ucfirst( $wcfm_products_single->post_status ) . '</span>';
				}
				
				// Stock
				if ( $the_product->is_in_stock() ) {
					$stock_html = '<span class="instock">' . __( 'In stock', 'woocommerce' ) . '</span>';
				} else {
					$stock_html = '<span class="outofstock">' . __( 'Out of stock', 'woocommerce' ) . '</span>';
				}
		
				// If the product has children, a single stock level would be misleading as some could be -ve and some +ve, some managed/some unmanaged etc so hide stock level in this case.
				if ( $the_product->managing_stock() && ! sizeof( $the_product->get_children() ) ) {
					$stock_html .= ' (' . $the_product->get_stock_quantity() . ')';
				}
				$wcfm_products_json_arr[$index][] =  apply_filters( 'woocommerce_admin_stock_html', $stock_html, $the_product );
				
				// Price
				$wcfm_products_json_arr[$index][] =  $the_product->get_price_html() ? $the_product->get_price_html() : '<span class="na">&ndash;</span>';
				
				// Type
				$pro_type = '';
				if ( 'grouped' == $the_product->get_type() ) {
					$pro_type = '<span class="product-type tips grouped wcicon-grouped text_tip" data-tip="' . esc_attr__( 'Grouped', 'wc-frontend-manager' ) . '"></span>';
				} elseif ( 'external' == $the_product->get_type() ) {
					$pro_type = '<span class="product-type tips external wcicon-external text_tip" data-tip="' . esc_attr__( 'External/Affiliate', 'wc-frontend-manager' ) . '"></span>';
				} elseif ( 'simple' == $the_product->get_type() ) {
		
					if ( $the_product->is_virtual() ) {
						$pro_type = '<span class="product-type tips virtual wcicon-virtual text_tip" data-tip="' . esc_attr__( 'Virtual', 'wc-frontend-manager' ) . '"></span>';
					} elseif ( $the_product->is_downloadable() ) {
						$pro_type = '<span class="product-type tips downloadable wcicon-downloadable text_tip" data-tip="' . esc_attr__( 'Downloadable', 'wc-frontend-manager' ) . '"></span>';
					} else {
						$pro_type = '<span class="product-type tips simple wcicon-simple text_tip" data-tip="' . esc_attr__( 'Simple', 'wc-frontend-manager' ) . '"></span>';
					}
		
				} elseif ( 'variable' == $the_product->get_type() ) {
					$pro_type = '<span class="product-type tips variable wcicon-variable text_tip" data-tip="' . esc_attr__( 'Variable', 'wc-frontend-manager' ) . '"></span>';
				} elseif ( 'subscription' == $the_product->get_type() ) {
					$pro_type = '<span class="product-type tips wcicon-variable text_tip" data-tip="' . esc_attr__( 'Subscription', 'wc-frontend-manager' ) . '"></span>';
				} elseif ( 'variable-subscription' == $the_product->get_type() ) {
					$pro_type = '<span class="product-type tips wcicon-variable text_tip" data-tip="' . esc_attr__( 'Variable Subscription', 'wc-frontend-manager' ) . '"></span>';
				} elseif ( 'job_package' == $the_product->get_type() ) {
					$pro_type = '<span class="product-type tips fa fa-briefcase text_tip" data-tip="' . esc_attr__( 'Listings Package', 'wc-frontend-manager' ) . '"></span>';
				} elseif ( 'resume_package' == $the_product->get_type() ) {
					$pro_type = '<span class="product-type tips fa fa-suitcase text_tip" data-tip="' . esc_attr__( 'Resume Package', 'wc-frontend-manager' ) . '"></span>';
				} elseif ( 'auction' == $the_product->get_type() ) {
					$pro_type = '<span class="product-type tips fa fa-gavel text_tip" data-tip="' . esc_attr__( 'Auction', 'wc-frontend-manager' ) . '"></span>';
				} elseif ( 'redq_rental' == $the_product->get_type() ) {
					$pro_type = '<span class="product-type tips fa fa-cab text_tip" data-tip="' . esc_attr__( 'Rental', 'wc-frontend-manager' ) . '"></span>';
				} elseif ( 'accommodation-booking' == $the_product->get_type() ) {
					$pro_type = '<span class="product-type tips fa fa-calendar text_tip" data-tip="' . esc_attr__( 'Accommodation', 'wc-frontend-manager' ) . '"></span>';
				} elseif ( 'appointment' == $the_product->get_type() ) {
					$pro_type = '<span class="product-type tips fa fa-calendar text_tip" data-tip="' . esc_attr__( 'Appointment', 'wc-frontend-manager' ) . '"></span>';
				} elseif ( 'bundle' == $the_product->get_type() ) {
					$pro_type = '<span class="product-type tips fa fa-cubes text_tip" data-tip="' . esc_attr__( 'Bundle', 'wc-frontend-manager' ) . '"></span>';
				} elseif ( 'composite' == $the_product->get_type() ) {
					$pro_type = '<span class="product-type tips fa fa-cubes text_tip" data-tip="' . esc_attr__( 'Composite', 'wc-frontend-manager' ) . '"></span>';
				} else {
					// Assuming that we have other types in future
					$pro_type = '<span class="product-type tips wcicon-' . $the_product->get_type() . ' text_tip ' . $the_product->get_type() . '" data-tip="' . ucfirst( $the_product->get_type() ) . '"></span>';
				}
				$wcfm_products_json_arr[$index][] =  $pro_type;
				
				// Views
				$wcfm_products_json_arr[$index][] =  '<span class="view_count">' . (int) get_post_meta( $wcfm_products_single->ID, '_wcfm_product_views', true ) . '</span>';
				
				// Date
				$wcfm_products_json_arr[$index][] =  date_i18n( wc_date_format(), strtotime($wcfm_products_single->post_date) );
				
				// Vendor
				$vendor_name = '&ndash;';
				if( !$WCFM->is_marketplace || wcfm_is_vendor() ) {
					$wcfm_products_json_arr[$index][] =  $vendor_name;
				} else {
					if( $WCFM->is_marketplace == 'wcmarketplace' ) {
						$vendor_terms = wp_get_post_terms( $wcfm_products_single->ID, 'dc_vendor_shop' );
						foreach( $vendor_terms as $vendor_term ) {
							$vendor_name = $vendor_term->name;
						}
					} elseif( $WCFM->is_marketplace == 'wcpvendors' ) {
						$vendor_terms = wp_get_post_terms( $wcfm_products_single->ID, 'wcpv_product_vendors' );
						foreach( $vendor_terms as $vendor_term ) {
							$vendor_name = $vendor_term->name;
						}
					} elseif( $WCFM->is_marketplace == 'wcvendors' ) {
						$vendor_name = get_user_meta( $wcfm_products_single->post_author, 'pv_shop_name', true );
					} elseif( $WCFM->is_marketplace == 'dokan' ) {
						$vendor_data = get_user_meta( $wcfm_products_single->post_author, 'dokan_profile_settings', true );
						$vendor_name = isset( $vendor_data['store_name'] ) ? esc_attr( $vendor_data['store_name'] ) : '';
						$vendor_name = empty( $vendor_name ) ? get_user_by( 'id', $wcfm_products_single->post_author )->display_name : $vendor_name;
					}
					$wcfm_products_json_arr[$index][] =  $vendor_name;
				}
				
				// Action
				$actions = '<a class="wcfm-action-icon" target="_blank" href="' . get_permalink( $wcfm_products_single->ID ) . '"><span class="fa fa-eye text_tip" data-tip="' . esc_attr__( 'View', 'wc-frontend-manager' ) . '"></span></a>';
				
				// Mark Featured - 3.0.1
				if( $wcfm_products_single->post_status == 'publish' ) {
					if( $wcfm_is_allow_featured_product = apply_filters( 'wcfm_is_allow_featured_product', true ) ) {
						if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
							if( has_term( 'featured', 'product_visibility', $wcfm_products_single->ID ) ) {
								$actions .= '<a class="wcfm_product_featured wcfm-action-icon" href="#" data-featured="nofeatured" data-proid="' . $wcfm_products_single->ID . '"><span class="fa fa-star text_tip" data-tip="' . esc_attr__( 'No Featured', 'wc-frontend-manager' ) . '"></span></a>';
							} else {
								$actions .= '<a class="wcfm_product_featured wcfm-action-icon" href="#" data-featured="featured" data-proid="' . $wcfm_products_single->ID . '"><span class="fa fa-star-o text_tip" data-tip="' . esc_attr__( 'Mark Featured', 'wc-frontend-manager' ) . '"></span></a>';
							}
						} else {
							if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
								$actions .= '<a class="wcfm_product_dummy_featured wcfm-action-icon" href="#" onclick="return false;"><span class="fa fa-star-o text_tip" data-tip="' . __( 'Featured Product: Upgrade your WCFM to WCFM Ultimate to avail this feature.', 'wc-frontend-manager' ) . '"></span></a>';
							}
						}
					}
				}
				
				// Duplicate - 2.5.2
				if( $wcfm_is_allow_duplicate_product = apply_filters( 'wcfm_is_allow_duplicate_product', true ) ) {
					if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
						$actions .= '<a class="wcfm_product_duplicate wcfm-action-icon" href="#" data-proid="' . $wcfm_products_single->ID . '"><span class="fa fa-copy text_tip" data-tip="' . esc_attr__( 'Duplicate', 'wc-frontend-manager' ) . '"></span></a>';
					} else {
						if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
							$actions .= '<a class="wcfm_product_dummy_duplicate wcfm-action-icon" href="#" onclick="return false;"><span class="fa fa-copy text_tip" data-tip="' . __( 'Duplicate Product: Upgrade your WCFM to WCFM Ultimate to avail this feature.', 'wc-frontend-manager' ) . '"></span></a>';
						}
					}
				}
				
				if( $wcfm_products_single->post_status == 'publish' ) {
					$actions .= ( current_user_can( 'edit_published_products' ) && apply_filters( 'wcfm_is_allow_edit_products', true ) ) ? '<a class="wcfm-action-icon" href="' . get_wcfm_edit_product_url($wcfm_products_single->ID, $the_product) . '"><span class="fa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wc-frontend-manager' ) . '"></span></a>' : '';
					$actions .= ( current_user_can( 'delete_published_products' ) && apply_filters( 'wcfm_is_allow_edit_products', true ) ) ? '<a class="wcfm-action-icon wcfm_product_delete" href="#" data-proid="' . $wcfm_products_single->ID . '"><span class="fa fa-trash-o text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager' ) . '"></span></a>' : '';
				} else {
					$actions .= ( current_user_can( 'edit_products' ) && apply_filters( 'wcfm_is_allow_edit_products', true ) ) ? '<a class="wcfm-action-icon" href="' . get_wcfm_edit_product_url($wcfm_products_single->ID, $the_product) . '"><span class="fa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wc-frontend-manager' ) . '"></span></a>' : '';
					$actions .= ( current_user_can( 'delete_products' ) && apply_filters( 'wcfm_is_allow_edit_products', true ) ) ? '<a class="wcfm_product_delete wcfm-action-icon" href="#" data-proid="' . $wcfm_products_single->ID . '"><span class="fa fa-trash-o text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager' ) . '"></span></a>' : '';
				}
				
				$wcfm_products_json_arr[$index][] =  apply_filters ( 'wcfm_products_actions',  $actions, $the_product );
				
				
				$index++;
			}												
		}
		if( !empty($wcfm_products_json_arr) ) $wcfm_products_json .= json_encode($wcfm_products_json_arr);
		else $wcfm_products_json .= '[]';
		$wcfm_products_json .= '
													}';
													
		echo $wcfm_products_json;
	}
}