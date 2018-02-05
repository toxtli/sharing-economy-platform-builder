<?php
/*
Plugin Name: WP Post Page Clone
Plugin URI: https://wordpress.org/plugins/wp-post-page-clone
Description: A plugin to generate duplicate post or page with contents and it's settings.
Author: Gaurang Sondagar
Author URI: http://gaurangsondagar99.wordpress.com/
Version: 1.0
Text Domain: wp-post-page-clone
Requires at least: 4.0
Tested up to: 4.7.2
Domin Path: Languages
License: GPLV2

/**
 * Exit if accessed directly
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Define Constant variables
 */
define('WP_POST_PAGE_CLONE_URL', plugins_url() . '/wp-post-page-clone');


if(!function_exists('wp_post_page_clone_translate')) {
    /**
     * Function for language translations
     */
    function wp_post_page_clone_translate() {

        load_plugin_textdomain('wp-post-page-clone', false, basename(dirname( __FILE__ ) ) . '/languages' );

    }

}

add_action( 'plugins_loaded', 'wp_post_page_clone_translate' );


if(!function_exists('wp_post_page_clone')) {
    /**
     * Function for post / page clone and redirect that post
     * @global type $wpdb
     */
    function wp_post_page_clone(){

            global $wpdb;
            if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'wp_post_page_clone' == $_REQUEST['action'] ) ) ) {
                    wp_die('No post or page to clone has been supplied!');
            }

            $post_id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
            $post = get_post( $post_id );
            $current_user = wp_get_current_user();
            $post_author = $current_user->ID;

            if (isset( $post ) && $post != null) {

                    $args = array(
                            'comment_status' => $post->comment_status,
                            'ping_status'    => $post->ping_status,
                            'post_author'    => $post_author,
                            'post_content'   => $post->post_content,
                            'post_excerpt'   => $post->post_excerpt,
                            'post_name'      => $post->post_name,
                            'post_parent'    => $post->post_parent,
                            'post_password'  => $post->post_password,
                            'post_status'    => 'draft',
                            'post_title'     => $post->post_title,
                            'post_type'      => $post->post_type,
                            'to_ping'        => $post->to_ping,
                            'menu_order'     => $post->menu_order
                    );

                    $clone_post_id = wp_insert_post( $args );

                    $taxonomies = get_object_taxonomies($post->post_type);
                    foreach ($taxonomies as $taxonomy) {
                            $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
                            wp_set_object_terms($clone_post_id, $post_terms, $taxonomy, false);
                    }

                    $post_meta_data = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
                    if (count($post_meta_data)!=0) {
                            $clone_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
                            foreach ($post_meta_data as $meta_data) {
                                    $meta_key = $meta_data->meta_key;
                                    $meta_value = addslashes($meta_data->meta_value);
                                    $clone_query_select[]= "SELECT $clone_post_id, '$meta_key', '$meta_value'";
                            }
                            $clone_query.= implode(" UNION ALL ", $clone_query_select);
                            $wpdb->query($clone_query);
                    }

                    wp_redirect( admin_url( 'post.php?action=edit&post=' . $clone_post_id ) );
                    exit;

            } else {

                    wp_die(__('Post or Page creation failed, could not find original post:', 'wp-post-page-clone') . $post_id);

            }
    }

}

add_action( 'admin_action_wp_post_page_clone', 'wp_post_page_clone' );


if(!function_exists('wp_post_page_link')) {

    /**
     * Add the duplicate link to action list for post_row_actions
     * @param string $actions
     * @param type $post
     * @return string
     */
    function wp_post_page_link( $actions, $post ) {
            if (current_user_can('edit_posts')) {
                    $actions['clone'] = '<a href="admin.php?action=wp_post_page_clone&amp;post=' . $post->ID . '" title="'.__('Clone Post and Page', 'wp-post-page-clone').'" rel="permalink">'.__('Click To Clone', 'wp-post-page-clone').'</a>';
            }
            return $actions;
    }

}

/**
 * Filter for post / page row actions
 */
add_filter( 'post_row_actions', 'wp_post_page_link', 10, 2 );
add_filter('page_row_actions', 'wp_post_page_link', 10, 2);

?>
