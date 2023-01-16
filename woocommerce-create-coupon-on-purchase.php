<?php
/**
 * Plugin Name: WooCommerce Create Coupon on Purchase
 * Plugin URI: https://press--st.art
 * Description: Creates a coupon in WooCommerce when a user purchases a product from the "Δωροκάρτες" category.
 * Version: 1.0
 * Author: George M
 * Author URI: https://press--st.art
 * License: GPL2
 */

defined( 'ABSPATH' ) || exit;

add_action( 'woocommerce_thankyou', 'create_coupon_on_purchase' );

/**
 * Creates a coupon in WooCommerce when a user purchases a product from the "Δωροκάρτες" category.
 *
 * @param int $order_id The order ID.
 */
function create_coupon_on_purchase( $order_id ) {
  $order = wc_get_order( $order_id );
  
  // Get the order total
  $order_total = $order->get_total();
  
  // Get the order items
  $order_items = $order->get_items();
  
  // Check if the order has any items from the "Δωροκάρτες" category
  $has_gift_card = false;
  foreach ( $order_items as $item ) {
    $product = $item->get_product();
    if ( has_term( 'Δωροκάρτες', 'product_cat', $product->get_id() ) ) {
      $has_gift_card = true;
      break;
    }
  }
  
  if ( ! $has_gift_card ) {
    return; // exit the function if the order does not contain any items from the "Δωροκάρτες" category
  }
  
  // Set the coupon code
  $code = 'auto_' . $order_id;
  
  // Set the coupon arguments
  $args = array(
    'code' => $code,
    'type' => 'fixed_cart',
    'amount' => $order_total, // coupon amount is set to the order total
    'individual_use' => true, // only allow one use per customer
    'product_ids' => array(), // apply to all products
    'expiry_date' => '', // no expiry date
  );
  
  // Create the coupon
  $coupon = new WC_Coupon( $args );
  $coupon->save();
}