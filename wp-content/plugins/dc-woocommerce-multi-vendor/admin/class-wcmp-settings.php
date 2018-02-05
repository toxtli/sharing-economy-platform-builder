<?php

class WCMp_Settings {

    private $tabs = array();
    private $options;
    private $tabsection_general = array();
    private $tabsection_payment = array();
    private $tabsection_vendor = array();
    private $tabsection_capabilities = array();

    /**
     * Start up
     */
    public function __construct() {
        // Admin menu
        add_action('admin_menu', array($this, 'add_settings_page'), 100);
        add_action('admin_init', array($this, 'settings_page_init'));

        // Settings tabs
        add_action('settings_page_general_tab_init', array(&$this, 'general_tab_init'), 10, 1);

        add_action('settings_page_payment_tab_init', array(&$this, 'payment_tab_init'), 10, 1);
        add_action('settings_page_payment_paypal_masspay_tab_init', array(&$this, 'payment_paypal_masspay_init'), 10, 2);
        add_action('settings_page_payment_paypal_payout_tab_init', array(&$this, 'payment_paypal_payout_init'), 10, 2);

        add_action('settings_page_frontend_tab_init', array(&$this, 'frontend_tab_init'), 10, 1);
        add_action('settings_page_to_do_list_tab_init', array(&$this, 'to_do_list_tab_init'), 10, 1);
        add_action('settings_page_notices_tab_init', array(&$this, 'notices_tab_init'), 10, 1);
        add_action('settings_page_general_policies_tab_init', array(&$this, 'general_policies_tab_init'), 10, 2);
        add_action('settings_page_general_customer_support_details_tab_init', array(&$this, 'general_customer_support_details_tab_init'), 10, 2);
        add_action('settings_page_vendor_general_tab_init', array(&$this, 'vendor_general_tab_init'), 10, 2);
        add_action('settings_page_vendor_registration_tab_init', array(&$this, 'vendor_registration_tab_init'), 10, 2);

        add_action('settings_page_vendor_dashboard_tab_init', array(&$this, 'vendor_dashboard_tab_init'), 10, 2);
        add_action('settings_page_wcmp-addons_tab_init', array(&$this, 'wcmp_addons_tab_init'), 10, 2);

        add_action('update_option_wcmp_vendor_general_settings_name', array(&$this, 'wcmp_update_option_wcmp_vendor_general_settings_name'));

        add_action('settings_page_capabilities_product_tab_init', array(&$this, 'capabilites_product_tab_init'), 10, 2);

        add_action('settings_page_capabilities_order_tab_init', array(&$this, 'capabilites_order_tab_init'), 10, 2);

        add_action('settings_page_capabilities_miscellaneous_tab_init', array(&$this, 'capabilites_miscellaneous_tab_init'), 10, 2);
    }

    /**
     * flush rewrite rules after endpoints change
     */
    public function wcmp_update_option_wcmp_vendor_general_settings_name() {
        global $WCMp;
        $WCMp->endpoints->init_wcmp_query_vars();
        $WCMp->endpoints->add_wcmp_endpoints();
        flush_rewrite_rules();
    }

    /**
     * Add options page   
     */
    public function add_settings_page() {
        global $WCMp, $submenu;

        add_menu_page(
                __('WCMp', 'dc-woocommerce-multi-vendor')
                , __('WCMp', 'dc-woocommerce-multi-vendor')
                , 'manage_woocommerce'
                , 'wcmp'
                , null
                , $WCMp->plugin_url . 'assets/images/dualcube.png'
                , 45
        );
        add_submenu_page('wcmp', __('Reports', 'dc-woocommerce-multi-vendor'), __('Reports', 'dc-woocommerce-multi-vendor'), 'manage_woocommerce', 'wc-reports&tab=wcmp_vendors', '__return_false');
        $wcmp_settings_page = add_submenu_page('wcmp', __('Settings', 'dc-woocommerce-multi-vendor'), __('Settings', 'dc-woocommerce-multi-vendor'), 'manage_woocommerce', 'wcmp-setting-admin', array($this, 'create_wcmp_settings'));
        $wcmp_todo_list = add_submenu_page('wcmp', __('To-do List', 'dc-woocommerce-multi-vendor'), __('To-do List', 'dc-woocommerce-multi-vendor'), 'manage_woocommerce', 'wcmp-to-do', array($this, 'wcmp_to_do'));
        $wcmp_extension_page = add_submenu_page('wcmp', __('Extensions', 'dc-woocommerce-multi-vendor'), __('Extensions', 'dc-woocommerce-multi-vendor'), 'manage_woocommerce', 'wcmp-extensions', array($this, 'wcmp_extensions'));


        $this->tabs = $this->get_wcmp_settings_tabs();
        $this->tabsection_general = $this->get_wcmp_settings_tabsections_general();
        $this->tabsection_payment = $this->get_wcmp_settings_tabsections_payment();
        $this->tabsection_vendor = $this->get_wcmp_settings_tabsections_vendor();
        $this->tabsection_capabilities = $this->get_wcmp_settings_tabsections_capabilities();
        // Add WCMp Help Tab
        add_action('load-' . $wcmp_settings_page, array(&$this, 'wcmp_settings_add_help_tab'));
        add_action('load-' . $wcmp_extension_page, array(&$this, 'wcmp_settings_add_help_tab'));
        add_action('load-' . $wcmp_todo_list, array(&$this, 'wcmp_settings_add_help_tab'));
        /* sort wcmp submenu */
        if (isset($submenu['wcmp'])) {
            $submenu_sort[] = $submenu['wcmp'][0];
            $submenu_sort[] = $submenu['wcmp'][5];
            $submenu_sort[] = $submenu['wcmp'][1];
            $submenu_sort[] = $submenu['wcmp'][2];
            $submenu_sort[] = $submenu['wcmp'][3];
            $submenu_sort[] = $submenu['wcmp'][4];
            $submenu_sort[] = $submenu['wcmp'][6];
            $submenu['wcmp'] = apply_filters('wcmp_submeu_items', $submenu_sort, $submenu['wcmp']);
        }
    }

