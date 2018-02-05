<?php
/**
 * WCFM plugin view
 *
 * WCFM Order Details View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @version   1.0.0
 */

$wcfm_is_allow_orders = apply_filters( 'wcfm_is_allow_orders', true );
if( !$wcfm_is_allow_orders ) {
	wcfm_restriction_message_show( "Orders" );
	return;
}


global $wp, $WCFM, $theorder, $wpdb;

if ( ! is_object( $theorder ) ) {
	if( isset( $wp->query_vars['wcfm-orders-details'] ) && !empty( $wp->query_vars['wcfm-orders-details'] ) ) {
		$order_id = $wp->query_vars['wcfm-orders-details'];
	}
}

if( !$order_id ) return;

if( wcfm_is_vendor() ) {
	$is_order_for_vendor = $WCFM->wcfm_vendor_support->wcfm_is_order_for_vendor( $order_id );
	if( !$is_order_for_vendor ) {
		wcfm_restriction_message_show( "Restricted Order" );
		return;
	}
}

$theorder = wc_get_order( $order_id );

if( !$theorder ) return;

$post = get_post($order_id);
$order = $theorder;

self::init_address_fields();

if ( WC()->payment_gateways() ) {
	$payment_gateways = WC()->payment_gateways->payment_gateways();
} else {
	$payment_gateways = array();
}

$payment_method = ! empty( $order->get_payment_method() ) ? $order->get_payment_method() : '';

$order_type_object = get_post_type_object( $post->post_type );

// Get line items
$line_items          = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
$line_items_fee      = $order->get_items( 'fee' );
$line_items_shipping = $order->get_items( 'shipping' );

if( $is_wcfm_order_details_tax_line_item = apply_filters( 'wcfm_order_details_tax_line_item', true ) ) {
	if ( wc_tax_enabled() ) {
		$order_taxes         = $order->get_taxes();
		$tax_classes         = WC_Tax::get_tax_classes();
		$classes_options     = array();
		$classes_options[''] = __( 'Standard', 'wc-frontend-manager' );
	
		if ( ! empty( $tax_classes ) ) {
			foreach ( $tax_classes as $class ) {
				$classes_options[ sanitize_title( $class ) ] = $class;
			}
		}
	
		// Older orders won't have line taxes so we need to handle them differently :(
		$tax_data = '';
		if ( $line_items ) {
			$check_item = current( $line_items );
			$tax_data   = maybe_unserialize( isset( $check_item['line_tax_data'] ) ? $check_item['line_tax_data'] : '' );
		} elseif ( $line_items_shipping ) {
			$check_item = current( $line_items_shipping );
			$tax_data = maybe_unserialize( isset( $check_item['taxes'] ) ? $check_item['taxes'] : '' );
		} elseif ( $line_items_fee ) {
			$check_item = current( $line_items_fee );
			$tax_data   = maybe_unserialize( isset( $check_item['line_tax_data'] ) ? $check_item['line_tax_data'] : '' );
		}
	
		$legacy_order     = ! empty( $order_taxes ) && empty( $tax_data ) && ! is_array( $tax_data );
		$show_tax_columns = ! $legacy_order || sizeof( $order_taxes ) === 1;
	}
}

$wcfm_generate_csv_url = apply_filters( 'wcfm_generate_csv_url', '', $order_id );

do_action( 'before_wcfm_orders_details' );

?>

