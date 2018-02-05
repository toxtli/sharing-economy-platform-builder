<?php
global $WCFM, $wpdb;

$order_count = 0;
$on_hold_count    = 0;
$processing_count = 0;

foreach ( wc_get_order_types( 'order-count' ) as $type ) {
	$counts           = (array) wp_count_posts( $type );
	$on_hold_count    += isset( $counts['wc-on-hold'] ) ? $counts['wc-on-hold'] : 0;
	$processing_count += isset( $counts['wc-processing'] ) ? $counts['wc-processing'] : 0;
	
	$order_count    += isset( $counts['wc-on-hold'] ) ? $counts['wc-on-hold'] : 0;
	$order_count    += isset( $counts['wc-processing'] ) ? $counts['wc-processing'] : 0;
	$order_count    += isset( $counts['wc-completed'] ) ? $counts['wc-completed'] : 0;
	$order_count    += isset( $counts['wc-pending'] ) ? $counts['wc-pending'] : 0;
}


// Get products using a query - this is too advanced for get_posts :(
$stock          = absint( max( get_option( 'woocommerce_notify_low_stock_amount' ), 1 ) );
$nostock        = absint( max( get_option( 'woocommerce_notify_no_stock_amount' ), 0 ) );
$transient_name = 'wc_low_stock_count';

if ( false === ( $lowinstock_count = get_transient( $transient_name ) ) ) {
	$query_from = apply_filters( 'woocommerce_report_low_in_stock_query_from', "FROM {$wpdb->posts} as posts
		INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
		INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
		WHERE 1=1
		AND posts.post_type IN ( 'product', 'product_variation' )
		AND posts.post_status = 'publish'
		AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
		AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) <= '{$stock}'
		AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) > '{$nostock}'
	" );
	$lowinstock_count = absint( $wpdb->get_var( "SELECT COUNT( DISTINCT posts.ID ) {$query_from};" ) );
	set_transient( $transient_name, $lowinstock_count, DAY_IN_SECONDS * 30 );
}

$transient_name = 'wc_outofstock_count';

if ( false === ( $outofstock_count = get_transient( $transient_name ) ) ) {
	$query_from = apply_filters( 'woocommerce_report_out_of_stock_query_from', "FROM {$wpdb->posts} as posts
		INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
		INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
		WHERE 1=1
		AND posts.post_type IN ( 'product', 'product_variation' )
		AND posts.post_status = 'publish'
		AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
		AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) <= '{$nostock}'
	" );
	$outofstock_count = absint( $wpdb->get_var( "SELECT COUNT( DISTINCT posts.ID ) {$query_from};" ) );
	set_transient( $transient_name, $outofstock_count, DAY_IN_SECONDS * 30 );
}

include_once( $WCFM->plugin_path . 'includes/reports/class-wcfm-report-sales-by-date.php' );

// For net sales block value
$wcfm_report_sales_by_date_block = new WCFM_Report_Sales_By_Date( '7day' );
$wcfm_report_sales_by_date_block->calculate_current_range( '7day' );
$report_data_block   = $wcfm_report_sales_by_date_block->get_report_data();

// For sales by date graph
$wcfm_report_sales_by_date = new WCFM_Report_Sales_By_Date( 'month' );
$wcfm_report_sales_by_date->calculate_current_range( 'month' );
$report_data   = $wcfm_report_sales_by_date->get_report_data();

// WCFM Analytics
include_once( $WCFM->plugin_path . 'includes/reports/class-wcfm-report-analytics.php' );
$wcfm_report_analytics = new WCFM_Report_Analytics();
$wcfm_report_analytics->chart_colors = apply_filters( 'wcfm_report_analytics_chart_colors', array(
			'view_count'       => '#C79810',
		) );
$wcfm_report_analytics->calculate_current_range( '7day' );

