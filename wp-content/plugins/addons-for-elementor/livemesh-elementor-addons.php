<?php
/**
 * Plugin Name: Addons for Elementor
 * Plugin URI: https://www.livemeshthemes.com/elementor-addons
 * Description: A collection of premium quality addons or widgets for use in Elementor page builder. Elementor must be installed and activated.
 * Author: Livemesh
 * Author URI: https://www.livemeshthemes.com/elementor-addons
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Version: 1.5.3
 * Text Domain: livemesh-el-addons
 * Domain Path: languages
 *
 * Addons for Elementor is distributed under the terms of the GNU
 * General Public License as published by the Free Software Foundation,
 * either version 2 of the License, or any later version.
 *
 * Addons for Elementor is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Addons for Elementor. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace LivemeshAddons;

// Exit if accessed directly
use LivemeshAddons\Controls\LAE_Control_Checkbox;

if (!defined('ABSPATH'))
    exit;

if (!class_exists('Livemesh_Elementor_Addons')) :

    /**
     * Main Livemesh_Elementor_Addons Class
     *
     */
    final class Livemesh_Elementor_Addons {

        /** Singleton *************************************************************/

        private static $instance;

        /**
         * Main Livemesh_Elementor_Addons Instance
         *
         * Insures that only one instance of Livemesh_Elementor_Addons exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         */
        public static function instance() {

            if (!isset(self::$instance) && !(self::$instance instanceof Livemesh_Elementor_Addons)) {

                self::$instance = new Livemesh_Elementor_Addons;

                self::$instance->setup_constants();

                self::$instance->includes();

                self::$instance->hooks();

            }
            return self::$instance;
        }

        /**
         * Throw error on object clone
         *
         * The whole idea of the singleton design pattern is that there is a single
         * object therefore, we don't want the object to be cloned.
         */
        public function __clone() {
            // Cloning instances of the class is forbidden
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'livemesh-el-addons'), '1.5.3');
        }

        /**
         * Disable unserializing of the class
         *
         */
        public function __wakeup() {
            // Unserializing instances of the class is forbidden
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'livemesh-el-addons'), '1.5.3');
        }

        /**
         * Setup plugin constants
         *
         */
        private function setup_constants() {

            // Plugin version
            if (!defined('LAE_VERSION')) {
                define('LAE_VERSION', '1.5.3');
            }

            // Plugin Folder Path
            if (!defined('LAE_PLUGIN_DIR')) {
                define('LAE_PLUGIN_DIR', plugin_dir_path(__FILE__));
            }

            // Plugin Folder URL
            if (!defined('LAE_PLUGIN_URL')) {
                define('LAE_PLUGIN_URL', plugin_dir_url(__FILE__));
            }

            // Plugin Folder Path
            if (!defined('LAE_ADDONS_DIR')) {
                define('LAE_ADDONS_DIR', plugin_dir_path(__FILE__) . 'includes/widgets/');
            }

            // Plugin Folder Path
            if (!defined('LAE_ADDONS_URL')) {
                define('LAE_ADDONS_URL', plugin_dir_url(__FILE__) . 'includes/widgets/');
            }

            // Plugin Root File
            if (!defined('LAE_PLUGIN_FILE')) {
                define('LAE_PLUGIN_FILE', __FILE__);
            }

            // Plugin Help Page URL
            if (!defined('LAE_PLUGIN_HELP_URL')) {
                define('LAE_PLUGIN_HELP_URL', admin_url() . 'admin.php?page=livemesh_el_addons_documentation');
            }

            $this->setup_debug_constants();
        }

        private function setup_debug_constants() {

            $enable_debug = false;

            $settings = get_option('lae_settings');

            if ($settings && isset($settings['lae_enable_debug']) && $settings['lae_enable_debug'] == "true")
                $enable_debug = true;

            // Enable script debugging
            if (!defined('LAE_SCRIPT_DEBUG')) {
                define('LAE_SCRIPT_DEBUG', $enable_debug);
            }

            // Minified JS file name suffix
            if (!defined('LAE_JS_SUFFIX')) {
                if ($enable_debug)
                    define('LAE_JS_SUFFIX', '');
                else
                    define('LAE_JS_SUFFIX', '.min');
            }
        }

        /**
         * Include required files
         *
         */
        private function includes() {


            require_once LAE_PLUGIN_DIR . 'includes/helper-functions.php';
            require_once LAE_PLUGIN_DIR . 'includes/query-functions.php';

            if (is_admin()) {
                require_once LAE_PLUGIN_DIR . 'admin/admin-init.php';
            }

        }

        /**
         * Load Plugin Text Domain
         *
         * Looks for the plugin translation files in certain directories and loads
         * them to allow the plugin to be localised
         */
        public function load_plugin_textdomain() {

            $lang_dir = apply_filters('lae_el_addons_lang_dir', trailingslashit(LAE_PLUGIN_DIR . 'languages'));

            // Traditional WordPress plugin locale filter
            $locale = apply_filters('plugin_locale', get_locale(), 'livemesh-el-addons');
            $mofile = sprintf('%1$s-%2$s.mo', 'livemesh-el-addons', $locale);

            // Setup paths to current locale file
            $mofile_local = $lang_dir . $mofile;

            if (file_exists($mofile_local)) {
                // Look in the /wp-content/plugins/livemesh-el-addons/languages/ folder
                load_textdomain('livemesh-el-addons', $mofile_local);
            }
            else {
                // Load the default language files
                load_plugin_textdomain('livemesh-el-addons', false, $lang_dir);
            }

            return false;
        }

        /**
         * Setup the default hooks and actions
         */
        private function hooks() {

            add_action('plugins_loaded', array(self::$instance, 'load_plugin_textdomain'));

            add_action('elementor/widgets/widgets_registered', array(self::$instance, 'include_widgets'));

            add_action('elementor/controls/controls_registered', array(self::$instance, 'include_controls'));

            add_action('elementor/frontend/after_register_scripts', array($this, 'register_frontend_scripts'), 10);

            add_action('elementor/frontend/after_register_styles', array($this, 'register_frontend_styles'), 10);

            add_action('elementor/frontend/after_enqueue_styles', array($this, 'enqueue_frontend_styles'), 10);

            add_action('elementor/init', array($this, 'add_elementor_category'));
        }


        public function add_elementor_category() {
            \Elementor\Plugin::instance()->elements_manager->add_category(
                'livemesh-addons',
                array(
                    'title' => __('Livemesh Addons', 'livemesh-el-addons'),
                    'icon' => 'fa fa-plug',
                ),
                2);
        }

        public function localize_scripts() {

            $custom_css = lae_get_option('lae_custom_css', '');

            wp_localize_script('lae-frontend-scripts', 'lae_settings', array('custom_css' => $custom_css));
        }

        /**
         * Load Frontend Scripts
         *
         */
        public function register_frontend_scripts() {

            // Use minified libraries if LAE_SCRIPT_DEBUG is turned off
            $suffix = (defined('LAE_SCRIPT_DEBUG') && LAE_SCRIPT_DEBUG) ? '' : '.min';

            wp_register_script('lae-modernizr', LAE_PLUGIN_URL . 'assets/js/modernizr-custom' . $suffix . '.js', array(), LAE_VERSION, true);

            wp_register_script('waypoints', LAE_PLUGIN_URL . 'assets/js/jquery.waypoints' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

            wp_register_script('isotope.pkgd', LAE_PLUGIN_URL . 'assets/js/isotope.pkgd' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

            wp_register_script('imagesloaded.pkgd', LAE_PLUGIN_URL . 'assets/js/imagesloaded.pkgd' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

            wp_register_script('jquery-stats', LAE_PLUGIN_URL . 'assets/js/jquery.stats' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

            wp_register_script('jquery-nivo', LAE_PLUGIN_URL . 'assets/js/jquery.nivo.slider' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

            wp_register_script('slick', LAE_PLUGIN_URL . 'assets/js/slick' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

            wp_register_script('responsiveslides', LAE_PLUGIN_URL . 'assets/js/responsiveslides' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

            wp_register_script('jquery-flexslider', LAE_PLUGIN_URL . 'assets/js/jquery.flexslider' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

            wp_register_script('lae-frontend-scripts', LAE_PLUGIN_URL . 'assets/js/lae-frontend' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

            wp_register_script('lae-widgets-scripts', LAE_PLUGIN_URL . 'assets/js/lae-widgets' . $suffix . '.js', array('jquery'), LAE_VERSION, true);

            $custom_css = lae_get_option('lae_custom_css', '');

            wp_localize_script('lae-frontend-scripts', 'lae_settings', array('custom_css' => $custom_css));

        }

        /**
         * Load Frontend Styles
         *
         */
        public function register_frontend_styles() {

            wp_register_style('lae-frontend-styles', LAE_PLUGIN_URL . 'assets/css/lae-frontend.css', array(), LAE_VERSION);

            wp_register_style('lae-widgets-styles', LAE_PLUGIN_URL . 'assets/css/lae-widgets.css', array('lae-frontend-styles'), LAE_VERSION);

            wp_register_style('lae-icomoon-styles', LAE_PLUGIN_URL . 'assets/css/icomoon.css', array(), LAE_VERSION);

            wp_register_style('lae-sliders-styles', LAE_PLUGIN_URL . 'assets/css/sliders.css', array(), LAE_VERSION);

        }

        /**
         * Load Frontend Styles
         *
         */
        public function enqueue_frontend_styles() {

            wp_enqueue_style('lae-frontend-styles');

            wp_enqueue_style('lae-widgets-styles');

            wp_enqueue_style('lae-icomoon-styles');

            wp_enqueue_style('lae-sliders-styles');

        }

        public function include_controls($controls_manager) {

            /* Load Custom Field Types */
            require_once LAE_PLUGIN_DIR . 'includes/controls/checkbox.php';

            $controls_manager->register_control('lae-checkbox', new LAE_Control_Checkbox());
        }

        /**
         * Include required files
         *
         */
        public function include_widgets($widgets_manager) {

            /* Load Elementor Addon Elements */


            $deactivate_element_team_members = lae_get_option('lae_deactivate_element_team', false);
            if (!$deactivate_element_team_members) {

                require_once LAE_ADDONS_DIR . 'team-members.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Team_Widget());

            }

            $deactivate_element_testimonials = lae_get_option('lae_deactivate_element_testimonials', false);
            if (!$deactivate_element_testimonials) {

                require_once LAE_ADDONS_DIR . 'testimonials.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Testimonials_Widget());

            }

            $deactivate_element_testimonials_slider = lae_get_option('lae_deactivate_element_testimonials_slider', false);
            if (!$deactivate_element_testimonials_slider) {

                require_once LAE_ADDONS_DIR . 'testimonials-slider.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Testimonials_Slider_Widget());

            }

            $deactivate_element_stats_bar = lae_get_option('lae_deactivate_element_stats_bar', false);
            if (!$deactivate_element_stats_bar) {

                require_once LAE_ADDONS_DIR . 'stats-bars.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Stats_Bars_Widget());

            }

            $deactivate_element_piecharts = lae_get_option('lae_deactivate_element_piecharts', false);
            if (!$deactivate_element_piecharts) {

                require_once LAE_ADDONS_DIR . 'piecharts.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Piecharts_Widget());

            }

            $deactivate_element_odometers = lae_get_option('lae_deactivate_element_odometers', false);
            if (!$deactivate_element_odometers) {

                require_once LAE_ADDONS_DIR . 'odometers.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Odometers_Widget());

            }

            $deactivate_element_services = lae_get_option('lae_deactivate_element_services', false);
            if (!$deactivate_element_services) {

                require_once LAE_ADDONS_DIR . 'services.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Services_Widget());

            }

            $deactivate_element_heading = lae_get_option('lae_deactivate_element_heading', false);
            if (!$deactivate_element_heading) {

                require_once LAE_ADDONS_DIR . 'heading.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Heading_Widget());

            }


            $deactivate_element_clients = lae_get_option('lae_deactivate_element_clients', false);
            if (!$deactivate_element_clients) {

                require_once LAE_ADDONS_DIR . 'clients.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Clients_Widget());
            }

            $deactivate_element_portfolio = lae_get_option('lae_deactivate_element_portfolio', false);
            if (!$deactivate_element_portfolio) {

                require_once LAE_ADDONS_DIR . 'portfolio.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Portfolio_Widget());

            }

            $deactivate_element_posts_carousel = lae_get_option('lae_deactivate_element_posts_carousel', false);
            if (!$deactivate_element_posts_carousel) {

                require_once LAE_ADDONS_DIR . 'posts-carousel.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Posts_Carousel_Widget());
            }

            $deactivate_element_pricing_table = lae_get_option('lae_deactivate_element_pricing_table', false);
            if (!$deactivate_element_pricing_table) {

                require_once LAE_ADDONS_DIR . 'pricing-table.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Pricing_Table_Widget());

            }

            $deactivate_element_carousel = lae_get_option('lae_deactivate_element_carousel', false);
            if (!$deactivate_element_carousel) {

                require_once LAE_ADDONS_DIR . 'carousel.php';

                $widgets_manager->register_widget_type(new \LivemeshAddons\Widgets\LAE_Carousel_Widget());
            }

        }

    }

endif; // End if class_exists check


/**
 * The main function responsible for returning the one true Livemesh_Elementor_Addons
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $lae = LAE(); ?>
 */
function LAE() {
    return Livemesh_Elementor_Addons::instance();
}

// Get LAE Running
LAE();