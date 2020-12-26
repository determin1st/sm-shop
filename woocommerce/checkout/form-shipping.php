<?php
/**
 * Checkout shipping information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-shipping.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 * @global WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-shipping-fields"></div>
<div class="woocommerce-additional-fields">
  <?php do_action('woocommerce_before_order_notes', $checkout); ?>
  <?php if (apply_filters('woocommerce_enable_order_notes_field', 'yes' === get_option('woocommerce_enable_order_comments', 'yes'))) : ?>
    <h3>Дополнительно</h3>
    <div class="woocommerce-additional-fields__field-wrapper">
      <?php
      #foreach ($checkout->get_checkout_fields('order') as $key => $field) {
      #  woocommerce_form_field($key, $field, $checkout->get_value($key));
      #}
      ?>
    </div>
    <div class="inputbox">
      <textarea name="order_comments" placeholder="особые пожелания отделу доставки."></textarea>
      <label for="order_comments">Примечание (необязательно)</label>
    </div>
  <?php endif; ?>
  <?php do_action('woocommerce_after_order_notes', $checkout); ?>
</div>

