<?php 
/**
 * Plugin Name:       Team Bidii Mpesa Gateway  
 * Plugin URI:        http://teambidii.co.ke
 * Description:       Handle M-Pesa payments on your WooCommerce Shop
 * Version:           1
 * Author:            Duncan @ Team Bidii Consulting
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
        class WC_Bidii_Mpesa_Gateway extends WC_Payment_Gateway{
            public function __construct(){
                $this->id =  "bidii_mpesa";
                $this->icon = apply_filters('bidii_mpesa_icon',
                plugins_url('/assets/icon.png', __FILE__) );
                $this->has_fields = false;
                $this -> method_title = __('Bidii Mpesa Gateway', 'bidii-mpesa');
                $this->method_description = __('Accept M-Pesa Payments on your WooCommerce website', 'bidii-mpesa');
                $this->title = $this -> get_option('title');
                $this->description = $this -> get_option('description');
                $this->instructions = $this -> get_option('instructions', $this-> description);

                
                $this->init_form_fields();
                $this->init_settings();

                add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
                add_action('woocommerce_thank_you_' . $this->id, array($this,'thank_you_page'));
            }
            
            public function init_form_fields(){
                $this->form_fields = apply_filters('bidii_mpesa_fields', array(
                    'enabled' => array(
                        'title' => __('Enable/Disable', 'bidii-mpesa'),
                        'type' => 'checkbox',
                        'label' => __('Enable or Disable Team Bidii Mpesa Gateway', 'bidii-mpesa'), 
                        'default' => 'no'
                    ),
                    'title' => array(
                        'title' => __('Team Bidii Mpesa Gateway', 'bidii-mpesa'),
                        'type' => 'text',
                        'default' => __('Team Bidii Mpesa Gateway', 'bidii-mpesa'),
                        'desc_tip' => true,
                        'description' => __('Add a new title for the Team Bidii Mpesa Gateway that customers see on the checkout page', 'bidii-mpesa'), 


                    ),
                    'description' => array(
                        'title' => __('Team Bidii Mpesa Gateway Description', 'bidii-mpesa'),
                        'type' => 'textarea',
                        'default' => __('Pay directly via M-Pesa to allow processing of your order', 'bidii-mpesa'),
                        'desc_tip' => true,
                        'description' => __('Add a new description for the Team Bidii Mpesa Gateway that customers see on the checkout page', 'bidii-mpesa'), 


                    ),
                    'instructions' => array(
                        'title' => __('Order Instructions', 'bidii-mpesa'),
                        'type' => 'textarea',
                        'default' => __('', 'bidii-mpesa'),
                        'desc_tip' => true,
                        'description' => __('The content you enter here will be added to the thank you page and order confirmation email', 'bidii-mpesa'), 


                    )
                ));
            }

            public function process_payments($order_id){
                $order_id = wc_get_order( $order_id );

                $order->update_status('confirmed', __('Pending payment', 'bidii-mpesa'));

                //Add API to clear payment
                //$this -> bidii_mpesa_daraja_api();

                $order -> reduce_order_stock();

                WC() -> cart -> empty_cart();

                return array(
                    'results' => 'success',
                    'redirect' => $this -> get_return_url($order)
                );
            }

            //public function bidii_mpesa_daraja_api(){
            
           // }

            public function thank_you_page(){
                if($this -> instructions) {
                    echo wpautop($this->instructions);
                }
            }

        }
    }
}


add_filter('woocommerce_payment_gateways', 'add_bidii_mpesa_gateway' );

function add_bidii_mpesa_gateway( $gateways ){  
    $gateways[] = 'WC_Bidii_Mpesa_Gateway';
    
    return $gateways;
}