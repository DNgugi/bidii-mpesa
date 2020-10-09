<?php 
/**
 * Plugin Name:       Team Bidii Mpesa Gateway  
 * Plugin URI:        http://teambidii.co.ke
 * Description:       Handle M-Pesa payments on your WooCommerce Shop
 * Version:           1
 * Author:            Team Bidii Consulting
 * Author URI:        http://teambidii.co.ke
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       bidii-mpesa
 * Domain Path:       /languages
 */

if(! in_array('woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option('active_plugins') ))) return;

add_action( 'plugins_loaded', 'bidii_mpesa_init', 11 );

function bidii_mpesa_init(){
    if (class_exists('WC_Payment_Gateway')){
        require_once plugin_dir_path( __FILE__ ) . '/includes/wc-gateway-bidii-mpesa.php';
        require_once plugin_dir_path( __FILE__ ) . '/includes/bidii-mpesa-checkout-description-fields.php';
    }
}


add_filter('woocommerce_payment_gateways', 'add_bidii_mpesa_gateway' );

function add_bidii_mpesa_gateway( $gateways ){  
    $gateways[] = 'WC_Gateway_Bidii_Mpesa';
    
    return $gateways;
}