    function wcmp_settings_add_help_tab() {
        global $WCMp;
        $screen = get_current_screen();

        $screen->add_help_tab(array(
            'id' => 'wcmp_intro',
            'title' => __('WC Marketplace', 'dc-woocommerce-multi-vendor'),
            'content' => '<h2>WC Marketplace ' . WCMp_PLUGIN_VERSION . '</h2>' . '<iframe src="https://player.vimeo.com/video/203286653?title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>',
        ));
        $screen->add_help_tab(array(
            'id' => 'wcmp_help',
            'title' => __('Help &amp; Support', 'dc-woocommerce-multi-vendor'),
            'content' => '<h2>Help &amp; Support</h2>' .
            '<p>Our enrich documentation is suffice to answer all of your queries on WC Marketplace. We have covered all of your questions with snippets, graphics and a complete set-up guide.</p>'
            . '<p>For further assistance in WC Marketplace please contact to our <a target="_blank" href="https://wc-marketplace.com/support-forum/">support forum</a> .</p>',
        ));
        $screen->add_help_tab(array(
            'id' => 'wcmp_found_bug',
            'title' => __('Found a bug?', 'dc-woocommerce-multi-vendor'),
            'content' => '<h2>Found a bug?</h2>'
            . '<p>If you find a bug within WC Marketplace core you can submit your report by raising a ticket via <a target="_blank" href="https://github.com/dualcube/dc-woocommerce-multi-vendor/issues">Github issues</a>. Prior to submitting the report, please read the contribution guide.</p>'
        ));
        $screen->add_help_tab(array(
            'id' => 'wcmp_knowledgebase',
            'title' => __('Knowledgebase', 'dc-woocommerce-multi-vendor'),
            'content' => '<h2>Knowledgebase</h2>'
            . '<p>If you would like to learn more about using WC Marketplace, please follow our <a target="_blank" href="https://wc-marketplace.com/knowledgebase/">knowledgebase</a> section.</p>'
        ));
        $screen->add_help_tab(array(
            'id' => 'wcmp_onboard_tab',
            'title' => __('Setup wizard', 'dc-woocommerce-multi-vendor'),
            'content' =>
            '<h2>' . __('Setup wizard', 'dc-woocommerce-multi-vendor') . '</h2>' .
            '<p>' . __('If you need to access the setup wizard again, please click on the button below.', 'dc-woocommerce-multi-vendor') . '</p>' .
            '<p><a href="' . admin_url('index.php?page=wcmp-setup') . '" class="button button-primary">' . __('Setup wizard', 'woocommerce') . '</a></p>',
        ));
        $screen->set_help_sidebar(
                '<p><strong>' . __('For more information:', 'dc-woocommerce-multi-vendor') . '</strong></p>' .
                '<p><a href="' . 'https://wordpress.org/plugins/dc-woocommerce-multi-vendor/' . '" target="_blank">' . __('WordPress.org Project', 'dc-woocommerce-multi-vendor') . '</a></p>' .
                '<p><a href="' . 'https://github.com/dualcube/dc-woocommerce-multi-vendor' . '" target="_blank">' . __('Github Project', 'dc-woocommerce-multi-vendor') . '</a></p>' .
                '<p><a href="' . 'http://wcmpdemos.com/addon/WCMp/' . '" target="_blank">' . __('View Demo', 'dc-woocommerce-multi-vendor') . '</a></p>' .
                '<p><a href="' . 'https://wc-marketplace.com/third-party-themes/' . '" target="_blank">' . __('Supported Themes', 'dc-woocommerce-multi-vendor') . '</a></p>' .
                '<p><a href="' . 'https://wc-marketplace.com/addons/' . '" target="_blank">' . __('Official Extensions', 'dc-woocommerce-multi-vendor') . '</a></p>'
        );
    }

    function get_wcmp_settings_tabs() {
        global $WCMp;
        $tabs = apply_filters('wcmp_tabs', array(
            'general' => __('General', 'dc-woocommerce-multi-vendor'),
            'vendor' => __('Vendor', 'dc-woocommerce-multi-vendor'),
//            'product' => __('Products', 'dc-woocommerce-multi-vendor'),
            'frontend' => __('Frontend', 'dc-woocommerce-multi-vendor'),
            'payment' => __('Payment', 'dc-woocommerce-multi-vendor'),
            'capabilities' => __('Capabilities', 'dc-woocommerce-multi-vendor')
        ));
        return $tabs;
    }

