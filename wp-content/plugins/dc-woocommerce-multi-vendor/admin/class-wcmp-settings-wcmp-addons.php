<?php

class WCMp_Settings_WCMp_Addons {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;

    /**
     * Start up
     */
    public function __construct($tab) {
        $this->tab = $tab;
        $this->options = get_option("wcmp_{$this->tab}_settings_name");
        $this->settings_page_init();
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMp, $wp_version;
        $args = array(
            'timeout' => 5,
            'redirection' => 5,
            'httpversion' => '1.0',
            'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url(),
            'blocking' => true,
            'headers' => array(),
            'cookies' => array(),
            'body' => null,
            'compress' => false,
            'decompress' => true,
            'sslverify' => true,
            'stream' => false,
            'filename' => null
        );
        $url = 'https://wc-marketplace.com/wp-json/wc/v1/products/?per_page=100&orderby=title&order=asc';
        $response = wp_remote_get($url, $args);
        ?>
        <div class="wcmp-addon-container">
            <div class="addon-banner">
                <img src="<?php echo $WCMp->plugin_url.'assets/images/addon-banner.png' ?>" />
                <div class="addon-banner-content">
                    <h1>WCMp Bundled Addons is available</h1>
                    <p>Give more power to your vendors to manage their shop, allow them to track their sales and control your marketplace with additional authority- unwrap powerful marketplace solution all in one bundle.</p>
                    <a href="https://wc-marketplace.com/wcmp-bundle/" target="_blank" class="">Grab It Now</a>
                </div>
            </div>
            <div class="addonbox-container">
                <?php
                if (!is_wp_error($response) && isset($response['body'])) {
                    foreach (json_decode($response['body']) as $product) {
                        if (isset($product->id)) {
                            ?>
                            <div class="addonbox">
                                <h2 class="hndle"><span><?php echo $product->name; ?></span></h2>
                                <div class="inside">
                                    <div class="submitbox" id="submitpost">
                                        <div class="addon-img-holder">
                                            <img src="<?php echo $product->images[0]->src; ?>" alt="wcmp" />
                                        </div>                                       

                                        <div id="major-publishing-actions">
                                            <p><?php echo strip_tags($product->short_description); ?></p>
                                            <div id="publishing-action">
                                                <a href="<?php echo $product->permalink; ?>" target="_blank" class="button button-primary button-large">View More</a>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <?php
                        }
                    }
                }
                ?>
            </div>
        </div>
        <?php
    }

}
