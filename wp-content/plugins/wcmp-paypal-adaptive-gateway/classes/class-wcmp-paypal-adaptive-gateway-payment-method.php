<?php

class WCMP_Paypal_Adaptive_Gateway_Payment_Method extends WC_Payment_Gateway {

    public function __construct() {
        global $WCMP_Paypal_Adaptive_Gateway;
        $this->id = 'wcmp-paypal-adaptive-payments';
        $this->icon = $WCMP_Paypal_Adaptive_Gateway->plugin_url . 'assets/images/paypal.png';
        $this->has_fields = false;
        $this->method_title = __('PayPal Adaptive Payments (WCMp Compatible)', 'wcmp-paypal-adaptive-gateway');
        $this->order_button_text = __('Proceed to PayPal', 'wcmp-paypal-adaptive-gateway');

        $this->api_prod_url = 'https://svcs.paypal.com/AdaptivePayments/';
        $this->api_sandbox_url = 'https://svcs.sandbox.paypal.com/AdaptivePayments/';
        $this->payment_prod_url = 'https://www.paypal.com/cgi-bin/webscr';
        $this->payment_sandbox_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        $this->notify_url = WC()->api_request_url('WCMp_PayPal_Adaptive_Payments_Gateway');

        $this->init_form_fields();

        $this->init_settings();

        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->api_username = $this->get_option('api_username');
        $this->api_password = $this->get_option('api_password');
        $this->api_signature = $this->get_option('api_signature');
        $this->app_id = $this->get_option('app_id');
        $this->receiver_email = $this->get_option('receiver_email');
        $this->method = $this->get_option('method');
        $this->sandbox = $this->get_option('sandbox');
        $this->debug = $this->get_option('debug');

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_api_wcmp_paypal_adaptive_payments_gateway', array($this, 'paypal_adaptive_ipn_response'));
        add_action('wcmp_paypal_adaptive_payments_ipn', array(&$this, 'handel_paypal_adaptive_ipn'));

        $this->admin_notices();
    }

    /**
     * Process the payment and return the result.
     *
     * @param  int $order_id
     *
     * @return array
     */
    public function process_payment($order_id) {
        global $WCMp;
        $order = wc_get_order($order_id);
        if (WCMP_Paypal_Adaptive_Gateway_Dependencies::wcmp_active_check()) {
            require_once ( $WCMp->plugin_path . 'classes/class-wcmp-calculate-commission.php' );
            $commission_obj = new WCMp_Calculate_Commission();
            $commission_obj->wcmp_process_commissions($order_id);
        }
        $payment_data = $this->get_payment_data($order);
        if ($payment_data['success']) {
            if ('yes' == $this->sandbox) {
                $url = $this->payment_sandbox_url;
            } else {
                $url = $this->payment_prod_url;
            }
            return array(
                'result' => 'success',
                'redirect' => esc_url_raw(add_query_arg(array('cmd' => '_ap-payment', 'paykey' => $payment_data['key']), $url))
            );
        } else {
            wc_add_notice($payment_data['message'], 'error');
            $this->delete_associated_commission($order_id);
            return array(
                'result' => 'fail',
                'redirect' => ''
            );
        }
    }

