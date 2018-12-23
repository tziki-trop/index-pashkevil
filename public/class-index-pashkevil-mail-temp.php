<?php
namespace CustomEmail;
use WP_Query; 
use WP_Error; 
 class Custom_Email {
    protected $types = [];
    protected $dynamicData = [];
    public function __construct(){
        $this->set_mail_types();
        $this->add_wp_actions();
     }
    private function set_mail_types(){
        $this->types['user_reg'] =   __( 'user_reg', 'carousel_elementor' );
		$this->types['send_messeg'] =  __( 'send_messeg', 'carousel_elementor' );

    } 
    public function add_wp_actions() {
     add_action( 'init', [$this,'on_init' ]);
     add_action( 'add_meta_boxes_mail_temp', [$this,'meta_box' ]);
     add_action( 'save_post_mail_temp', [$this,'save_meta_box_data' ]);
         add_filter( 'manage_mail_temp_posts_columns', [$this,'set_custom_mail_temp_column'] );
      add_action( 'manage_mail_temp_posts_custom_column' , [$this,'custom_mail_temp_column'], 10, 2 ); 
     add_action('send_castum_email_temp', [$this,'send_email'],10,3 );
    // add_action( 'elementor_pro/forms/validation', [ $this,'send_email_to_client'],10,2 ); 
   // add_shortcode('test_shit', [$this,'test_sum']);  
 }
 public function test_sum(){
    $user_data = get_field( "owner" , 16 );
    return var_export($user_data);
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
               if( !session_id() )
                   session_start();
  
 register_post_type( 'mail_temp',
    array(
      'labels' => array(
        'name' => __( 'mail_temp', 'donat'),
        'singular_name' => __( 'mail_temp', 'donat'),
        'add_new' => __('Add mail_temp','donat'),      
          'add_new_item' => __('Add mail_temp','donat')
      ),
        'show_in_menu' => true,
        'show_ui' => true,
      'public' => false,
      'has_archive' => false,
      'supports' => array('custom-fields','title','editor')
    )
  );
           }
public function send_email($type,$email,$dynamicData = []){
       // $this->dynamicData = $dynamicData;
        $mail_content = $this->get_mail_data_by_type($type , $dynamicData);
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
    private function get_mail_data_by_type($type , $dynamicData){
        
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
                      $res['title'] =  $this->prepare_template(get_the_title(),$dynamicData);
                      $res['content'] =  $this->prepare_template(get_the_content(),$dynamicData);
                      return $res;
                      endwhile;
                  }
        return false;
    }
    private function prepare_template($template  , $dynamicData){
		foreach ($dynamicData as $placeholder => $value) {
			//$securePlaceholder = strtoupper( $placeholder );
			$placeholder = "{{" . $placeholder . "}}";
			$template = str_replace( $placeholder, $value, $template );
		}
		return $template;
	}

 }
new Custom_Email();