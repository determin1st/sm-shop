<?php
# prepare
# TODO:REGISTER {{{
$regForm      = '';
$isRegActive  = false;
$isRegAuto    = false;
#if (get_option('woocommerce_enable_myaccount_registration') === 'yes')
if (false)
{
  $isRegActive = array_key_exists('register', $_REQUEST);
  $isRegAuto   = (get_option('woocommerce_registration_generate_password') === 'yes');
  ###
  ob_start();
?>

<form method="post" action="{{action}}" class="register">
  <?php do_action('woocommerce_register_form_start');?>
  <div class="inputbox">
    <input type="email" name="email" value="{{regEmail}}" autocomplete="email">
    <label>{{emailLabel}}</label>
  </div>
  <?php if ($isRegAuto) : ?>
    <p>{{genText}}</p>
  <?php else : ?>
    <div class="inputbox">
      <input type="password" name="password" autocomplete="new-password">
      <label>{{passLabel}}</label>
    </div>
  <?php endif; ?>
  <?php do_action('woocommerce_register_form');?>
  <div class="cmdbox">
    <?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce');?>
    <button type="submit" name="register">{{regLabel}}</button>
  </div>
  <div class="sep">
    <div><label>&nbsp;&nbsp;{{question}}&nbsp;&nbsp;</label><hr></div>
  </div>
  <div class="cmdbox light">
    <button type="button" class="switch">{{loginLabel}}</button>
  </div>
  <?php do_action('woocommerce_register_form_end');?>
</form>

<?php
  $regForm = StorefrontModernBlocks::parse(ob_get_clean(), [
    'action'     => esc_url(home_url('/')),
    'regEmail'   => (!empty($_POST['email']) ? esc_attr(wp_unslash($_POST['email'])) : ''),
    'emailLabel' => __('Email address', 'woocommerce'),
    'passLabel'  => __('Password', 'woocommerce'),
    'genText'    => __('A password will be sent to your email address.', 'woocommerce'),
    'regLabel'   => __('Register', 'woocommerce'),
    'loginLabel' => __('Login', 'woocommerce'),
    'question'   => 'Есть учетная запись?',
  ]);
}
# }}}
# LOGIN {{{
ob_start();
?>

<form method="post" action="{{action}}" class="login">
  <?php do_action('woocommerce_login_form_start'); ?>
  <div class="inputbox">
    <input type="text" name="username" value="{{userName}}" autocomplete="username">
    <label>{{userLabel}}</label>
  </div>
  <div class="inputbox">
    <input type="password" name="password" autocomplete="current-password">
    <label>{{passLabel}}</label>
  </div>
  <?php do_action('woocommerce_login_form'); ?>
  <input type="hidden" name="rememberme" value="forever">
  <div class="pad"></div>
  <div class="cmdbox">
    <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
    <button type="submit" name="login">OK</button>
  </div>
  <?php if ($regForm) : ?>
  <div class="sep">
    <div><label>&nbsp;&nbsp;{{question}}&nbsp;&nbsp;</label><hr></div>
  </div>
  <div class="cmdbox light">
    <button type="button" class="switch">{{regLabel}}</button>
  </div>
  <?php endif; ?>
  <?php do_action('woocommerce_login_form_end'); ?>
</form>

<?php
$loginForm = StorefrontModernBlocks::parse(ob_get_clean(), [
  'action'     => esc_url(home_url('/')),
  'userName'   => (empty($_POST['username']) ? '' : esc_attr(wp_unslash($_POST['username']))),
  'userLabel'  => __('Username or email address', 'woocommerce'),
  'passLabel'  => __('Password', 'woocommerce'),
  'loginLabel' => __('Login', 'woocommerce'),
  'regLabel'   => __('Register', 'woocommerce'),
  'question'   => 'Нет учетной записи?',
]);
# }}}
# compose
# {{{
ob_start();
do_action('woocommerce_before_customer_login_form');
?>

<div id="auth" class="w3-box {{variant}}">
  <div>
    <div class="titlebox">
      <h1>{{title}}</h1>
    </div>
    <div class="formbox">
      {{login}}{{register}}
    </div>
  </div>
</div>

<?php
do_action('woocommerce_after_customer_login_form');
# }}}
echo \StorefrontModern\Blocks::parse(ob_get_clean(), [
  'title'    => ($isRegActive ? __('Register', 'woocommerce') : __('Login', 'woocommerce')),
  'variant'  => ($isRegActive ? 'b' : 'a'),
  'login'    => $loginForm,
  'register' => $regForm,
]);
?>
