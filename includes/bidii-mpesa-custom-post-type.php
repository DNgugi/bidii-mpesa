<?php 
function bidii_mpesa_setup_post_type(){
    $args = array(
        'public'=> true,
        'publicly-queryable' => false,
        'label'=> __('Bidii Mpesa Data', 'bidii-mpesa'),
        'menu_icon' => 'dashicons-analytics',
        'supports' => array('title', 'editor'),
    );
    register_post_type( 'bidii_mpesa_data', $args );
}
add_action( 'init', 'bidii_mpesa_setup_post_type' );

