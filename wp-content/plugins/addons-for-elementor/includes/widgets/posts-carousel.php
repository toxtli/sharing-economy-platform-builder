<?php

/*
Widget Name: Livemesh Posts Carousel
Description: Display blog posts or custom post types as a carousel.
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

class LAE_Posts_Carousel_Widget extends Widget_Base {

    public function get_name() {
        return 'lae-posts-carousel';
    }

    public function get_title() {
        return __('Livemesh Posts Carousel', 'livemesh-el-addons');
    }

    public function get_icon() {
        return 'eicon-posts-carousel';
    }

    public function get_categories() {
        return array('livemesh-addons');
    }

    public function get_script_depends() {
        return [
            'lae-widgets-scripts',
            'lae-frontend-scripts',
            'slick',
        ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_posts_carousel',
            [
                'label' => __('Posts Query', 'livemesh-el-addons'),
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
            'taxonomy_chosen',
            [
                'label' => __('Choose the taxonomy to display info.', 'livemesh-el-addons'),
                'description' => __('Choose the taxonomy to use for display of taxonomy information for posts/custom post types.', 'livemesh-el-addons'),
                'type' => Controls_Manager::SELECT,
                'label_block' => true,
                'default' => 'category',
                'options' => lae_get_taxonomies_map(),
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
            'display_title',
            [
                'label' => __('Display posts title below the post item?', 'livemesh-el-addons'),
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
                'label' => __('Display post excerpt/summary below the post item?', 'livemesh-el-addons'),
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
                'label' => __('Display post author info below the post item?', 'livemesh-el-addons'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'label_off' => __('No', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );


        $this->add_control(
            'display_post_date',
            [
                'label' => __('Display post date info below the post item?', 'livemesh-el-addons'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'label_off' => __('No', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );


        $this->add_control(
            'display_taxonomy',
            [
                'label' => __('Display taxonomy info below the post item?', 'livemesh-el-addons'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'label_off' => __('No', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_carousel_settings',
            [
                'label' => __('Carousel Settings', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_SETTINGS,
            ]
        );

        $this->add_control(
            'arrows',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('No', 'livemesh-el-addons'),
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'yes',
                'label' => __('Prev/Next Arrows?', 'livemesh-el-addons'),
            ]
        );


        $this->add_control(
            'dots',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('No', 'livemesh-el-addons'),
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'no',
                'label' => __('Show dot indicators for navigation?', 'livemesh-el-addons'),
            ]
        );

        $this->add_control(
            'pause_on_hover',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('No', 'livemesh-el-addons'),
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'yes',
                'label' => __('Pause on Hover?', 'livemesh-el-addons'),
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('No', 'livemesh-el-addons'),
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'no',
                'label' => __('Autoplay?', 'livemesh-el-addons'),
                'description' => __('Should the carousel autoplay as in a slideshow.', 'livemesh-el-addons'),
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label' => __('Autoplay speed in ms', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3000,
            ]
        );


        $this->add_control(
            'animation_speed',
            [
                'label' => __('Autoplay animation speed in ms', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'default' => 300,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_responsive',
            [
                'label' => __('Responsive Options', 'livemesh-el-addons'),
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
                'description' => __('Space between columns.', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'default' => 10,
                'selectors' => [
                    '{{WRAPPER}} .lae-posts-carousel .lae-posts-carousel-item' => 'padding: {{VALUE}}px;',
                ],
            ]
        );

        $this->add_control(
            'display_columns',
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
            'scroll_columns',
            [
                'label' => __('Columns to scroll', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'default' => 3,
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
                    '(tablet-){{WRAPPER}} .lae-posts-carousel .lae-posts-carousel-item' => 'padding: {{VALUE}}px;',
                ],
            ]
        );


        $this->add_control(
            'tablet_display_columns',
            [
                'label' => __('Columns per row', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'default' => 2,
            ]
        );

        $this->add_control(
            'tablet_scroll_columns',
            [
                'label' => __('Columns to scroll', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'default' => 2,
            ]
        );

        $this->add_control(
            'tablet_width',
            [
                'label' => __('Tablet Resolution', 'livemesh-el-addons'),
                'description' => __('The resolution to treat as a tablet resolution.', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'default' => 800,
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
                'label' => __('Mobile Gutter', 'livemesh-el-addons'),
                'description' => __('Space between columns.', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'default' => 10,
                'selectors' => [
                    '(mobile-){{WRAPPER}} .lae-posts-carousel .lae-posts-carousel-item' => 'padding: {{VALUE}}px;',
                ],
            ]
        );

        $this->add_control(
            'mobile_display_columns',
            [
                'label' => __('Columns per row', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 3,
                'step' => 1,
                'default' => 1,
            ]
        );

        $this->add_control(
            'mobile_scroll_columns',
            [
                'label' => __('Columns to scroll', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 3,
                'step' => 1,
                'default' => 1,
            ]
        );

        $this->add_control(
            'mobile_width',
            [
                'label' => __('Mobile Resolution', 'livemesh-el-addons'),
                'description' => __('The resolution to treat as a mobile resolution.', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'default' => 480,
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'section_carousel_item_thumbnail_styling',
            [
                'label' => __('Post Thumbnail', 'livemesh-el-addons'),
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
                    '{{WRAPPER}} .lae-posts-carousel .lae-posts-carousel-item .lae-project-image .lae-image-info .lae-post-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_hover_border_color',
            [
                'label' => __( 'Title Hover Border Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-posts-carousel .lae-posts-carousel-item .lae-project-image .lae-image-info .lae-post-title a:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .lae-posts-carousel .lae-posts-carousel-item .lae-project-image .lae-image-info .lae-post-title',
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
                    '{{WRAPPER}} .lae-posts-carousel .lae-posts-carousel-item .lae-project-image .lae-image-info .lae-terms, {{WRAPPER}} .lae-posts-carousel .lae-posts-carousel-item .lae-project-image .lae-image-info .lae-terms a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'tags_typography',
                'selector' => '{{WRAPPER}} .lae-posts-carousel .lae-posts-carousel-item .lae-project-image .lae-image-info .lae-terms, {{WRAPPER}} .lae-posts-carousel .lae-posts-carousel-item .lae-project-image .lae-image-info .lae-terms a',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_entry_title_styling',
            [
                'label' => __('Post Entry Title', 'livemesh-el-addons'),
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
                    '{{WRAPPER}} .lae-posts-carousel .lae-posts-carousel-item .entry-title a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'entry_title_typography',
                'selector' => '{{WRAPPER}} .lae-posts-carousel .lae-posts-carousel-item .entry-title',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_entry_summary_styling',
            [
                'label' => __('Post Entry Summary', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'entry_summary_color',
            [
                'label' => __( 'Entry Summary Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-posts-carousel .lae-posts-carousel-item .entry-summary' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'entry_summary_typography',
                'selector' => '{{WRAPPER}} .lae-posts-carousel .lae-posts-carousel-item .entry-summary',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_entry_meta_styling',
            [
                'label' => __('Post Entry Meta', 'livemesh-el-addons'),
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
                    '{{WRAPPER}} .lae-posts-carousel .lae-posts-carousel-item .lae-entry-meta span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'entry_meta_typography',
                'selector' => '{{WRAPPER}} .lae-posts-carousel .lae-posts-carousel-item .lae-entry-meta span',
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
                    '{{WRAPPER}} .lae-posts-carousel .lae-posts-carousel-item .lae-entry-meta span a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'entry_meta_link_typography',
                'selector' => '{{WRAPPER}} .lae-posts-carousel .lae-posts-carousel-item .lae-entry-meta span a',
            ]
        );


    }

    protected function render() {

        $settings = $this->get_settings();
        $taxonomies = array();

        $carousel_settings = [
            'arrows' => ('yes' === $settings['arrows']),
            'dots' => ('yes' === $settings['dots']),
            'autoplay' => ('yes' === $settings['autoplay']),
            'autoplay_speed' => absint($settings['autoplay_speed']),
            'animation_speed' => absint($settings['animation_speed']),
            'pause_on_hover' => ('yes' === $settings['pause_on_hover']),
        ];

        $responsive_settings = [
            'display_columns' => $settings['display_columns'],
            'scroll_columns' => $settings['scroll_columns'],
            'gutter' => $settings['gutter'],
            'tablet_width' => $settings['tablet_width'],
            'tablet_display_columns' => $settings['tablet_display_columns'],
            'tablet_scroll_columns' => $settings['tablet_scroll_columns'],
            'tablet_gutter' => $settings['tablet_gutter'],
            'mobile_width' => $settings['mobile_width'],
            'mobile_display_columns' => $settings['mobile_display_columns'],
            'mobile_scroll_columns' => $settings['mobile_scroll_columns'],
            'mobile_gutter' => $settings['mobile_gutter'],

        ];

        $carousel_settings = array_merge($carousel_settings, $responsive_settings);

        // Use the processed post selector query to find posts.
        $query_args = lae_build_query_args($settings);

        $loop = new \WP_Query($query_args);

        // Loop through the posts and do something with them.
        if ($loop->have_posts()) : ?>

            <div id="lae-posts-carousel-<?php echo uniqid(); ?>"
                 class="lae-posts-carousel lae-container"
                 data-settings='<?php echo wp_json_encode($carousel_settings); ?>'>

                <?php $taxonomies[] = $settings['taxonomy_chosen']; ?>

                <?php while ($loop->have_posts()) : $loop->the_post(); ?>

                    <div data-id="id-<?php the_ID(); ?>" class="lae-posts-carousel-item">

                        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

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

                                <div class="lae-entry-text-wrap <?php echo($thumbnail_exists ? '' : ' nothumbnail'); ?>">

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

                        </article>
                        <!-- .hentry -->

                    </div><!--.lae-posts-carousel-item -->

                <?php endwhile; ?>

                <?php wp_reset_postdata(); ?>

            </div> <!-- .lae-posts-carousel -->

        <?php endif; ?>

        <?php
    }

    protected function content_template() {
    }

}