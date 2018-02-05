<?php
class wcfm_dhl_woocommerce_shipping_admin{

	public $found_rates = array();
public function __construct(){

	$this->settings 					 = get_option( 'woocommerce_'.WF_DHL_ID.'_settings', null );
	$this->custom_services 				 = isset( $this->settings['services'] ) ? $this->settings['services'] : '';
	$this->enable_shipping_label 		 = isset( $this->settings['enabled_label'] ) ? $this->settings['enabled_label'] : 'yes';
	$this->image_type 					 = isset( $this->settings['image_type'] ) ? $this->settings['image_type'] : '';
	$this->services 					 = include( 'data-wf-service-codes.php' );
	$this->sat_delivery 				 = isset( $this->settings['enable_saturday_delivery'] ) ? $this->settings['enable_saturday_delivery'] : '';
	$this->origin_country = isset($this->settings['base_country']) ? $this->settings['base_country'] : '';
	$this->return_label_key 				 = isset( $this->settings['return_label_key'] ) ? $this->settings['return_label_key'] : '';
	$this->cash_on_delivery 				 = isset( $this->settings['cash_on_delivery'] ) ? $this->settings['cash_on_delivery'] : '';
	$this->show_front_end_shipping_method = isset( $this->settings['show_front_end_shipping_method'] ) ? $this->settings['show_front_end_shipping_method'] : '';
	$this->debug 						 = ( $bool = $this->settings[ 'debug' ] ) && $bool == 'yes' ? true : false;
	$this->default_domestic_service		 = isset( $this->settings['default_domestic_service'] ) ? $this->settings['default_domestic_service'] : '';
	$this->default_international_service = isset( $this->settings['default_international_service'] ) ? $this->settings['default_international_service'] : '';
	$this->plt = ( !empty($this->settings['plt']) && $this->settings['plt'] === 'yes' ) ? true : false;
	if ( $this->settings['dimension_weight_unit'] == 'KG_CM' ) {
		$this->weight_unit = 'KGS';
		$this->dim_unit    = 'CM';
	} else {
		$this->weight_unit = 'LBS';
		$this->dim_unit    = 'IN';
	}
	$this->account_number  = isset( $this->settings['account_number'] ) ?  $this->settings['account_number'] : '';
	$this->add_trackingpin_shipmentid = isset($this->settings['add_trackingpin_shipmentid']) ? $this->settings['add_trackingpin_shipmentid'] : 'no';
	$this->id = WF_DHL_ID;
	$_stagingUrl           = 'https://xmlpitest-ea.dhl.com/XMLShippingServlet';
	$_productionUrl        = 'https://xmlpi-ea.dhl.com/XMLShippingServlet';
	$this->production      = (!empty($this->settings['production']) && $this->settings['production'] === 'yes') ? true : false;
	$this->service_url     = ($this->production == true) ? $_productionUrl : $_stagingUrl;
	$this->site_id         = !empty($this->settings['site_id']) ? $this->settings['site_id'] : '' ;
	$this->site_password   = !empty($this->settings['site_password']) ? $this->settings['site_password'] : '';
	$this->freight_shipper_city = isset($this->settings['freight_shipper_city']) ? $this->settings['freight_shipper_city'] : '';
	$this->origin          = apply_filters('woocommerce_dhl_origin_postal_code', str_replace(' ', '', strtoupper(isset($this->settings['origin']) ? $this->settings['origin'] : '')));
	$this->request_type    = !empty( $this->settings['request_type'] ) ? $this->settings['request_type'] : '';
	$this->dimension_unit  = $this->settings['dimension_weight_unit'] == 'LBS_IN' ? 'IN' : 'CM';
	$this->weight_unit     = $this->settings['dimension_weight_unit'] == 'LBS_IN' ? 'LBS' : 'KG';
	$this->conversion_rate = !empty($this->settings['conversion_rate']) ? $this->settings['conversion_rate'] : '';
			
	$this->conversion_rate = apply_filters('wf_dhl_conversion_rate',    $this->conversion_rate, $this->settings['dhl_currency_type']);
	$this->quoteapi_dimension_unit = $this->dimension_unit;
	$this->quoteapi_weight_unit = $this->weight_unit == 'LBS' ? 'LB' : 'KG';
	$this->insure_contents = ( $bool = $this->settings['insure_contents'] ) && $bool == 'yes' ? true : false;
	$this->select_service_check_box = (isset($this->settings['services_select']) && $this->settings['services_select'] ==='yes') ? true : false;
	//add_action('load-edit.php', array( $this, 'wf_orders_bulk_action' ) ); //to handle post id for bulk actions		
	//add_action('admin_notices', array( $this,'bulk_label_admin_notices') );
	 
	if (is_admin() && $this->enable_shipping_label === 'yes') {
		//add_action('add_meta_boxes', array($this, 'wf_add_dhl_metabox'),15);
	}


	if ( isset( $_GET['wf_dhl_generate_packages'] ) ) {
		$this->wf_dhl_generate_packages();
	}

	if ( isset( $_GET['wf_dhl_generate_return_packages'] ) ) {
		$this->wf_dhl_generate_return_packages();
	}

	if ( isset( $_GET['dhl_product_choose_return_shipment'] ) ) {
		$this->dhl_product_choose_return_shipment();
	}
	
	if ( isset( $_GET['wf_dhl_process_return_packages'] ) ) {
		$this->wf_dhl_process_return_packages();
	}
	if (isset($_GET['wf_dhl_createshipment'])) {
		$this->wf_dhl_createshipment();
	}
	if (isset($_GET['wf_dhl_create_return_shipment'])) {
		$this->wf_dhl_create_return_shipment();
	}
	if (isset($_GET['wf_dhl_delete_label'])) {
		$this->wf_dhl_delete_label();
	}
	if (isset($_GET['wf_dhl_delete_return_label'])) {
		$this->wf_dhl_delete_return_label();
	}

	if (isset($_GET['wf_dhl_viewlabel'])) {
		$this->wf_dhl_viewlabel();
	}
	if (isset($_GET['wf_dhl_viewreturnlabel'])) {
		$this->wf_dhl_viewreturnlabel();
	}
	
	if (isset($_GET['wf_dhl_view_commercial_invoice'])) {
		$this->wf_dhl_view_commercial_invoice();
	}
	if (isset($_GET['wf_dhl_view_return_commercial_invoice'])) {
		$this->wf_dhl_view_return_commercial_invoice();
	}
	if (isset($_GET['wf_dhl_generate_packages_rates'])) {
		$this->wf_dhl_generate_packages_rates();
	}


}

function wf_dhl_delete_return_label()
{
	$get_id = $_GET['wf_dhl_delete_return_label'];
	
	$return_shipment_id = get_post_meta($get_id,'wf_woo_dhl_return_shipmentId');
	if(!empty($return_shipment_id))
	{
		foreach ($return_shipment_id as $value) {
			delete_post_meta($get_id,'wf_woo_dhl_return_shippingLabel_'.$value );
			delete_post_meta($get_id,'wf_woo_dhl_return_packageDetails_'.$value );
			delete_post_meta($get_id,'wf_woo_dhl_shipping_return_commercialInvoice_'.$value );
			
		}
	}		
	delete_post_meta($get_id,'wf_woo_dhl_return_shipmentId');
	delete_post_meta($get_id,'wf_woo_dhl_shipmentReturnErrorMessage');
	delete_post_meta($get_id,'wf_woo_dhl_return_service_code');
	delete_post_meta( $get_id, '_wf_dhl_stored_return_packages');
	delete_post_meta( $get_id, '_wf_dhl_process_return_shipment');
	delete_post_meta( $get_id, '_wf_dhl_stored_return_packages');
	

	//wp_redirect( admin_url( '/post.php?post='.$get_id.'&action=edit') );
	?>
	<script>
	window.location = "<?php echo get_wcfm_view_order_url( $get_id ); ?>";
	</script>
	<?php
	exit;

}


function wf_dhl_delete_label()
{
	$get_id = $_GET['wf_dhl_delete_label'];
	$shipment_id = get_post_meta($get_id,'wf_woo_dhl_shipmentId');

	delete_post_meta($get_id,'wfdhlexpresstrackingmsg');
	foreach ($shipment_id as $value) {
		delete_post_meta($get_id,'wf_woo_dhl_shippingLabel_'.$value );
		delete_post_meta($get_id,'wf_woo_dhl_shipping_commercialInvoice_'.$value );
		
	}
	$return_shipment_id = get_post_meta($get_id,'wf_woo_dhl_return_shipmentId');
	if(!empty($return_shipment_id))
	{
		foreach ($return_shipment_id as $value) {
			delete_post_meta($get_id,'wf_woo_dhl_return_shippingLabel_'.$value );
			delete_post_meta($get_id,'wf_woo_dhl_return_packageDetails_'.$value );
			delete_post_meta($get_id,'wf_woo_dhl_shipping_return_commercialInvoice_'.$value );
			
		}
	}		
	delete_post_meta($get_id,'wf_woo_dhl_shipmentId');
	delete_post_meta($get_id,'wf_woo_dhl_shipmentErrorMessage');
	delete_post_meta($get_id,'wf_woo_dhl_service_code');
	delete_post_meta($get_id,'wf_woo_dhl_return_shipmentId');
	delete_post_meta($get_id,'wf_woo_dhl_shipmentReturnErrorMessage');
	delete_post_meta($get_id,'wf_woo_dhl_return_service_code');
	delete_post_meta( $get_id, '_wf_dhl_stored_return_packages');
	delete_post_meta( $get_id, '_wf_dhl_process_return_shipment');
	delete_post_meta( $get_id, '_wf_dhl_stored_return_packages');
	

	//wp_redirect( admin_url( '/post.php?post='.$get_id.'&action=edit') );
	?>
	<script>
	window.location = "<?php echo get_wcfm_view_order_url( $get_id ); ?>";
	</script>
	<?php
	exit;

}
function wf_dhl_generate_packages(){
	//if( !$this->wf_user_permission() ) {
		//echo "You don't have admin privileges to view this page.";
		//exit;
	//}
	
	$post_id	=	base64_decode($_GET['wf_dhl_generate_packages']);
	
	$order = $this->wf_load_order( $post_id );
	if ( !$order ) return;
	
	if ( ! class_exists( 'wf_dhl_woocommerce_shipping_admin_helper' ) )
		include_once ( WF_DHL_PAKET_EXPRESS_ROOT_PATH . 'dhl_express/includes/class-wf-dhl-woocommerce-shipping-admin-helper.php' );
	
	$woodhlwrapper = new wf_dhl_woocommerce_shipping_admin_helper();
	$packages	=	$woodhlwrapper->wf_get_package_from_order($order);
	
	foreach ($packages as $key => $package) {
		$package_single = $woodhlwrapper->get_dhl_packages($package);
		$package_data[] = $package_single;
		$dhl_requests[] = $this->get_service_request($order,$package_single);
	}
	update_post_meta( $post_id, '_wf_dhl_stored_packages', $package_data );
	 $result = array();
	 if ($dhl_requests) {
	 	try {
			foreach ( $dhl_requests as $key => $request ) {
				  $this->process_result($this->get_result($request));
			}            
        } catch (Exception $e) {
            //$this->debug(print_r($e, true), 'error');
            return false;
        }
        update_post_meta( $post_id, '_wf_dhl_available_services', $this->found_rates );
    }

	//$this->wf_available_services($post_id);
	?>
	<script>
	window.location = "<?php echo get_wcfm_view_order_url( $post_id ); ?>";
	</script>
	<?php
	exit;
}
function wf_dhl_generate_packages_rates()
{
	if( !$this->wf_user_permission() ) {
		echo "You don't have admin privileges to view this page.";
		exit;
	}
	
	$post_id	=	base64_decode($_GET['wf_dhl_generate_packages_rates']);
	
	$lenth_arr 	= 	explode(',',$_GET['length']);
	$width_arr 	= 	explode(',',$_GET['width']);
	$height_arr 	= 	explode(',',$_GET['height']);
	$weight_arr 	= 	explode(',',$_GET['weight']);
	$insurance_arr  = explode(',',$_GET['insurance']);

	$order = $this->wf_load_order( $post_id );
	if ( !$order ) return;

	$get_stored_packages = get_post_meta( $post_id, '_wf_dhl_stored_packages',true );

	$i=0;
	foreach ($get_stored_packages as $package) {
		if(!empty($package))
		{
				foreach ($package as $key => $value) {
					$package[$key]['Dimensions']['Length'] = isset($lenth_arr[$key]) ? $lenth_arr[$key] : 0;
					$package[$key]['Dimensions']['Width'] = isset($width_arr[$key]) ? $width_arr[$key] : 0 ;
					$package[$key]['Dimensions']['Height'] = isset($height_arr[$key]) ? $height_arr[$key] : 0;
					$package[$key]['Weight']['Value'] = isset($weight_arr[$key]) ? $weight_arr[$key] : 0;
					$package[$key]['InsuredValue']['Amount'] = isset($insurance_arr[$key]) ? $insurance_arr[$key] : 0;
					
				}
		}
		$package_data[] = $package;
		$dhl_requests[] = $this->get_service_request($order,$package);
	}
	update_post_meta( $post_id, '_wf_dhl_stored_packages', $package_data );
	 $result = array();
	 if ($dhl_requests) {
	 	try {
			foreach ( $dhl_requests as $key => $request ) {
				  $this->process_result($this->get_result($request));
			}            
        } catch (Exception $e) {
            //$this->debug(print_r($e, true), 'error');
            return false;
        }
        update_post_meta( $post_id, '_wf_dhl_available_services', $this->found_rates );
    }

	//$this->wf_available_services($post_id);
	
	//wp_redirect( admin_url( '/post.php?post='.$post_id.'&action=edit#dhl_meta_box') );
	?>
	<script>
	window.location = "<?php echo get_wcfm_view_order_url( $post_id ); ?>";
	</script>
	<?php
	exit;

}

private function process_result($result = '') {
        $processed_ratecode = array();
        $rate_compain = '';
        if ($result && !empty($result->GetQuoteResponse->BkgDetails->QtdShp)) {
            foreach ($result->GetQuoteResponse->BkgDetails->QtdShp as $quote) {
                $rate_code = strval((string) $quote->GlobalProductCode);
                if (!in_array($rate_code,$processed_ratecode)) {
                    if ((string) $quote->CurrencyCode == get_woocommerce_currency()) {
                        $rate_cost = floatval((string) $quote->ShippingCharge);
                        $rate_compain = floatval((string) $quote->WeightCharge);
                    } else {
                        $rate_cost = floatval((string) $this->wf_get_cost_based_on_currency($quote->QtdSInAdCur, $quote->ShippingCharge));
                        $rate_compain = floatval((string) $this->wf_get_cost_based_on_currency($quote->QtdSInAdCur, $quote->WeightCharge));
                    }
                    $processed_ratecode[] = $rate_code;
                    $rate_id = $rate_code;
                    
                    $delivery_time = new DateInterval($quote->DeliveryTime);
                    $delivery_time = $delivery_time->format('%h:%I');
                    $delivery_date_time = date("M-d", strtotime($quote->DeliveryDate)).' '.$delivery_time;
                    $rate_name = strval( (string) $quote->ProductShortName );
                    if($rate_cost > 0) $this->prepare_rate($rate_code, $rate_id, $rate_name, $rate_cost,$delivery_date_time,$rate_compain);
                }
            }
        }
    }
     private function wf_get_cost_based_on_currency($qtdsinadcur, $default_charge) {
        if (!empty($qtdsinadcur)) {
            foreach ($qtdsinadcur as $multiple_currencies) {
                if ((string) $multiple_currencies->CurrencyCode == get_woocommerce_currency() && !empty($multiple_currencies->TotalAmount))
                    return $multiple_currencies->TotalAmount;
            }
        }
        return $default_charge;
    }

