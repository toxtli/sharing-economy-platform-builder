<?php

class WCFM_Tracking_Admin_DHLExpress
{
	const SHIPPING_METHOD_DISPLAY	= "Tracking";
	const TRACKING_TITLE_DISPLAY	= "DHL Express Shipment Tracking";

	const TRACK_SHIPMENT_KEY		= "wf_dhlexpress_shipment"; //Note: If this key is getting changed, do the same change in JS code below.
	const SHIPMENT_SOURCE_KEY		= "wf_dhlexpress_shipment_source";
	const SHIPMENT_RESULT_KEY		= "wf_dhlexpress_shipment_result";
	const TRACKING_MESSAGE_KEY 		= "wfdhlexpresstrackingmsg";
	const TRACKING_METABOX_KEY		= "WF_Tracking_Metabox_DHLExpress";

	private function wf_init() {
		
		$this->settings 					 = get_option( 'woocommerce_'.WF_DHL_ID.'_settings', null );
		$this->site_id         = $this->settings['site_id'];
    	$this->site_password   = $this->settings['site_password'];
    	$this->add_trackingpin_shipmentid = isset($this->settings['add_trackingpin_shipmentid']) ? $this->settings['add_trackingpin_shipmentid'] : 'no';
	}

	function __construct(){
		$this->wf_init();

		if ( is_admin() ) { 
			add_action('admin_notices', array( $this, 'wf_admin_notice'), 15);
		}
		

		// Shipment Tracking - Customer Order Details Page.
		add_action( 'woocommerce_view_order', array( $this, 'wf_display_tracking_info_for_customer' ), 6 );
		//add_action( 'woocommerce_view_order', array( $this, 'wf_display_tracking_api_info_for_customer' ), 20 );
		add_action( 'woocommerce_email_order_meta', array( $this, 'wf_add_tracking_info_to_email'), 20 );

			
	}
	
	function get_tracking_info($get_id,$tracking_id)
	{
		$mailing_datetime = date('Y-m-d', time()) . 'T' . date('H:i:s', time());
$xmlRequest = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<req:KnownTrackingRequest xmlns:req="http://www.dhl.com" 
						xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
						xsi:schemaLocation="http://www.dhl.com
						TrackingRequestKnown.xsd">
	<Request>
		<ServiceHeader>
			<MessageTime>{$mailing_datetime}</MessageTime>
			<MessageReference>1234567890123456789012345678</MessageReference>
			<SiteID>{$this->site_id}</SiteID>
			<Password>{$this->site_password}</Password>
		</ServiceHeader>
	</Request>
	<LanguageCode>en</LanguageCode>
	<AWBNumber>{$tracking_id}</AWBNumber>
	<LevelOfDetails>ALL_CHECK_POINTS</LevelOfDetails>
	<PiecesEnabled>S</PiecesEnabled>       
</req:KnownTrackingRequest>
XML;

	$result = wp_remote_post("https://xmlpitest-ea.dhl.com/XMLShippingServlet", array(
		'method' => 'POST',
		'timeout' => 70,
		'sslverify' => 0,
        'body' => $xmlRequest
		)
	);

	$tracking_array = array();
	
	if ( is_wp_error( $result ) ) {
           $tracking_array['status'] = 'HTTP Faliure';
        }
     else
     {
		libxml_use_internal_errors(true);
		$xml = simplexml_load_string(utf8_encode($result['body']));
		
		if(isset($xml->AWBInfo->Status->ActionStatus))
		{
			$tracking_array['status'] = (string)$xml->AWBInfo->Status->ActionStatus;

			if(isset($xml->AWBInfo->ShipmentInfo->ShipmentEvent))
			{	$i=0;
				foreach ($xml->AWBInfo->ShipmentInfo->ShipmentEvent as $value) {
					$tracking_array['shippment'][$i]['date'] =  (string)$value->Date;
					$tracking_array['shippment'][$i]['time'] =  (string)$value->Time;
					$tracking_array['shippment'][$i]['desc'] =  (string)$value->ServiceEvent->Description;
					$tracking_array['shippment'][$i]['area'] =  (string)$value->ServiceArea->Description;
					$tracking_array['shippment'][$i]['code'] =  (string)$value->ServiceEvent->EventCode;
					$i++;
				}
			}
		}
	}
	return $tracking_array;
	}

