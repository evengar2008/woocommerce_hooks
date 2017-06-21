<?php
/**
 * Plugin Name: WooCommerce Settings Local Pickup Pro Min Order Amount
 * Description: A plugin to set min order amount for local pickup plus shipping method
 * Version: 1.0
 */

class WC_Settings_Tab_LP_Min_Order_Amount {
    /**
     * Bootstraps the class and hooks required actions & filters.
     *
     */
    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_settings_tab_lp_min_order_amount', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_settings_tab_lp_min_order_amount', __CLASS__ . '::update_settings' );

        add_filter( 'woocommerce_no_shipping_available_html', __CLASS__ . '::my_custom_no_shipping_message' );
        add_filter( 'woocommerce_cart_no_shipping_available_html', __CLASS__ . '::my_custom_no_shipping_message' );  

        add_filter( 'woocommerce_shipping_local_pickup_plus_is_available', __CLASS__ . '::wcs_pickup_min_amount', 10, 2 );      
    }
    
    
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['settings_tab_lp_min_order_amount'] = __( 'Local Pickup Plus Min Order Amount', 'woocommerce-settings-tab-lp_min_order_amount' );
        return $settings_tabs;
    }

    public static function settings_tab() {
        woocommerce_admin_fields( self::get_settings() );
    }

    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }

    public static function get_settings() {
        $settings = array(
            'section_title' => array(
                'name'     => __( 'Section Title', 'woocommerce-settings-tab-lp_min_order_amount' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_settings_tab_lp_min_order_amount_section_title'
            ),

            'title' => array(
                'name'     => 'Min Order Amount, ' . get_woocommerce_currency_symbol(),
                'desc_tip' => 'Please enter a number in ',
                'id'       => 'wc_min_order_amount_field',
                'type'     => 'number',
                'desc'     => 'Enter a number to set min order amount for local pickup plus in ' . get_woocommerce_currency_symbol(),
            ),

            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_settings_tab_lp_min_order_amount_section_end'
            )
        );
        return apply_filters( 'wc_settings_tab_lp_min_order_amount_settings', $settings );
    }

    public static function wcs_pickup_min_amount( $is_available ) {

        if ( ! $is_available ) {
            return $is_available;
        }

        $has_met_min_amount = false;
        $min_amount = get_option('wc_min_order_amount_field');

        if ( WC()->cart->prices_include_tax ) {
            $total = WC()->cart->cart_contents_total + array_sum( WC()->cart->taxes );
        } else {
            $total = WC()->cart->cart_contents_total;
        }
        
        if ( $total >= $min_amount ) {
            $has_met_min_amount = true;
        }
        else {
            //echo 'min order amount not met';
        }
        return $has_met_min_amount;

    }    

    public static function my_custom_no_shipping_message( $message ) {
        return 'Shipping not available. Minimum order amount is ' . get_option('wc_min_order_amount_field') . get_woocommerce_currency_symbol();
    }    
}

WC_Settings_Tab_LP_Min_Order_Amount::init();