    private function prepare_rate($rate_code, $rate_id, $rate_name, $rate_cost, $delivery_time, $rate_compain = '0') {

        // Name adjustment
        if (!empty($this->custom_services[$rate_code]['name'])) {
            $rate_name = $this->custom_services[$rate_code]['name'];
        }

        // Cost adjustment %
        if (!empty($this->custom_services[$rate_code]['adjustment_percent'])) {
            $rate_cost = $rate_cost + ( $rate_cost * ( floatval($this->custom_services[$rate_code]['adjustment_percent']) / 100 ) );
        }
        // Cost adjustment
        if (!empty($this->custom_services[$rate_code]['adjustment'])) {
            $rate_cost = $rate_cost + floatval($this->custom_services[$rate_code]['adjustment']);
        }

        // Enabled check
        if($this->select_service_check_box)
		{
			if (isset($this->custom_services[$rate_code]) && empty($this->custom_services[$rate_code]['enabled'])) {
				return;
			}
		}


        // Merging
        if (isset($this->found_rates[$rate_id])) {
            $rate_cost = $rate_cost + $this->found_rates[$rate_id]['cost'];
            $packages = 1 + $this->found_rates[$rate_id]['packages'];
        } else {
            $packages = 1;
        }
        // Sort
        if (isset($this->custom_services[$rate_code]['order'])) {
            $sort = $this->custom_services[$rate_code]['order'];
        } else {
            $sort = 999;
        }

        $extra_charge = $rate_cost - $rate_compain;

        $this->found_rates[$rate_id] = array(
            'id' => $rate_id,
            'label' => $rate_name,
            'cost' => $rate_cost,
            'sort' => $sort,
            'packages' => $packages,
            'meta_data' => array('dhl_delivery_time'=>$delivery_time,'weight_charge'=>floatval($rate_compain),'extra_charge'=>$extra_charge)
        );
    }

