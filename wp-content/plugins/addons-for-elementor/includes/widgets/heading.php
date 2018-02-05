<?php

/*
Widget Name: Livemesh Heading
Description: Display one or more heading depicting a percentage value in a multi-column grid.
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


class LAE_Heading_Widget extends Widget_Base {

    public function get_name() {
        return 'lae-heading';
    }

    public function get_title() {
        return __('Livemesh Heading', 'livemesh-el-addons');
    }

    public function get_icon() {
        return 'eicon-text-area';
    }

    public function get_categories() {
        return array('livemesh-addons');
    }

    public function get_script_depends() {
        return [
            'lae-widgets-scripts',
            'lae-frontend-scripts',
            'waypoints'
        ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_heading',
            [
                'label' => __('Heading', 'livemesh-el-addons'),
            ]
        );


        $this->add_control(

            'style', [
                'type' => Controls_Manager::SELECT,
                'label' => __('Choose Style', 'livemesh-el-addons'),
                'default' => 'style1',
                'options' => [
                    'style1' => __('Style 1', 'livemesh-el-addons'),
                    'style2' => __('Style 2', 'livemesh-el-addons'),
                    'style3' => __('Style 3', 'livemesh-el-addons'),
                ],
            ]
        );

        $this->add_control(
            'heading',
            [
                'type' => Controls_Manager::TEXT,
                'label' => __('Heading Title', 'livemesh-el-addons'),
                'label_block' => true,
                'separator' => 'before',
                'default' => __('Heading Title', 'livemesh-el-addons'),
            ]
        );

        $this->add_control(
            'subtitle',
            [
                'type' => Controls_Manager::TEXT,
                'label' => __('Subheading', 'livemesh-el-addons'),
                'label_block' => true,
                'description' => __('A subtitle displayed above the title heading.', 'livemesh-el-addons'),
                'condition' => [
                    'style' => 'style2',
                ],
            ]
        );

        $this->add_control(
            'short_text',
            [
                'type' => 'textarea',
                'label' => __('Short Text', 'livemesh-el-addons'),
                'description' => __('Short text generally displayed below the heading title.', 'livemesh-el-addons'),
                'condition' => [
                    'style' => ['style1', 'style2']
                ],
            ]
        );

        $this->add_control(
            'heading_settings',
            [
                'label' => __( 'Settings', 'livemesh-el-addons' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'align',
            [
                'label' => __('Alignment', 'livemesh-el-addons'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'livemesh-el-addons'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'livemesh-el-addons'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'livemesh-el-addons'),
                        'icon' => 'fa fa-align-right',
                    ],
                    'justify' => [
                        'title' => __('Justified', 'livemesh-el-addons'),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'default' => 'center',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_styling',
            [
                'label' => __('Title', 'livemesh-el-addons'),
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
                'default' => 'h3',
            ]
        );

        $this->add_control(
            'heading_color',
            [
                'label' => __('Heading Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .lae-heading .lae-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_typography',
                'label' => __('Typography', 'livemesh-el-addons'),
                'selector' => '{{WRAPPER}} .lae-heading .lae-title',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_subtitle',
            [
                'label' => __('Subtitle', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'subtitle_color',
            [
                'label' => __( 'Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-heading .lae-subtitle' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'subtitle_typography',
                'selector' => '{{WRAPPER}} .lae-heading .lae-subtitle',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_short_text',
            [
                'label' => __('Short Text', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __( 'Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-heading .lae-text' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .lae-heading .lae-text',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {

        $settings = $this->get_settings();

        ?>

        <div class="lae-heading lae-<?php echo $settings['style']; ?> lae-align<?php echo $settings['align']; ?>">

            <?php if ($settings['style'] == 'style2' && !empty($settings['subtitle'])): ?>

                <div class="lae-subtitle"><?php echo esc_html($settings['subtitle']); ?></div>

            <?php endif; ?>

            <<?php echo esc_html($settings['title_tag']); ?> class="lae-title"><?php echo wp_kses_post($settings['heading']); ?></<?php echo esc_html($settings['title_tag']); ?>>

            <?php if ($settings['style'] != 'style3' && !empty($settings['short_text'])): ?>

                <p class="lae-text"><?php echo wp_kses_post($settings['short_text']); ?></p>

            <?php endif; ?>

        </div>

        <?php
    }

    protected function content_template() {
    }

}