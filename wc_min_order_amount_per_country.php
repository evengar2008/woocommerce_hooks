<?php
//place this in functions.php to set WooCommerce per-country minimum order amount. In this case, it's all countries but Italy that have greater order amount
add_action( 'woocommerce_checkout_process', 'wc_minimum_order_amount' );
add_action( 'woocommerce_before_cart' , 'wc_minimum_order_amount' );
  
function wc_minimum_order_amount() {
    // Valore da impostare per ordine minimo
    global $woocommerce;
    //get customer country
    $country = WC()->customer->get_country();
    //echo $country;
    if ("IT" != $country) {
        $minimum = 50;
    }  
    else {
        $mininum = 20;
    }
    //check if in cart or checkout
    if ( WC()->cart->subtotal < $minimum ) {
         if( is_cart() ) {
                wc_print_notice( 
                       sprintf( 'Ordine minimo di %s, il tuo carrello contiene articoli per %s.' , 
                            wc_price( $minimum ), 
                            wc_price( WC()->cart->total )
                        ), 'error' 
                    );

                } 
        else {
            wc_add_notice( 
               sprintf( 'Ordine minimo di %s, il tuo carrello contiene articoli per %s.' , 
                    wc_price( $minimum ), 
                    wc_price( WC()->cart->total )
                ), 'error' 
            );

        }
    }

}
?>