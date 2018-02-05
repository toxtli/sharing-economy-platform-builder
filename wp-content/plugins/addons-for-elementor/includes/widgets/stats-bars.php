<?php

/*
Widget Name: Livemesh Stats Bars
Description: Display multiple stats bars that talk about skills or other percentage stats.
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


class LAE_Stats_Bars_Widget extends Widget_Base {

    public function get_name() {
        return 'lae-stats-bars';
    }

    public function get_title() {
        return __('Livemesh Stats Bars', 'livemesh-el-addons');
    }

    public function get_icon() {
        return 'fa fa-tasks';
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
            'section_stats_bars',
            [
                'label' => __('Stats Bars', 'livemesh-el-addons'),
            ]
        );

        $this->add_control(
            'stats_bars',
            [
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'stats_title' => __('Web Design', 'livemesh-el-addons'),
                        'percentage_value' => 87,
                    ],

                    [
                        'stats_title' => __('SEO Services', 'livemesh-el-addons'),
                        'percentage_value' => 76,
                    ],

                    [
                        'stats_title' => __('Brand Marketing', 'livemesh-el-addons'),
                        'percentage_value' => 40,
                    ],
                ],
                'fields' => [
                    [
                        'name' => 'stats_title',
                        'label' => __('Stats Title', 'livemesh-el-addons'),
                        'type' => Controls_Manager::TEXT,
                        'description' => __('The title for the stats bar', 'livemesh-el-addons'),
                    ],

                    [
                        'name' => 'percentage_value',
                        'label' => __('Percentage Value', 'livemesh-el-addons'),
                        'type' => Controls_Manager::NUMBER,
                        'min' => 1,
                        'max' => 100,
                        'step' => 1,
                        'default' => 30,
                        'description' => __('The percentage value for the stats.', 'livemesh-el-addons'),
                    ],

                    [
                        'name' => 'bar_color',
                        'label' => __('Bar Color', 'livemesh-el-addons'),
                        'type' => Controls_Manager::COLOR,
                        'default' => '#f94213',
                    ],

                ],
                'title_field' => '{{{ stats_title }}}',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_stats_bar_styling',
            [
                'label' => __('Stats Bar', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'stats_bar_bg_color',
            [
                'label' => __('Stats Bar Background Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-stats-bars .lae-stats-bar .lae-stats-bar-bg' => 'background-color: {{VALUE}};',
                ],
            ]
        );



        $this->add_control(
            'stats_bar_spacing',
            [
                'label' => __('Stats Bar Spacing', 'livemesh-el-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'default' => [
                    'size' => 18,
                ],
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 128,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .lae-stats-bars .lae-stats-bar' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->add_control(
            'stats_bar_height',
            [
                'label' => __('Stats Bar Height', 'livemesh-el-addons'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'default' => [
                    'size' => 10,
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 96,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .lae-stats-bars .lae-stats-bar .lae-stats-bar-bg, {{WRAPPER}} .lae-stats-bars .lae-stats-bar .lae-stats-bar-content' => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .lae-stats-bars .lae-stats-bar .lae-stats-bar-bg' => 'margin-top: -{{SIZE}}{{UNIT}};',
                ],
            ]
        );



        $this->add_control(
            'stats_bar_border_radius',
            [
                'label' => __('Stats Bar Border Radius', 'livemesh-el-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .lae-stats-bars .lae-stats-bar .lae-stats-bar-bg, {{WRAPPER}} .lae-stats-bars .lae-stats-bar .lae-stats-bar-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );



        $this->end_controls_section();


        $this->start_controls_section(
            'section_stats_title',
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
                'selectors' => [
                    '{{WRAPPER}} .lae-stats-bars .lae-stats-bar .lae-stats-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'stats_title_typography',
                'selector' => '{{WRAPPER}} .lae-stats-bars .lae-stats-bar .lae-stats-title',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_stats_percentage',
            [
                'label' => __('Stats Percentage', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_control(
            'stats_percentage_spacing',
            [
                'label' => __('Spacing from Stats Title', 'livemesh-el-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => 0,
                    'right' => 0,
                    'bottom' => 0,
                    'left' => 5,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .lae-stats-bars .lae-stats-bar .lae-stats-title span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'isLinked' => false
            ]
        );

        $this->add_control(
            'stats_percentage_color',
            [
                'label' => __('Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-stats-bars .lae-stats-bar .lae-stats-title span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'stats_percentage_typography',
                'selector' => '{{WRAPPER}} .lae-stats-bars .lae-stats-bar .lae-stats-title span',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {

        $settings = $this->get_settings();
        ?>

        <div class="lae-stats-bars">

            <?php foreach ($settings['stats_bars'] as $stats_bar) :

                $color_style = '';
                $color = $stats_bar['bar_color'];
                if ($color)
                    $color_style = ' style="background:' . esc_attr($color) . ';"';

                ?>

                <div class="lae-stats-bar">

                    <div class="lae-stats-title">
                        <?php echo esc_html($stats_bar['stats_title']) ?><span><?php echo esc_attr($stats_bar['percentage_value']); ?>%</span>
                    </div>

                    <div class="lae-stats-bar-wrap">

                        <div <?php echo $color_style; ?> class="lae-stats-bar-content"
                                                         data-perc="<?php echo esc_attr($stats_bar['percentage_value']); ?>"></div>

                        <div class="lae-stats-bar-bg"></div>

                    </div>

                </div>

                <?php

            endforeach;

            ?>

        </div>

        <?php
    }

    protected function content_template() {

    }

}