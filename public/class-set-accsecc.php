<?php
namespace BusinessAccsecc;
use WP_Error; 
use WP_Query;

 class BusinessAccseccClass {
    const LOGIN_PAGE = 116;
    const REGISTER_PAGE = 73;
    const ADD_BUSINESS = 51;
    const ACCOUNT_PAGE = 109;
    public function __construct(){
        $this->add_wp_actions();
     }
     public function add_wp_actions(){
        add_action( 'wp', [$this,'set_access'] );
     }
    protected function get_user_role(){
		if( is_user_logged_in() ) {
            $user = wp_get_current_user();
        $role = ( array ) $user->roles;
            return $role[0];
     } else {
     return false;
     }
    }
    public function set_access(){ 
     
		$role = $this->get_user_role();
		if($role === "administrator")
        return;
        if(is_singular( 'business' )){
        $user = (int)get_field( 'owner', get_queried_object_id() );
        if(get_current_user_id() == $user)
        return;
        wp_redirect(get_permalink(self::LOGIN_PAGE),301);  
        }
        if((is_page(self::REGISTER_PAGE) || is_page(self::LOGIN_PAGE)) && is_user_logged_in()){
        wp_redirect(get_permalink(self::ACCOUNT_PAGE));  
        }
        if( is_page(self::ACCOUNT_PAGE) && !is_user_logged_in()){
            wp_redirect(get_permalink(self::REGISTER_PAGE));  
        }
        if( is_page(self::ADD_BUSINESS) && !is_user_logged_in()){
            wp_redirect(get_permalink(self::REGISTER_PAGE));  
        } 
        if(isset($_GET['pid']) && (int)$_GET['pid'] === 0)  {
            $vars = array('new_business_added' => true);  
            $url = add_query_arg($vars,get_permalink(self::ACCOUNT_PAGE));
            wp_redirect($url);  
        }
        if(isset($_GET['user_aut']))  {
         $aut =    get_user_meta($_GET['user_id'], 'loginonse', true);
         if($_GET['user_aut'] === $aut){
            update_user_meta( $user, 'loginonse', '' );

            wp_clear_auth_cookie();
           wp_set_current_user ( (int) $_GET['user_id'] );
           wp_set_auth_cookie  ( (int) $_GET['user_id'] );
         }
 
        }
     }
}   
new BusinessAccseccClass();