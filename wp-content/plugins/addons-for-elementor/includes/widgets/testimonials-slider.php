<?php

/*
Widget Name: Livemesh Testimonials Slider
Description: Display responsive touch friendly slider of testimonials from clients/customers.
Author: LiveMesh
Author URI: https://www.livemeshthemes.com
*/

namespace LivemeshAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class LAE_Testimonials_Slider_Widget extends Widget_Base {

    public function get_name() {
        return 'lae-testimonials-slider';
    }

    public function get_title() {
        return __('Livemesh Testimonials Slider', 'livemesh-el-addons');
    }

    public function get_icon() {
        return 'eicon-blockquote';
    }

    public function get_categories() {
        return array('livemesh-addons');
    }

    public function get_script_depends() {
        return [
            'lae-widgets-scripts',
            'lae-frontend-scripts',
            'jquery-flexslider'
        ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_testimonials_slider',
            [
                'label' => __('Testimonials Slider', 'livemesh-el-addons'),
            ]
        );

        $this->add_control(
            'testimonials',
            [
                'label' => __('Testimonials', 'livemesh-el-addons'),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'client_name' => __('Customer #1', 'livemesh-el-addons'),
                        'credentials' => __('CEO, Invision Inc.', 'livemesh-el-addons'),
                        'testimonial_text' => __('I am testimonial text. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'livemesh-el-addons'),
                    ],
                    [
                        'client_name' => __('Customer #2', 'livemesh-el-addons'),
                        'credentials' => __('Lead Developer, Automattic Inc', 'livemesh-el-addons'),
                        'testimonial_text' => __('I am testimonial text. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'livemesh-el-addons'),
                    ],
                    [
                        'client_name' => __('Customer #3', 'livemesh-el-addons'),
                        'credentials' => __('Store Manager, Walmart Inc', 'livemesh-el-addons'),
                        'testimonial_text' => __('I am testimonial text. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'livemesh-el-addons'),
                    ],
                ],
                'fields' => [
                    [
                        'name' => 'client_name',
                        'label' => __('Name', 'livemesh-el-addons'),
                        'type' => Controls_Manager::TEXT,
                        'description' => __('The client or customer name for the testimonial', 'livemesh-el-addons'),
                    ],
                    [
                        'name' => 'credentials',
                        'label' => __('Client Details', 'livemesh-el-addons'),
                        'type' => Controls_Manager::TEXT,
                        'description' => __('The details of the client/customer like company name, position held, company URL etc. HTML accepted.', 'livemesh-el-addons'),
                    ],

                    [
                        'name' => 'client_image',
                        'label' => __('Customer/Client Image', 'livemesh-el-addons'),
                        'type' => Controls_Manager::MEDIA,
                        'default' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                        'label_block' => true,
                    ],

                    [
                        'name' => 'testimonial_text',
                        'label' => __('Testimonials Text', 'livemesh-el-addons'),
                        'type' => Controls_Manager::WYSIWYG,
                        'description' => __('What your customer/client had to say', 'livemesh-el-addons'),
                        'show_label' => false,
                    ],

                ],
                'title_field' => '{{{ client_name }}}',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_settings',
            [
                'label' => __('Slider Settings', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_SETTINGS,
            ]
        );

        $this->add_control(
            'slide_animation',
            [
                'label' => __( 'Animation', 'livemesh-el-addons' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'slide',
                'options' => [
                    'slide' => __( 'Slide', 'livemesh-el-addons' ),
                    'fade' => __( 'Fade', 'livemesh-el-addons' ),
                ],
            ]
        );

        $this->add_control(
            'direction',
            [
                'label' => __( 'Direction', 'livemesh-el-addons' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'horizontal',
                'options' => [
                    'horizontal' => __( 'Horizontal', 'livemesh-el-addons' ),
                    'vertical' => __( 'Vertical', 'livemesh-el-addons' ),
                ],
            ]
        );

        $this->add_control(
            'slideshow_speed',
            [
                'label' => __( 'Slideshow Speed', 'livemesh-el-addons' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 5000,
            ]
        );


        $this->add_control(
            'animation_speed',
            [
                'label' => __( 'Animation Speed', 'livemesh-el-addons' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 600,
            ]
        );

        $this->add_control(
            'pause_on_hover',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('No', 'livemesh-el-addons'),
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'separator' => 'before',
                'default' => 'yes',
                'label' => __('Pause on Hover?', 'livemesh-el-addons'),
                'description' => __('Should the slider pause on mouse hover over the slider.', 'livemesh-el-addons'),
            ]
        );

        $this->add_control(
            'pause_on_action',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('No', 'livemesh-el-addons'),
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'no',
                'label' => __('Pause slider on action?', 'livemesh-el-addons'),
                'description' => __('Should the slideshow pause once user initiates an action using navigation/direction controls.', 'livemesh-el-addons'),
            ]
        );

        $this->add_control(
            'direction_nav',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('No', 'livemesh-el-addons'),
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'separator' => 'before',
                'default' => 'yes',
                'label' => __('Direction Navigation?', 'livemesh-el-addons'),
                'description' => __('Should the slider have direction navigation?', 'livemesh-el-addons'),
            ]
        );


        $this->add_control(
            'control_nav',
            [
                'type' => Controls_Manager::SWITCHER,
                'label_off' => __('No', 'livemesh-el-addons'),
                'label_on' => __('Yes', 'livemesh-el-addons'),
                'return_value' => 'yes',
                'default' => 'no',
                'label' => __('Navigation Controls?', 'livemesh-el-addons'),
                'description' => __('Should the slider have navigation controls?', 'livemesh-el-addons'),
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_testimonials_thumbnail',
            [
                'label' => __( 'Author Thumbnail', 'livemesh-el-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'show_label' => false,
            ]
        );

        $this->add_control(
            'thumbnail_border_radius',
            [
                'label' => __('Author Thumbnail Border Radius', 'livemesh-el-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .lae-testimonials-slider .lae-testimonial-user .lae-image-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'thumbnail_size',
            [
                'label' => __('Author Thumbnail Size', 'livemesh-el-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ '%', 'px' ],
                'range' => [
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 50,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .lae-testimonials-slider .lae-testimonial-user .lae-image-wrapper img' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_testimonials_text',
            [
                'label' => __('Author Testimonial', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __( 'Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-testimonials-slider .lae-testimonial-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .lae-testimonials-slider .lae-testimonial-text',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_testimonials_author_name',
            [
                'label' => __( 'Author Name', 'livemesh-el-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
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
                'default' => 'h4',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __( 'Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-testimonials-slider .lae-testimonial-user .lae-text .lae-author-name' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .lae-testimonials-slider .lae-testimonial-user .lae-text .lae-author-name',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_testimonials_author_credentials',
            [
                'label' => __('Author Credentials', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'credential_color',
            [
                'label' => __( 'Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-testimonials-slider .lae-testimonial-user .lae-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'credential_typography',
                'selector' => '{{WRAPPER}} .lae-testimonials-slider .lae-testimonial-user .lae-text',
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'section_quote_icon_styling',
            [
                'label' => __('Quote Icon', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'quote_icon_size',
            [
                'label' => __('Icon size in pixels', 'livemesh-el-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em' ],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 128,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .lae-testimonials-slider .lae-testimonial-text i' => 'font-size: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_control(
            'quote_icon_color',
            [
                'label' => __('Icon Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .lae-testimonials-slider .lae-testimonial-text i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();


    }

    protected function render() {

        $settings = $this->get_settings();

        $slider_options = [
            'slide_animation' => $settings['slide_animation'],
            'direction' => $settings['direction'],
            'slideshow_speed' => absint( $settings['slideshow_speed'] ),
            'animation_speed' => absint( $settings['animation_speed'] ),
            'control_nav' => ( 'yes' === $settings['control_nav'] ),
            'direction_nav' => ( 'yes' === $settings['direction_nav'] ),
            'pause_on_hover' => ( 'yes' === $settings['pause_on_hover'] ),
            'pause_on_action' => ( 'yes' === $settings['pause_on_action'] )
        ];
        ?>

        <div class="lae-testimonials-slider lae-flexslider lae-container"  data-settings='<?php echo wp_json_encode($slider_options); ?>'>

            <div class="lae-slides">

                <?php foreach ($settings['testimonials'] as $testimonial) : ?>

                <div class="lae-slide lae-testimonial-wrapper">

                    <div class="lae-testimonial">

                        <div class="lae-testimonial-text">

                            <i class="lae-icon-quote"></i>

                            <?php echo $this->parse_text_editor($testimonial['testimonial_text']); ?>

                        </div>

                        <div class="lae-testimonial-user">

                            <div class="lae-image-wrapper">

                                <?php $client_image = $testimonial['client_image']; ?>

                                <?php if (!empty($client_image)): ?>

                                    <?php echo wp_get_attachment_image($client_image['id'], 'thumbnail', false, array('class' => 'lae-image full')); ?>

                                <?php endif; ?>

                            </div>

                            <div class="lae-text">

                                <<?php echo $settings['title_tag']; ?> class="lae-author-name"><?php echo esc_html($testimonial['client_name']) ?></<?php echo $settings['title_tag']; ?>>

                            <div class="lae-author-credentials"><?php echo wp_kses_post($testimonial['credentials']); ?></div>

                        </div>

                    </div>

                </div>

            </div>

            <?php endforeach; ?>

        </div>

        </div>

        <?php
    }

    protected function content_template() {
    }

}