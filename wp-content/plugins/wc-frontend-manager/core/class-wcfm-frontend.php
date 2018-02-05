<?php

/**
 * WCFM Frontend Class
 *
 * @version		1.0.0
 * @package		wcfm/core
 * @author 		WC Lovers
 */
class WCFM_Frontend {
	
 	public function __construct() {
 		
 		// WCFM Page Template
 		add_action( 'page_template', array( &$this, 'wcfm_dashboard_template' ) );
 		
 		// WCFM End Point Title
 		add_filter( 'the_title', array( &$this, 'wcfm_page_endpoint_title' ) );
 		
 		// Check User Authentication to Access WCFM Pages
 		add_action( 'template_redirect', array(&$this, 'wcfm_template_redirect' ));
 		
 		// WCFM Icon at Shop
 		add_action( 'woocommerce_before_shop_loop', array( &$this, 'wcfm_home' ), 5 );
 		add_action( 'woocommerce_after_shop_loop', array( &$this, 'wcfm_home' ), 5 );
 		
 		// Product Manage from Archive Pages
 		add_action( 'woocommerce_before_shop_loop_item', array(&$this, 'wcfm_product_manage'), 5 );
		add_action( 'woocommerce_before_single_product_summary', array(&$this, 'wcfm_product_manage'), 5 );
		
		// WCFM Page Header panels
    add_action( 'wcfm_page_heading', array($this, 'wcfm_page_heading'), 10 );
    
    // WCFM Product Manager Taxonomy view - 3.0.3
    add_filter( 'wcfm_is_category_checklist', array(&$this, 'wcfm_is_category_checklist') );
    
    // WCFM Analytics Data Save - Version 2.2.5
    add_action( 'wp_footer', array( &$this, 'wcfm_save_page_analytics_data') );
    
    // WCFM Ultimate Inactive Notice
    add_filter( 'is_wcfmu_inactive_notice_show', array( &$this, 'is_wcfmu_inactive_notice_show') );
		
		//enqueue scripts
		add_action( 'wp_enqueue_scripts', array( &$this, 'wcfm_scripts' ), 15 );
		//enqueue styles
		add_action( 'wp_enqueue_scripts', array( &$this, 'wcfm_styles' ), 15 );
		
 	}
 	
 	/**
 	 * WCFM Page template if full screen selected
 	 */
	function wcfm_dashboard_template( $page_template ) {
		global $WCFM;
		if ( wc_post_content_has_shortcode( 'wc_frontend_manager' ) && is_user_logged_in() ) {
			$wcfm_options = get_option('wcfm_options');
			$is_dashboard_full_view_disabled = isset( $wcfm_options['dashboard_full_view_disabled'] ) ? $wcfm_options['dashboard_full_view_disabled'] : 'no';
			$is_dashboard_theme_header_disabled = isset( $wcfm_options['dashboard_theme_header_disabled'] ) ? $wcfm_options['dashboard_theme_header_disabled'] : 'no';
			if( $is_dashboard_full_view_disabled != 'yes' ) {
				$template_path = WC()->template_path();
				$skin_path     = $WCFM->plugin_path . 'templates/classic/';
				if( $is_dashboard_theme_header_disabled == 'yes' ) $skin_path     = $WCFM->plugin_path . 'templates/default/';
				$page_template = wc_locate_template( 'wcfm-content.php', $template_path, $skin_path );
			}
		}

		return $page_template;
	}
 	
 	/**
	 * Replace a page title with the endpoint title.
	 * @param  string $title
	 * @return string
	 */
	function wcfm_page_endpoint_title( $title ) {
		global $WCFM, $WCFM_Query, $wp_query;
	
		if ( ! is_null( $wp_query ) && ! is_admin() && is_main_query() && in_the_loop() && is_page() && is_wcfm_endpoint_url() ) {
			$endpoint = $WCFM_Query->get_current_endpoint();
	
			if ( $endpoint_title = $WCFM_Query->get_endpoint_title( $endpoint ) ) {
				$title = $endpoint_title;
			}
	
			remove_filter( 'the_title', array( &$this, 'wcfm_page_endpoint_title' ) );
		}
	
		return $title;
	}
	
