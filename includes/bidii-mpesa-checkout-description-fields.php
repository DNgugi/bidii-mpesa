<?php

add_filter('woocommerce_gateway_description', 'bidii_mpesa_checkout_description_fields', 20, 2);
add_filter('woocommerce_checkout_process', 'bidii_mpesa_checkout_fields_validation');
add_filter('woocommerce_checkout_update_order_meta', 'bidii_mpesa_checkout_update_order_meta', 10, 1);

//Just the phone number field to accept the M-Pesa Number
function bidii_mpesa_checkout_description_fields( $description, $payment_id){
    if ('bidii_mpesa' != $payment_id){
        return $description;
    }
    ob_start();
    echo '<div style="display: block; width: 150px; height: auto;">'; 
    echo '<img src="' . plugins_url('../assets/icon.png', __FILE__) .'">';
    echo '</div>';

    woocommerce_form_field(
        'mpesa_number',array(
            'type' => 'text',
            'label' => __('Enter your M-Pesa number to receive payment request', 'bidii-mpesa'),
            'class' => array('form-row', 'form-row-wide'),
            'required' => true
        )
 
    );
    $description .= ob_get_clean();

    return $description;
}

function bidii_mpesa_checkout_fields_validation(){
    if( 'bidii_mpesa' === $_POST['payment_method'] &&  ! isset ($_POST['mpesa_number'] )|| empty ($_POST['mpesa_number'])){
        wc_add_notice('Please enter a valid M-Pesa number (e.g 254712345678)','error');
    }
}

function bidii_mpesa_checkout_update_order_meta($order_id){
    if(  isset ($_POST['mpesa_number']) && ! empty ($_POST['mpesa_number'])){
        update_post_meta($order_id, 'mpesa_number', $_POST['mpesa_number']);
    }
}

