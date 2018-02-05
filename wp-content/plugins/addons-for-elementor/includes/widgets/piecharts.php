<?php

/*
Widget Name: Livemesh Piecharts
Description: Display one or more piecharts depicting a percentage value in a multi-column grid.
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


class LAE_Piecharts_Widget extends Widget_Base {

    public function get_name() {
        return 'lae-piecharts';
    }

    public function get_title() {
        return __('Livemesh Piecharts', 'livemesh-el-addons');
    }

    public function get_icon() {
        return 'eicon-counter-circle';
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
            'section_piecharts',
            [
                'label' => __('Piecharts', 'livemesh-el-addons'),
            ]
        );

        $this->add_control(
            'per_line',
            [
                'label' => __('Piecharts per row', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'default' => 4,
            ]
        );


        $this->add_control(
            'piecharts',
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
                        'stats_title' => __('WordPress Development', 'livemesh-el-addons'),
                        'percentage_value' => 90,
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
                        'description' => __('The title for the piechart', 'livemesh-el-addons'),
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

                ],
                'title_field' => '{{{ stats_title }}}',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_styling',
            [
                'label' => __('Piechart Styling', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'bar_color',
            [
                'label' => __('Bar color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '#f94213',
            ]
        );


        $this->add_control(
            'track_color',
            [
                'label' => __('Track color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '#dddddd',
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
                    '{{WRAPPER}} .lae-piechart .lae-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'stats_title_typography',
                'selector' => '{{WRAPPER}} .lae-piechart .lae-label',
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
            'stats_percentage_color',
            [
                'label' => __('Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-piechart .lae-percentage span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'stats_percentage_typography',
                'selector' => '{{WRAPPER}} .lae-piechart .lae-percentage span',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_stats_percentage_symbol',
            [
                'label' => __('Stats Percentage Symbol', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'stats_percentage_symbol_color',
            [
                'label' => __('Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-piechart .lae-percentage sup' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'stats_percentage_symbol_typography',
                'selector' => '{{WRAPPER}} .lae-piechart .lae-percentage sup',
            ]
        );


    }

    protected function render() {

        $settings = $this->get_settings();
        ?>

        <?php $column_style = lae_get_column_class(intval($settings['per_line'])); ?>

        <?php

        $bar_color = ' data-bar-color="' . esc_attr($settings['bar_color']) . '"';
        $track_color = ' data-track-color="' . esc_attr($settings['track_color']) . '"';

        ?>

        <div class="lae-piecharts lae-grid-container">

            <?php foreach ($settings['piecharts'] as $piechart): ?>

                <div class="lae-piechart <?php echo $column_style; ?>">

                    <div class="lae-percentage" <?php echo $bar_color; ?> <?php echo $track_color; ?>
                         data-percent="<?php echo intval($piechart['percentage_value']); ?>">

                        <span><?php echo intval($piechart['percentage_value']); ?><sup>%</sup></span>

                    </div>

                    <div class="lae-label"><?php echo esc_html($piechart['stats_title']); ?></div>

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