	function wf_add_tracking_info_to_email( $order, $sent_to_admin = false, $plain_text = false ) {
		$shipment_result_array 	= get_post_meta( wf_get_order_id($order), self::SHIPMENT_RESULT_KEY, true );

		if( !empty( $shipment_result_array ) ) {
			echo '<h3>'.__( 'Shipping Detail', 'wf-shipping-dhl' ).'</h3>';
			$shipment_source_data 	= $this->get_shipment_source_data( wf_get_order_id($order) );
			$order_notice 	= WfTrackingUtil::get_shipment_display_message ( $shipment_result_array, $shipment_source_data );
			echo '<p>'.$order_notice.'</p></br>';
		}
	}
 
	public function wf_display_tracking_info_for_customer( $order_id ) {
		if($this->add_trackingpin_shipmentid === 'yes')
		{
		//$shipment_result_array 	= get_post_meta( $order_id , self::SHIPMENT_RESULT_KEY, true );
		$shipmentIds = get_post_meta($order_id, 'wf_woo_dhl_shipmentId', false);
		
		if( !empty( $shipmentIds ) ) {
			echo '<h2><b>Tracking Information</b></h2>';
		
			foreach ($shipmentIds as $shipmentId) {
				echo '<strong>Tracking ID #:</strong> <a href="http://www.dhl.com/en/express/tracking.html?AWB='.$shipmentId.'&brand=DHL" target="_blank">'. $shipmentId.'</a> ';
			$tracking_array = $this->get_tracking_info($order_id,$shipmentId);
			echo '<span style="">';
			//$last_checkpoint_status = '';
			$full_check_point_data = '';
			$dhl_confirm_order = false;
			$dhl_processing_order = false;
			$dhl_dispatch_order = false;
			$dhl_completed_order = false;
			$dhl_order_status = 'none';
			if($tracking_array['status'] !='success')
			{
			//	$last_checkpoint_status = ' <small> (No Shipments Found : Test Mode)</small>';
				$full_check_point_data .='<li>
							<div class="wf-dhl-direction-r">
								<div class="wf-dhl-wf-dhl-flag-wrapper">
									<span class="wf-dhl-flag">Test Mode</span>
									<span class="wf-dhl-wf-dhl-time-wrapper"><span class="wf-dhl-time">Faliure</span></span>
								</div>
								<div class="wf-dhl-desc">No Shipments Found</div>
							</div>
						</li>';
				$dhl_confirm_order = true;
				$dhl_order_status = 'No Shipment Found';
			}else
			{

				if(isset($tracking_array['shippment']))
				{
					$dhl_confirm_order = true;
					$dhl_processing_order = true;
					foreach ($tracking_array['shippment'] as $key => $value) {
			//		$last_checkpoint_status = empty($value['desc']) ? ' <small>(Shipment information received)</small>' : ' <small>('.$value['desc'].')</small>';
					$full_check_point_data .='<li>
							<div class="wf-dhl-direction-r">
								<div class="wf-dhl-wf-dhl-flag-wrapper">
									<span class="wf-dhl-flag">'.$value['date'].'</span>
									<span class="wf-dhl-wf-dhl-time-wrapper"><span class="wf-dhl-time">'.$value['time'].'</span></span>
								</div>
								<div class="wf-dhl-desc">'.$value['desc'].' , '.$value['area'].'</div>
							</div>
						</li>';
						$dhl_order_status = $value['desc'];
						if(isset($value['code']) && $value['code'] == 'PU')
						{
							$dhl_dispatch_order = true;
						}
						if(isset($value['code']) && $value['code'] == 'OK')
						{
							$dhl_completed_order = true;
						}
					}
					
					
				}else{
					$dhl_confirm_order =true;
			//		$last_checkpoint_status = ' <small>(Shipment information received)</small>';
					$full_check_point_data .='<li>
							<div class="wf-dhl-direction-r">
								<div class="wf-dhl-wf-dhl-flag-wrapper">
									<span class="wf-dhl-flag">Initial</span>
									<span class="wf-dhl-wf-dhl-time-wrapper"><span class="wf-dhl-time">Shipment Received</span></span>
								</div>
								<div class="wf-dhl-desc">Shipment information received</div>
							</div>
						</li>';
					$dhl_order_status = 'Shipment Received';
				}
			}
			//echo $last_checkpoint_status;

			echo ' <a href="#wf_dhl_metabox" id="wf_shipment_details_but"  style="text-decoration:none;color:#ba0c2f;">  <span class="dashicons dashicons-search"></span> </a></span><br><br>';
			}
			include_once(WF_DHL_PAKET_EXPRESS_ROOT_PATH. '/dhl_express/resources/css/tracking-front-end.php');
			?>
			

<!-- The wf-dhl-model -->
<div id="wf_shipment_data_popup" class="wf-dhl-model">

  <!-- wf-dhl-model content -->
  <div class="wf-dhl-model-content" style="height:95%;overflow-x: scroll;">
    <span class="wf-dhl-close">&times;</span>
 <!-- The wf-dhl-wf-dhl-timeline -->

 <div class="dhl_content">
	<div class="dhl_content2" style="padding:5px;left:15%;">
		<div class="dhl_content2-header1">
			<p>Shipped Via : <span><?php echo 'DHL Express'; ?></span></p>
		</div>
		<div class="dhl_content2-header1">
			<p>Status : <span><?php echo $dhl_order_status; ?></span></p>
		</div>
		<div class="dhl_content2-header1">
			<p>Order : <span>#<?php echo $order_id; ?></span></p>
		</div>
		<div class="dhl_clear"></div>
	</div>
	<div class="dhl_content3" style="margin-top:-20px;">
        <div class="dhl_shipment">
			<div class="dhl_confirm">
                <div class="dhl_imgcircle" style="<?php echo ($dhl_confirm_order) ? 'background-color:#ffcd00 !important':''; ?>">
                    <img style="top: 29% !important;left: 34% !important;" src="<?php echo WF_DHL_PAKET_PATH. '/dhl_express/resources/images/confirm.png'; ?>" alt="confirm order">
            	</div>
				<span class="dhl_line" style="<?php echo ($dhl_confirm_order) ? 'background-color:#ffcd00 !important':''; ?>"></span>
				<p>Confirmed</p>
			</div>
			<div class="dhl_process">
           	 	<div class="dhl_imgcircle" style="<?php echo ($dhl_processing_order) ? 'background-color:#ffcd00 !important':''; ?>">
                	<img style="top: 30% !important;left: 26% !important;" src="<?php echo WF_DHL_PAKET_PATH. '/dhl_express/resources/images/process.png'; ?>" alt="process order">
            	</div>
				<span class="dhl_line" style="<?php echo ($dhl_processing_order) ? 'background-color:#ffcd00 !important':''; ?>"></span>
				<p>Processing</p>
			</div>
			<div class="dhl_dispatch">
				<div class="dhl_imgcircle" style="<?php echo ($dhl_dispatch_order) ? 'background-color:#ffcd00 !important':''; ?>">
                	<img src="<?php echo WF_DHL_PAKET_PATH. '/dhl_express/resources/images/dispatch.png'; ?>" alt="dispatch product">
            	</div>
				<span class="dhl_line" style="<?php echo ($dhl_dispatch_order) ? 'background-color:#ffcd00 !important':''; ?>"></span>
				<p>Dispatched</p>
			</div>
			<div class="dhl_delivery">
				<div class="dhl_imgcircle"style="<?php echo ($dhl_completed_order) ? 'background-color:#ffcd00 !important':''; ?>">
                	<img style="top: 29% !important;left: 22% !important;" src="<?php echo WF_DHL_PAKET_PATH. '/dhl_express/resources/images/delivery.png'; ?>" alt="delivery">
				</div>
				<p>Delivered</p>
			</div>
			<div class="dhl_clear" ></div>
		</div>
	</div>
</div>
<br>
<center><h3> Result Summary </h3></center>
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
<?php
			// Note: There is a bug in wc_add_notice which gives inconstancy while displaying messages.
			// Uncomment after it gets resolved.
			// $this->display_notice_message( $order_notice );
			// $shipment_source_data 	= $this->get_shipment_source_data( $order_id );
			// $order_notice 	= WfTrackingUtil::get_shipment_display_message ( $shipment_result_array, $shipment_source_data );
			// echo $order_notice;
		}
	}
}
	public function wf_display_tracking_api_info_for_customer( $order_id ) {
		$turn_off_api = get_option( WfTrackingUtil::TRACKING_SETTINGS_TAB_KEY.WfTrackingUtil::TRACKING_TURN_OFF_API_KEY );
		if( 'yes' == $turn_off_api ) {
			return;
		}
		
		$shipment_result_array 	= get_post_meta( $order_id , self::SHIPMENT_RESULT_KEY, true );

		if( !empty( $shipment_result_array ) ) {
			if( !empty( $shipment_result_array['tracking_info_api'] ) ) {
				$this->display_api_message_table( $shipment_result_array['tracking_info_api'] );
			}
		}
	}

