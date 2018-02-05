<?php
/**
 * WCFM plugin core
 *
 * Plugin Vendor Support Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   2.0.0
 */
 
class WCFM_Vendor_Support {

	public function __construct() {
		global $WCFM;
		
		if ( !is_admin() || defined('DOING_AJAX') ) {
			if( !wcfm_is_vendor() ) {
				if( $is_allow_vendors = apply_filters( 'wcfm_is_allow_vendors', true ) ) {
					// WC Booking Query Var Filter
					add_filter( 'wcfm_query_vars', array( &$this, 'vendors_wcfm_query_vars' ), 20 );
					add_filter( 'wcfm_endpoint_title', array( &$this, 'vendors_wcfm_endpoint_title' ), 20, 2 );
					add_action( 'init', array( &$this, 'vendors_wcfm_init' ), 20 );
					
					// WCFM Third Party Endpoint Edit
					add_filter( 'wcfm_endpoints_slug', array( $this, 'wcfm_vendors_endpoints_slug' ) );
					
					// WC Booking Menu Filter
					add_filter( 'wcfm_menus', array( &$this, 'vendors_wcfm_menus' ), 20 );
				}
			}
			
			add_filter( 'wcfm_is_admin_fee_mode', array( &$this, 'wcfm_is_admin_fee_mode' ) );
			
			if( wcfm_is_vendor() ) {
				add_filter( 'wcfm_orders_total_heading', array( &$this, 'wcfm_vendors_orders_total_heading' ) );
			}
			
			if( $is_allow_commission_manage = apply_filters( 'wcfm_is_allow_commission_manage', true ) ) {
				// Associate Vendor
				add_action( 'end_wcfm_products_manage', array( &$this, 'wcfm_associate_vendor' ), 490 );
				add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcfm_associate_vendor_save' ), 490, 2 );
				
				// Commmission Manage
				if( $WCFM->is_marketplace == 'wcvendors' ) {
					add_action( 'end_wcfm_products_manage', array( &$this, 'wcvendors_product_commission' ), 500 );
					add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcvendors_product_commission_save' ), 500, 2 );
				} else	if( $WCFM->is_marketplace == 'wcmarketplace' ) {
					add_action( 'end_wcfm_products_manage', array( &$this, 'wcmarketplace_product_commission' ), 500 );
					
					// For Variation
					add_filter( 'wcfm_product_manage_fields_variations', array( &$this, 'wcmarketplace_commission_fields_variations' ), 500, 4 );
					add_filter( 'wcfm_variation_edit_data', array( &$this, 'wcmarketplace_commission_data_variations' ), 500, 3 );
					
					// Commision Save
					add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcmarketplace_product_commission_save' ), 500, 2 );
					
					// Commision Save For Variation
					add_filter( 'wcfm_product_variation_data_factory', array( &$this, 'wcmarketplace_product_variation_commission_save' ), 500, 5 );
				}
			}
			
			// Product Vendors Manage Vendor Product Permissions
			if( $WCFM->is_marketplace == 'wcpvendors' ) {
				add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcpvendors_product_manage_vendor_association' ), 10, 2 );
			}
			
			add_filter( 'wcfm_message_types', array( &$this, 'wcfm_store_message_types' ) );
		}
		
		// Login Redirect
		add_filter( 'woocommerce_login_redirect', array($this, 'wcfm_wc_vendor_login_redirect'), 50, 2 );
		add_filter( 'login_redirect', array($this, 'wcfm_vendor_login_redirect'), 50, 3 );
		
		// WC Vendor Capability update
		add_action( 'wcvendors_option_updates', array( &$this, 'vendors_capability_option_updates' ), 10, 2 );
	}
	
	/**
   * WCFM Vendors Query Var
   */
  function vendors_wcfm_query_vars( $query_vars ) {
  	$wcfm_modified_endpoints = (array) get_option( 'wcfm_endpoints' );
  	
		$query_vendors_vars = array(
			'wcfm-vendors'                 => ! empty( $wcfm_modified_endpoints['wcfm-vendors'] ) ? $wcfm_modified_endpoints['wcfm-vendors'] : 'wcfm-vendors',
			'wcfm-vendors-manage'          => ! empty( $wcfm_modified_endpoints['wcfm-vendors-manage'] ) ? $wcfm_modified_endpoints['wcfm-vendors-manage'] : 'wcfm-vendors-manage',
			'wcfm-vendors-commission'      => ! empty( $wcfm_modified_endpoints['wcfm-vendors-commission'] ) ? $wcfm_modified_endpoints['wcfm-vendors-commission'] : 'wcfm-vendors-commission',
		);
		
		$query_vars = array_merge( $query_vars, $query_vendors_vars );
		
		return $query_vars;
  }
  
  /**
   * WCFM Vendors End Point Title
   */
  function vendors_wcfm_endpoint_title( $title, $endpoint ) {
  	global $wp;
  	switch ( $endpoint ) {
  		case 'wcfm-vendors' :
				$title = __( 'Vendors Dashboard', 'wc-frontend-manager' );
			break;
			case 'wcfm-vendors-manage' :
				$title = __( 'Vendors Manager', 'wc-frontend-manager' );
			break;
			case 'wcfm-vendors-commission' :
				$title = __( 'Vendors Commission', 'wc-frontend-manager' );
			break;
  	}
  	
  	return $title;
  }
  
  /**
   * WCFM Vendors Endpoint Intialize
   */
  function vendors_wcfm_init() {
  	global $WCFM_Query;
	
		// Intialize WCFM End points
		$WCFM_Query->init_query_vars();
		$WCFM_Query->add_endpoints();
		
		if( !get_option( 'wcfm_updated_end_point_wcfm_vendors' ) ) {
			// Flush rules after endpoint update
			flush_rewrite_rules();
			update_option( 'wcfm_updated_end_point_wcfm_vendors', 1 );
		}
  }
  
  /**
	 * WCFM Vendors Endpoiint Edit
	 */
  function wcfm_vendors_endpoints_slug( $endpoints ) {
		
		$vendors_endpoints = array(
													'wcfm-vendors'  		      => 'wcfm-vendors',
													'wcfm-vendors-manage'  	  => 'wcfm-vendors-manage',
													'wcfm-vendors-commission' => 'wcfm-vendors-commission'
													);
		$endpoints = array_merge( $endpoints, $vendors_endpoints );
		
		return $endpoints;
	}
  
  /**
   * WCFM Vendors Menu
   */
  function vendors_wcfm_menus( $menus ) {
  	global $WCFM;
  	
		$menus = array_slice($menus, 0, 3, true) +
												array( 'wcfm-vendors' => array(   'label'  => __( 'Vendors', 'wc-frontend-manager'),
																										 'url'       => get_wcfm_vendors_url(),
																										 'icon'      => 'user-o',
																										 'priority'  => 45
																										) )	 +
													array_slice($menus, 3, count($menus) - 3, true) ;
		
  	return $menus;
  }
  
