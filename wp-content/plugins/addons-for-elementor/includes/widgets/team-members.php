<?php

/*
Widget Name: Livemesh Team Members
Description: Display a list of your team members optionally in a multi-column grid.
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


class LAE_Team_Widget extends Widget_Base {

    public function get_name() {
        return 'lae-team-members';
    }

    public function get_title() {
        return __('Livemesh Team Members', 'livemesh-el-addons');
    }

    public function get_icon() {
        return 'fa fa-user-o';
    }

    public function get_categories() {
        return array('livemesh-addons');
    }

    public function get_script_depends() {
        return [
            'lae-widgets-scripts',
            'lae-frontend-scripts'
        ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_team',
            [
                'label' => __('Team', 'livemesh-el-addons'),
            ]
        );

        $this->add_control(

            'style', [
                'type' => Controls_Manager::SELECT,
                'label' => __('Choose Team Style', 'livemesh-el-addons'),
                'default' => 'style1',
                'options' => [
                    'style1' => __('Style 1', 'livemesh-el-addons'),
                    'style2' => __('Style 2', 'livemesh-el-addons'),
                ],
                'prefix_class' => 'lae-team-members-',
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
                'default' => 3,
                'condition' => [
                    'style' => 'style1',
                ],
            ]
        );


        $this->add_control(
            'team_members',
            [
                'label' => __('Team Members', 'livemesh-el-addons'),
                'type' => Controls_Manager::REPEATER,
                'separator' => 'before',
                'default' => [
                    [
                        'member_name' => __('Team Member #1', 'livemesh-el-addons'),
                        'member_position' => __('CEO', 'livemesh-el-addons'),
                        'member_details' => __('I am member details. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'livemesh-el-addons'),
                    ],
                    [
                        'member_name' => __('Team Member #2', 'livemesh-el-addons'),
                        'member_position' => __('Lead Developer', 'livemesh-el-addons'),
                        'member_details' => __('I am member details. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'livemesh-el-addons'),
                    ],
                    [
                        'member_name' => __('Team Member #3', 'livemesh-el-addons'),
                        'member_position' => __('Finance Manager', 'livemesh-el-addons'),
                        'member_details' => __('I am member details. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'livemesh-el-addons'),
                    ],
                ],
                'fields' => [
                    [
                        'name' => 'member_name',
                        'label' => __('Member Name', 'livemesh-el-addons'),
                        'type' => Controls_Manager::TEXT,
                    ],
                    [
                        'name' => 'member_position',
                        'label' => __('Position', 'livemesh-el-addons'),
                        'type' => Controls_Manager::TEXT,
                    ],

                    [
                        'name' => 'member_image',
                        'label' => __('Team Member Image', 'livemesh-el-addons'),
                        'type' => Controls_Manager::MEDIA,
                        'default' => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                        'label_block' => true,
                    ],
                    [
                        'name' => 'member_details',
                        'label' => __('Team Member details', 'livemesh-el-addons'),
                        'type' => Controls_Manager::TEXTAREA,
                        'default' => __('Details about team member', 'livemesh-el-addons'),
                        'description' => __('Provide a short writeup for the team member', 'livemesh-el-addons'),
                        'label_block' => true,
                    ],
                    [
                        'name' => 'social_profile',
                        'label' => __('Social Profile', 'livemesh-el-addons'),
                        'type' => Controls_Manager::HEADING,
                    ],
                    [
                        'type' => Controls_Manager::TEXT,
                        'name' => 'member_email',
                        'label' => __('Email Address', 'livemesh-el-addons'),
                        'description' => __('Enter the email address of the team member.', 'livemesh-el-addons'),
                    ],

                    [
                        'type' => Controls_Manager::TEXT,
                        'name' => 'facebook_url',
                        'label' => __('Facebook Page URL', 'livemesh-el-addons'),
                        'description' => __('URL of the Facebook page of the team member.', 'livemesh-el-addons'),
                    ],

                    [
                        'type' => Controls_Manager::TEXT,
                        'name' => 'twitter_url',
                        'label' => __('Twitter Profile URL', 'livemesh-el-addons'),
                        'description' => __('URL of the Twitter page of the team member.', 'livemesh-el-addons'),
                    ],

                    [
                        'type' => Controls_Manager::TEXT,
                        'name' => 'linkedin_url',
                        'label' => __('LinkedIn Page URL', 'livemesh-el-addons'),
                        'description' => __('URL of the LinkedIn profile of the team member.', 'livemesh-el-addons'),
                    ],

                    [
                        'type' => Controls_Manager::TEXT,
                        'name' => 'pinterest_url',
                        'label' => __('Pinterest Page URL', 'livemesh-el-addons'),
                        'description' => __('URL of the Pinterest page for the team member.', 'livemesh-el-addons'),
                    ],

                    [
                        'type' => Controls_Manager::TEXT,
                        'name' => 'dribbble_url',
                        'label' => __('Dribbble Profile URL', 'livemesh-el-addons'),
                        'description' => __('URL of the Dribbble profile of the team member.', 'livemesh-el-addons'),
                    ],

                    [
                        'type' => Controls_Manager::TEXT,
                        'name' => 'google_plus_url',
                        'label' => __('GooglePlus Page URL', 'livemesh-el-addons'),
                        'description' => __('URL of the Google Plus page of the team member.', 'livemesh-el-addons'),
                    ],

                    [
                        'type' => Controls_Manager::TEXT,
                        'name' => 'instagram_url',
                        'label' => __('Instagram Page URL', 'livemesh-el-addons'),
                        'description' => __('URL of the Instagram feed for the team member.', 'livemesh-el-addons'),
                    ],
                    
                ],
                'title_field' => '{{{ member_name }}}',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_team_profiles_style',
            [
                'label' => __('General', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
                'show_label' => false,
            ]
        );

        $this->add_responsive_control(
            'team_member_spacing',
            [
                'label' => __('Team Member Spacing', 'livemesh-el-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .lae-team-members .lae-team-member-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'isLinked' => false,
                'condition' => [
                    'style' => ['style2'],
                ],
            ]
        );

        $this->add_responsive_control(
            'thumbnail_hover_brightness',
            [
                'label' => __('Thumbnail Hover Brightness (%)', 'livemesh-el-addons'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 50,
                ],
                'range' => [
                    'px' => [
                        'max' => 100,
                        'min' => 1,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .lae-team-members .lae-team-member:hover .lae-image-wrapper img' => '-webkit-filter: brightness({{SIZE}}%);-moz-filter: brightness({{SIZE}}%);-ms-filter: brightness({{SIZE}}%); filter: brightness({{SIZE}}%);',
                ],
            ]
        );


        $this->add_control(
            'thumbnail_border_radius',
            [
                'label' => __('Thumbnail Border Radius', 'livemesh-el-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .lae-team-members .lae-team-member .lae-image-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_team_member_title',
            [
                'label' => __('Member Title', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_tag',
            [
                'label' => __('Title HTML Tag', 'livemesh-el-addons'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => __('H1', 'livemesh-el-addons'),
                    'h2' => __('H2', 'livemesh-el-addons'),
                    'h3' => __('H3', 'livemesh-el-addons'),
                    'h4' => __('H4', 'livemesh-el-addons'),
                    'h5' => __('H5', 'livemesh-el-addons'),
                    'h6' => __('H6', 'livemesh-el-addons'),
                    'div' => __('div', 'livemesh-el-addons'),
                ],
                'default' => 'h3',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-team-members .lae-team-member .lae-team-member-text .lae-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .lae-team-members .lae-team-member .lae-team-member-text .lae-title',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_team_member_position',
            [
                'label' => __('Member Position', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'position_color',
            [
                'label' => __('Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-team-members .lae-team-member .lae-team-member-text .lae-team-member-position' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'position_typography',
                'selector' => '{{WRAPPER}} .lae-team-members .lae-team-member .lae-team-member-text .lae-team-member-position',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_team_member_details',
            [
                'label' => __('Member Details', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .lae-team-members .lae-team-member .lae-team-member-details' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'selector' => '{{WRAPPER}} .lae-team-members .lae-team-member .lae-team-member-details',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_social_icon_styling',
            [
                'label' => __('Social Icons', 'livemesh-el-addons'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'social_icon_size',
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
                    '{{WRAPPER}} .lae-team-members .lae-team-member .lae-image-wrapper .lae-social-list i' => 'font-size: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_control(
            'social_icon_spacing',
            [
                'label' => __('Spacing', 'livemesh-el-addons'),
                'description' => __('Space between icons.', 'livemesh-el-addons'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => 0,
                    'right' => 15,
                    'bottom' => 0,
                    'left' => 0,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .lae-team-members .lae-team-member .lae-social-list .lae-social-list-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'isLinked' => false
            ]
        );

        $this->add_control(
            'social_icon_color',
            [
                'label' => __('Icon Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .lae-team-members .lae-team-member .lae-social-list .lae-social-list-item i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'social_icon_hover_color',
            [
                'label' => __('Icon Hover Color', 'livemesh-el-addons'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .lae-team-members .lae-team-member .lae-social-list .lae-social-list-item i:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render() {

        $settings = $this->get_settings();
        ?>

        <?php $column_style = ''; ?>

        <?php $container_style = 'lae-container'; ?>

        <?php if ($settings['style'] == 'style1'): ?>

            <?php $column_style = lae_get_column_class(intval($settings['per_line'])); ?>

            <?php $container_style = 'lae-grid-container'; ?>

        <?php endif; ?>

        <div class="lae-team-members lae-<?php echo $settings['style']; ?> <?php echo $container_style; ?>">

            <?php foreach ($settings['team_members'] as $team_member): ?>

                <div class="lae-team-member-wrapper <?php echo $column_style; ?>">

                    <div class="lae-team-member">

                        <div class="lae-image-wrapper">

                            <?php $member_image = $team_member['member_image']; ?>

                            <?php if (!empty($member_image)): ?>

                                <?php echo wp_get_attachment_image($member_image['id'], 'full', false, array('class' => 'lae-image full')); ?>

                            <?php endif; ?>

                            <?php if ($settings['style'] == 'style1'): ?>

                                <?php $this->social_profile($team_member) ?>

                            <?php endif; ?>

                        </div>

                        <div class="lae-team-member-text">

                            <<?php echo $settings['title_tag']; ?> class="lae-title"><?php echo esc_html($team_member['member_name']) ?></<?php echo $settings['title_tag']; ?>>

                            <div class="lae-team-member-position">

                                <?php echo do_shortcode($team_member['member_position']) ?>

                            </div>

                            <div class="lae-team-member-details">

                                <?php echo do_shortcode($team_member['member_details']) ?>

                            </div>

                            <?php if ($settings['style'] == 'style2'): ?>

                                <?php $this->social_profile($team_member) ?>

                            <?php endif; ?>

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
    
    private function social_profile($team_member) {
        ?>

        <div class="lae-social-wrap">

            <div class="lae-social-list">

                <?php

                $email = $team_member['member_email'];
                $facebook_url = $team_member['facebook_url'];
                $twitter_url = $team_member['twitter_url'];
                $linkedin_url = $team_member['linkedin_url'];
                $dribbble_url = $team_member['dribbble_url'];
                $pinterest_url = $team_member['pinterest_url'];
                $googleplus_url = $team_member['google_plus_url'];
                $instagram_url = $team_member['instagram_url'];


                if ($email)
                    echo '<div class="lae-social-list-item"><a class="lae-email" href="mailto:' . $email . '" title="' . __("Send an email", 'livemesh-el-addons') . '"><i class="lae-icon-email"></i></a></div>';
                if ($facebook_url)
                    echo '<div class="lae-social-list-item"><a class="lae-facebook" href="' . $facebook_url . '" target="_blank" title="' . __("Follow on Facebook", 'livemesh-el-addons') . '"><i class="lae-icon-facebook"></i></a></div>';
                if ($twitter_url)
                    echo '<div class="lae-social-list-item"><a class="lae-twitter" href="' . $twitter_url . '" target="_blank" title="' . __("Subscribe to Twitter Feed", 'livemesh-el-addons') . '"><i class="lae-icon-twitter"></i></a></div>';
                if ($linkedin_url)
                    echo '<div class="lae-social-list-item"><a class="lae-linkedin" href="' . $linkedin_url . '" target="_blank" title="' . __("View LinkedIn Profile", 'livemesh-el-addons') . '"><i class="lae-icon-linkedin"></i></a></div>';
                if ($googleplus_url)
                    echo '<div class="lae-social-list-item"><a class="lae-googleplus" href="' . $googleplus_url . '" target="_blank" title="' . __("Follow on Google Plus", 'livemesh-el-addons') . '"><i class="lae-icon-googleplus"></i></a></div>';
                if ($instagram_url)
                    echo '<div class="lae-social-list-item"><a class="lae-instagram" href="' . $instagram_url . '" target="_blank" title="' . __("View Instagram Feed", 'livemesh-el-addons') . '"><i class="lae-icon-instagram"></i></a></div>';
                if ($pinterest_url)
                    echo '<div class="lae-social-list-item"><a class="lae-pinterest" href="' . $pinterest_url . '" target="_blank" title="' . __("Subscribe to Pinterest Feed", 'livemesh-el-addons') . '"><i class="lae-icon-pinterest"></i></a></div>';
                if ($dribbble_url)
                    echo '<div class="lae-social-list-item"><a class="lae-dribbble" href="' . $dribbble_url . '" target="_blank" title="' . __("View Dribbble Portfolio", 'livemesh-el-addons') . '"><i class="lae-icon-dribbble"></i></a></div>';

                ?>

            </div>

        </div>
        <?php
    }

    protected function content_template() {
    }

}