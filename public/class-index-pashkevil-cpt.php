<?php
namespace BusinessCpt;
use WP_Error; 
use WP_Query;

 class Business {
    protected $types = [];
    protected $dynamicData = [];
    public function __construct(){
        $this->set_business_types();
        $this->add_wp_actions();
     }
    private function set_business_types(){
        $this->types['regiler'] =   __( 'regiler', 'carousel_elementor' );
		$this->types['pro'] =  __( 'pro', 'carousel_elementor' );

    } 
    public function get_business_types( $types = [] ){
        return  array_merge($this->types, $types);
    }
    public function add_wp_actions(){
    add_filter( 'get_business_types', [$this,'get_business_types']);
    add_action( 'init', [$this,'on_init' ]);
    add_action( 'reg_cpts', [$this,'on_init' ]);
    add_action('add_bis_to_index', [$this,'add_bis'], 10, 2);
    add_action('lead_to_client', [$this,'send_email_to_client'], 10, 2);
    add_action( 'add_meta_boxes_business', [$this,'meta_box' ]);
    add_action( 'save_post_business', [$this,'save_meta_box_data' ]);
    add_filter( 'manage_business_posts_columns', [$this,'set_custom_business_column'] );
    add_action( 'manage_business_posts_custom_column' , [$this,'custom_business_column'], 10, 2 ); 
    add_filter('acf/fields/google_map/api', [ $this,'my_acf_google_map_api']);
    add_action( 'wp_ajax_get_business',  [ $this,'get_business'] );
    add_action( 'wp_ajax_nopriv_get_business',  [ $this,'get_business' ]);
  //  add_action( 'elementor_pro/forms/validation', [ $this,'send_email_to_client'],10,2 ); 
    add_filter('acf/pre_save_post' , [ $this,'acf_pre_save'], 10, 1 );
   // add_filter( 'wp_insert_post_empty_content', [$this,'disable_save'], 999999, 2 );
  // add_action('acf/save_post', [$this,'acf_save_data'], 20,1);
   // add_action( 'save_post', [$this,'acf_save_data'] );
    add_action( 'display_one_bis', [$this,'display_one_bis'] );
    add_action( 'make_bis_pro', [$this,'make_bis_pro'],10,2 );
    add_action( 'make_bis_regiler', [$this,'make_bis_regiler'],10,1 );
    }
    public function make_bis_pro($post_id,$exp = false){
        update_post_meta($post_id, 'business_type', 'pro');
        if($exp != false)
        wp_schedule_single_event($exp, 'make_bis_regiler', $post_id);

    }
    public function make_bis_regiler($post_id){
        update_post_meta($post_id, 'business_type', 'regiler');
    }
    public function display_one_bis(){
        $type =  get_post_meta( get_the_ID() , 'business_type' , true ); 
        if(!array_key_exists($type,$this->types))
        return;
		?>
				<div class="one_bus" data-bus-id="<?php echo get_the_ID(); ?>" data-bus-type="<?php echo $type; ?>"><?php
				elementor_theme_do_location( 'abusiness_'.$type );
				?></div>
        <?php
      
    }
    public function add_bis($fields,$ajax_handler){
        if ( ! function_exists( 'post_exists' ))
        require_once( ABSPATH . 'wp-admin/includes/post.php' );
            
            if(post_exists($fields['name']))
            {
                $ajax_handler->add_error_message("עסק בשם זה כבר קיים, אנא נסה שם אחר");
                return;

            }
            if(!is_user_logged_in())
            {
                $ajax_handler->add_error_message("עלייך להרשם קודם למערכת");
                return;

            }
        $args = array(
            'post_status' => "pendine_approvel",
            'post_title' => $fields['name'],
            'post_type' => 'business',
            'meta_input' => array(
                'owner' => get_current_user_id(),
                'business_type' => 'regiler',
            )
            );
        $pid = wp_insert_post($args);
        if(is_wp_error($pid)){
        $ajax_handler->add_error_message($user->get_error_message());
        return;
        }
        $ajax_handler->add_response_data( 'redirect_url', get_permalink($pid));

    }

    public function acf_save_data( $post_id ){
         if(isset($_SESSION['owner'])) {
         update_post_meta($post_id, 'owner', $_SESSION['owner']);
         unset($_SESSION['owner']);
        //  wp_redirect(get_permalink($post_id));  

           }
    }
    public function acf_pre_save( $post_id ) {
       $args = array( 
           'post_status' => 'publish',
           'ID' => $post_id,
       
        );
     //   echo get_post_meta( $post_id , 'business_type' , true ); 

       do_action('reg_cpts');
       wp_update_post($args);
        return $post_id;
    }

    public function send_email_to_client($fields, $ajax_handler){
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
        do_action('send_castum_email_temp','send_messeg',$email,$data);
    }
    public function my_acf_google_map_api( $api ){
	        //AIzaSyBpBpabON9ZP9otcxyd7eXudE_zNkgT6tQ
        $api['key'] = '';
        
        return $api;
        
    }
    public function get_business(){	  
   
      if ( !wp_verify_nonce( $_POST['nons'], 'validate' )){
        echo json_encode (array('status' => "error",'error' =>'no validate' ) );
        wp_die();
         }

      if(!isset($_POST['type']) || !isset($_POST['id'])){
      echo json_encode ( array('status' => "error",'error' =>'no bus' ) );
         wp_die();
     }
         //global $wp_query;
        $args=array(  
        'post_type' => 'business',
        'post_status' => 'publish',
        'post__in' =>  array((int)$_POST['id'])
     );

            $wp_query = new WP_Query( $args);
            //$wp_query = new WP_Query( ); sdsdf
    
        $output = $wp_query->have_posts();
        if( $wp_query->have_posts() ){
        //$output = "test1";
        //test t
        while ($wp_query->have_posts()) : $wp_query->the_post();
               ob_start();
               //echo get_the_ID(); 
               //$type =  get_post_meta( get_the_ID() , 'business_type' , true ); 
               elementor_theme_do_location( 'abusiness_extended_'.$_POST['type'] );
               $output = ob_get_contents();
               ob_end_clean();
        endwhile;
       
        }   
        //wp_reset_query();
        echo json_encode(array('status' => "seccsee",'data' => $output ));
        wp_die();
      
}
  
    public function set_custom_business_column($columns){
       $columns['type'] = __( 'Type', 'business-management' );
       return $columns;
    }
    public function custom_business_column( $column, $post_id ) {
            switch ( $column ) {
            case 'type' :
            echo get_post_meta( $post_id , 'business_type' , true ); 
            break;

                }

    }
    public function save_meta_box_data($post_id){
     $error = false;
     if ( ! isset( $_POST['business_nonce'] ) ) {
        return;
        }
       
   
     if ( ! wp_verify_nonce( $_POST['business_nonce'], 'business_nonce' ) ) {
        return;
        }
       
   
     if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
     }
        
   
     if ( ! current_user_can('administrator') ) {
            return;
        }
     if ( ! isset( $_POST['business_type'] )) {
      
   
        return;
        }
    
        $type = sanitize_text_field( $_POST['business_type'] );
        update_post_meta( $post_id, 'business_type', $type );

           }
    public function meta_box() {
            add_meta_box(
        'global-notice',
        __( 'business type', 'sitepoint' ),
        [$this,'meta_box_callback'],
         'business', 
        'side', 
        'high'
         );
    }
    public function meta_box_callback($post){
            if (isset($_SESSION) && array_key_exists( 'business_type_errors', $_SESSION ) ) {?>
            <div class="error">
            <p><?php echo $_SESSION['business_type_errors']; ?></p>
        </div><?php
        unset( $_SESSION['business_type_errors'] );
        }
        wp_nonce_field( 'business_nonce', 'business_nonce' );

     $value = get_post_meta( $post->ID, 'business_type', true ); 
     $test = get_post_meta( $post->ID, 'test', true ); 
     $test1 = get_field_object('field_5c103c824c00a', $post->ID);
         var_dump($test);
        ?>
        <p>business type</p>
        <select id="business_type" name="business_type">
            <?php foreach($this->types as $option => $name){ ?>
                <option value="<?php echo $option; ?>"<?=$value == $option ? ' selected="selected"' : '';?>><?php echo $name; ?></option>
                <?php } ?>
                    </select>

             <?php

        }
    public function on_init(){
        acf_form_head(); 
   
               if( !session_id() )
                   session_start();
  
            register_post_type( 'business',
         array(
      'labels' => array(
        'name' => __( 'business', 'donat'),
        'singular_name' => __( 'business', 'donat'),
        'add_new' => __('Add business','donat'),      
          'add_new_item' => __('Add business','donat')
      ),
        'show_in_menu' => true,
        'show_ui' => true,
      'public' => true,
      'has_archive' => true,
      'supports' => array('title','editor')
          )
        );
        $labels = array(
            'name' => __( 'zones', 'taxonomy general name' ),
     'singular_name' => __( 'zones', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search zones' ),
        'popular_items' => __( 'Popular zones' ),
        'all_items' => __( 'All zones' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'Edit zones' ), 
        'update_item' => __( 'Update zones' ),
        'update_item' => __( 'Update zones' ),
        'add_new_item' => __( 'Add New zones' ),
                'new_item_name' => __( 'New zones Name' ),
     'separate_items_with_commas' => __( 'Separate zoness with commas' ),
      'add_or_remove_items' => __( 'Add or remove zones' ),
     'choose_from_most_used' => __( 'Choose from the most used zones' ),
      'menu_name' => __( 'zones' ),
  ); 
         register_taxonomy('zones','business',array(
	'public' => true,
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var' => true,
    'rewrite' => array( 'slug' => 'zones' ),
	 'show_admin_column' => true
  ));
  
        $labels = array(
    'name' => __( 'tags', 'taxonomy general name' ),
    'singular_name' => __( 'tags', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search tags' ),
    'popular_items' => __( 'Popular tags' ),
    'all_items' => __( 'All tags' ),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Edit tags' ), 
    'update_item' => __( 'Update tags' ),
    'update_item' => __( 'Update tags' ),
    'add_new_item' => __( 'Add New tags' ),
    'new_item_name' => __( 'New tags Name' ),
    'separate_items_with_commas' => __( 'Separate tagss with commas' ),
    'add_or_remove_items' => __( 'Add or remove tags' ),
    'choose_from_most_used' => __( 'Choose from the most used tags' ),
    'menu_name' => __( 'tags' ),
  ); 
    register_taxonomy('tags','business',array(
	'public' => true,
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var' => true,
    'rewrite' => array( 'slug' => 'tags' ),
	 'show_admin_column' => true
    ));
    register_post_status('pendine_approvel' , array(
        'label'                     => 'empty',
        'public'                    => false,
        'private'                   => true,
        'exclude_from_search'       => true,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'pendine_approvel <span class="count">(%s)</span>','empty <span class="count">(%s)</span>' ),
        ) );
           }
 
        }
new Business();