	function display_api_message_table ( $tracking_info_api_array ) {
		
		echo '<h3>'.__( self::TRACKING_TITLE_DISPLAY, 'wf-shipping-dhl' ).'</h3>';
		echo '<table class="shop_table wooforce_tracking_details">
			<thead>
				<tr>
					<th class="product-name">'.__( 'Shipment ID', 'wf-shipping-dhl' ).'<br/>('.__( 'Follow link for detailed status.', 'wf-shipping-dhl' ).')</th>
					<th class="product-total">'.__( 'Status', 'wf-shipping-dhl' ).'</th>
				</tr>
			</thead>
			<tfoot>';

		foreach ( $tracking_info_api_array as $tracking_info_api ) {
			echo '<tr>';
			echo '<th scope="row">'.'<a href="'.$tracking_info_api['tracking_link'].''.$tracking_info_api['tracking_id'].'" target="_blank">'.$tracking_info_api['tracking_id'].'</a></th>';
			
			if( '' == $tracking_info_api['api_tracking_status'] ) {
				$message = __( 'Unable to update real time status at this point of time. Please follow the link on shipment id to check status.', 'wf-shipping-dhl' );
			}
			else {
				$message = $tracking_info_api['api_tracking_status'];
			}
			echo '<td><span>'.__( $message, 'wf-shipping-dhl' ).'</span></td>';
			echo '</tr>';
		}
		echo '</tfoot>
		</table>';
	}