    function get_wcmp_settings_tabsections_general() {
        global $WCMp;
        $tabsection_general = apply_filters('wcmp_tabsection_general', array(
            'general' => __('General', 'dc-woocommerce-multi-vendor'),
            'policies' => __('Policies', 'dc-woocommerce-multi-vendor'),
            'customer_support_details' => __('Customer Support', 'dc-woocommerce-multi-vendor'),
        ));
        return $tabsection_general;
    }

    function get_wcmp_settings_tabsections_payment() {
        $tabsection_payment = apply_filters('wcmp_tabsection_payment', array(
            'payment' => __('Payment Settings', 'dc-woocommerce-multi-vendor'),
            'paypal_masspay' => __('Paypal Masspay', 'dc-woocommerce-multi-vendor'),
            'paypal_payout' => __('Paypal Payout', 'dc-woocommerce-multi-vendor')
        ));
        return $tabsection_payment;
    }

    function get_wcmp_settings_tabsections_vendor() {
        $tabsection_vendor = apply_filters('wcmp_tabsection_vendor', array(
            'general' => 'Vendor Pages',
            'registration' => __('Vendor Registration', 'dc-woocommerce-multi-vendor'),
            'dashboard' => __('Vendor Dashboard', 'dc-woocommerce-multi-vendor')
        ));
        return $tabsection_vendor;
    }

    function get_wcmp_settings_tabsections_capabilities() {
        $tabsection_vendor = apply_filters('wcmp_tabsection_capabilities', array(
            'product' => __('Product', 'dc-woocommerce-multi-vendor'),
            'order' => __('Order', 'dc-woocommerce-multi-vendor'),
            'miscellaneous' => __('Miscellaneous', 'dc-woocommerce-multi-vendor')
        ));
        return $tabsection_vendor;
    }

    function get_saettings_tab_desc() {
        global $WCMp;
        $tab_desc = apply_filters('wcmp_tabs_desc', array(
            'product' => __('Configure the "Product Add" page for vendors. Choose the features you want to show to your vendors.', 'dc-woocommerce-multi-vendor'),
            'frontend' => __('Configure which vendor details you want to reveal to your users', 'dc-woocommerce-multi-vendor'),
//            'capabilities' => __('These are general sets of permissions for vendors. Note that these are global settings, and you may override these settings for an individual vendor from the vendor profile page. ', 'dc-woocommerce-multi-vendor'),
        ));
        return $tab_desc;
    }

    function wcmp_settings_tabs($current = 'general') {
        global $WCMp;
        $admin_url = get_admin_url();

        if (isset($_GET['tab'])) :
            $current = $_GET['tab'];
        else:
            $current = 'general';
        endif;
        $sublinks = array();
        if ($current == 'general') {
            if (isset($_GET['tab_section'])) {
                $current_section = $_GET['tab_section'];
            } else {
                $current_section = 'general';
            }
            foreach ($this->tabsection_general as $tabsection => $sectionname) :
                if ($tabsection == 'university' || $tabsection == 'vendor_notices' || $tabsection == 'commission') {
                    $admin_url = trailingslashit(get_admin_url());
                    if ($tabsection == 'university') {
                        $link_url = $admin_url . 'edit.php?post_type=wcmp_university';
                    } elseif ($tabsection == 'vendor_notices') {
                        $link_url = $admin_url . 'edit.php?post_type=wcmp_vendor_notice';
                    } elseif ($tabsection == 'commission') {
                        $link_url = $admin_url . 'edit.php?post_type=dc_commission';
                    }
                    $sublinks[] = "<li><a class='wcmp_sub_sction' href='$link_url'>$sectionname</a>  </li>";
                } else {
                    if ($tabsection == $current_section) :
                        $sublinks[] = "<li><a class='current wcmp_sub_sction' href='?page=wcmp-setting-admin&tab=$current&tab_section=$tabsection'>$sectionname</a>  </li>";
                    else :
                        $sublinks[] = "<li><a class='wcmp_sub_sction' href='?page=wcmp-setting-admin&tab=$current&tab_section=$tabsection'>$sectionname</a>  </li>";
                    endif;
                }
            endforeach;
        } else if ($current == 'payment') {
            if (isset($_GET['tab_section'])) {
                $current_section = $_GET['tab_section'];
            } else {
                $current_section = 'payment';
            }
            foreach ($this->tabsection_payment as $tabsection => $sectionname) {
                if ($tabsection == $current_section) :
                    $sublinks[] = "<li><a class='current wcmp_sub_sction' href='?page=wcmp-setting-admin&tab=$current&tab_section=$tabsection'>$sectionname</a>  </li>";
                else :
                    $sublinks[] = "<li><a class='wcmp_sub_sction' href='?page=wcmp-setting-admin&tab=$current&tab_section=$tabsection'>$sectionname</a>  </li>";
                endif;
            }
        } else if ($current == 'vendor') {
            if (isset($_GET['tab_section'])) {
                $current_section = $_GET['tab_section'];
            } else {
                $current_section = 'general';
            }
            foreach ($this->tabsection_vendor as $tabsection => $sectionname) {
                if ($tabsection == $current_section) :
                    $sublinks[] = "<li><a class='current wcmp_sub_sction' href='?page=wcmp-setting-admin&tab=$current&tab_section=$tabsection'>$sectionname</a>  </li>";
                else :
                    $sublinks[] = "<li><a class='wcmp_sub_sction' href='?page=wcmp-setting-admin&tab=$current&tab_section=$tabsection'>$sectionname</a>  </li>";
                endif;
            }
        } else if ($current == 'capabilities') {
            if (isset($_GET['tab_section'])) {
                $current_section = $_GET['tab_section'];
            } else {
                $current_section = 'product';
            }
            foreach ($this->tabsection_capabilities as $tabsection => $sectionname) {
                if ($tabsection == $current_section) :
                    $sublinks[] = "<li><a class='current wcmp_sub_sction' href='?page=wcmp-setting-admin&tab=$current&tab_section=$tabsection'>$sectionname</a>  </li>";
                else :
                    $sublinks[] = "<li><a class='wcmp_sub_sction' href='?page=wcmp-setting-admin&tab=$current&tab_section=$tabsection'>$sectionname</a>  </li>";
                endif;
            }
        }
        $links = array();
        foreach ($this->tabs as $tab => $name) :
            if ($tab == $current) :
                $links[] = "<a class='nav-tab nav-tab-active' href='?page=wcmp-setting-admin&tab=$tab'>$name</a>";
            else :
                $links[] = "<a class='nav-tab' href='?page=wcmp-setting-admin&tab=$tab'>$name</a>";
            endif;
        endforeach;

        echo '<div class="icon32" id="dualcube_menu_ico"><br></div>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($links as $link)
            echo $link;
        echo '</h2>';

        $display_sublink = false;
        if ($current == 'general' || $current == 'payment' || $current == 'vendor' || $current == 'capabilities') {
            $display_sublink = true;
        }
        $display_sublink = apply_filters('display_wcmp_sublink', $display_sublink, $current);
        $sublinks = apply_filters('wcmp_subtab', $sublinks, $current);
        if ($display_sublink) {
            echo '<ul class="subsubsub wcmpsubtabadmin">';
            foreach ($sublinks as $sublink) {
                echo $sublink;
            }
            echo '</ul>';
            echo '<div style="width:100%; clear:both;">&nbsp;</div>';
        }

        $tab_desc = $this->get_saettings_tab_desc();
        foreach ($this->tabs as $tabd => $named) :
            if ($tabd == $current && !empty($tab_desc[$tabd])) :
                printf(__("<h4 style=\'border-bottom: 1px solid rgb(215, 211, 211);padding-bottom: 21px;\'>%s</h4>", 'dc-woocommerce-multi-vendor'), $tab_desc[$tabd]);
            endif;
        endforeach;
    }

