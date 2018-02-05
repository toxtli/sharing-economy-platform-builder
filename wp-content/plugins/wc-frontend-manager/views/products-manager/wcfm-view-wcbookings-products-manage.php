<?php
/**
 * WCFM plugin views
 *
 * Plugin WC Booking Products Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfm/views
 * @version   2.1.0
 */
global $wp, $WCFM;

$product_id = 0;
$booking_qty = 1;

$min_date      = 0;
$min_date_unit = '';
$max_date = 12;
$max_date_unit = '';

$buffer_period= '';
$apply_adjacent_buffer = '';

$default_date_availability = '';

$booking_cost = '';
$booking_base_cost = '';
$display_cost = '';

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
	if( $product_id ) {
		//$product = wc_get_product( $product_id );
		$bookable_product = new WC_Product_Booking( $product_id );
		
		$booking_qty = $bookable_product->get_qty( 'edit' );
		
		$min_date      = $bookable_product->get_min_date_value( 'edit' );
		$min_date_unit = $bookable_product->get_min_date_unit( 'edit' );
		$max_date      = $bookable_product->get_max_date_value( 'edit' );
		$max_date_unit = $bookable_product->get_max_date_unit( 'edit' );
		
		$buffer_period = esc_attr( $bookable_product->get_buffer_period( 'edit' ) );
		$apply_adjacent_buffer = $bookable_product->get_apply_adjacent_buffer( 'edit' ) ? 'yes' : 'no';
		
		$default_date_availability = $bookable_product->get_default_date_availability( 'edit' );
		
		$booking_cost = $bookable_product->get_cost( 'edit' );
		$booking_base_cost = $bookable_product->get_base_cost( 'edit' );
		$display_cost = $bookable_product->get_display_cost( 'edit' );
	}
}

?>

<!-- Collapsible Booking 2  -->
<div class="page_collapsible products_manage_availability booking" id="wcfm_products_manage_form_availability_head"><label class="fa fa-clock-o"></label><?php _e('Availability', 'woocommerce-bookings'); ?><span></span></div>
<div class="wcfm-container booking">
	<div id="wcfm_products_manage_form_availability_expander" class="wcfm-content">
		<?php
		$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcbokings_availability_fields', array(  
					
					"_wc_booking_qty" => array('label' => __('Max bookings per block', 'woocommerce-bookings') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele booking', 'label_class' => 'wcfm_title booking', 'value' => $booking_qty, 'hints' => __( 'The maximum bookings allowed for each block. Can be overridden at resource level.', 'woocommerce-bookings' ), 'attributes' => array( 'min' => '', 'step' => '1' ) ),
					"_wc_booking_min_date" => array('label' => __('Minimum block bookable', 'woocommerce-bookings') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele booking', 'label_class' => 'wcfm_title booking', 'value' => $min_date ),
					"_wc_booking_min_date_unit" => array('type' => 'select', 'options' => array( 'month' => __( 'Month(s)', 'woocommerce-bookings'), 'day' => __( 'Day(s)', 'woocommerce-bookings' ), 'hour' => __( 'Hour(s)', 'woocommerce-bookings' ), 'minute' => __( 'Minute(s)', 'woocommerce-bookings' ) ), 'class' => 'wcfm-select wcfm_ele booking', 'label_class' => 'wcfm_title booking', 'value' => $min_date_unit, 'desc_class' => 'in_the_future', 'desc' => __( 'in the future', 'woocommerce-bookings' ) ),
					"_wc_booking_max_date" => array('label' => __('Maximum block bookable', 'woocommerce-bookings') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele booking', 'label_class' => 'wcfm_title booking', 'value' => $max_date ),
					"_wc_booking_max_date_unit" => array('type' => 'select', 'options' => array( 'month' => __( 'Month(s)', 'woocommerce-bookings'), 'day' => __( 'Day(s)', 'woocommerce-bookings' ), 'hour' => __( 'Hour(s)', 'woocommerce-bookings' ), 'minute' => __( 'Minute(s)', 'woocommerce-bookings' ) ), 'class' => 'wcfm-select wcfm_ele booking', 'label_class' => 'wcfm_title booking', 'value' => $max_date_unit, 'desc_class' => 'in_the_future', 'desc' => __( 'in the future', 'woocommerce-bookings' ) ),
					"_wc_booking_buffer_period" => array('label' => __('Require a buffer period of', 'woocommerce-bookings') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele booking', 'label_class' => 'wcfm_title booking', 'value' => $buffer_period, 'desc' => '<span class="_wc_booking_buffer_period_unit"></span>' . __( 'between bookings', 'woocommerce-bookings' ) ),
					"_wc_booking_apply_adjacent_buffer" => array('label' => __('Adjacent Buffering?', 'woocommerce-bookings') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele booking', 'label_class' => 'wcfm_title checkbox_title booking', 'value' => 'yes', 'dfvalue' => $apply_adjacent_buffer, 'hints' => __( 'By default buffer period applies forward into the future of a booking. Enabling this option will apply adjacently ( Before and After Bookings).', 'woocommerce-bookings' ) ),
					"_wc_booking_default_date_availability" => array('label' => __('All dates are...', 'woocommerce-bookings') , 'type' => 'select', 'options' => array( 'available' => __( 'available by default', 'woocommerce-bookings'), 'non-available' => __( 'not-available by default', 'woocommerce-bookings' ) ), 'class' => 'wcfm-select wcfm_ele booking', 'label_class' => 'wcfm_title booking', 'value' => $default_date_availability, 'hints' => __( 'This option affects how you use the rules below.', 'woocommerce-bookings' ) )
																										
																														), $product_id ) );
		?>
	</div>
</div>
<!-- end collapsible Booking -->
<div class="wcfm_clearfix"></div>

<!-- Collapsible Booking 3  -->
<div class="page_collapsible products_manage_costs booking" id="wcfm_products_manage_form_costs_head"><label class="fa fa-currency"><?php echo get_woocommerce_currency_symbol() ; ?></label><?php _e('Costs', 'woocommerce-bookings'); ?><span></span></div>
<div class="wcfm-container booking">
	<div id="wcfm_products_manage_form_costs_expander" class="wcfm-content">
		<?php
		$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcbokings_cost_fields', array(  
					
					"_wc_booking_cost" => array('label' => __('Base cost', 'woocommerce-bookings') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele booking', 'label_class' => 'wcfm_title booking', 'value' => $booking_cost, 'hints' => __( 'One-off cost for the booking as a whole.', 'woocommerce-bookings' ), 'attributes' => array( 'min' => '', 'step' => '0.01' ) ),
					"_wc_booking_base_cost" => array('label' => __('Block cost', 'woocommerce-bookings') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele booking', 'label_class' => 'wcfm_title booking', 'value' => $booking_base_cost, 'hints' => __( 'This is the cost per block booked. All other costs (for resources and persons) are added to this.', 'woocommerce-bookings' ), 'attributes' => array( 'min' => '', 'step' => '0.01' ) ),
					"_wc_display_cost" => array('label' => __('Display cost', 'woocommerce-bookings') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele booking', 'label_class' => 'wcfm_title booking', 'value' => $display_cost, 'hints' => __( 'The cost is displayed to the user on the frontend. Leave blank to have it calculated for you. If a booking has varying costs, this will be prefixed with the word `from:`.', 'woocommerce-bookings' ), 'attributes' => array( 'min' => '', 'step' => '0.01' ) ),
																										
																														), $product_id ) );
		?>
	</div>
</div>
<!-- end collapsible Booking -->
<div class="wcfm_clearfix"></div>