    /**
     * Get the payment data.
     *
     * @param  WC_Order $order Order data.
     *
     * @return array
     */
    protected function get_payment_data($order) {
        $error_message = __('An error has occurred while processing your payment, please try again. Or contact us for assistance.', 'wcmp-paypal-adaptive-gateway');
        $data = $this->generate_payment_args($order);

        // Sets the post params.
        $params = array(
            'body' => json_encode($data),
            'timeout' => 60,
            'httpversion' => '1.1',
            'headers' => array(
                'X-PAYPAL-SECURITY-USERID' => $this->api_username,
                'X-PAYPAL-SECURITY-PASSWORD' => $this->api_password,
                'X-PAYPAL-SECURITY-SIGNATURE' => $this->api_signature,
                'X-PAYPAL-REQUEST-DATA-FORMAT' => 'JSON',
                'X-PAYPAL-RESPONSE-DATA-FORMAT' => 'JSON',
                'X-PAYPAL-APPLICATION-ID' => $this->app_id,
            )
        );

        if ('yes' == $this->sandbox) {
            $url = $this->api_sandbox_url;
        } else {
            $url = $this->api_prod_url;
        }

        if ('yes' == $this->debug) {
            doPaypalAdaptiveLog('Requesting payment key for order ' . $order->get_order_number() . ' with the following data: ' . print_r($data, true));
        }

        $response = wp_safe_remote_post($url . 'Pay', $params);

        if (is_wp_error($response)) {
            if ('yes' == $this->debug) {
                doPaypalAdaptiveLog('WP_Error in generate payment key: ' . $response->get_error_message());
            }
            $this->delete_associated_commission($order->get_id());
        } else if (200 == $response['response']['code'] && 'OK' == $response['response']['message']) {
            $body = json_decode($response['body'], true);

            if (isset($body['payKey'])) {
                $pay_key = esc_attr($body['payKey']);

                if ('yes' == $this->debug) {
                    doPaypalAdaptiveLog('Payment key successfully created! The key is: ' . $pay_key);
                }

                // Just set the payment options.
                $this->set_payment_options($pay_key);

                return array(
                    'success' => true,
                    'message' => '',
                    'key' => $pay_key
                );
            }

            if (isset($body['error'])) {
                if ('yes' == $this->debug) {
                    doPaypalAdaptiveLog('Failed to generate the payment key: ' . print_r($body, true));
                }

                foreach ($body['error'] as $error) {
                    if ('579042' == $error['errorId']) {
                        $error_message = sprintf(__('Your order has expired, please %s to try again.', 'wcmp-paypal-adaptive-gateway'), '<a href="' . esc_url($order->get_cancel_order_url()) . '">' . __('click here', 'wcmp-paypal-adaptive-gateway') . '</a>');
                        break;
                    } else if (isset($error['message'])) {
                        $order->add_order_note(sprintf(__('PayPal Adaptive Payments error: %s', 'wcmp-paypal-adaptive-gateway'), esc_html($error['message'])));
                    }
                }
                $this->delete_associated_commission($order->get_id());
            }
        } else {
            if ('yes' == $this->debug) {
                doPaypalAdaptiveLog('Error in generate payment key: ' . print_r($response, true));
            }
            $this->delete_associated_commission($order->get_id());
        }

        return array(
            'success' => false,
            'message' => $error_message,
            'key' => ''
        );
    }

