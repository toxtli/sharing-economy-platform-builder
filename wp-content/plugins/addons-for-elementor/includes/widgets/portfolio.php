<?php

/*
Widget Name: Livemesh Grid
Description: Display posts or custom post types in a multi-column grid.
Author: LiveMesh
Author URI: https://www.livemeshthemes.com
*/

namespace LivemeshAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;


if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class LAE_Portfolio_Widget extends Widget_Base {

    public function get_name() {
        return 'lae-portfolio';
    }

    public function get_title() {
        return __('Livemesh Grid', 'livemesh-el-addons');
    }

    public function get_icon() {
        return 'eicon-posts-grid';
    }

    public function get_categories() {
        return array('livemesh-addons');
    }

    public function get_script_depends() {
        return [
            'lae-widgets-scripts',
            'lae-frontend-scripts',
            'isotope.pkgd',
            'imagesloaded.pkgd'
        ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_query',
            [
                'label' => __('Post Query', 'livemesh-el-addons'),
            ]
        );


        $this->add_control(
            'post_types',
            [
                'label' => __('Post Types', 'livemesh-el-addons'),
                'type' => Controls_Manager::SELECT2,
                'default' => 'post',
                'options' => lae_get_all_post_type_options(),
                'multiple' => true
            ]
        );

        $this->add_control(
            'tax_query',
            [
                'label' => __('Taxonomies', 'livemesh-el-addons'),
                'type' => Controls_Manager::SELECT2,
                'options' => lae_get_all_taxonomy_options(),
                'multiple' => true,
                'label_block' => true
            ]
        );

        $this->add_control(
            'post_in',
            [
                'label' => __('Post In', 'livemesh-el-addons'),
                'description' => __('Provide a comma separated list of Post IDs to display in the grid.', 'livemesh-el-addons'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true
            ]
        );

        $this->add_control(
            'posts_per_page',
            [
                'label' => __('Posts Per Page', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'default' => 6,
            ]
        );

        $this->add_control(
            'advanced',
            [
                'label' => __('Advanced', 'livemesh-el-addons'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label' => __('Order By', 'livemesh-el-addons'),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'none' => __('No order', 'livemesh-el-addons'),
                    'ID' => __('Post ID', 'livemesh-el-addons'),
                    'author' => __('Author', 'livemesh-el-addons'),
                    'title' => __('Title', 'livemesh-el-addons'),
                    'date' => __('Published date', 'livemesh-el-addons'),
                    'modified' => __('Modified date', 'livemesh-el-addons'),
                    'parent' => __('By parent', 'livemesh-el-addons'),
                    'rand' => __('Random order', 'livemesh-el-addons'),
                    'comment_count' => __('Comment count', 'livemesh-el-addons'),
                    'menu_order' => __('Menu order', 'livemesh-el-addons'),
                    'post__in' => __('By include order', 'livemesh-el-addons'),
                ),
                'default' => 'date',
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => __('Order', 'livemesh-el-addons'),
                'type' => Controls_Manager::SELECT,
                'options' => array(
                    'ASC' => __('Ascending', 'livemesh-el-addons'),
                    'DESC' => __('Descending', 'livemesh-el-addons'),
                ),
                'default' => 'DESC',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_post_content',
            [
                'label' => __('Post Content', 'livemesh-el-addons'),
            ]
        );

        $this->add_control(
            'heading',
            [
                'label' => __('Heading for the grid', 'livemesh-el-addons'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __('My Portfolio', 'livemesh-el-addons'),
                'default' => __('My Portfolio', 'livemesh-el-addons'),
            ]
        );

        $this->add_control(
            'taxonomy_filter',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __('Choose the taxonomy to display and filter on.', 'livemesh-el-addons'),
                'label_block' => true,
                'description' => __('Choose the taxonomy information to display for posts/portfolio and the taxonomy that is used to filter the portfolio/post. Takes effect only if no taxonomy filters are specified when building query.', 'livemesh-el-addons'),
                'options' => lae_get_taxonomies_map(),
                'default' => 'category',
            ]
        );

        $this->add_control(
            'display_title',
            [
                'label' => __('Display posts title for the post/portfolio item?', 'livemesh-el-addons'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'label_off' => __('No', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'display_summary',
            [
                'label' => __('Display post excerpt/summary for the post/portfolio item?', 'livemesh-el-addons'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'label_off' => __('No', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_post_meta',
            [
                'label' => __('Post Meta', 'livemesh-el-addons'),
            ]
        );


        $this->add_control(
            'display_author',
            [
                'label' => __('Display post author info for the post/portfolio item?', 'livemesh-el-addons'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'label_off' => __('No', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );


        $this->add_control(
            'display_post_date',
            [
                'label' => __('Display post date info for the post item?', 'livemesh-el-addons'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'label_off' => __('No', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );


        $this->add_control(
            'display_taxonomy',
            [
                'label' => __('Display taxonomy info for the post item? Choose the right taxonomy in Post Content section above.', 'livemesh-el-addons'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'label_off' => __('No', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_settings',
            [
                'label' => __('General Settings', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_SETTINGS,
            ]
        );

        $this->add_control(
            'image_linkable',
            [
                'label' => __('Link Images to Posts?', 'livemesh-el-addons'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'label_off' => __('No', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'filterable',
            [
                'label' => __('Filterable?', 'livemesh-el-addons'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'label_off' => __('No', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'per_line',
            [
                'label' => __('Columns per row', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'default' => 3,
            ]
        );

        $this->add_control(
            'layout_mode',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __('Choose a layout for the grid', 'livemesh-el-addons'),
                'options' => array(
                    'fitRows' => __('Fit Rows', 'livemesh-el-addons'),
                    'masonry' => __('Masonry', 'livemesh-el-addons'),
                ),
                'default' => 'fitRows',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_responsive',
            [
                'label' => __('Gutter Options', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_SETTINGS,
            ]
        );

        $this->add_control(
            'heading_desktop',
            [
                'label' => __( 'Desktop', 'livemesh-el-addons' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'gutter',
            [
                'label' => __('Gutter', 'livemesh-el-addons'),
                'description' => __('Space between columns in the grid.', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'default' => 20,
                'selectors' => [
                    '{{WRAPPER}} .lae-portfolio' => 'margin-left: -{{VALUE}}px; margin-right: -{{VALUE}}px;',
                    '{{WRAPPER}} .lae-portfolio .lae-portfolio-item' => 'padding: {{VALUE}}px;',
                ],
            ]
        );

        $this->add_control(
            'heading_tablet',
            [
                'label' => __( 'Tablet', 'livemesh-el-addons' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'tablet_gutter',
            [
                'label' => __('Gutter', 'livemesh-el-addons'),
                'description' => __('Space between columns.', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'default' => 10,
                'selectors' => [
                    '(tablet-){{WRAPPER}} .lae-portfolio' => 'margin-left: -{{VALUE}}px; margin-right: -{{VALUE}}px;',
                    '(tablet-){{WRAPPER}} .lae-portfolio .lae-portfolio-item' => 'padding: {{VALUE}}px;',
                ],
            ]
        );


        $this->add_control(
            'heading_mobile',
            [
                'label' => __( 'Mobile Phone', 'livemesh-el-addons' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'mobile_gutter',
            [
                'label' => __('Gutter', 'livemesh-el-addons'),
                'description' => __('Space between columns.', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'default' => 10,
                'selectors' => [
                    '(mobile-){{WRAPPER}} .lae-portfolio' => 'margin-left: -{{VALUE}}px; margin-right: -{{VALUE}}px;',
                    '(mobile-){{WRAPPER}} .lae-portfolio .lae-portfolio-item' => 'padding: {{VALUE}}px;',
                ],
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'section_heading_styling',
            [
                'label' => __('Grid Heading', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_control(
            'heading_tag',
            [
                'label' => __( 'Heading HTML Tag', 'livemesh-el-addons' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => __( 'H1', 'livemesh-el-addons' ),
                    'h2' => __( 'H2', 'livemesh-el-addons' ),
                    'h3' => __( 'H3', 'livemesh-el-addons' ),
                    'h4' => __( 'H4', 'livemesh-el-addons' ),
                    'h5' => __( 'H5', 'livemesh-el-addons' ),
                    'h6' => __( 'H6', 'livemesh-el-addons' ),
                    'div' => __( 'div', 'livemesh-el-addons' ),
                ],
                'default' => 'h3',
            ]
        );

        $this->add_control(
            'heading_color',
            [
                'label' => __( 'Heading Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-portfolio-wrap .lae-heading' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_typography',
                'selector' => '{{WRAPPER}} .lae-portfolio-wrap .lae-heading',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_filters_styling',
            [
                'label' => __('Grid Filters', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'filter_color',
            [
                'label' => __( 'Filter Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-portfolio-wrap .lae-taxonomy-filter .lae-filter-item a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_hover_color',
            [
                'label' => __( 'Filter Hover Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-portfolio-wrap .lae-taxonomy-filter .lae-filter-item a:hover, {{WRAPPER}} .lae-portfolio-wrap .lae-taxonomy-filter .lae-filter-item.lae-active a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'filter_active_border',
            [
                'label' => __( 'Active Filter Border Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-portfolio-wrap .lae-taxonomy-filter .lae-filter-item.lae-active:after ' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'filter_typography',
                'selector' => '{{WRAPPER}} .lae-portfolio-wrap .lae-taxonomy-filter .lae-filter-item a',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_grid_thumbnail_styling',
            [
                'label' => __('Grid Thumbnail', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'heading_thumbnail_info',
            [
                'label' => __( 'Thumbnail Info Entry Title', 'livemesh-el-addons' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'title_tag',
            [
                'label' => __( 'Title HTML Tag', 'livemesh-el-addons' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => __( 'H1', 'livemesh-el-addons' ),
                    'h2' => __( 'H2', 'livemesh-el-addons' ),
                    'h3' => __( 'H3', 'livemesh-el-addons' ),
                    'h4' => __( 'H4', 'livemesh-el-addons' ),
                    'h5' => __( 'H5', 'livemesh-el-addons' ),
                    'h6' => __( 'H6', 'livemesh-el-addons' ),
                    'div' => __( 'div', 'livemesh-el-addons' ),
                ],
                'default' => 'h3',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __( 'Title Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-portfolio-wrap .lae-portfolio .lae-portfolio-item .lae-project-image .lae-image-info .lae-post-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_hover_border_color',
            [
                'label' => __( 'Title Hover Border Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-portfolio-wrap .lae-portfolio .lae-portfolio-item .lae-project-image .lae-image-info .lae-post-title a:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .lae-portfolio-wrap .lae-portfolio .lae-portfolio-item .lae-project-image .lae-image-info .lae-post-title',
            ]
        );

        $this->add_control(
            'heading_thumbnail_info_taxonomy',
            [
                'label' => __( 'Thumbnail Info Taxonomy Terms', 'livemesh-el-addons' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'thumbnail_info_tags_color',
            [
                'label' => __( 'Taxonomy Terms Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-portfolio-wrap .lae-portfolio .lae-portfolio-item .lae-project-image .lae-image-info .lae-terms, {{WRAPPER}} .lae-portfolio-wrap .lae-portfolio .lae-portfolio-item .lae-project-image .lae-image-info .lae-terms a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'tags_typography',
                'selector' => '{{WRAPPER}} .lae-portfolio-wrap .lae-portfolio .lae-portfolio-item .lae-project-image .lae-image-info .lae-terms, {{WRAPPER}} .lae-portfolio-wrap .lae-portfolio .lae-portfolio-item .lae-project-image .lae-image-info .lae-terms a',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_entry_title_styling',
            [
                'label' => __('Grid Item Entry Title', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'entry_title_tag',
            [
                'label' => __( 'Entry Title HTML Tag', 'livemesh-el-addons' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => __( 'H1', 'livemesh-el-addons' ),
                    'h2' => __( 'H2', 'livemesh-el-addons' ),
                    'h3' => __( 'H3', 'livemesh-el-addons' ),
                    'h4' => __( 'H4', 'livemesh-el-addons' ),
                    'h5' => __( 'H5', 'livemesh-el-addons' ),
                    'h6' => __( 'H6', 'livemesh-el-addons' ),
                    'div' => __( 'div', 'livemesh-el-addons' ),
                ],
                'default' => 'h3',
            ]
        );

        $this->add_control(
            'entry_title_color',
            [
                'label' => __( 'Entry Title Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-portfolio-wrap .lae-portfolio .lae-portfolio-item .entry-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'entry_title_typography',
                'selector' => '{{WRAPPER}} .lae-portfolio-wrap .lae-portfolio .lae-portfolio-item .entry-title',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_entry_summary_styling',
            [
                'label' => __('Grid Item Entry Summary', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'entry_summary_color',
            [
                'label' => __( 'Entry Summary Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-portfolio-wrap .lae-portfolio .lae-portfolio-item .entry-summary' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'entry_summary_typography',
                'selector' => '{{WRAPPER}} .lae-portfolio-wrap .lae-portfolio .lae-portfolio-item .entry-summary',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_entry_meta_styling',
            [
                'label' => __('Grid Item Entry Meta', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'heading_entry_meta',
            [
                'label' => __( 'Entry Meta', 'livemesh-el-addons' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'entry_meta_color',
            [
                'label' => __( 'Entry Meta Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-portfolio-wrap .lae-portfolio .lae-portfolio-item .lae-entry-meta span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'entry_meta_typography',
                'selector' => '{{WRAPPER}} .lae-portfolio-wrap .lae-portfolio .lae-portfolio-item .lae-entry-meta span',
            ]
        );


        $this->add_control(
            'heading_entry_meta_link',
            [
                'label' => __( 'Entry Meta Link', 'livemesh-el-addons' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'entry_meta_link_color',
            [
                'label' => __( 'Entry Meta Link Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-portfolio-wrap .lae-portfolio .lae-portfolio-item .lae-entry-meta span a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'entry_meta_link_typography',
                'selector' => '{{WRAPPER}} .lae-portfolio-wrap .lae-portfolio .lae-portfolio-item .lae-entry-meta span a',
            ]
        );

        $this->end_controls_section();

    }


    function posts_grid($loop, $settings, $taxonomies) {

        $column_style = lae_get_column_class(intval($settings['per_line'])); ?>

        <?php $current_page = get_queried_object_id(); ?>

        <?php while ($loop->have_posts()) : $loop->the_post(); ?>

            <?php $post_id = get_the_ID(); ?>

            <?php
            if ($post_id === $current_page)
                continue; // skip current page since we can run into infinite loop when users choose All option in build query
            ?>

            <?php
            $style = '';
            foreach ($taxonomies as $taxonomy) {
                $terms = get_the_terms($post_id, $taxonomy);
                if (!empty($terms) && !is_wp_error($terms)) {
                    foreach ($terms as $term) {
                        $style .= ' term-' . $term->term_id;
                    }
                }
            }
            ?>

            <div data-id="id-<?php echo $post_id; ?>"
                 class="lae-portfolio-item <?php echo $style; ?> <?php echo $column_style; ?>">

                <article id="post-<?php echo $post_id; ?>" <?php post_class(); ?>>

                    <?php if ($thumbnail_exists = has_post_thumbnail()): ?>

                        <div class="lae-project-image">

                            <?php if ($settings['image_linkable'] == 'yes'): ?>

                                <a href="<?php the_permalink(); ?>"> <?php the_post_thumbnail('large'); ?> </a>

                            <?php else: ?>

                                <?php the_post_thumbnail('large'); ?>

                            <?php endif; ?>

                            <div class="lae-image-info">

                                <div class="lae-entry-info">

                                    <?php the_title('<'. $settings['title_tag']. ' class="lae-post-title"><a href="' . get_permalink() . '" title="' . get_the_title() . '"
                                               rel="bookmark">', '</a></'. $settings['title_tag'] . '>'); ?>

                                    <?php echo lae_get_taxonomy_info($taxonomies); ?>

                                </div>

                            </div>

                        </div>

                    <?php endif; ?>

                    <?php if (($settings['display_title'] == 'yes') || ($settings['display_summary'] == 'yes')) : ?>

                        <div
                                class="lae-entry-text-wrap <?php echo($thumbnail_exists ? '' : ' nothumbnail'); ?>">

                            <?php if ($settings['display_title'] == 'yes') : ?>

                                <?php the_title('<'. $settings['entry_title_tag']. ' class="entry-title"><a href="' . get_permalink() . '" title="' . get_the_title() . '"
                                               rel="bookmark">', '</a></'. $settings['entry_title_tag'] . '>'); ?>

                            <?php endif; ?>

                            <?php if (($settings['display_post_date'] == 'yes') || ($settings['display_author'] == 'yes') || ($settings['display_taxonomy'] == 'yes')) : ?>

                                <div class="lae-entry-meta">

                                    <?php if ($settings['display_author'] == 'yes'): ?>

                                        <?php echo lae_entry_author(); ?>

                                    <?php endif; ?>

                                    <?php if ($settings['display_post_date'] == 'yes'): ?>

                                        <?php echo lae_entry_published(); ?>

                                    <?php endif; ?>

                                    <?php if ($settings['display_taxonomy'] == 'yes'): ?>

                                        <?php echo lae_get_taxonomy_info($taxonomies); ?>

                                    <?php endif; ?>

                                </div>

                            <?php endif; ?>

                            <?php if ($settings['display_summary'] == 'yes') : ?>

                                <div class="entry-summary">

                                    <?php the_excerpt(); ?>

                                </div>

                            <?php endif; ?>

                        </div>

                    <?php endif; ?>

                </article><!-- .hentry -->

            </div>

        <?php endwhile; ?>

        <?php wp_reset_postdata(); ?>

        <?php

    }

    protected function render() {

        $settings = $this->get_settings();

        // Use the processed post selector query to find posts.
        $query_args = lae_build_query_args($settings);

        $loop = new \WP_Query($query_args);

        // Loop through the posts and do something with them.
        if ($loop->have_posts()) :

            // Check if any taxonomy filter has been applied
            list($chosen_terms, $taxonomies) = lae_get_chosen_terms($settings['tax_query']);
            if (empty($chosen_terms))
                $taxonomies[] = $settings['taxonomy_filter'];

            ?>

            <div class="lae-portfolio-wrap lae-gapless-grid">

                <?php if (!empty($settings['heading']) || $settings['filterable'] == 'yes'): ?>

                    <?php $header_class = (trim($settings['heading']) === '') ? ' lae-no-heading' : ''; ?>

                    <div class="lae-portfolio-header <?php echo $header_class; ?>">

                        <?php if (!empty($settings['heading'])) : ?>

                            <<?php echo $settings['heading_tag']; ?> class="lae-heading"><?php echo wp_kses_post($settings['heading']); ?></<?php echo $settings['heading_tag']; ?>>

                        <?php endif; ?>

                        <?php

                        if ($settings['filterable'] == 'yes')
                            echo lae_get_taxonomy_terms_filter($taxonomies, $chosen_terms);

                        ?>

                    </div>

                <?php endif; ?>

                <div id="lae-portfolio-<?php echo uniqid(); ?>"
                     class="lae-portfolio js-isotope lae-<?php echo esc_attr($settings['layout_mode']); ?> lae-grid-container"
                     data-isotope-options='{ "itemSelector": ".lae-portfolio-item", "layoutMode": "<?php echo esc_attr($settings['layout_mode']); ?>" }'>

                    <?php $this->posts_grid($loop, $settings, $taxonomies); ?>

                </div><!-- Isotope items -->

            </div>

            <?php

        endif;
    }

    protected function content_template() {
    }

}