	/**
	 * Template redirect function
	 * @return void
	*/
	function wcfm_template_redirect() {
		global $WCFM;
		
		// If user not loggedin then reirect to Home page
		if( !is_user_logged_in() && is_wcfm_page() ) {
      wp_safe_redirect( get_permalink( wc_get_page_id( 'myaccount' ) ) );
      exit();
    }
    
    // If user loggedin but not admin or shop manager then reirect to MyAccount page
		if( is_user_logged_in() && is_wcfm_page() ) {
			$user = wp_get_current_user();
			$allowed_roles = apply_filters( 'wcfm_allwoed_user_rols',  array( 'administrator', 'shop_manager' ) );
			if ( !array_intersect( $allowed_roles, (array) $user->roles ) )  {
				wp_safe_redirect(  get_permalink( wc_get_page_id( 'myaccount' ) ) );
				exit();
			}
		}
	}
	
	/**
	 * WCFM Home at Archive Pages
	 */
	function wcfm_home() {
 		global $WCFM;
 		
 		$wcfm_options = (array) get_option( 'wcfm_options' );
 		$is_quick_access_disabled = isset( $wcfm_options['quick_access_disabled'] ) ? $wcfm_options['quick_access_disabled'] : 'no';
 		if( $is_quick_access_disabled == 'yes' ) return;
 			
 		if( !is_user_logged_in() ) return;
		$user = wp_get_current_user();
		$allowed_roles = apply_filters( 'wcfm_allwoed_user_rols',  array( 'administrator', 'shop_manager' ) );
		if ( !array_intersect( $allowed_roles, (array) $user->roles ) )  return;
		
		$quick_access_image_url = isset( $wcfm_options['wcfm_quick_access_icon'] ) ? $wcfm_options['wcfm_quick_access_icon'] : $WCFM->plugin_url . '/assets/images/wcfm-30x30.png';
 		echo '<a href="' . get_wcfm_page() . '"><img class="text_tip" data-tip="' . __( 'Dashboard', 'wc-frontend-manager' ) . '" id="wcfm_home" src="' . $quick_access_image_url . '" width="30" alt="' . __( 'Dashboard', 'wc-frontend-manager' ) . '" /></a>';
 	}
	
	/**
	 * WCFM Product Manage from Archive Pages
	 */
	function wcfm_product_manage() {
		global $WCFM, $post, $woocommerce_loop;

		if( !is_user_logged_in() ) return;
		if( !apply_filters( 'wcfm_is_allow_quick_edit_product', true ) ) return;
		$user = wp_get_current_user();
		$allowed_roles = apply_filters( 'wcfm_allwoed_user_rols',  array( 'administrator', 'shop_manager' ) );
		if ( !array_intersect( $allowed_roles, (array) $user->roles ) )  return;
				
		$current_user = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		if( wcfm_is_vendor() && ( $current_user != $post->post_author ) ) return;
		
		$pro_id = $post->ID;
		$_product = wc_get_product($pro_id);
		
		?>
		<div class="wcfm_buttons">
		  <?php do_action( 'wcfm_product_manage', $pro_id, $_product ); ?>
		  <?php if( current_user_can( 'edit_published_products' ) && apply_filters( 'wcfm_is_allow_edit_products', true ) ) { ?>
				<a class="wcfm_button" href="<?php echo get_wcfm_edit_product_url( $pro_id, $_product ); ?>"> <span class="fa fa-edit text_tip" data-tip="<?php echo esc_attr__( 'Edit', 'wc-frontend-manager' ); ?>"></span> </a>
		  <?php } ?>
		  <?php if( current_user_can( 'delete_published_products' ) && apply_filters( 'wcfm_is_allow_edit_products', true ) ) { ?>
		  	<span class="wcfm_button_separator">|</span>
		  	<a class="wcfm_button wcfm_delete_product" href="#" data-proid="<?php echo $pro_id; ?>"><span class="fa fa-trash-o text_tip" data-tip="<?php echo esc_attr__( 'Delete', 'wc-frontend-manager' ); ?>"></span> </a>
		  <?php } ?>
		</div>
		<?php
		
	}
	
