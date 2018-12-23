<?php
  /*$acf = [];
        $insert_terms = [];
        foreach($_POST['acf'] as $key => $value){
            $fild = get_field_object($key);
            if($fild["type"]=== "taxonomy" ){
                if(is_array($value))
                $insert_terms [$fild["taxonomy"]] = array_map('intval', $value);
                //$acf[$key] = array_map('intval', $value);
                else $insert_terms [$fild["taxonomy"]] =  array( (int)$value);
            }
            else $acf[$key] = $value;
        }
        $acf['taxonomy'] = $insert_terms;
        $_POST['acf'] = $acf;
        if (!session_id())
        session_start();
        $_SESSION['taxonomy'] = $insert_terms;
        if( $post_id === 'new' ) {
            if(!is_user_logged_in())
            return null;
            $_POST['owner'] = get_current_user_id();
            return $post_id;
        }*/
public function acf_save_data( $post_id )
{
    var_dump($_POST);
    $post_id = $_POST["_acf_post_id"];
    update_post_meta($post_id, 'test', "test");
    if(isset($_SESSION['taxonomy'])) {
    $taxonomy = $_SESSION['taxonomy'];
	//if( isset($_POST['taxonomy']) )
	//{
       // var_dump('taxonomy');

        foreach($taxonomy as $key => $value){
            do_action( 'reg_cpts' );
            if ( ! taxonomy_exists( $key ) ) {
                var_dump("rong text");
            }
           $term =  wp_set_post_terms( $post_id, $value, $key );
           if(is_wp_error($term)){
           update_post_meta($post_id, 'test', $term->get_error_message());
           var_dump($term->get_error_message());
      //fff
           }
         //   $ajax_handler->add_error_message($term->get_error_message());
        }
		//$fields = $_POST['fields'];
    }
    wp_die();


}