<div class="collapse wcfm-collapse" id="wcfm_order_details">

  <div class="wcfm-page-headig">
		<span class="fa fa-cart-arrow-down"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Order Details', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"></div>
		
		<div class="wcfm-container wcfm-top-element-container">
			<h2><?php _e( 'Order #', 'wc-frontend-manager' ); echo $theorder->get_order_number(); ?></h2>
			<span class="order-status order-status-<?php echo sanitize_title( $theorder->get_status() ); ?>"><?php echo ucfirst( sanitize_title( $theorder->get_status() ) ); ?></span>
			
			<?php
			if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
				?>
				<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('post.php?post='.$order_id.'&action=edit'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager' ); ?>"><span class="fa fa-wordpress"></span></a>
				<?php
			}
			?>
			
			<div id="order_quick_actions">
				<?php
				if( $wcfm_is_allow_export_csv = apply_filters( 'wcfm_is_allow_export_csv', true ) ) {
					if( $wcfm_generate_csv_url ) {
						echo '<a class="wcfm_csv_export order_quick_action add_new_wcfm_ele_dashboard" href="'.$wcfm_generate_csv_url.'" data-orderid="' . $order_id . '"><span class="fa fa-file-excel-o text_tip" data-tip="' . esc_attr__( 'CSV Export', 'wc-frontend-manager' ) . '"></span></a>';
					}
				}
				
				if( apply_filters( 'wcfm_is_allow_pdf_invoice', true ) || apply_filters( 'wcfm_is_allow_pdf_packing_slip', true ) ) {
					if( WCFM_Dependencies::wcfmu_plugin_active_check() && WCFM_Dependencies::wcfm_wc_pdf_invoices_packing_slips_plugin_active_check() ) {
						if( apply_filters( 'wcfm_is_allow_pdf_packing_slip', true ) ) {
							echo '<a class="wcfm_pdf_packing_slip order_quick_action add_new_wcfm_ele_dashboard" href="#" data-orderid="' . $order_id . '"><span class="fa fa-file-powerpoint-o text_tip" data-tip="' . esc_attr__( 'PDF Packing Slip', 'wc-frontend-manager' ) . '"></span></a>';
						}
						if( apply_filters( 'wcfm_is_allow_pdf_invoice', true ) ) {
							echo '<a class="wcfm_pdf_invoice order_quick_action add_new_wcfm_ele_dashboard" href="#" data-orderid="' . $order_id . '"><span class="fa fa-file-pdf-o text_tip" data-tip="' . esc_attr__( 'PDF Invoice', 'wc-frontend-manager' ) . '"></span></a>';
						}
					} else {
						if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
							if( wcfm_is_vendor() ) {
								echo '<a class="wcfm_pdf_invoice_vendor_dummy order_quick_action add_new_wcfm_ele_dashboard" href="#" data-orderid="' . $order_id . '"><span class="fa fa-file-pdf-o text_tip" data-tip="' . esc_attr__( 'PDF Invoice', 'wc-frontend-manager' ) . '"></span></a>';
							} else {
								echo '<a class="wcfm_pdf_invoice_dummy order_quick_action add_new_wcfm_ele_dashboard" href="#" data-orderid="' . $order_id . '"><span class="fa fa-file-pdf-o text_tip" data-tip="' . esc_attr__( 'PDF Invoice', 'wc-frontend-manager' ) . '"></span></a>';
							}
						}
					}
				}
				?>
			</div>
			<div class="wcfm-clearfix"></div>
		</div>
	  <div class="wcfm-clearfix"></div><br />
	  
	  <?php do_action( 'begin_wcfm_orders_details' ); ?>
	  
		<div class="wcfm-container">
			<div id="orders_details_general_expander" class="wcfm-content">
	
				<p class="form-field form-field-wide"><label for="order_date"><?php _e( 'Order date:', 'wc-frontend-manager' ) ?></label>
					<?php echo date_i18n( wc_date_format() . ' @' . wc_time_format(), strtotime( $post->post_date ) ); ?>
				</p>
				
				<?php if( $wcfm_is_allow_order_status_update = apply_filters( 'wcfm_is_allow_order_status_update', true ) ) { ?>
					<p class="form-field form-field-wide wc-order-status"><label for="order_status"><?php _e( 'Order status:', 'wc-frontend-manager' ) ?> 
							<?php
							if( $wcfm_is_allow_order_details = apply_filters( 'wcfm_allow_order_details', true ) ) {
								if ( $order->needs_payment() ) {
									printf( '<a target="_blank" href="%s">%s &rarr;</a>',
										esc_url( $order->get_checkout_payment_url() ),
										__( 'Customer payment page', 'wc-frontend-manager' )
									);
								}
							}
						  ?>
						</label>
						<?php
							$order_status = '<select id="wcfm_order_status" name="order_status">';
							$statuses = apply_filters( 'wcfm_allowed_order_status', wc_get_order_statuses() );
							foreach ( $statuses as $status => $status_name ) {
								$order_status .= '<option value="' . esc_attr( $status ) . '" ' . selected( $status, 'wc-' . $order->get_status(), false ) . '>' . esc_html( $status_name ) . '</option>';
							}
							$order_status .= '</select>';
							$order_status .= '<button class="wcfm_modify_order_status button" id="wcfm_modify_order_status" data-orderid="' . $order->get_id() . '">' .  __( 'Update', 'wc-frontend-manager' ) . '</button>';
							echo $order_status;
						?>
					</p>
			  <?php } ?>		
					
		    <?php if( $wcfm_is_allow_order_details = apply_filters( 'wcfm_allow_order_details', true ) ) { ?>
					<p class="form-field form-field-wide wc-customer-user">
						<label for="customer_user"><?php _e( 'Customer:', 'wc-frontend-manager' ) ?> <?php
							if ( $order->get_user_id() ) {
								$args = array( 'post_status' => 'all',
									'post_type'      => 'shop_order',
									'_customer_user' => absint( $order->get_user_id() )
								);
								printf( '<a target="_blank" href="%s">%s &rarr;</a>',
									esc_url( add_query_arg( $args, admin_url( 'edit.php' ) ) ),
									__( 'View other orders', 'wc-frontend-manager' )
								);
							}
						?></label>
						<?php
						$user_string = '';
						$user_id     = '';
						if ( $order->get_user_id() ) {
							$user_id     = absint( $order->get_user_id() );
							$user        = get_user_by( 'id', $user_id );
							$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' - ' . esc_html( $user->user_email ) . ')';
						}
						echo htmlspecialchars( $user_string );
						?>
					</p>
					<?php do_action( 'woocommerce_admin_order_data_after_order_details', $order ); ?>
			
					<p class="order_number">
						<?php
			
							if ( $payment_method ) {
								printf( __( '<label for="order_payment_via">Payment via: </label> %s', 'wc-frontend-manager' ), ( isset( $payment_gateways[ $payment_method ] ) ? esc_html( $payment_gateways[ $payment_method ]->get_title() ) : esc_html( $payment_method ) ) );
				
								if ( $transaction_id = $order->get_transaction_id() ) {
										if ( isset( $payment_gateways[ $payment_method ] ) && ( $url = $payment_gateways[ $payment_method ]->get_transaction_url( $order ) ) ) {
										echo ' (<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $transaction_id ) . '</a>)';
									} else {
										echo ' (' . esc_html( $transaction_id ) . ')';
									}
								}
				
								if ( $order->get_date_paid() ) {
									/* translators: 1: date 2: time */
									printf( ' ' . __( 'on %1$s @ %2$s', 'woocommerce' ), wc_format_datetime( $order->get_date_paid() ), wc_format_datetime( $order->get_date_paid(), get_option( 'time_format' ) ) );
								}
				
								echo '. ';
							}
				
							if ( $ip_address = $order->get_customer_ip_address() ) {
								echo __( 'Customer IP', 'wc-frontend-manager' ) . ': <span class="woocommerce-Order-customerIP">' . esc_html( $ip_address ) . '</span>';
							}
						?>
					</p>
				<?php } ?>
				
				<table>
					<thead>
						<tr>
							<th>
								<?php _e( 'Billing Details', 'wc-frontend-manager' ); ?>
							</th>
							<th>
								<?php _e( 'Shipping Details', 'wc-frontend-manager' ); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<?php
									// Display values
									echo '<div class="address">';
									
									if( apply_filters( 'wcfm_allow_customer_billing_details', true ) ) {
										if ( $order->get_formatted_billing_address() ) {
											echo '<p><strong>' . __( 'Address', 'wc-frontend-manager' ) . ':</strong>' . wp_kses( $order->get_formatted_billing_address(), array( 'br' => array() ) ) . '</p>';
										} else {
											echo '<p class="none_set"><strong>' . __( 'Address', 'wc-frontend-manager' ) . ':</strong> ' . __( 'No billing address set.', 'wc-frontend-manager' ) . '</p>';
										}
									}
		
									if( apply_filters( 'wcfm_allow_order_customer_details', true ) ) {
										foreach ( self::$billing_fields as $key => $field ) {
											if ( isset( $field['show'] ) && false === $field['show'] ) {
												continue;
											}
			
											$field_name = 'billing_' . $key;
			
											if ( is_callable( array( $order, 'get_' . $field_name ) ) ) {
												$field_value = $order->{"get_$field_name"}( 'edit' );
											} else {
												$field_value = $order->get_meta( '_' . $field_name );
											}
		
											echo '<p><strong>' . esc_html( $field['label'] ) . ':</strong> ' . make_clickable( esc_html( $field_value ) ) . '</p>';
										}
									}
		
									echo '</div>';
		
									do_action( 'woocommerce_admin_order_data_after_billing_address', $order );
									?>
							</td>
							<td>
								<?php
									// Display values
									echo '<div class="address">';
									
										if( $is_allow_customer_shipping_details = apply_filters( 'wcfm_allow_customer_shipping_details', true ) ) {
											if ( $order->get_formatted_shipping_address() ) {
												echo '<p><strong>' . __( 'Address', 'wc-frontend-manager' ) . ':</strong>' . wp_kses( $order->get_formatted_shipping_address(), array( 'br' => array() ) ) . '</p>';
											} else {
												echo '<p class="none_set"><strong>' . __( 'Address', 'wc-frontend-manager' ) . ':</strong> ' . __( 'No shipping address set.', 'wc-frontend-manager' ) . '</p>';
											}
										}
		
										if( $is_allow_order_customer_details = apply_filters( 'wcfm_allow_order_customer_details', true ) ) {
											if ( ! empty( self::$shipping_fields ) ) {
												foreach ( self::$shipping_fields as $key => $field ) {
													if ( isset( $field['show'] ) && false === $field['show'] ) {
														continue;
													}
			
													$field_name = 'shipping_' . $key;
			
													if ( is_callable( array( $order, 'get_' . $field_name ) ) ) {
														$field_value = $order->{"get_$field_name"}( 'edit' );
													} else {
														$field_value = $order->get_meta( '_' . $field_name );
													}
			
													echo '<p><strong>' . esc_html( $field['label'] ) . ':</strong> ' . make_clickable( esc_html( $field_value ) ) . '</p>';
												}
											}
										}
		
										if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' == get_option( 'woocommerce_enable_order_comments', 'yes' ) ) && $post->post_excerpt ) {
											echo '<p><strong>' . __( 'Customer Provided Note', 'wc-frontend-manager' ) . ':</strong> ' . nl2br( esc_html( $post->post_excerpt ) ) . '</p>';
										}
		
									echo '</div>';
									?>
							</td>
						</tbody>
					</table>
			</div>
		</div>
		<!-- end collapsible -->
		
		<div class="wcfm-clearfix"></div><br />
		<!-- collapsible -->
		<div class="page_collapsible orders_details_items" id="wcfm_orders_items_options"><?php _e('Order Items', 'wc-frontend-manager'); ?><span></span></div>
		<div class="wcfm-container">
			<div id="orders_details_items_expander" class="wcfm-content">
				<table cellpadding="0" cellspacing="0" class="woocommerce_order_items">
					<thead>
						<tr>
							<th class="item-thumb no_mob" data-sort="string-ins"></th>
							<th class="item sortable" data-sort="string-ins"><?php _e( 'Item', 'wc-frontend-manager' ); ?></th>
							<?php do_action( 'woocommerce_admin_order_item_headers', $order ); ?>
							<th class="item_cost sortable no_mob" data-sort="float"><?php _e( 'Cost', 'wc-frontend-manager' ); ?></th>
							<th class="item_quantity sortable no_mob" data-sort="int"><?php _e( 'Qty', 'wc-frontend-manager' ); ?></th>
							<?php if( $is_wcfm_order_details_line_total_head = apply_filters( 'wcfm_order_details_line_total_head', true ) ) { ?>
								<th class="line_cost sortable" data-sort="float"><?php _e( 'Total', 'wc-frontend-manager' ); ?></th>
							<?php } ?>
							<?php do_action( 'wcfm_order_details_after_line_total_head', $order ); ?>
							<?php if( $is_wcfm_order_details_tax_line_item = apply_filters( 'wcfm_order_details_tax_line_item', true ) ) { ?>
								<?php
									if ( empty( $legacy_order ) && ! empty( $order_taxes ) ) :
										foreach ( $order_taxes as $tax_id => $tax_item ) :
											$tax_class      = wc_get_tax_class_by_tax_id( $tax_item['rate_id'] );
											$tax_class_name = isset( $classes_options[ $tax_class ] ) ? $classes_options[ $tax_class ] : __( 'Tax', 'wc-frontend-manager' );
											$column_label   = ! empty( $tax_item['label'] ) ? $tax_item['label'] : __( 'Tax', 'wc-frontend-manager' );
											$column_tip     = $tax_item['name'] . ' (' . $tax_class_name . ')';
											?>
											<th class="line_tax text_tip no_ipad no_mob" data-tip="<?php echo esc_attr( $column_tip ); ?>">
												<?php echo esc_attr( $column_label ); ?>
												<input type="hidden" class="order-tax-id" name="order_taxes[<?php echo $tax_id; ?>]" value="<?php echo esc_attr( $tax_item['rate_id'] ); ?>">
												<a class="delete-order-tax" href="#" data-rate_id="<?php echo $tax_id; ?>"></a>
											</th>
											<?php
										endforeach;
									endif;
								?>
							<?php } ?>
						</tr>
					</thead>
					<tbody id="order_line_items">
					<?php
						$line_items = apply_filters( 'wcfm_valid_line_items', $line_items, $order->get_id() );
						foreach ( $line_items as $item_id => $item ) {
							$_product  = $item->get_product();
			
							do_action( 'woocommerce_before_order_item_' . $item->get_type() . '_html', $item_id, $item, $order );
							
							if( current_user_can( 'edit_published_products' ) && apply_filters( 'wcfm_is_allow_edit_products', true ) ) {
								$product_link  = $_product ? get_wcfm_edit_product_url( $item->get_product_id(), $_product ) : '';
							} else {
								$product_link  = $_product ? get_permalink( $item->get_product_id() ) : '';
							}
							$thumbnail     = $_product ? apply_filters( 'woocommerce_admin_order_item_thumbnail', $_product->get_image( 'thumbnail', array( 'title' => '' ), false ), $item_id, $item ) : '';
							$tax_data = $item->get_taxes();
							?>
							<tr class="item <?php echo apply_filters( 'woocommerce_admin_html_order_item_class', ( ! empty( $class ) ? $class : '' ), $item, $order ); ?>" data-order_item_id="<?php echo $item_id; ?>">
								<td class="thumb no_mob">
									<?php
										echo '<div class="wc-order-item-thumbnail no_ipad">' . wp_kses_post( $thumbnail ) . '</div>';
									?>
								</td>
								<td class="name" data-sort-value="<?php echo esc_attr( $item->get_name() ); ?>">
									<?php
										echo $product_link ? '<a href="' . esc_url( $product_link ) . '" class="wc-order-item-name">' .  esc_html( $item->get_name() ) . '</a>' : '<div class="class="wc-order-item-name"">' . esc_html( $item->get_name() ) . '</div>';
							
										if ( $_product && $_product->get_sku() ) {
											echo '<div class="wc-order-item-sku"><strong>' . __( 'SKU:', 'wc-frontend-manager' ) . '</strong> ' . esc_html( $_product->get_sku() ) . '</div>';
										}
							
										if ( ! empty( $item->get_variation_id() ) ) {
											echo '<div class="wc-order-item-variation"><strong>' . __( 'Variation ID:', 'wc-frontend-manager' ) . '</strong> ';
											if ( ! empty( $item->get_variation_id() ) && 'product_variation' === get_post_type( $item->get_variation_id() ) ) {
												echo esc_html( $item->get_variation_id() );
											} elseif ( ! empty( $item->get_variation_id() ) ) {
												echo esc_html( $item->get_variation_id() ) . ' (' . __( 'No longer exists', 'wc-frontend-manager' ) . ')';
											}
											echo '</div>';
										}
									?>
							
									<?php do_action( 'woocommerce_before_order_itemmeta', $item_id, $item, $_product ) ?>
									
									<div class="view">
										<?php
											global $wpdb;
									
											if ( $metadata = $item->get_formatted_meta_data( '' ) ) {
												echo '<table cellspacing="0" class="display_meta">';
												foreach ( $metadata as $meta_id => $meta ) {
									
													// Skip hidden core fields
													if ( in_array( $meta->key, apply_filters( 'woocommerce_hidden_order_itemmeta', array(
														'_qty',
														'_tax_class',
														'_product_id',
														'_variation_id',
														'_line_subtotal',
														'_line_subtotal_tax',
														'_line_total',
														'_line_tax',
														'method_id',
														'_vendor_id',
														'vendor_id',
														'_fulfillment_status',
														'_commission_status',
														'cost',
														'pickup_hidden_datetime',
														'return_hidden_datetime',
														'return_hidden_days',
														'redq_google_cal_sync_id'
													) ) ) ) {
														continue;
													}
									
													// Skip serialised meta
													if ( is_serialized( $meta->display_key ) ) {
														continue;
													}
									
													echo '<tr><th>' . wp_kses_post( rawurldecode( $meta->display_key ) ) . ':</th><td>' . wp_kses_post( wpautop( make_clickable( rawurldecode( $meta->value ) ) ) ) . '</td></tr>';
												}
												echo '</table>';
											}
										?>
									</div>
									
									<?php do_action( 'woocommerce_after_order_itemmeta', $item_id, $item, $_product ) ?>
								</td>
							
								<?php do_action( 'woocommerce_admin_order_item_values', $_product, $item, absint( $item_id ) ); ?>
							
								<td class="item_cost no_mob" width="1%" data-sort-value="<?php echo esc_attr( $order->get_item_subtotal( $item, false, true ) ); ?>">
									<div class="view">
										<?php
											if ( $item->get_total() ) {
												echo wc_price( $order->get_item_total( $item, false, true ), array( 'currency' => $order->get_currency() ) );
							
												if ( $item->get_subtotal() != $item->get_total() ) {
													echo '<span class="wc-order-item-discount">-' . wc_price( wc_format_decimal( $order->get_item_subtotal( $item, false, false ) - $order->get_item_total( $item, false, false ), '' ), array( 'currency' => $order->get_currency() ) ) . '</span>';
												}
											}
										?>
									</div>
								</td>
								<td class="no_mob" width="1%">
									<div class="view">
										<?php
											echo '<small class="times">&times;</small> ' . ( $item->get_quantity() ? esc_html( $item->get_quantity() ) : '1' );
							
											if ( $refunded_qty = $order->get_qty_refunded_for_item( $item_id ) ) {
												echo '<small class="refunded">' . ( $refunded_qty * -1 ) . '</small>';
											}
										?>
									</div>
								</td>
								
								<?php if( $is_wcfm_order_details_line_total = apply_filters( 'wcfm_order_details_line_total', true ) ) { ?>
									<td class="line_cost" width="1%" data-sort-value="<?php echo esc_attr( ( $item->get_total() ) ? $item->get_total() : '' ); ?>">
										<div class="view">
											<?php
												if ( $item->get_total() ) {
													echo wc_price( $item->get_total(), array( 'currency' => $order->get_currency() ) );
												}
								
												if ( $item->get_subtotal() !== $item->get_total() ) {
													echo '<span class="wc-order-item-discount">-' . wc_price( wc_format_decimal( $item->get_subtotal() - $item->get_total(), '' ), array( 'currency' => $order->get_currency() ) ) . '</span>';
												}
								
												if ( $refunded = $order->get_total_refunded_for_item( $item_id ) ) {
													echo '<small class="refunded">' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
												}
											?>
										</div>
									</td>
								<?php } ?>
								<?php do_action( 'wcfm_after_order_details_line_total', $item, $order ); ?>
							
								<?php if( $is_wcfm_order_details_tax_line_item = apply_filters( 'wcfm_order_details_tax_line_item', true ) ) { ?>
									<?php
									if ( wc_tax_enabled() ) {
											if ( ! empty( $tax_data ) ) {
												foreach ( $order_taxes as $tax_item ) {
													$tax_item_id       = $tax_item['rate_id'];
													$tax_item_total    = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : '';
													$tax_item_subtotal = isset( $tax_data['subtotal'][ $tax_item_id ] ) ? $tax_data['subtotal'][ $tax_item_id ] : '';
													?>
													<td class="line_tax no_ipad no_mob" width="1%">
														<div class="view">
															<?php
																if ( '' != $tax_item_total ) {
																	echo wc_price( wc_round_tax_total( $tax_item_total ), array( 'currency' => $order->get_currency() ) );
																} else {
																	echo '&ndash;';
																}
									
																if ( $item->get_subtotal() !== $item->get_total() ) {
																	echo '<span class="wc-order-item-discount">-' . wc_price( wc_round_tax_total( $tax_item_subtotal - $tax_item_total ), array( 'currency' => $order->get_currency() ) ) . '</span>';
																}
									
																if ( $refunded = $order->get_tax_refunded_for_item( $item_id, $tax_item_id ) ) {
																	echo '<small class="refunded">' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
																}
															?>
														</div>
													</td>
													<?php
												}
											}
										}
									?>
								<?php } ?>
							
							</tr>
	
							<?php
			
							do_action( 'woocommerce_order_item_' . $item->get_type() . '_html', $item_id, $item, $order );
						}
						do_action( 'woocommerce_admin_order_items_after_line_items', $order->get_id() );
					?>
					</tbody>
					
					<?php if( $is_wcfm_order_details_shipping_line_item = apply_filters( 'wcfm_order_details_shipping_line_item', true ) ) { ?>
					<tbody id="order_shipping_line_items">
					<?php
						$shipping_methods = WC()->shipping() ? WC()->shipping->load_shipping_methods() : array();
						foreach ( $line_items_shipping as $item_id => $item ) {
							?>
							<tr class="shipping <?php echo ( ! empty( $class ) ) ? $class : ''; ?>" data-order_item_id="<?php echo $item_id; ?>">
								<td class="thumb no_ipad no_mob"><span class="fa fa-truck"></span></td>
							
								<td class="name">
									<div class="view">
										<?php echo ! empty( $item->get_name() ) ? wc_clean( $item->get_name() ) : __( 'Shipping', 'wc-frontend-manager' ); ?>
									</div>
							
									<?php do_action( 'woocommerce_before_order_itemmeta', $item_id, $item, null ) ?>
									<div class="view">
										<?php
											global $wpdb;
									
											if ( $metadata = $item->get_formatted_meta_data( '' ) ) {
												echo '<table cellspacing="0" class="display_meta">';
												foreach ( $metadata as $meta_id => $meta ) {
									
													// Skip hidden core fields
													if ( in_array( $meta->key, apply_filters( 'woocommerce_hidden_order_itemmeta', array(
														'_qty',
														'_tax_class',
														'_product_id',
														'_variation_id',
														'_line_subtotal',
														'_line_subtotal_tax',
														'_line_total',
														'_line_tax',
														'method_id',
														'_fulfillment_status',
														'_commission_status',
														'cost',
														'pickup_hidden_datetime',
														'return_hidden_datetime',
														'return_hidden_days',
														'redq_google_cal_sync_id'
													) ) ) ) {
														continue;
													}
									
													// Skip serialised meta
													if ( is_serialized( $meta->display_key ) ) {
														continue;
													}
									
													echo '<tr><th>' . wp_kses_post( rawurldecode( $meta->display_key ) ) . ':</th><td>' . wp_kses_post( wpautop( make_clickable( rawurldecode( $meta->value ) ) ) ) . '</td></tr>';
												}
												echo '</table>';
											}
										?>
									</div>
									<?php do_action( 'woocommerce_after_order_itemmeta', $item_id, $item, null ) ?>
								</td>
							
								<?php do_action( 'woocommerce_admin_order_item_values', null, $item, absint( $item_id ) ); ?>
							
								<td class="item_cost no_mob" width="1%">&nbsp;</td>
								<td class="quantity no_mob" width="1%">&nbsp;</td>
							
								<td class="line_cost" width="1%">
									<div class="view">
										<?php
											echo ( isset( $item['cost'] ) ) ? wc_price( wc_round_tax_total( $item['cost'] ), array( 'currency' => $order->get_currency() ) ) : '';
							
											if ( $refunded = $order->get_total_refunded_for_item( $item_id, 'shipping' ) ) {
												echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
											}
										?>
									</div>
								</td>
							
								<?php if( $is_wcfm_order_details_tax_line_item = apply_filters( 'wcfm_order_details_tax_line_item', true ) ) { ?>
									<?php
										if ( ( $tax_data = $item->get_taxes() ) && wc_tax_enabled() ) {
											foreach ( $order_taxes as $tax_item ) {
												$tax_item_id    = $tax_item->get_rate_id();
												$tax_item_total = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : '';
												?>
													<td class="line_tax no_ipad no_mob" width="1%">
														<div class="view">
															<?php
																echo ( '' != $tax_item_total ) ? wc_price( wc_round_tax_total( $tax_item_total ), array( 'currency' => $order->get_currency() ) ) : '&ndash;';
								
																if ( $refunded = $order->get_tax_refunded_for_item( $item_id, $tax_item_id, 'shipping' ) ) {
																	echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
																}
															?>
														</div>
													</td>
								
												<?php
											}
										}
									?>
								<?php } ?>
							
							</tr>
							<?php
						}
						do_action( 'woocommerce_admin_order_items_after_shipping', $order->get_id() );
					?>
					</tbody>
					<?php } ?>
					
					<?php if( $is_wcfm_order_details_fee_line_item = apply_filters( 'wcfm_order_details_fee_line_item', true ) ) { ?>
					<tbody id="order_fee_line_items">
					<?php
						foreach ( $line_items_fee as $item_id => $item ) {
							?>
							<tr class="fee <?php echo ( ! empty( $class ) ) ? $class : ''; ?>" data-order_item_id="<?php echo $item_id; ?>">
								<td class="thumb no_ipad no_mob"><span class="fa fa-plus-circle"></span></td>
							
								<td class="name">
									<div class="view">
										<?php echo ! empty( $item->get_name() ) ? esc_html( $item->get_name() ) : __( 'Fee', 'wc-frontend-manager' ); ?>
									</div>
								</td>
							
								<?php do_action( 'woocommerce_admin_order_item_values', null, $item, absint( $item_id ) ); ?>
							
								<td class="item_cost no_mob" width="1%">&nbsp;</td>
								<td class="quantity no_mob" width="1%">&nbsp;</td>
							
								<td class="line_cost" width="1%">
									<div class="view">
										<?php
											echo ( $item->get_total() ) ? wc_price( wc_round_tax_total( $item->get_total() ), array( 'currency' => $order->get_currency() ) ) : '';
							
											if ( $refunded = $order->get_total_refunded_for_item( $item_id, 'fee' ) ) {
												echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
											}
										?>
									</div>
								</td>
							
								<?php if( $is_wcfm_order_details_tax_line_item = apply_filters( 'wcfm_order_details_tax_line_item', true ) ) { ?>
									<?php
										if ( empty( $legacy_order ) && wc_tax_enabled() ) :
											$line_tax_data = isset( $item['line_tax_data'] ) ? $item['line_tax_data'] : '';
											$tax_data      = maybe_unserialize( $line_tax_data );
								
											foreach ( $order_taxes as $tax_item ) :
												$tax_item_id       = $tax_item['rate_id'];
												$tax_item_total    = isset( $tax_data['total'][ $tax_item_id ] ) ? $tax_data['total'][ $tax_item_id ] : '';
												?>
													<td class="line_tax no_ipad no_mob" width="1%">
														<div class="view">
															<?php
																echo ( '' != $tax_item_total ) ? wc_price( wc_round_tax_total( $tax_item_total ), array( 'currency' => $order->get_currency() ) ) : '&ndash;';
								
																if ( $refunded = $order->get_tax_refunded_for_item( $item_id, $tax_item_id, 'fee' ) ) {
																	echo '<small class="refunded">-' . wc_price( $refunded, array( 'currency' => $order->get_currency() ) ) . '</small>';
																}
															?>
														</div>
													</td>
								
												<?php
											endforeach;
										endif;
									?>
								<?php } ?>
							
							</tr>
							<?php
						}
						do_action( 'woocommerce_admin_order_items_after_fees', $order->get_id() );
					?>
					</tbody>
					<?php } ?>
					
					<?php if( $is_wcfm_order_details_refund_line_item = apply_filters( 'wcfm_order_details_refund_line_item', true ) ) { ?>
					<tbody id="order_refunds">
					<?php
						if ( $refunds = $order->get_refunds() ) {
							foreach ( $refunds as $refund ) {
							/**
							 * @var object $refund The refund object.
							 */
							$who_refunded = new WP_User( $refund->get_refunded_by() );
							?>
							<tr class="refund <?php echo ( ! empty( $class ) ) ? $class : ''; ?>" data-order_refund_id="<?php echo $refund->get_id(); ?>">
								<td class="thumb no_ipad no_mob"><span class="wcicon-status-refunded"></span></td>
							
								<td class="name">
									<?php
										/* translators: 1: refund id 2: date */
										printf( __( 'Refund #%1$s - %2$s', 'woocommerce' ), $refund->get_id(), wc_format_datetime( $order->get_date_created(), get_option( 'date_format' ) . ', ' . get_option( 'time_format' ) ) );
							
										if ( $who_refunded->exists() ) {
											echo ' ' . esc_attr_x( 'by', 'Ex: Refund - $date >by< $username', 'woocommerce' ) . ' ' . '<abbr class="refund_by" title="' . sprintf( esc_attr__( 'ID: %d', 'woocommerce' ), absint( $who_refunded->ID ) ) . '">' . esc_attr( $who_refunded->display_name ) . '</abbr>' ;
										}
									?>
									<?php if ( $refund->get_reason() ) : ?>
										<p class="description"><?php echo esc_html( $refund->get_reason() ); ?></p>
									<?php endif; ?>
									<input type="hidden" class="order_refund_id" name="order_refund_id[]" value="<?php echo esc_attr( $refund->get_id() ); ?>" />
								</td>
							
								<?php do_action( 'woocommerce_admin_order_item_values', null, $refund, $refund->get_id() ); ?>
							
								<td class="item_cost no_mob" width="1%">&nbsp;</td>
								<td class="quantity no_mob" width="1%">&nbsp;</td>
							
								<td class="line_cost" width="1%">
									<div class="view">
										<?php echo wc_price( '-' . $refund->get_amount() ); ?>
									</div>
								</td>
							
								<?php if ( wc_tax_enabled() ) : $total_taxes = count( $order_taxes ); ?>
									<?php for ( $i = 0;  $i < $total_taxes; $i++ ) : ?>
										<td class="line_tax no_ipad no_mob" width="1%"></td>
									<?php endfor; ?>
								<?php endif; ?>
								<?php
							}
							do_action( 'woocommerce_admin_order_items_after_refunds', $order->get_id() );
						}
					?>
					</tbody>
					<?php } ?>
				</table>
				
				<div class="wc-order-data-row wc-order-totals-items wc-order-items-editable">
					<?php if( $is_wcfm_order_details_coupon_line_item = apply_filters( 'wcfm_order_details_coupon_line_item', true ) ) { ?>
						<?php
							$coupons = $order->get_items( array( 'coupon' ) );
							if ( $coupons ) {
								?>
								<div class="wc-used-coupons">
									<ul class="wc_coupon_list"><?php
										echo '<li><strong>' . __( 'Coupon(s) Used', 'wc-frontend-manager' ) . '</strong></li>';
										foreach ( $coupons as $item_id => $item ) {
											$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish' LIMIT 1;", $item->get_name() ) );
					
											$link = $post_id ? add_query_arg( array( 'post' => $post_id, 'action' => 'edit' ), admin_url( 'post.php' ) ) : add_query_arg( array( 's' => $item->get_name(), 'post_status' => 'all', 'post_type' => 'shop_coupon' ), admin_url( 'edit.php' ) );
					
											echo '<li class="code"><a href="' . esc_url( $link ) . '" class="img_tip" data-tip="' . esc_attr( wc_price( $item['discount_amount'], array( 'currency' => $order->get_currency() ) ) ) . '"><span>' . esc_html( $item->get_name() ). '</span></a></li>';
										}
									?></ul>
								</div>
								<?php
							}
						?>
					<?php } ?>
					<table class="wc-order-totals">
						<?php if( $is_wcfm_order_details_coupon_line_item = apply_filters( 'wcfm_order_details_coupon_line_item', true ) ) { ?>
							<tr>
								<td class="label"><span class="fa fa-question-circle-o no_mob img_tip" data-tip="<?php _e( 'This is the total discount. Discounts are defined per line item.', 'wc-frontend-manager' ) ; ?>"></span> <?php _e( 'Discount', 'wc-frontend-manager' ); ?>:</td>
								<td width="1%"></td>
								<td class="total">
									<?php echo wc_price( $order->get_total_discount(), array( 'currency' => $order->get_currency() ) ); ?>
								</td>
							</tr>
						<?php } ?>
				
						<?php do_action( 'woocommerce_admin_order_totals_after_discount', $order->get_id() ); ?>
				
						<?php if( $is_wcfm_order_details_shipping_line_item = apply_filters( 'wcfm_order_details_shipping_line_item', true ) ) { ?>
							<tr>
								<td class="label"><span class="fa fa-question-circle-o no_mob img_tip" data-tip="<?php _e( 'This is the shipping and handling total costs for the order.', 'wc-frontend-manager' ) ; ?>"></span> <?php _e( 'Shipping', 'wc-frontend-manager' ); ?>:</td>
								<td width="1%"></td>
								<td class="total"><?php
									if ( ( $refunded = $order->get_total_shipping_refunded() ) > 0 ) {
										echo '<del>' . strip_tags( wc_price( $order->get_total_shipping(), array( 'currency' => $order->get_currency() ) ) ) . '</del> <ins>' . wc_price( $order->get_total_shipping() - $refunded, array( 'currency' => $order->get_currency() ) ) . '</ins>';
									} else {
										echo wc_price( $order->get_total_shipping(), array( 'currency' => $order->get_currency() ) );
									}
								?></td>
							</tr>
						<?php } ?>
				
						<?php do_action( 'woocommerce_admin_order_totals_after_shipping', $order->get_id() ); ?>
				
						<?php if( $is_wcfm_order_details_tax_total = apply_filters( 'wcfm_order_details_tax_total', true ) ) { ?>
							<?php if ( wc_tax_enabled() ) : ?>
								<?php foreach ( $order->get_tax_totals() as $code => $tax ) : ?>
									<tr>
										<td class="label"><?php echo $tax->label; ?>:</td>
										<td width="1%"></td>
										<td class="total"><?php
											if ( ( $refunded = $order->get_total_tax_refunded_by_rate_id( $tax->rate_id ) ) > 0 ) {
												echo '<del>' . strip_tags( $tax->formatted_amount ) . '</del> <ins>' . wc_price( WC_Tax::round( $tax->amount, wc_get_price_decimals() ) - WC_Tax::round( $refunded, wc_get_price_decimals() ), array( 'currency' => $order->get_currency() ) ) . '</ins>';
											} else {
												echo $tax->formatted_amount;
											}
										?></td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php } ?>
				
						<?php do_action( 'woocommerce_admin_order_totals_after_tax', $order->get_id() ); ?>
				
						<?php if( $is_wcfm_order_details_total = apply_filters( 'wcfm_order_details_total', true ) ) { ?>
						<tr>
							<td class="label"><?php _e( 'Order Total', 'wc-frontend-manager' ); ?>:</td>
							<td>
								
							</td>
							<td class="total">
								<div class="view"><?php echo $order->get_formatted_order_total(); ?></div>
							</td>
						</tr>
						<?php } ?>
				
						<?php do_action( 'wcfm_order_totals_after_total', $order->get_id() ); ?>
				
						<?php if( $is_wcfm_order_details_refund_line_item = apply_filters( 'wcfm_order_details_refund_line_item', true ) ) { ?>
							<?php if ( $order->get_total_refunded() ) : ?>
								<tr>
									<td class="label refunded-total"><?php _e( 'Refunded', 'wc-frontend-manager' ); ?>:</td>
									<td width="1%"></td>
									<td class="total refunded-total">-<?php echo wc_price( $order->get_total_refunded(), array( 'currency' => $order->get_currency() ) ); ?></td>
								</tr>
							<?php endif; ?>
						<?php } ?>
				
						<?php 
						//do_action( 'woocommerce_admin_order_totals_after_refunded', $order->get_id() ); 
						?>
				
					</table>
					<div class="wcfm-clearfix"></div>
				</div>
			</div>
		</div>
		<!-- end collapsible -->
		<?php do_action( 'end_wcfm_orders_details' ); ?>
	</div>
</div>


<?php
do_action( 'after_wcfm_orders_details' );
?>