	/**
	 * WCFM Pages Header Panels
	 * 
	 * @since 2.3.2
	 */
	function wcfm_page_heading() {
		global $WCFM, $wpdb;
		require_once( $WCFM->library->views_path . 'wcfm-view-header-panels.php' );
	}
	
	/**
	 * WCFM Category view as Checklist
	 */
	function wcfm_is_category_checklist( $is_checklist_view ) {
		
		$wcfm_options = get_option('wcfm_options');
	  $is_checklist_view_disabled = isset( $wcfm_options['checklist_view_disabled'] ) ? $wcfm_options['checklist_view_disabled'] : 'no';
	  if( $is_checklist_view_disabled == 'yes' ) $is_checklist_view = false;
		
		return $is_checklist_view;
	}
	
	/**
	 * WCFM sent messages
	 *
	 * @since 3.0.6
	 */
	function wcfm_send_direct_message( $author_id, $message_to, $author_is_admin, $author_is_vendor, $wcfm_messages, $wcfm_messages_type = 'direct' ) {
		global $WCFM, $wpdb;
		
		$is_notice = 0;
		$is_direct_message = 1;
    		
		$wcfm_create_message     = "INSERT into {$wpdb->prefix}wcfm_messages 
																(`message`, `author_id`, `author_is_admin`, `author_is_vendor`, `is_notice`, `is_direct_message`, `message_to`, `message_type`)
																VALUES
																('{$wcfm_messages}', {$author_id}, {$author_is_admin}, {$author_is_vendor}, {$is_notice}, {$is_direct_message}, {$message_to}, '{$wcfm_messages_type}')";
																
		$wpdb->query($wcfm_create_message);
		
		$messageid = $wpdb->insert_id;
		$todate = date('Y-m-d H:i:s');
		if( $messageid ) {
			$wcfm_read_message     = "INSERT into {$wpdb->prefix}wcfm_messages_modifier 
																	(`message`, `is_read`, `read_by`, `read_on`)
																	VALUES
																	({$messageid}, 1, {$author_id}, '{$todate}')";
			$wpdb->query($wcfm_read_message);
		}
	}
	
	/**
	 * WCFM unread message count
	 *
	 * @since 2.3.4
	 */
	function wcfm_direct_message_count( $message_type = 'notice', $message_status = 'unread' ) {
		global $WCFM, $wpdb;
		
		$message_to = apply_filters( 'wcfm_message_author', get_current_user_id() );
		
		if( $message_type == 'enquiry' ) {
			$sql = "SELECT COUNT(wcfm_enquiries.ID) FROM {$wpdb->prefix}wcfm_enquiries AS wcfm_enquiries";
			$sql .= " WHERE 1=1";
			$sql .= " AND `reply` = ''";
			if( wcfm_is_vendor() ) { 
				$sql .= " AND `vendor_id` = {$message_to}";
			}
			$sql = apply_filters( 'wcfm_enquery_list_query', $sql );
			$total_mesaages = $wpdb->get_var( $sql );
		} else {
			$sql = 'SELECT COUNT(wcfm_messages.ID) FROM ' . $wpdb->prefix . 'wcfm_messages AS wcfm_messages';
			$sql .= ' WHERE 1=1';
			
			if( $message_type == 'notice' ) {
				$status_filter = " AND `is_notice` = 1";
			} elseif( $message_type == 'message' ) {
				$status_filter = " AND `is_direct_message` = 1";
			}
			$sql .= $status_filter;
			
			if( wcfm_is_vendor() ) { 
				//$vendor_filter = " AND `author_is_admin` = 1";
				$vendor_filter = " AND ( `author_id` = {$message_to} OR `message_to` = -1 OR `message_to` = {$message_to} )";
				$sql .= $vendor_filter;
			} else {
				$group_manager_filter = apply_filters( 'wcfm_notification_group_manager_filter', '' );
				if( $group_manager_filter ) {
					$sql .= $group_manager_filter;
				} else {
					$sql .= " AND `author_id` != -1";
				}
			}
			
			$message_status_filter = " AND NOT EXISTS (SELECT * FROM {$wpdb->prefix}wcfm_messages_modifier as wcfm_messages_modifier_2 WHERE wcfm_messages.ID = wcfm_messages_modifier_2.message AND wcfm_messages_modifier_2.read_by={$message_to})";
			$sql .= $message_status_filter;
			
			$total_mesaages = $wpdb->get_var( $sql );
		}
		
		return  $total_mesaages;
	}
	
