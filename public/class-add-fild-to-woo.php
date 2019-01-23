<?php
if ( ! defined( 'ABSPATH' ) ) {
 exit;
}
/**
 * Display the custom text field
 * @since 1.0.0
 */


class MeneggeWoocommercr {
public function __construct(){
        $this->add_wp_actions();
    }
 public function add_wp_actions(){
        add_action( 'woocommerce_product_options_general_product_data', [$this,'create_custom_field'] );
        add_action( 'woocommerce_process_product_meta', [$this,'save_custom_field'] );
        add_action( 'woocommerce_before_add_to_cart_button', [$this,'display_custom_field'] );
        add_filter( 'woocommerce_add_to_cart_validation', [$this,'validate_custom_field'], 10, 3 );
        add_action( 'woocommerce_before_calculate_totals', [$this,'before_calculate_totals'], 10, 1 );
        add_filter( 'woocommerce_add_cart_item_data', [$this,'add_custom_field_item_data'], 10, 4 );

        add_filter( 'woocommerce_cart_item_name', [$this,'cart_item_name'], 10, 3 );
     //   add_action( 'woocommerce_checkout_update_order_meta', [$this,'add_custom_data_meta_to_order'], 10, 3 );
        add_action( 'woocommerce_checkout_create_order_line_item', [$this,'add_custom_data_to_order'], 10, 4 );

        add_action('woocommerce_subscription_status_updated', [$this,'on_sub_change_status'], 10, 3);
        add_filter( 'woocommerce_account_menu_items', [$this,'menu_items'] );
       // woocommerce_account_
        add_action( 'woocommerce_account_business_endpoint', [$this,'point_business'] );
      
         
        add_action( 'init',[$this, 'iconic_add_my_account_endpoint'] );
    }
  public  function iconic_add_my_account_endpoint() {
 
        add_rewrite_endpoint( 'business', EP_PAGES );
     
    }
    
public function point_business() {
   // echo "test";
        //content goes here
        echo do_shortcode("[elementor-template id=\"378\"]");
      //  echo '//content goes here';    
    }
public function menu_items( $items ) {
    $bis = array('business' => __( 'העסקים שלי', 'webkul' ));
    $items = $bis + $items;

    return $items;
}
public function on_sub_change_status($order,$status,$old_status){
    
      

        $items = $order->get_items();
       foreach ( $order->get_items() as $item ) {
            $product = $item->get_product();
            if($status != "active")
             $level = "regiler";
             else
            $level = $product->get_attribute( 'pa_level' );
            $post_id = $item->get_meta('business');
            update_post_meta((int)$post_id,'business_type' , $level);
       }
  }
public function create_custom_field() {
 $args = array(
 'id' => 'business_field',
 'label' => __( 'שם העסק', 'cfwc' ),
 'class' => 'cfwc-custom-field',
 'desc_tip' => true,
 'description' => __( 'הכנס את מזהה העסק פה', 'ctwc' ),
 );
 woocommerce_wp_text_input( $args );
}

public function save_custom_field( $post_id ) {
 $product = wc_get_product( $post_id );
 $title = isset( $_POST['business_field'] ) ? $_POST['business_field'] : '';
 $product->update_meta_data( 'business', sanitize_text_field( $title ) );
 $product->save();
}

public function display_custom_field() {
 global $post;
 // Check for the custom field value
 $product = wc_get_product( $post->ID );
 $title = $product->get_meta( 'business_field' );
 $business = apply_filters('get_user_bus',array());

 //var_dump( $business);
 ?>
 <div class="cfwc-custom-field-wrapper"><label for="business_field_input">אנא בחר עסק</label><br>
    <select class="castum_bus" id="business_field_input" name="business_field_input">
    <?php
    foreach($business as $id => $title){
    ?>
    <option value="<?php echo $id; ?>"><?php echo $title; ?></option>
    <?php
        }
    ?>
    </select>
    </div>
    <?php

}

public function validate_custom_field( $passed, $product_id, $quantity ) {
 if( empty( $_POST['business_field_input'] ) ) {
 // Fails validation
 //$passed = false;
 //wc_add_notice( __( 'אנא בחר עסק', 'cfwc' ), 'error' );
 }
 return $passed;
}

public function add_custom_field_item_data( $cart_item_data, $product_id, $variation_id, $quantity ) {
 if( ! empty( $_POST['business_field_input'] ) ) {
 // Add the item data
 $cart_item_data['title_field'] = $_POST['business_field_input'];
 $product = wc_get_product( $product_id ); // Expanded function
 //$price = $product->get_price(); // Expanded function
 //$cart_item_data['total_price'] = $price + 100; // Expanded function
 }
 return $cart_item_data;
}

public function before_calculate_totals( $cart_obj ) {
 if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
 return;
 }
 // Iterate through each cart item
 foreach( $cart_obj->get_cart() as $key=>$value ) {
 if( isset( $value['total_price'] ) ) {
 $price = $value['total_price'];
 $value['data']->set_price( ( $price ) );
 }
 }
}

public function cart_item_name( $name, $cart_item, $cart_item_key ) {
 if( isset( $cart_item['title_field'] ) ) {
 $name .= sprintf(
 '<p>%s</p>',
esc_html( $cart_item['title_field'] )
 );
 }
 return $name;
}
public function add_custom_data_meta_to_order( $order_id ) {
    $recipient_address = $_POST['business_field_input'];
    if ( ! empty( $recipient_address ) )
    update_post_meta( $order_id, 'business', sanitize_text_field( $recipient_address ) );
   
} 
public function add_custom_data_to_order( $item, $cart_item_key, $values, $order ) {
 foreach( $item as $cart_item_key=>$values ) {
 if( isset( $values['title_field'] ) ) {
 $item->add_meta_data("business", $values['title_field'], true );
 }
 }    
}    
}
new MeneggeWoocommercr();
