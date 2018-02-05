<?php
if (!defined('ABSPATH')) {
    exit;
}

use Stripe\Stripe;
use Stripe\Transfer;

if (!class_exists('WC_Product_Vendors_Stripe_Connect')) {

    class WC_Product_Vendors_Stripe_Connect extends WC_Product_Vendors_Payout_Scheduler {

        public $stripe_settings;
        private $is_testmode;
        private $stripe_client_id;
        private $stripe_secret_key;
        public $currency;

        public function __construct() {
            $this->stripe_settings = get_option('woocommerce_stripe_settings');
            $this->is_testmode = $this->stripe_settings['testmode'] === "yes" ? true : false;
            $this->stripe_secret_key = $this->is_testmode ? $this->stripe_settings['test_secret_key'] : $this->stripe_settings['secret_key'];
            $this->stripe_client_id = $this->is_testmode ? get_option('wcpv_vendor_settings_stripe_test_client_id') : get_option('wcpv_vendor_settings_stripe_live_client_id');
            $this->currency = get_woocommerce_currency();
            add_filter('wcpv_vendor_settings', array(&$this, 'wcpv_vendor_settings'));
            add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
            add_action('admin_menu', array(&$this, 'register_stripe_connect_vendor_menus'), 999);
            add_action('wcpv_commission_list_bulk_action', array(&$this, 'wcpv_commission_list_bulk_action'));
            parent::__construct(new WC_Product_Vendors_Commission(new WC_Product_Vendors_PayPal_MassPay()));
        }

        /**
         * Add extra field in product vendor setting panel
         * @param array $new_settings
         * @return array
         */
        public function wcpv_vendor_settings($new_settings) {
            $repla = array(
                array(
                    'title' => __('Payment Gateway', 'marketplace-stripe-gateway'),
                    'desc' => __('Choose preferred payment gateway to pay vendors', 'marketplace-stripe-gateway'),
                    'id' => 'wcpv_vendor_settings_payment_gateway',
                    'default' => 'paypal-masspay',
                    'type' => 'select',
                    'options' => array(
                        'paypal-masspay' => __('Paypal Masspay', 'marketplace-stripe-gateway'),
                        'stripe-connect' => __('Stripe Connect', 'marketplace-stripe-gateway'),
                    ),
                    'desc_tip' => true,
                    'autoload' => false
                ),
                array(
                    'title' => __('(Stripe)Test Client ID', 'marketplace-stripe-gateway'),
                    'desc' => __('Enter the API Test Client ID.', 'marketplace-stripe-gateway'),
                    'id' => 'wcpv_vendor_settings_stripe_test_client_id',
                    'default' => '',
                    'type' => 'text',
                    'autoload' => false
                ),
                array(
                    'title' => __('(Stripe)Live Client ID', 'marketplace-stripe-gateway'),
                    'desc' => __('Enter the API Live Client ID.', 'marketplace-stripe-gateway'),
                    'id' => 'wcpv_vendor_settings_stripe_live_client_id',
                    'default' => '',
                    'type' => 'text',
                    'autoload' => false
                )
            );
            array_splice($new_settings, 2, 0, $repla);
            return $new_settings;
        }

        /**
         * load js file in product vendor settings panel
         * @global object $WCMp_Stripe_Gateway
         */
        public function admin_enqueue_scripts() {
            global $WCMp_Stripe_Gateway;
            $screen = get_current_screen();
            if (in_array($screen->id, array('woocommerce_page_wc-settings'))):
                wp_enqueue_script('wc-product-vendors-stripe-connect-js', $WCMp_Stripe_Gateway->plugin_url . 'assets/admin/js/wc-product-vendors-stripe-connect.js', array('jquery'), $WCMp_Stripe_Gateway->version, true);
            endif;
        }

        /**
         * Do stripe payment along with paypal masspay
         * @return boolean
         */
        public function do_payment() {
            // no need to process if set to manual
            if ('manual' === $this->frequency) {
                return;
            }

            $unpaid_commission_ids = $this->commission->get_unpaid_commission_ids();
            if (get_option('wcpv_vendor_settings_payment_gateway') == 'stripe-connect') {
                if (empty($unpaid_commission_ids)) {
                    return;
                }
                $commission_data = $this->commission->get_commission_data($unpaid_commission_ids);
                // we want to combine each vendors total commission so that store owners
                // will not be charged per transaction for all items for each vendor
                if (apply_filters('wcpv_combine_total_commission_payout_per_vendor', true)) {
                    $commission_data = $this->commission->combine_total_commission_per_vendor($commission_data);
                }
                //doWooStripeLOG(print_r($commission_data, true));
                foreach ($commission_data as $commission) {
                    $vendor_id = $commission->vendor_id;
                    if (get_term_meta($vendor_id, '_stripe_connected', true)) {
                        $stripe_user_id = get_term_meta($vendor_id, '_stripe_user_id', true);
                        if ($this->process_stripe_payment($stripe_user_id, $commission->total_commission_amount)) {
                            $this->commission->update_status(absint($commission->id), absint($commission->order_item_id), 'paid');
                        }
                    }
                }
            }

            return true;
        }
        /**
         * Bulk pay vendor commission via stripe
         * @return type
         */
        public function wcpv_commission_list_bulk_action() {
            if (empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'bulk-commissions')) {
                return;
            }
            if (empty($_REQUEST['ids'])) {
                return;
            }
            $current_action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
            if($current_action == 'pay' && get_option('wcpv_vendor_settings_payment_gateway') == 'stripe-connect'){
                $ids = array_map( 'absint', $_REQUEST['ids'] );
                $unpaid_commission_ids = $this->commission->get_unpaid_commission_ids();
                foreach ($ids as $key => $id){
                    if(!array_key_exists($key, $unpaid_commission_ids)){
                        unset($ids[$key]);
                    }
                }
                $commission_data = $this->commission->get_commission_data($ids);
                foreach ($commission_data as $commission) {
                    $vendor_id = $commission->vendor_id;
                    if (get_term_meta($vendor_id, '_stripe_connected', true)) {
                        $stripe_user_id = get_term_meta($vendor_id, '_stripe_user_id', true);
                        if ($this->process_stripe_payment($stripe_user_id, $commission->total_commission_amount)) {
                            $this->commission->update_status(absint($commission->id), absint($commission->order_item_id), 'paid');
                        }
                    }
                }
            }
        }

        /**
         * Add stripe connect menu page
         */
        public function register_stripe_connect_vendor_menus() {
            if (WC_Product_Vendors_Utils::auth_vendor_user() && WC_Product_Vendors_Utils::is_admin_vendor() && get_option('wcpv_vendor_settings_payment_gateway') == 'stripe-connect') {
                add_menu_page(__('Stripe Connect', 'marketplace-stripe-gateway'), __('Stripe Connect', 'marketplace-stripe-gateway'), 'manage_product', 'wcpv-vendor-stripe-connect', array($this, 'render_stripe_connect_page'), 'dashicons-paperclip', 70);
            }
        }

        /**
         * Render stripe connect button and connect or disconnect stripe account
         */
        public function render_stripe_connect_page() {
            $vendor_id = WC_Product_Vendors_Utils::get_logged_in_vendor();
            $authorize_request_body = array(
                'response_type' => 'code',
                'scope' => 'read_write',
                'client_id' => $this->stripe_client_id,
                'state' => $vendor_id
            );
            $connect_url = 'https://connect.stripe.com/oauth/authorize?' . http_build_query($authorize_request_body);
            // Connect stripe account
            if ($this->stripe_client_id && $this->stripe_secret_key && !get_term_meta($vendor_id, '_stripe_connected', true)) {
                if (isset($_GET['code']) && isset($_GET['state'])) {
                    $state = $_GET['state'];
                    $code = $_GET['code'];
                    $token_request_body = array(
                        'grant_type' => 'authorization_code',
                        'client_id' => $this->stripe_client_id,
                        'code' => $code,
                        'client_secret' => $this->stripe_secret_key
                    );
                    $req = curl_init('https://connect.stripe.com/oauth/token');
                    curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($req, CURLOPT_POST, true);
                    curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));
                    curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($req, CURLOPT_SSL_VERIFYHOST, 2);
                    curl_setopt($req, CURLOPT_VERBOSE, true);
                    $resp = json_decode(curl_exec($req), true);
                    curl_close($req);
                    if (!isset($resp['error'])) {
                        update_term_meta($state, '_stripe_connected', true);
                        update_term_meta($state, '_access_token', $resp['access_token']);
                        update_term_meta($state, '_refresh_token', $resp['refresh_token']);
                        update_term_meta($state, '_stripe_publishable_key', $resp['stripe_publishable_key']);
                        update_term_meta($state, '_stripe_user_id', $resp['stripe_user_id']);
                    }
                }
            }

            //Disconnect stripe account
            if (isset($_POST['disconnect_stripe_connect']) && get_term_meta($vendor_id, '_stripe_connected', true)) {
                $stripe_user_id = get_term_meta($vendor_id, '_stripe_user_id', true);
                $token_request_body = array(
                    'client_id' => $this->stripe_client_id,
                    'stripe_user_id' => $stripe_user_id,
                    'client_secret' => $this->stripe_secret_key
                );
                $req = curl_init('https://connect.stripe.com/oauth/deauthorize');
                curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($req, CURLOPT_POST, true);
                curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));
                curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($req, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($req, CURLOPT_VERBOSE, true);
                $resp = json_decode(curl_exec($req), true);
                curl_close($req);
                if (isset($resp['stripe_user_id'])) {
                    delete_term_meta($vendor_id, '_stripe_connected');
                    delete_term_meta($vendor_id, '_access_token');
                    delete_term_meta($vendor_id, '_refresh_token');
                    delete_term_meta($vendor_id, '_stripe_publishable_key');
                    delete_term_meta($vendor_id, '_stripe_user_id');
                }
            }
            ?>
            <div class="wrap">
                <form action="" method="post">
                    <?php if (get_term_meta($vendor_id, '_stripe_connected', true)) { ?>
                        <button type="submit" name="disconnect_stripe_connect" class="button button-primary"><?php _e('Disconnect Stripe', 'marketplace-stripe-gateway'); ?></button>
                    <?php } else { ?>
                        <a href="<?php echo $connect_url; ?>" class="button button-primary" ><?php _e('Connect Stripe', 'marketplace-stripe-gateway'); ?></a>
                    <?php } ?>
                </form>
            </div>
            <?php
        }

        /**
         * process stripe payment
         * @param string $stripe_user_id
         * @param string $amount
         * @return boolean
         */
        private function process_stripe_payment($stripe_user_id, $amount) {
            try {
                Stripe::setApiKey($this->stripe_secret_key);
                $transfer_args = array(
                    'amount' => $this->get_stripe_amount($amount),
                    'currency' => $this->currency,
                    'destination' => $stripe_user_id
                );
                return Transfer::create($transfer_args);
            } catch (\Stripe\Error\InvalidRequest $e) {
                $this->message[] = array('message' => $e->getMessage(), 'type' => 'error');
                doWooStripeLOG(print_r($e->getMessage(), true));
            } catch (\Stripe\Error\Authentication $e) {
                $this->message[] = array('message' => $e->getMessage(), 'type' => 'error');
                doWooStripeLOG(print_r($e->getMessage(), true));
            } catch (\Stripe\Error\ApiConnection $e) {
                $this->message[] = array('message' => $e->getMessage(), 'type' => 'error');
                doWooStripeLOG(print_r($e->getMessage(), true));
            } catch (\Stripe\Error\Base $e) {
                $this->message[] = array('message' => $e->getMessage(), 'type' => 'error');
                doWooStripeLOG(print_r($e->getMessage(), true));
            } catch (Exception $e) {
                $this->message[] = array('message' => $e->getMessage(), 'type' => 'error');
                doWooStripeLOG(print_r($e->getMessage(), true));
            }
            return false;
        }

        /**
         * Format stripe amount
         * @param float $amount
         * @return int
         */
        private function get_stripe_amount($amount) {
            switch (strtoupper($this->currency)) {
                // Zero decimal currencies.
                case 'BIF' :
                case 'CLP' :
                case 'DJF' :
                case 'GNF' :
                case 'JPY' :
                case 'KMF' :
                case 'KRW' :
                case 'MGA' :
                case 'PYG' :
                case 'RWF' :
                case 'VND' :
                case 'VUV' :
                case 'XAF' :
                case 'XOF' :
                case 'XPF' :
                    $amount = absint($amount);
                    break;
                default :
                    $amount = round($amount, 2) * 100; // In cents.
                    break;
            }
            return $amount;
        }

    }

}