	function getIP() {
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
			 if (array_key_exists($key, $_SERVER) === true) {
				foreach (explode(',', $_SERVER[$key]) as $ip) {
				  if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
						return $ip;
				  }
				 }
			 }
		 }
   }
   
	/**
	 * Saving WCFM Page Analytics Data
	 * @since 2.2.5
	 */
	function wcfm_save_page_analytics_data() {
		global $WCFM, $_SERVER, $post, $wpdb, $_SESSION;
		
		//$_SESSION['wcfm_pages'] = array( 'shop' => 'no', 'stores' => array(), 'products' => array() );
		
		$todate = date('Y-m-d');
		
		if( !isset($_SERVER['HTTP_REFERER']) ) $_SERVER['HTTP_REFERER'] = '';
		
		if( !isset( $location['location'] ) || !isset( $_SESSION['location']['country'] ) || !isset( $_SESSION['location']['state'] ) || !isset( $_SESSION['location']['city'] ) ) {
			//$json = file_get_contents('http://getcitydetails.geobytes.com/GetCityDetails?fqcn='. $this->getIP()); 
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, 'http://getcitydetails.geobytes.com/GetCityDetails?fqcn='. $this->getIP() );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			$json = curl_exec($ch);
			curl_close ($ch);
			$data = json_decode($json);
			$_SESSION['location']['country'] = $data->geobytesinternet;
			$_SESSION['location']['state'] = $data->geobytescode;
			$_SESSION['location']['city'] = $data->geobytescity;
			//echo '<b>'. $this->getIP() .'</b> resolves to: '. $data->geobytesinternet . "::" . $data->geobytescode . "::" . $data->geobytescity . "::" . var_dump($data);
		}
		
		// vendor store
		$is_marketplace = wcfm_is_marketplace();
		
		if( is_shop() ) {
			$wc_shop = true;
			if( $is_marketplace == 'wcvendors' ) {
		  	if ( WCV_Vendors::is_vendor_page() ) {
		  		$wc_shop = false;
		  		$vendor_shop 		= urldecode( get_query_var( 'vendor_shop' ) );
		  		$vendor_id   		= WCV_Vendors::get_vendor_id( $vendor_shop );
		  		if( !isset( $_SESSION['wcfm_pages'] ) || !isset( $_SESSION['wcfm_pages']['stores'] ) || ( isset( $_SESSION['wcfm_pages'] ) && isset( $_SESSION['wcfm_pages']['stores'] ) && !in_array( $vendor_id, $_SESSION['wcfm_pages']['stores'] ) ) ) {
						// wcfm_detailed_analysis Query
						$wcfm_detailed_analysis = "INSERT into {$wpdb->prefix}wcfm_detailed_analysis 
																			(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `referer`, `ip_address`, `country`, `state`, `city`)
																			VALUES
																			(0, 1, 0, -1, {$vendor_id}, '{$_SERVER['HTTP_REFERER']}', '{$_SERVER['REMOTE_ADDR']}', '{$_SESSION['location']['country']}', '{$_SESSION['location']['state']}', '{$_SESSION['location']['city']}')";
						$wpdb->query($wcfm_detailed_analysis);
						
						// wcfm_daily_analysis Query
						$wcfm_daily_analysis = "INSERT into {$wpdb->prefix}wcfm_daily_analysis 
																			(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `count`, `visited`)
																			VALUES
																			(0, 1, 0, -1, {$vendor_id}, 1, '{$todate}')
																			ON DUPLICATE KEY UPDATE
																			count = count+1";
						$wpdb->query($wcfm_daily_analysis);
						
						// Session store
						$_SESSION['wcfm_pages']['stores'][] = $vendor_id;
					}
		  	}
		  }
		  
		  if( $wc_shop ) {
		  	if( !isset( $_SESSION['wcfm_pages'] ) || !isset( $_SESSION['wcfm_pages']['shop'] ) || ( isset( $_SESSION['wcfm_pages'] ) && isset( $_SESSION['wcfm_pages']['shop'] ) && ( 'no' == $_SESSION['wcfm_pages']['shop'] ) ) ) {
		  		$post_author = $post->post_author;
		  		if( !$post_author ) $post_author = 1;
					// wcfm_detailed_analysis Query
					$wcfm_detailed_analysis = "INSERT into {$wpdb->prefix}wcfm_detailed_analysis 
																		(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `referer`, `ip_address`, `country`, `state`, `city`)
																		VALUES
																		(1, 0, 0, 0, {$post_author}, '{$_SERVER['HTTP_REFERER']}', '{$_SERVER['REMOTE_ADDR']}', '{$_SESSION['location']['country']}', '{$_SESSION['location']['state']}', '{$_SESSION['location']['city']}')";
					$wpdb->query($wcfm_detailed_analysis);
					
					// wcfm_daily_analysis Query
					$wcfm_daily_analysis = "INSERT into {$wpdb->prefix}wcfm_daily_analysis 
																		(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `count`, `visited`)
																		VALUES
																		(1, 0, 0, 0, {$post_author}, 1, '{$todate}')
																		ON DUPLICATE KEY UPDATE
																		count = count+1";
					$wpdb->query($wcfm_daily_analysis);
					
					// Session store
					$_SESSION['wcfm_pages']['shop'] = 'yes';
				}
			}
		} elseif( is_product_category() ) {
			$product_category = get_queried_object()->term_id;
			if( !isset( $_SESSION['wcfm_pages'] ) || !isset( $_SESSION['wcfm_pages']['product_category'] ) || ( isset( $_SESSION['wcfm_pages'] ) && isset( $_SESSION['wcfm_pages']['product_category'] ) && !in_array( $product_category, $_SESSION['wcfm_pages']['product_category'] ) ) ) {
				// wcfm_detailed_analysis Query
				$wcfm_detailed_analysis = "INSERT into {$wpdb->prefix}wcfm_detailed_analysis 
																	(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `referer`, `ip_address`, `country`, `state`, `city`)
																	VALUES
																	(9, 0, 0, {$product_category}, 1, '{$_SERVER['HTTP_REFERER']}', '{$_SERVER['REMOTE_ADDR']}', '{$_SESSION['location']['country']}', '{$_SESSION['location']['state']}', '{$_SESSION['location']['city']}')";
				$wpdb->query($wcfm_detailed_analysis);
				
				// wcfm_daily_analysis Query
				$wcfm_daily_analysis = "INSERT into {$wpdb->prefix}wcfm_daily_analysis 
																	(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `count`, `visited`)
																	VALUES
																	(9, 0, 0, {$product_category}, 1, 1, '{$todate}')
																	ON DUPLICATE KEY UPDATE
																	count = count+1";
				$wpdb->query($wcfm_daily_analysis);
				
				// Session store
				$_SESSION['wcfm_pages']['product_category'][] = $product_category;
			}
		} elseif( is_product() ) {
			if( !isset( $_SESSION['wcfm_pages'] ) || !isset( $_SESSION['wcfm_pages']['products'] ) || ( isset( $_SESSION['wcfm_pages'] ) && isset( $_SESSION['wcfm_pages']['products'] ) && !in_array( $post->ID, $_SESSION['wcfm_pages']['products'] ) ) ) {
				$post_author = $post->post_author;
		  	if( !$post_author ) $post_author = 1;
				// wcfm_detailed_analysis Query
				$wcfm_detailed_analysis = "INSERT into {$wpdb->prefix}wcfm_detailed_analysis 
																	(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `referer`, `ip_address`, `country`, `state`, `city`)
																	VALUES
																	(0, 0, 1, {$post->ID}, {$post_author}, '{$_SERVER['HTTP_REFERER']}', '{$_SERVER['REMOTE_ADDR']}', '{$_SESSION['location']['country']}', '{$_SESSION['location']['state']}', '{$_SESSION['location']['city']}')";
				$wpdb->query($wcfm_detailed_analysis);
				
				// wcfm_daily_analysis Query
				$wcfm_daily_analysis = "INSERT into {$wpdb->prefix}wcfm_daily_analysis 
																	(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `count`, `visited`)
																	VALUES
																	(0, 0, 1, {$post->ID}, {$post_author}, 1, '{$todate}')
																	ON DUPLICATE KEY UPDATE
																	count = count+1";
				$wpdb->query($wcfm_daily_analysis);
				
				$wcfm_product_views = (int) get_post_meta( $post->ID, '_wcfm_product_views', true );
				if( !$wcfm_product_views ) $wcfm_product_views = 1;
				else $wcfm_product_views += 1;
				update_post_meta( $post->ID, '_wcfm_product_views', $wcfm_product_views );
				
				// Session store
				$_SESSION['wcfm_pages']['products'][] = $post->ID;
			}
		} elseif( is_singular( 'job_listing' ) ) {
			if( !isset( $_SESSION['wcfm_pages'] ) || !isset( $_SESSION['wcfm_pages']['listings'] ) || ( isset( $_SESSION['wcfm_pages'] ) && isset( $_SESSION['wcfm_pages']['listings'] ) && !in_array( $post->ID, $_SESSION['wcfm_pages']['listings'] ) ) ) {
				$wcfm_listing_views = (int) get_post_meta( $post->ID, '_wcfm_listing_views', true );
				if( !$wcfm_listing_views ) $wcfm_listing_views = 1;
				else $wcfm_listing_views += 1;
				update_post_meta( $post->ID, '_wcfm_listing_views', $wcfm_listing_views );
				
				// Session store
				$_SESSION['wcfm_pages']['listings'][] = $post->ID;
			}
		} else {
		  if( $is_marketplace == 'wcmarketplace' ) {
		  	if (is_tax('dc_vendor_shop')) {
		  		$vendor = get_wcmp_vendor_by_term(get_queried_object()->term_id);
		  		if( !isset( $_SESSION['wcfm_pages'] ) || !isset( $_SESSION['wcfm_pages']['stores'] ) || ( isset( $_SESSION['wcfm_pages'] ) && isset( $_SESSION['wcfm_pages']['stores'] ) && !in_array( $vendor->id, $_SESSION['wcfm_pages']['stores'] ) ) ) {
						// wcfm_detailed_analysis Query
						$wcfm_detailed_analysis = "INSERT into {$wpdb->prefix}wcfm_detailed_analysis 
																			(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `referer`, `ip_address`, `country`, `state`, `city`)
																			VALUES
																			(0, 1, 0, -1, {$vendor->id}, '{$_SERVER['HTTP_REFERER']}', '{$_SERVER['REMOTE_ADDR']}', '{$_SESSION['location']['country']}', '{$_SESSION['location']['state']}', '{$_SESSION['location']['city']}')";
						$wpdb->query($wcfm_detailed_analysis);
						
						// wcfm_daily_analysis Query
						$wcfm_daily_analysis = "INSERT into {$wpdb->prefix}wcfm_daily_analysis 
																			(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `count`, `visited`)
																			VALUES
																			(0, 1, 0, -1, {$vendor->id}, 1, '{$todate}')
																			ON DUPLICATE KEY UPDATE
																			count = count+1";
						$wpdb->query($wcfm_daily_analysis);
						
						// Session store
						$_SESSION['wcfm_pages']['stores'][] = $vendor->id;
					}
		  	}
		  } elseif( $is_marketplace == 'wcpvendors' ) {
		  	if (is_tax('wcpv_product_vendors')) {
		  		$vendor_shop = get_queried_object()->term_id;
		  		if( !isset( $_SESSION['wcfm_pages'] ) || !isset( $_SESSION['wcfm_pages']['stores'] ) || ( isset( $_SESSION['wcfm_pages'] ) && isset( $_SESSION['wcfm_pages']['stores'] ) && !in_array( $vendor_shop, $_SESSION['wcfm_pages']['stores'] ) ) ) {
						// wcfm_detailed_analysis Query
						$wcfm_detailed_analysis = "INSERT into {$wpdb->prefix}wcfm_detailed_analysis 
																			(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `referer`, `ip_address`, `country`, `state`, `city`)
																			VALUES
																			(0, 1, 0, -1, {$vendor_shop}, '{$_SERVER['HTTP_REFERER']}', '{$_SERVER['REMOTE_ADDR']}', '{$_SESSION['location']['country']}', '{$_SESSION['location']['state']}', '{$_SESSION['location']['city']}')";
						$wpdb->query($wcfm_detailed_analysis);
						
						// wcfm_daily_analysis Query
						$wcfm_daily_analysis = "INSERT into {$wpdb->prefix}wcfm_daily_analysis 
																			(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `count`, `visited`)
																			VALUES
																			(0, 1, 0, -1, {$vendor_shop}, 1, '{$todate}')
																			ON DUPLICATE KEY UPDATE
																			count = count+1";
						$wpdb->query($wcfm_daily_analysis);
						
						// Session store
						$_SESSION['wcfm_pages']['stores'][] = $vendor_shop;
					}
		  	}
		  } elseif( $is_marketplace == 'dokan' ) {
		  	if( dokan_is_store_page() ) {
		  		$custom_store_url = dokan_get_option( 'custom_store_url', 'dokan_general', 'store' );
		  		$store_name = get_query_var( $custom_store_url );
		  		$vendor_id  = 0;
		  		if ( !empty( $store_name ) ) {
            $store_user = get_user_by( 'slug', $store_name );
          }
		  		$vendor_id   		= $store_user->ID;
		  		if( !isset( $_SESSION['wcfm_pages'] ) || !isset( $_SESSION['wcfm_pages']['stores'] ) || ( isset( $_SESSION['wcfm_pages'] ) && isset( $_SESSION['wcfm_pages']['stores'] ) && !in_array( $vendor_id, $_SESSION['wcfm_pages']['stores'] ) ) ) {
						// wcfm_detailed_analysis Query
						$wcfm_detailed_analysis = "INSERT into {$wpdb->prefix}wcfm_detailed_analysis 
																			(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `referer`, `ip_address`, `country`, `state`, `city`)
																			VALUES
																			(0, 1, 0, -1, {$vendor_id}, '{$_SERVER['HTTP_REFERER']}', '{$_SERVER['REMOTE_ADDR']}', '{$_SESSION['location']['country']}', '{$_SESSION['location']['state']}', '{$_SESSION['location']['city']}')";
						$wpdb->query($wcfm_detailed_analysis);
						
						// wcfm_daily_analysis Query
						$wcfm_daily_analysis = "INSERT into {$wpdb->prefix}wcfm_daily_analysis 
																			(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `count`, `visited`)
																			VALUES
																			(0, 1, 0, -1, {$vendor_id}, 1, '{$todate}')
																			ON DUPLICATE KEY UPDATE
																			count = count+1";
						$wpdb->query($wcfm_daily_analysis);
						
						// Session store
						$_SESSION['wcfm_pages']['stores'][] = $vendor_id;
					}
		  	}
		  }
		}
		
		//print_R($_SERVER);
	}
	
	/**
	 * Check is a product shipping or not
	 */
	function is_wcfm_needs_shipping( $product ) {
		$needs_shipping = true; 
		               
		$non_shipped_product_types = apply_filters( 'wcfm_non_shipped_product_types', array( 'booking', 'accommodation-booking', 'appointment', 'job_package', 'auction', 'resume_package' ) ); 
		if ( !is_object( $product ) ) {
			$needs_shipping = false; 
		} elseif ( is_object( $product ) && ( $product->is_virtual() || $product->is_downloadable() || in_array( $product->get_type(), $non_shipped_product_types ) ) ) {
			$needs_shipping = false; 
		}
		
		return $needs_shipping;
	}
	
	/**
	 * Is WCFM Ultimate Inactive Notice Show
	 */
	function is_wcfmu_inactive_notice_show( $show ) {
		$wcfm_options = get_option('wcfm_options');
	  $is_ultimate_notice_disabled = isset( $wcfm_options['ultimate_notice_disabled'] ) ? $wcfm_options['ultimate_notice_disabled'] : 'no';
	  if( $is_ultimate_notice_disabled == 'yes' ) $show = false;
		return $show;
	}
	
	/**
	 * WCFM Core JS
	 */
	function wcfm_scripts() {
 		global $WCFM, $wp, $WCFM_Query;
 		
 		if( isset( $_REQUEST['fl_builder'] ) ) return;
 		
 		// Libs
	  $WCFM->library->load_qtip_lib();
 		
 		// Core JS
	  wp_enqueue_script( 'wcfm_core_js', $WCFM->library->js_lib_url . 'wcfm-script-core.js', array('jquery', 'wcfm_qtip_js' ), $WCFM->version, true );
	  
	  $unread_message = $WCFM->frontend->wcfm_direct_message_count( 'message' );
		$unread_enquiry = $WCFM->frontend->wcfm_direct_message_count( 'enquiry' );
	  
	  // Localize Script
	  $tinyMCE_toolbar_options = 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify |  bullist numlist outdent indent | link image | ltr rtl';
	  wp_localize_script( 'wcfm_core_js', 'wcfm_params', array( 'ajax_url'    => WC()->ajax_url(), 'shop_url' => get_permalink( wc_get_page_id( 'shop' ) ), 'wcfm_is_allow_wcfm' => wcfm_is_allow_wcfm(), 'wcfm_allow_tinymce_options' => apply_filters( 'wcfm_allow_tinymce_options', $tinyMCE_toolbar_options ), 'unread_message' => $unread_message, 'unread_enquiry' => $unread_enquiry ) );
	  
	  // Load End Point Scripts
	  if( is_wcfm_page() ) {
			if ( isset( $wp->query_vars['page'] ) ) {
				$WCFM->library->load_scripts( 'wcfm-dashboard' );
			} else {
				$wcfm_endpoints = $WCFM_Query->get_query_vars();
				$is_endpoint = false;
				foreach ( $wcfm_endpoints as $key => $value ) {
					if ( isset( $wp->query_vars[ $key ] ) ) {
						$WCFM->library->load_scripts( $key );
						$is_endpoint = true;
					}
				}
				if( !$is_endpoint ) {
					// Load dashboard Scripts
					$WCFM->library->load_scripts( 'wcfm-dashboard' );
				}
			}
		}
 	}
 	
 	/**
 	 * WCFM Core CSS
 	 */
 	function wcfm_styles() {
 		global $WCFM, $wp, $WCFM_Query;
 		
 		if( isset( $_REQUEST['fl_builder'] ) ) return;
 		
 		// WC Icon set
	  wp_enqueue_style( 'wcfm_wc_icon_css',  $WCFM->library->css_lib_url . 'wcfm-style-icon.css', array(), $WCFM->version );
	  
	  // Font Awasome Icon set
	  wp_enqueue_style( 'wcfm_fa_icon_css',  $WCFM->plugin_url . 'assets/fonts/font-awesome/css/font-awesome.min.css', array(), $WCFM->version );
	  
	  // Admin Bar CSS
	  wp_enqueue_style( 'wcfm_admin_bar_css',  $WCFM->library->css_lib_url . 'wcfm-style-adminbar.css', array(), $WCFM->version );
	  
	  // WCFM Core CSS
	  wp_enqueue_style( 'wcfm_core_css',  $WCFM->library->css_lib_url . 'wcfm-style-core.css', array(), $WCFM->version );
	  
	  // Load End Point CSS
	  if( is_wcfm_page() ) {
			if ( isset( $wp->query_vars['page'] ) ) {
				$WCFM->library->load_styles( 'wcfm-dashboard' );
			} else {
				$wcfm_endpoints = $WCFM_Query->get_query_vars();
				$is_endpoint = false;
				foreach ( $wcfm_endpoints as $key => $value ) {
					if ( isset( $wp->query_vars[ $key ] ) ) {
						$WCFM->library->load_styles( $key );
						$is_endpoint = true;
					}
				}
				if( !$is_endpoint ) {
					// Load dashboard Scripts
					$WCFM->library->load_styles( 'wcfm-dashboard' );
				}
			}
		}
 	}
 	
}