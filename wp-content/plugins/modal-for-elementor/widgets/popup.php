<?php
namespace ElementorModal\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Frontend;
use WP_Query;


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class ElementorModal extends Widget_Base {
	
	protected $_has_template_content = false;
	
	public function get_name() {
		return 'popup';
	}
	public function get_title() {
		return __( 'PopBox', 'modal-for-elementor' );
	}
	public function get_icon() {
		return 'eicon-button';
	}
	public function get_categories() {
		return [ 'norewp-elements' ];
	}
	public static function get_button_sizes() {
		return [
			'xs' => __( 'Extra Small', 'modal-for-elementor' ),
			'sm' => __( 'Small', 'modal-for-elementor' ),
			'md' => __( 'Medium', 'modal-for-elementor' ),
			'lg' => __( 'Large', 'modal-for-elementor' ),
			'xl' => __( 'Extra Large', 'modal-for-elementor' ),
		];
	}
	protected function get_popups() {
		$popups_query = new WP_Query( array(
			'post_type' => 'elementor-popup',
			'posts_per_page' => -1,
		) );

		if ( $popups_query->have_posts() ) {
			$popups_array = array();
			$popups = $popups_query->get_posts();
			
			$i = 0;
			foreach( $popups as $popap ) {
				$popups_array[$popap->ID] = $popap->post_title;
				if($i === 0)
					$selected = $popap->ID;
				$i++;
			}
			
			$popups = array(
				'first_popup' => $selected,
				'popups' => $popups_array,
			);
			return $popups;
		}
	}
	protected function _register_controls() {
		
		$this->start_controls_section(
			'section_button',
			[
				'label' => __( 'Trigger Button', 'modal-for-elementor' ),
			]
		);
		$this->add_control(
			'button_type',
			[
				'label' => __( 'Type', 'modal-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'Default', 'modal-for-elementor' ),
					'info' => __( 'Info', 'modal-for-elementor' ),
					'success' => __( 'Success', 'modal-for-elementor' ),
					'warning' => __( 'Warning', 'modal-for-elementor' ),
					'danger' => __( 'Danger', 'modal-for-elementor' ),
				],
			]
		);
		$this->add_control(
			'button_text',
			[
				'label' => __( 'Text', 'modal-for-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Click me', 'modal-for-elementor' ),
				'placeholder' => __( 'Click me', 'modal-for-elementor' ),
			]
		);
		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'modal-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => __( 'Left', 'modal-for-elementor' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'modal-for-elementor' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'modal-for-elementor' ),
						'icon' => 'fa fa-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'modal-for-elementor' ),
						'icon' => 'fa fa-align-justify',
					],
				],
				'prefix_class' => 'elementor%s-align-',
				'default' => '',
			]
		);
		$this->add_control(
			'size',
			[
				'label' => __( 'Size', 'modal-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'sm',
				'options' => self::get_button_sizes(),
			]
		);
		$this->add_control(
			'icon',
			[
				'label' => __( 'Icon', 'modal-for-elementor' ),
				'type' => Controls_Manager::ICON,
				'label_block' => true,
				'default' => '',
			]
		);
		$this->add_control(
			'icon_align',
			[
				'label' => __( 'Icon Position', 'modal-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left' => __( 'Before', 'modal-for-elementor' ),
					'right' => __( 'After', 'modal-for-elementor' ),
				],
				'condition' => [
					'icon!' => '',
				],
			]
		);
		$this->add_control(
			'icon_indent',
			[
				'label' => __( 'Icon Spacing', 'modal-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'condition' => [
					'icon!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-button .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-button .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'label' => __( 'Typography', 'modal-for-elementor' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} a.elementor-button',
			]
		);
		
		$this->add_control(
			'view',
			[
				'label' => __( 'View', 'modal-for-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);
		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_popup',
			[
				'label' => __( 'Modal Content', 'modal-for-elementor' ),
			]
		);
		$this->add_control(
			'popup',
			[
				'label' => __( 'Select Modal Content', 'modal-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => $this->get_popups()['first_popup'],
				'options' => $this->get_popups()['popups'],
			]
		);
        
        $this->add_control(
			'close_button',
			[
				'label' => __( 'Show Close Button', 'modal-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'modal-for-elementor' ),
				'label_on' => __( 'Show', 'modal-for-elementor' ),
				'default' => 'yes',			
				'selectors' => [
					'{{WRAPPER}} button.close' => 'display: inherit;',
                ],
            ]
		);
		
		$this->add_control(
			'close_button_pos',
			[
				'label' => __( 'Switch Button Position', 'modal-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Right', 'modal-for-elementor' ),
				'label_on' => __( 'Left', 'modal-for-elementor' ),
				'default' => '',			
				'selectors' => [
					'{{WRAPPER}} button.close' => 'left: 0;',
                ],
            ]
		);
		
		$this->add_control(
			'close_size',
			[
				'label' => __( 'Icon Size', 'modal-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} button.close i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'close_padding',
			[
				'label' => __( 'Close Padding', 'modal-for-elementor' ),
				'description' => __( 'Please note that padding bottom has no effect - Left/Right padding will depend on button position!', 'modal-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} button.close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		
		$this->add_control(
			'close_text',
			[
				'label' => __( 'Close Text', 'modal-for-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' 	=> __( 'Close', 'modal-for-elementor' ),
				'default' 		=> '',
				'description' 	=> __( 'Add call to action i.e "Close" before the popup close X', 'modal-for-elementor' ),
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'close_typography',
				'label' => __( 'Close Typography', 'modal-for-elementor' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} button.close:not(i)',
			]
		);
        
		$this->end_controls_section();         
		
		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Button', 'modal-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);		

		$this->start_controls_tabs( 'tabs_button_style' );
		
		$this->start_controls_tab(
			'tab_button_settings',
			[
				'label' => __( 'Settings', 'modal-for-elementor' ),
			]
		);
		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'label' => __( 'Border', 'modal-for-elementor' ),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .elementor-button',
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label' => __( 'Border Radius', 'modal-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-button',
			]
		);

		$this->add_control(
			'button_padding',
			[
				'label' => __( 'Text Padding', 'modal-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => __( 'Colors', 'modal-for-elementor' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => __( 'Text Color', 'modal-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} a.elementor-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => __( 'Background Color', 'modal-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_4,
				],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => __( 'Hover', 'modal-for-elementor' ),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label' => __( 'Text Color', 'modal-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.elementor-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover_color',
			[
				'label' => __( 'Background Color', 'modal-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.elementor-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label' => __( 'Border Color', 'modal-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} a.elementor-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => __( 'Animation', 'modal-for-elementor' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_section();

        //Modal Container Optins Start Here
        $this->start_controls_section(
			'modalstyle',
			[
				'label' => __( 'Modal Container', 'modal-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_responsive_control(
			'modal_content_max_width',
			[
				'label' => __( 'Container Max-Width', 'modal-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 720,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1920,
						'step' => 1,
					],
					'%' => [
						'min' => 5,
						'max' => 100,
					],
				],
				'size_units' => [ '%', 'px' ],
				'selectors' => [
					'{{WRAPPER}} .modal-content' => 'max-width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);
		
		$this->add_control(
			'overlay_hint',
			[
				'label' => __( 'Select and configure the required modal overlay background type below', 'modal-for-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'modal_bgcolor',
				'types' => [ 'classic', 'gradient' ],
				'default' => 'rgba(0,0,0,0.7)',
				'selector' => '{{WRAPPER}} .modal',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'modalcontentstyle',
			[
				'label' => __( 'Modal Content', 'modal-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'modal_window_hint',
			[
				'label' => __( 'Select and configure the required popup modal window\'s background type below', 'extend-elements' ),
				'type' => Controls_Manager::RAW_HTML,
			]
		);
		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'label' => __( 'Popup Window Background', 'modal-for-elementor' ),
				'name' => 'modal_window_bg',
				'types' => [ 'none', 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .modal-content',
			]
		);
        
        $this->add_control(
			'button_close_text_color',
			[
				'label' => __( 'Close Button Color', 'modal-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} button.close' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'modal_content_width',
			[
				'label' => __( 'Modal Width', 'modal-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 60,
					'unit' => '%',
				],
				'range' => [
					'px' => [
							'min' => 0,
							'max' => 1920,
							'step' => 1,
					],
					'%' => [
							'min' => 25,
							'max' => 100,
					],
				],
				'size_units' => [ '%', 'px' ],
				'selectors' => [
					'{{WRAPPER}} .modal-content' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);
		
		$this->add_responsive_control(
			'modal_content_top',
			[
				'label' => __( 'Top Offset', 'modal-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 5,
					'unit' => '%',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ '%', 'px' ],
				'selectors' => [
					'{{WRAPPER}} .modal-content' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'modal_content_padding',
			[
				'label' => __( 'Padding', 'modal-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => 0,
					'left' => 0,
					'right' => 0,
					'bottom' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .modal-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'modal_border',
				'label' => __( 'Border', 'modal-for-elementor' ),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .modal-content',
			]
		);

		$this->add_control(
			'modal_border_radius',
			[
				'label' => __( 'Border Radius', 'modal-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .modal-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'popbox_content_box_shadow',
				'selector' => '{{WRAPPER}} .modal-content',
			]
		);

		$this->end_controls_section();
        
	}
	protected function render() {
		$settings = $this->get_settings();
		$close = $settings['close_text'];
		$selectedPopup = new WP_Query( array( 'p' => $settings['popup'], 'post_type' => 'elementor-popup' ) );
		if ( $selectedPopup->have_posts() ) {
			
			$selectedPopup->the_post();

			$this->add_render_attribute( 'wrapper', 'class', 'elementor-button-wrapper' );
			$this->add_render_attribute( 'button', 'class', 'elementor-button' );
			if ( ! empty( $settings['size'] ) ) {
				$this->add_render_attribute( 'button', 'class', 'elementor-size-' . $settings['size'] );
			}
			if ( ! empty( $settings['button_type'] ) ) {
				$this->add_render_attribute( 'button', 'class', 'elementor-button-' . $settings['button_type'] );
			}
			if ( $settings['hover_animation'] ) {
				$this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['hover_animation'] );
			}
			if ( ! empty( $selectedPopup->post->ID ) ) {
				$this->add_render_attribute( 'button', 'data-target', '#popup-' . esc_attr($selectedPopup->post->ID) );
			}

			$this->add_render_attribute( 'button', 'class', 'modal-popup' );
			$this->add_render_attribute( 'content-wrapper', 'class', 'elementor-button-content-wrapper' );
			$this->add_render_attribute( 'icon-align', 'class', 'elementor-align-icon-' . $settings['icon_align'] );
			$this->add_render_attribute( 'icon-align', 'class', 'elementor-button-icon' );					
			?>
			<!-- PopBox:popboxRender -->
			<!-- PopBox trigger button -->
			<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
				<a <?php echo $this->get_render_attribute_string( 'button' ); ?>>
					<span <?php echo $this->get_render_attribute_string( 'content-wrapper' ); ?>>
						<?php if ( ! empty( $settings['icon'] ) ) : ?>
							<span <?php echo $this->get_render_attribute_string( 'icon-align' ); ?>>
								<i class="<?php echo esc_attr( $settings['icon'] ); ?>"></i>
							</span>
						<?php endif; ?>
						<span class="elementor-button-text"><?php echo $settings['button_text']; ?></span>
					</span>
				</a>
			</div>
			<!-- /PopBox trigger button -->
			<!-- PopBox -->
			<div class="modal fade" id="popup-<?php echo $selectedPopup->post->ID; ?>" tabindex="-1" role="dialog" aria-labelledby="popup-<?php echo $selectedPopup->post->ID; ?>-label">			
				<div class="modal-content">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">
						<?php echo $close; ?> <i class="fa fa-close"></i></span>
					</button>
					<div class="modal-body">
						<?php
							$elementor  = get_post_meta( $selectedPopup->post->ID, '_elementor_edit_mode', true );							
							if ( $elementor ) {
								$frontend = new Frontend;
								echo $frontend->get_builder_content( $selectedPopup->post->ID, true );
							} else {
								the_content();
							}
						?>
					</div>
				</div>
			</div>
			<script type="text/javascript">
				(function($) {
					$('#popup-<?php echo $selectedPopup->post->ID; ?>').on('hide.bs.modal', function(e) {    
						var $if = $(e.delegateTarget).find('iframe');
						var src = $if.attr("src");
						$if.attr("src", '/empty.html');
						$if.attr("src", src);
					});
				})(jQuery);
			</script>
			<!-- PopBox -->
			<?php
			wp_reset_postdata();
			
		}
	}
	protected function _content_template() {}

}