    /**
     * Options page callback
     */
    public function create_wcmp_settings() {
        global $WCMp;
        ?>
        <div class="wrap">
            <?php $this->wcmp_settings_tabs(); ?>
            <?php
            $tab = ( isset($_GET['tab']) ? $_GET['tab'] : 'general' );

            if ($tab == 'general' && isset($_GET['tab_section']) && $_GET['tab_section'] != 'general') {
                $tab_section = $_GET['tab_section'];
                $this->options = get_option("wcmp_{$tab}_{$tab_section}_settings_name");
            } else if ($tab == 'payment' && isset($_GET['tab_section']) && $_GET['tab_section'] != 'payment') {
                $tab_section = $_GET['tab_section'];
                $this->options = get_option("wcmp_{$tab}_{$tab_section}_settings_name");
            } else if ($tab == 'vendor') {
                if (isset($_GET['tab_section']) && $_GET['tab_section'] != 'vendor') {
                    $tab_section = $_GET['tab_section'];
                } else {
                    $tab_section = 'general';
                }
                $this->options = get_option("wcmp_{$tab}_{$tab_section}_settings_name");
            } else if ($tab == 'capabilities') {
                if (isset($_GET['tab_section'])) {
                    $tab_section = $_GET['tab_section'];
                } else {
                    $tab_section = 'product';
                }
                $this->options = get_option("wcmp_{$tab}_{$tab_section}_settings_name");
            } else if (isset($_GET['tab_section']) && $tab != 'general' && $tab != 'payment') {
                $tab_section = $_GET['tab_section'];
                $this->options = get_option("wcmp_{$tab}_{$tab_section}_settings_name");
            } else {
                $this->options = get_option("wcmp_{$tab}_settings_name");
            }


            // This prints out all hidden setting errors
            if ($tab == 'general' && isset($_GET['tab_section']) && $_GET['tab_section'] != 'general') {
                settings_errors("wcmp_{$tab}_{$tab_section}_settings_name");
            } else if ($tab == 'payment' && isset($_GET['tab_section']) && $_GET['tab_section'] != 'payment') {
                settings_errors("wcmp_{$tab}_{$tab_section}_settings_name");
            } else if ($tab == 'vendor' || $tab == 'capabilities') {
                settings_errors("wcmp_{$tab}_{$tab_section}_settings_name");
            } else if (isset($_GET['tab_section']) && $tab != 'general' && $tab != 'payment') {
                $tab_section = $_GET['tab_section'];
                settings_errors("wcmp_{$tab}_{$tab_section}_settings_name");
            } else {
                settings_errors("wcmp_{$tab}_settings_name");
            }
            ?>
            <form class='wcmp_vendors_settings' method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                if ($tab == 'general' && isset($_GET['tab_section']) && $_GET['tab_section'] != 'general') {
                    settings_fields("wcmp_{$tab}_{$tab_section}_settings_group");
                    do_action("wcmp_{$tab}_{$tab_section}_settings_before_submit");
                    do_settings_sections("wcmp-{$tab}-{$tab_section}-settings-admin");
                    submit_button();
                } else if ($tab == 'payment' && isset($_GET['tab_section']) && $_GET['tab_section'] != 'payment') {
                    settings_fields("wcmp_{$tab}_{$tab_section}_settings_group");
                    do_action("wcmp_{$tab}_{$tab_section}_settings_before_submit");
                    do_settings_sections("wcmp-{$tab}-{$tab_section}-settings-admin");
                    submit_button();
                } else if ($tab == 'vendor') {
                    settings_fields("wcmp_{$tab}_{$tab_section}_settings_group");
                    do_action("wcmp_{$tab}_{$tab_section}_settings_before_submit");
                    do_settings_sections("wcmp-{$tab}-{$tab_section}-settings-admin");
                    if ($tab_section == 'registration') {
                        do_action("settings_page_{$tab}_{$tab_section}_tab_init", $tab, $tab_section);
                        wp_enqueue_style('wcmp_vendor_registration', $WCMp->plugin_url . 'assets/admin/css/admin-vendor_registration.css', array(), $WCMp->version);
                        wp_enqueue_script('wcmp_angular', $WCMp->plugin_url . 'assets/admin/js/angular.min.js', array(), $WCMp->version);
                        wp_enqueue_script('wcmp_angular-ui', $WCMp->plugin_url . 'assets/admin/js/sortable.js', array('wcmp_angular'), $WCMp->version);
                        wp_enqueue_script('wcmp_vendor_registration', $WCMp->plugin_url . 'assets/admin/js/vendor_registration_app.js', array('wcmp_angular', 'wcmp_angular-ui'), $WCMp->version);
                        $wcmp_vendor_registration_form_data = get_option('wcmp_vendor_registration_form_data');
                        wp_localize_script('wcmp_vendor_registration', 'vendor_registration_param', array('partials' => $WCMp->plugin_url . 'assets/admin/partials/', 'ajax_url' => admin_url('admin-ajax.php'), 'form_data' => $wcmp_vendor_registration_form_data));
                    } else {
                        submit_button();
                    }
                } else if ($tab == 'capabilities') {
                    if (isset($_GET['tab_section'])) {
                        $tab_section = $_GET['tab_section'];
                    } else {
                        $tab_section = 'product';
                    }
                    settings_fields("wcmp_{$tab}_{$tab_section}_settings_group");
                    do_action("wcmp_{$tab}_{$tab_section}_settings_before_submit");
                    do_settings_sections("wcmp-{$tab}-{$tab_section}-settings-admin");
                    submit_button();
                } else if ($tab == 'wcmp-addons') {
                    do_action("settings_page_{$tab}_tab_init", $tab);
                } else if (isset($_GET['tab_section']) && $_GET['tab_section'] && $tab != 'general' && $tab != 'payment') {
                    $tab_section = $_GET['tab_section'];
                    settings_fields("wcmp_{$tab}_{$tab_section}_settings_group");
                    do_action("wcmp_{$tab}_{$tab_section}_settings_before_submit");
                    do_settings_sections("wcmp-{$tab}-{$tab_section}-settings-admin");
                    submit_button();
                } else {
                    settings_fields("wcmp_{$tab}_settings_group");
                    do_action("wcmp_{$tab}_settings_before_submit");
                    do_settings_sections("wcmp-{$tab}-settings-admin");
                    submit_button();
                }
                ?>
            </form>
            <?php
            if (isset($_GET['tab']) && $_GET['tab'] == 'payment') {
                if (wp_next_scheduled('paypal_masspay_cron_start')) {
                    _e('<br><b>MassPay Sync</b><br>', 'dc-woocommerce-multi-vendor');
                    printf(__('Next MassPay cron @ %s', 'dc-woocommerce-multi-vendor'), date('d/m/Y g:i:s A', wp_next_scheduled('paypal_masspay_cron_start')));
                    printf(__('<br>Now the time is %s', 'dc-woocommerce-multi-vendor'), date('d/m/Y g:i:s A', time()));
                }
            }
            ?>
        </div>
        <?php
        do_action('dualcube_admin_footer');
    }