	/**
	 * WCFM WC Vendor Login redirect
	 */
	function wcfm_wc_vendor_login_redirect( $redirect_to, $user ) {
		if ( isset($user->roles) && is_array($user->roles) ) {
			if ( in_array( 'vendor', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			} if ( in_array( 'seller', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			} elseif ( in_array( 'dc_vendor', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			} elseif ( in_array( 'wc_product_vendors_admin_vendor', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			} elseif ( in_array( 'wc_product_vendors_manager_vendor', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			} elseif ( in_array( 'shop_manager', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			} elseif ( in_array( 'shop_staff', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			}
		}
		
		if ( $user && isset($user->ID) ) {
			$current_login = get_user_meta( $user->ID, '_current_login', true );
			update_user_meta( $user->ID, '_previous_login', $current_login );
			update_user_meta( $user->ID, '_current_login', time() );
		}
		
		return apply_filters( 'wcfm_login_redirect', $redirect_to, $user );
	}
	
	/**
	 * WCFM Vendor Login redirect
	 */
	function wcfm_vendor_login_redirect( $redirect_to, $request, $user ) {
		if ( isset($user->roles) && is_array($user->roles) ) {
			if ( in_array( 'vendor', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			} elseif ( in_array( 'seller', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			} elseif ( in_array( 'dc_vendor', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			} elseif ( in_array( 'wc_product_vendors_admin_vendor', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			} elseif ( in_array( 'wc_product_vendors_manager_vendor', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			} elseif ( in_array( 'shop_manager', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			} elseif ( in_array( 'shop_staff', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			}
		}
		
		if ( $user && isset($user->ID) ) {
			$current_login = get_user_meta( $user->ID, '_current_login', true );
			update_user_meta( $user->ID, '_previous_login', $current_login );
			update_user_meta( $user->ID, '_current_login', time() );
		}
		
		return apply_filters( 'wcfm_login_redirect', $redirect_to, $user );
	}
	
	/**
	 * Orders total heading as commission for vendors
	 */
	function wcfm_vendors_orders_total_heading( $heading ) {
		global $WCFM;
		
		$heading = __( 'Commission', 'wc-frontend-manager');
		return $heading;
	}
	
	// WCFM Associate Vednor to Product
	function wcfm_associate_vendor( $product_id ) {
		global $WCFM;
		
		$is_marketplace = wcfm_is_marketplace();
		if( $is_marketplace ) {
			$vendor_arr = $this->wcfm_get_vendor_list();
				
			$wcfm_associate_vendor = $this->wcfm_get_vendor_id_from_product( $product_id );
	
			?>
			<!-- collapsible 11.5 - WCFM Vendor Association -->
			<div class="page_collapsible products_manage_vendor_association simple variable grouped external booking" id="wcfm_products_manage_form_vendor_association_head"><label class="fa fa-user-o"></label><?php _e('Vendor', 'wc-frontend-manager'); ?><span></span></div>
			<div class="wcfm-container simple variable external grouped booking">
				<div id="wcfm_products_manage_form_vendor_association_expander" class="wcfm-content">
					<?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_manage_fields_commission', array(  
																																															"wcfm_associate_vendor" => array( 'label' => __( 'Vendor', 'wc-frontend-manager' ), 'type' => 'select', 'options' => $vendor_arr, 'attributes' => array( 'style' => 'width: 60%;' ), 'class' => 'wcfm-select', 'label_class' => 'wcfm_title', 'value' => $wcfm_associate_vendor ),
																																										)) );
					?>
				</div>
			</div>
			<!-- end collapsible -->
			<div class="wcfm_clearfix"></div>
			<?php
		}
	}
	
	// WCFM Associate Vednor to Product Save
	function wcfm_associate_vendor_save( $new_product_id, $wcfm_products_manage_form_data ) {
		
		$is_marketplace = wcfm_is_marketplace();
		if( $is_marketplace ) {
			if( !wcfm_is_vendor() ) {
				if( $is_marketplace == 'wcpvendors' ) {
					if( isset( $wcfm_products_manage_form_data['wcfm_associate_vendor'] ) && !empty( $wcfm_products_manage_form_data['wcfm_associate_vendor'] ) ) {
						$vnd_term = absint( $wcfm_products_manage_form_data['wcfm_associate_vendor'] );
						wp_delete_object_term_relationships( $new_product_id, WC_PRODUCT_VENDORS_TAXONOMY );
						wp_set_object_terms( $new_product_id, $vnd_term, WC_PRODUCT_VENDORS_TAXONOMY, true );
					} else {
						wp_delete_object_term_relationships( $new_product_id, WC_PRODUCT_VENDORS_TAXONOMY );
					}
					// Pass Shipping/Tax to vendor
					update_post_meta( $new_product_id, '_wcpv_product_default_pass_shipping_tax', 'yes' );
				} elseif( $is_marketplace == 'wcmarketplace' ) {
					if( isset( $wcfm_products_manage_form_data['wcfm_associate_vendor'] ) && !empty( $wcfm_products_manage_form_data['wcfm_associate_vendor'] ) ) {
						$vnd_term = absint( $wcfm_products_manage_form_data['wcfm_associate_vendor'] );
						$vendor_term = get_user_meta( $vnd_term, '_vendor_term_id', true );
						$vendor_term = absint( $vendor_term );
						wp_delete_object_term_relationships( $new_product_id, 'dc_vendor_shop' );
						wp_set_object_terms( $new_product_id, $vendor_term, 'dc_vendor_shop', true );
						
						// Set author as well
						$arg = array(
							'ID' => $new_product_id,
							'post_author' => $vnd_term,
						);
						wp_update_post( $arg );
					} else {
						wp_delete_object_term_relationships( $new_product_id, 'dc_vendor_shop' );
						// Set author as well
						$arg = array(
							'ID' => $new_product_id,
							'post_author' => get_current_user_id(),
						);
						wp_update_post( $arg );
					}
				} elseif( $is_marketplace == 'wcvendors' ) {
					if( isset( $wcfm_products_manage_form_data['wcfm_associate_vendor'] ) && !empty( $wcfm_products_manage_form_data['wcfm_associate_vendor'] ) ) {
						$vnd_term = absint( $wcfm_products_manage_form_data['wcfm_associate_vendor'] );
						$arg = array(
							'ID' => $new_product_id,
							'post_author' => $vnd_term,
						);
						wp_update_post( $arg );
					} else {
						$arg = array(
							'ID' => $new_product_id,
							'post_author' => get_current_user_id(),
						);
						wp_update_post( $arg );
					}
				} elseif( $is_marketplace == 'dokan' ) {
					if( isset( $wcfm_products_manage_form_data['wcfm_associate_vendor'] ) && !empty( $wcfm_products_manage_form_data['wcfm_associate_vendor'] ) ) {
						$vnd_term = absint( $wcfm_products_manage_form_data['wcfm_associate_vendor'] );
						$arg = array(
							'ID' => $new_product_id,
							'post_author' => $vnd_term,
						);
						wp_update_post( $arg );
					} else {
						$arg = array(
							'ID' => $new_product_id,
							'post_author' => get_current_user_id(),
						);
						wp_update_post( $arg );
					}
				}
			}
		}
	}
	
	// WCV Vendor Commission
	function wcvendors_product_commission( $product_id ) {
		global $WCFM;
		
		$pv_commission_rate = '';
		if( $product_id  ) {
			$pv_commission_rate = get_post_meta( $product_id , 'pv_commission_rate', true );
		}
		?>
		<!-- collapsible 12 - WCV Commission Support -->
		<div class="page_collapsible products_manage_commission simple variable grouped external booking" id="wcfm_products_manage_form_commission_head"><label class="fa fa-percent"></label><?php _e('Commission', 'wc-frontend-manager'); ?><span></span></div>
		<div class="wcfm-container simple variable external grouped booking">
			<div id="wcfm_products_manage_form_commission_expander" class="wcfm-content">
				<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_manage_fields_commission', array(  
																																														"pv_commission_rate" => array('label' => __('Commission(%)', 'wc-frontend-manager') , 'type' => 'number', 'attributes' => array( 'min' => '', 'steps' => 1 ), 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $pv_commission_rate ),
																																									)) );
				?>
			</div>
		</div>
		<!-- end collapsible -->
		<div class="wcfm_clearfix"></div>
		<?php
	}
	
	// WCV Vendor Commision Save
	function wcvendors_product_commission_save( $new_product_id, $wcfm_products_manage_form_data ) {
		
		if( isset( $wcfm_products_manage_form_data['pv_commission_rate'] ) && !empty( $wcfm_products_manage_form_data['pv_commission_rate'] ) ) {
			update_post_meta( $new_product_id, 'pv_commission_rate', $wcfm_products_manage_form_data['pv_commission_rate'] );
		}
	}
	
	// WCMp Vendor Product Commission
	function wcmarketplace_product_commission( $product_id ) {
		global $WCFM, $WCMp;
		
		$commission_per_poduct = '';
		$commission_percentage_per_poduct = '';
		$commission_fixed_with_percentage = '';
		$commission_fixed_with_percentage_qty = '';
		if( $product_id  ) {
			$commission_per_poduct = get_post_meta( $product_id, '_commission_per_product', true);
			$commission_percentage_per_poduct = get_post_meta( $product_id, '_commission_percentage_per_product', true);
			$commission_fixed_with_percentage = get_post_meta( $product_id, '_commission_fixed_with_percentage', true);
			$commission_fixed_with_percentage_qty = get_post_meta( $product_id, '_commission_fixed_with_percentage_qty', true);
		}
		?>
		<!-- collapsible 12 - WCMp Commission Support -->
		<div class="page_collapsible products_manage_commission simple variable grouped external booking" id="wcfm_products_manage_form_commission_head"><label class="fa fa-percent"></label><?php _e('Commission', 'wc-frontend-manager'); ?><span></span></div>
		<div class="wcfm-container simple variable external grouped booking">
			<div id="wcfm_products_manage_form_commission_expander" class="wcfm-content">
				<?php
				if ($WCMp->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage') {
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_manage_fields_commission', array(  
																																															"_commission_percentage_per_product" => array('label' => __('Commission(%)', 'wc-frontend-manager') , 'type' => 'number', 'attributes' => array( 'min' => '', 'steps' => 1 ), 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $commission_percentage_per_poduct ),
																																															"_commission_fixed_with_percentage" => array('label' => __('Fixed (per transaction)', 'wc-frontend-manager') , 'type' => 'number', 'attributes' => array( 'min' => '', 'steps' => 1 ), 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $commission_fixed_with_percentage )
																																										)) );
				} elseif ($WCMp->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage_qty') {
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_manage_fields_commission', array(  
																																															"_commission_percentage_per_product" => array('label' => __('Commission(%)', 'wc-frontend-manager') , 'type' => 'number', 'attributes' => array( 'min' => '', 'steps' => 1 ), 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $commission_percentage_per_poduct ),
																																															"_commission_fixed_with_percentage_qty" => array('label' => __('Fixed (per unit)', 'wc-frontend-manager') , 'type' => 'number', 'attributes' => array( 'min' => '', 'steps' => 1 ), 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $commission_fixed_with_percentage_qty )
																																										)) );
				} else {
					$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_manage_fields_commission', array(  
																																															"_commission_per_product" => array('label' => __('Commission', 'wc-frontend-manager') . ' ('.$WCMp->vendor_caps->payment_cap['commission_type'].')' , 'type' => 'number', 'attributes' => array( 'min' => '', 'steps' => 1 ), 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $commission_per_poduct ),
																																										)) );
				}
				?>
			</div>
		</div>
		<!-- end collapsible -->
		<div class="wcfm_clearfix"></div>
		<?php
	}
	
	/**
	 * WCFMu Variation aditional options
	 */
	function wcmarketplace_commission_fields_variations( $variation_fileds, $variations, $variation_shipping_option_array, $variation_tax_classes_options ) {
		global $WCFM, $WCMp;
		
		if ($WCMp->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage') {
			$wcfmu_variation_commission_fields = array(  
																									"_commission_percentage_per_product" => array('label' => __('Commission(%)', 'wc-frontend-manager') , 'type' => 'number', 'attributes' => array( 'min' => '', 'steps' => 1 ), 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking' ),
																									"_commission_fixed_with_percentage" => array('label' => __('Fixed (per transaction)', 'wc-frontend-manager') , 'type' => 'number', 'attributes' => array( 'min' => '', 'steps' => 1 ), 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking' )
																				         );
		} elseif ($WCMp->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage_qty') {
			$wcfmu_variation_commission_fields = array(  
																									"_commission_percentage_per_product" => array('label' => __('Commission(%)', 'wc-frontend-manager') , 'type' => 'number', 'attributes' => array( 'min' => '', 'steps' => 1 ), 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking' ),
																									"_commission_fixed_with_percentage_qty" => array('label' => __('Fixed (per unit)', 'wc-frontend-manager') , 'type' => 'number', 'attributes' => array( 'min' => '', 'steps' => 1 ), 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking' )
																								);
		} else {
			$wcfmu_variation_commission_fields = array(  
																									"_commission_per_product" => array('label' => __('Commission', 'wc-frontend-manager') . ' ('.$WCMp->vendor_caps->payment_cap['commission_type'].')' , 'type' => 'number', 'attributes' => array( 'min' => '', 'steps' => 1 ), 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking' ),
																					       );
		}
		
		$variation_fileds = array_merge( $variation_fileds, $wcfmu_variation_commission_fields );
		
		return $variation_fileds;
	}
	
	/**
	 * Variaton commission edit data
	 */
	function wcmarketplace_commission_data_variations( $variations, $variation_id, $variation_id_key ) {
		global $WCFM, $WCMp;
		
		if( $variation_id  ) {
			$variations[$variation_id_key]['description'] = get_post_meta($variation_id, '_product_vendors_commission', true);
			$variations[$variation_id_key]['_commission_percentage_per_product'] = get_post_meta( $variation_id, '_product_vendors_commission_percentage', true);
			$variations[$variation_id_key]['_commission_fixed_with_percentage'] = get_post_meta( $variation_id, '_product_vendors_commission_fixed_per_trans', true);
			$variations[$variation_id_key]['_commission_fixed_with_percentage_qty'] = get_post_meta( $variation_id, '_product_vendors_commission_fixed_per_qty', true);
		}
		
		return $variations;
	}
	
	// WCMp Vendor Product Commision Save
	function wcmarketplace_product_commission_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $WCFM, $WCMp;
		
		if ($WCMp->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage') {
			if( isset( $wcfm_products_manage_form_data['_commission_percentage_per_product'] ) && !empty( $wcfm_products_manage_form_data['_commission_percentage_per_product'] ) ) {
				update_post_meta( $new_product_id, '_commission_percentage_per_product', $wcfm_products_manage_form_data['_commission_percentage_per_product'] );
			}
			if( isset( $wcfm_products_manage_form_data['_commission_fixed_with_percentage'] ) && !empty( $wcfm_products_manage_form_data['_commission_fixed_with_percentage'] ) ) {
				update_post_meta( $new_product_id, '_commission_fixed_with_percentage', $wcfm_products_manage_form_data['_commission_fixed_with_percentage'] );
			}
		} elseif ($WCMp->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage_qty') {
			if( isset( $wcfm_products_manage_form_data['_commission_percentage_per_product'] ) && !empty( $wcfm_products_manage_form_data['_commission_percentage_per_product'] ) ) {
				update_post_meta( $new_product_id, '_commission_percentage_per_product', $wcfm_products_manage_form_data['_commission_percentage_per_product'] );
			}
			if( isset( $wcfm_products_manage_form_data['_commission_fixed_with_percentage_qty'] ) && !empty( $wcfm_products_manage_form_data['_commission_fixed_with_percentage_qty'] ) ) {
				update_post_meta( $new_product_id, '_commission_fixed_with_percentage_qty', $wcfm_products_manage_form_data['_commission_fixed_with_percentage_qty'] );
			}
		} else {
			if( isset( $wcfm_products_manage_form_data['_commission_per_product'] ) && !empty( $wcfm_products_manage_form_data['_commission_per_product'] ) ) {
				update_post_meta( $new_product_id, '_commission_per_product', $wcfm_products_manage_form_data['_commission_per_product'] );
			}
		}
	}
	
	// WCMp Vendor Product Variation Commision Save
	function wcmarketplace_product_variation_commission_save( $wcfm_variation_data, $new_product_id, $variation_id, $variations, $wcfm_products_manage_form_data ) {
		global $WCFM, $WCMp;
		
		if ($WCMp->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage') {
			if( isset( $variations['_commission_percentage_per_product'] ) && !empty( $variations['_commission_percentage_per_product'] ) ) {
				update_post_meta( $variation_id, '_product_vendors_commission_percentage', $variations['_commission_percentage_per_product'] );
			}
			if( isset( $variations['_commission_fixed_with_percentage'] ) && !empty( $variations['_commission_fixed_with_percentage'] ) ) {
				update_post_meta( $variation_id, '_product_vendors_commission_fixed_per_trans', $variations['_commission_fixed_with_percentage'] );
			}
		} elseif ($WCMp->vendor_caps->payment_cap['commission_type'] == 'fixed_with_percentage_qty') {
			if( isset( $variations['_commission_percentage_per_product'] ) && !empty( $variations['_commission_percentage_per_product'] ) ) {
				update_post_meta( $variation_id, '_product_vendors_commission_percentage', $variations['_commission_percentage_per_product'] );
			}
			if( isset( $variations['_commission_fixed_with_percentage_qty'] ) && !empty( $variations['_commission_fixed_with_percentage_qty'] ) ) {
				update_post_meta( $variation_id, '_product_vendors_commission_fixed_per_qty', $variations['_commission_fixed_with_percentage_qty'] );
			}
		} else {
			if( isset( $variations['_commission_per_product'] ) && !empty( $variations['_commission_per_product'] ) ) {
				update_post_meta( $variation_id, '_product_vendors_commission', $variations['_commission_per_product'] );
			}
		}
		
		return $wcfm_variation_data;
	}
	
	// Vendors Capability Options update
  function vendors_capability_option_updates( $options = array(), $tabname = 'capabilities' ) {
  	
  	if( $tabname == 'capabilities' ) {
  		$options = get_option( 'wcfm_capability_options' );
  		$is_marketplace = wcfm_is_marketplace();
  		
  		if( $is_marketplace ) {
  		
				if( $is_marketplace == 'wcvendors' ) {
					$vendor_role = get_role( 'vendor' );
				} elseif( $is_marketplace == 'wcmarketplace' ) {
					$vendor_role = get_role( 'dc_vendor' );
				} elseif( $is_marketplace == 'wcpvendors' ) {
					$vendor_role = get_role( 'wc_product_vendors_admin_vendor' );
				} elseif( $is_marketplace == 'dokan' ) {
					$vendor_role = get_role( 'seller' );
				}
				
				// Delete Media Capability
				if( isset( $options['delete_media'] ) && $options[ 'delete_media' ] == 'yes' ) {
					$vendor_role->remove_cap( 'delete_attachments' );
					$vendor_role->remove_cap( 'delete_posts' );
				} else {
					$vendor_role->add_cap( 'delete_attachments' );
					$vendor_role->add_cap( 'delete_posts' );
				}
				
				// Ensure Vendors Media Upload Capability
				$vendor_role->add_cap('edit_posts');
				$vendor_role->add_cap('edit_post');
				$vendor_role->add_cap('edit_others_posts');
        $vendor_role->add_cap('edit_others_pages');
        $vendor_role->add_cap('edit_published_posts');
        $vendor_role->add_cap('edit_published_pages');
        $vendor_role->add_cap( 'upload_files' );
				
				// Booking Capability
				if( wcfm_is_booking() ) {
					if( isset( $options['manage_booking'] ) && $options[ 'manage_booking' ] == 'yes' ) {
						$vendor_role->remove_cap( 'manage_bookings' );
					} else {
						$vendor_role->add_cap( 'manage_bookings' );
					}
				}
				
				// Appointment Capability
				if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
					if( WCFMu_Dependencies::wcfm_wc_appointments_active_check() ) {
						if( isset( $options['manage_appointment'] ) && $options[ 'manage_appointment' ] == 'yes' ) {
							$vendor_role->remove_cap( 'manage_appointments' );
							$vendor_role->remove_cap( 'manage_others_appointments' );
						} else {
							$vendor_role->add_cap( 'manage_appointments' );
							$vendor_role->add_cap( 'manage_others_appointments' );
						}
					}
				}
				
				// Submit Products
				if( isset( $options[ 'submit_products' ] ) && $options[ 'submit_products' ] == 'yes' ) {
					$vendor_role->remove_cap( 'edit_products' );
					$vendor_role->remove_cap( 'manage_products' );
					$vendor_role->remove_cap( 'read_products' );
				} else {
					$vendor_role->add_cap( 'edit_products' );
					$vendor_role->add_cap( 'manage_products' );
					$vendor_role->add_cap( 'read_products' );
				}
				
				// Publish Products
				if( isset( $options[ 'publish_products' ] ) && $options[ 'publish_products' ] == 'yes' ) {
					$vendor_role->remove_cap( 'publish_products' );
				} else {
					$vendor_role->add_cap( 'publish_products' );
				}
				
				// Live Products Edit
				if( isset( $options[ 'edit_live_products' ] ) && $options[ 'edit_live_products' ] == 'yes' ) {
					$vendor_role->remove_cap( 'edit_published_products' );
				} else {
					$vendor_role->add_cap( 'edit_published_products' );
				}
				
				// Delete Products
				if( isset( $options[ 'delete_products' ] ) && $options[ 'delete_products' ] == 'yes' ) {
					$vendor_role->remove_cap( 'delete_published_products' );
					$vendor_role->remove_cap( 'delete_products' );
				} else {
					$vendor_role->add_cap( 'delete_published_products' );
					$vendor_role->add_cap( 'delete_products' );
				}
				
				// Submit Cuopon
				if( isset( $options[ 'submit_coupons' ] ) && $options[ 'submit_coupons' ] == 'yes' ) {
					$vendor_role->remove_cap( 'edit_shop_coupons' );
					$vendor_role->remove_cap( 'manage_shop_coupons' );
					$vendor_role->remove_cap( 'read_shop_coupons' );
				} else {
					$vendor_role->add_cap( 'edit_shop_coupons' );
					$vendor_role->add_cap( 'manage_shop_coupons' );
					$vendor_role->add_cap( 'read_shop_coupons' );
				}
				
				// Publish Coupon
				if( isset( $options[ 'publish_coupons' ] ) && $options[ 'publish_coupons' ] == 'yes' ) {
					$vendor_role->remove_cap( 'publish_shop_coupons' );
				} else {
					$vendor_role->add_cap( 'publish_shop_coupons' );
				}
				
				// Live Coupon Edit
				if( isset( $options[ 'edit_live_coupons' ] ) && $options[ 'edit_live_coupons' ] == 'yes' ) {
					$vendor_role->remove_cap( 'edit_published_shop_coupons' );
				} else {
					$vendor_role->add_cap( 'edit_published_shop_coupons' );
				}
				
				// Delete Coupon
				if( isset( $options[ 'delete_coupons' ] ) && $options[ 'delete_coupons' ] == 'yes' ) {
					$vendor_role->remove_cap( 'delete_published_shop_coupons' );
					$vendor_role->remove_cap( 'delete_shop_coupons' );
				} else {
					$vendor_role->add_cap( 'delete_published_shop_coupons' );
					$vendor_role->add_cap( 'delete_shop_coupons' );
				}
			}
		}
  }
  
  // Product Vendor association on Product save
  function wcpvendors_product_manage_vendor_association( $new_product_id, $wcfm_products_manage_form_data ) {
  	global $WCFM, $WCMp;
  	
  	
		// check post type to be product
		if ( 'product' === get_post_type( $new_product_id ) ) {
			
			$product_post = get_post( $new_product_id );
			
			if ( WC_Product_Vendors_Utils::is_vendor( $product_post->post_author ) ) {
				$vendor_data = WC_Product_Vendors_Utils::get_all_vendor_data( $product_post->post_author );
				if( $vendor_data && !empty( $vendor_data ) ) {
					$vendor_data_term = key( $vendor_data );
		
					// automatically set the vendor term for this product
					wp_set_object_terms( $new_product_id, $vendor_data_term, WC_PRODUCT_VENDORS_TAXONOMY );
				}
			}
		}
  }
  
  function wcfm_get_vendor_list( $all = false, $offset = '', $number = '', $search = '' ) {
  	global $WCFM;
  	
  	$is_marketplace = wcfm_is_marketplace();
  	$vendor_arr = array();
		if( $is_marketplace ) {
			if( !wcfm_is_vendor() ) {
				if( $all ) {
					$vendor_arr = array( 0 => __('All', 'wc-frontend-manager' ) );
				} else {
					$vendor_arr = array( '' => __('Choose Vendor ...', 'wc-frontend-manager' ) );
				}
				$wcfm_allow_vendors_list = apply_filters( 'wcfm_allow_vendors_list', '', $is_marketplace );
				if( $is_marketplace == 'wcpvendors' ) {
					$args = array(
						'hide_empty'   => false,
						'hierarchical' => false,
					);
					if( $number ) {
						$args['offset'] = $offset;
						$args['number'] = $number;
					}
					if( $search ) {
						$args['search'] = $search;
					}
					if( $wcfm_allow_vendors_list ) {
						$args['include']  = $wcfm_allow_vendors_list;
					}
					$vendors = get_terms( WC_PRODUCT_VENDORS_TAXONOMY, $args );
					
					if( !empty( $vendors ) ) {
						foreach ( $vendors as $vendor ) {
							$vendor_arr[$vendor->term_id] = esc_html( $vendor->name );
						}
					}
				} else {
					$args = array(
						'role__in'     => array( 'dc_vendor', 'vendor', 'seller' ),
						'orderby'      => 'login',
						'order'        => 'ASC',
						'include'      => $wcfm_allow_vendors_list,
						'count_total'  => false,
						'fields'       => array( 'ID', 'display_name' )
					 ); 
					if( $number ) {
						$args['offset'] = $offset;
						$args['number'] = $number;
					}
					if( $search ) {
						$args['search'] = $search;
					}
					$all_users = get_users( $args );
					if( !empty( $all_users ) ) {
						foreach( $all_users as $all_user ) {
							$vendor_arr[$all_user->ID] = $all_user->display_name;
						}
					}
				}
			}
		}
		
		return $vendor_arr;
	}
	
	function wcfm_get_vendor_store_by_vendor( $vendor_id ) {
		global $WCFM, $wpdb, $WCMp;
  	
  	$vendor_store = '&ndash;';
  	
  	if( !$vendor_id ) return $vednor_store;
  	$vendor_id = absint( $vendor_id );
  	
  	$marketplece = wcfm_is_marketplace();
  	if( $marketplece == 'wcvendors' ) {
  		$shop_name = get_user_meta( $vendor_id, 'pv_shop_name', true );
			if( $shop_name ) $vendor_store = $shop_name;
			$shop_link       = WCV_Vendors::get_vendor_shop_page( $vendor_id );
			if( $shop_name ) { $vendor_store = '<a target="_blank" href="' . apply_filters('wcv_vendor_shop_permalink', $shop_link) . '">' . $shop_name . '</a>'; }
			else { $vendor_store = '<a target="_blank" href="' . apply_filters('wcv_vendor_shop_permalink', $shop_link) . '">' . __('Shop', 'wc-frontend-manager') . '</a>'; }
		} elseif( $marketplece == 'wcmarketplace' ) {
			$vendor = get_wcmp_vendor( $vendor_id );
			if( $vendor ) {
				$shop_name = get_user_meta( $vendor_id , '_vendor_page_title', true);
				$store_name = get_user_meta( $vendor_id, 'store_name', true );
				if( $shop_name ) { $vendor_store = '<a target="_blank" href="' . apply_filters('wcmp_vendor_shop_permalink', $vendor->permalink) . '">' . $shop_name . '</a>'; }
				elseif( $store_name ) { $vendor_store = '<a target="_blank" href="' . apply_filters('wcmp_vendor_shop_permalink', $vendor->permalink) . '">' . $store_name . '</a>'; }
				else { $vendor_store = '<a target="_blank" href="' . apply_filters('wcmp_vendor_shop_permalink', $vendor->permalink) . '">' . __('Shop', 'wc-frontend-manager') . '</a>'; }
			}
		} elseif( $marketplece == 'wcpvendors' ) {
			$vendor_data = get_term( $vendor_id, WC_PRODUCT_VENDORS_TAXONOMY );
			$shop_name = $vendor_data->name;
			$shop_link = get_term_link( $vendor_id, WC_PRODUCT_VENDORS_TAXONOMY );
			if( $shop_name ) { $vendor_store = '<a target="_blank" href="' . apply_filters('wcpv_vendor_shop_permalink', $shop_link) . '">' . $shop_name . '</a>'; }
			else { $vendor_store = '<a target="_blank" href="' . apply_filters('wcpv_vendor_shop_permalink', $shop_link) . '">' . __('Shop', 'wc-frontend-manager') . '</a>'; }
		} elseif( $marketplece == 'dokan' ) {
			$vendor_data = get_user_meta( $vendor_id, 'dokan_profile_settings', true );
			$shop_name     = isset( $vendor_data['store_name'] ) ? esc_attr( $vendor_data['store_name'] ) : '';
			$shop_name     = empty( $shop_name ) ? get_user_by( 'id', $vendor_id )->display_name : $shop_name;
			$shop_link       = dokan_get_store_url( $vendor_id );
			if( $shop_name ) { $vendor_store = '<a target="_blank" href="' . apply_filters('dokan_vendor_shop_permalink', $shop_link) . '">' . $shop_name . '</a>'; }
			else { $vendor_store = '<a target="_blank" href="' . apply_filters('dokan_vendor_shop_permalink', $shop_link) . '">' . __('Shop', 'wc-frontend-manager') . '</a>'; }
		}
		
		return $vendor_store;
	}
	
	/**
   * WCFM is admin fee mode?
   */
  function wcfm_is_admin_fee_mode( $is_admin_fee ) {
  	
  	$marketplece = wcfm_is_marketplace();
  	if( $marketplece == 'wcmarketplace' ) {
  		global $WCMp;
			if (isset($WCMp->vendor_caps->payment_cap['revenue_sharing_mode'])) {
				if ($WCMp->vendor_caps->payment_cap['revenue_sharing_mode'] == 'admin') {
					$is_admin_fee = true;
				}
			}
		}
  	return $is_admin_fee;
  }
  
  /**
   * Gross sales by Vendor
   */
  function wcfm_get_gross_sales_by_vendor( $vendor_id = '', $interval = '7day', $is_paid = false, $order_id = 0 ) {
  	global $WCFM, $wpdb, $WCMp;
  	
  	$gross_sales = 0;
  	
  	$marketplece = wcfm_is_marketplace();
  	if( $marketplece == 'wcvendors' ) {
  		$sql = "SELECT order_id, GROUP_CONCAT(product_id) product_ids, SUM( commission.total_shipping ) AS total_shipping FROM {$wpdb->prefix}pv_commission AS commission";
			$sql .= " WHERE 1=1";
			if( $vendor_id ) $sql .= " AND `vendor_id` = {$vendor_id}";
			if( $order_id ) {
				$sql .= " AND `order_id` = {$order_id}";
			} else {
				if( $is_paid ) {
					$sql .= " AND commission.status = 'paid'";
				}
				$sql = wcfm_query_time_range_filter( $sql, 'time', $interval );
			}
			$sql .= " GROUP BY commission.order_id";
			
			$gross_sales_whole_week = $wpdb->get_results( $sql );
			if( !empty( $gross_sales_whole_week ) ) {
				foreach( $gross_sales_whole_week as $net_sale_whole_week ) {
					if( $net_sale_whole_week->order_id ) {
						$order_post_title = get_the_title( $net_sale_whole_week->order_id );
						if( !$order_post_title ) continue;
						try {
							$order       = wc_get_order( $net_sale_whole_week->order_id );
							$line_items  = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
							$valid_items = (array) $order_item_ids = explode( ",", $net_sale_whole_week->product_ids );
							
							foreach( $line_items as $key => $line_item ) {
								if ( in_array( $line_item->get_variation_id(), $valid_items ) || in_array( $line_item->get_product_id(), $valid_items ) ) {
									$gross_sales += (float) sanitize_text_field( $line_item->get_total() );
									if(WC_Vendors::$pv_options->get_option( 'give_tax' )) {
										$gross_sales += (float) sanitize_text_field( $line_item->get_total_tax() );
									}
									if(WC_Vendors::$pv_options->get_option( 'give_shipping' )) {
										$gross_sales += (float) $net_sale_whole_week->total_shipping;
									}
								}
							}
						} catch (Exception $e) {
							continue;
						}
					}
				}
			}
		} elseif( $marketplece == 'wcmarketplace' ) {
			$sql = "SELECT order_item_id, shipping, shipping_tax_amount FROM {$wpdb->prefix}wcmp_vendor_orders AS commission";
			$sql .= " WHERE 1=1";
			if( $vendor_id ) $sql .= " AND `vendor_id` = {$vendor_id}";
			if( $order_id ) {
				$sql .= " AND `order_id` = {$order_id}";
			} else {
				$sql .= " AND `line_item_type` = 'product' AND `commission_id` != 0 AND `commission_id` != '' AND `is_trashed` != 1";
				if( $is_paid ) {
					$sql .= " AND commission.commission_status = 'paid'";
					$sql = wcfm_query_time_range_filter( $sql, 'commission_paid_date', $interval );
				} else {
					$sql = wcfm_query_time_range_filter( $sql, 'created', $interval );
				}
			}
			
			$gross_sales_whole_week = $wpdb->get_results( $sql );
			if( !empty( $gross_sales_whole_week ) ) {
				foreach( $gross_sales_whole_week as $net_sale_whole_week ) {
					if( $net_sale_whole_week->order_item_id ) {
						try {
							$line_item = new WC_Order_Item_Product( $net_sale_whole_week->order_item_id );
							$gross_sales += (float) sanitize_text_field( $line_item->get_total() );
							if($WCMp->vendor_caps->vendor_payment_settings('give_tax')) {
								$gross_sales += (float) sanitize_text_field( $line_item->get_total_tax() );
								$gross_sales += (float) $net_sale_whole_week->shipping_tax_amount;
							}
							if($WCMp->vendor_caps->vendor_payment_settings('give_shipping')) {
								$gross_sales += (float) $net_sale_whole_week->shipping;
							}
						} catch (Exception $e) {
							continue;
						}
					}
				}
			}
		} elseif( $marketplece == 'wcpvendors' ) {
			$sql = "SELECT SUM( commission.product_amount ) AS total_product_amount, SUM( commission.product_shipping_amount ) AS product_shipping_amount, SUM( commission.product_shipping_tax_amount ) AS product_shipping_tax_amount, SUM( commission.product_tax_amount ) AS product_tax_amount FROM " . WC_PRODUCT_VENDORS_COMMISSION_TABLE . " AS commission";
			$sql .= " WHERE 1=1";
			if( $vendor_id )  $sql .= " AND commission.vendor_id = {$vendor_id}";
			if( $order_id ) {
				$sql .= " AND `order_id` = {$order_id}";
			} else {
				if( $is_paid ) {
					$sql .= " AND commission.commission_status = 'paid'";
					$sql = wcfm_query_time_range_filter( $sql, 'paid_date', $interval );
				} else {
					$sql = wcfm_query_time_range_filter( $sql, 'order_date', $interval );
				}
			}
			
			$total_sales = $wpdb->get_results( $sql );
			if( !empty($total_sales) ) {
				foreach( $total_sales as $total_sale ) {
					$gross_sales = $total_sale->total_product_amount + $total_sale->product_shipping_amount + $total_sale->product_shipping_tax_amount + $total_sale->product_tax_amount;
				}
			}
		} elseif( $marketplece == 'dokan' ) {
			$sql = "SELECT SUM( commission.order_total ) AS total_order_amount FROM {$wpdb->prefix}dokan_orders AS commission LEFT JOIN {$wpdb->posts} p ON commission.order_id = p.ID";
			$sql .= " WHERE 1=1";
			if( $vendor_id )  $sql .= " AND commission.seller_id = {$vendor_id}";
			if( $order_id ) {
				$sql .= " AND `commission.order_id` = {$order_id}";
			} else {   
				//$status = dokan_withdraw_get_active_order_status_in_comma();
				//$sql .= " AND order_status IN({$status})";
				$sql = wcfm_query_time_range_filter( $sql, 'post_date', $interval, '', '', 'p' );
			}
			
			$total_sales = $wpdb->get_results( $sql );
			if( !empty($total_sales) ) {
				foreach( $total_sales as $total_sale ) {
					$gross_sales = $total_sale->total_order_amount;
				}
			}
		}

		if( !$gross_sales ) $gross_sales = 0;
		
		return $gross_sales;
  }
	
	/**
   * Total commission paid by Admin
   */
  function wcfm_get_commission_by_vendor( $vendor_id = '', $interval = '7day', $is_paid = false ) {
  	global $WCFM, $wpdb, $WCMp;
  	
  	$commission = 0;
  	
  	$marketplece = wcfm_is_marketplace();
  	if( $marketplece == 'wcvendors' ) {
  		$commission_table = 'pv_commission'; 
  		$total_due = 'total_due';
  		$total_shipping = 'total_shipping';
  		$tax = 'tax';
  		$shipping_tax = 'tax';
  		$status = 'status';
  		$time = 'time';
  		$vendor_handler = 'vendor_id';
  		$table_handler = 'commission';
		} elseif( $marketplece == 'wcmarketplace' ) {
			$commission_table = 'wcmp_vendor_orders'; 
  		$total_due = 'commission_amount';
  		$total_shipping = 'shipping';
  		$tax = 'tax';
  		$shipping_tax = 'shipping_tax_amount';
  		$status = 'commission_status';
  		$vendor_handler = 'vendor_id';
  		$table_handler = 'commission';
  		if( $is_paid )
  			$time = 'commission_paid_date';
  		else
  			$time = 'created';
		} elseif( $marketplece == 'wcpvendors' ) {
			$commission_table = 'wcpv_commissions'; 
  		$total_due = 'total_commission_amount';
  		$total_shipping = 'product_shipping_amount';
  		$tax = 'product_tax_amount';
  		$shipping_tax = 'product_shipping_tax_amount';
  		$status = 'commission_status';
  		$vendor_handler = 'vendor_id';
  		$table_handler = 'commission';
  		if( $is_paid )
  			$time = 'paid_date';
  		else
  		  $time = 'order_date';
		} elseif( $marketplece == 'dokan' ) {
			$commission_table = 'dokan_orders'; 
  		$total_due = 'net_amount';
  		$time = 'post_date';
  		$vendor_handler = 'seller_id';
  		$table_handler = 'p';
  		if( $is_paid ) {
  			$sql = "SELECT SUM( withdraw.amount ) AS amount FROM {$wpdb->prefix}dokan_withdraw AS withdraw";
  			$sql .= " WHERE 1=1";
  			if( $vendor_id ) $sql .= " AND withdraw.user_id = {$vendor_id}";
  			$sql .= " AND withdraw.status = 1";
  			$sql = wcfm_query_time_range_filter( $sql, 'date', $interval, '', '', 'withdraw' );
  			$total_commissions = $wpdb->get_results( $sql );
  			$commission = 0;
				if( !empty($total_commissions) ) {
					foreach( $total_commissions as $total_commission ) {
						$commission += $total_commission->amount;
					}
				}
				if( !$commission ) $commission = 0;
				return $commission;
  		}
		}
  	
		if( $marketplece == 'dokan' ) {
			$sql = "SELECT SUM( commission.{$total_due} ) AS total_due FROM {$wpdb->prefix}{$commission_table} AS commission LEFT JOIN {$wpdb->posts} p ON commission.order_id = p.ID";
		} else {
		  $sql = "SELECT SUM( commission.{$total_due} ) AS total_due, SUM( commission.{$total_shipping} ) AS total_shipping, SUM( commission.{$tax} ) AS tax, SUM( commission.{$shipping_tax} ) AS shipping_tax FROM {$wpdb->prefix}{$commission_table} AS commission";
		}
		
		$sql .= " WHERE 1=1";
		if( $vendor_id ) $sql .= " AND commission.{$vendor_handler} = {$vendor_id}";
		if( $is_paid ) $sql .= " AND commission.{$status} = 'paid'";
		if( $marketplece == 'wcmarketplace' ) { $sql .= " AND commission.commission_id != 0 AND commission.commission_id != '' AND `is_trashed` != 1"; }
		if( $marketplece == 'dokan' ) {
			//$status = dokan_withdraw_get_active_order_status_in_comma();
			//$sql .= " AND order_status IN({$status})";
		}
		$sql = wcfm_query_time_range_filter( $sql, $time, $interval, '', '', $table_handler );
		
		$total_commissions = $wpdb->get_results( $sql );
		$commission = 0;
		if( !empty($total_commissions) ) {
			foreach( $total_commissions as $total_commission ) {
				$commission += $total_commission->total_due;
				if( $marketplece == 'wcvendors' ) {
					if ( WC_Vendors::$pv_options->get_option( 'give_tax' ) ) { $commission += $total_commission->total_shipping; } 
					if ( WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) { $commission += $total_commission->tax; }
				} elseif( $marketplece == 'wcmarketplace' ) {
					if($WCMp->vendor_caps->vendor_payment_settings('give_shipping')) { $commission += ( $total_commission->total_shipping == 'NAN' ) ? 0 : $total_commission->total_shipping; } 
					if($WCMp->vendor_caps->vendor_payment_settings('give_tax')) { 
					  $commission += ( $total_commission->tax == 'NAN' ) ? 0 : $total_commission->tax;
					  $commission += ( $total_commission->shipping_tax == 'NAN' ) ? 0 : $total_commission->shipping_tax;
					}
				}
			}
		}
		if( !$commission ) $commission = 0;
		
		return $commission;
  }
  
  /**
   * Total sales by vendor items
   */
  function wcfm_get_total_sell_by_vendor( $vendor_id = '', $interval = '7day' ) {
  	global $WCFM, $wpdb, $WCMp;
  	
  	$total_sell = 0;
  	
  	$marketplece = wcfm_is_marketplace();
  	if( $marketplece == 'wcvendors' ) {
  		$commission_table = 'pv_commission'; 
  		$qty = 'qty';
  		$time = 'time';
  		$vendor_handler = 'vendor_id';
  		$table_handler = 'commission';
  		$func = 'SUM';
		} elseif( $marketplece == 'wcmarketplace' ) {
			$commission_table = 'wcmp_vendor_orders'; 
  		$qty = 'quantity';
  		$time = 'created';
  		$vendor_handler = 'vendor_id';
  		$table_handler = 'commission';
  		$func = 'SUM';
		} elseif( $marketplece == 'wcpvendors' ) {
			$commission_table = 'wcpv_commissions'; 
  		$qty = 'product_quantity';
  		$time = 'order_date';
  		$vendor_handler = 'vendor_id';
  		$table_handler = 'commission';
  		$func = 'SUM';
		} elseif( $marketplece == 'dokan' ) {
			include_once( $WCFM->plugin_path . 'includes/reports/class-dokan-report-sales-by-date.php' );
			$wcfm_report_sales_by_date = new Dokan_Report_Sales_By_Date( $interval );
			$wcfm_report_sales_by_date->calculate_current_range( $interval );
			$report_data   = $wcfm_report_sales_by_date->get_report_data();
			return $report_data->total_items;
		}
  	
  	$sql = "SELECT {$func}( commission.{$qty} ) AS qty FROM {$wpdb->prefix}{$commission_table} AS commission";
		$sql .= " WHERE 1=1";
		if( $vendor_id ) $sql .= " AND commission.{$vendor_handler} = {$vendor_id}";
		if( $marketplece == 'wcmarketplace' ) { $sql .= " AND commission.commission_id != 0 AND commission.commission_id != '' AND `is_trashed` != 1"; }
		$sql = wcfm_query_time_range_filter( $sql, $time, $interval, '', '', $table_handler );
		
		$total_sell = $wpdb->get_var( $sql );
		if( !$total_sell ) $total_sell = 0;
		
		return $total_sell;
  }
  
  /**
   * Total commission for an Order
   */
  function wcfm_get_commission_by_order( $order_id = '', $is_paid = false ) {
  	global $WCFM, $wpdb, $WCMp;
  	
  	$commission = 0;
  	
  	$marketplece = wcfm_is_marketplace();
  	if( $marketplece == 'wcvendors' ) {
  		$commission_table = 'pv_commission'; 
  		$total_due = 'total_due';
  		$total_shipping = 'total_shipping';
  		$tax = 'tax';
  		$shipping_tax = 'tax';
  		$status = 'status';
  		$time = 'time';
  		$table_handler = 'commission';
		} elseif( $marketplece == 'wcmarketplace' ) {
			$commission_table = 'wcmp_vendor_orders'; 
  		$total_due = 'commission_amount';
  		$total_shipping = 'shipping';
  		$tax = 'tax';
  		$shipping_tax = 'shipping_tax_amount';
  		$status = 'commission_status';
  		$table_handler = 'commission';
  		if( $is_paid )
  			$time = 'commission_paid_date';
  		else
  			$time = 'created';
		} elseif( $marketplece == 'wcpvendors' ) {
			$commission_table = 'wcpv_commissions'; 
  		$total_due = 'total_commission_amount';
  		$total_shipping = 'product_shipping_amount';
  		$tax = 'product_tax_amount';
  		$shipping_tax = 'product_shipping_tax_amount';
  		$status = 'commission_status';
  		$table_handler = 'commission';
  		if( $is_paid )
  			$time = 'paid_date';
  		else
  		  $time = 'order_date';
		} elseif( $marketplece == 'dokan' ) {
			$commission_table = 'dokan_orders'; 
  		$total_due = 'net_amount';
  		$status = 'order_status';
  		$table_handler = 'p';
  		if( $is_paid )
  			$is_paid = '';
		}
  	
		if( $marketplece == 'dokan' ) {
			$sql = "SELECT SUM( commission.{$total_due} ) AS total_due FROM {$wpdb->prefix}{$commission_table} AS commission";
		} else {
  		$sql = "SELECT SUM( commission.{$total_due} ) AS total_due, SUM( commission.{$total_shipping} ) AS total_shipping, SUM( commission.{$tax} ) AS tax, SUM( commission.{$shipping_tax} ) AS shipping_tax FROM {$wpdb->prefix}{$commission_table} AS commission";
  	}
		$sql .= " WHERE 1=1";
		if( $order_id ) $sql .= " AND commission.order_id = {$order_id}";
		if( $is_paid ) $sql .= " AND commission.{$status} = 'paid'";
		if( $marketplece == 'wcmarketplace' ) { $sql .= " AND commission.commission_id != 0 AND commission.commission_id != '' AND `is_trashed` != 1"; }
		
		$total_commissions = $wpdb->get_results( $sql );
		if( !empty($total_commissions) ) {
			foreach( $total_commissions as $total_commission ) {
				$commission = $total_commission->total_due;
				if( $marketplece == 'wcvendors' ) {
					if ( WC_Vendors::$pv_options->get_option( 'give_tax' ) ) { $commission += $total_commission->total_shipping; } 
					if ( WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) { $commission += $total_commission->tax; }
				} elseif( $marketplece == 'wcmarketplace' ) {
					if($WCMp->vendor_caps->vendor_payment_settings('give_shipping')) { $commission += ( $total_commission->total_shipping == 'NAN' ) ? 0 : $total_commission->total_shipping; } 
					if($WCMp->vendor_caps->vendor_payment_settings('give_tax')) { 
					  $commission += ( $total_commission->tax == 'NAN' ) ? 0 : $total_commission->tax;
					  $commission += ( $total_commission->shipping_tax == 'NAN' ) ? 0 : $total_commission->shipping_tax;
					}
				}
			}
		}
		if( !$commission ) $commission = 0;
		
		return $commission;
  }
  
  function wcfm_get_products_by_vendor( $vendor_id = 0 ) {
		global $WCFM;
		
		$vendor_product_list = array();
		
		if( !$vendor_id ) return $vendor_product_list;
		
		$args = array(
							'posts_per_page'   => -1,
							'offset'           => 0,
							'orderby'          => 'date',
							'order'            => 'DESC',
							'post_type'        => 'product',
							//'author'	   => get_current_user_id(),
							'post_status'      => array('draft', 'pending', 'publish'),
							'suppress_filters' => 0 
						);
		$is_marketplace = wcfm_is_marketplace();
		if( $is_marketplace ) {
			if( $is_marketplace == 'wcpvendors' ) {
				$args['tax_query'][] = array(
																			'taxonomy' => WC_PRODUCT_VENDORS_TAXONOMY,
																			'field' => 'term_id',
																			'terms' => $vendor_id,
																		);
			} elseif( $is_marketplace == 'wcvendors' ) {
				$args['author'] = $vendor_id;
			} elseif( $is_marketplace == 'wcmarketplace' ) {
				$vendor_term = absint( get_user_meta( $vendor_id, '_vendor_term_id', true ) );
				$args['tax_query'][] = array(
																			'taxonomy' => 'dc_vendor_shop',
																			'field' => 'term_id',
																			'terms' => $vendor_term,
																		);
			} elseif( $is_marketplace == 'dokan' ) {
				$args['author'] = $vendor_id;
			}
		}
		$vendor_products = get_posts($args);
		if( !empty( $vendor_products ) ) {
			foreach( $vendor_products as $vendor_product ) {
				$vendor_product_list[$vendor_product->ID] = $vendor_product;
			}
		}
		
		return $vendor_product_list;
	}
  
  function wcfm_get_vendor_id_from_product( $product_id ) {
  	global $WCFM, $wpdb;
  	
  	$vendor_id = 0;
  	if( $WCFM->is_marketplace == 'wcmarketplace' ) {
  		$vendor = get_wcmp_product_vendors( $product_id );
  		if( $vendor ) $vendor_id = $vendor->id;
		} elseif( $WCFM->is_marketplace == 'wcvendors' ) {
			$author = WCV_Vendors::get_vendor_from_product( $product_id );
			if ( WCV_Vendors::is_vendor( $author ) ) $vendor_id = $author;
		} elseif( $WCFM->is_marketplace == 'wcpvendors' ) {
			$vendor_id = WC_Product_Vendors_Utils::get_vendor_id_from_product( $product_id );
		} elseif( $WCFM->is_marketplace == 'dokan' ) {
			$product = get_post( $product_id );
			$author = $product->post_author;
			if ( dokan_is_user_seller( $author ) ) $vendor_id = $author;
		}
		
		if( !$vendor_id || empty( $vendor_id ) ) $vendor_id = 0;
		
		return $vendor_id;
  }
  
  function wcfm_is_product_from_vendor( $product_id, $current_vendor = '' ) {
  	global $WCFM, $wpdb;
  	
  	$vendor_id = 0;
  	$is_product_from_vendor = false;
  	if( $WCFM->is_marketplace == 'wcmarketplace' ) {
  		$vendor = get_wcmp_product_vendors( $product_id );
  		if( $vendor ) $vendor_id = $vendor->id;
  		if( $vendor_id && !$current_vendor ) {
  			$current_vendor   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
  			$current_vendor_term = get_user_meta( $current_vendor, '_vendor_term_id', true );
  			if( $current_vendor == $vendor_id ) $is_product_from_vendor = true;
  		}
		} elseif( $WCFM->is_marketplace == 'wcvendors' ) {
			$author = WCV_Vendors::get_vendor_from_product( $product_id );
			if ( WCV_Vendors::is_vendor( $author ) ) $vendor_id = $author;
			if( $vendor_id && !$current_vendor ) {
				$current_vendor   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
				if( $current_vendor == $vendor_id ) $is_product_from_vendor = true;
			}
		} elseif( $WCFM->is_marketplace == 'wcpvendors' ) {
			$vendor_id = WC_Product_Vendors_Utils::get_vendor_id_from_product( $product_id );
			if( $vendor_id && !$current_vendor ) {
				$current_vendor   = apply_filters( 'wcfm_current_vendor_id', WC_Product_Vendors_Utils::get_logged_in_vendor() );
				if( $current_vendor == $vendor_id ) $is_product_from_vendor = true;
			}
		} elseif( $WCFM->is_marketplace == 'dokan' ) {
			$product = get_post( $product_id );
			$author = $product->post_author;
			if ( dokan_is_user_seller( $author ) ) $vendor_id = $author;
			if( $vendor_id && !$current_vendor ) {
				$current_vendor   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
				if( $current_vendor == $vendor_id ) $is_product_from_vendor = true;
			}
		}
		
		return $is_product_from_vendor;
  }
  
  function wcfm_is_order_for_vendor( $order_id, $current_vendor = '' ) {
  	global $WCFM, $wpdb;
  	
  	$is_order_for_vendor = true;
  	
  	if( $WCFM->is_marketplace == 'wcvendors' ) {
  		$commission_table = 'pv_commission'; 
  		$vendor_handler = 'vendor_id';
  		if( !$current_vendor ) {
				$current_vendor   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
			}
		} elseif( $WCFM->is_marketplace == 'wcmarketplace' ) {
			$commission_table = 'wcmp_vendor_orders'; 
  		$vendor_handler = 'vendor_id';
  		if( !$current_vendor ) {
				$current_vendor   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
			}
		} elseif( $WCFM->is_marketplace == 'wcpvendors' ) {
			$commission_table = 'wcpv_commissions'; 
  		$vendor_handler = 'vendor_id';
  		if( !$current_vendor ) {
  			$current_vendor   = apply_filters( 'wcfm_current_vendor_id', WC_Product_Vendors_Utils::get_logged_in_vendor() );
  		}
		} elseif( $WCFM->is_marketplace == 'dokan' ) {
			$commission_table = 'dokan_orders'; 
  		$vendor_handler = 'seller_id';
  		if( !$current_vendor ) {
				$current_vendor   = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
			}
		}
  	
		$sql = "SELECT * FROM {$wpdb->prefix}{$commission_table} AS commission";
		$sql .= " WHERE 1=1";
		if( $order_id ) $sql .= " AND commission.order_id = {$order_id}";
		if( $current_vendor ) $sql .= " AND commission.{$vendor_handler} = {$current_vendor}";
		$vendor_order_data = $wpdb->get_results( $sql );
		if( empty($vendor_order_data) ) { $is_order_for_vendor = false; }
		
		return $is_order_for_vendor;
	}
  
  function wcfm_get_vendor_email_from_product( $product_id ) {
  	global $WCFM, $wpdb;
  	
  	$vendor_email = 0;
  	if ( $product_id ) {
			if( $WCFM->is_marketplace ) {
				if( $WCFM->is_marketplace == 'wcmarketplace' ) {
				  $vendor = get_wcmp_product_vendors( $product_id );
					if( $vendor ) {
						$vendor_id = $vendor->id;
						$vendor_data = get_userdata( $vendor_id );
						if ( ! empty( $vendor_data ) ) {
							$vendor_email = $vendor_data->user_email;
						}
					}
				} elseif( $WCFM->is_marketplace == 'wcvendors' ) {
					$vendor_id = WCV_Vendors::get_vendor_from_product( $product_id );
					if( WCV_Vendors::is_vendor( $vendor_id ) ) {
						$vendor_data = get_userdata( $vendor_id );
						if ( ! empty( $vendor_data ) ) {
							$vendor_email = $vendor_data->user_email;
						}
					}
				} elseif( $WCFM->is_marketplace == 'wcpvendors' ) {
					$vendor_id = WC_Product_Vendors_Utils::get_vendor_id_from_product( $product_id );
					$vendor_data = WC_Product_Vendors_Utils::get_vendor_data_by_id( $vendor_id );
					if ( ! empty( $vendor_id ) && ! empty( $vendor_data ) ) {
						$vendor_email = $vendor_data['email'];
					}
				} elseif( $WCFM->is_marketplace == 'dokan' ) {
					$product = get_post( $product_id );
					$vendor_id = $product->post_author;
					if( dokan_is_user_seller( $vendor_id ) ) {
						$vendor_data = get_userdata( $vendor_id );
						if ( ! empty( $vendor_data ) ) {
							$vendor_email = $vendor_data->user_email;
						}
					}
				}
			}
		}
		
		if( !$vendor_email || empty( $vendor_email ) ) $vendor_email = '';
		
		return $vendor_email;
  }
  
  function wcfm_store_message_types( $message_types ) {
  	
  	$message_types['product_review'] = __( 'Review', 'wc-frontend-manager' );
  	
  	return $message_types;
  }
}