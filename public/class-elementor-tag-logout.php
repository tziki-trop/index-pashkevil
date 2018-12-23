<?php
namespace Pashkevil\Tags;
use Elementor\Core\DynamicTags\Tag;
if ( ! defined( 'ABSPATH' ) ) exit;
class TZT_Tag_log_our extends \Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'log-out-url';
	}

	public function get_title() {
		return __( 'Log Out', 'elementor-pro' );
	}

	public function get_group() {
		return 'meta-variables';
	}

	public function get_categories() {
		return [ \Elementor\Modules\DynamicTags\Module::URL_CATEGORY ];
	}
protected function _register_controls() {

		
     
		$this->add_control(
			'get_name',
			[
				'label' => __( 'URL to redirect', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::URL,
				
			]
		);
		
		
	}

	public function render() {
		$param_name = $this->get_settings( 'get_name' );
        $val =   wp_logout_url( $param_name['url'] );	  
		echo $val;
	}
}