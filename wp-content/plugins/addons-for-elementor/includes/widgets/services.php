<?php

/*
Widget Name: Livemesh Services
Description: Capture services in a multi-column grid.
Author: LiveMesh
Author URI: https://www.livemeshthemes.com
*/

namespace LivemeshAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\utils;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class LAE_Services_Widget extends Widget_Base {

    public function get_name() {
        return 'lae-services';
    }

    public function get_title() {
        return __('Livemesh Services', 'livemesh-el-addons');
    }

    public function get_icon() {
        return 'fa fa-clone';
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
            'section_services',
            [
                'label' => __('Services', 'livemesh-el-addons'),
            ]
        );

        $this->add_control(

            'style',
            [
                'type' => Controls_Manager::SELECT,
                'label' => __('Choose Style', 'livemesh-el-addons'),
                'default' => 'style1',
                'options' => [
                    'style1' => __('Style 1', 'livemesh-el-addons'),
                    'style2' => __('Style 2', 'livemesh-el-addons'),
                    'style3' => __('Style 3', 'livemesh-el-addons'),
                ],
                'prefix_class' => 'lae-services-',
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
            'services',
            [
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'service_title' => __('Web Design', 'livemesh-el-addons'),
                        'icon_type' => 'icon',
                        'icon' => 'fa fa-bell-o',
                        'service_excerpt' => 'Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo. Donec venenatis vulputate lorem. In hac habitasse aliquam.',
                    ],
                    [
                        'service_title' => __('SEO Services', 'livemesh-el-addons'),
                        'icon_type' => 'icon',
                        'icon' => 'fa fa-laptop',
                        'service_excerpt' => 'Suspendisse nisl elit, rhoncus eget, elementum ac, condimentum eget, diam. Phasellus nec sem in justo pellentesque facilisis platea dictumst.',
                    ],
                    [
                        'service_title' => __('Brand Marketing', 'livemesh-el-addons'),
                        'icon_type' => 'icon',
                        'icon' => 'fa fa-toggle-off',
                        'service_excerpt' => 'Nunc egestas, augue at pellentesque laoreet, felis eros vehicula leo, at malesuada velit leo quis pede. Etiam ut purus mattis mauris sodales.',
                    ],
                ],
                'fields' => [

                    [
                        'name' => 'service_title',
                        'label' => __('Service Title', 'livemesh-el-addons'),
                        'type' => Controls_Manager::TEXT,
                        'label_block' => true,
                        'default' => __('My service title', 'livemesh-el-addons'),
                    ],
                    [
                        'name' => 'icon_type',
                        'label' => __('Icon Type', 'livemesh-el-addons'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'icon',
                        'options' => [
                            'none' => __('None', 'livemesh-el-addons'),
                            'icon' => __('Icon', 'livemesh-el-addons'),
                            'icon_image' => __('Icon Image', 'livemesh-el-addons'),
                        ],
                    ],
                    [
                        'name' => 'icon_image',
                        'label' => __('Service Image', 'livemesh-el-addons'),
                        'type' => Controls_Manager::MEDIA,
                        'default' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                        'label_block' => true,
                        'condition' => [
                            'icon_type' => 'icon_image',
                        ],
                    ],
                    [
                        'name' => 'icon',
                        'label' => __('Service Icon', 'livemesh-el-addons'),
                        'type' => Controls_Manager::ICON,
                        'label_block' => true,
                        'default' => '',
                        'condition' => [
                            'icon_type' => 'icon',
                        ],
                    ],
                    [
                        'name' => 'service_excerpt',
                        'label' => __('Service description', 'livemesh-el-addons'),
                        'type' => Controls_Manager::TEXTAREA,
                        'default' => __('Service description goes here', 'livemesh-el-addons'),
                        'label_block' => true,
                    ],

                ],
                'title_field' => '{{{ service_title }}}',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_service_title',
            [
                'label' => __('Service Title', 'livemesh-el-addons'),
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
            'title_color',
            [
                'label' => __( 'Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-services .lae-service .lae-service-text .lae-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .lae-services .lae-service .lae-service-text .lae-title',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_service_text',
            [
                'label' => __('Service Text', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __( 'Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-services .lae-service .lae-service-text .lae-service-details' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .lae-services .lae-service .lae-service-text .lae-service-details',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_service_icon',
            [
                'label' => __('Service Icons', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'icon_size',
            [
                'label' => __('Icon or Icon Image size in pixels', 'livemesh-el-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em' ],
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .lae-services .lae-service .lae-image-wrapper img' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .lae-services .lae-service .lae-icon-wrapper span' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => __('Icon Custom Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-services .lae-service .lae-icon-wrapper span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hover_color',
            [
                'label' => __('Icon Hover Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-services .lae-service .lae-icon-wrapper span:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings();
        ?>

        <?php $column_style = lae_get_column_class(intval($settings['per_line'])); ?>

        <div class="lae-services lae-<?php echo $settings['style']; ?> lae-grid-container">

            <?php foreach ($settings['services'] as $service): ?>

                <div class="lae-service-wrapper <?php echo $column_style; ?>">

                    <div class="lae-service">

                        <?php if ($service['icon_type'] == 'icon_image') : ?>

                            <?php $icon_image = $service['icon_image']; ?>

                            <?php if (!empty($icon_image)): ?>

                                <div class="lae-image-wrapper">

                                    <?php echo wp_get_attachment_image($icon_image['id'], 'full', false, array('class' => 'lae-image full')); ?>

                                </div>

                            <?php endif; ?>

                        <?php elseif ($service['icon_type'] == 'icon') : ?>

                            <div class="lae-icon-wrapper">

                                <span class="<?php echo esc_attr($service['icon']); ?>"></span>

                            </div>

                        <?php endif; ?>

                        <div class="lae-service-text">

                            <<?php echo $settings['title_tag']; ?> class="lae-title"><?php echo esc_html($service['service_title']) ?></<?php echo $settings['title_tag']; ?>>

                            <div class="lae-service-details"><?php echo do_shortcode(wp_kses_post($service['service_excerpt'])); ?></div>

                        </div>

                    </div>

                </div>

                <?php

            endforeach;

            ?>

        </div>

        <div class="lae-clear"></div>

        <?php
    }

    protected function content_template() {
    }

}