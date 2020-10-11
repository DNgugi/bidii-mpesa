<?php 

add_action('rest_api_init', 'bidii_mpesa_add_callback_url_endpoint');
function bidii_mpesa_add_callback_url_endpoint(){
register_rest_route( 
    'bidii-mpesa', 
    'receive_callback', 
    array(
        'methods' => 'POST',
        'callback' => 'bidii_mpesa_receive_callback'
    ));
}

function bidii_mpesa_receive_callback($data_received){
    
    
    $post_args = array(
        'post_title' => wp_strip_all_tags($result['mPesaReceiptNumber']),
        'post_content' => $result,
        'post_status' => 'publish',
        'post_type' => 'bidii_mpesa_data'
        );
        
    $post_var = wp_insert_post($post_args);
    
    $response = array (
            "ResponseCode"=> "00000000",
	        "ResponseDesc"=> "success"
        );
    
      return $response;
        //need to store so payment function can check for $mpesaReceiptNumber
}

function bidii_mpesa_setup_post_type(){
    $args = array(
        'public'=> true,
        'publicly-queryable' => false,
        'label'=> __('Bidii Mpesa Data', 'bidii-mpesa'),
        'menu_icon' => 'dashicons-analytics',
        'supports' => array('title', 'editor')
    );
    register_post_type( 'bidii_mpesa_data', $args );
}
add_action( 'init', 'bidii_mpesa_setup_post_type' );

