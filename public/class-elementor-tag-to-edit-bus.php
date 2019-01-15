<?php
namespace Pashkevil\Tags;
use Elementor\Core\DynamicTags\Tag;
if ( ! defined( 'ABSPATH' ) ) exit;
class TZT_go_to_edit_bus extends \Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'go_to_edit_bus';
	}
  
	public function get_title() {
		return __( 'go_to_edit_bus', 'elementor-pro' );
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
        $pid = get_the_ID();
        $url = add_query_arg( 'business_id', $pid , get_permalink(332) );
	  
		echo $url;
	}
}