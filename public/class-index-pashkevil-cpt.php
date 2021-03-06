<?php
namespace BusinessCpt;
use WP_Error; 
use WP_Query;

 class Business {
    protected $types = [];
    protected $dynamicData = [];
    protected $user;

    public function __construct(){
        $this->set_business_types();
        $this->add_wp_actions();
     }
    private function set_business_types(){
        $this->types['regiler'] =   __( 'regiler', 'index-pashkevil' );
        $this->types['pro'] =  __( 'pro', 'index-pashkevil' );
        $this->types['premium'] =  __( 'premium', 'index-pashkevil' );


    } 
    public function get_business_types( $types = [] ){
        return  array_merge($this->types, $types);
    }
   
    public function add_wp_actions(){
    add_filter('ajax_l_t_c', [$this,'ajax_l_t_c']);
    add_shortcode('get_bis_status', [$this,'get_bis_status']);
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
    add_filter('acf/pre_save_post' , [ $this,'acf_pre_save'], 10, 1 );
    add_action('acf/save_post',[ $this,'after_acf_save'], 20);
    add_action( 'display_one_bis', [$this,'display_one_bis'] );
    add_action( 'make_bis_pro', [$this,'make_bis_pro'],10,2 );
    add_action( 'make_bis_regiler', [$this,'make_bis_regiler'],10,1 );
    add_action('acf/validate_save_post' , [ $this,'add_user_cpt'], 10, 0 );
    add_action('delete_empty_cpt',[$this,'delete_cpt'],10,1);
    add_action( 'admin_post_nopriv', [$this,'add_user_cpt'],10,1 );

    add_action( 'pre_get_posts',  [ $this, 'example_post_ordering'], 10 );

    }
    public function get_bis_status(){
        $status =  get_post_meta(get_the_ID(),'business_type', true);
        return "<p class='status ststus_".$status."'>".$this->types[$status]."</p>";
    }
    /**
 * Modifies query before retrieving posts. Sets the 
 * `meta_query` and `orderby` param when no `orderby` 
 * param is set, (default ordering).
 * 
 * @param   WP_Query  $query  The full `WP_Query` object.
 * @return  void
 */
function example_post_ordering( $query ) {
   // if(! is_admin())
   // return;

if($query->is_main_query() && ! is_admin() && in_array ( $query->get('post_type'), array('business') )){
    $query->set('orderby','meta_value title');
    $query->set('meta_key', 'business_type');
    $query->set('order', 'ASC');
}
   

}

    public function after_acf_save($post_id){
        if (strpos(wp_get_referer(), 'wp-admin') !== false) {
            acf_reset_validation_errors();
          return;
        } 
        if(!isset($_POST['acf']['field_5c3dc6dc93b5e'])) 
        return;
    $userarray = array(
        'user_pass' => $_POST['acf']['field_5c3dc72e06c5e'],
        'user_email' => $_POST['acf']['field_5c3dc6dc93b5e'],
        'user_login' => $_POST['acf']['field_5c3dc6dc93b5e'],
        'role' =>  'owner' 
    );
    $user_pass = $_POST['acf']['field_5c3dc72e06c5e'];
    $user_email = $_POST['acf']['field_5c3dc6dc93b5e'];
  //  $user_login = $_POST['acf']['field_5c3dc6dc93b5e'];


   // $acf_nonce = $_POST['acf_nonce'];
   // $_POST['acf_nonce'] = 
    $user = wp_insert_user($userarray);
  //  global $wpdb;
   // $compacted = compact( 'user_pass', 'user_email');
    // $data = wp_unslash( $compacted );
   // $wpdb->insert( $wpdb->users, $data );              
   //  $user = (int) $wpdb->insert_id;
   // if ( is_wp_error($user) )  {
   // } 
  //  else {
  //   $_POST['acf_nonce'] = $acf_nonce;
  //  }
    $aut = wp_generate_password();
    update_user_meta( $user, 'loginonse', $aut );
    update_post_meta($post_id, 'owner', $user) ;
    wp_clear_auth_cookie();
    wp_set_current_user ( $user );
    wp_set_auth_cookie  (  $user );
    //$URL = "http://index.itech-websolutions.com/my-account/";
    $url =  add_query_arg( array("user_id" => $user , "user_aut" => $aut , "bis" => $post_id  ), get_permalink(1225));
   // var_dump($URL);
   // wp_die();
    wp_safe_redirect($url);
  //  wp_redirect($url);
//wp_die();
       exit;


    }  
    public function delete_cpt($post_id){
        update_post_meta($post_id,'name' , "tst");
      //  if(get_post_status((int)$post_id) != "publish")
        wp_delete_post((int)$post_id,true);
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
        $url = add_query_arg( 'business_id', $pid , get_permalink(332) );

        $ajax_handler->add_response_data( 'redirect_url', $url);

    }

 
    public function add_user_cpt(){
      //  if(wp_get_referer())
        if (strpos(wp_get_referer(), 'wp-admin') !== false) {
            acf_reset_validation_errors();
            return;
        }
        if(!isset($_POST['acf']['field_5c3dc6dc93b5e']))
        return;
        $user_data = array(
            'email' => $_POST['acf']['field_5c3dc6dc93b5e'],
            'pas' => $_POST['acf']['field_5c3dc72e06c5e'],
            'name' => $_POST['acf']['field_5c3dc701b0363']
        );

        if(email_exists($user_data[ 'email' ])){
          $url = add_query_arg(array('updated' => false,'formerror'=> urlencode( "משתמש קיים על מייל זה, נסה להתחבר במקום" )),wp_get_referer());
          wp_safe_redirect($url);
          exit;      
       // return;
        }
       // acf_reset_validation_errors();


    }
    public function acf_pre_save( $post_id ) {
        if(!isset($_POST['acf']['field_5c3dc6dc93b5e'])){
        do_action('reg_cpts');
        update_post_meta($post_id, 'owner', get_current_user_id()) ; 
        }
        if(get_post_status($post_id) === "publish")
        return $post_id; 
        
         //var_dump($user->ID);
         $args = array( 
            'post_status' => 'publish',
            'ID' => $post_id,     
         );
       do_action('reg_cpts');
       wp_update_post($args); 
       $email = $_POST['acf']['field_5c3dc6dc93b5e'];
       $user =  get_user_by( "email", $email );
       update_post_meta($post_id, 'owner', $user->ID) ;
     //  do_action('acf/save_post', $post_id);
        return $post_id;
    }
    public function ajax_l_t_c($fields){
        $email = get_post_meta((int)$fields[ 'pid' ] , 'email' , true);
        $data = array_merge($fields, get_post_meta((int)$fields[ 'pid' ]));
        $user_data = get_field( "owner" ,(int) $fields[ 'pid' ] );
        do_action('send_castum_email_temp','send_messeg',$email,$data);
        return true;
    }
    public function send_email_to_client($fields, $ajax_handler){
        $email = get_post_meta((int)$fields[ 'pid' ] , 'email' , true);
  
        $data = array_merge($fields, get_post_meta((int)$fields[ 'pid' ]));
        $user_data = get_field( "owner" ,(int) $fields[ 'pid' ] );
      //  $data = array_merge( $data , $user_data);
        do_action('send_castum_email_temp','send_messeg',$email,$data);
        return true;
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
      //   var_dump($test);
        ?>
        <p>סוג העסק</p>
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
        'name' => __( 'business','index-pashkevil'),
        'singular_name' => __( 'business','index-pashkevil'),
        'add_new' => __('Add business','index-pashkevil'),      
          'add_new_item' => __('Add business','index-pashkevil')
      ),
        'show_in_menu' => true,
        'show_ui' => true,
      'public' => true,
      'has_archive' => true,
      'supports' => array('title','editor')
          )
        );
        
        $labels = array(
            'name'                       => _x( 'zones', 'Taxonomy General Name', 'index-pashkevil' ),
            'singular_name'              => _x( 'zones', 'Taxonomy Singular Name', 'index-pashkevil' ),
            'menu_name'                  => __( 'zones', 'index-pashkevil' ),
            'all_items'                  => __( 'All zones', 'index-pashkevil' ),
            'parent_item'                => __( 'Parent zones', 'index-pashkevil' ),
            'parent_item_colon'          => __( 'Parent zones:', 'index-pashkevil' ),
            'new_item_name'              => __( 'New zones Name', 'index-pashkevil' ),
            'add_new_item'               => __( 'Add New zones', 'index-pashkevil' ),
            'edit_item'                  => __( 'Edit zones', 'index-pashkevil' ),
            'update_item'                => __( 'Update zones', 'index-pashkevil' ),
            'view_item'                  => __( 'View zones', 'index-pashkevil' ),
            'separate_items_with_commas' => __( 'Separate zones with commas', 'index-pashkevil' ),
            'add_or_remove_items'        => __( 'Add or remove zones', 'index-pashkevil' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'index-pashkevil' ),
            'popular_items'              => __( 'Popular zones', 'index-pashkevil' ),
            'search_items'               => __( 'Search zones', 'index-pashkevil' ),
            'not_found'                  => __( 'Not Found', 'index-pashkevil' ),
            'no_terms'                   => __( 'No zones', 'index-pashkevil' ),
            'items_list'                 => __( 'zones list', 'index-pashkevil' ),
            'items_list_navigation'      => __( 'zones list navigation', 'index-pashkevil' ),
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
    'name'                       => _x( 'tags', 'Taxonomy General Name', 'index-pashkevil' ),
    'singular_name'              => _x( 'tags', 'Taxonomy Singular Name', 'index-pashkevil' ),
    'menu_name'                  => __( 'tags', 'index-pashkevil' ),
    'all_items'                  => __( 'All tags', 'index-pashkevil' ),
    'parent_item'                => __( 'Parent tags', 'index-pashkevil' ),
    'parent_item_colon'          => __( 'Parent tags:', 'index-pashkevil' ),
    'new_item_name'              => __( 'New tags Name', 'index-pashkevil' ),
    'add_new_item'               => __( 'Add New tags', 'index-pashkevil' ),
    'edit_item'                  => __( 'Edit tags', 'index-pashkevil' ),
    'update_item'                => __( 'Update tags', 'index-pashkevil' ),
    'view_item'                  => __( 'View tags', 'index-pashkevil' ),
    'separate_items_with_commas' => __( 'Separate tags with commas', 'index-pashkevil' ),
    'add_or_remove_items'        => __( 'Add or remove tags', 'index-pashkevil' ),
    'choose_from_most_used'      => __( 'Choose from the most used', 'index-pashkevil' ),
    'popular_items'              => __( 'Popular tags', 'index-pashkevil' ),
    'search_items'               => __( 'Search tags', 'index-pashkevil' ),
    'not_found'                  => __( 'Not Found', 'index-pashkevil' ),
    'no_terms'                   => __( 'No tags', 'index-pashkevil' ),
    'items_list'                 => __( 'tags list', 'index-pashkevil' ),
    'items_list_navigation'      => __( 'tags list navigation', 'index-pashkevil' ),
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
    $labels = array(
        'name'                       => _x( 'b_category', 'Taxonomy General Name', 'index-pashkevil' ),
        'singular_name'              => _x( 'b_category', 'Taxonomy Singular Name', 'index-pashkevil' ),
        'menu_name'                  => __( 'b_category', 'index-pashkevil' ),
        'all_items'                  => __( 'All b_category', 'index-pashkevil' ),
        'parent_item'                => __( 'Parent b_category', 'index-pashkevil' ),
        'parent_item_colon'          => __( 'Parent b_category:', 'index-pashkevil' ),
        'new_item_name'              => __( 'New b_category Name', 'index-pashkevil' ),
        'add_new_item'               => __( 'Add New b_category', 'index-pashkevil' ),
        'edit_item'                  => __( 'Edit b_category', 'index-pashkevil' ),
        'update_item'                => __( 'Update b_category', 'index-pashkevil' ),
        'view_item'                  => __( 'View b_category', 'index-pashkevil' ),
        'separate_items_with_commas' => __( 'Separate b_category with commas', 'index-pashkevil' ),
        'add_or_remove_items'        => __( 'Add or remove b_category', 'index-pashkevil' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'index-pashkevil' ),
        'popular_items'              => __( 'Popular b_category', 'index-pashkevil' ),
        'search_items'               => __( 'Search b_category', 'index-pashkevil' ),
        'not_found'                  => __( 'Not Found', 'index-pashkevil' ),
        'no_terms'                   => __( 'No b_category', 'index-pashkevil' ),
        'items_list'                 => __( 'b_category list', 'index-pashkevil' ),
        'items_list_navigation'      => __( 'b_category list navigation', 'index-pashkevil' ),
    );
    
        register_taxonomy('b_category','business',array(
        'public' => true,
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array( 'slug' => 'b_category' ),
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