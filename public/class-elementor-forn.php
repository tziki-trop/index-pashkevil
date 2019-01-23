<?php
namespace acffrontend;
use WP_Query; 
use WP_Error; 
class menege_elementor_form {

public function __construct(){
       // $this->set_mail_types();
        $this->add_wp_actions();
     }
    public function add_wp_actions(){
        add_action( 'elementor_pro/forms/validation', [$this,'manegg_form'],10,2 ); 
        add_action('wp_ajax_l_t_c', [$this,'ajax_l_t_c']);
        add_action('wp_ajax_admin_l_t_c',[$this, 'ajax_l_t_c']);   
    }
   
    public function ajax_l_t_c(){
       //   echo json_encode($_POST['form_fields']);
      //  exit;
   
        $res =  apply_filters("ajax_l_t_c",$_POST['form_fields']);
        echo json_encode(array($res));
        exit;
    }
    public function manegg_form ( $record, $ajax_handler ) {
        $raw_fields = $record->get( 'fields' );
        $fields = [];   
        foreach ( $raw_fields as $id => $field ) {
            $fields[ $id ] = (string)$field['value'];
        } 
         switch($record->get_form_settings( 'form_id' )){
             case  "add_user" :
             do_action("add_user_to_index", $fields , $ajax_handler );
             break;
             case  "update_user" :
             do_action("update_user_to_index", $fields , $ajax_handler );
             break;
             case  "login_user" :
             do_action("login_user_to_index", $fields , $ajax_handler );
             break;
             case  "add_bis" :
             do_action("add_bis_to_index", $fields , $ajax_handler );
             break;
             case "lead_to_client" :
             do_action("lead_to_client",$field,$ajax_handler);

         }  
    }   
}
    new menege_elementor_form();
