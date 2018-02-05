<?php
/**
 * WCFM plugin views
 *
 * Plugin GEO My WP Products Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfm/views/product-manager
 * @version   3.2.4
 */
global $wp, $WCFM, $wpdb;

$geomywp_settings   = get_option( 'gmw_options' );

$product_id = '';
$post_info = array();
if ( !isset( $geomywp_settings[ 'post_types_settings' ] ) || !isset( $geomywp_settings[ 'post_types_settings' ][ 'post_types' ] ) || empty( $geomywp_settings[ 'post_types_settings' ][ 'post_types' ] ) || ( !in_array( 'product', $geomywp_settings[ 'post_types_settings' ][ 'post_types' ] ) ) ) { return; }

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	if( $product_id ) {
		$post_info = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "places_locator WHERE post_id = %d", array( $product_id ) ) );
	}
}

if ( empty( $post_info ) ) {
	$post_info = ( object ) array(
			'post_id'           => '',
			'feature'           => '',
			'post_status'       => '',
			'post_type'         => '',
			'post_title'        => '',
			'lat'               => '',
			'long'              => '',
			'street_number'     => '',
			'street_name'       => '',
			'street'            => '',
			'apt'               => '',
			'city'              => '',
			'state'             => '',
			'state_long'        => '',
			'zipcode'           => '',
			'country'           => '',
			'country_long'      => '',
			'address'           => apply_filters( 'wcfm_geo_locator_default_address', '' ),
			'formatted_address' => '',
			'phone'             => '',
			'fax'               => '',
			'email'             => '',
			'website'           => '',
			'map_icon'          => ''
	);
}

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

wp_register_style( 'gmw-pt-admin-style', 		GMW_PT_URL . 'assets/css/style-admin.css' );
wp_register_script( 'gmw-admin-address-picker', $WCFM->library->js_lib_url . 'products-manager/wcfm-script-geomywp-addresspicker.js', array( 'jquery' ), GMW_VERSION, true );

//add default values if not exist to prevent JavaScript error
if ( !isset( $geomywp_settings['post_types_settings']['edit_post_zoom_level'] ) ) $geomywp_settings['post_types_settings']['edit_post_zoom_level'] = 7;
if ( !isset( $geomywp_settings['post_types_settings']['edit_post_latitude'] ) )   $geomywp_settings['post_types_settings']['edit_post_latitude']   = '40.7115441';
if ( !isset( $geomywp_settings['post_types_settings']['edit_post_longitude'] ) )  $geomywp_settings['post_types_settings']['edit_post_longitude']  = '-74.01348689999998';

wp_localize_script( 'gmw-admin-address-picker', 'gmwSettings', $geomywp_settings );

?>

