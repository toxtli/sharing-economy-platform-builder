<?php

/*
Widget Name: Livemesh Carousel
Description: Display a list of custom HTML content as a carousel.
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


class LAE_Carousel_Widget extends Widget_Base {

    public function get_name() {
        return 'lae-carousel';
    }

    public function get_title() {
        return __('Livemesh Carousel', 'livemesh-el-addons');
    }

    public function get_icon() {
        return 'eicon-carousel';
    }

    public function get_categories() {
        return array('livemesh-addons');
    }

    public function get_script_depends() {
        return [
            'lae-widgets-scripts',
            'lae-frontend-scripts',
            'slick'
        ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_carousel',
            [
                'label' => __('Carousel', 'livemesh-el-addons'),
            ]
        );


        $this->add_control(
            'carousel_heading',
            [
                'label' => __('HTML Elements', 'livemesh-el-addons'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'elements',
            [
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'element_title' => 'Aliquam lorem ante',
                        'element_content' => 'Suspendisse potenti. Praesent ac sem eget est egestas volutpat. Fusce neque. In hac habitasse platea dictumst. Morbi nec metus.

Sed magna purus, fermentum eu, tincidunt eu, varius ut, felis. Phasellus leo dolor, tempus non, auctor et, hendrerit quis, nisi. Vestibulum volutpat pretium libero. Nullam accumsan lorem in dui. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.

In consectetuer turpis ut velit. Phasellus leo dolor, tempus non, auctor et, hendrerit quis, nisi. Vivamus laoreet. Praesent ac massa at ligula laoreet iaculis. Cras non dolor.',
                    ],
                    [
                        'element_title' => 'Pellentesque commodo eros',
                        'element_content' => 'In hac habitasse platea dictumst. Ut a nisl id ante tempus hendrerit. Morbi mattis ullamcorper velit. Nullam sagittis. Sed a libero.

Donec mollis hendrerit risus. Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Praesent egestas neque eu enim. Donec mollis hendrerit risus.

Donec orci lectus, aliquam ut, faucibus non, euismod id, nulla. Aenean imperdiet. Nulla consequat massa quis enim. Aenean imperdiet. Fusce commodo aliquam arcu.',
                    ],
                    [
                        'element_title' => 'Aenean commodo ligula',
                        'element_content' => 'Fusce convallis metus id felis luctus adipiscing. Suspendisse faucibus, nunc et pellentesque egestas, lacus ante convallis tellus, vitae iaculis lacus elit id tortor. Sed lectus. Etiam vitae tortor. Praesent adipiscing.

Sed in libero ut nibh placerat accumsan. Pellentesque ut neque. Donec id justo. Phasellus gravida semper nisi. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.

Vestibulum dapibus nunc ac augue. Nam at tortor in tellus interdum sagittis. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Sed lectus. Quisque ut nisi.',
                    ],
                    [
                        'element_title' => 'Suspendisse pulvinar augue',
                        'element_content' => 'Sed aliquam ultrices mauris. Sed mollis, eros et ultrices tempus, mauris ipsum aliquam libero, non adipiscing dolor urna a orci. Etiam feugiat lorem non metus. In turpis. Morbi mattis ullamcorper velit.

Sed consequat, leo eget bibendum sodales, augue velit cursus nunc, quis gravida magna mi a libero. Maecenas nec odio et ante tincidunt tempus. Ut leo. Praesent vestibulum dapibus nibh. Sed aliquam ultrices mauris.

Nunc interdum lacus sit amet orci. Nunc interdum lacus sit amet orci. Vestibulum facilisis, purus nec pulvinar iaculis, ligula mi congue nunc, vitae euismod ligula urna in dolor. Curabitur at lacus ac velit ornare lobortis. Fusce vulputate eleifend sapien.',
                    ],
                    [
                        'element_title' => 'Aenean tellus metus',
                        'element_content' => 'Vivamus elementum semper nisi. Praesent adipiscing. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Fusce vel dui.

Sed fringilla mauris sit amet nibh. Nunc nonummy metus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Phasellus blandit leo ut odio. Praesent egestas neque eu enim.

Fusce risus nisl, viverra et, tempor et, pretium in, sapien. Vestibulum turpis sem, aliquet eget, lobortis pellentesque, rutrum eu, nisl. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Sed fringilla mauris sit amet nibh. Pellentesque ut neque.',
                    ],
                ],
                'fields' => [
                    [
                        'name' => 'element_title',
                        'type' => Controls_Manager::TEXT,
                        'label_block' => true,
                        'label' => __('Element Title & HTML Content', 'livemesh-el-addons'),
                        'description' => __('The title to identify the HTML element', 'livemesh-el-addons'),
                    ],
                    [
                        'name' => 'element_content',
                        'label' => __('HTML Element Content', 'livemesh-el-addons'),
                        'type' => Controls_Manager::WYSIWYG,
                        'default' => __('The HTML content for the element', 'livemesh-el-addons'),
                        'show_label' => false,
                    ],

                ],
                'title_field' => '{{{ element_title }}}',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_settings',
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
                'separator' => 'before',
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
                'separator' => 'before',
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
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => 0,
                    'right' => 10,
                    'bottom' => 0,
                    'left' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .lae-carousel .lae-carousel-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => 0,
                    'right' => 10,
                    'bottom' => 0,
                    'left' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '(tablet-){{WRAPPER}} .lae-carousel .lae-carousel-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                'label' => __('Gutter', 'livemesh-el-addons'),
                'description' => __('Space between columns.', 'livemesh-el-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => 0,
                    'right' => 5,
                    'bottom' => 0,
                    'left' => 5,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '(mobile-){{WRAPPER}} .lae-carousel .lae-carousel-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
            'section_carousel_style',
            [
                'label' => __( 'Carousel', 'livemesh-el-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'show_label' => false,
            ]
        );

        $this->add_control(
            'heading_content',
            [
                'label' => __( 'Content', 'livemesh-el-addons' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label' => __( 'Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-carousel .lae-carousel-item' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_bg_color',
            [
                'label' => __( 'Background Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-carousel .lae-carousel-item' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_padding',
            [
                'label' => __('Padding', 'livemesh-el-addons'),
                'description' => __('Padding for the columns.', 'livemesh-el-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => 5,
                    'right' => 5,
                    'bottom' => 5,
                    'left' => 5,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .lae-carousel .lae-carousel-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'selector' => '{{WRAPPER}} .lae-carousel .lae-carousel-item',
            ]
        );
    }

    protected function render() {

        $settings = $this->get_settings();

        $elements = $settings['elements'];

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
        ?>

        <?php if (!empty($elements)) : ?>

            <div id="lae-carousel-<?php echo uniqid(); ?>"
                 class="lae-carousel lae-container"
                 data-settings='<?php echo wp_json_encode($carousel_settings); ?>'>

                <?php foreach ($elements as $element) : ?>

                    <div class="lae-carousel-item">

                        <?php echo $this->parse_text_editor($element['element_content']); ?>

                    </div><!--.lae-carousel-item -->

                <?php endforeach; ?>

            </div> <!-- .lae-carousel -->

        <?php endif; ?>

        <?php
    }

    protected function content_template() {
    }

}