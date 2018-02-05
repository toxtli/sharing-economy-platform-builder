<?php

function lae_get_all_post_type_options() {

    $post_types = get_post_types(array('public' => true), 'objects');

    $options = ['' => ''];

    foreach ($post_types as $post_type) {
        $options[$post_type->name] = $post_type->label;
    }

    return $options;
}

/**
 * Action to handle searching taxonomy terms.
 */
function lae_get_all_taxonomy_options() {

    global $wpdb;

    $results = array();

    foreach ($wpdb->get_results("
		SELECT terms.slug AS 'slug', terms.name AS 'label', termtaxonomy.taxonomy AS 'type'
		FROM $wpdb->terms AS terms
		JOIN $wpdb->term_taxonomy AS termtaxonomy ON terms.term_id = termtaxonomy.term_id
		LIMIT 100
	") as $result) {
        $results[$result->type . ':' . $result->slug] = $result->type . ':' . $result->label;
    }
    return $results;
}

function lae_build_query_args($settings) {

    $query_args = [
        'orderby' => $settings['orderby'],
        'order' => $settings['order'],
        'ignore_sticky_posts' => 1,
        'post_status' => 'publish',
    ];

    if (!empty($settings['post_in'])) {
        $query_args['post_type'] = 'any';
        $query_args['post__in'] = explode(',', $settings['post_in']);
        $query_args['post__in'] = array_map('intval', $query_args['post__in']);
    }
    else {
        if (!empty($settings['post_types'])) {
            $query_args['post_type'] = $settings['post_types'];
        }

        if (!empty($settings['tax_query'])) {
            $tax_queries = $settings['tax_query'];

            $query_args['tax_query'] = array();
            $query_args['tax_query']['relation'] = 'OR';
            foreach ($tax_queries as $tq) {
                list($tax, $term) = explode(':', $tq);

                if (empty($tax) || empty($term))
                    continue;
                $query_args['tax_query'][] = array(
                    'taxonomy' => $tax,
                    'field' => 'slug',
                    'terms' => $term
                );
            }
        }
    }

    $query_args['posts_per_page'] = $settings['posts_per_page'];

    $query_args['paged'] = max(1, get_query_var('paged'), get_query_var('page'));

    return $query_args;
}
