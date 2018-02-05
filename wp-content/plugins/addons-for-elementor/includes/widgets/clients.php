<?php

/*
Widget Name: Livemesh Clients
Description: Display one or more clients depicting a percentage value in a multi-column grid.
Author: LiveMesh
Author URI: https://www.livemeshthemes.com
*/

namespace LivemeshAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class LAE_Clients_Widget extends Widget_Base {

    public function get_name() {
        return 'lae-clients';
    }

    public function get_title() {
        return __('Livemesh Clients', 'livemesh-el-addons');
    }

    public function get_icon() {
        return 'eicon-person';
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
            'section_clients',
            [
                'label' => __('Clients', 'livemesh-el-addons'),
            ]
        );

        $this->add_control(
            'per_line',
            [
                'label' => __('Columns per row', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 6,
                'step' => 1,
                'default' => 4,
            ]
        );

        $this->add_control(
            'clients',
            [
                'type' => Controls_Manager::REPEATER,
                'fields' => [

                    [
                        'name' => 'client_name',
                        'type' => Controls_Manager::TEXT,
                        'label' => __('Client Name', 'livemesh-el-addons'),
                        'label_block' => true,
                        'description' => __('The name of the client/customer.', 'livemesh-el-addons'),
                    ],

                    [
                        'name' => 'client_link',
                        'label' => __('Client URL', 'livemesh-el-addons'),
                        'description' => __('The website of the client/customer.', 'livemesh-el-addons'),
                        'type' => Controls_Manager::URL,
                        'label_block' => true,
                        'default' => [
                            'url' => '',
                            'is_external' => 'true',
                        ],
                        'placeholder' => __('http://client-link.com', 'livemesh-el-addons'),
                    ],

                    [
                        'name' => 'client_image',
                        'label' => __('Client Logo/Image', 'livemesh-el-addons'),
                        'description' => __('The logo image for the client/customer.', 'livemesh-el-addons'),
                        'type' => Controls_Manager::MEDIA,
                        'default' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                        'label_block' => true,
                    ],

                ],
                'title_field' => '{{{ client_name }}}',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_styling',
            [
                'label' => __('Clients', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,

            ]
        );

        $this->add_control(
            'heading_client_image',
            [
                'label' => __( 'Client Images', 'livemesh-el-addons' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'client_border_color',
            [
                'label' => __( 'Client Border Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-clients .lae-client' => 'border-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'client_hover_bg_color',
            [
                'label' => __( 'Client Hover Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-clients .lae-client .lae-image-overlay' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'thumbnail_hover_opacity',
            [
                'label' => __( 'Thumbnail Hover Opacity (%)', 'livemesh-el-addons' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0.7,
                ],
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .lae-clients .lae-client:hover .lae-image-overlay' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_control(
            'client_padding',
            [
                'label' => __('Client Padding', 'livemesh-el-addons'),
                'description' => __('Padding for the client images.', 'livemesh-el-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .lae-clients .lae-client' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'heading_client_name',
            [
                'label' => __( 'Client Name', 'livemesh-el-addons' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );


        $this->add_control(
            'client_name_color',
            [
                'label' => __( 'Client Name Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-clients .lae-client .lae-client-name a' => 'color: {{VALUE}};',
                ],
            ]
        );




        $this->add_control(
            'client_name_hover_color',
            [
                'label' => __( 'Client Name Hover Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-clients .lae-client .lae-client-name a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'client_name_typography',
                'selector' => '{{WRAPPER}} .lae-clients .lae-client .lae-client-name a',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {

        $settings = $this->get_settings();
        ?>

        <?php $num_of_columns = intval($settings['per_line']); ?>

        <?php $column_style = lae_get_column_class($num_of_columns); ?>

        <div class="lae-clients lae-grid-container lae-gapless-grid">

            <?php foreach ($settings['clients'] as $client): ?>

                <div class="lae-client <?php echo $column_style; ?>">

                    <?php if (!empty($client['client_image'])): ?>

                        <?php echo wp_get_attachment_image($client['client_image']['id'], 'full', false, array('class' => 'lae-image full', 'alt' => $client['client_name'])); ?>

                    <?php endif; ?>



                    <?php if (!empty($client['client_link']) && !empty($client['client_link']['url'])): ?>

                        <?php $target = $client['client_link']['is_external'] ? 'target="_blank"' : ''; ?>

                        <div class="lae-client-name">

                            <a href="<?php echo esc_url($client['client_link']['url']); ?>"
                               title="<?php echo esc_html($client['client_name']); ?>"
                                <?php echo $target; ?>><?php echo esc_html($client['client_name']); ?></a>
                        </div>

                    <?php else: ?>

                        <div class="lae-client-name"><?php echo esc_html($client['client_name']); ?></div>

                    <?php endif; ?>


                    <div class="lae-image-overlay"></div>

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