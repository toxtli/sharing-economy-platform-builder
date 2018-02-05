<?php

/*
Widget Name: Livemesh Pricing Table
Description: Display pricing plans in a multi-column grid.
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


class LAE_Pricing_Table_Widget extends Widget_Base {


    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        add_shortcode('lae_pricing_item', array($this, 'pricing_item_shortcode'));
    }

    public function pricing_item_shortcode($atts, $content = null, $tag) {

        $title = $value = '';

        extract(shortcode_atts(array(
            'title' => '',
            'value' => ''

        ), $atts));

        ob_start();

        ?>

        <div class="lae-pricing-item">

            <div class="lae-title">

                <?php echo htmlspecialchars_decode(wp_kses_post($title)); ?>

            </div>

            <div class="lae-value-wrap">

                <div class="lae-value">

                    <?php echo htmlspecialchars_decode(wp_kses_post($value)); ?>

                </div>

            </div>

        </div>

        <?php


        $output = ob_get_clean();

        return $output;
    }

    public function get_name() {
        return 'lae-pricing-table';
    }

    public function get_title() {
        return __('Livemesh Pricing Table', 'livemesh-el-addons');
    }

    public function get_icon() {
        return 'eicon-price-table';
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
            'section_pricing_table',
            [
                'label' => __('Pricing Table', 'livemesh-el-addons'),
            ]
        );

        $this->add_control(
            'per_line',
            [
                'label' => __('Pricing plans in a row', 'livemesh-el-addons'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 5,
                'step' => 1,
                'default' => 4,
            ]
        );

        $this->add_control(
            'pricing_heading',
            [
                'label' => __('Pricing Plans', 'livemesh-el-addons'),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'pricing_plans',
            [
                'type' => Controls_Manager::REPEATER,
                'fields' => [
                    [
                        'name' => 'pricing_title',
                        'type' => Controls_Manager::TEXT,
                        'label' => __('Pricing Plan Title', 'livemesh-el-addons'),
                        'label_block' => true,
                    ],
                    [
                        'name' => 'tagline',
                        'type' => Controls_Manager::TEXT,
                        'label' => __('Tagline Text', 'livemesh-el-addons'),
                        'description' => __('Provide any subtitle or taglines like "Most Popular", "Best Value", "Best Selling", "Most Flexible" etc. that you would like to use for this pricing plan.', 'livemesh-el-addons'),
                    ],

                    [
                        'name' => 'pricing_image',
                        'label' => __('Pricing Image', 'livemesh-el-addons'),
                        'type' => Controls_Manager::MEDIA,
                        'default' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                        'label_block' => true,
                    ],

                    [
                        'name' => 'price_tag',
                        'type' => Controls_Manager::TEXT,
                        'label' => __('Price Tag', 'livemesh-el-addons'),
                        'description' => __('Enter the price tag for the pricing plan. HTML is accepted.', 'livemesh-el-addons'),
                    ],

                    [
                        'name' => 'button_text',
                        'type' => Controls_Manager::TEXT,
                        'label' => __('Text for Pricing Link/Button', 'livemesh-el-addons'),
                    ],

                    [
                        'name' => 'button_url',
                        'label' => __('URL for the Pricing link/button', 'livemesh-el-addons'),
                        'type' => Controls_Manager::URL,
                        'label_block' => true,
                        'default' => [
                            'url' => '',
                            'is_external' => 'true',
                        ],
                        'placeholder' => __('http://your-link.com', 'livemesh-el-addons'),
                    ],


                    [
                        'name' => 'highlight',
                        'label' => __('Highlight Pricing Plan', 'livemesh-el-addons'),
                        'type' => Controls_Manager::SWITCHER,
                        'label_off' => __('No', 'livemesh-el-addons'),
                        'label_on' => __('Yes', 'livemesh-el-addons'),
                        'return_value' => 'yes',
                        'default' => 'no',
                    ],

                    [
                        'name' => 'pricing_content',
                        'type' => Controls_Manager::TEXTAREA,
                        'label' => __('Pricing Plan Details', 'livemesh-el-addons'),
                        'description' => __('Enter the content for the pricing plan that include information about individual features of the pricing plan. For prebuilt styling, enter shortcodes content like - [lae_pricing_item title="Storage Space" value="50 GB"] [lae_pricing_item title="Video Uploads" value="50"][lae_pricing_item title="Portfolio Items" value="20"]', 'livemesh-el-addons'),
                        'show_label' => true,
                        'rows' => 10
                    ],

                ],
                'title_field' => '{{{ pricing_title }}}',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_pricing_style',
            [
                'label' => __( 'Plan Name', 'livemesh-el-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'plan_name_tag',
            [
                'label' => __( 'HTML Tag', 'livemesh-el-addons' ),
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
            'plan_name_color',
            [
                'label' => __( 'Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-pricing-table .lae-top-header .lae-plan-name' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'plan_name_typography',
                'selector' => '{{WRAPPER}} .lae-pricing-table .lae-top-header .lae-plan-name',
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'section_plan_tagline',
            [
                'label' => __( 'Plan Tagline', 'livemesh-el-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'plan_tagline_color',
            [
                'label' => __( 'Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-pricing-table .lae-top-header .lae-tagline' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'plan_tagline_typography',
                'selector' => '{{WRAPPER}} .lae-pricing-table .lae-top-header .lae-tagline',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_plan_price',
            [
                'label' => __( 'Plan Price', 'livemesh-el-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_control(
            'plan_price_tag',
            [
                'label' => __( 'HTML Tag', 'livemesh-el-addons' ),
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
            'plan_price_color',
            [
                'label' => __( 'Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-pricing-table .lae-pricing-plan .lae-plan-price span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'plan_price_typography',
                'selector' => '{{WRAPPER}} .lae-pricing-table .lae-pricing-plan .lae-plan-price span',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_item_title',
            [
                'label' => __( 'Pricing Item Title', 'livemesh-el-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'item_title_color',
            [
                'label' => __( 'Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-pricing-table .lae-plan-details .lae-pricing-item .lae-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'item_title_typography',
                'selector' => '{{WRAPPER}} .lae-pricing-table .lae-plan-details .lae-pricing-item .lae-title',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_item_value',
            [
                'label' => __( 'Pricing Item Value', 'livemesh-el-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'item_value_color',
            [
                'label' => __( 'Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-pricing-table .lae-plan-details .lae-pricing-item .lae-value' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'item_value_typography',
                'selector' => '{{WRAPPER}} .lae-pricing-table .lae-plan-details .lae-pricing-item .lae-value',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_purchase_button',
            [
                'label' => __( 'Purchase Button', 'livemesh-el-addons' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );


        $this->add_control(
            'purchase_button_spacing',
            [
                'label' => __('Button Spacing', 'livemesh-el-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => 15,
                    'right' => 15,
                    'bottom' => 15,
                    'left' => 15,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .lae-pricing-table .lae-purchase .lae-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'purchase_button_size',
            [
                'label' => __('Button Size', 'livemesh-el-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default' => [
                    'top' => 12,
                    'right' => 25,
                    'bottom' => 12,
                    'left' => 25,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .lae-pricing-table .lae-purchase .lae-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'isLinked' => false
            ]
        );

        $this->add_control(
            'button_custom_color',
            [
                'label' => __('Button Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .lae-pricing-table .lae-purchase .lae-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'button_custom_hover_color',
            [
                'label' => __('Button Hover Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .lae-pricing-table .lae-purchase .lae-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'purchase_button_color',
            [
                'label' => __( 'Label Color', 'livemesh-el-addons' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-pricing-table .lae-purchase .lae-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'purchase_button_typography',
                'selector' => '{{WRAPPER}} .lae-pricing-table .lae-purchase .lae-button',
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings();

        if (empty($settings['pricing_plans']))
            return;

        ?>

        <?php $column_style = lae_get_column_class(intval($settings['per_line'])); ?>

        <div class="lae-pricing-table lae-grid-container">

            <?php

            foreach ($settings['pricing_plans'] as $pricing_plan) :

                $pricing_title = esc_html($pricing_plan['pricing_title']);
                $tagline = esc_html($pricing_plan['tagline']);
                $price_tag = htmlspecialchars_decode(wp_kses_post($pricing_plan['price_tag']));
                $pricing_img = $pricing_plan['pricing_image'];
                $pricing_url = (empty($pricing_plan['button_url']['url'])) ? '#' : esc_url($pricing_plan['button_url']['url']);
                $pricing_button_text = esc_html($pricing_plan['button_text']);
                $button_new_window = esc_html($pricing_plan['button_url']['is_external']);
                $highlight = ($pricing_plan['highlight'] == 'yes');

                $price_tag = (empty($price_tag)) ? '' : $price_tag;

                ?>

                <div class="lae-pricing-plan <?php echo ($highlight ? ' lae-highlight' : ''); ?> <?php echo $column_style; ?>">

                    <div class="lae-top-header">

                        <?php if (!empty($tagline))
                            echo '<p class="lae-tagline center">' . $tagline . '</p>'; ?>

                        <<?php echo $settings['plan_name_tag']; ?> class="lae-plan-name lae-center"><?php echo $pricing_title; ?></<?php echo $settings['plan_name_tag']; ?>>

                        <?php

                        if (!empty($pricing_img)) :
                            echo wp_get_attachment_image($pricing_img['id'], 'full', false, array('class' => 'lae-image full', 'alt' => $pricing_title));
                        endif;

                        ?>

                    </div>

                    <<?php echo $settings['plan_price_tag']; ?> class="lae-plan-price lae-plan-header lae-center">

                        <span class="lae-text">

                            <?php echo wp_kses_post($price_tag); ?>

                        </span>

                    </<?php echo $settings['plan_price_tag']; ?>>

                    <div class="lae-plan-details">

                        <?php echo $this->parse_text_editor($pricing_plan['pricing_content']) ?>

                    </div><!-- .lae-plan-details -->

                    <div class="lae-purchase">

                        <a class="lae-button default" href="<?php echo esc_url($pricing_url); ?>"
                            <?php if (!empty($button_new_window))
                                echo 'target="_blank"'; ?>><?php echo esc_html($pricing_button_text); ?></a>

                    </div>

                </div>
                <!-- .lae-pricing-plan -->

                <?php

            endforeach;

            ?>

        </div><!-- .lae-pricing-table -->

        <div class="lae-clear"></div>

        <?php
    }

    protected function content_template() {
    }

}