<?php
namespace Pashkevil\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use ElementorPro\Base\Base_Widget;
use Donations\Widgets\Helpers;
use WP_Query;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class TZT_Get_Terms_Widget extends Widget_Base {

public function get_name() {
		return 'terms_widget'; 
	}

	public function get_title() {
		return __( 'terms widget', 'client_to_google_sheet' );
	}

	public function get_icon() {
		return 'fa fa-file-text';
	}
	public function is_reload_preview_required() {
		return true;
	}
	public function get_categories() {
		return [ 'pro-elements' ];
	}
	 protected function _register_controls() {
		 	$this->start_controls_section(
					
			'content',
			[
				'label' => __( 'Content', 'client_to_google_sheet' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
        );
        $this->add_control(
			'taxonomy', [
				'label' => __( 'taxonomy', 'client_to_google_sheet' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
        );
     
        $this->end_controls_section(); 

        $this->start_controls_section(
			'text_style',
			[
				'label' => __( 'Text', 'client_to_google_sheet' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
           );
           $this->add_responsive_control(
			'term_margin',
			[
				'label' => __( 'term_margin', 'popuplabels' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px','%' ],
				'selectors' => [
					'{{WRAPPER}} p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'default' => [
                 'top' => 5,
                 'right' => 5,
                  'bottom' => 5,
                 'left' => 5,
                'isLinked' => true,
               ],
			]
		);
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'terms_typography',
				'label' => __( 'Typography', 'client_to_google_sheet' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} p',
			]
		);
    
		$this->end_controls_section(); 
		 } 
	
  protected function render() {
    $settings = $this->get_settings_for_display();
    $term_list = wp_get_post_terms( get_the_ID(), $settings['taxonomy'], array( 'fields' => 'names' ) );
       foreach($term_list as $term){
           ?>
            <p><?php echo $term; ?></p>

           <?php
       }

    }

    protected function _content_template(){ 

    }
}