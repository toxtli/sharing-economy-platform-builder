<?php
/**
 * WCFM plugin core
 *
 * Plugin non Ajax Controler
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   1.1.6
 */
 
class WCFM_Non_Ajax {

	public function __construct() {
		global $WCFM;
		
		// WCFM Dashboard Product Stats Report - 3.0.0
		add_action( 'after_wcfm_dashboard_left_col', array( &$this, 'wcfm_dashboard_product_stats_report' ) );
		
		// WCFM Dashboard Sales Report
		add_action( 'after_wcfm_dashboard_sales_report', array( &$this, 'wcfm_dashboard_sales_report' ) );
		
		// Plugins page help links
		add_filter( 'plugin_action_links_' . $WCFM->plugin_base_name, array( &$this, 'wcfm_plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( &$this, 'wcfm_plugin_row_meta' ), 10, 2 );
		
		add_action( 'admin_bar_menu', array( &$this, 'wcfm_admin_bar_menu' ), 100 );
		
	}
	
	/**
	 * WCFM Dashboard Product Stats Report
	 */
	function wcfm_dashboard_product_stats_report() {
		global $WCFM, $wpdb;
		
		if ( is_wcfm_analytics() && !WCFM_Dependencies::wcfma_plugin_active_check() ) return;
		
		$current_user_id = apply_filters( 'wcfm_current_vendor_id', get_current_user_id() );
		if( current_user_can( 'administrator' ) ) $current_user_id = 0;
		
		$publish_count = wcfm_get_user_posts_count( $current_user_id, 'product', 'publish' );
		$pending_count = wcfm_get_user_posts_count( $current_user_id, 'product', 'pending' );
		$draft_count   = wcfm_get_user_posts_count( $current_user_id, 'product', 'draft' );
		
		$product_stat = '{"online" : ' . $publish_count . ', "pending" : ' . $pending_count . ', "draft" : ' . $draft_count . '}';
		?>
		<script type="text/javascript">
			var product_stat_data = <?php echo $product_stat; ?>;
			jQuery(document).ready(function ($) {
	
				var barProductStatsData = {
            labels: ["<?php _e( 'Online', 'wc-frontend-manager' ); ?>", "<?php _e( 'Pending', 'wc-frontend-manager' ); ?>", "<?php _e( 'Draft', 'wc-frontend-manager' ); ?>"],
            datasets: [{
                label: 'Count',
                backgroundColor: color(window.chartColors.purple).alpha(0.3).rgbString(),
                borderColor: window.chartColors.purple,
                borderWidth: 2,
                data: [product_stat_data.online, product_stat_data.pending, product_stat_data.draft]
            }]

        };

        window.onload = function() {
            var ctx = document.getElementById("product_stats_report-canvas").getContext("2d");
            window.wcfmProductStatsBar = new Chart(ctx, {
                type: 'bar',
                data: barProductStatsData,
                options: {
                    responsive: true,
                    legend: {
                    	  display: false,
                        position: 'top',
                    },
                    title: {
                        display: false,
                        text: 'Product Stats Report'
                    }
                }
            });

        };

			});
  	</script>
		<?php
		
	}
	
	/**
	 * WCFM Dashboard Sales Report
	 */
	function wcfm_dashboard_sales_report() {
		global $WCFM, $wpdb;
		
		$wcfm_is_allow_reports = apply_filters( 'wcfm_is_allow_reports', true );
		if( !$wcfm_is_allow_reports ) return;
			
		$query            = array();
		$query['fields']  = "SELECT SUM( order_item_meta.meta_value ) as qty, order_item_meta_2.meta_value as product_id
			FROM {$wpdb->posts} as posts";
		$query['join']    = "INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_id ";
		$query['join']   .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id ";
		$query['join']   .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id ";
		$query['where']   = "WHERE posts.post_type IN ( 'shop_order','shop_order_refund' ) ";
		$query['where']  .= "AND posts.post_status IN ( 'wc-" . implode( "','wc-", apply_filters( 'woocommerce_reports_order_statuses', array( 'completed', 'processing', 'on-hold' ) ) ) . "' ) ";
		$query['where']  .= "AND order_item_meta.meta_key = '_qty' ";
		$query['where']  .= "AND order_item_meta_2.meta_key = '_product_id' ";
		$query['where']  .= "AND posts.post_date >= '" . date( 'Y-m-d', strtotime( '-7 DAY', current_time( 'timestamp' ) ) ) . "' ";
		$query['where']  .= "AND posts.post_date <= '" . date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) . "' ";
		$query['groupby'] = "GROUP BY product_id";
		$query['orderby'] = "ORDER BY qty DESC";
		$query['limits']  = "LIMIT 5";
		
		$top_sellers = $wpdb->get_results( implode( ' ', apply_filters( 'woocommerce_dashboard_status_widget_top_seller_query', $query ) ) );
		$top_sellers_array = '';
		$top_seller_pro = '';
		$top_seller_labels = '';
		$top_seller_datas = '';
		if( !empty($top_sellers) ) {
			foreach( $top_sellers as $index => $top_seller ) {
				if($top_seller_labels) $top_seller_labels .= ',';
				if($top_seller_datas) $top_seller_datas .= ',';
				
				$top_seller_labels .= '"' . get_the_title( $top_seller->product_id ) . '"';
				$top_seller_datas  .= '"' . $top_seller->qty . '"';
				
			}
		}
		
		if($top_seller_labels && $top_seller_datas) {
			$top_seller_pro = '{"labels": [' . $top_seller_labels . '], "datas": [' . $top_seller_datas . ']}';
		} else {
			$top_seller_pro = '{"labels": ["' . __( 'No sales yet ..!!!', 'wc-frontend-manager' ) . '"], "datas": [1] }';
		}
		
		?>
		<script type="text/javascript">
		  var top_sellers_array = <?php echo ($top_seller_pro); ?>;
		  jQuery(document).ready(function($) {
		  	jQuery('#sales-piechart').css( 'width', jQuery('#sales-piechart').outerWidth() + 'px' );
			  var config = {
						type: 'pie',
						data: {
								datasets: [{
										data: top_sellers_array.datas,
										backgroundColor: [
											  window.chartColors.green,
											  window.chartColors.blue,
												window.chartColors.red,
												window.chartColors.orange,
												window.chartColors.purple,
										],
										label: 'Top Selling Products'
								}],
								labels: top_sellers_array.labels
						},
						options: {
							responsive: true,
							legend: {
								position: "bottom",
								//display:  false
							},
							responsive: true
						}
				};
				
				var ctx = document.getElementById("sales-piechart-canvas").getContext("2d");
        window.topSellerPie = new Chart(ctx, config);
		} );
    </script>
		<?php
	}
	
	/**
	 * Show action links on the plugin screen.
	 *
	 * @param	mixed $links Plugin Action links
	 * @return	array
	 */
	public function wcfm_plugin_action_links( $links ) {
		global $WCFM;
		$action_links = array(
			'settings' => '<a target="_blank" href="' . admin_url( 'admin.php?page=wcfm_settings' ) . '" aria-label="' . esc_attr__( 'View WCFM settings', 'wc-frontend-manager' ) . '">' . esc_html__( 'Settings', 'wc-frontend-manager' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param	mixed $links Plugin Row Meta
	 * @param	mixed $file  Plugin Base file
	 * @return	array
	 */
	public function wcfm_plugin_row_meta( $links, $file ) {
		global $WCFM;
		if ( $WCFM->plugin_base_name == $file ) {
			$row_meta = array(
				'docs'      => '<a target="_blank" href="' . esc_url( apply_filters( 'wcfm_docs_url', 'https://wclovers.com/knowledgebase/' ) ) . '" aria-label="' . esc_attr__( 'View WCFM documentation', 'wc-frontend-manager' ) . '">' . esc_html__( 'Documentation', 'wc-frontend-manager' ) . '</a>',
				'faq'       => '<a target="_blank" href="' . esc_url( apply_filters( 'wcfm_faq_url', 'https://wclovers.com/faq/' ) ) . '" aria-label="' . esc_attr__( 'View WCFM FAQ', 'wc-frontend-manager' ) . '">' . esc_html__( 'FAQ', 'wc-frontend-manager' ) . '</a>',
				'support'   => '<a target="_blank" href="' . esc_url( apply_filters( 'wcfm_support_url', 'https://wclovers.com/forums' ) ) . '" aria-label="' . esc_attr__( 'Visit premium customer support', 'woocommerce' ) . '">' . esc_html__( 'Support', 'woocommerce' ) . '</a>',
				//'contactus' => '<a href="' . esc_url( apply_filters( 'wcfm_contactus_url', 'http://wclovers.com/contact-us/' ) ) . '" aria-label="' . esc_attr__( 'Any WC help feel free to contact us', 'wc-frontend-manager' ) . '">' . esc_html__( 'Contact US', 'wc-frontend-manager' ) . '</a>'
			);
			
			$ultimate_meta = array();
			if(!WCFM_Dependencies::wcfmu_plugin_active_check()) {
				$ultimate_meta = array( 'ultimate' => '<a href="' . esc_url( apply_filters( 'wcfm_ultimate_url', 'https://wclovers.com/product/woocommerce-frontend-manager-ultimate/' ) ) . '" aria-label="' . esc_attr__( 'Add more power to your WCFM', 'wc-frontend-manager' ) . '">' . esc_html__( 'WCFM Ultimate', 'wc-frontend-manager' ) . '</a>' );
			}

			return array_merge( $links, $row_meta, $ultimate_meta );
		}

		return (array) $links;
	}
	
	function wcfm_admin_bar_menu() {
		global $WCFM, $wp_admin_bar;
		
		$wcfm_menus = $WCFM->get_wcfm_menus();
		//unset($wcfm_menus['settings']);
		
		$title = '<div class="wcfm-admin-menu-head"><img src="' . $WCFM->plugin_url . '/assets/images/wcfm-30x30.png" alt="WCFM Home" /><span class="screen-reader-text">' . __( 'WCFM', 'wordpress-seo' ) . '</span></div>';
		
		$wp_admin_bar->add_menu( array(
			'id'    => 'wcfm-menu',
			'title' => $title,
			'href'  => get_wcfm_url(),
			'meta'   => array( 'tabindex' => 0 )
		) );
		
		if( !empty($wcfm_menus) ) {
			foreach( $wcfm_menus as $wcfm_menu_key => $wcfm_menu_data ) {
				if( !isset( $wcfm_menu_data['capability'] ) || empty( $wcfm_menu_data['capability'] ) || apply_filters( $wcfm_menu_data['capability'], true ) ) {
					$wp_admin_bar->add_menu( array(
						'parent'    => 'wcfm-menu',
						'id' => 'wcfm-menu-'. $wcfm_menu_key,
						'title' => '<span class="wcfm-admin-menu">' . $wcfm_menu_data['label'] . '</span>',
						'href'  => $wcfm_menu_data['url'],
						'meta'   => array( 'tabindex' => 0 )
					) );
					
					if( isset( $wcfm_menu_data['has_new'] ) ) {
						if( !isset( $wcfm_menu_data['submenu_capability'] ) || empty( $wcfm_menu_data['submenu_capability'] ) || apply_filters( $wcfm_menu_data['submenu_capability'], true ) ) {
							$wp_admin_bar->add_menu( array(
								'parent'    => 'wcfm-menu-'. $wcfm_menu_key,
								'id' => 'wcfm-menu-sub-parent-'. $wcfm_menu_key,
								'title' => '<span class="wcfm-admin-menu">' . $wcfm_menu_data['label'] . '</span>',
								'href'  => $wcfm_menu_data['url'],
								'meta'   => array( 'tabindex' => 0 )
							) );
							$wp_admin_bar->add_menu( array(
								'parent'    => 'wcfm-menu-'. $wcfm_menu_key,
								'id' => 'wcfm-menu-sub-'. $wcfm_menu_key,
								'title' => '<span class="wcfm-admin-menu">' . __( 'Add New', 'wc-frontend-manager' ) . '</span>',
								'href'  => $wcfm_menu_data['new_url'],
								'meta'   => array( 'tabindex' => 0 )
							) );
						}
					}
				}
			}
		}
		
		/*if( is_admin() ) {
			$wp_admin_bar->add_menu( array(
				'parent'    => 'wcfm-menu',
				'id' => 'wcfm-menu-settings',
				'title' => '<span class="wcfm-admin-menu"><span class="fa fa-cog"></span>' . __( 'Settings', 'wc-frontend-manager' ) . '</span>',
				'href'  => admin_url( 'admin.php?page=wcfm_settings' ),
				'meta'   => array( 'tabindex' => 0 )
			) );
		} else {
			$wp_admin_bar->add_menu( array(
				'parent'    => 'wcfm-menu',
				'id' => 'wcfm-menu-settings',
				'title' => '<span class="wcfm-admin-menu"><span class="fa fa-cog"></span>' . __( 'Settings', 'wc-frontend-manager' ) . '</span>',
				'href'  => get_wcfm_settings_url(),
				'meta'   => array( 'tabindex' => 0 )
			) );
		}*/
	}
}