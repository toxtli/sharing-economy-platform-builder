<?php

/*
Widget Name: Livemesh Odometers
Description: Display one or more animated odometer statistics in a multi-column grid.
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


class LAE_Odometers_Widget extends Widget_Base {

    public function get_name() {
        return 'lae-odometers';
    }

    public function get_title() {
        return __('Livemesh Odometers', 'livemesh-el-addons');
    }

    public function get_icon() {
        return 'eicon-counter';
    }

    public function get_categories() {
        return array('livemesh-addons');
    }

    public function get_script_depends() {
        return [
            'lae-widgets-scripts',
            'lae-frontend-scripts',
            'waypoints',
            'jquery-stats'
        ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_odometers',
            [
                'label' => __('Odometers', 'livemesh-el-addons'),
            ]
        );

        $this->add_control(
            'per_line',
            [
                'label' => __('Odometers per row', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'default' => 4,
            ]
        );

        $this->add_control(
            'odometers',
            [
                'label' => __('Odometers', 'livemesh-el-addons'),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'stats_title' => __('No of Customers', 'livemesh-el-addons'),
                        'start_value' => 1000,
                        'stop_value' => 65600,
                        'prefix' => '',
                        'suffix' => ''
                    ],
                    [
                        'stats_title' => __('Hours Worked', 'livemesh-el-addons'),
                        'start_value' => 1,
                        'stop_value' => 34000,
                        'prefix' => '',
                        'suffix' => ''
                    ],
                    [
                        'stats_title' => __('Support Tickets', 'livemesh-el-addons'),
                        'start_value' => 1,
                        'stop_value' => 348,
                        'prefix' => '',
                        'suffix' => 'k'
                    ],
                    [
                        'stats_title' => __('Product Revenue', 'livemesh-el-addons'),
                        'start_value' => 1,
                        'stop_value' => 35,
                        'prefix' => '$',
                        'suffix' => 'm'
                    ],
                ],
                'fields' => [
                    [
                        'name' => 'stats_title',
                        'label' => __('Stats Title', 'livemesh-el-addons'),
                        'type' => Controls_Manager::TEXT,
                        'label_block' => true,
                    ],
                    [
                        'name' => 'start_value',
                        'label' => __('Start Value', 'livemesh-el-addons'),
                        'type' => Controls_Manager::NUMBER,
                        'default' => 0,
                    ],
                    [
                        'name' => 'stop_value',
                        'label' => __('Stop Value', 'livemesh-el-addons'),
                        'type' => Controls_Manager::NUMBER,
                        'default' => 100,
                    ],

                    [
                        'name' => 'icon_type',
                        'label' => __('Choose Icon Type', 'livemesh-el-addons'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'icon',
                        'options' => [
                            'icon' => __('Icon', 'livemesh-el-addons'),
                            'icon_image' => __('Icon Image', 'livemesh-el-addons'),
                        ],
                    ],

                    [
                        'name' => 'icon_image',
                        'label' => __('Stats Image', 'livemesh-el-addons'),
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
                        'label' => __('Stats Icon', 'livemesh-el-addons'),
                        'type' => Controls_Manager::ICON,
                        'label_block' => true,
                        'default' => '',
                        'condition' => [
                            'icon_type' => 'icon',
                        ],
                    ],

                    [
                        'name' => 'prefix',
                        'label' => __('Prefix', 'livemesh-el-addons'),
                        'type' => Controls_Manager::TEXT,
                        'description' => __('The prefix string like currency symbols like $ to indicate a monetary value.', 'livemesh-el-addons'),
                    ],

                    [
                        'name' => 'suffix',
                        'label' => __('Suffix', 'livemesh-el-addons'),
                        'type' => Controls_Manager::TEXT,
                        'description' => __('The suffix string like hr for hours or m for million.', 'livemesh-el-addons'),
                    ],

                ],
                'title_field' => '{{{ stats_title }}}',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_stats_number',
            [
                'label' => __('Stats Number', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'stats_number_color',
            [
                'label' => __( 'Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-odometers .lae-odometer .lae-number' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'stats_number_typography',
                'selector' => '{{WRAPPER}} .lae-odometers .lae-odometer .lae-number',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_stats_prefix_suffix',
            [
                'label' => __('Stats Prefix and Suffix', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'stats_prefix_suffix_color',
            [
                'label' => __( 'Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-odometers .lae-odometer .lae-prefix, .lae-odometers .lae-odometer .lae-suffix' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'stats_prefix_suffix_typography',
                'selector' => '{{WRAPPER}} .lae-odometers .lae-odometer .lae-prefix, .lae-odometers .lae-odometer .lae-suffix',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_styling',
            [
                'label' => __('Stats Title', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'stats_title_color',
            [
                'label' => __('Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .lae-odometers .lae-odometer .lae-stats-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'stats_title_typography',
                'label' => __('Typography', 'livemesh-el-addons'),
                'selector' => '{{WRAPPER}} .lae-odometers .lae-odometer .lae-stats-title',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_icon_styling',
            [
                'label' => __('Icons', 'livemesh-el-addons'),
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
                        'max' => 128,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .lae-odometers .lae-odometer .lae-image-wrapper img' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .lae-odometers .lae-odometer .lae-icon-wrapper' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'icon_spacing',
            [
                'label' => __('Spacing', 'livemesh-el-addons'),
                'description' => __('Space after icon.', 'livemesh-el-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => 0,
                    'right' => 15,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .lae-odometers .lae-odometer .lae-stats-title .lae-icon-wrapper, {{WRAPPER}} .lae-odometers .lae-odometer .lae-stats-title .lae-image-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'isLinked' => false
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => __('Icon Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .lae-odometers .lae-odometer .lae-stats-title .lae-icon-wrapper' => 'color: {{VALUE}};',
                ],
            ]
        );
    }

    protected function render() {

        $settings = $this->get_settings();
        ?>

        <?php $column_style = lae_get_column_class(intval($settings['per_line'])); ?>

        <div class="lae-odometers lae-grid-container">

            <?php foreach ($settings['odometers'] as $odometer): ?>

                <?php

                $prefix = (!empty ($odometer['prefix'])) ? '<span class="prefix">' . $odometer['prefix'] . '</span>' : '';
                $suffix = (!empty ($odometer['suffix'])) ? '<span class="suffix">' . $odometer['suffix'] . '</span>' : '';

                ?>

                <div class="lae-odometer <?php echo $column_style; ?>">

                    <?php echo (!empty ($odometer['prefix'])) ? '<span class="lae-prefix">' . $odometer['prefix'] . '</span>' : ''; ?>

                    <div class="lae-number odometer" data-stop="<?php echo intval($odometer['stop_value']); ?>">

                        <?php echo intval($odometer['start_value']); ?>

                    </div>

                    <?php echo (!empty ($odometer['suffix'])) ? '<span class="lae-suffix">' . $odometer['suffix'] . '</span>' : ''; ?>

                    <?php $icon_type = esc_html($odometer['icon_type']); ?>

                    <?php if ($icon_type == 'icon_image') : ?>

                        <?php $icon_image = $odometer['icon_image']; ?>

                        <?php if (!empty($icon_image)): ?>

                            <?php $icon_html = '<span class="lae-image-wrapper">' . wp_get_attachment_image($icon_image['id'], 'full', false, array('class' => 'lae-image full')) . '</span>'; ?>

                        <?php endif; ?>


                    <?php else : ?>

                        <?php $icon_html = '<span class="lae-icon-wrapper"><i class="' . esc_attr($odometer['icon']) . '"></i></span>'; ?>

                    <?php endif; ?>

                    <div class="lae-stats-title-wrap">

                        <div class="lae-stats-title"><?php echo $icon_html . esc_html($odometer['stats_title']); ?></div>

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