$user_id = get_current_user_id();
$wp_user_avatar_id = get_user_meta( $user_id, 'wp_user_avatar', true );
$wp_user_avatar = wp_get_attachment_url( $wp_user_avatar_id );
if ( !$wp_user_avatar ) {
	$wp_user_avatar = $WCFM->plugin_url . 'assets/images/user.png';
}

$is_marketplace = wcfm_is_marketplace();

do_action( 'before_wcfm_dashboard' );
?>

<div class="collapse wcfm-collapse" id="wcfm_dashboard">

  <div class="wcfm-page-headig">
		<span class="fa fa-dashboard"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Dashboard', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		
		<?php do_action( 'begin_wcfm_dashboard' ); ?>
		
		<?php require_once( $WCFM->library->views_path . 'dashboard/wcfm-view-dashboard-welcome-box.php' ); ?>
		
		<?php if( apply_filters( 'wcfm_is_pref_stats_box', true ) ) { ?>
			<div class="wcfm_dashboard_stats">
				<?php if ( apply_filters( 'wcfm_is_allow_reports', true ) && current_user_can( 'view_woocommerce_reports' ) && ( $report_data_block ) ) { ?>
					<div class="wcfm_dashboard_stats_block">
						<a href="<?php echo get_wcfm_reports_url( 'month' ); ?>">
							<span class="fa fa-currency"><?php echo get_woocommerce_currency_symbol() ; ?></span>
							<div>
								<strong><?php echo wc_price( $report_data_block->total_sales ); ?></strong><br />
								<?php _e( 'gross sales in last 7 days', 'wc-frontend-manager' ); ?>
							</div>
						</a>
					</div>
				<?php } ?>
				
				<?php
				if( $is_marketplace ) {
					$commission = $WCFM->wcfm_vendor_support->wcfm_get_commission_by_vendor();
					//$total_sell = $WCFM->wcfm_vendor_support->wcfm_get_total_sell_by_vendor();
					
					$admin_fee_mode = false;
					if( $is_marketplace == 'wcmarketplace' ) {
						global $WCMp;
						if (isset($WCMp->vendor_caps->payment_cap['revenue_sharing_mode'])) {
							if ($WCMp->vendor_caps->payment_cap['revenue_sharing_mode'] == 'admin') {
								$admin_fee_mode = true;
								$grose_sell = $WCFM->wcfm_vendor_support->wcfm_get_gross_sales_by_vendor();
								$commission = $grose_sell - $commission;
							}
						}
					} elseif( $is_marketplace == 'dokan' ) {
						$grose_sell = $WCFM->wcfm_vendor_support->wcfm_get_gross_sales_by_vendor();
						$commission = $grose_sell - $commission;
					}
				?>
					<div class="wcfm_dashboard_stats_block">
						<a href="<?php echo get_wcfm_reports_url( ); ?>">
							<span class="fa fa-money"></span>
							<div>
								<strong><?php echo wc_price( $commission ); ?></strong><br />
								<?php if( $admin_fee_mode ) { _e( 'admin fees in last 7 days', 'wc-frontend-manager' ); } else { _e( 'commission in last 7 days', 'wc-frontend-manager' ); } ?>
							</div>
						</a>
					</div>
					<div class="wcfm_dashboard_stats_block">
						<a href="<?php echo apply_filters( 'sales_by_product_report_url', get_wcfm_reports_url( ), '' ); ?>">
							<span class="fa fa-cubes"></span>
							<div>
								<?php printf( _n( "<strong>%s item</strong><br />", "<strong>%s items</strong><br />", $report_data_block->total_items, 'wc-frontend-manager' ), $report_data_block->total_items ); ?>
								<?php _e( 'sold in last 7 days', 'wc-frontend-manager' ); ?>
							</div>
						</a>
					</div>
				<?php
				}
				?>
				<?php if ( apply_filters( 'wcfm_is_allow_orders', true ) && current_user_can( 'edit_shop_orders' ) ) { ?>
					<div class="wcfm_dashboard_stats_block">
						<a href="<?php echo get_wcfm_orders_url( ); ?>">
							<span class="fa fa-cart-plus"></span>
							<div>
								<?php printf( _n( "<strong>%s order</strong><br />", "<strong>%s orders</strong><br />", $report_data_block->total_orders, 'wc-frontend-manager' ), $report_data_block->total_orders ); ?>
								<?php _e( 'received in last 7 days', 'wc-frontend-manager' ); ?>
							</div>
						</a>
					</div>
				<?php } ?>
			</div>
			<div class="wcfm-clearfix"></div>
		<?php } ?>
		<?php do_action( 'wcfm_after_dashboard_stats_box' ); ?>
		
		<?php if ( apply_filters( 'wcfm_is_allow_reports', true ) && current_user_can( 'view_woocommerce_reports' ) ) { ?>
			<div class="wcfm_dashboard_wc_reports_sales">
				<div class="wcfm-container">
					<div id="wcfm_dashboard_wc_reports_expander_sales" class="wcfm-content">
						<div id="poststuff" class="woocommerce-reports-wide">
							<div class="postbox">
								<div class="inside">
									<a class="chart_holder_anchor" href="<?php echo get_wcfm_reports_url( 'month' ); ?>">
										<?php $wcfm_report_sales_by_date->get_main_chart(0); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="wcfm-clearfix"></div>
		<?php } ?>
		
		<div class="wcfm_dashboard_wc_status">
		
			<div class="wcfm_dashboard_wc_status_data">
			
			  <?php if ( $is_wcfm_analytics_enable = is_wcfm_analytics() ) { ?>
					<?php if ( $wcfm_is_allow_analytics = apply_filters( 'wcfm_is_allow_analytics', true ) ) { ?>
						<div class="wcfm_dashboard_wcfm_analytics">
							<div class="page_collapsible" id="wcfm_dashboard_wcfm_anaytics"><span class="fa fa-line-chart"></span><span class="dashboard_widget_head"><?php _e('Store Analytics', 'wc-frontend-manager'); ?></span></div>
							<div class="wcfm-container">
								<div id="wcfm_dashboard_wcfm_analytics_expander" class="wcfm-content">
									<div id="poststuff" class="woocommerce-reports-wide">
										<div class="postbox">
											<div class="inside">
												<?php if( WCFM_Dependencies::wcfma_plugin_active_check() ) { ?>
													<a class="chart_holder_anchor" href="<?php echo get_wcfm_analytics_url( 'month' ); ?>">
												<?php } ?>
														<?php $wcfm_report_analytics->get_main_chart(); ?>
												<?php if( WCFM_Dependencies::wcfma_plugin_active_check() ) { ?>
													</a>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
				
				<?php if ( !is_wcfm_analytics() || WCFM_Dependencies::wcfma_plugin_active_check() ) { ?>
					<div class="wcfm_dashboard_wcfm_product_stats">
						<div class="page_collapsible" id="wcfm_dashboard_wcfm_product_status"><span class="fa fa-cubes"></span><span class="dashboard_widget_head"><?php _e('Product Stats', 'wc-frontend-manager'); ?></span></div>
						<div class="wcfm-container">
							<div id="wcfm_dashboard_wcfm_product_stats_expander" class="wcfm-content">
								 <?php if ( current_user_can( 'edit_products' ) && apply_filters( 'wcfm_is_allow_manage_products', true ) ) { ?>
								 <a class="chart_holder_anchor" href="<?php echo get_wcfm_products_url( ); ?>">
								 <?php } ?>
									 <div id="product_stats-report"><canvas id="product_stats_report-canvas"></canvas></div>	
								 <?php if ( current_user_can( 'edit_products' ) && apply_filters( 'wcfm_is_allow_manage_products', true ) ) { ?>
								 </a>
								 <?php } ?>
							</div>
						</div>
					</div>
				<?php } ?>
				
				<?php do_action( 'after_wcfm_dashboard_product_stats' ); ?>
				
				<?php if( apply_filters( 'wcfm_is_pref_stats_box', true ) ) { ?>
					<?php if( apply_filters( 'wcfm_is_allow_reports', true ) || apply_filters( 'wcfm_is_allow_orders', true ) ) { ?>
						<div class="wcfm_dashboard_more_stats">
							<div class="page_collapsible" id="wcfm_dashboard_wc_status">
								<span class="fa fa-linode fa-clock-o"></span>
								<span class="dashboard_widget_head"><?php _e('Store Stats', 'wc-frontend-manager'); ?></span>
							</div>
							<div class="wcfm-container">
								<div id="wcfm_dashboard_wc_status_expander" class="wcfm-content">
									<ul class="wc_status_list">
										<?php
										if ( current_user_can( 'view_woocommerce_reports' ) && ( $top_seller = $this->get_top_seller() ) && $top_seller->qty ) {
											?>
											<li class="best-seller-this-month">
												<a href="<?php echo apply_filters( 'sales_by_product_report_url',  get_wcfm_reports_url( ), $top_seller->product_id ); ?>">
													<span class="fa fa-cube"></span>
													<?php printf( __( '%s top seller in last 7 days (sold %d)', 'wc-frontend-manager' ), '<strong>' . get_the_title( $top_seller->product_id ) . '</strong> - ', $top_seller->qty ); ?>
												</a>
											</li>
											<?php
										}
										?>
										
										<?php do_action( 'after_wcfm_dashboard_sales_reports' ); ?>
										
										<?php if ( current_user_can( 'edit_shop_orders' ) ) { ?>
										<li class="processing-orders">
											<a href="<?php echo get_wcfm_orders_url( 'processing' ); ?>">
												<span class="fa fa-life-ring"></span>
												<?php printf( _n( "<strong>%s order</strong> - processing", "<strong>%s orders</strong> - processing", $processing_count, 'wc-frontend-manager' ), $processing_count ); ?>
											</a>
										</li>
										<li class="on-hold-orders">
											<a href="<?php echo get_wcfm_orders_url( 'on-hold' ); ?>">
												<span class="fa fa-minus-circle"></span>
												<?php printf( _n( "<strong>%s order</strong> - on-hold", "<strong>%s orders</strong> - on-hold", $on_hold_count, 'wc-frontend-manager' ), $on_hold_count ); ?>
											</a>
										</li>
										<?php } ?>
										
										<?php do_action( 'after_wcfm_dashboard_orders' ); ?>
										
										<li class="low-in-stock">
											<a href="<?php echo apply_filters( 'low_in_stock_report_url',  get_wcfm_reports_url( ) ); ?>">
												<span class="fa fa-sort-amount-desc"></span>
												<?php printf( _n( "<strong>%s product</strong> - low in stock", "<strong>%s products</strong> - low in stock", $lowinstock_count, 'wc-frontend-manager' ), $lowinstock_count ); ?>
											</a>
										</li>
										<li class="out-of-stock">
											<a href="<?php echo get_wcfm_reports_url( '', 'wcfm-reports-out-of-stock' ); ?>">
												<span class="fa fa-times-circle-o"></span>
												<?php printf( _n( "<strong>%s product</strong> - out of stock", "<strong>%s products</strong> - out of stock", $outofstock_count, 'wc-frontend-manager' ), $outofstock_count ); ?>
											</a>
										</li>
										
										<?php do_action( 'after_wcfm_dashboard_stock_reports' ); ?>
										
									</ul>
								</div>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
				
			</div>
			
			<?php do_action( 'after_wcfm_dashboard_left_col' ); ?>
			
			<div class="wcfm_dashboard_wc_status_graph">
			
				<?php if ( apply_filters( 'wcfm_is_allow_reports', true ) && current_user_can( 'view_woocommerce_reports' ) ) { ?>
					<div class="wcfm_dashboard_wc_reports_pie">
						<div class="page_collapsible" id="wcfm_dashboard_wc_reports_pie"><span class="fa fa-pie-chart"></span><span class="dashboard_widget_head"><?php _e('Sales by Product', 'wc-frontend-manager'); ?></span></div>
						<div class="wcfm-container">
							<div id="wcfm_dashboard_wc_reports_expander_pie" class="wcfm-content">
								<a class="chart_holder_anchor" href="<?php echo apply_filters( 'sales_by_product_report_url',  get_wcfm_reports_url( ), ( $top_seller ) ? $top_seller->product_id : '' ); ?>">
									<div id="sales-piechart"><canvas id="sales-piechart-canvas"></canvas></div>
								</a>
							</div>
						</div>
					</div>
					<?php do_action('after_wcfm_dashboard_sales_report'); ?>
				<?php } ?>
				
				<?php if ( is_wcfm_analytics() && WCFM_Dependencies::wcfma_plugin_active_check() ) { ?>
					<?php if ( $wcfm_is_allow_analytics = apply_filters( 'wcfm_is_allow_analytics', true ) ) { ?>
						<div class="wcfm_dashboard_wcfm_region_stats">
							<div class="page_collapsible" id="wcfm_dashboard_wcfm_region_status"><span class="fa fa-globe"></span><span class="dashboard_widget_head"><?php _e('Top Regions', 'wc-frontend-manager'); ?></span></div>
							<div class="wcfm-container">
								<div id="wcfm_dashboard_wcfm_region_stats_expander" class="wcfm-content">
									 <a class="chart_holder_anchor" href="<?php echo get_wcfm_analytics_url( 'month' ); ?>">
										 <div id="wcfm_world_map_analytics_view"></div>
										 <?php
										 global $WCFMa;
										 $WCFMa->library->world_map_analytics_data(); 
										 ?>
									 </a>
								</div>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
				
				<?php do_action('after_wcfm_dashboard_zone_analytics'); ?>
				
				<?php if( $wcfm_is_allow_notice = apply_filters( 'wcfm_is_allow_notice', true ) ) { ?>
					<div class="wcfm_dashboard_latest_topics">
						<div class="page_collapsible" id="wcfm_dashboard_latest_topics"><span class="fa fa-bullhorn"></span><span class="dashboard_widget_head"><?php _e('Latest Topics', 'wc-frontend-manager'); ?></span></div>
						<div class="wcfm-container">
							<div id="wcfm_dashboard_latest_topics_expander" class="wcfm-content">
								<?php
								$args = array(
									'posts_per_page'   => 5,
									'offset'           => 0,
									'orderby'          => 'date',
									'order'            => 'DESC',
									'post_type'        => 'wcfm_notice',
									'post_parent'      => 0,
									'post_status'      => array('draft', 'pending', 'publish'),
									'suppress_filters' => 0 
								);
								$args = apply_filters( 'wcfm_notice_args', $args );
								$wcfm_notices_array = get_posts( $args );
								
								if( !empty( $wcfm_notices_array ) ) {
									foreach($wcfm_notices_array as $wcfm_notices_single) {
										echo '<div class="wcfm_dashboard_latest_topic"><a href="' . get_wcfm_notice_view_url($wcfm_notices_single->ID) . '" class="wcfm_dashboard_item_title"><span class="fa fa-bullhorn"></span>' . substr( $wcfm_notices_single->post_title, 0, 80 ) . ' ...</a></div>';
									}
								} else {
									_e( 'There is no topic yet!!', 'wc-frontend-manager' );
								}
								?>
							</div>
						</div>
					</div>
				<?php } ?>
			  
			</div>
			<?php do_action( 'after_wcfm_dashboard_right_col' ); ?>
		</div>
	</div>
</div>