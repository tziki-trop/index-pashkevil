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
use DOMDocument;
use WP_Query;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class TZT_Acf_Front_And_Widget extends Widget_Base {

public function get_name() {
		return 'acf_front_end_widget'; 
	}

	public function get_title() {
		return __( 'acf_front_end_widget', 'client_to_google_sheet' );
	}

	public function get_icon() {
		return 'fa fa-file-text';
	}

	public function get_categories() {
		return [ 'pro-elements' ];
    }
    public function is_reload_preview_required() {
		return true;
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
			'fild_group_menual',
			[
				'label' => __( 'menuel fild group?', 'client_to_google_sheet' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'yes', 'client_to_google_sheet' ),
				'label_off' => __( 'no', 'client_to_google_sheet' ),
				'return_value' => 'true',
			]
        );
        $this->add_control(
			'fild_group_id_array',
			[
				'label' => __( 'Show Elements', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => [
					'11'  => __( 'regiler', 'plugin-domain' ),
					'17' => __( 'pro', 'plugin-domain' ),				],
                'default' => [ '11'],
                'condition' => [
					'fild_group_menual' => 'true',
				],
			]
        );
    
    
        $this->add_control(
			'new_post',
			[
				'label' => __( 'new_post?', 'client_to_google_sheet' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'yes', 'client_to_google_sheet' ),
				'label_off' => __( 'no', 'client_to_google_sheet' ),
				'return_value' => 'true',
			]
		);
        $this->add_control(
			'pid', [
				'label' => __( 'post id', 'client_to_google_sheet' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'show_label' => true,
				'dynamic'=>array('active'=>'true'),
                'condition' => [
					'new_post!' => 'true',
				],
			]
        );
        $this->add_control(
			'submit_text', [
				'label' => __( 'submit_text', 'client_to_google_sheet' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
        );
        $this->add_control(
			'update_messeg', [
				'label' => __( 'update_messeg', 'client_to_google_sheet' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
        );
        $this->add_control(
			'url', [
				'label' => __( 'url', 'client_to_google_sheet' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);
        $this->end_controls_section(); 
        $this->start_controls_section(
					
			'label',
			[
				'label' => __( 'label', 'client_to_google_sheet' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'label' => __( 'Typography', 'client_to_google_sheet' ),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
					'selector' => "{{WRAPPER}} .acf-label",
			]
        );
        $this->add_control(
			'label_color',
			[
				'label' => __( 'Color', 'client_to_google_sheet' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => ['{{WRAPPER}} .acf-label' => 'color: {{VALUE}}',],
			]
                );
                $this->end_controls_section(); 
                $this->start_controls_section(
                            
                    'input',
                    [
                        'label' => __( 'input', 'client_to_google_sheet' ),
                        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                    ]
                );
                $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                        'name' => 'input_typography',
                        'label' => __( 'Typography', 'client_to_google_sheet' ),
                        'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                            'selector' => "{{WRAPPER}} .acf-input",
                    ]
                );
                $this->add_control(
                    'input_color',
                    [
                        'label' => __( 'Color', 'client_to_google_sheet' ),
                        'type' => \Elementor\Controls_Manager::COLOR,
                        'default' => '#000',
                        'selectors' => ['{{WRAPPER}} .acf-input' => 'color: {{VALUE}}',],
                    ]
                        );
                        $this->end_controls_section(); 

                        $this->start_controls_section(
					
                            'submit',
                            [
                                'label' => __( 'submit', 'client_to_google_sheet' ),
                                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                            ]
                        );
                      
                     $this->add_group_control(

                        Group_Control_Typography::get_type(),
                        [
                            'name' => 'submit_typography',
                            'label' => __( 'Typography', 'client_to_google_sheet' ),
                            'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                                'selector' => "{{WRAPPER}} .acf-form-submit input",
                        ]
                    );
                    $this->add_control(
                        'submit_color',
                        [
                            'label' => __( 'Color', 'client_to_google_sheet' ),
                            'type' => \Elementor\Controls_Manager::COLOR,
                            'default' => '#000',
                            'selectors' => ['{{WRAPPER}} .acf-form-submit input' => 'color: {{VALUE}}',],
                        ]
                            );
                            $this->add_control(
                                'submit_bg_color',
                                [
                                    'label' => __( 'background color', 'client_to_google_sheet' ),
                                    'type' => \Elementor\Controls_Manager::COLOR,
                                    'default' => '#000',
                                    'selectors' => ['{{WRAPPER}} .acf-form-submit input' => 'background-color: {{VALUE}}!important',],
                                ]
                                    );
                                    $this->add_group_control(
                                        Group_Control_Border::get_type(),
                                        [
                                            'name' => 'submit_border',
                                            'label' => __( 'Border', 'plugin-domain' ),
                                            'selector' => '{{WRAPPER}} .acf-form-submit input',
                                        ]
                                    );
                                    $this->add_control(
                                        'border_radus_submit',
                                        [
                                            'label' => __( 'border radius', 'plugin-domain' ),
                                            'type' => Controls_Manager::DIMENSIONS,
                                            'size_units' => [ 'px'],
                                            'selectors' => [
                                                '{{WRAPPER}} .acf-form-submit input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                                            ],
                                        ]
                                    );
                     
     
                     
                //acf-input
		 }
  protected function render() {
   /* $dom = new DOMDocument();
    $dom->loadHTML("<html><body><div class='test'>hello</div><div>bye</div></body></html>");
    $nodes = $dom->getElementsByTagName("*");
    global $shortcode_tags;
    foreach($shortcode_tags as $code => $function)
    {
        ?>
            <tr><td>[<?php echo $code; ?>]</td></tr>
        <?php
    }
    foreach ($nodes as $node) {
       // var_dump($node);
        echo $node->getAttribute("class")."<br />";
    } */
    $settings = $this->get_settings_for_display();

    if($settings['fild_group_menual'] != "true"){
    if(!current_user_can( 'administrator' )){
    $user = (int)get_field( 'owner', $settings['pid'] );
    if(get_current_user_id() != $user)
    return '';
    }
    $fild_grups = array(11);
    $type =  get_post_meta( $settings['pid'] , 'business_type' , true ); 
    if($type === "pro")
    $fild_grups [] = 17;
    }
    else {
    
    $fild_grups = array_map('intval',$settings['fild_group_id_array']);
    }  

    acf_enqueue_uploader(); 
    ?>
         <script type="text/javascript">
           (function($) {
        	acf.do_action('append', $('#popup-id'));	
            });
            </script>
    <?php
      $settungs_acf = array();
      //$fild_grups;
      $settungs_acf['field_groups'] =   $fild_grups;
      $settungs_acf['submit_value'] =  $settings['submit_text'];
      $settungs_acf['updated_message'] =  $settings['update_messeg'];
      $settungs_acf['post_content'] = false;
      $settungs_acf['post_title'] = true;
      if($settings['pid'] != "")
      $settungs_acf['post_id'] = $settings['pid'];
      $settungs_acf['uploader'] = 'basic';
      if($settings['url'] != '')
      $settungs_acf['return'] = $settings['url'];
      if($settings['new_post'] === "true"){
        $settungs_acf['post_id'] = 'new_post';
        $settungs_acf['new_post'] = array(
             'post_type'     => 'business',
             'post_status'   => 'publish',
             'meta_input'  =>  array('business_type' => 'regiler')
        );
       
    }
      acf_form($settungs_acf);
      
    
}

    protected function _content_template(){ 
    }
}