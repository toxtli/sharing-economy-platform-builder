<?php
/**
 * WCFM plugin controllers
 *
 * Third Party Plugin Products Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.2.2
 */

class WCFM_ThirdParty_Products_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		// WC Paid Listing Support - 2.3.4
    if( $wcfm_allow_job_package = apply_filters( 'wcfm_is_allow_job_package', true ) ) {
			if ( WCFM_Dependencies::wcfm_wc_paid_listing_active_check() ) {
				// WC Paid Listing Product Meta Data Save
				add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcfm_wcpl_product_meta_save' ), 50, 2 );
			}
		}
		
		// WC Rental & Booking Support - 2.3.8
    if( $wcfm_allow_rental = apply_filters( 'wcfm_is_allow_rental', true ) ) {
			if( WCFM_Dependencies::wcfm_wc_rental_active_check() ) {
				// WC Rental Product Meta Data Save
				add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcfm_wcrental_product_meta_save' ), 80, 2 );
			}
		}
		
		// YITH AuctionsFree Support - 3.0.4
    if( $wcfm_allow_auction = apply_filters( 'wcfm_is_allow_auction', true ) ) {
			if( WCFM_Dependencies::wcfm_yith_auction_free_active_check() ) {
				// YITH Auction Product Meta Data Save
				add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcfm_yith_auction_free_product_meta_save' ), 70, 2 );
			}
		}
		
		// Geo my WP Support - 3.2.4
    if( $wcfm_allow_geo_my_wp = apply_filters( 'wcfm_is_allow_geo_my_wp', true ) ) {
			if( WCFM_Dependencies::wcfm_geo_my_wp_plugin_active_check() ) {
				// GEO my WP Product Location DataSave
				add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcfm_geomywp_product_meta_save' ), 100, 2 );
			}
		}
		
		// Woocommerce Germanized Support - 3.3.2
    if( $wcfm_allow_woocommerce_germanized = apply_filters( 'wcfm_is_allow_woocommerce_germanized', true ) ) {
			if( WCFM_Dependencies::wcfm_woocommerce_germanized_plugin_active_check() ) {
				// Woocommerce Germanized Product Pricing & Shipping DataSave
				add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcfm_woocommerce_germanized_product_meta_save' ), 100, 2 );
				
				// Woocommerce Germanized Variation Pricing & Shipping DataSave
				add_action( 'wcfm_product_variation_data_factory', array( &$this, 'wcfm_woocommerce_germanized_variations_product_meta_save' ), 100, 5 );
			}
		}
		
		// Third Party Product Meta Data Save
    add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcfm_thirdparty_products_manage_meta_save' ), 100, 2 );
	}
	
	/**
	 * WC Paid Listing Product Meta data save
	 */
	function wcfm_wcpl_product_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $_POST;
		
		if( $wcfm_products_manage_form_data['product_type'] == 'job_package' ) {
	
			$job_package_fields = array(
				'_job_listing_package_subscription_type',
				'_job_listing_limit',
				'_job_listing_duration'
			);
	
			foreach ( $job_package_fields as $field_name ) {
				if ( isset( $wcfm_products_manage_form_data[ $field_name ] ) ) {
					update_post_meta( $new_product_id, $field_name, stripslashes( $wcfm_products_manage_form_data[ $field_name ] ) );
				}
			}
			
			// Featured
			$is_featured = ( isset( $wcfm_products_manage_form_data['_job_listing_featured'] ) ) ? 'yes' : 'no';
	
			update_post_meta( $new_product_id, '_job_listing_featured', $is_featured );
		}
	}
	
	/**
	 * WC Rental Product Meta data save
	 */
	function wcfm_wcrental_product_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $_POST;
		
		if( $wcfm_products_manage_form_data['product_type'] == 'redq_rental' ) {
			$rental_fields = array(
				'pricing_type',
				'hourly_price',
				'general_price',
				'redq_rental_availability'
			);
	
			foreach ( $rental_fields as $field_name ) {
				if ( isset( $wcfm_products_manage_form_data[ $field_name ] ) ) {
					$rental_fields[ str_replace( 'redq_', '', $field_name ) ] = $wcfm_products_manage_form_data[ $field_name ];
					update_post_meta( $new_product_id, $field_name, $wcfm_products_manage_form_data[ $field_name ] );
				}
			}
			
			update_post_meta( $new_product_id, '_price', $wcfm_products_manage_form_data[ 'general_price' ] );
			update_post_meta( $new_product_id, 'redq_all_data', $rental_fields );
		}
	}
	
	/**
	 * WC Rental Product Meta data save
	 */
	function wcfm_yith_auction_free_product_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $_POST;
		
		if( $wcfm_products_manage_form_data['product_type'] == 'auction' ) {
			$aution_fields = array(
				'_yith_auction_for',
				'_yith_auction_to',
			);
			
			$wcfm_products_manage_form_data['_yith_auction_for'] = ( $wcfm_products_manage_form_data[ '_yith_auction_for' ] ) ? strtotime( $wcfm_products_manage_form_data[ '_yith_auction_for' ] ) : '';
			$wcfm_products_manage_form_data['_yith_auction_to'] = ( $wcfm_products_manage_form_data[ '_yith_auction_to' ] ) ? strtotime( $wcfm_products_manage_form_data[ '_yith_auction_to' ] ) : '';
			
			
			foreach ( $aution_fields as $field_name ) {
				if ( isset( $wcfm_products_manage_form_data[ $field_name ] ) ) {
					$rental_fields[ $field_name ] = $wcfm_products_manage_form_data[ $field_name ];
					update_post_meta( $new_product_id, $field_name, $wcfm_products_manage_form_data[ $field_name ] );
				}
			}
			
			// Stock Update
			update_post_meta( $new_product_id, '_manage_stock', 'yes' );
			update_post_meta( $new_product_id, '_stock_status', 'instock' );
			update_post_meta( $new_product_id, '_stock', 1 );
		}
	}
	
	/**
	 * GEO my WP Product Meta data save
	 */
	function wcfm_geomywp_product_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $_POST;
		
		$geomywp_settings   = get_option( 'gmw_options' );
		if ( !isset( $geomywp_settings[ 'post_types_settings' ] ) || !isset( $geomywp_settings[ 'post_types_settings' ][ 'post_types' ] ) || empty( $geomywp_settings[ 'post_types_settings' ][ 'post_types' ] ) || ( !in_array( 'product', $geomywp_settings[ 'post_types_settings' ][ 'post_types' ] ) ) ) { return; }
		
		$prefix     	= '_wppl_';
		$wcfm_geomywp_meta_fields 	= array(
				'id'       => 'wppl-meta-box',
				'fields'   => array(
						array(
								'name' => __( 'Street', 'GMW' ),
								'desc' => '',
								'id'   => $prefix . 'street',
								'type' => 'text',
								'std'  => '',
								'placeholder' => '',
						),
						array(
								'name' => __( 'Apt/Suit', 'GMW' ),
								'desc' => '',
								'id'   => $prefix . 'apt',
								'type' => 'text',
								'std'  => '',
								'placeholder' => '',
						),
						array(
								'name' => __( 'City', 'GMW' ),
								'desc' => '',
								'id'   => $prefix . 'city',
								'type' => 'text',
								'std'  => '',
								'placeholder' => '',
						),
						array(
								'name' => __( 'State', 'GMW' ),
								'desc' => '',
								'id'   => $prefix . 'state',
								'type' => 'text',
								'std'  => '',
								'placeholder' => '',
						),
						array(
								'name' => __( 'Zipcode', 'GMW' ),
								'desc' => '',
								'id'   => $prefix . 'zipcode',
								'type' => 'text',
								'std'  => '',
								'placeholder' => '',
						),
						array(
								'name' => __( 'Country', 'GMW' ),
								'desc' => '',
								'id'   => $prefix . 'country',
								'type' => 'text',
								'std'  => '',
								'placeholder' => '',
						),
						array(
								'name' => __( 'Phone Number', 'GMW' ),
								'desc' => '',
								'id'   => $prefix . 'phone',
								'type' => 'text',
								'std'  => '',
								'placeholder' => '',
						),
						array(
								'name' => __( 'Fax Number', 'GMW' ),
								'desc' => '',
								'id'   => $prefix . 'fax',
								'type' => 'text',
								'std'  => '',
								'placeholder' => '',
						),
						array(
								'name' => __( 'Email Address', 'GMW' ),
								'desc' => '',
								'id'   => $prefix . 'email',
								'type' => 'text',
								'std'  => '',
								'placeholder' => '',
						),
						array(
								'name' => __( 'Website', 'GMW' ),
								'desc' => 'Ex: www.website.com',
								'id'   => $prefix . 'website',
								'type' => 'text',
								'std'  => '',
								'placeholder' => 'ex: http://www.mywebsite.com',
						),
						array(
								'name' => __( 'Latitude', 'GMW' ),
								'desc' => '',
								'id'   => $prefix . 'enter_lat',
								'type' => 'text-right',
								'std'  		=> '',
								'placeholder' => '',
						),
						array(
								'name' => __( 'Longitude', 'GMW' ),
								'desc' => '',
								'id'   => $prefix . 'enter_long',
								'type' => 'text-right',
								'std'  => '',
								'placeholder' => '',
						),
						array(
								'name' => __( 'Latitude', 'GMW' ),
								'desc' => '',
								'id'   => $prefix . 'lat',
								'type' => 'text-disable',
								'std'  => '',
								'placeholder' => '',
						),
						array(
								'name' => __( 'Longitude', 'GMW' ),
								'desc' => '',
								'id'   => $prefix . 'long',
								'type' => 'text-disable',
								'std'  => '',
								'placeholder' => '',
						),
						array(
								'name' => __( 'Full Address', 'GMW' ),
								'desc' => '',
								'id'   => $prefix . 'address',
								'type' => 'text-disable',
								'std'  => '',
								'placeholder' => '',
						),
						array(
								'name' => __( 'Days & Hours', 'GMW' ),
								'desc' => '',
								'id'   => $prefix . 'days_hours',
								'type' => 'text',
								'std'  => '',
								'placeholder' => '',
						),
						array(
								'name' => __( 'State Long', 'GMW' ),
								'desc' => '',
								'id'   => $prefix . 'state_long',
								'type' => 'text',
								'std'  => '',
								'placeholder' => '',
						),
						array(
								'name' => __( 'Country Long', 'GMW' ),
								'desc' => '',
								'id'   => $prefix . 'country_long',
								'type' => 'text',
								'std'  => '',
								'placeholder' => '',
						),
						array(
								'name' => __( 'Formatted address', 'GMW' ),
								'desc' => '',
								'id'   => $prefix . 'formatted_address',
								'type' => 'text',
								'std'  => '',
								'placeholder' => '',
						),
						array(
								'name' => __( 'street_number', 'GMW' ),
								'desc' => '',
								'id'   => $prefix . 'street_number',
								'type' => 'text',
								'std'  => '',
								'placeholder' => '',
						),
						array(
								'name' => __( 'street_name', 'GMW' ),
								'desc' => '',
								'id'   => $prefix . 'street_name',
								'type' => 'text',
								'std'  => '',
								'placeholder' => '',
						)
				)
		);
		
		foreach ( $wcfm_geomywp_meta_fields[ 'fields' ] as $field ) :

			if ( $field[ 'id' ] == '_wppl_days_hours' ) {

				if ( isset( $wcfm_products_manage_form_data[ $field[ 'id' ] ] ) ) :

					$old = get_post_meta( $new_product_id, $field[ 'id' ], true );
					$new = $wcfm_products_manage_form_data[ $field[ 'id' ] ];

					if ( $new && $new != $old ) {
							update_post_meta( $new_product_id, $field[ 'id' ], $new );
					} elseif ( '' == $new && $old ) {
							delete_post_meta( $new_product_id, $field[ 'id' ], $old );
					}

				endif;
			}

			endforeach;

			//do_action( 'gmw_pt_admin_update_location_post_meta', $new_product_id, $_POST, $wppl_options );
			//delete locaiton if there are no address or lat/long
			if ( !isset( $wcfm_products_manage_form_data[ '_wppl_formatted_address' ] ) || empty( $wcfm_products_manage_form_data[ '_wppl_formatted_address' ] ) || !isset( $wcfm_products_manage_form_data[ '_wppl_lat' ] ) || empty( $wcfm_products_manage_form_data[ '_wppl_lat' ] ) ) {

				$wpdb->query(
								$wpdb->prepare(
												"DELETE FROM " . $wpdb->prefix . "places_locator WHERE post_id=%d", $new_product_id
								)
				);
			} else {
			
				$wcfm_products_manage_form_data['gmw_map_icon']  = ( isset( $wcfm_products_manage_form_data['gmw_map_icon'] ) && !empty( $wcfm_products_manage_form_data['gmw_map_icon'] ) ) ? $wcfm_products_manage_form_data['gmw_map_icon'] : '_default.png';
				$wcfm_products_manage_form_data 					       = apply_filters( 'gmw_pt_before_location_updated', $wcfm_products_manage_form_data, $new_product_id );

				//location array
				$location = array(
						'post_id'           => $new_product_id,
						'feature'           => 0,
						'post_type'         => 'product',
						'post_title'        => $wcfm_products_manage_form_data['title'],
						'post_status'       => 'publish',
						'street_number'     => $wcfm_products_manage_form_data['_wppl_street_number'],
						'street_name'       => $wcfm_products_manage_form_data['_wppl_street_name'], 
						'street'            => $wcfm_products_manage_form_data['_wppl_street'],
						'apt'               => $wcfm_products_manage_form_data['_wppl_apt'],
						'city'              => $wcfm_products_manage_form_data['_wppl_city'],
						'state'             => $wcfm_products_manage_form_data['_wppl_state'],
						'state_long'        => $wcfm_products_manage_form_data['_wppl_state_long'],
						'zipcode'           => $wcfm_products_manage_form_data['_wppl_zipcode'],
						'country'           => $wcfm_products_manage_form_data['_wppl_country'],
						'country_long'      => $wcfm_products_manage_form_data['_wppl_country_long'],
						'address'           => $wcfm_products_manage_form_data['_wppl_address'],
						'formatted_address' => $wcfm_products_manage_form_data['_wppl_formatted_address'],
						'phone'             => $wcfm_products_manage_form_data['_wppl_phone'],
						'fax'               => $wcfm_products_manage_form_data['_wppl_fax'],
						'email'             => $wcfm_products_manage_form_data['_wppl_email'],
						'website'           => $wcfm_products_manage_form_data['_wppl_website'],
						'lat'               => $wcfm_products_manage_form_data['_wppl_lat'],
						'long'              => $wcfm_products_manage_form_data['_wppl_long'],
						'map_icon'          => $wcfm_products_manage_form_data['gmw_map_icon'],
				);
				
				//update locaiton in database
				$wpdb->replace( $wpdb->prefix . 'places_locator', 
				array(
					'post_id'           => $location['post_id'],
					'feature'           => $location['feature'],
					'post_status'       => $location['post_status'],
					'post_type'         => $location['post_type'],
					'post_title'        => $location['post_title'],
					'lat'               => $location['lat'],
					'long'              => $location['long'],
					'street_number'     => $location['street_number'],
					'street_name'       => $location['street_name'],
					'street'            => $location['street'],
					'apt'               => $location['apt'],
					'city'              => $location['city'],
					'state'             => $location['state'],
					'state_long'        => $location['state_long'],
					'zipcode'           => $location['zipcode'],
					'country'           => $location['country'],
					'country_long'      => $location['country_long'],
					'address'           => $location['address'],
					'formatted_address' => $location['formatted_address'],
					'phone'             => $location['phone'],
					'fax'               => $location['fax'],
					'email'             => $location['email'],
					'website'           => $location['website'],			
					'map_icon'          => $location['map_icon'],
				)
			);
				
		}
	}
	
	/**
	 * Woocommerce Germanized Product Meta data save
	 */
	function wcfm_woocommerce_germanized_product_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $_POST;
		
		$product = wc_get_product( $new_product_id );
		$product_type = ( ! isset( $wcfm_products_manage_form_data['product_type'] ) || empty( $wcfm_products_manage_form_data['product_type'] ) ) ? 'simple' : sanitize_title( stripslashes( $wcfm_products_manage_form_data['product_type'] ) );
		
		if ( isset( $wcfm_products_manage_form_data['_unit'] ) ) {

			if ( empty( $wcfm_products_manage_form_data['_unit'] ) || in_array( $wcfm_products_manage_form_data['_unit'], array( 'none', '-1' ) ) )
				$product = wc_gzd_unset_crud_meta_data( $product, '_unit' );
			else
				$product = wc_gzd_set_crud_meta_data( $product, '_unit', sanitize_text_field( $wcfm_products_manage_form_data['_unit'] ) );

		}

		if ( isset( $wcfm_products_manage_form_data['_unit_base'] ) ) {
			$product = wc_gzd_set_crud_meta_data( $product, '_unit_base', ( $wcfm_products_manage_form_data['_unit_base'] === '' ) ? '' : wc_format_decimal( $wcfm_products_manage_form_data['_unit_base'] ) );
		}

		if ( isset( $wcfm_products_manage_form_data['_unit_product'] ) ) {
			$product = wc_gzd_set_crud_meta_data( $product, '_unit_product', ( $wcfm_products_manage_form_data['_unit_product'] === '' ) ? '' : wc_format_decimal( $wcfm_products_manage_form_data['_unit_product'] ) );
		}

		$product = wc_gzd_set_crud_meta_data( $product, '_unit_price_auto', ( isset( $wcfm_products_manage_form_data['_unit_price_auto'] ) ) ? 'yes' : '' );
		
		if ( isset( $wcfm_products_manage_form_data['_unit_price_regular'] ) ) {
			$product = wc_gzd_set_crud_meta_data( $product, '_unit_price_regular', ( $wcfm_products_manage_form_data['_unit_price_regular'] === '' ) ? '' : wc_format_decimal( $wcfm_products_manage_form_data['_unit_price_regular'] ) );
			$product = wc_gzd_set_crud_meta_data( $product, '_unit_price', ( $wcfm_products_manage_form_data['_unit_price_regular'] === '' ) ? '' : wc_format_decimal( $wcfm_products_manage_form_data['_unit_price_regular'] ) );
		}
		
		if ( isset( $wcfm_products_manage_form_data['_unit_price_sale'] ) ) {

			// Unset unit price sale if no product sale price has been defined
			if ( ! isset( $wcfm_products_manage_form_data['sale_price'] ) || $wcfm_products_manage_form_data['sale_price'] === '' )
				$wcfm_products_manage_form_data['_unit_price_sale'] = '';

			$product = wc_gzd_set_crud_meta_data( $product, '_unit_price_sale', ( $wcfm_products_manage_form_data['_unit_price_sale'] === '' ) ? '' : wc_format_decimal( $wcfm_products_manage_form_data['_unit_price_sale'] ) );
		}

		// Ignore variable data
		if ( in_array( $product_type, array( 'variable', 'grouped' ) ) ) {

			$product = wc_gzd_set_crud_meta_data( $product, '_unit_price_regular', '' );
			$product = wc_gzd_set_crud_meta_data( $product, '_unit_price_sale', '' );
			$product = wc_gzd_set_crud_meta_data( $product, '_unit_price', '' );
			$product = wc_gzd_set_crud_meta_data( $product, '_unit_price_auto', '' );

		} else {

			$date_from = isset( $wcfm_products_manage_form_data['sale_date_from'] ) ? wc_clean( $wcfm_products_manage_form_data['sale_date_from'] ) : '';
			$date_to   = isset( $wcfm_products_manage_form_data['sale_date_upto'] ) ? wc_clean( $wcfm_products_manage_form_data['sale_date_upto'] ) : '';

			// Update price if on sale
			if ( isset( $wcfm_products_manage_form_data['_unit_price_sale'] ) ) {
				
				if ( '' !== $wcfm_products_manage_form_data['_unit_price_sale'] && '' == $date_to && '' == $date_from ) {
					$product = wc_gzd_set_crud_meta_data( $product, '_unit_price', wc_format_decimal( $wcfm_products_manage_form_data['_unit_price_sale'] ) );
				} else {
					$product = wc_gzd_set_crud_meta_data( $product, '_unit_price', ( $wcfm_products_manage_form_data['_unit_price_regular'] === '' ) ? '' : wc_format_decimal( $wcfm_products_manage_form_data['_unit_price_regular'] ) );
				}

				if ( '' !== $wcfm_products_manage_form_data['_unit_price_sale'] && $date_from && strtotime( $date_from ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
					$product = wc_gzd_set_crud_meta_data( $product, '_unit_price', wc_format_decimal( $wcfm_products_manage_form_data['_unit_price_sale'] ) );
				}

				if ( $date_to && strtotime( $date_to ) < strtotime( 'NOW', current_time( 'timestamp' ) ) )
					$product = wc_gzd_set_crud_meta_data( $product, '_unit_price', ( $wcfm_products_manage_form_data['_unit_price_regular'] === '' ) ? '' : wc_format_decimal( $wcfm_products_manage_form_data['_unit_price_regular'] ) );
			}
		}
		
		$sale_price_labels = array( '_sale_price_label', '_sale_price_regular_label' );

		foreach ( $sale_price_labels as $label ) {

			if ( isset( $wcfm_products_manage_form_data[$label] ) ) {

				if ( empty( $wcfm_products_manage_form_data[$label] ) || in_array( $wcfm_products_manage_form_data[$label], array( 'none', '-1' ) ) )
					$product = wc_gzd_unset_crud_meta_data( $product, $label );
				else
					$product = wc_gzd_set_crud_meta_data( $product, $label, sanitize_text_field( $wcfm_products_manage_form_data[$label] ) );
			}
		}
		
		if ( isset( $wcfm_products_manage_form_data[ '_mini_desc' ] ) ) {
			$product = wc_gzd_set_crud_meta_data( $product, '_mini_desc', ( $wcfm_products_manage_form_data[ '_mini_desc' ] === '' ? '' : wc_gzd_sanitize_html_text_field( $wcfm_products_manage_form_data[ '_mini_desc' ] ) ) );
		}

		if ( isset( $wcfm_products_manage_form_data[ 'delivery_time' ] ) && ! empty( $wcfm_products_manage_form_data[ 'delivery_time' ] ) ) {
			$product = wc_gzd_set_crud_term_data( $product, $wcfm_products_manage_form_data[ 'delivery_time' ], 'product_delivery_time' );
		} else {
			$product = wc_gzd_unset_crud_term_data( $product, 'product_delivery_time' );
		}

		// Free shipping
		$product = wc_gzd_set_crud_meta_data( $product, '_free_shipping', ( isset( $wcfm_products_manage_form_data['_free_shipping'] ) ) ? 'yes' : '' );

		// Is a service?
		$product = wc_gzd_set_crud_meta_data( $product, '_service', ( isset( $wcfm_products_manage_form_data['_service'] ) ) ? 'yes' : '' );
		
		// Applies to differential taxation?
		$product = wc_gzd_set_crud_meta_data( $product, '_differential_taxation', ( isset( $wcfm_products_manage_form_data['_differential_taxation'] ) ) ? 'yes' : '' );

		if ( isset( $wcfm_products_manage_form_data['_differential_taxation'] ) ) {
		  $product = wc_gzd_set_crud_data( $product, 'tax_status', 'shipping' );
    }

		// Ignore variable data
		if ( in_array( $product_type, array( 'variable', 'grouped' ) ) ) {
			$product = wc_gzd_set_crud_meta_data( $product, '_mini_desc', '' );
		}

		if ( wc_gzd_get_dependencies()->woocommerce_version_supports_crud() ) {
			$product->save();
			
			// Lets update the display price
			if ( $product->is_on_sale() ) {
				update_post_meta( $new_product_id, '_unit_price', $wcfm_products_manage_form_data[ '_unit_price_sale' ] );
			} else {
				update_post_meta( $new_product_id, '_unit_price', $wcfm_products_manage_form_data[ '_unit_price_regular' ] );
			}
		}
	}
	
	/**
	 * Woocommerce Germanized Variations Meta data save
	 */
	function wcfm_woocommerce_germanized_variations_product_meta_save( $wcfm_variation_data, $new_product_id, $variation_id, $variations, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $_POST;
		
		$product = wc_get_product( $variation_id );
		$product_type = ( ! isset( $wcfm_products_manage_form_data['product_type'] ) || empty( $wcfm_products_manage_form_data['product_type'] ) ) ? 'simple' : sanitize_title( stripslashes( $wcfm_products_manage_form_data['product_type'] ) );
		
		if ( isset( $variations['_unit'] ) ) {

			if ( empty( $variations['_unit'] ) || in_array( $variations['_unit'], array( 'none', '-1' ) ) )
				$product = wc_gzd_unset_crud_meta_data( $product, '_unit' );
			else
				$product = wc_gzd_set_crud_meta_data( $product, '_unit', sanitize_text_field( $variations['_unit'] ) );

		}

		if ( isset( $variations['_unit_base'] ) ) {
			$product = wc_gzd_set_crud_meta_data( $product, '_unit_base', ( $variations['_unit_base'] === '' ) ? '' : wc_format_decimal( $variations['_unit_base'] ) );
		}

		if ( isset( $variations['_unit_product'] ) ) {
			$product = wc_gzd_set_crud_meta_data( $product, '_unit_product', ( $variations['_unit_product'] === '' ) ? '' : wc_format_decimal( $variations['_unit_product'] ) );
		}

		$product = wc_gzd_set_crud_meta_data( $product, '_unit_price_auto', ( isset( $variations['_unit_price_auto'] ) ) ? 'yes' : '' );
		
		if ( isset( $variations['_unit_price_regular'] ) ) {
			$product = wc_gzd_set_crud_meta_data( $product, '_unit_price_regular', ( $variations['_unit_price_regular'] === '' ) ? '' : wc_format_decimal( $variations['_unit_price_regular'] ) );
			$product = wc_gzd_set_crud_meta_data( $product, '_unit_price', ( $variations['_unit_price_regular'] === '' ) ? '' : wc_format_decimal( $variations['_unit_price_regular'] ) );
		}
		
		if ( isset( $variations['_unit_price_sale'] ) ) {

			// Unset unit price sale if no product sale price has been defined
			if ( ! isset( $variations['sale_price'] ) || $variations['sale_price'] === '' )
				$variations['_unit_price_sale'] = '';

			$product = wc_gzd_set_crud_meta_data( $product, '_unit_price_sale', ( $variations['_unit_price_sale'] === '' ) ? '' : wc_format_decimal( $variations['_unit_price_sale'] ) );
		}

		$date_from = isset( $wcfm_products_manage_form_data['sale_date_from'] ) ? wc_clean( $wcfm_products_manage_form_data['sale_date_from'] ) : '';
		$date_to   = isset( $wcfm_products_manage_form_data['sale_date_upto'] ) ? wc_clean( $wcfm_products_manage_form_data['sale_date_upto'] ) : '';

		// Update price if on sale
		if ( isset( $variations['_unit_price_sale'] ) ) {
			
			if ( '' !== $variations['_unit_price_sale'] && '' == $date_to && '' == $date_from ) {
				$product = wc_gzd_set_crud_meta_data( $product, '_unit_price', wc_format_decimal( $variations['_unit_price_sale'] ) );
			} else {
				$product = wc_gzd_set_crud_meta_data( $product, '_unit_price', ( $variations['_unit_price_regular'] === '' ) ? '' : wc_format_decimal( $variations['_unit_price_regular'] ) );
			}

			if ( '' !== $variations['_unit_price_sale'] && $date_from && strtotime( $date_from ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
				$product = wc_gzd_set_crud_meta_data( $product, '_unit_price', wc_format_decimal( $variations['_unit_price_sale'] ) );
			}

			if ( $date_to && strtotime( $date_to ) < strtotime( 'NOW', current_time( 'timestamp' ) ) )
				$product = wc_gzd_set_crud_meta_data( $product, '_unit_price', ( $variations['_unit_price_regular'] === '' ) ? '' : wc_format_decimal( $variations['_unit_price_regular'] ) );
		}
		
		$sale_price_labels = array( '_sale_price_label', '_sale_price_regular_label' );

		foreach ( $sale_price_labels as $label ) {

			if ( isset( $variations[$label] ) ) {

				if ( empty( $variations[$label] ) || in_array( $variations[$label], array( 'none', '-1' ) ) )
					$product = wc_gzd_unset_crud_meta_data( $product, $label );
				else
					$product = wc_gzd_set_crud_meta_data( $product, $label, sanitize_text_field( $variations[$label] ) );
			}
		}
		
		if ( isset( $variations[ '_mini_desc' ] ) ) {
			$product = wc_gzd_set_crud_meta_data( $product, '_mini_desc', ( $variations[ '_mini_desc' ] === '' ? '' : wc_gzd_sanitize_html_text_field( $variations[ '_mini_desc' ] ) ) );
		}

		if ( isset( $variations[ 'delivery_time' ] ) && ! empty( $variations[ 'delivery_time' ] ) ) {
			$product = wc_gzd_set_crud_term_data( $product, $variations[ 'delivery_time' ], 'product_delivery_time' );
		} else {
			$product = wc_gzd_unset_crud_term_data( $product, 'product_delivery_time' );
		}

		// Is a service?
		$product = wc_gzd_set_crud_meta_data( $product, '_service', ( isset( $variations['_service'] ) ) ? 'yes' : '' );
		
		if ( wc_gzd_get_dependencies()->woocommerce_version_supports_crud() ) {
			$product->save();
		}
		
		return $wcfm_variation_data;
	}
	
	/**
	 * Third Party Product Meta data save
	 */
	function wcfm_thirdparty_products_manage_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $_POST;
		
		// Yoast SEO Support
		if(WCFM_Dependencies::wcfm_yoast_plugin_active_check()) {
			if(isset($wcfm_products_manage_form_data['yoast_wpseo_focuskw_text_input'])) {
				update_post_meta( $new_product_id, '_yoast_wpseo_focuskw_text_input', $wcfm_products_manage_form_data['yoast_wpseo_focuskw_text_input'] );
				update_post_meta( $new_product_id, '_yoast_wpseo_focuskw', $wcfm_products_manage_form_data['yoast_wpseo_focuskw_text_input'] );
			}
			if(isset($wcfm_products_manage_form_data['yoast_wpseo_metadesc'])) {
				update_post_meta( $new_product_id, '_yoast_wpseo_metadesc', strip_tags( $wcfm_products_manage_form_data['yoast_wpseo_metadesc'] ) );
			}
		}
		
		// WooCommerce Custom Product Tabs Lite Support
		if(WCFM_Dependencies::wcfm_wc_tabs_lite_plugin_active_check()) {
			if(isset($wcfm_products_manage_form_data['product_tabs'])) {
				$frs_woo_product_tabs = array();
				if( !empty( $wcfm_products_manage_form_data['product_tabs'] ) ) {
					foreach( $wcfm_products_manage_form_data['product_tabs'] as $frs_woo_product_tab ) {
						if( $frs_woo_product_tab['title'] ) {
							// convert the tab title into an id string
							$tab_id = strtolower( wc_clean( $frs_woo_product_tab['title'] ) );
		
							// remove non-alphas, numbers, underscores or whitespace
							$tab_id = preg_replace( "/[^\w\s]/", '', $tab_id );
		
							// replace all underscores with single spaces
							$tab_id = preg_replace( "/_+/", ' ', $tab_id );
		
							// replace all multiple spaces with single dashes
							$tab_id = preg_replace( "/\s+/", '-', $tab_id );
		
							// prepend with 'tab-' string
							$tab_id = 'tab-' . $tab_id;
							
							$frs_woo_product_tabs[] = array(
																							'title'   => wc_clean( $frs_woo_product_tab['title'] ),
																							'id'      => $tab_id,
																							'content' => $frs_woo_product_tab['content']
																						);
						}
					}
					update_post_meta( $new_product_id, 'frs_woo_product_tabs', $frs_woo_product_tabs );
				} else {
					delete_post_meta( $new_product_id, 'frs_woo_product_tabs' );
				}
			}
		}
		
		// WooCommerce barcode & ISBN Support
		if(WCFM_Dependencies::wcfm_wc_barcode_isbn_plugin_active_check()) {
			if(isset($wcfm_products_manage_form_data['barcode'])) {
				update_post_meta( $new_product_id, 'barcode', $wcfm_products_manage_form_data['barcode'] );
				update_post_meta( $new_product_id, 'ISBN', $wcfm_products_manage_form_data['ISBN'] );
			}
		}
		
		// WooCommerce MSRP Pricing Support
		if(WCFM_Dependencies::wcfm_wc_msrp_pricing_plugin_active_check()) {
			if(isset($wcfm_products_manage_form_data['_msrp_price'])) {
				update_post_meta( $new_product_id, '_msrp_price', strip_tags( $wcfm_products_manage_form_data['_msrp_price'] ) );
			}
		}
		
		// Quantities and Units for WooCommerce Support 
		if( $allow_quantities_units = apply_filters( 'wcfm_is_allow_quantities_units', true ) ) {
			if(WCFM_Dependencies::wcfm_wc_quantities_units_plugin_active_check()) {
				if(isset($wcfm_products_manage_form_data['_wpbo_override'])) {
					update_post_meta( $new_product_id, '_wpbo_override', 'on' );
					update_post_meta( $new_product_id, '_wpbo_deactive', isset( $wcfm_products_manage_form_data['_wpbo_deactive'] ) ? 'on' : '' );
					update_post_meta( $new_product_id, '_wpbo_step', strip_tags( $wcfm_products_manage_form_data['_wpbo_step'] ) );
					update_post_meta( $new_product_id, '_wpbo_minimum', strip_tags( $wcfm_products_manage_form_data['_wpbo_minimum'] ) );
					update_post_meta( $new_product_id, '_wpbo_maximum', strip_tags( $wcfm_products_manage_form_data['_wpbo_maximum'] ) );
					update_post_meta( $new_product_id, '_wpbo_minimum_oos', strip_tags( $wcfm_products_manage_form_data['_wpbo_minimum_oos'] ) );
					update_post_meta( $new_product_id, '_wpbo_maximum_oos', strip_tags( $wcfm_products_manage_form_data['_wpbo_maximum_oos'] ) );
					update_post_meta( $new_product_id, 'unit', strip_tags( $wcfm_products_manage_form_data['unit'] ) );
				} else {
					update_post_meta( $new_product_id, '_wpbo_override', '' );
				}
			}
		}
		
		// WooCommerce Product Fees Support
		if( $allow_product_fees = apply_filters( 'wcfm_is_allow_product_fees', true ) ) {
			if(WCFM_Dependencies::wcfm_wc_product_fees_plugin_active_check()) {
				update_post_meta( $new_product_id, 'product-fee-name', $wcfm_products_manage_form_data['product-fee-name'] );
				update_post_meta( $new_product_id, 'product-fee-amount', $wcfm_products_manage_form_data['product-fee-amount'] );
				$product_fee_multiplier = ( $wcfm_products_manage_form_data['product-fee-multiplier'] ) ? 'yes' : 'no';
				update_post_meta( $new_product_id, 'product-fee-multiplier', $product_fee_multiplier );
			}
		}
		
		// WooCommerce Bulk Discount Support
		if( $allow_bulk_discount = apply_filters( 'wcfm_is_allow_bulk_discount', true ) ) {
			if(WCFM_Dependencies::wcfm_wc_bulk_discount_plugin_active_check()) {
				$_bulkdiscount_enabled = ( $wcfm_products_manage_form_data['_bulkdiscount_enabled'] ) ? 'yes' : 'no';
				update_post_meta( $new_product_id, '_bulkdiscount_enabled', $_bulkdiscount_enabled );
				update_post_meta( $new_product_id, '_bulkdiscount_text_info', $wcfm_products_manage_form_data['_bulkdiscount_text_info'] );
				update_post_meta( $new_product_id, '_bulkdiscounts', $wcfm_products_manage_form_data['_bulkdiscounts'] );
				
				$bulk_discount_rule_counter = 0;
				foreach( $wcfm_products_manage_form_data['_bulkdiscounts'] as $bulkdiscount ) {
					$bulk_discount_rule_counter++;
					update_post_meta( $new_product_id, '_bulkdiscount_quantity_'.$bulk_discount_rule_counter, $bulkdiscount['quantity'] );
					update_post_meta( $new_product_id, '_bulkdiscount_discount_'.$bulk_discount_rule_counter, $bulkdiscount['discount'] );
				}
				
				if( $bulk_discount_rule_counter < 5 ) {
					for( $bdrc = ($bulk_discount_rule_counter+1); $bdrc <= 5; $bdrc++ ) {
						update_post_meta( $new_product_id, '_bulkdiscount_quantity_'.$bdrc, '' );
						update_post_meta( $new_product_id, '_bulkdiscount_discount_'.$bdrc, '' );
					}
				}
			}
		}
		
		// WooCommerce Product Fees Support
		if( apply_filters( 'wcfm_is_allow_role_based_price', true ) ) {
			if(WCFM_Dependencies::wcfm_wc_role_based_price_active_check()) {
				if( isset( $wcfm_products_manage_form_data['role_based_price'] ) ) {
					update_post_meta( $new_product_id, '_role_based_price', $wcfm_products_manage_form_data['role_based_price'] );	
				}
			}
		}
		
	}
}