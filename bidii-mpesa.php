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
        class WC_Bidii_Mpesa_Gateway extends WC_Payment_Gateway{
            public function __construct(){
                $this->id =  "bidii_mpesa";
                $this->icon = apply_filters('bidii_mpesa_icon',
                plugins_url('/assets/icon.png', __FILE__) );
                $this->has_fields = true;
                $this -> method_title = __('Bidii Mpesa Gateway', 'bidii-mpesa');
                $this->method_description = __('Accept M-Pesa Payments on your WooCommerce website', 'bidii-mpesa');
                $this->title = $this -> get_option('title');
                $this->description = $this -> get_option('description');
                $this->instructions = $this -> get_option('instructions', $this-> description);

                
                $this->init_form_fields();
                $this->init_settings();

                add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
                add_action('woocommerce_thank_you_' . $this->id, array($this,'thank_you_page'));
                // We need custom JavaScript to obtain a token
                add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
            
                // You can also register a webhook here
                // add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) );
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

            public function payment_fields(){

                 if ( $this->description ) {
                     echo wpautop( wp_kses_post( $this->description ) );
                 }

                 // I will echo() the form, but you can close PHP tags and print it directly in HTML
                echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-mpesa-form wc-payment-form" style="background:transparent;">';
                
                     // Add this action hook if you want your custom payment gateway to support it
                do_action( 'woocommerce_mpesa_form_start', $this->id );
                
                     // I recommend to use inique IDs, because other gateways could already use #ccNo, #expdate, #cvc
                     echo '<div class="form-row form-row-wide"><label> Your M-Pesa Number <span class="required">*</span></label>
                         <input id="misha_ccNo" type="text" autocomplete="off">
                        </div>
                         <div class="clear"></div>';
                
                     do_action( 'woocommerce_mpesa_form_end', $this->id );
                
                     echo '<div class="clear"></div></fieldset>';
                

            }

            public function validate_fields(){
                
            }

            public function process_payments($order_id){
                $order = wc_get_order( $order_id );

                $order->update_status('on-hold', __('Pending payment', 'bidii-mpesa'));

                //Add API to clear payment
                //$this -> bidii_mpesa_daraja_api();
                // $mpesa= new \Safaricom\Mpesa\Mpesa();
                
                // we received the payment
                $order->payment_complete();
                $order->reduce_order_stock();

                // some notes to customer (replace true with false to make it private)
                $order->add_order_note( 'Hey, your order is paid! Thank you!', true );

                // Empty cart
                $woocommerce->cart->empty_cart();

                // Redirect to the thank you page
                return array(
                    'result' => 'success',
                    'redirect' => $this->get_return_url( $order )
                );
            }

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