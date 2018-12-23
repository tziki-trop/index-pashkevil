<?php
namespace acffrontend;
use WP_Query; 
use WP_Error; 
class load_mor_bis {
    protected $types = [];
    protected $dynamicData = [];
public function __construct(){
       // $this->set_mail_types();
        $this->add_wp_actions();
     }
     public function add_wp_actions(){
         add_action('add_js_data_to_page',[$this,'add_js_var']);
         add_action('wp_ajax_loadmore', [$this,'loadmore']); // wp_ajax_{action}
         add_action('wp_ajax_nopriv_loadmore', [$this,'loadmore']);
       //  add_action( 'wp_enqueue_scripts', [$this,'load_more_scripts']);
     
     }
     public function load_more_scripts(){
        $src = plugin_dir_path( dirname( __FILE__ ) ) . 'public/js/load-more-js.js';
        wp_enqueue_script( 'loadmore_ajax', $src, array('jquery'),1, true);
     }
     public function add_js_var($data){
     
        wp_localize_script( 'loadmore_ajax', 'loadmore_params', array(
            'bottomOffset' => 300,
            'canBeLoaded' => true,
            'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
            'posts' => json_encode( $data['query_vars'] ), // everything about your loop is here
            'current_page' => get_query_var( 'paged' ) ? get_query_var('paged') : 1,
            'max_page' => $data['max_num_pages']
        ) );

     }
     public function loadmore(){
        $args = json_decode( stripslashes( $_POST['query'] ), true );
        $args['paged'] = $_POST['page'] + 1; // we need next page to be loaded
        query_posts( $args );

             if( have_posts() ) :
             while( have_posts() ): the_post();
             do_action('display_one_bis');
             endwhile;
             endif;
             die;
     }
  
    

  
   
   
    
  
    
}
    new load_mor_bis();
