<?php
namespace ElementorMenus\Modules\Search\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Elementor Elementor Navbar
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class Elementor_Search extends Widget_Base {

	protected $_has_template_content = false;

	public function get_name() {
		return 'elementor-search';
	}

	public function get_title() {
		return __( 'Search Box', 'navmenu-addon-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-search';
	}

	public function get_categories() {
		return [ 'branding-elements' ];
	}

	protected function _register_controls() {
		// $menus = $this->get_menus();

		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Search', 'navmenu-addon-for-elementor' ),
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'        => __( 'Alignment', 'navmenu-addon-for-elementor' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => __( 'Left', 'navmenu-addon-for-elementor' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'navmenu-addon-for-elementor' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'navmenu-addon-for-elementor' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'prefix_class' => 'elementor%s-align-',
				'default'      => '',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'search_typography',
				'label'    => __( 'Typography', 'navmenu-addon-for-elementor' ),
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .elementor-search',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_search_style',
			[
				'label' => __( 'Search', 'navmenu-addon-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'search_text_color',
			[
				'label'     => __( 'Box Color', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .elementor-search, .elementor-search textarea, .elementor-search input' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'search_bg_color',
			[
				'label'     => __( 'Box Background', 'navmenu-addon-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .elementor-search' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'box_padding',
			[
				'label'      => __( 'Search Padding', 'navmenu-addon-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-search' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_search_borders',
			[
				'label' => __( 'Border', 'navmenu-addon-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'border',
				'label'    => __( 'Border', 'navmenu-addon-for-elementor' ),
				'default'  => '1px',
				'selector' => '{{WRAPPER}} .elementor-search',
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'navmenu-addon-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .elementor-search' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings(); ?>
		
		<div class="elementor-search">
			<div class="search cf">
				<div class="form">
					<form method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<label>
							<span class="screen-reader-text"><?php apply_filters( 'elementor_search_label', _ex( 'Search:', 'label', 'navmenu-addon-for-elementor' ) ); ?></span>
							<input type="search" class="input search-field" placeholder="<?php echo apply_filters( 'elementor_search_placeholder', _x( 'Type keyword & hit enter to search;', 'placeholder', 'navmenu-addon-for-elementor' ) ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" title="<?php apply_filters( 'elementor_search_label', _ex( 'Search for:', 'label', 'navmenu-addon-for-elementor' ) ); ?>">
						</label>
							<input type="submit" class="btn fa" value="">
					</form>
				</div>
				<div class="label">
					<i class="fa fa-search ib-m"></i>
					<span class="ib-m"><?php apply_filters( 'elementor_search_label', _ex( 'Search:', 'label', 'navmenu-addon-for-elementor' ) ); ?></span>
				</div>
				<div class="dismiss">
					<i class="fa fa-times ib-m"></i>
				</div>
			</div>
		</div>
		<?php
	}

	protected function _content_template() {}
}
