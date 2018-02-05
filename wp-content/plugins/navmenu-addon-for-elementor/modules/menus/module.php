<?php
namespace ElementorMenus\Modules\Menus;

use Elementor\Plugin;
use ElementorMenus\Base\Module_Base;

class Module extends Module_Base {

	public function __construct() {
		parent::__construct();

		// $this->add_actions();
	}

	public function get_name() {
		return 'elementor-menus';
	}

	public function get_widgets() {
		return [
			'Default_Navmenu',
			'Navmenu_Overlay',
			'Mega_Menu',
		];
	}

}
