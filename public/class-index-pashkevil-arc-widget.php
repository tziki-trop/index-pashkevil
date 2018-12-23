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
class TZT_Archive_Widget extends Widget_Base {

public function get_name() {
		return 'archive_widget'; 
	}

	public function get_title() {
		return __( 'archive widget', 'client_to_google_sheet' );
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
    
		$this->end_controls_section(); 
		 } 
		
     protected function set_query(){
      global $wp_query;        
	  $settings = $this->get_settings_for_display();
	//  $wp_query=>set('post_status' => array('publish')),
	  if( $wp_query->have_posts() ):
		$data = [];
        $data['query_vars'] = $wp_query->query_vars;
		$data['max_num_pages'] = $wp_query->max_num_pages;
		do_action('add_js_data_to_page', $data);

      ?>
	  	<div class="all_busienss">
		<?php wp_nonce_field( 'validate', 'ajax_nonce' ); ?>

		  <?php
		 while ($wp_query->have_posts()) : $wp_query->the_post();
		 do_action('display_one_bis');
		 endwhile;

         ?>
		 </div><?php
     endif;
         
     }
  protected function render() {
	   $this->set_query();

    }

    protected function _content_template(){ 

    }
}