    /**
     * Generate payment arguments for PayPal.
     *
     * @param  WC_Order $order Order data.
     *
     * @return array           PayPal payment arguments.
     */
    protected function generate_payment_args($order) {
        $args = array(
            'actionType' => 'CREATE',
            'currencyCode' => get_woocommerce_currency(),
            'trackingId' => $order->id,
            'returnUrl' => str_replace('&amp;', '&', $this->get_return_url($order)),
            'cancelUrl' => str_replace('&amp;', '&', $order->get_cancel_order_url()),
            'ipnNotificationUrl' => $this->notify_url,
            'requestEnvelope' => array(
                'errorLanguage' => 'en_US',
                'detailLevel' => 'ReturnAll'
            )
        );

        $receivers = array();
        $total_vendor_commission = 0;
        if (WCMP_Paypal_Adaptive_Gateway_Dependencies::wcmp_active_check()) {
            $vendor_term_ids = get_vendor_from_an_order($order);
            if ($vendor_term_ids && is_array($vendor_term_ids)) {
                foreach ($vendor_term_ids as $vendor_term_id) {
                    $vendor = get_wcmp_vendor_by_term($vendor_term_id);
                    $vendor_payment_method = get_user_meta($vendor->id, '_vendor_payment_mode', true);
                    $vendor_paypal_email = get_user_meta($vendor->id, '_vendor_paypal_email', true);
                    if ($vendor_payment_method == 'paypal_adaptive' && $vendor_paypal_email && apply_filters('is_wcmp_vendor_receive_paypal_adaptive', true, $vendor)) {
                        $vendor_order_amount = get_wcmp_vendor_order_amount(array('order_id' => $order, 'vendor_id' => $vendor));
                        $vendor_commission = round($vendor_order_amount['total'], 2);
                        if ($vendor_commission > 0) {
                            $receivers[$vendor_paypal_email] = array('email' => $vendor_paypal_email, 'amount' => $vendor_commission);
                            if ('chained' == $this->method) {
                                $receivers[$vendor_paypal_email]['primary'] = 'false';
                            }
                        }
                        $total_vendor_commission += $vendor_commission;
                    }
                }
            }
        }
        if ($total_vendor_commission > 0) {
            $admin_commission = round(($order->order_total - $total_vendor_commission), 2);
            if ('chained' == $this->method) {
                $primary_receiver = array(
                    'amount' => number_format($order->order_total, 2, '.', ''),
                    'email' => $this->receiver_email,
                    'primary' => 'true'
                );
                array_unshift($receivers, $primary_receiver);
            } else if ($admin_commission > 0) {
                $primary_receiver = array(
                    'amount' => $admin_commission,
                    'email' => $this->receiver_email,
                );
                array_unshift($receivers, $primary_receiver);
            }

            $args['receiverList'] = array(
                'receiver' => array_values($receivers)
            );
        } else {
            $args['receiverList'] = array(
                'receiver' => array(
                    array(
                        'amount' => number_format($order->order_total, 2, '.', ''),
                        'email' => $this->receiver_email
                    )
                )
            );
        }

        $args = apply_filters('wcmp_paypal_adaptive_payment_args', $args, $order);

        return $args;
    }

    /**
     * Set PayPal payment options.
     *
     * @param string $pay_key
     */
    protected function set_payment_options($pay_key) {

        $data = array(
            'payKey' => $pay_key,
            'requestEnvelope' => array(
                'errorLanguage' => 'en_US',
                'detailLevel' => 'ReturnAll'
            ),
            'displayOptions' => array(
                'businessName' => trim(substr(get_option('blogname'), 0, 128))
            ),
            'senderOptions' => array(
                'referrerCode' => 'WCMp_Cart'
            )
        );

        if ('' != $this->header_image) {
            $data['displayOptions']['headerImageUrl'] = $this->header_image;
        }

        // Sets the post params.
        $params = array(
            'body' => json_encode($data),
            'timeout' => 60,
            'httpversion' => '1.1',
            'headers' => array(
                'X-PAYPAL-SECURITY-USERID' => $this->api_username,
                'X-PAYPAL-SECURITY-PASSWORD' => $this->api_password,
                'X-PAYPAL-SECURITY-SIGNATURE' => $this->api_signature,
                'X-PAYPAL-REQUEST-DATA-FORMAT' => 'JSON',
                'X-PAYPAL-RESPONSE-DATA-FORMAT' => 'JSON',
                'X-PAYPAL-APPLICATION-ID' => $this->app_id,
            )
        );

        if ('yes' == $this->sandbox) {
            $url = $this->api_sandbox_url;
        } else {
            $url = $this->api_prod_url;
        }

        if ('yes' == $this->debug) {
            doPaypalAdaptiveLog('Setting payment options with the following data: ' . print_r($data, true));
        }

        $response = wp_safe_remote_post($url . 'SetPaymentOptions', $params);
        if (!is_wp_error($response) && 200 == $response['response']['code'] && 'OK' == $response['response']['message']) {
            if ('yes' == $this->debug) {
                doPaypalAdaptiveLog('Payment options configured successfully!');
            }
        } else {
            if ('yes' == $this->debug) {
                doPaypalAdaptiveLog('Failed to configure payment options: ' . print_r($response, true));
            }
        }
    }