<!-- collapsible - GEO my WP Support -->
<div class="page_collapsible products_manage_yoast simple variable grouped external booking" id="wcfm_products_manage_form_geomywp_head"><label class="fa fa-map-marker"></label><?php _e( 'Location', 'GMW' ); ?><span></span></div>
<div class="wcfm-container simple variable external grouped booking">
	<div id="wcfm_products_manage_form_geomywp_expander" class="wcfm-content">
		<div class="gmw-location-section map">
			<h3>
				<?php _e( 'Use the map to drag and drop the marker to the desired location.','GMW' );?>
			</h3>
			<div id="map"></div>
		</div>
		
		<div class="gmw-location-section current-location">
			<h3>
				<?php _e('Get your current location','GMW'); ?>
			</h3>
			<div class="current-location-inner">
				<input type="button" id="gmw-admin-locator-btn" class="button-primary"
					value="<?php _e('Locate Me','GMW'); ?>" />
			</div>
		</div>
		
		<div class="gmw-location-section autocomplete">
			<h3>
				<?php _e('Type an address to autocomplete','GMW'); ?>
			</h3>
			<div class="autocomplete-location-inner">
				<input type="text" id="wppl-addresspicker"
					value="<?php echo sanitize_text_field( esc_attr( stripslashes( $post_info->address ) ) ); ?>" />
			</div>
		</div>
		
		<div class="clear"></div>

		<div class="gmw-location-section gmw-location-manually-wrapper">
			<h3><?php _e('Enter Location Manually','GMW'); ?></h3>
			
			<div class="gmw-location-section-inner">

				<div class="address">

					<h3><?php _e('Address','GMW'); ?></h3>
					
					<div class="gmw-location-section-description">
						<p><?php _e('Fill out the address fields and click "Get Lat/Long" to retrive the latitude and longitude of the location.','GMW'); ?></p>
					</div>
					
					<table class="gmw-admin-location-table">
						<tr>
							<th><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][0]['id']; ?>"><?php echo $wcfm_geomywp_meta_fields['fields'][0]['name']; ?></label></th>
							<td><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][0]['id']; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][0]['id']; ?>" value="<?php echo sanitize_text_field( esc_attr( stripslashes( $post_info->street ) ) ); ?>"   /><br /></td>
						</tr>
						<tr>
							<th><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][1]['id']; ?>"><?php echo $wcfm_geomywp_meta_fields['fields'][1]['name']; ?></label></th>
							<td><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][1]['id']; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][1]['id']; ?>" value="<?php echo $post_info->apt; ?>"   /><br /></td>
						</tr>
						<tr>
							<th><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][2]['id']; ?>"><?php echo $wcfm_geomywp_meta_fields['fields'][2]['name']; ?></label></th>
							<td><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][2]['id']; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][2]['id']; ?>" value="<?php echo sanitize_text_field( esc_attr( stripslashes( $post_info->city ) ) ); ?>"   /><br /></td>
						</tr>
						<tr>
							<th><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][3]['id']; ?>"><?php echo $wcfm_geomywp_meta_fields['fields'][3]['name']; ?></label></th>
							<td><input type="text" id="<?php echo $wcfm_geomywp_meta_fields['fields'][3]['id']; ?>" class="<?php echo $wcfm_geomywp_meta_fields['fields'][3]['id']; ?>" value="<?php echo $post_info->state; ?>"/><br /></td>
						</tr>
						<tr>
							<th><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][4]['id']; ?>"><?php echo $wcfm_geomywp_meta_fields['fields'][4]['name']; ?></label></th>
							<td><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][4]['id']; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][4]['id']; ?>" value="<?php echo $post_info->zipcode; ?>"   /><br /></td>
						</tr>
						<tr>
							<th><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][5]['id']; ?>"><?php echo $wcfm_geomywp_meta_fields['fields'][5]['name']; ?></label></th>
							<td><input type="text" id="<?php echo $wcfm_geomywp_meta_fields['fields'][5]['id']; ?>" class="<?php echo $wcfm_geomywp_meta_fields['fields'][5]['id']; ?>" value="<?php echo $post_info->country; ?>"/><br /></td>
						</tr>
						<tr>
							<th></th>
							<td><input type="button"id="gmw-admin-getlatlong-btn" class="button-primary" value="Get Lat/Long" style="margin: 10px 0px;"></td>
						</tr>
					</table>
					<table style="display:none;">

				<tr>
							<th><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][19]['id']; ?>"><?php echo $wcfm_geomywp_meta_fields['fields'][19]['name']; ?></label></th>
						</tr>
						<tr>
							<td><input type="text" id="<?php echo $wcfm_geomywp_meta_fields['fields'][19]['id']; ?>" name="<?php echo $wcfm_geomywp_meta_fields['fields'][19]['id']; ?>" class="<?php echo $wcfm_geomywp_meta_fields['fields'][19]['id']; ?>" value="<?php echo $post_info->street_number; ?>"   /><br /></td>
						</tr>
						
						<tr>
							<th><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][20]['id']; ?>"><?php echo $wcfm_geomywp_meta_fields['fields'][20]['name']; ?></label></th>
						</tr>
						<tr>
							<td><input type="text" id="<?php echo $wcfm_geomywp_meta_fields['fields'][20]['id']; ?>" name="<?php echo $wcfm_geomywp_meta_fields['fields'][20]['id']; ?>" class="<?php echo $wcfm_geomywp_meta_fields['fields'][20]['id']; ?>" value="<?php echo $post_info->street_name; ?>"   /><br /></td>
						</tr>
						
						<tr>
							<th><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][3]['id']; ?>"><?php echo $wcfm_geomywp_meta_fields['fields'][3]['name']; ?></label></th>
						</tr>
						<tr>
							<td><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][3]['id']; ?>" class="<?php echo $wcfm_geomywp_meta_fields['fields'][3]['id']; ?>" value="<?php echo $post_info->state; ?>"   /><br /></td>
						</tr>
						
						<tr>
							<th><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][16]['id']; ?>"><?php echo $wcfm_geomywp_meta_fields['fields'][16]['name']; ?></label></th>
						</tr>
						<tr>
							<td><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][16]['id']; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][16]['id']; ?>" class="<?php echo $wcfm_geomywp_meta_fields['fields'][16]['id']; ?>" value="<?php echo $post_info->state_long; ?>" style="width: 100%;"/><br /></td>
						</tr>
						
						<tr>
							<th><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][5]['id']; ?>"><?php echo $wcfm_geomywp_meta_fields['fields'][5]['name']; ?></label></th>
						</tr>
						<tr>
							<td><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][5]['id']; ?>"  class="<?php echo $wcfm_geomywp_meta_fields['fields'][5]['id']; ?>" value="<?php echo $post_info->country; ?>"   /><br /></td>
						</tr>
						<tr>
							<th><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][17]['id']; ?>"><?php echo $wcfm_geomywp_meta_fields['fields'][17]['name']; ?></label></th>
						</tr>
						<tr>
							<td><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][17]['id']; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][17]['id']; ?>" class="<?php echo $wcfm_geomywp_meta_fields['fields'][17]['id']; ?>" value="<?php echo $post_info->country_long; ?>" style="width: 100%;" /><br /></td>
						</tr
						<tr>
							<th><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][14]['id']; ?>"><?php echo $wcfm_geomywp_meta_fields['fields'][14]['name']; ?></label></th>
						</tr>
						<tr>
							<td><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][14]['id']; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][14]['id']; ?>" class="<?php echo $wcfm_geomywp_meta_fields['fields'][14]['id']; ?>" value="<?php echo sanitize_text_field( esc_attr( stripslashes( $post_info->address ) ) ); ?>" style="width: 100%;"/><br /></td>
						</tr>
						<tr>
							<th><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][18]['id']; ?>"><?php echo $wcfm_geomywp_meta_fields['fields'][18]['name']; ?></label></th>
						</tr>
						<tr>
							<td><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][18]['id']; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][18]['id']; ?>" class="<?php echo $wcfm_geomywp_meta_fields['fields'][18]['id']; ?>" value="<?php echo sanitize_text_field( esc_attr( stripslashes( $post_info->formatted_address ) ) ); ?>" style="width: 100%;" /><br /></td>
						</tr>
					</table>
				</div>
				<div class="right-side">
					<div class="coords">
						<h3><?php _e('Latitude / Longitude','GMW'); ?></h3>
						<div class="gmw-location-section-description">
							<p><?php _e('Fill out the Latitude and Longitude fields and click on "Get Address" to retrive the address of the location.','GMW'); ?></p>
						</div>
						<table class="gmw-admin-location-table">
							<tr>
								<th><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][12]['id']; ?>"><?php echo $wcfm_geomywp_meta_fields['fields'][12]['name'];?></label></th>
								<td>
									<input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][12]['id']; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][12]['id']; ?>" class="<?php echo $wcfm_geomywp_meta_fields['fields'][12]['id']; ?>" value="<?php echo $post_info->lat; ?>"  />
									<input type="hidden" name="gmw_check_lat" id="gmw_check_lat" value"">
								</td>
							</tr>
							<tr>
								<th><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][13]['id']; ?>"><?php echo $wcfm_geomywp_meta_fields['fields'][13]['name']; ?></label></th>
								<td>
									<input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][13]['id']; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][13]['id']; ?>" class="<?php echo $wcfm_geomywp_meta_fields['fields'][13]['id']; ?>" value="<?php echo $post_info->long; ?>"  />
									<input type="hidden" name="gmw_check_long" id="gmw_check_long" value"">
								</td>
							</tr>
							<tr>
								<th></th>
								<td><input style="margin: 10px 0px;" type="button" id="gmw-admin-getaddress-btn" class="button-primary" value="Get Address" /></td>
							</tr>
						</table>
					</div>

					<div class="delete-location-wrapper">
						<h3><?php _e('Delete Location','GMW'); ?></h3>
						<div class="delete-locaiton-inner">
							<input type="button" style="float:none;" id="gmw-admin-delete-btn" class="button-primary" value="<?php _e('Delete address','GMW'); ?>" />
						</div>
					</div>

					<div id="gmw-getting-info" class="location-status-wrapper">
						<h3 ><?php _e('Location status','GMW'); ?></h3>
						<div class="location-status-inner">
							<div id="gmw-location-loader" style="display:none;background:none; border:0px;height: 23px;"><img style="width:15px;margin-right: 5px"src="<?php echo GMW_IMAGES; ?>/gmw-loader.gif" id="ajax-loader-image" alt=" "><?php _e('Loading...','GMW'); ?></div>
							<div id="gmw-good-location-message" class="" style="display:none;height: 23px;"><p><?php _e( 'Location is ready', 'GMW'); ?></p></div>
							<div id="gmw-bad-location-message" class="gmw-location-message" style="height: 23px;"><p style="color:red"><?php _e( 'A valid address, latitude and longitude are required to save the Location','GMW'); ?></p></div>
						</div>
					</div>

					<div class="clear"></div>

				</div>
			</div>
		</div>
		
		<div class="gmw-location-section additional-information-wrapper">
			<h3 style="font-size:13px;"><?php _e( 'Additional Information','GMW' ); ?></h3>
		
			<div style="padding:5px;">

		<div class="metabox-tabs-div">
			<ul class="metabox-tabs" id="metabox-tabs">
				<li class="active extra-info-tab"><a class="active" href="javascript:void(null);"><?php _e('Contact Information','GMW'); ?></a></li>
				<li class="days-hours-tab"><a href="javascript:void(null);"><?php _e('Days & Hours','GMW'); ?></a></li>
			</ul>

					<div class="extra-info-tab">
						<h4 class="heading"><?php _e('Additional Information','GMW'); ?></h4>
				<table class="form-table">
					<tr>
								<th><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][6]['id']; ?>"><?php echo $wcfm_geomywp_meta_fields['fields'][6]['name']; ?></label></th>
						<td><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][6]['id']; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][6]['id']; ?>" value="<?php echo $post_info->phone; ?>" /></td>
							</tr>
							<tr>
								<th><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][7]['id']; ?>"><?php echo $wcfm_geomywp_meta_fields['fields'][7]['name']; ?></label></th>
						<td><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][7]['id']; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][7]['id']; ?>" value="<?php echo $post_info->fax; ?>"/></td>
							</tr>
							<tr>
								<th><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][8]['id']; ?>"><?php echo $wcfm_geomywp_meta_fields['fields'][8]['name']; ?></label></th>
						<td><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][8]['id']; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][8]['id']; ?>" value="<?php echo $post_info->email; ?>" placeholder="<?php echo $wcfm_geomywp_meta_fields['fields'][8]['placeholder']; ?>" /></td>
							</tr>
							<tr>
								<th><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][9]['id']; ?>"><?php echo $wcfm_geomywp_meta_fields['fields'][9]['name']; ?></label></th>
						<td>
							<div style="position: relative;">
								<!-- <span style="position: absolute; padding:8px 3px 8px 8px; font-size: 13px; color:#888 ">http://</span> -->
								<input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][9]['id']; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][9]['id']; ?>" value="<?php echo $post_info->website; ?>" placeholder="<?php echo $wcfm_geomywp_meta_fields['fields'][9]['placeholder']; ?>" />
							</div>		
						</td>
							</tr>
						</table>
					</div>
					<?php 
					$days_hours = get_post_meta( $product_id, $wcfm_geomywp_meta_fields['fields'][15]['id'], true );
					$days_hours = ( isset( $days_hours ) && is_array( $days_hours ) && array_filter( $days_hours ) )  ? get_post_meta( $product_id, $wcfm_geomywp_meta_fields['fields'][15]['id'], true ) : false;
					?>
					<div class="days-hours-tab">
						<h4 class="heading"><?php _e( 'Days & Hours', 'GMW' ); ?></h4>
						<table class="form-table">
							<tr>
								<th style="width:30px"><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>"><?php _e('Days','GMW'); ?></label></th>
								<td style="width:150px"><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id'].'[0][days]'; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>" value="<?php echo $days_hours[0]['days']; ?>" style="width:150px" /><br /></td>
								<th style="width:30px"><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>"><?php _e( 'Hours','GMW' ); ?></label></th>
								<td><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']. '[0][hours]'; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>" value="<?php echo $days_hours[0]['hours']; ?>" style="width:150px" /><br /></td>
							</tr>
							<tr>
								<th style="width:30px"><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>"><?php _e('Days','GMW'); ?></label></th>
								<td style="width:150px"><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']. '[1][days]'; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>" value="<?php echo $days_hours[1]['days']; ?>" style="width:150px" /><br /></td>
								<th style="width:30px"><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>"><?php _e('Hours','GMW'); ?></label></th>
								<td><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']. '[1][hours]'; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>" value="<?php echo $days_hours[1]['hours']; ?>" style="width:150px" /><br /></td>
								</tr>
							<tr>
								<th style="width:30px"><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>"><?php _e('Days','GMW'); ?></label></th>
								<td style="width:150px"><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id'].'[2][days]'; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>" value="<?php echo $days_hours[2]['days']; ?>" style="width:150px" /><br /></td>
								<th style="width:30px"><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>"><?php _e('Hours','GMW'); ?></label></th>
								<td><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id'].'[2][hours]'; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>" value="<?php echo $days_hours[2]['hours'];?>" style="width:150px" /><br /></td>
							</tr>
							<tr>
								<th style="width:30px"><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>"><?php _e('Days','GMW'); ?></label></th>
								<td style="width:150px"><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id'].'[3][days]'; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>" value="<?php echo $days_hours[3]['days']; ?>" style="width:150px" /><br /></td>
								<th style="width:30px"><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>"><?php _e('Hours','GMW'); ?></label></th>
								<td><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id'].'[3][hours]'; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>" value="<?php echo $days_hours[3]['hours']; ?>" style="width:150px" /><br /></td>
							</tr>
							<tr>
								<th style="width:30px"><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>"><?php _e('Days','GMW'); ?></label></th>
								<td style="width:150px"><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id'].'[4][days]'; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>" value="<?php echo $days_hours[4]['days']; ?>" style="width:150px" /><br /></td>
								<th style="width:30px"><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>"><?php _e('Hours','GMW'); ?></label></th>
								<td><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id'].'[4][hours]'; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>" value="<?php echo $days_hours[4]['hours']; ?>" style="width:150px" /><br /></td>
							</tr>
							<tr>
								<th style="width:30px"><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>"><?php _e('Days','GMW'); ?></label></th>
								<td style="width:150px"><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id'].'[5][days]'; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>" value="<?php echo $days_hours[5]['days']; ?>" style="width:150px" /><br /></td>
								<th style="width:30px"><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>"><?php _e('Hours','GMW'); ?></label></th>
								<td><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id'].'[5][hours]'; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>" value="<?php echo $days_hours[5]['hours']; ?>" style="width:150px" /><br /></td>
							</tr>
							<tr>
								<th style="width:30px"><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>"><?php _e('Days','GMW'); ?></label></th>
								<td style="width:150px"><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id'].'[6][days]'; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>" value="<?php echo $days_hours[6]['days']; ?>" style="width:150px" /><br /></td>
								<th style="width:30px"><label for="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>"><?php _e('Hours','GMW'); ?></label></th>
								<td><input type="text" name="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id'].'[6][hours]'; ?>" id="<?php echo $wcfm_geomywp_meta_fields['fields'][15]['id']; ?>" value="<?php echo $days_hours[6]['hours']; ?>" style="width:150px" /><br /></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- end collapsible -->
<div class="wcfm_clearfix"></div>

<?php 
			
wp_enqueue_style( 'gmw-pt-admin-style' );
wp_enqueue_script( 'google-maps' );
wp_enqueue_script( 'jquery-ui-autocomplete' );
wp_enqueue_script( 'gmw-admin-address-picker' );

	//make sure address_mandatory is not undefined
	$geomywp_settings['post_types_settings']['mandatory_address'] = gmw_get_option( 'post_types_settings', 'mandatory_address', false );

wp_localize_script( 'gmw-admin-address-picker','gmwOptions', $geomywp_settings );
?>