    function wcmp_extensions() {
        global $WCMp;
        ?>  
        <div class="wrap">
            <h1><?php _e('WCMp Extensions') ?></h1>
            <?php do_action("settings_page_wcmp-addons_tab_init", 'wcmp-addons'); ?>
            <?php do_action('dualcube_admin_footer'); ?>
        </div>
        <?php
    }

    function wcmp_to_do() {
        global $WCMp;
        ?>  
        <div class="wrap wcmp_vendors_settings">
            <h1><?php _e('To-do') ?></h1>
            <?php do_action("settings_page_to_do_list_tab_init", 'to_do_list'); ?>
            <?php do_action('dualcube_admin_footer'); ?>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        do_action('befor_settings_page_init');
        // Register each tab settings
        foreach ($this->tabs as $tab => $name) :
            do_action("settings_page_{$tab}_tab_init", $tab);
            if ($tab == 'general') {
                foreach ($this->tabsection_general as $tabsection => $sectionname) {
                    if ($tabsection == 'general' || $tabsection == 'university' || $tabsection == 'vendor_notices' || $tabsection == 'commission') {
                        
                    } else {
                        do_action("settings_page_{$tab}_{$tabsection}_tab_init", $tab, $tabsection);
                    }
                }
            } else if ($tab == 'payment') {
                foreach ($this->tabsection_payment as $tabsection => $sectionname) {
                    if ($tabsection == 'payment') {
                        
                    } else {
                        do_action("settings_page_{$tab}_{$tabsection}_tab_init", $tab, $tabsection);
                    }
                }
            } else if ($tab == 'vendor') {
                foreach ($this->tabsection_vendor as $tabsection => $sectionname) {
                    if ($tabsection == 'vendor') {
                        
                    } else {
                        if ($tabsection == 'registration')
                            continue;
                        do_action("settings_page_{$tab}_{$tabsection}_tab_init", $tab, $tabsection);
                    }
                }
            }else if ($tab == 'capabilities') {
                foreach ($this->tabsection_capabilities as $tabsection => $sectionname) {
                    do_action("settings_page_{$tab}_{$tabsection}_tab_init", $tab, $tabsection);
                }
            }
            do_action('after_setup_wcmp_settings_page', $tab);
        endforeach;
        do_action('after_settings_page_init');
    }