    	private function get_result($request) {
       
        $result = wp_remote_post($this->service_url, array(
            'method' => 'POST',
            'timeout' => 70,
            'sslverify' => 0,
            //'headers'          => $this->wf_get_request_header('application/vnd.cpc.shipment-v7+xml','application/vnd.cpc.shipment-v7+xml'),
            'body' => $request
                )
        );

        if ( is_wp_error( $result ) ) {
            $error_message = $result->get_error_message();
          
        }
        elseif (is_array($result) && !empty($result['body'])) {
            $result = $result['body'];
        } else {
            $result = '';
        }
        
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($result);

        $shipmentErrorMessage = "";
        if ($xml) {
            return $xml;
        } else {
            return null;
        }
    }

function get_service_request($order,$dhl_packages)
{
	

	$order_items = $order->get_items();
	$mailing_date = date('Y-m-d', time());
	$mailing_datetime = date('Y-m-d', time()) . 'T' . date('H:i:s', time());
	
	$origin_postcode_city = $this->wf_get_postcode_city($this->origin_country, $this->freight_shipper_city, $this->origin);
	 $fetch_accountrates = $this->request_type == "ACCOUNT" ? "<PaymentAccountNumber>" . $this->account_number . "</PaymentAccountNumber>" : "";

	$paymentCountryCode = $this->origin_country;
     $pieces = '';

     if ($dhl_packages) {
            foreach ($dhl_packages as $key => $parcel) {
                $pack_type = $parcel['packtype'];
				if($pack_type === 'YP')
				{
					$pack_type = 'BOX';
				}
                $index = $key + 1;
                $pieces .= '<Piece><PieceID>' . $index . '</PieceID>';
                $pieces .= '<PackageTypeCode>'.$pack_type.'</PackageTypeCode>';
                if( !empty($parcel['Dimensions']['Height']) && !empty($parcel['Dimensions']['Length']) && !empty($parcel['Dimensions']['Width']) ){
                    $pieces .= '<Height>' . $parcel['Dimensions']['Height'] . '</Height>';
                    $pieces .= '<Depth>' . $parcel['Dimensions']['Length'] . '</Depth>';
                    $pieces .= '<Width>' . $parcel['Dimensions']['Width'] . '</Width>';
                }
                $pieces .= '<Weight>' . $parcel['Weight']['Value'] . '</Weight></Piece>';
            }
        }
     $total_value = $this->wf_get_package_total_value($dhl_packages);
     if ($this->settings['insure_contents'] == 'yes' && !empty($this->conversion_rate))
            $currency = $this->settings['dhl_currency_type'];
     	else $currency = get_woocommerce_currency();

        $insurance_details = $this->insure_contents ? "<InsuredValue>{$total_value}</InsuredValue><InsuredCurrency>{$currency}</InsuredCurrency>" : "";
        $additional_insurance_details = ($this->insure_contents && $this->conversion_rate) ? "<QtdShp><QtdShpExChrg><SpecialServiceType>II</SpecialServiceType><LocalSpecialServiceType>XCH</LocalSpecialServiceType></QtdShpExChrg></QtdShp>" : ""; //insurance type

     $shipping_country = $order->get_shipping_country();

     $destination_city = strtoupper($order->get_shipping_city());
     $destination_postcode = $order->get_shipping_postcode();	
    
      $is_dutiable = ($shipping_country == $this->origin_country || wf_dhl_is_eu_country($this->origin_country, $shipping_country)) ? "N" : "Y";
        $dutiable_content = $is_dutiable == "Y" ? "<Dutiable><DeclaredCurrency>{$currency}</DeclaredCurrency><DeclaredValue>{$total_value}</DeclaredValue></Dutiable>" : "";
        $destination_postcode_city = $this->wf_get_postcode_city($shipping_country, $destination_city, $destination_postcode);

$xmlRequest = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<p:DCTRequest xmlns:p="http://www.dhl.com" xmlns:p1="http://www.dhl.com/datatypes" xmlns:p2="http://www.dhl.com/DCTRequestdatatypes" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.dhl.com DCT-req.xsd ">
  <GetQuote>
    <Request>
		<ServiceHeader>
			<MessageTime>{$mailing_datetime}</MessageTime>
			<MessageReference>1234567890123456789012345678901</MessageReference>
			<SiteID>{$this->site_id}</SiteID>
			<Password>{$this->site_password}</Password>
		</ServiceHeader>
    </Request>
    <From>
	  <CountryCode>{$this->origin_country}</CountryCode>
	  {$origin_postcode_city}
    </From>
    <BkgDetails>
      <PaymentCountryCode>{$paymentCountryCode}</PaymentCountryCode>
      <Date>{$mailing_date}</Date>
      <ReadyTime>PT10H21M</ReadyTime>
      <DimensionUnit>{$this->quoteapi_dimension_unit}</DimensionUnit>
      <WeightUnit>{$this->quoteapi_weight_unit}</WeightUnit>
      <Pieces>
		{$pieces}
	  </Pieces>
	  {$fetch_accountrates}
	  <IsDutiable>{$is_dutiable}</IsDutiable>
	  <NetworkTypeCode>AL</NetworkTypeCode>
	  	{$additional_insurance_details}
	  {$insurance_details}
	  </BkgDetails>
    <To>
      <CountryCode>{$shipping_country}</CountryCode>
	  {$destination_postcode_city}
    </To>
	{$dutiable_content}
  </GetQuote>
</p:DCTRequest>
XML;
	return $xmlRequest;
}
private function wf_get_package_total_value($dhl_packages) {
        $total_value = 0;
        if ($dhl_packages) {
            foreach ($dhl_packages as $key => $parcel) {
                $total_value += $parcel['InsuredValue']['Amount'] * $parcel['GroupPackageCount'];
            }
        }
        return $total_value;
    }
private function wf_get_postcode_city($country, $city, $postcode) {
        $no_postcode_country = array('AE', 'AF', 'AG', 'AI', 'AL', 'AN', 'AO', 'AW', 'BB', 'BF', 'BH', 'BI', 'BJ', 'BM', 'BO', 'BS', 'BT', 'BW', 'BZ', 'CD', 'CF', 'CG', 'CI', 'CK',
            'CL', 'CM', 'CO', 'CR', 'CV', 'DJ', 'DM', 'DO', 'EC', 'EG', 'ER', 'ET', 'FJ', 'FK', 'GA', 'GD', 'GH', 'GI', 'GM', 'GN', 'GQ', 'GT', 'GW', 'GY', 'HK', 'HN', 'HT', 'IE', 'IQ', 'IR',
            'JM', 'JO', 'KE', 'KH', 'KI', 'KM', 'KN', 'KP', 'KW', 'KY', 'LA', 'LB', 'LC', 'LK', 'LR', 'LS', 'LY', 'ML', 'MM', 'MO', 'MR', 'MS', 'MT', 'MU', 'MW', 'MZ', 'NA', 'NE', 'NG', 'NI',
            'NP', 'NR', 'NU', 'OM', 'PA', 'PE', 'PF', 'PY', 'QA', 'RW', 'SA', 'SB', 'SC', 'SD', 'SL', 'SN', 'SO', 'SR', 'SS', 'ST', 'SV', 'SY', 'TC', 'TD', 'TG', 'TL', 'TO', 'TT', 'TV', 'TZ',
            'UG', 'UY', 'VC', 'VE', 'VG', 'VN', 'VU', 'WS', 'XA', 'XB', 'XC', 'XE', 'XL', 'XM', 'XN', 'XS', 'YE', 'ZM', 'ZW');

        $postcode_city = !in_array( $country, $no_postcode_country ) ? $postcode_city = "<Postalcode>{$postcode}</Postalcode>" : '';
        if( !empty($city) ){
            $postcode_city .= "<City>{$city}</City>";
        }
        return $postcode_city;
    }

function wf_dhl_process_return_packages()
{
	if( !$this->wf_user_permission() ) {
		echo "You don't have admin privileges to view this page.";
		exit;
	}
	$post_id	=	base64_decode($_GET['wf_dhl_process_return_packages']);

	$order = $this->wf_load_order( $post_id );
	if ( !$order ) return;
	
	update_post_meta( $post_id, '_wf_dhl_process_return_shipment', 'yes' );
	
	//wp_redirect( admin_url( '/post.php?post='.$post_id.'&action=edit') );
	?>
	<script>
	window.location = "<?php echo get_wcfm_view_order_url( $post_id ); ?>";
	</script>
	<?php
	exit;

}
function dhl_product_choose_return_shipment()
{

	if( !$this->wf_user_permission() ) {
		echo "You don't have admin privileges to view this page.";
		exit;
	}
	
	$post_id	=	base64_decode($_GET['dhl_product_choose_return_shipment']);
	
	$order = $this->wf_load_order( $post_id );
	if ( !$order ) return;

	delete_post_meta($post_id, '_wf_dhl_stored_return_products');
	delete_post_meta( $post_id, '_wf_dhl_stored_return_packages');
	//wp_redirect( admin_url( '/post.php?post='.$post_id.'&action=edit') );
	?>
	<script>
	window.location = "<?php echo get_wcfm_view_order_url( $post_id ); ?>";
	</script>
	<?php
	exit;

}
function wf_dhl_generate_return_packages()
{
	if( !$this->wf_user_permission() ) {
		echo "You don't have admin privileges to view this page.";
		exit;
	}
	
	$post_id	=	base64_decode($_GET['wf_dhl_generate_return_packages']);
	
	$order = $this->wf_load_order( $post_id );
	if ( !$order ) return;


	$selected_items = '';

	if(isset($_GET['dhl_express_manual_return_products']) && $_GET['dhl_express_manual_return_products'] !='null')
	{
		$check_item = $_GET['dhl_express_manual_return_products'];
		if(!empty($check_item))
		{
			$data = explode(',',$check_item);
			$selected_items = array();
			if(!empty($data))
			{
				foreach ( $data as $k => $v )
				{
				  $selected_items[] = explode( '|', $v );
				}
			}
			update_post_meta($post_id, '_wf_dhl_stored_return_products', $check_item );
		}else{
			update_post_meta($post_id, '_wf_dhl_stored_return_products', '' );
		}
	}else{

		update_post_meta($post_id, '_wf_dhl_stored_return_products', '' );
		
	}


	if ( ! class_exists( 'wf_dhl_woocommerce_shipping_admin_helper' ) )
		include_once ( WF_DHL_PAKET_EXPRESS_ROOT_PATH . 'dhl_express/includes/class-wf-dhl-woocommerce-shipping-admin-helper.php' );
	
	$woodhlwrapper = new wf_dhl_woocommerce_shipping_admin_helper();
	$packages	=	$woodhlwrapper->wf_get_return_package_return_from_order($order,$selected_items);
	if(!empty($packages))
	{
		foreach ($packages as $key => $package) {
			$package_data[] = $woodhlwrapper->get_dhl_packages($package);

		}
	}

	update_post_meta( $post_id, '_wf_dhl_stored_return_packages', $package_data );
	
	//wp_redirect( admin_url( '/post.php?post='.$post_id.'&action=edit') );
	?>
	<script>
	window.location = "<?php echo get_wcfm_view_order_url( $post_id ); ?>";
	</script>
	<?php
	exit;
}

function bulk_label_admin_notices() {	 
  global $post_type, $pagenow;

  if( $pagenow == 'edit.php' && $post_type == 'shop_order' && isset($_REQUEST['bulk_label']) ) {
  	$order_ids = explode( ",", $_REQUEST['ids'] );
  	
  	$faild_ids_str = '';
  	$success_ids_str = '';
  	$already_exist_arr = explode( ',', $_REQUEST['already_exist'] );
  	if(!empty($order_ids))
  	{
	  	foreach ($order_ids as $key => $id) {
			$dhl_shipment_err	=	get_post_meta( $id, 'wf_woo_dhl_shipmentErrorMessage',true );
			$dhl_shipment_err  .= get_post_meta($id,'wf_woo_dhl_shipmentReturnErrorMessage',true);
			if( !empty($dhl_shipment_err) ){
				$faild_ids_str .= $id.', ';
			}
			elseif( !in_array( $id, $already_exist_arr ) ){
				$success_ids_str .= $id.', '; 
			}
	  	}
  	}

  	$faild_ids_str = rtrim($faild_ids_str,', ');
  	$success_ids_str = rtrim($success_ids_str,', ');

  	if( $faild_ids_str != '' ){
	    echo '<div class="error"><p>' . __('Create shipment is failed for following order(s) '.$faild_ids_str, 'wf-shipping-dhl') . '</p></div>';
  	}
	
	if( $success_ids_str != '' ){
	    echo '<div class="updated"><p>' . __('Successfully created shipment for following order(s) '.$success_ids_str, 'wf-shipping-dhl') . '</p></div>';
  	}

  	if( isset( $_REQUEST['already_exist'] ) && $_REQUEST['already_exist'] != '' ){
	    echo '<div class="notice notice-success"><p>' . __('Shipment already exist for following order(s) '.$_REQUEST['already_exist'] , 'wf-shipping-dhl') . '</p></div>';
  	}

  }
}

public function wf_orders_bulk_action()
{
	$wp_list_table = _get_list_table('WP_Posts_List_Table');
	$action = $wp_list_table->current_action();
	
	if ($action == 'create_shipment_dhl') {
		//forcefully turn off debug mode, otherwise it will die and cause to break the loop.
		$this->debug = false;
		$label_exist_for = '';
		if(isset($_REQUEST['post']) && !empty($_REQUEST['post']))
		{
			foreach($_REQUEST['post'] as $post_id) {
				$order = $this->wf_load_order( $post_id );
				if (!$order) 
					return;
				$orderid = wf_get_order_id($order);
				
				$shipmentIds = get_post_meta($orderid, 'wf_woo_dhl_shipmentId', false);
				if ( !empty($shipmentIds) ) {
					$label_exist_for .= $orderid.', ';
				}
				else{
					$this->wf_create_shipment($order);
				}
			}

		}		
		$sendback = add_query_arg( array(
			'bulk_label' => 1, 
			'ids' => join(',', $_REQUEST['post'] ),
			'already_exist' =>rtrim( $label_exist_for, ', ' )
		), admin_url( 'edit.php?post_type=shop_order' ) );
		
		wp_redirect($sendback);
		exit();
	}
		if ($action == 'create_shipment_return_dhl') {
		//forcefully turn off debug mode, otherwise it will die and cause to break the loop.
		$this->debug = false;
		$label_exist_for = '';
		if(isset($_REQUEST['post']) && !empty($_REQUEST['post']))
		{
			foreach($_REQUEST['post'] as $post_id) {
				$order = $this->wf_load_order( $post_id );
				if (!$order) 
					return;
				$orderid = wf_get_order_id($order);
				
				$shipmentIds = get_post_meta($orderid, 'wf_woo_dhl_return_shipmentId', false);
				if ( !empty($shipmentIds) ) {
					$label_exist_for .= $orderid.', ';
				}
				else{
					$this->wf_create_return_shipment($order);
				}
			}
		}
		$sendback = add_query_arg( array(
			'bulk_label' => 1, 
			'ids' => join(',', $_REQUEST['post'] ),
			'already_exist' =>rtrim( $label_exist_for, ', ' )
		), admin_url( 'edit.php?post_type=shop_order' ) );
		
		wp_redirect($sendback);
		exit();
	}
}

private function wf_load_order($orderId){
	if (!class_exists('WC_Order')) {
		return false;
	}
	return wc_get_order($orderId);      
}

private function wf_user_permission(){
	// Check if user has rights to generate invoices
	$current_user = wp_get_current_user();
	$user_ok = false;
	if ($current_user instanceof WP_User) {
		if (in_array('administrator', $current_user->roles) || in_array('shop_manager', $current_user->roles)) {
			$user_ok = true;
		}
	}
	return $user_ok;
}

public function wf_dhl_createshipment(){
	$user_ok = $this->wf_user_permission();
	if (!$user_ok) 			
		return;
	
	$order = $this->wf_load_order($_GET['wf_dhl_createshipment']);
	if (!$order) 
		return;
	
	
	$this->wf_create_shipment($order);
	
	if ( $this->debug ) {
        //dont redirect when debug is printed
        die();
	}
    else{			
		//wp_redirect(admin_url('/post.php?post='.$_GET['wf_dhl_createshipment'].'&action=edit&'.WCFM_Tracking_Admin_DHLExpress::get_admin_notification_message_var()));
		$post_id	=	base64_decode($_GET['wf_dhl_createshipment']);
		?>
		<script>
		window.location = "<?php echo get_wcfm_view_order_url( $post_id ); ?>";
		</script>
		<?php
		exit;
	}
	
}
public function wf_dhl_create_return_shipment(){
	$user_ok = $this->wf_user_permission();
	if (!$user_ok) 			
		return;
	
	$order = $this->wf_load_order($_GET['wf_dhl_create_return_shipment']);
	if (!$order) 
		return;
	
	
	$this->wf_create_return_shipment($order);
	
	if ( $this->debug ) {
        //dont redirect when debug is printed
        die();
	}
    else{			
		//wp_redirect(admin_url('/post.php?post='.$_GET['wf_dhl_create_return_shipment'].'&action=edit&'.WCFM_Tracking_Admin_DHLExpress::get_admin_notification_message_var()));
		$post_id	=	base64_decode($_GET['wf_dhl_create_return_shipment']);
		?>
		<script>
		window.location = "<?php echo get_wcfm_view_order_url( $post_id ); ?>";
		</script>
		<?php
		exit;
	}
	
}


public function wf_dhl_viewlabel(){
	$view_label = isset($_GET['wf_dhl_viewlabel']) ? $_GET['wf_dhl_viewlabel'] : ''; 
	$shipmentDetails = explode('|', base64_decode($view_label));

	if (count($shipmentDetails) != 2) {
		exit;
	}
	
	$shipmentId = $shipmentDetails[0]; 
	$post_id = $shipmentDetails[1]; 
	$shipping_label = get_post_meta($post_id, 'wf_woo_dhl_shippingLabel_'.$shipmentId, true);
	header('Content-Type: application/'.$this->image_type);
	header('Content-disposition: inline; filename="ShipmentArtifact-' . $shipmentId . '.'.$this->image_type.'"');
	print(base64_decode($shipping_label)); 
	exit;
}
public function wf_dhl_viewreturnlabel(){
	$view_return_label = isset($_GET['wf_dhl_viewreturnlabel']) ? $_GET['wf_dhl_viewreturnlabel'] : ''; 
	
	$shipmentDetails = explode('|', base64_decode($view_return_label));

	if (count($shipmentDetails) != 2) {
		exit;
	}
	
	$shipmentId = $shipmentDetails[0]; 
	$post_id = $shipmentDetails[1]; 
	$shipping_label = get_post_meta($post_id, 'wf_woo_dhl_return_shippingLabel_'.$shipmentId, true);
	header('Content-Type: application/'.$this->image_type);
	header('Content-disposition: inline; filename="ShipmentArtifactReturn-' . $shipmentId . '.'.$this->image_type.'"');
	print(base64_decode($shipping_label)); 
	exit;
}

public function wf_dhl_view_commercial_invoice(){
	$view_invoice = isset($_GET['wf_dhl_view_commercial_invoice']) ? $_GET['wf_dhl_view_commercial_invoice'] : ''; 
	
	$invoiceDetails = explode('|', base64_decode($view_invoice));

	if (count($invoiceDetails) != 2) {
		exit;
	}
	$image_type	=	'pdf'; //commercial invoice generated in pdf only
	$shipmentId = $invoiceDetails[0]; 
	$post_id = $invoiceDetails[1]; 
	$commercial_invoice = get_post_meta($post_id, 'wf_woo_dhl_shipping_commercialInvoice_'.$shipmentId, true);
	header('Content-Type: application/'.$image_type);
	header('Content-disposition: inline; filename="CommercialInvoice-' . $shipmentId . '.'.$image_type.'"');
	print(base64_decode($commercial_invoice)); 
	exit;
}
public function wf_dhl_view_return_commercial_invoice(){
	$view_return_invoice = isset($_GET['wf_dhl_view_return_commercial_invoice']) ? $_GET['wf_dhl_view_return_commercial_invoice'] : ''; 
	$invoiceDetails = explode('|', base64_decode($view_return_invoice));

	if (count($invoiceDetails) != 2) {
		exit;
	}
	$image_type	=	'pdf'; //commercial invoice generated in pdf only
	$shipmentId = $invoiceDetails[0]; 
	$post_id = $invoiceDetails[1]; 
	$commercial_invoice = get_post_meta($post_id, 'wf_woo_dhl_shipping_return_commercialInvoice_'.$shipmentId, true);
	header('Content-Type: application/'.$image_type);
	header('Content-disposition: inline; filename="ReturnCommercialInvoice-' . $shipmentId . '.'.$image_type.'"');
	print(base64_decode($commercial_invoice)); 
	exit;
}

private function wf_is_service_valid_for_country($order,$service_code){
	return true; 
}

private function wf_get_shipping_service($order,$retrive_from_order = false){
	
	if($retrive_from_order == true){
		$orderid = wf_get_order_id($order);
		$service_code = get_post_meta( $orderid, 'wf_woo_dhl_service_code', true);
		if(!empty($service_code)) 
			return $service_code;
	}

	if(!empty($_GET['dhl_express_shipping_service'])){			
		return $_GET['dhl_express_shipping_service'];			
	}
	if(!empty($_GET['dhl_express_return_shipping_service'])){			
		return $_GET['dhl_express_return_shipping_service'];			
	}
		
	//TODO: Take the first shipping method. It doesnt work if you have item wise shipping method
	$shipping_methods = $order->get_shipping_methods();
	
	if ($shipping_methods ) {
		//return '';
		$shipping_method = array_shift($shipping_methods);
	$shipping_output = explode('|',$shipping_method['method_id']);		

	$sipping_methods = str_replace(WF_DHL_ID.':', '', $shipping_output[0]);
	}
	else
	{

	$is_international = ( wf_get_order_shipping_country($order) == $this->origin_country ) ? false : true;

	if( $is_international ){
		if(!empty( $this->default_international_service) )
			return $this->default_international_service;
	}
	elseif( !empty($this->default_domestic_service) && $this->default_domestic_service !='none'  ){
		return $this->default_domestic_service;
	}
	}

	return $sipping_methods;
}

public function wf_create_shipment($order){		
	 if ( ! class_exists( 'wf_dhl_woocommerce_shipping_admin_helper' ) )
		include_once ( WF_DHL_PAKET_EXPRESS_ROOT_PATH . 'dhl_express/includes/class-wf-dhl-woocommerce-shipping-admin-helper.php' );
	
	$woodhlwrapper = new wf_dhl_woocommerce_shipping_admin_helper();
	$serviceCode = $this->wf_get_shipping_service($order,false);
	
	$orderid = wf_get_order_id($order);
	$woodhlwrapper->print_label($order,$serviceCode,$orderid);
}
public function wf_create_return_shipment($order){		
	 if ( ! class_exists( 'wf_dhl_woocommerce_shipping_admin_helper' ) )
		include_once ( WF_DHL_PAKET_EXPRESS_ROOT_PATH . 'dhl_express/includes/class-wf-dhl-woocommerce-shipping-admin-helper.php' );
	
	$woodhlwrapper = new wf_dhl_woocommerce_shipping_admin_helper();
	$serviceCode = $this->wf_get_shipping_service($order,false);

	$orderid = wf_get_order_id($order);
	$woodhlwrapper->print_reurn_label($order,$serviceCode,$orderid);
}


public function wf_add_dhl_metabox(){
	global $post;
	if (!$post) {
		return;
	}
	
	if ( in_array( $post->post_type, array('shop_order') )) {
		$order = $this->wf_load_order($post->ID);
		if (!$order) 
			return;
	
		//add_meta_box('wf_dhl_metabox', __('DHL Express', 'wf-shipping-dhl'), array($this, 'wf_dhl_metabox_content'), 'shop_order', 'advanced', 'default');
	}
}

public function wf_dhl_metabox_content( $orderid ){
	global $post;
	
	$post->ID = $orderid;
	
	$order = $this->wf_load_order($orderid);

	$shipmentIds = get_post_meta($orderid, 'wf_woo_dhl_shipmentId', false);
	$return_shipmentIds = get_post_meta($orderid, 'wf_woo_dhl_return_shipmentId', false);
	
	$shipmentErrorMessage = get_post_meta($orderid, 'wf_woo_dhl_shipmentErrorMessage',true);
	$shipmentReturnErrorMessage = get_post_meta($orderid, 'wf_woo_dhl_shipmentReturnErrorMessage',true);
	
	//Only Display error message if the process is not complete. If the Invoice link available then Error Message is unnecessary
	if(!empty($shipmentErrorMessage))
	{
		echo '<div class="error"><p>' . sprintf( __( 'DHL Express Create Shipment Error:%s', 'wf-shipping-dhl' ), $shipmentErrorMessage) . '</p></div>';
	}
	if(!empty($shipmentReturnErrorMessage))
	{
		echo '<div class="error"><p>' . sprintf( __( 'DHL Express Return Shipment Error:%s', 'wf-shipping-dhl' ), $shipmentReturnErrorMessage) . '</p></div>';
	}

	echo '<ul id="dhl_meta_box">';
	$selected_sevice = $this->wf_get_shipping_service($order,true);	
	if (!empty($shipmentIds)) {

		if(!empty($selected_sevice) && !empty($this->services[$selected_sevice]) )
			echo "<li>Shipping service: <strong>".$this->services[$selected_sevice]."</strong></li>";		
		include_once('class-wf-tracking-admin.php');
		$tracking_obj = new WCFM_Tracking_Admin_DHLExpress();

		foreach($shipmentIds as $shipmentId) {
			echo '<li><strong>Shipment #:</strong> <a href="http://www.dhl.com/en/express/tracking.html?AWB='.$shipmentId.'&brand=DHL" target="_blank">'. $shipmentId.'</a> ';
			if($this->add_trackingpin_shipmentid === 'yes')
			{
			$tracking_array = $tracking_obj->get_tracking_info($orderid,$shipmentId);
			echo '<span style="">';
			//$last_checkpoint_status = '';
			$full_check_point_data = '';
			
			if($tracking_array['status'] !='success')
			{
				//$last_checkpoint_status = ' <small> (No Shipments Found : Test Mode)</small>';
				$full_check_point_data .='<li>
							<div class="wf-dhl-direction-r">
								<div class="wf-dhl-wf-dhl-flag-wrapper">
									<span class="wf-dhl-flag">Test Mode</span>
									<span class="wf-dhl-wf-dhl-time-wrapper"><span class="wf-dhl-time">Faliure</span></span>
								</div>
								<div class="wf-dhl-desc">No Shipments Found</div>
							</div>
						</li>';
			}else
			{

				if(isset($tracking_array['shippment']))
				{
					foreach ($tracking_array['shippment'] as $key => $value) {
					//	$last_checkpoint_status = empty($value['desc']) ? ' <small>(Shipment information received)</small>' : ' <small>('.$value['desc'].')</small>';
					$full_check_point_data .='<li>
							<div class="wf-dhl-direction-r">
								<div class="wf-dhl-wf-dhl-flag-wrapper">
									<span class="wf-dhl-flag">'.$value['date'].'</span>
									<span class="wf-dhl-wf-dhl-time-wrapper"><span class="wf-dhl-time">'.$value['time'].'</span></span>
								</div>
								<div class="wf-dhl-desc">'.$value['desc'].'</div>
							</div>
						</li>';
					}
					
				}else{
					//$last_checkpoint_status = ' <small>(Shipment information received)</small>';
					$full_check_point_data .='<li>
							<div class="wf-dhl-direction-r">
								<div class="wf-dhl-wf-dhl-flag-wrapper">
									<span class="wf-dhl-flag">Initial</span>
									<span class="wf-dhl-wf-dhl-time-wrapper"><span class="wf-dhl-time">Shipment Received</span></span>
								</div>
								<div class="wf-dhl-desc">Shipment information received</div>
							</div>
						</li>';
				}
			}
			//echo $last_checkpoint_status;
			include_once(WF_DHL_PAKET_EXPRESS_ROOT_PATH. '/dhl_express/resources/css/tracking-back-end.php');
			echo ' <a href="#wf_dhl_metabox1" style="text-decoration:none;color:#ba0c2f;" id="wf_shipment_details_but" > <span class="dashicons dashicons-search"></span> </a></span>';
			
			?>
<!-- The wf-dhl-model -->
<div id="wf_shipment_data_popup" class="wf-dhl-model">

  <!-- wf-dhl-model content -->
  <div class="wf-dhl-model-content" style="height:70%;overflow-x: scroll;">
    <span class="wf-dhl-close">&times;</span>
  
<!-- The wf-dhl-wf-dhl-timeline -->

<ul class="wf-dhl-wf-dhl-timeline">
<?php echo $full_check_point_data; ?>
  
</ul>

  </div>

</div>

<script>
// Get the wf-dhl-model
var model = document.getElementById('wf_shipment_data_popup');

// Get the button that opens the wf-dhl-model
var btn = document.getElementById("wf_shipment_details_but");

// Get the <span> element that wf-dhl-closes the wf-dhl-model
var span = document.getElementsByClassName("wf-dhl-close")[0];

// When the user clicks the button, open the wf-dhl-model 
btn.onclick = function() {
    model.style.display = "block";
}

// When the user clicks on <span> (x), wf-dhl-close the wf-dhl-model
span.onclick = function() {
    model.style.display = "none";
}

// When the user clicks anywhere outside of the wf-dhl-model, wf-dhl-close it
window.onclick = function(event) {
    if (event.target == model) {
        model.style.display = "none";
    }
}
</script>
<?php } ?>
			<table style='width:100%'>
				<tbody><tr><td style='width:85%;'>
			<?php
			$packageDetailForTheshipment = get_post_meta($orderid, 'wf_woo_dhl_packageDetails_'.$shipmentId, true);
			
			
			if(!empty($packageDetailForTheshipment)){
				foreach($packageDetailForTheshipment as $dimentionValue){
					echo $dimentionValue;

				}
				?>
			</td>
			<td style='width:15%;text-align:right;vertical-align:bottom;'>
			<img src='<?php echo WF_DHL_PAKET_PATH.'/dhl_express/resources/images/box.png'; ?>' style='width: 60px;padding-right: 5px;'><div style='width:50%:float:left;'>

			</div></td>
			</tr></tbody></table>
			<?php
			}
			echo '<hr style="border-color:#c9c9c9"></li>';
			$shipping_label = get_post_meta($orderid, 'wf_woo_dhl_shippingLabel_'.$shipmentId, true);
			if(!empty($shipping_label)) {
				$download_url = add_query_arg( 'wf_dhl_viewlabel', base64_encode($shipmentId.'|'.$orderid), get_wcfm_view_order_url( $order_id ) );?>
				<a class="button wcfm_submit_button tips button-primary" target="_blank"  href="<?php echo $download_url; ?>" data-tip="<?php _e('Shipment Label', 'wf-shipping-dhl'); ?>"><?php _e('Shipment Label', 'wf-shipping-dhl'); ?></a>
				<a class="button wcfm_submit_button tips"  href="<?php echo add_query_arg( 'wf_dhl_delete_label', base64_encode($order_id), get_wcfm_view_order_url( $order_id ) ); ?>" onclick="return confirm('Are you sure?');" data-tip="<?php _e('Reset Shipment', 'wf-shipping-dhl'); ?>"><?php _e('Reset Shipment', 'wf-shipping-dhl'); ?></a>
				<?php 
			}
			
			
			$commercial_invoice = get_post_meta($orderid, 'wf_woo_dhl_shipping_commercialInvoice_'.$shipmentId, true);
			if(!empty($commercial_invoice)){
				$commercial_invoice_download_url = add_query_arg( 'wf_dhl_view_commercial_invoice', base64_encode($shipmentId.'|'.$orderid), get_wcfm_view_order_url( $order_id ) );?>
				<a class="button wcfm_submit_button tips button-primary"  href="<?php echo $commercial_invoice_download_url; ?>" target="_blank" data-tip="<?php _e('Commercial Invoice', 'wf-shipping-dhl'); ?>"><?php _e('Commercial Invoice', 'wf-shipping-dhl'); ?></a>
				<?php 
			}
			
		} 
		// Return Label New development
		if(!empty($this->return_label_key) && $this->return_label_key === 'yes')
		{
		if(empty($return_shipmentIds))
			{

				$stored_return_shipment	=	get_post_meta( $orderid, '_wf_dhl_process_return_shipment', true );
				
				if(empty($stored_return_shipment))
				{
				?>
					<a class="button wcfm_submit_button tips dhl_generate_packages button-primary" style="text-align: center;float:right;" href="<?php echo add_query_arg( 'wf_dhl_process_return_packages', base64_encode($orderid), get_wcfm_view_order_url( $order_id ) ); ?>" data-tip="<?php _e( 'Process Return Shipment', 'wf-shipping-dhl' ); ?>"><?php _e( 'Process Return Shipment', 'wf-shipping-dhl' ); ?></a><hr style="border-color:#0074a2">
					<script type="text/javascript">
					jQuery("a.dhl_process_return_packages").on("click", function() {
						location.href = this.href;
					});
					</script>
				
				<?php
				}
				else
				{
				$stored_return_packages	=	get_post_meta( $orderid, '_wf_dhl_stored_return_packages', true );
				$generate_return_url = add_query_arg( 'wf_dhl_create_return_shipment', base64_encode($orderid), get_wcfm_view_order_url( $order_id ) );
			if(empty($stored_return_packages))
			{
				$items = $order->get_items();
				echo '<hr ><b>Select Products to be Return</b><br/>';
				echo '<table id="dhl_slect_qty_table" style="margin-top: 5px;margin-bottom: 5px;box-shadow: 1px 1px 5px lightgrey;width:100%;" class="wf-shipment-package-table">';
					echo '<tr>';
					echo '<th > </th>';
					echo '<th style="padding:4px;text-align:left">Product Name</th>';
					echo '<th style="padding:4px;text-align:left">Qty</th>';
					echo '</tr>';
					if(!empty($items))
					{
						foreach ($items as $item_id => $orderItem) {
							$item_id 		= $orderItem['variation_id'] ? $orderItem['variation_id'] : $orderItem['product_id'];

							$product_name = $orderItem['name'];
							echo '<tr><td style="padding-left: 3px"><input type="checkbox" style="padding-left:2px;" name="wf_dhl_item_id" id="wf_dhl_item_id" value="'.$item_id.'"></td><td><small>'.$product_name.'</small></td><td><input type="number" id="dhl_return_product_ids" min="1" max="'.$orderItem['quantity'].'" name="dhl_return_product_ids" style="width:50px;" value='.$orderItem['quantity'].'></td></tr>';
						}
					}
					echo '</table>';
				?>
			<a class="button wcfm_submit_button tips dhl_generate_return_packages button-primary" id="" style="text-align: center;" href="<?php echo add_query_arg( 'wf_dhl_generate_return_packages', base64_encode($orderid), get_wcfm_view_order_url( $order_id ) ); ?>" data-tip="<?php _e( 'Generate Return Packages', 'wf-shipping-dhl' ); ?>"><?php _e( 'Generate Return Packages', 'wf-shipping-dhl' ); ?></a><hr style="border-color:#0074a2">
				<!-- <script type="text/javascript">
				jQuery("a.dhl_generate_return_packages").on("click", function() {
					location.href = this.href;
				});
			</script> -->
			<script>
	jQuery("a.dhl_generate_return_packages").one("click", function() {
		var values = new Array();
		jQuery(this).click(function () { return false; });

			jQuery('#dhl_slect_qty_table').find('tr').each(function () {
		        var row = jQuery(this);

		        if (row.find('input[type="checkbox"]').is(':checked')) {
		            values.push([row.find('input[name="wf_dhl_item_id"]').val()] + '|' +row.find('input[name="dhl_return_product_ids"]').val());
		        }
		    });


		   location.href = this.href 
			+ '&dhl_express_manual_return_products=' + values.join(",");
		return false;			
	});
	</script>	
			<?php
			}else

			{
				
				echo '<hr><span style="font-weight:bold;">'.__( 'Return Package(s)' , 'wf-shipping-dhl').': </span> ';
				if($this->plt)
				{
					//echo ' <a href="'.admin_url( 'admin.php?page=' . wf_get_settings_url() . '&tab=shipping&section=wf_dhl_shipping&subtab=labels' ) .'" target="_blank" style="color:#ba0c2f;"><label style="background:yellow;float:right;padding:2px;">Paper Less Trade (PLT) is enabled.</label></a>';
				}
			
				echo '<table id="wf_dhl_return_package_list" class="wf-shipment-package-table">';					
					echo '<tr>';
						echo '<th style="padding:6px;text-align:left;">'.__('Item(s)', 'wf-shipping-dhl').'</th>';
						echo '<th style="padding:6px;text-align:left;">'.__('Weight', 'wf-shipping-dhl').' ('.$this->weight_unit.')</th>';
						echo '<th style="text-align:left;padding:left:6px;">'.__('Length', 'wf-shipping-dhl').'</th>';
						echo '<th style="text-align:left;padding:left:6px;">'.__('Width', 'wf-shipping-dhl').' </th>';
						echo '<th style="text-align:left;padding:left:6px;">'.__('Height', 'wf-shipping-dhl').' </th>';
						echo '<th style="text-align:left;padding:left:6px;">'.__('Insurance', 'wf-shipping-dhl').'</th>';
						echo '<th>&nbsp;</th>';
					echo '</tr>';
					if( empty($stored_return_packages[0]) ){
						$stored_return_packages[0][0] = $this->get_dhl_dummy_package();
					}
					if(!empty($stored_return_packages))
					{
						foreach($stored_return_packages as $package_group_key	=>	$package_group){
							if( !empty($package_group) && is_array($package_group) ){ //package group may empty if boxpacking and product have no dimensions 
								foreach($package_group as $stored_package_key	=>	$stored_package){
									$product_details = '';
											foreach ($stored_package['packed_products'] as $key => $value) {
												
												$product_id = $value->get_id();
												$product_title = get_the_title($product_id);
												$product_details .= $product_title.', ';
											}
										$trim_data = strlen($product_details) > 30 ? substr($product_details,0,30)."..." : $product_details;
										$product_details = rtrim( $product_details,', ');
										$product_details = '<a href="#" title="'.$product_details.'" style="text-decoration: unset;color: black;cursor: default;">'. $trim_data .'</a>';

									$dimensions	=	$this->get_dimension_from_package($stored_package);
									if(is_array($dimensions)){
										?>
										<tr>
											<td style="width:25%;padding:5px;border-radius:5px;margin-left:4px;"><small><?php echo $product_details; ?></small></td>
											<td><input type="text" id="dhl_return_manual_weight" name="dhl_return_manual_weight[]" style="width:60%;padding:5px;border-radius:5px;margin-left:4px;" value="<?php echo $dimensions['Weight'];?>" /> <b><?php echo $this->weight_unit; ?></b></td>     
											<td><input type="text" id="dhl_return_manual_length" name="dhl_return_manual_length[]" style="width:60%;padding:5px;border-radius:5px;margin-left:4px;" value="<?php echo $dimensions['Length'];?>" /> <b><?php echo $this->dim_unit; ?></b></td>
											<td><input type="text" id="dhl_return_manual_width" name="dhl_return_manual_width[]" style="width:60%;padding:5px;border-radius:5px;margin-left:4px;" value="<?php echo $dimensions['Width'];?>" /> <b><?php echo $this->dim_unit; ?></b></td>
											<td><input type="text" id="dhl_return_manual_height" name="dhl_return_manual_height[]" style="width:60%;padding:5px;border-radius:5px;margin-left:4px;" value="<?php echo $dimensions['Height'];?>" /> <b><?php echo $this->dim_unit; ?></b></td>
											<td><input type="text" id="dhl_return_manual_insurance" name="dhl_return_manual_insurance[]" style="width:60%;padding:5px;border-radius:5px;margin-left:4px;" value="<?php echo $dimensions['insurance'];?>" /></td>
											<td><a class="wf_dhl_return_package_line_remove tips" data-tip="<?php _e( 'Delete Package', 'wf-shipping-dhl' ); ?>">&#x26D4;</a></td>
										</tr>
										<?php
									}
								}
							}
						}
					}
				echo '</table>';
				echo '<a class="wf-action-button wcfm_submit_button wf-add-button" style="font-size: 12px;" id="wf_dhl_add_reurn_package">Add Package</a>';
			
			echo '</li>';
			echo '<li>choose Return service:<br><select class="wc-enhanced-select" style="width:40%;padding:5px" id="dhl_express_manual_return_service">';
				if($this->show_front_end_shipping_method && $this->show_front_end_shipping_method === 'yes' && !empty($selected_sevice))
				{
					echo '<option value="'.$selected_sevice.'" selected="true" >'.$this->services[$selected_sevice].'</option>';
				}else
				{
				foreach($this->custom_services as $service_code => $service){
												if($service['enabled'] == true && $this->wf_is_service_valid_for_country($order,$service_code) == true){
						echo '<option value="'.$service_code.'" ' . selected($selected_sevice,$service_code,false) . ' >'.$this->services[$service_code].'</option>';
						}	
					}
				}
				echo '</select></li>';
				echo '<li>';
				echo '<hr style="border-color:#c9c9c9"></li>';
			?>

			<li>
				<a class="button wcfm_submit_button tips onclickdisable dhl_create_return_shipment button-primary" style="text-align: center;" href="<?php echo $generate_return_url; ?>" data-tip="<?php _e('Create Return Shipment', 'wf-shipping-dhl'); ?>"><?php _e('Create Return Shipment', 'wf-shipping-dhl'); ?></a>
				<a class="button wcfm_submit_button tips onclickdisable dhl_product_choose_return_shipment" style="text-align: center;" href="<?php echo add_query_arg( 'dhl_product_choose_return_shipment', base64_encode($orderid), get_wcfm_view_order_url( $order_id ) ); ?>" data-tip="<?php _e('Back', 'wf-shipping-dhl'); ?>"><?php _e('Back', 'wf-shipping-dhl'); ?></a>
			</li>

			<script type="text/javascript">
				jQuery(document).ready(function(){
					jQuery('#wf_dhl_add_reurn_package').on("click", function(){
						var new_row = '<tr>';
							new_row 	+= '<td></td>';
							new_row 	+= '<td><input type="text" id="dhl_return_manual_weight" name="dhl_return_manual_weight[]" style="width:60%;padding:5px;border-radius:5px;margin-left:4px;" value="0"> <b><?php echo $this->weight_unit; ?></b></td>';
							new_row 	+= '<td><input type="text" id="dhl_return_manual_length" name="dhl_return_manual_length[]" style="width:60%;padding:5px;border-radius:5px;margin-left:4px;" value="0"> <b><?php echo $this->dim_unit; ?></b></td>';								
							new_row 	+= '<td><input type="text" id="dhl_return_manual_width" name="dhl_return_manual_width[]" style="width:60%;padding:5px;border-radius:5px;margin-left:4px;" value="0"> <b><?php echo $this->dim_unit; ?></b></td>';
							new_row 	+= '<td><input type="text" id="dhl_return_manual_height" name="dhl_return_manual_height[]" style="width:60%;padding:5px;border-radius:5px;margin-left:4px;" value="0"> <b><?php echo $this->dim_unit; ?></b></td>';
							new_row 	+= '<td><input type="text" id="dhl_return_manual_insurance" name="dhl_return_manual_insurance[]" style="width:60%;padding:5px;border-radius:5px;margin-left:4px;" value="0"></td>';
							new_row 	+= '<td><a class="wf_dhl_return_package_line_remove tips" data-tip="Delete Package">&#x26D4;</a></td>';
						new_row 	+= '</tr>';
						
						jQuery('#wf_dhl_return_package_list tr:last').after(new_row);
					});
					
					jQuery(document).on('click', '.wf_dhl_return_package_line_remove', function(){
						if(confirm('Are you sure you want to remove this package?'))
						{
							jQuery(this).closest('tr').remove();
						}
					});
				});
			</script>
			<script>
	jQuery("a.dhl_create_return_shipment").one("click", function() {
		
		jQuery(this).click(function () { return false; });
			var manual_weight_arr 	= 	jQuery("input[id='dhl_return_manual_weight']").map(function(){return jQuery(this).val();}).get();
			var manual_weight 		=	JSON.stringify(manual_weight_arr);
			
			var manual_height_arr 	= 	jQuery("input[id='dhl_return_manual_height']").map(function(){return jQuery(this).val();}).get();
			var manual_height 		=	JSON.stringify(manual_height_arr);
			
			var manual_width_arr 	= 	jQuery("input[id='dhl_return_manual_width']").map(function(){return jQuery(this).val();}).get();
			var manual_width 		=	JSON.stringify(manual_width_arr);
			
			var manual_length_arr 	= 	jQuery("input[id='dhl_return_manual_length']").map(function(){return jQuery(this).val();}).get();
			var manual_length 		=	JSON.stringify(manual_length_arr);
			
			var manual_insurance_arr 	= 	jQuery("input[id='dhl_return_manual_insurance']").map(function(){return jQuery(this).val();}).get();
		 	var manual_insurance 		=	JSON.stringify(manual_insurance_arr);
			
			
		   location.href = this.href + '&weight=' + manual_weight +
			'&length=' + manual_length
			+ '&width=' + manual_width
			+ '&height=' + manual_height
			+ '&insurance=' + manual_insurance
			+ '&dhl_express_return_shipping_service=' + jQuery('#dhl_express_manual_return_service').val();
		return false;			
	});
	</script>	
			<?php
			}
			
			
			
		}

	}else{
			$selected_return_service_code = get_post_meta($orderid,'wf_woo_dhl_return_service_code',true);
			if(!empty($selected_return_service_code) && !empty($this->services[$selected_return_service_code]) )
			echo "<hr><li>Return Shipping service: <strong>".$this->services[$selected_return_service_code]."</strong></li>";		
		
		foreach($return_shipmentIds as $shipmentId) {
			echo '<li><strong>Return Shipment #:</strong> <a href="http://www.dhl.com/en/express/tracking.html?AWB='.$shipmentId.'&brand=DHL" target="_blank" >'. $shipmentId.'</a>';
			if($this->add_trackingpin_shipmentid === 'yes')
			{
			$tracking_array = $tracking_obj->get_tracking_info($orderid,$shipmentId);
			echo '<span style="">';
			//$last_checkpoint_status = '';
			$full_check_point_data = '';
			
			if($tracking_array['status'] !='success')
			{
				//$last_checkpoint_status = ' <small> (No Shipments Found : Test Mode)</small>';
				$full_check_point_data .='<li>
							<div class="wf-dhl-direction-r">
								<div class="wf-dhl-wf-dhl-flag-wrapper">
									<span class="wf-dhl-flag">Test Mode</span>
									<span class="wf-dhl-wf-dhl-time-wrapper"><span class="wf-dhl-time">Faliure</span></span>
								</div>
								<div class="wf-dhl-desc">No Shipments Found</div>
							</div>
						</li>';
			}else
			{

				if(isset($tracking_array['shippment']))
				{
					foreach ($tracking_array['shippment'] as $key => $value) {
						//$last_checkpoint_status = empty($value['desc']) ? ' <small>(Shipment information received)</small>' : ' <small>('.$value['desc'].')</small>';
					$full_check_point_data .='<li>
							<div class="wf-dhl-direction-r">
								<div class="wf-dhl-wf-dhl-flag-wrapper">
									<span class="wf-dhl-flag">'.$value['date'].'</span>
									<span class="wf-dhl-wf-dhl-time-wrapper"><span class="wf-dhl-time">'.$value['time'].'</span></span>
								</div>
								<div class="wf-dhl-desc">'.$value['desc'].'</div>
							</div>
						</li>';
					}
					
				}else{
					//$last_checkpoint_status = ' <small>(Shipment information received)</small>';
					$full_check_point_data .='<li>
							<div class="wf-dhl-direction-r">
								<div class="wf-dhl-wf-dhl-flag-wrapper">
									<span class="wf-dhl-flag">Initial</span>
									<span class="wf-dhl-wf-dhl-time-wrapper"><span class="wf-dhl-time">Shipment Received</span></span>
								</div>
								<div class="wf-dhl-desc">Shipment information received</div>
							</div>
						</li>';
				}
			}

			echo ' <a href="#wf_dhl_metabox1"  style="text-decoration:none;color:#ba0c2f;"  id="wf_shipment_data_return_but" > <span class="dashicons dashicons-search"></span> </a></span>';

			?>
			
<!-- The wf-dhl-model -->
<div id="wf_shipment_data_return_popup" class="wf-dhl-model">

  <!-- wf-dhl-model content -->
  <div class="wf-dhl-model-content" style="height:70%;overflow-x: scroll;">
    <span class="wf-dhl-return-close">&times;</span>
  
<!-- The wf-dhl-wf-dhl-timeline -->

<ul class="wf-dhl-wf-dhl-timeline">
<?php echo $full_check_point_data; ?>
  
</ul>

  </div>

</div>

<script>
// Get the wf-dhl-model
var returnmodel = document.getElementById('wf_shipment_data_return_popup');

// Get the button that opens the wf-dhl-model
var returnbtn = document.getElementById("wf_shipment_data_return_but");

// Get the <span> element that wf-dhl-closes the wf-dhl-model
var returnspan = document.getElementsByClassName("wf-dhl-return-close")[0];

// When the user clicks the button, open the wf-dhl-model 
returnbtn.onclick = function() {
    returnmodel.style.display = "block";
}

// When the user clicks on <span> (x), wf-dhl-close the wf-dhl-model
returnspan.onclick = function() {
    returnmodel.style.display = "none";
}

// When the user clicks anywhere outside of the wf-dhl-model, wf-dhl-close it
window.onclick = function(event) {
    if (event.target == returnmodel) {
        returnmodel.style.display = "none";
    }
}
</script>
<?php } ?>
				<table style='width:100%' class=''>
				<tbody><tr><td style='width:80%;'>
			<?php
			$packageDetailForTheshipment = get_post_meta($orderid, 'wf_woo_dhl_return_packageDetails_'.$shipmentId, true);
			if(!empty($packageDetailForTheshipment)){
				foreach($packageDetailForTheshipment as $dimentionValue){
					echo $dimentionValue;
				}
			}
			?>
			</td>
			<td style='width:15%;text-align:right;vertical-align:bottom;'>
			<img src='<?php echo WF_DHL_PAKET_PATH.'/dhl_express/resources/images/box.png'; ?>' style='width: 60px;padding-right: 5px;'><div style='width:50%:float:left;'>

			</div></td>
			</tr></tbody></table>
			<?php
			$shipping_label = get_post_meta($orderid, 'wf_woo_dhl_return_shippingLabel_'.$shipmentId, true);
			if(!empty($shipping_label)){

				echo '<hr style="border-color:#c9c9c9"></li>';
				$download_return_url = add_query_arg( 'wf_dhl_viewreturnlabel', base64_encode($shipmentId.'|'.$orderid), get_wcfm_view_order_url( $order_id ) );?>
				<a class="button wcfm_submit_button tips button-primary" target="_blank"  href="<?php echo $download_return_url; ?>" data-tip="<?php _e('Return Label', 'wf-shipping-dhl'); ?>"><?php _e('Return Label', 'wf-shipping-dhl'); ?></a>
				<a class="button wcfm_submit_button tips"  href="<?php echo add_query_arg( 'wf_dhl_delete_return_label', base64_encode($orderid), get_wcfm_view_order_url( $order_id ) ); ?>" onclick="return confirm('Are you sure?');" data-tip="<?php _e('Reset Return Label', 'wf-shipping-dhl'); ?>"><?php _e('Reset Return Label', 'wf-shipping-dhl'); ?></a>
				<?php 
			}
			
			$commercial_invoice = get_post_meta($orderid, 'wf_woo_dhl_shipping_return_commercialInvoice_'.$shipmentId, true);
			if(!empty($commercial_invoice)){
				$commercial_invoice_download_url = add_query_arg( 'wf_dhl_view_return_commercial_invoice', base64_encode($shipmentId.'|'.$orderid), get_wcfm_view_order_url( $order_id ) );?>
				<a class="button wcfm_submit_button tips button-primary"  href="<?php echo $commercial_invoice_download_url; ?>" target="_blank" data-tip="<?php _e('Commercial Invoice', 'wf-shipping-dhl'); ?>"><?php _e('Commercial Invoice', 'wf-shipping-dhl'); ?></a>
				<?php 
			}
			echo '<hr style="border-color:#c9c9c9"></li>';

		} 
		}	
		}							
	}
	else {
		$stored_packages	=	get_post_meta( $orderid, '_wf_dhl_stored_packages', true );
		if(empty($stored_packages)	&&	!is_array($stored_packages)){
			echo '<strong>'.__( 'Auto generate packages.', 'wf-shipping-dhl' ).'</strong></br>';
			?>
			<a class="button wcfm_submit_button tips dhl_generate_packages button-primary"  href="<?php echo add_query_arg( 'wf_dhl_generate_packages', base64_encode($orderid), get_wcfm_view_order_url( $order_id ) ); ?>" data-tip="<?php _e( 'Generate Packages', 'wf-shipping-dhl' ); ?>"><?php _e( 'Generate Packages', 'wf-shipping-dhl' ); ?></a>
			<?php
		}else{
			$generate_url = add_query_arg( 'wf_dhl_createshipment', base64_encode($orderid), get_wcfm_view_order_url( $order_id ) );
			$select_box_value = '';
			echo '<li>';
				echo '<span style="font-weight:bold;">'.__( 'Package(s)' , 'wf-shipping-dhl').':</span>';
				if($this->plt)
				{
					//echo ' <a href="'.admin_url( 'admin.php?page=' . wf_get_settings_url() . '&tab=shipping&section=wf_dhl_shipping&subtab=labels' ) .'" target="_blank" style="color:#ba0c2f;"><label style="background:yellow;float:right;padding:2px;">Paper Less Trade (PLT) is enabled.</label></a>';
				}
				echo '<table id="wf_dhl_package_list" class="wf-shipment-package-table" style="margin-bottom: 5px;margin-top: 5px;box-shadow:.5px .5px 5px lightgrey;">';					
					echo '<tr>';

						//echo '<th>'.__('DHL Service', 'wf-shipping-dhl').'</th>';
						echo '<th style="padding:6px;text-align:left;">'.__('Item(s)', 'wf-shipping-dhl').'</th>';
						echo '<th style="padding:6px;text-align:left;">'.__('Weight', 'wf-shipping-dhl').' ('.$this->weight_unit.')</th>';
						echo '<th style="text-align:left;padding:6px;">'.__('Length', 'wf-shipping-dhl').'</th>';
						echo '<th style="text-align:left;padding:6px;">'.__('Width', 'wf-shipping-dhl').' </th>';
						echo '<th style="text-align:left;padding:6px;">'.__('Height', 'wf-shipping-dhl').' </th>';
						echo '<th style="text-align:left;padding:6px;">'.__('Insurance', 'wf-shipping-dhl').'</th>';
						echo '<th style="text-align:left;padding-right:20px;">&nbsp;</th>';
					echo '</tr>';
					if( empty($stored_packages[0]) ){
						$stored_packages[0][0] = $this->get_dhl_dummy_package();
					}
					foreach($stored_packages as $package_group_key	=>	$package_group){
						if( !empty($package_group) && is_array($package_group) ){ //package group may empty if boxpacking and product have no dimensions 
							foreach($package_group as $stored_package_key	=>	$stored_package){
									$product_details = '';
									if(!empty($stored_package) && is_array($stored_package))
									{
										if(isset($stored_package['packed_products']) && is_array($stored_package['packed_products']))
										{
											foreach ($stored_package['packed_products'] as $key => $value) {
												
												$product_id = $value->get_id();
												$product_title = get_the_title($product_id);
												$product_details .= $product_title.', ';
											}
										}
									}
									$trim_data = strlen($product_details) > 30 ? substr($product_details,0,30)."..." : $product_details;
									$product_details = rtrim( $product_details,', ');
									$product_details = '<a href="#" title="'.$product_details.'" style="text-decoration: unset;color: black;cursor: default;" >'. $trim_data .'</a>';
									
								$dimensions	=	$this->get_dimension_from_package($stored_package);
								if(is_array($dimensions)){
									?>
									<tr>
										<!-- <td><select id="dhl_manual_service" name="dhl_manual_service[]" class="dhl_manual_service wc-enhanced-select"><?php echo $select_box_value; ?></select></td>
										 --><td style="width:25%;padding:5px;border-radius:5px;margin-left:4px;"><small><?php echo $product_details; ?></small></td>     
										 <td><input type="text" id="dhl_manual_weight" name="dhl_manual_weight[]" style="width:60%;padding:5px;border-radius:5px;margin-left:4px;" value="<?php echo $dimensions['Weight'];?>" /> <b><?php echo $this->weight_unit; ?></b></td>     
										<td><input type="text" id="dhl_manual_length" name="dhl_manual_length[]" style="width:60%;padding:5px;border-radius:5px;margin-left:4px;" value="<?php echo $dimensions['Length'];?>" /> <b><?php echo $this->dim_unit; ?></b></td>
										<td><input type="text" id="dhl_manual_width" name="dhl_manual_width[]" style="width:60%;padding:5px;border-radius:5px;margin-left:4px;" value="<?php echo $dimensions['Width'];?>" /> <b><?php echo $this->dim_unit; ?></b></td>
										<td><input type="text" id="dhl_manual_height" name="dhl_manual_height[]" style="width:60%;padding:5px;border-radius:5px;margin-left:4px;" value="<?php echo $dimensions['Height'];?>" /> <b><?php echo $this->dim_unit; ?></b></td>
										<td><input type="text" id="dhl_manual_insurance" name="dhl_manual_insurance[]" style="width:60%;padding:5px;border-radius:5px;margin-left:4px;" value="<?php echo $dimensions['insurance'];?>" /></td>
										<td><a class="tips wf_dhl_package_line_remove"  data-tip="<?php _e( 'Delete Package', 'wf-shipping-dhl' ); ?>" >&#x26D4;</a></td>
									</tr>
									<?php
								}
							}
						}
					}
				echo '</table>';
				echo '<a class="wf-action-button wcfm_submit_button wf-add-button button-secondary" style="font-size: 12px;" id="wf_dhl_add_package">Add Package</a>'; ?>
				<a class="button wcfm_submit_button tips dhl_generate_packages button-secondary"  href="<?php echo add_query_arg( 'wf_dhl_generate_packages', base64_encode($orderid), get_wcfm_view_order_url( $order_id ) ); ?>" data-tip="<?php _e( 'Regenerate Packages', 'wf-shipping-dhl' ); ?>"><?php _e( 'Regenerate', 'wf-shipping-dhl' ); ?></a> <?php
			
			echo '</li>';
			?>
			<script type="text/javascript">
				jQuery(document).ready(function(){
					jQuery('#wf_dhl_add_package').on("click", function(){
						var new_row = '<tr>';
							//new_row		+= '<td><select id="dhl_manual_service" name="dhl_manual_service[]" class="dhl_manual_service wc-enhanced-select"><?php echo $select_box_value; ?></select></td>';
							new_row 	+= '<td></td>';
							new_row 	+= '<td><input type="text" id="dhl_manual_weight" name="dhl_manual_weight[]" style="width:60%;padding:5px;border-radius:5px;margin-left:4px;" value="0"> <b><?php echo $this->weight_unit; ?></b></td>';
							new_row 	+= '<td><input type="text" id="dhl_manual_length" name="dhl_manual_length[]" style="width:60%;padding:5px;border-radius:5px;margin-left:4px;" value="0"> <b><?php echo $this->dim_unit; ?></b></td>';								
							new_row 	+= '<td><input type="text" id="dhl_manual_width" name="dhl_manual_width[]" style="width:60%;padding:5px;border-radius:5px;margin-left:4px;" value="0"> <b><?php echo $this->dim_unit; ?></b></td>';
							new_row 	+= '<td><input type="text" id="dhl_manual_height" name="dhl_manual_height[]" style="width:60%;padding:5px;border-radius:5px;margin-left:4px;" value="0"> <b><?php echo $this->dim_unit; ?></b></td>';
							 new_row 	+= '<td><input type="text" id="dhl_manual_insurance" name="dhl_manual_insurance[]" style="width:60%;padding:5px;border-radius:5px;margin-left:4px;" value="0"></td>';
							new_row 	+= '<td><a class="wf_dhl_package_line_remove tips" data-tip="Delete Package">&#x26D4;</a></td>';
						new_row 	+= '</tr>';
						
						jQuery('#wf_dhl_package_list tr:last').after(new_row);
					});
					
					jQuery(document).on('click', '.wf_dhl_package_line_remove', function(){
						if(confirm('Are you sure you want to remove this package?'))
						{
							jQuery(this).closest('tr').remove();
						}
					});
				});
			</script>
			<?php
			$return_service_data = get_post_meta($orderid,'_wf_dhl_available_services',true);
			
			echo '<li>';
				echo '<span style="font-weight:bold;">'.__( 'Choose Service' , 'wf-shipping-dhl').':</span>';
				echo '<table id="wf_dhl_service_select" class="wf-shipment-package-table" style="margin-bottom: 5px;margin-top: 5px;box-shadow:.5px .5px 5px lightgrey;">';					
					echo '<tr>';

						echo '<th></th>';
						echo '<th style="text-align:left;padding:5px;">'.__('Service Name', 'wf-shipping-dhl').'</th>';
						echo '<th style="text-align:left;">'.__('Delivery Time', 'wf-shipping-dhl').' </th>';
						echo '<th style="text-align:left;">'.__('Cost ('.get_woocommerce_currency_symbol().')', 'wf-shipping-dhl').' </th>';
						
					echo '</tr>';
					if(!empty($return_service_data))
					{
					foreach ($return_service_data as $key => $value) {
						echo '<tr style="padding:10px;">';
						
						if($this->show_front_end_shipping_method && $this->show_front_end_shipping_method === 'yes' && !empty($selected_sevice) && array_key_exists($selected_sevice,$return_service_data))
						{
							if(	$selected_sevice === $value['id'] )
							{
							echo '<td style="padding-left: 5px;padding-bottom: 3px;"><input name="wf_service_choosing_radio" id="wf_service_choosing_radio" value="'.$value['id'].'" type="radio" checked="true" ></td>';
							?>
							<td><small><?php echo $value['label']; ?></small></td>
							<td><small><?php echo $value['meta_data']['dhl_delivery_time']; ?></small></td>
							<td><small><?php 

							// Rate conversion
					        if ($this->conversion_rate) {
					                echo $value['cost'] * $this->conversion_rate;   
					        }else
					        {
					        	echo  $value['cost'];	
					        }
							 ?></small></td>
							</tr>
							<?php
								}
						}
						else
						{
						if(array_key_exists($selected_sevice,$return_service_data ))
						{

							if($selected_sevice === $value['id'])
							{
								
								echo '<td style="padding-left: 5px;padding-bottom: 3px;"><input name="wf_service_choosing_radio" id="wf_service_choosing_radio" value="'.$value['id'].'" type="radio" checked="true" ></td>';
						
							}	else
							{

								echo '<td style="padding-left: 5px;padding-bottom: 3px;" ><input name="wf_service_choosing_radio" id="wf_service_choosing_radio" value="'.$value['id'].'" type="radio"  ></td>';
							}
						}
						else
						{

							
							echo '<td style="padding-left: 5px;padding-bottom: 3px;"><input name="wf_service_choosing_radio" id="wf_service_choosing_radio" value="'.$value['id'].'" type="radio" checked="true" ></td>';
						} ?>
							
							<td><small><?php echo $value['label']; ?></small></td>
							<td><small><?php echo $value['meta_data']['dhl_delivery_time']; ?></small></td>
							<td><small><?php 

							// Rate conversion
					        if ($this->conversion_rate) {
					                echo $value['cost'] * $this->conversion_rate;   
					        }else
					        {
					        	echo  $value['cost'];	
					        }
							 ?></small></td>
						</tr>
						<?php
					}
					}
				}
				else
				{
					echo '<tr><td colspan="4"> Re-Calculate the Shipment</td></tr>';
				}                                                                                                     
				echo '</table>';
				?>
				<a class="button wcfm_submit_button tips wf_dhl_generate_packages_rates button-secondary"  href="<?php echo add_query_arg( 'wf_dhl_generate_packages_rates', base64_encode($orderid), get_wcfm_view_order_url( $order_id ) ); ?>" data-tip="<?php _e( 'Re-Calculate', 'wf-shipping-dhl' ); ?>"><?php _e( 'Re-Calculate', 'wf-shipping-dhl' ); ?></a>
				<?php
			
			
			if(!empty($this->sat_delivery) && $this->sat_delivery === 'yes') { ?>
			<li>
				<label for="wf_dhl_sat_delivery">
					<input type="checkbox" style="" id="wf_dhl_sat_delivery" name="wf_dhl_sat_delivery" class=""><?php _e('Saturday Delivery', 'wf-shipping-dhl') ?>
				</label>
			</li>

			<?php } ?>
			<?php if(!empty($this->cash_on_delivery) && $this->cash_on_delivery === 'yes') { ?>
			<li>
				<label for="wf_dhl_cash_on_delivery">
					<input type="checkbox" style="" id="wf_dhl_cash_on_delivery" name="wf_dhl_cash_on_delivery" class=""><?php _e('Cash on Delivery', 'wf-shipping-dhl') ?>
				</label>
			</li>

			<?php }
			echo '<hr style="border-color:#c9c9c9"></li>';
			 ?>
			
			<li>
				<a class="button wcfm_submit_button tips onclickdisable dhl_create_shipment button-primary" style="" href="<?php echo $generate_url; ?>" data-tip="<?php _e('Create Shipment', 'wf-shipping-dhl'); ?>"><?php _e('Create Shipment', 'wf-shipping-dhl'); ?></a> 
			</li></ul><?php
		} ?>
		<script type="text/javascript">
			jQuery("a.dhl_generate_packages").on("click", function() {
				location.href = this.href;
			});
		</script>
		<?php } ?>

	<script>
	
	jQuery("a.wf_dhl_generate_packages_rates").one("click", function() {
		
		jQuery(this).click(function () { return false; });
			// var manual_service_arr	=	[];
			// 			jQuery('.dhl_manual_service').each(function(){
			// 				manual_service_arr.push(jQuery(this).val());
			// 			});
			// var manual_service 		=	JSON.stringify(manual_service_arr);
			
			var manual_weight_arr 	= 	jQuery("input[id='dhl_manual_weight']").map(function(){return jQuery(this).val();}).get();
			
			var manual_height_arr 	= 	jQuery("input[id='dhl_manual_height']").map(function(){return jQuery(this).val();}).get();
			
			var manual_width_arr 	= 	jQuery("input[id='dhl_manual_width']").map(function(){return jQuery(this).val();}).get();
			
			var manual_length_arr 	= 	jQuery("input[id='dhl_manual_length']").map(function(){return jQuery(this).val();}).get();
			
			 var manual_insurance_arr 	= 	jQuery("input[id='dhl_manual_insurance']").map(function(){return jQuery(this).val();}).get();
				
		   location.href = this.href + '&weight=' + manual_weight_arr +
			'&length=' + manual_length_arr
			+ '&width=' + manual_width_arr
			+ '&height=' + manual_height_arr
			+ '&insurance=' + manual_insurance_arr;
		return false;			
	});
	jQuery("a.dhl_create_shipment").one("click", function() {
		
		jQuery(this).click(function () { return false; });
			// var manual_service_arr	=	[];
			// 			jQuery('.dhl_manual_service').each(function(){
			// 				manual_service_arr.push(jQuery(this).val());
			// 			});
			// var manual_service 		=	JSON.stringify(manual_service_arr);
			
			var manual_weight_arr 	= 	jQuery("input[id='dhl_manual_weight']").map(function(){return jQuery(this).val();}).get();
			var manual_weight 		=	JSON.stringify(manual_weight_arr);
			
			var manual_height_arr 	= 	jQuery("input[id='dhl_manual_height']").map(function(){return jQuery(this).val();}).get();
			var manual_height 		=	JSON.stringify(manual_height_arr);
			
			var manual_width_arr 	= 	jQuery("input[id='dhl_manual_width']").map(function(){return jQuery(this).val();}).get();
			var manual_width 		=	JSON.stringify(manual_width_arr);
			
			var manual_length_arr 	= 	jQuery("input[id='dhl_manual_length']").map(function(){return jQuery(this).val();}).get();
			var manual_length 		=	JSON.stringify(manual_length_arr);
			
			 var manual_insurance_arr 	= 	jQuery("input[id='dhl_manual_insurance']").map(function(){return jQuery(this).val();}).get();
			 var manual_insurance 		=	JSON.stringify(manual_insurance_arr);
			
			var selected = jQuery("input[name='wf_service_choosing_radio']:checked").val();
				
		   location.href = this.href + '&weight=' + manual_weight +
			'&length=' + manual_length
			+ '&width=' + manual_width
			+ '&height=' + manual_height
			+ '&insurance=' + manual_insurance
			+ '&sat_delivery=' + jQuery('#wf_dhl_sat_delivery').is(':checked')
			+ '&cash_on_delivery=' + jQuery('#wf_dhl_cash_on_delivery').is(':checked')
			+ '&dhl_express_shipping_service=' + selected;
		return false;			
	});
	</script>		
	<?php
}

private function get_dhl_dummy_package(){
	return array(
		'Dimensions' => array(
			'Length' => 0,
			'Width' => 0,
			'Height' => 0,
			'Units' => $this->dim_unit
		),
		'Weight' => array(
			'Value' => 0,
			'Units' => $this->weight_unit
		)
	);
}

public function get_dimension_from_package($package){
	$dimensions	=	array(
		'Length'	=>	0,
		'Width'		=>	0,
		'Height'	=>	0,
		'Weight'	=>	0,
		'insurance'	=>	0,
	);
	
	if(!is_array($package)){ // Package is not valid
		return $dimensions;
	}
	if(isset($package['Dimensions'])){
		$dimensions['Length']	=	$package['Dimensions']['Length'];
		$dimensions['Width']	=	$package['Dimensions']['Width'];
		$dimensions['Height']	=	$package['Dimensions']['Height'];
		$dimensions['dim_unit']	=	isset($package['Dimensions']['Units']) ? $package['Dimensions']['Units'] : 0 ;
	}
	
	$dimensions['Weight']	=	$package['Weight']['Value'];
	$dimensions['weight_unit']	=	$package['Weight']['Units'];
	$dimensions['insurance']	=	isset($package['InsuredValue']['Amount']) ? $package['InsuredValue']['Amount'] : 0;
	return $dimensions;
}	
}
new wcfm_dhl_woocommerce_shipping_admin();