	function display_notice_message( $message, $type = 'notice' ) {
		if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '>=' ) ) {
			wc_add_notice( $message, $type );
		} else {
			global $woocommerce;
			$woocommerce->add_message( $message );
		}
	}

	function wf_admin_notice(){
		global $pagenow;
		global $post;
		
		if( !isset( $_GET[ self::TRACKING_MESSAGE_KEY ] ) && empty( $_GET[ self::TRACKING_MESSAGE_KEY ] ) ) {
			return;
		}

		$wftrackingmsg = $_GET[ self::TRACKING_MESSAGE_KEY ];

		switch ( $wftrackingmsg ) {
			case "0":
				echo '<div class="error"><p>'.self::SHIPPING_METHOD_DISPLAY.': '.__( 'Sorry, Unable to proceed.', 'wf-shipping-dhl' ).'</p></div>';
				break;
			case "4":
				echo '<div class="error"><p>'.self::SHIPPING_METHOD_DISPLAY.': '.__( 'Unable to track the shipment. Please cross check shipment id or try after some time.', 'wf-shipping-dhl' ).'</p></div>';
				break;
			case "5":
				$wftrackingmsg = get_post_meta( $post->ID, self::TRACKING_MESSAGE_KEY, true);
				if( '' != trim( $wftrackingmsg )) {
					echo '<div class="updated"><p>'.__( $wftrackingmsg, 'wf-shipping-dhl' ).'</p></div>';
				}
				break;
			case "6":
				echo '<div class="updated"><p>'.__( 'Tracking is unset.', 'wf-shipping-dhl' ).'</p></div>';
				break;
			case "7":
				echo '<div class="updated"><p>'.__( 'Tracking Data is reset to default.', 'wf-shipping-dhl' ).'</p></div>';
				break;
			default:
				break;
		}
	}

	function get_shipment_source_data( $post_id ) {
		$shipment_source_data 	= get_post_meta( $post_id, self::SHIPMENT_SOURCE_KEY, true );
		
		if ( empty( $shipment_source_data ) || !is_array( $shipment_source_data ) ) {
			$shipment_source_data	= array();
			$shipment_source_data['shipment_id_cs']		= '';
			$shipment_source_data['shipping_service']	= '';
			$shipment_source_data['order_date']			= '';
		}
		return $shipment_source_data;
	}
	
	function wf_tracking_metabox_content(){
		global $post;
		
		$tracking_url 	= admin_url( '/post.php?post='.( $post->ID ) );
		
		$shipment_source_data 	= $this->get_shipment_source_data( $post->ID );

	?>
		<ul class="order_actions submitbox">
			<li id="actions" class="wide" style="text-align: center;">
			<input type="text" id="wf_dhl_express_live_ids" value="<?php echo $shipment_source_data['shipment_id_cs'] ?>" style="padding: 8px 4px 7px;border-radius: 5px;" class="input-text" placeholder="Enter Tracking Id to Track" /><br>
    <a href="<?php echo $tracking_url; ?>"  class="button-primary woocommerce_shipment_dhlexpress_live_tracking">Track</a>
    
    </li>
    <?php
    $saved_tracking_data = get_post_meta($post->ID,'_dhl_tracking_shipment_tracking_data');
  
    foreach ($saved_tracking_data as $key => $value) {
    	foreach ($value['shippment'] as $key => $shipment) {
    		echo  '<p><img src="https://www.dhlparcel.nl/sites/default/files/pakket_alert.png" style="width:12px;"/> <small>'.$shipment['date'] . ' '. $shipment['time']. '</small><br><span style="padding-left:15px;">'. $shipment['desc'].'</span></p>';
    	}
    	  }
    ?>
    <li style="display:none;">
				<select name="shipping_service_dhlexpress"  id="shipping_service_dhlexpress">
	<?php
				echo "<option value=''>".__( 'None', 'wf-shipping-dhl' )."</option>";
				//foreach ( $this->tracking_data as $key => $details ) {
					//echo '<option value='.$key.' '.selected($shipment_source_data['shipping_service'], $key).' >'.__( $details[ "name" ], 'wf-shipping-dhl' ).'</option>';
					echo '<option value='.'dhl-express'.' '.selected($shipment_source_data['shipping_service'], 'dhl-express').' >'.__( 'DHL', 'wf-shipping-dhl' ).'</option>';
				//}
	?>
				</select><br>
				<strong><?php _e( 'Enter Tracking IDs', 'wf-shipping-dhl' ) ?></strong>
				<img class="help_tip" style="float:none;font-size: 17.5px;
    padding: 10px 6px 12px;" data-tip="<?php _e( 'Comma separated, in case of multiple shipment ids for this order.', 'wf-shipping-dhl' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /><br>
				<textarea id="tracking_dhlexpress_shipment_ids"  class="input-text" type="text" name="tracking_dhlexpress_shipment_ids" ><?php echo $shipment_source_data['shipment_id_cs']; ?></textarea><br>
				<strong>Shipment Date</strong>
				<img class="help_tip" style="float:none;" data-tip="<?php _e( 'This field is Optional.', 'wf-shipping-dhl' ); ?>" src="<?php echo WC()->plugin_url();?>/assets/images/help.png" height="16" width="16" /><br>
				<input type="text" id="order_date_dhlexpress" class="date-picker" value="<?php echo $shipment_source_data['order_date']; ?>"></p>
			</li>
			<li id="" class="wide" style="display:none;">
				<a class="button button-primary woocommerce_shipment_dhlexpress_tracking tips" href="<?php echo $tracking_url; ?>" data-tip="<?php _e( 'Save/Show Tracking Info', 'wf-shipping-dhl' ); ?>"><?php _e('Save/Show Tracking Info', 'wf-shipping-dhl'); ?></a>
			</li>
		</ul>
		<script>
			jQuery(document).ready(function($) {
				$( "date-picker" ).datepicker();
			});
			
			jQuery("a.woocommerce_shipment_dhlexpress_tracking").on("click", function() {
			   location.href = this.href + '&wf_dhlexpress_shipment=' + jQuery('#tracking_dhlexpress_shipment_ids').val().replace(/ /g,'')+'&shipping_service='+ jQuery( "#shipping_service_dhlexpress" ).val()+'&order_date='+ jQuery( "#order_date_dhlexpress" ).val();
			   return false;
			});
			jQuery("a.woocommerce_shipment_dhlexpress_live_tracking").on("click", function() {
			   location.href = this.href + '&wf_dhlexpress_live_shipment=' + jQuery('#wf_dhl_express_live_ids').val();
			   return false;
			});
		</script>
	<?php
	}

	public static function display_admin_notification_message( $post_id, $admin_notice ) {
		update_post_meta( $post_id, self::TRACKING_MESSAGE_KEY, $admin_notice );
	}
	
	public static function get_admin_notification_message_var(){
		$wftrackingmsg = 5;
		return self::TRACKING_MESSAGE_KEY.'='.$wftrackingmsg;
	}

	function get_shipment_info( $post_id, $shipment_source_data ) {

		if( empty( $post_id ) ) {
			$wftrackingmsg = 0;
			wp_redirect( admin_url( '/post.php?post='.$post_id.'&action=edit&'.self::TRACKING_MESSAGE_KEY.'='.$wftrackingmsg ) );
			exit;
		}
		
 		if( !empty($shipment_source_data['shipping_service'] )) {
			update_post_meta( $post_id, self::SHIPMENT_SOURCE_KEY, $shipment_source_data );
			update_post_meta( $post_id, self::SHIPMENT_RESULT_KEY, '' );

			$wftrackingmsg = 6;
			wp_redirect( admin_url( '/post.php?post='.$post_id.'&action=edit&'.self::TRACKING_MESSAGE_KEY.'='.$wftrackingmsg ) );
			exit;
		}
		
		update_post_meta( $post_id, self::SHIPMENT_SOURCE_KEY, $shipment_source_data );
		
		try {
			$shipment_result 	= WfTrackingUtil::get_shipment_result( $shipment_source_data );
		}catch( Exception $e ) {
			$wftrackingmsg = 0;
			wp_redirect( admin_url( '/post.php?post='.$post_id.'&action=edit&'.self::TRACKING_MESSAGE_KEY.'='.$wftrackingmsg ) );
			exit;
		}

		return $shipment_result;
	}

	function wf_load_order( $orderId ){
		if ( !class_exists( 'WC_Order' ) ) {
			return false;
		}
		return wc_get_order( $orderId );      
	}

	function wf_user_check() {
		if ( is_admin() ) {
			return true;
		}
		return false;
	}
}

new WCFM_Tracking_Admin_DHLExpress();