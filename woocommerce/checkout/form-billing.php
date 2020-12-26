<?php
/**
 * Checkout billing information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
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
<div class="woocommerce-billing-fields">
  <h3>Доставка</h3>
  <?php do_action('woocommerce_before_checkout_billing_form', $checkout); ?>
  <div class="woocommerce-billing-fields__field-wrapper">
    <?php
    #$fields = $checkout->get_checkout_fields('billing');
    #foreach ($fields as $key => $field) {
    #  woocommerce_form_field($key, $field, $checkout->get_value($key));
    #}
    # получаем текущие значения
    $a = [];
    $b = $checkout->get_checkout_fields('billing');
    foreach ($b as $c => $d) {
      $a[$c] = $checkout->get_value($c);
    }
    ?>
  </div>
  <!--//-->
  <div class="inputbox">
    <input type="text"
           name="billing_first_name"
           placeholder="Гусев Иван"
           value="<?php echo $a['billing_first_name'];?>"
           autocomplete="name">
    <label for="billing_first_name">ФИО получателя</label>
  </div>
  <div class="groupbar">
    <div class="inputbox">
      <select name="billing_country" autocomplete="country">
        <?php
        # получаем список стран
        $d = WC()->countries->get_allowed_countries();
        asort($d);
        foreach ($d as $b => $c) {
          # проверяем текущую
          $d = ($b === $a['billing_country']) ?
            ' selected="selected"' : '';
          # вывод
          echo '<option value="'.$b.'"'.$d.'>'.$c."</option>\n";
        }
        ?>
      </select>
      <label for="billing_country">Страна</label>
    </div>
    <div class="inputbox">
      <input type="text"
             name="billing_city"
             placeholder="Москва"
             value="<?php echo $a['billing_city'];?>"
             autocomplete="address-level2">
      <label for="billing_city">Город / Населённый пункт</label>
    </div>
  </div>
  <div class="inputbox">
    <textarea name="billing_address_1"
              placeholder="р-н Внуково, ул Пушкина 12, кв 34"
              autocomplete="street-address"><?php echo $a['billing_address_1'];?></textarea>
    <label for="billing_address_1">Адрес</label>
  </div>
  <div class="inputbox">
    <input type="text"
           name="billing_postcode"
           placeholder="101000"
           value="<?php echo $a['billing_postcode'];?>"
           autocomplete="postal-code">
    <label for="billing_postcode" class="">Почтовый индекс</label>
  </div>
  <!--//-->
  <br>
  <h3>Контактная информация</h3>
  <div class="inputbox">
    <input type="email"
           name="billing_email"
           placeholder="username@gmail.com"
           value="<?php echo $a['billing_email'];?>"
           autocomplete="email username">
    <label for="billing_email">Email</label>
  </div>
  <div class="inputbox">
    <input type="tel"
           name="billing_phone"
           value="<?php $a['billing_phone'];?>"
           autocomplete="tel">
    <label for="billing_phone">Телефон (необязательно)</label>
  </div>
  <!--//-->
  <?php do_action('woocommerce_after_checkout_billing_form', $checkout); ?>
</div>




<?php if (!is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
	<div class="woocommerce-account-fields">
		<?php if (!$checkout->is_registration_required()) : ?>

			<p class="form-row form-row-wide create-account">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ); ?> type="checkbox" name="createaccount" value="1" /> <span><?php esc_html_e( 'Create an account?', 'woocommerce' ); ?></span>
				</label>
			</p>

		<?php endif; ?>

		<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

		<?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>

			<div class="create-account">
				<?php foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) : ?>
					<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
				<?php endforeach; ?>
				<div class="clear"></div>
			</div>

		<?php endif; ?>

		<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
	</div>
<?php endif; ?>