    public function init_form_fields() {

        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'wcmp-paypal-adaptive-gateway'),
                'type' => 'checkbox',
                'label' => __('Enable PayPal Adaptive Payments', 'wcmp-paypal-adaptive-gateway'),
                'default' => 'yes'
            ),
            'title' => array(
                'title' => __('Title', 'wcmp-paypal-adaptive-gateway'),
                'type' => 'text',
                'description' => __('This controls the title which the user sees during checkout.', 'wcmp-paypal-adaptive-gateway'),
                'default' => __('PayPal adaptive', 'wcmp-paypal-adaptive-gateway'),
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => __('Description', 'wcmp-paypal-adaptive-gateway'),
                'type' => 'textarea',
                'description' => __('This controls the description which the user sees during checkout.', 'wcmp-paypal-adaptive-gateway'),
                'default' => __('Pay via PayPal; you can pay with your credit card if you don\'t have a PayPal account', 'wcmp-paypal-adaptive-gateway')
            ),
            'api_username' => array(
                'title' => __('PayPal API Username', 'wcmp-paypal-adaptive-gateway'),
                'type' => 'text',
                'description' => __('Please enter your PayPal API username; this is needed in order to take payment.', 'wcmp-paypal-adaptive-gateway'),
                'default' => '',
                'desc_tip' => true,
            ),
            'api_password' => array(
                'title' => __('PayPal API Password', 'wcmp-paypal-adaptive-gateway'),
                'type' => 'text',
                'description' => __('Please enter your PayPal API password; this is needed in order to take payment.', 'wcmp-paypal-adaptive-gateway'),
                'default' => '',
                'desc_tip' => true,
            ),
            'api_signature' => array(
                'title' => __('PayPal API Signature', 'wcmp-paypal-adaptive-gateway'),
                'type' => 'text',
                'description' => __('Please enter your PayPal API signature; this is needed in order to take payment.', 'wcmp-paypal-adaptive-gateway'),
                'default' => '',
                'desc_tip' => true,
            ),
            'app_id' => array(
                'title' => __('PayPal Application ID', 'wcmp-paypal-adaptive-gateway'),
                'type' => 'text',
                'description' => __('Please enter your PayPal Application ID; you need create an application on PayPal.', 'wcmp-paypal-adaptive-gateway'),
                'default' => '',
                'desc_tip' => true,
            ),
            'receiver_email' => array(
                'title' => __('Receiver Email', 'wcmp-paypal-adaptive-gateway'),
                'type' => 'email',
                'description' => __('Input your main receiver email for your PayPal account.', 'wcmp-paypal-adaptive-gateway'),
                'default' => '',
                'desc_tip' => true,
                'placeholder' => 'you@youremail.com'
            ),
            'sandbox' => array(
                'title' => __('PayPal sandbox', 'wcmp-paypal-adaptive-gateway'),
                'type' => 'checkbox',
                'label' => __('Enable PayPal sandbox', 'wcmp-paypal-adaptive-gateway'),
                'default' => 'no',
                'description' => sprintf(__('PayPal sandbox can be used to test payments. Sign up for a developer account <a href="%s">developer account</a>.', 'wcmp-paypal-adaptive-gateway'), 'https://developer.paypal.com/'),
            ),
            'method' => array(
                'title' => __('Payment Method', 'wcmp-paypal-adaptive-gateway'),
                'type' => 'select',
                'description' => __('Select the payment method: Parallel Payment - payment from a sender that is split directly among 2-6 receivers or Chained Payment - payment from a sender that is indirectly split among 1-9 secondary receivers.', 'wcmp-paypal-adaptive-gateway'),
                'default' => 'parallel',
                'desc_tip' => true,
                'options' => array(
                    'parallel' => __('Parallel Payment', 'wcmp-paypal-adaptive-gateway'),
                    'chained' => __('Chained Payment', 'wcmp-paypal-adaptive-gateway')
                )
            ),
            'debug' => array(
                'title' => __('Debug Log', 'wcmp-paypal-adaptive-gateway'),
                'type' => 'checkbox',
                'label' => __('Enable logging', 'wcmp-paypal-adaptive-gateway'),
                'default' => 'no',
                'description' => '',
            )
        );
    }

    protected function admin_notices() {
        if (is_admin()) {
            if ('yes' == $this->get_option('enabled') && ( empty($this->api_username) || empty($this->api_password) || empty($this->api_signature) || empty($this->app_id) || empty($this->receiver_email) )) {
                add_action('admin_notices', array($this, 'gateway_not_configured_message'));
            }
            if (!$this->using_supported_currency()) {
                add_action('admin_notices', array($this, 'unsupported_currency_not_message'));
            }
        }
    }

    public function using_supported_currency() {
        if (!in_array(get_woocommerce_currency(), apply_filters('wcmp_paypal_adaptive_supported_currencies', array('AUD', 'BRL', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'NOK', 'NZD', 'PHP', 'PLN', 'GBP', 'SGD', 'SEK', 'CHF', 'TWD', 'THB', 'TRY', 'USD')))) {
            return false;
        }

        return true;
    }

    public function gateway_not_configured_message() {
        $id = 'woocommerce_wcmp-paypal-adaptive-payments_';
        if (isset($_POST[$id . 'api_username']) && !empty($_POST[$id . 'api_username']) && isset($_POST[$id . 'api_password']) && !empty($_POST[$id . 'api_password']) && isset($_POST[$id . 'api_signature']) && !empty($_POST[$id . 'api_signature']) && isset($_POST[$id . 'app_id']) && !empty($_POST[$id . 'app_id']) && isset($_POST[$id . 'receiver_email']) && !empty($_POST[$id . 'receiver_email'])) {
            return;
        }
        echo '<div class="error"><p><strong>' . __('PayPal Adaptive Payments Disabled For WC Marketplace', 'wcmp-paypal-adaptive-gateway') . '</strong>: ' . __('You must fill the API Username, API Password, API Signature, Application ID and Receiver Email options.', 'wcmp-paypal-adaptive-gateway') . '</p></div>';
    }

    public function unsupported_currency_not_message() {
        echo '<div class="error"><p><strong>' . __('PayPal Adaptive Payments Disabled', 'wcmp-paypal-adaptive-gateway') . '</strong>: ' . __('PayPal does not support your store currency.', 'wcmp-paypal-adaptive-gateway') . '</p></div>';
    }

    public function paypal_adaptive_ipn_response() {
        @ob_clean();
        $ipn_response = !empty($_POST) ? $_POST : false;
        if ($ipn_response) {
            header('HTTP/1.1 200 OK');
            do_action('wcmp_paypal_adaptive_payments_ipn', $ipn_response);
        } else {
            wp_die('PayPal Adaptive IPN Request Failure', 'PayPal IPN', array('response' => 200));
        }
    }

    public function handel_paypal_adaptive_ipn($ipn_response) {
        // handel ipn response here
        $posted = stripslashes_deep($ipn_response);
        if (!isset($posted['tracking_id'])) {
            exit;
        }
        $order_id = intval($posted['tracking_id']);
        if ('yes' == $this->debug) {
            doPaypalAdaptiveLog('Checking IPN response for order #' . $order_id . '...');
        }
        $order = wc_get_order($order_id);
        $status = esc_attr($posted['status']);
        if ('yes' == $this->debug) {
            doPaypalAdaptiveLog('Payment status: ' . $status);
        }
        switch ($status) {
            case 'CANCELED' :
                $order->update_status('cancelled', __('Payment canceled via IPN.', 'wcmp-paypal-adaptive-gateway'));
                $this->delete_associated_commission($order_id);
                break;
            case 'CREATED' :
                $order->update_status('on-hold', __('The payment request was received. Funds will be transferred once the payment is approved.', 'wcmp-paypal-adaptive-gateway'));
                break;
            case 'COMPLETED' :
                // Check order not already completed.
                if ($order->status == 'completed') {
                    if ('yes' == $this->debug) {
                        $this->log->add($this->id, 'Aborting, Order #' . $order->id . ' is already complete.');
                    }
                    exit;
                }
                if (!empty($posted['sender_email'])) {
                    update_post_meta($order->id, 'Payer PayPal address', sanitize_text_field($posted['sender_email']));
                }
                $order->add_order_note(__('The payment was successful.', 'wcmp-paypal-adaptive-gateway'));
                $order->payment_complete();
                $this->commission_payment_compleate($order_id);
                break;
            case 'INCOMPLETE' :
                $order->update_status('on-hold', __('Some transfers succeeded and some failed for a parallel payment or, for a delayed chained payment, secondary receivers have not been paid.', 'wcmp-paypal-adaptive-gateway'));
                break;
            case 'ERROR' :
                $order->update_status('failed', __('The payment failed and all attempted transfers failed or all completed transfers were successfully reversed.', 'wcmp-paypal-adaptive-gateway'));
                $this->delete_associated_commission($order_id);
                break;
            case 'REVERSALERROR' :
                $order->update_status('failed', __('One or more transfers failed when attempting to reverse a payment.', 'wcmp-paypal-adaptive-gateway'));
                $this->delete_associated_commission($order_id);
                break;
            case 'PROCESSING' :
                $order->update_status('on-hold', __('The payment is in progress.', 'wcmp-paypal-adaptive-gateway'));
                break;
            case 'PENDING' :
                $order->update_status('pending', __('The payment is awaiting processing.', 'wcmp-paypal-adaptive-gateway'));
                break;

            default :

                break;
        }
    }

    protected function delete_associated_commission($order_id) {
        global $wpdb;
        if (WCMP_Paypal_Adaptive_Gateway_Dependencies::wcmp_active_check()) {
            $vendor_orders_in_order = get_wcmp_vendor_orders(array('order_id' => $order_id));
            if (!empty($vendor_orders_in_order)) {
                $commission_ids = wp_list_pluck($vendor_orders_in_order, 'commission_id');
                if ($commission_ids && is_array($commission_ids)) {
                    foreach ($commission_ids as $commission_id) {
                        wp_delete_post($commission_id);
                    }
                }
            }
            $wpdb->delete($wpdb->prefix . 'wcmp_vendor_orders', array('order_id' => $order_id), array('%d'));
            delete_post_meta($order_id, '_commissions_processed');
        }
    }

    protected function commission_payment_compleate($order_id) {
        if (WCMP_Paypal_Adaptive_Gateway_Dependencies::wcmp_active_check()) {
            global $WCMp;
            $vendor_orders_in_order = get_wcmp_vendor_orders(array('order_id' => $order_id));
            if (!empty($vendor_orders_in_order)) {
                $commission_ids = wp_list_pluck($vendor_orders_in_order, 'commission_id');
                if ($commission_ids && is_array($commission_ids)) {
                    $commission_to_pay = array();
                    foreach ($commission_ids as $commission_id) {
                        $vendor_term_id = get_post_meta($commission_id, '_commission_vendor', true);
                        if ($vendor_term_id) {
                            $vendor = get_wcmp_vendor_by_term($vendor_term_id);
                            $vendor_payment_method = get_user_meta($vendor->id, '_vendor_payment_mode', true);
                            $vendor_paypal_email = get_user_meta($vendor->id, '_vendor_paypal_email', true);
                            if ($vendor_payment_method == 'paypal_adaptive' && $vendor_paypal_email && apply_filters('is_wcmp_vendor_receive_paypal_adaptive', true, $vendor)) {
                                $commission_to_pay[$vendor_term_id][] = $commission_id;
                            }
                        }
                    }
                    foreach ($commission_to_pay as $vendor_term_id => $commissions) {
                        $vendor = get_wcmp_vendor_by_term($vendor_term_id);
                        $payment_method = get_user_meta($vendor->id, '_vendor_payment_mode', true);
                        if ($payment_method && $payment_method == 'paypal_adaptive') {
                            if (array_key_exists($payment_method, $WCMp->payment_gateway->payment_gateways)) {
                                $WCMp->payment_gateway->payment_gateways[$payment_method]->process_payment($vendor, $commissions, 'gateway');
                            }
                        }
                    }
                }
            }
        }
    }

}
