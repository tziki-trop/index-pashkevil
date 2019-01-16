<?php
namespace acffrontend;
use WP_Query; 
use WP_Error; 
class menegge_users {
    protected $types = [];
    protected $dynamicData = [];
public function __construct(){
       // $this->set_mail_types();
        $this->add_wp_actions();
     }
     public function add_wp_actions(){
        add_action('after_setup_theme', [$this,'remove_admin_bar']);
        add_action( 'init', [$this,'register_role' ]);
      //  add_action( 'elementor_pro/forms/validation', [$this,'add_user'],10,2 ); 
        add_action( 'elementor_pro/posts/query/owner_business',[$this,'user_query']);
        add_action('add_user_to_index', [$this,'add_user'], 10, 2);
        add_action('update_user_to_index', [$this,'update_user'], 10, 2);
        add_action('login_user_to_index', [$this,'login_user'], 10, 2);
        add_filter('get_user_bus',  [$this,'get_user_bus']);
        add_filter('add_user_acf', [$this,'add_user_acf'], 10, 1);    

     }
     public function add_user_acf($fields){
        if(email_exists($fields[ 'email' ])){
            return array('status' => false,'error'=>"משתמש קיים על מייל זה, נסה להתחבר במקום" );
           // $ajax_handler->add_error_message("user exsist");
          //  return;
            }
        $userarray = array(
                'user_pass' => $fields[ 'pas' ],
                'user_email' => $fields[ 'email' ],
                'user_login' => $fields[ 'email' ],
                'role' =>  'owner' 
            );
        $user = wp_insert_user($userarray);
            //$user = wp_create_user($fields[ 'email' ], $fields[ 'pas' ], $fields[ 'email' ]);
        if(is_wp_error($user)){
            return array('status' => false,'error'=>$user->get_error_message());
          //  $ajax_handler->add_error_message($user->get_error_message());
          //  return;
            }
            do_action('send_castum_email_temp','user_reg',$fields[ 'email' ],$fields);
            wp_clear_auth_cookie();
            wp_set_current_user ( $user );
            wp_set_auth_cookie  ( $user );
            return array('status' => true,'user_id'=>$user);
     //   $ajax_handler->add_response_data( 'redirect_url', get_permalink(51));
     }
     public function get_user_bus(){
        $bus = false;
        $args = array(
            'post_type'    => 'business',
            'meta_key'     => 'owner',
            'meta_value'   => get_current_user_id(), // change to how "event date" is stored
            'meta_compare' => 'LIKE',
        );
     //   return $args;
        $user_post_loop = new WP_Query( $args );
        if ($user_post_loop->have_posts() ) :
            $bus[''] = "בחר עסק";

            while ( $user_post_loop->have_posts() ) : $user_post_loop->the_post();
            $bus[get_the_ID()] = get_the_title();
        endwhile;   
        else :
        endif; 
        wp_reset_query();
      
       return $bus;
     }
     public function user_query($query){
             $meta =  array(
         'relation' => 'AND',
          array(
            'key' => 'owner',
            'value' => get_current_user_id(),
            'compare' => 'LIKE',
       ));
        
            //$query->set( 'post_author', get_current_user_id() );
              
              $query->set( 'meta_query', $meta );
              $query->set( 'post_status', 'any' );

    }
     public function register_role(){

        $result = add_role(
            'owner',
            __( 'owner' ),
            array(
                'read'         => true,  // true allows this capability
                'edit_posts'   => false,
                'delete_posts' => false, 
                'edit_private_pages' => true,
                'upload_files' => true,
                'edit_posts' => true,
                'read_private_posts' => true,// Use false to explicitly deny
            )
        );
     }
    public function remove_admin_bar() {
       if (!current_user_can('administrator') && !is_admin()) {
          show_admin_bar(false);
     }
     }
     public function add_user($fields,$ajax_handler){
        if(email_exists($fields[ 'email' ])){
            $ajax_handler->add_error_message("user exsist");
            return;
            }
        $userarray = array(
                'user_pass' => $fields[ 'pas' ],
                'user_email' => $fields[ 'email' ],
                'user_login' => $fields[ 'email' ],
                'role' =>  'owner' 
            );
        $user = wp_insert_user($userarray);
            //$user = wp_create_user($fields[ 'email' ], $fields[ 'pas' ], $fields[ 'email' ]);
        if(is_wp_error($user)){
            $ajax_handler->add_error_message($user->get_error_message());
            return;
            }
        do_action('send_castum_email_temp','user_reg',$fields[ 'email' ],$fields);
            wp_clear_auth_cookie();
            wp_set_current_user ( $user );
            wp_set_auth_cookie  ( $user );
        $ajax_handler->add_response_data( 'redirect_url', get_permalink(51));
     }
     public function update_user($fields,$ajax_handler){
            $userarray = array(
                'ID' => get_current_user_id(),
                'user_pass' => $fields[ 'pas' ],
            );    
            $user =  wp_update_user($userarray);
            if(is_wp_error($user)){
            $ajax_handler->add_error_message($user->get_error_message());
            return;
            }
        
        
     }
     public function login_user($fields,$ajax_handler){
      
            $user = wp_authenticate( $fields[ 'email' ], $fields[ 'pas' ]);
            if(is_wp_error($user)){
            $ajax_handler->add_error_message($user->get_error_message());
            return;
            } 
            wp_clear_auth_cookie();
            wp_set_current_user ( $user->ID );
            wp_set_auth_cookie  ( $user->ID ); 
            $ajax_handler->add_response_data( 'redirect_url', get_permalink(109));
        
     }
     public function add_user_hold ( $record, $ajax_handler ) {
       // $ajax_handler->add_error_message("error");

        $send = $record->get_form_settings( 'form_id' );
            if($send === "add_user"){
        //return;   
        $raw_fields = $record->get( 'fields' );
        $fields = [];   
        foreach ( $raw_fields as $id => $field ) {
            $fields[ $id ] = (string)$field['value'];
        }      
        if(email_exists($fields[ 'email' ])){
        $ajax_handler->add_error_message("user exsist");
        return;
        }
       // $fields[ 'pas' ] = wp_generate_password();
        $userarray = array(
            'user_pass' => $fields[ 'pas' ],
            'user_email' => $fields[ 'email' ],
            'user_login' => $fields[ 'email' ],
            'role' =>  'owner' 
        );
        $user = wp_insert_user($userarray);
        //$user = wp_create_user($fields[ 'email' ], $fields[ 'pas' ], $fields[ 'email' ]);
        if(is_wp_error($user)){
        $ajax_handler->add_error_message($user->get_error_message());
        return;
          }
        do_action('send_castum_email_temp','user_reg',$fields[ 'email' ],$fields);
        wp_clear_auth_cookie();
        wp_set_current_user ( $user );
        wp_set_auth_cookie  ( $user );
        $ajax_handler->add_response_data( 'redirect_url', get_permalink(51));
      }
              if($send === "update_user"){
        $raw_fields = $record->get( 'fields' );
        $fields = [];   
        foreach ( $raw_fields as $id => $field ) {
            $fields[ $id ] = (string)$field['value'];
        } 
        $userarray = array(
            'ID' => get_current_user_id(),
            'user_pass' => $fields[ 'pas' ],
        );
       // $ajax_handler->add_error_message($fields[ 'pas' ]);

         $user =  wp_update_user($userarray);
        if(is_wp_error($user)){
        $ajax_handler->add_error_message($user->get_error_message());
        return;
        }
    
         }
             if($send === "login_user"){
        $raw_fields = $record->get( 'fields' );
        $fields = [];   
        foreach ( $raw_fields as $id => $field ) {
            $fields[ $id ] = (string)$field['value'];
        } 
        $user = wp_authenticate( $fields[ 'email' ], $fields[ 'pas' ]);
        if(is_wp_error($user)){
        $ajax_handler->add_error_message($user->get_error_message());
        return;
        } 
        wp_clear_auth_cookie();
        wp_set_current_user ( $user->ID );
        wp_set_auth_cookie  ( $user->ID ); 
        $ajax_handler->add_response_data( 'redirect_url', get_permalink(109));
         }
    

        
    
    }   
}
    new menegge_users();
