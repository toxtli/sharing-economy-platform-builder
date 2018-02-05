<?php
namespace ElementorMenus\Modules\Branding;

use Elementor\Plugin;
use ElementorMenus\Base\Module_Base;

class Module extends Module_Base {

	public function __construct() {
		parent::__construct();

		// $this->add_actions();
	}

	public function get_name() {
		return 'elementor-branding';
	}

	public function get_widgets() {
		return [
			'Elementor_Branding',
		];
	}

}
