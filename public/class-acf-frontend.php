<?php
namespace acffrontend;
use WP_Query; 
use WP_Error; 
 class acffrontend {
    protected $types = [];
    protected $dynamicData = [];
public function __construct(){
       // $this->set_mail_types();
        $this->add_wp_actions();
     }
    public function save_new_business( $post_id ) {

        // check if this is to be a new post
        if( $post_id != 'new' ) {   
            return $post_id;
        }

        wp_create_user( $first_name . "_" . $last_name, 'random password here' );
        wp_create_user($username, $password, $email = '')
        // Create a new post
        $post = array(
            'post_status'  => 'draft' ,
            'post_title'  => 'A title, maybe a $_POST variable' ,
            'post_type'  => 'post' ,
        );  
    
        // insert the post
        $post_id = wp_insert_post( $post ); 
    
        // return the new ID
        return $post_id;
    
    }
     
private function set_mail_types(){
        $this->types['user_reg'] =   __( 'user_reg', 'carousel_elementor' );
		$this->types['send_messeg'] =  __( 'send_messeg', 'carousel_elementor' );

    } 
public function add_wp_actions(){
    add_action( 'init', [$this,'on_init' ]);
    add_shortcode('get_acf_group', [$this,'get_acf_group']);  
  //  add_filter('acf/pre_save_post' , [$this,'save_new_business'], 10, 1 );

 }
public function get_acf_group($atts = [], $content = null, $tag = ''){
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
    $wporg_atts = shortcode_atts([
                                     'title' => 'WordPress.org',
                                 ], $atts, $tag);
    $settungs = array('post_id' => 16,
     'field_groups' => array(11),
     'uploader' => 'basic'
    
);
    $o = $wporg_atts['title'];
    ob_start();
    acf_enqueue_uploader(); 
    ?>
   <script type="text/javascript">
    (function($) {
	acf.do_action('append', $('#popup-id'));	
});
</script><?php
    acf_form($settungs);                            
    $output = ob_get_contents();
    ob_end_clean();
    return $output;

}

public function test_sum(){
    $user_data = get_field( "owner" , 16 );
    return var_export($user_data);
 }
 public function send_email_to_client($record, $ajax_handler){

        $send = $record->get_form_settings( 'form_id' );
        if($send != "lead_to_client")
                return;
        $raw_fields = $record->get( 'fields' );
        foreach ( $raw_fields as $id => $field ) {
            $fields[ $id ] = (string)$field['value'];
        }
        $email = get_post_meta($fields[ 'pid' ] , 'email' , true);
     /*   Array
        (
            [ID] => 6
            [user_firstname] => John
            [user_lastname] => Doe
            [nickname] => johndoe
            [user_nicename] => johndoe
            [display_name] => John Doe
            [user_email] => john.doe@site.com
            [user_url] => 
            [user_registered] => 2016-07-03 12:23:56
            [user_description] => 
            [user_avatar] => 
        )*/
        $data = array_merge($fields, get_post_meta($fields[ 'pid' ]));
        $user_data = get_field( "owner" , $fields[ 'pid' ] );
        $data = array_merge( $data , $user_data);
        
        $this->send_email('send_messeg',$email,$data);
    }
    
public function set_custom_mail_temp_column($columns){
      $columns['type'] = __( 'Type', 'business-management' );
       return $columns;
    }
public function custom_mail_temp_column( $column, $post_id ) {
    switch ( $column ) {
    case 'type' :
            echo get_post_meta( $post_id , 'mail_type' , true ); 
            break;

    }

    }
public function save_meta_box_data($post_id){
     $error = false;
     if ( ! isset( $_POST['mail_temp_nonce'] ) ) {
        return;
    }
       
   
     if ( ! wp_verify_nonce( $_POST['mail_temp_nonce'], 'mail_temp_nonce' ) ) {
        return;
    }
       
   
     if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
        
   
     if ( ! current_user_can('administrator') ) {
            return;
        }
     if ( ! isset( $_POST['mail_type'] )) {
      
   
        return;
    }
        
   
          $args=array(
                'post__not_in' => array($post_id),
                'post_status' => 'publish',
                'post_type' => 'mail_temp',
                'posts_per_page' => -1,
                'meta_query' => array(
                        'relation' => 'AND',
                        array(
                           'key' => 'mail_type',
                           'value' => $_POST['mail_type'],
                           'compare' => '=',
                        )      
                )
		 );
                  $my_query =  new WP_Query($args);
   
   
      if( $my_query->have_posts()  && get_post_status($post_id) !== "draft"){
                $error = new WP_Error( 'typ_exsist', __( "Email Templet already exists for this language", "my_textdomain" ) );
                $_SESSION['mail_type_errors'] = $error->get_error_message();
                wp_update_post((array('ID' => $post_id,"post_status" => "draft")));
                return;
           }
        $type = sanitize_text_field( $_POST['mail_type'] );
        update_post_meta( $post_id, 'mail_type', $type );

           }
public function meta_box() {
    add_meta_box(
        'global-notice',
        __( 'mail type', 'sitepoint' ),
        [$this,'meta_box_callback'],
         'mail_temp', 
        'side', 
        'high'
    );
    }
public function meta_box_callback($post){
    if (isset($_SESSION) && array_key_exists( 'mail_type_errors', $_SESSION ) ) {?>
    <div class="error">
    <p><?php echo $_SESSION['mail_type_errors']; ?></p>
    </div><?php
    unset( $_SESSION['mail_type_errors'] );
    }
    wp_nonce_field( 'mail_temp_nonce', 'mail_temp_nonce' );

    $value = get_post_meta( $post->ID, 'mail_type', true ); 
     //   var_dump();
    ?>
    <p>Mail type</p>
    <select id="mail_type" name="mail_type">
    <?php foreach($this->types as $option => $name){ ?>
    <option value="<?php echo $option; ?>"<?=$value == $option ? ' selected="selected"' : '';?>><?php echo $name; ?></option>
     <?php } ?>
    </select>
    <?php

}
public function on_init(){
        
    acf_form_head(); 
 
    }
    public function send_email($type,$email,$dynamicData = []){
        $this->dynamicData = $dynamicData;
        $mail_content = $this->get_mail_data_by_type($type);
        $headers = array('Content-Type: text/html; charset=UTF-8;', 'From: '.get_bloginfo( "name").' <noreplay@'.str_replace(array( 'http://', 'https://','www.' ),'',home_url()).'>');
       // $mail_content= [];
     //   $mail_content['title'] = "test"; $mail_content['content'] = "test2";
		$mail_ras = wp_mail( $email, $mail_content['title'], $mail_content['content'] , $headers );
	
                       // var_dump($mail_ras);

    }
    private function get_mail_by_id($user){
          $user_info = get_userdata( $user );
	      return $user_info->user_email;     
     }
    private function get_mail_data_by_type($type){
        
                $res = [];
                $args=array(
				'post_type' => 'mail_temp',
                'posts_per_page' => 1,
                'meta_query' => array(
                        'relation' => 'AND',
                        array(
                           'key' => 'mail_type',
                           'value' => $type,
                           'compare' => '=',
                      )  ),
                    
                
		 );
        
        $my_query =  new WP_Query($args);
        if( $my_query->have_posts() ){
                      while ($my_query->have_posts()) : $my_query->the_post(); 
                      $res['id'] = get_the_ID();
                      $res['title'] =  $this->prepare_template(get_the_title());
                      $res['content'] =  $this->prepare_template(get_the_content());
                      return $res;
                      endwhile;
                  }
        return false;
    }
    private function prepare_template($template){
		foreach ($this->dynamicData as $placeholder => $value) {
			$securePlaceholder = strtoupper( $placeholder );
			$preparedPlaceholder = "{{" . $securePlaceholder . "}}";
			$template = str_replace( $preparedPlaceholder, $value, $template );
        
		}
		return $template;
	}

 }
new acffrontend();