    /**
     * Register and add settings fields
     */
    public function settings_field_init($tab_options) {
        global $WCMp;

        if (!empty($tab_options) && isset($tab_options['tab']) && isset($tab_options['ref']) && isset($tab_options['sections'])) {
            // Register tab options
            register_setting(
                    "wcmp_{$tab_options['tab']}_settings_group", // Option group
                    "wcmp_{$tab_options['tab']}_settings_name", // Option name
                    array($tab_options['ref'], "wcmp_{$tab_options['tab']}_settings_sanitize") // Sanitize
            );

            foreach ($tab_options['sections'] as $sectionID => $section) {
                // Register section
                if (method_exists($tab_options['ref'], "{$sectionID}_info")) {
                    add_settings_section(
                            $sectionID, // ID
                            $section['title'], // Title
                            array($tab_options['ref'], "{$sectionID}_info"), // Callback
                            "wcmp-{$tab_options['tab']}-settings-admin" // Page
                    );
                } else {
                    $callback = isset($section['ref']) && method_exists($section['ref'], "{$sectionID}_info") ? array($section['ref'], "{$sectionID}_info") : __return_false();
                    add_settings_section(
                            $sectionID, // ID
                            $section['title'], // Title
                            $callback, // Callback
                            "wcmp-{$tab_options['tab']}-settings-admin" // Page
                    );
                }

                // Register fields
                if (isset($section['fields'])) {
                    foreach ($section['fields'] as $fieldID => $field) {
                        if (isset($field['type'])) {
                            $field['tab'] = $tab_options['tab'];
                            $callbak = $this->get_field_callback_type($field['type']);
                            if (!empty($callbak)) {
                                add_settings_field(
                                        $fieldID, $field['title'], array($this, $callbak), "wcmp-{$tab_options['tab']}-settings-admin", $sectionID, $this->process_fields_args($field, $fieldID)
                                );
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Register and add settings fields
     */
    public function settings_field_withsubtab_init($tab_options) {
        global $WCMp;



        if (!empty($tab_options) && isset($tab_options['tab']) && isset($tab_options['ref']) && isset($tab_options['sections']) && isset($tab_options['subsection'])) {
            // Register tab options
            register_setting(
                    "wcmp_{$tab_options['tab']}_{$tab_options['subsection']}_settings_group", // Option group
                    "wcmp_{$tab_options['tab']}_{$tab_options['subsection']}_settings_name", // Option name
                    array($tab_options['ref'], "wcmp_{$tab_options['tab']}_{$tab_options['subsection']}_settings_sanitize") // Sanitize
            );

            foreach ($tab_options['sections'] as $sectionID => $section) {
                // Register section
                if (method_exists($tab_options['ref'], "{$sectionID}_info")) {
                    add_settings_section(
                            $sectionID, // ID
                            $section['title'], // Title
                            array($tab_options['ref'], "{$sectionID}_info"), // Callback
                            "wcmp-{$tab_options['tab']}-{$tab_options['subsection']}-settings-admin" // Page
                    );
                } else {
                    $callback = isset($section['ref']) && method_exists($section['ref'], "{$sectionID}_info") ? array($section['ref'], "{$sectionID}_info") : __return_false();
                    add_settings_section(
                            $sectionID, // ID
                            $section['title'], // Title
                            $callback, // Callback
                            "wcmp-{$tab_options['tab']}-{$tab_options['subsection']}-settings-admin" // Page
                    );
                }

                // Register fields
                if (isset($section['fields'])) {
                    foreach ($section['fields'] as $fieldID => $field) {
                        if (isset($field['type'])) {
                            $field['tab'] = $tab_options['tab'] . '_' . $tab_options['subsection'];
                            $callbak = $this->get_field_callback_type($field['type']);
                            if (!empty($callbak)) {
                                add_settings_field(
                                        $fieldID, $field['title'], array($this, $callbak), "wcmp-{$tab_options['tab']}-{$tab_options['subsection']}-settings-admin", $sectionID, $this->process_fields_args($field, $fieldID)
                                );
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * function process_fields_args
     * @param $fields
     * @param $fieldId
     * @return Array
     */
    function process_fields_args($field, $fieldID) {

        if (!isset($field['id'])) {
            $field['id'] = $fieldID;
        }

        if (!isset($field['label_for'])) {
            $field['label_for'] = $fieldID;
        }

        if (!isset($field['name'])) {
            $field['name'] = $fieldID;
        }

        return $field;
    }

    function general_tab_init($tab) {
        global $WCMp;
        $WCMp->admin->load_class("settings-{$tab}", $WCMp->plugin_path, $WCMp->token);
        new WCMp_Settings_Gneral($tab);
    }

    function general_policies_tab_init($tab, $subsection) {
        global $WCMp;
        $WCMp->admin->load_class("settings-{$tab}-{$subsection}", $WCMp->plugin_path, $WCMp->token);
        new WCMp_Settings_Gneral_Policies($tab, $subsection);
    }

    function general_customer_support_details_tab_init($tab, $subsection) {
        global $WCMp;
        $WCMp->admin->load_class("settings-{$tab}-{$subsection}", $WCMp->plugin_path, $WCMp->token);
        new WCMp_Settings_Gneral_Customer_support_Details($tab, $subsection);
    }

    function capabilites_product_tab_init($tab, $subsection) {
        global $WCMp;
        $WCMp->admin->load_class("settings-{$tab}-{$subsection}", $WCMp->plugin_path, $WCMp->token);
        new WCMp_Settings_Capabilities_Product($tab, $subsection);
    }

    function capabilites_order_tab_init($tab, $subsection) {
        global $WCMp;
        $WCMp->admin->load_class("settings-{$tab}-{$subsection}", $WCMp->plugin_path, $WCMp->token);
        new WCMp_Settings_Capabilities_Order($tab, $subsection);
    }

    function capabilites_miscellaneous_tab_init($tab, $subsection) {
        global $WCMp;
        $WCMp->admin->load_class("settings-{$tab}-{$subsection}", $WCMp->plugin_path, $WCMp->token);
        new WCMp_Settings_Capabilities_Miscellaneous($tab, $subsection);
    }

    function notices_tab_init($tab) {
        global $WCMp;
        $WCMp->admin->load_class("settings-{$tab}", $WCMp->plugin_path, $WCMp->token);
        new WCMp_Settings_Notices($tab);
    }

    function payment_tab_init($tab) {
        global $WCMp;
        $WCMp->admin->load_class("settings-{$tab}", $WCMp->plugin_path, $WCMp->token);
        new WCMp_Settings_Payment($tab);
    }

    function payment_paypal_masspay_init($tab, $subsection) {
        global $WCMp;
        $WCMp->admin->load_class("settings-{$tab}-{$subsection}", $WCMp->plugin_path, $WCMp->token);
        new WCMp_Settings_Payment_Paypal_Masspay($tab, $subsection);
    }

    function payment_paypal_payout_init($tab, $subsection) {
        global $WCMp;
        $WCMp->admin->load_class("settings-{$tab}-{$subsection}", $WCMp->plugin_path, $WCMp->token);
        new WCMp_Settings_Payment_Paypal_Payout($tab, $subsection);
    }

    function frontend_tab_init($tab) {
        global $WCMp;
        $WCMp->admin->load_class("settings-{$tab}", $WCMp->plugin_path, $WCMp->token);
        new WCMp_Settings_Frontend($tab);
    }

    function to_do_list_tab_init($tab) {
        global $WCMp;
        $WCMp->admin->load_class("settings-{$tab}", $WCMp->plugin_path, $WCMp->token);
        new WCMp_Settings_To_Do_List($tab);
    }

    function vendor_registration_tab_init($tab, $subsection) {
        global $WCMp;
        $WCMp->admin->load_class("settings-{$tab}-{$subsection}", $WCMp->plugin_path, $WCMp->token);
        new WCMp_Settings_Vendor_Registration($tab, $subsection);
    }

    public function vendor_dashboard_tab_init($tab, $subsection) {
        global $WCMp;
        $WCMp->admin->load_class("settings-{$tab}-{$subsection}", $WCMp->plugin_path, $WCMp->token);
        new WCMp_Settings_Vendor_Dashboard($tab, $subsection);
    }

    function vendor_general_tab_init($tab, $subsection) {
        global $WCMp;
        $WCMp->admin->load_class("settings-{$tab}-{$subsection}", $WCMp->plugin_path, $WCMp->token);
        new WCMp_Settings_Vendor_General($tab, $subsection);
    }

    function wcmp_addons_tab_init($tab) {
        global $WCMp;
        $WCMp->admin->load_class("settings-{$tab}", $WCMp->plugin_path, $WCMp->token);
        new WCMp_Settings_WCMp_Addons($tab);
    }

    function get_field_callback_type($fieldType) {
        $callBack = '';
        switch ($fieldType) {
            case 'input':
            case 'number':
            case 'text':
            case 'email':
            case 'url':
                $callBack = 'text_field_callback';
                break;

            case 'hidden':
                $callBack = 'hidden_field_callback';
                break;

            case 'textarea':
                $callBack = 'textarea_field_callback';
                break;

            case 'wpeditor':
                $callBack = 'wpeditor_field_callback';
                break;

            case 'checkbox':
                $callBack = 'checkbox_field_callback';
                break;

            case 'radio':
                $callBack = 'radio_field_callback';
                break;
            case 'radio_select':
                $callBack = 'radio_select_field_callback';
                break;

            case 'select':
                $callBack = 'select_field_callback';
                break;

            case 'upload':
                $callBack = 'upload_field_callback';
                break;

            case 'colorpicker':
                $callBack = 'colorpicker_field_callback';
                break;

            case 'datepicker':
                $callBack = 'datepicker_field_callback';
                break;

            case 'multiinput':
                $callBack = 'multiinput_callback';
                break;

            default:
                $callBack = '';
                break;
        }

        return $callBack;
    }

    /**
     * Get the hidden field display
     */
    public function hidden_field_callback($field) {
        global $WCMp;
        $field['value'] = isset($field['value']) ? esc_attr($field['value']) : '';
        $field['value'] = isset($this->options[$field['name']]) ? esc_attr($this->options[$field['name']]) : $field['value'];
        $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMp->wcmp_wp_fields->hidden_input($field);
    }

    /**
     * Get the text field display
     */
    public function text_field_callback($field) {
        global $WCMp;
        $field['dfvalue'] = isset($field['dfvalue']) ? esc_attr($field['dfvalue']) : '';
        $field['value'] = isset($field['value']) ? esc_attr($field['value']) : $field['dfvalue'];
        $field['value'] = isset($this->options[$field['name']]) ? esc_attr($this->options[$field['name']]) : $field['value'];
        $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMp->wcmp_wp_fields->text_input($field);
    }

    /**
     * Get the text area display
     */
    public function textarea_field_callback($field) {
        global $WCMp;
        $field['value'] = isset($field['value']) ? esc_textarea($field['value']) : '';
        $field['value'] = isset($this->options[$field['name']]) ? esc_textarea($this->options[$field['name']]) : $field['value'];
        $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMp->wcmp_wp_fields->textarea_input($field);
    }

    /**
     * Get the wpeditor display
     */
    public function wpeditor_field_callback($field) {
        global $WCMp;
        $field['value'] = isset($field['value']) ? ( $field['value'] ) : '';
        $field['value'] = isset($this->options[$field['name']]) ? ( $this->options[$field['name']] ) : $field['value'];
        $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMp->wcmp_wp_fields->wpeditor_input($field);
    }

    /**
     * Get the checkbox field display
     */
    public function checkbox_field_callback($field) {
        global $WCMp;
        $field['value'] = isset($field['value']) ? esc_attr($field['value']) : '';
        $field['value'] = isset($this->options[$field['name']]) ? esc_attr($this->options[$field['name']]) : $field['value'];
        $field['dfvalue'] = isset($this->options[$field['name']]) ? esc_attr($this->options[$field['name']]) : '';
        $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMp->wcmp_wp_fields->checkbox_input($field);
    }

    /**
     * Get the checkbox field display
     */
    public function radio_field_callback($field) {
        global $WCMp;
        $field['value'] = isset($field['value']) ? esc_attr($field['value']) : '';
        $field['value'] = isset($this->options[$field['name']]) ? esc_attr($this->options[$field['name']]) : $field['value'];
        $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMp->wcmp_wp_fields->radio_input($field);
    }
    
    /**
     * Get the checkbox field display
     */
    public function radio_select_field_callback($field) {
        global $WCMp;
        $field['value'] = isset($field['value']) ? esc_attr($field['value']) : '';
        $field['value'] = isset($this->options[$field['name']]) ? esc_attr($this->options[$field['name']]) : $field['value'];
        $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMp->wcmp_wp_fields->radio_select_input($field);
    }

    /**
     * Get the select field display
     */
    public function select_field_callback($field) {
        global $WCMp;
        $field['value'] = isset($field['value']) ? esc_textarea($field['value']) : '';
        $field['value'] = isset($this->options[$field['name']]) ? esc_textarea($this->options[$field['name']]) : $field['value'];
        $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMp->wcmp_wp_fields->select_input($field);
    }

    /**
     * Get the upload field display
     */
    public function upload_field_callback($field) {
        global $WCMp;
        $field['value'] = isset($field['value']) ? esc_attr($field['value']) : '';
        $field['value'] = isset($this->options[$field['name']]) ? esc_attr($this->options[$field['name']]) : $field['value'];
        $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMp->wcmp_wp_fields->upload_input($field);
    }

    /**
     * Get the multiinput field display
     */
    public function multiinput_callback($field) {
        global $WCMp;
        $field['value'] = isset($field['value']) ? $field['value'] : array();
        $field['value'] = isset($this->options[$field['name']]) ? $this->options[$field['name']] : $field['value'];
        $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMp->wcmp_wp_fields->multi_input($field);
    }

    /**
     * Get the colorpicker field display
     */
    public function colorpicker_field_callback($field) {
        global $WCMp;
        $field['value'] = isset($field['value']) ? esc_attr($field['value']) : '';
        $field['value'] = isset($this->options[$field['name']]) ? esc_attr($this->options[$field['name']]) : $field['value'];
        $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMp->wcmp_wp_fields->colorpicker_input($field);
    }

    /**
     * Get the datepicker field display
     */
    public function datepicker_field_callback($field) {
        global $WCMp;
        $field['value'] = isset($field['value']) ? esc_attr($field['value']) : '';
        $field['value'] = isset($this->options[$field['name']]) ? esc_attr($this->options[$field['name']]) : $field['value'];
        $field['name'] = "wcmp_{$field['tab']}_settings_name[{$field['name']}]";
        $WCMp->wcmp_wp_fields->datepicker_input($field);
    }

}
