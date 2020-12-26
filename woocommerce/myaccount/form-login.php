<?php
# проверка
if (!defined('ABSPATH')) {
  exit;
}
# подготовка
$isRegEnabled = (get_option('woocommerce_enable_myaccount_registration') === 'yes');
$regEmail = '';
$regGenPassword = false;
if ($isRegEnabled)
{
  if (!empty($_POST['email'])) {
    $regEmail = esc_attr(wp_unslash($_POST['email']));
  }
  if (get_option('woocommerce_registration_generate_password') === 'yes') {
    $regGenPassword = true;
  }
}
$userName = empty($_POST['username']) ?
  '' : esc_attr(wp_unslash($_POST['username']));
$text = [
  'username'      => __('Username or email address', 'woocommerce'),
  'password'      => __('Password', 'woocommerce'),
  'remember'      => __('Remember me', 'woocommerce'),
  'login'         => __('Log in', 'woocommerce'),
  'register'      => __('Register', 'woocommerce'),
  'loginTitle'    => __('Login', 'woocommerce'),
  'lostTitle'     => __('Lost your password?', 'woocommerce'),
  'email'         => __('Email address', 'woocommerce'),
];
# определим классы для форм и заголовок
$classLogin = 'login';
$classReg   = 'register';
if ($isRegEnabled && array_key_exists('register', $_REQUEST)) {
  $classLogin .= ' hidden';
  $title = $text['register'];
}
else {
  $classReg .= ' hidden';
  $title = $text['loginTitle'];
}
# вызываем обработчик
do_action('woocommerce_before_customer_login_form');
?>


<div id="customer_login"><div>
  <div class="title"><h2><?php echo $title;?></h2></div>
  <div class="form-box">
  <!--/ LOGIN /-->
  <form method="post" class="<?php echo $classLogin;?>">
    <?php do_action('woocommerce_login_form_start'); ?>
    <div class="inputbox">
      <input type="text" name="username" class="first"
             value="<?php echo $userName; ?>"
             autocomplete="username">
      <label><?php echo $text['username'];?></label>
    </div>
    <div class="inputbox">
      <input type="password" name="password" autocomplete="current-password">
      <label><?php echo $text['password'];?></label>
    </div>
    <?php do_action('woocommerce_login_form'); ?>
    <input type="hidden" name="rememberme" value="forever">
    <div class="cmdbox">
      <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce');?>
      <button type="submit" name="login"><?php echo $text['login'];?></button>
    </div>
    <?php if ($isRegEnabled) : ?>
    <div class="separator">
      <div><span>&nbsp;&nbsp;У Вас нет учетной записи?&nbsp;&nbsp;</span><hr></div>
    </div>
    <div class="cmdbox light">
      <button type="button" class="switch"><?php echo $text['register'];?></button>
    </div>
    <?php endif; ?>
    <?php do_action('woocommerce_login_form_end'); ?>
  </form>
  <?php if ($isRegEnabled) : ?>
  <!--/ REGISTER /-->
  <form method="post" class="<?php echo $classReg;?>">
    <?php do_action('woocommerce_register_form_start');?>
    <div class="inputbox">
      <input type="email" name="email" class="first"
             value="<?php echo $regEmail;?>"
             autocomplete="email">
      <label><?php echo $text['email'];?></label>
    </div>
    <?php if ($regGenPassword) : ?>
      <p><?php esc_html_e('A password will be sent to your email address.', 'woocommerce');?></p>
    <?php else : ?>
      <div class="inputbox">
        <input type="password" name="password" autocomplete="new-password">
        <label><?php echo $text['password'];?></label>
      </div>
    <?php endif; ?>
    <?php do_action('woocommerce_register_form');?>
    <div class="cmdbox">
      <?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce');?>
      <button type="submit" name="register"><?php echo $text['register'];?></button>
    </div>
    <div class="separator">
      <div><span>&nbsp;&nbsp;Уже есть учетная запись?&nbsp;&nbsp;</span><hr></div>
    </div>
    <div class="cmdbox light">
      <button type="button" class="switch"><?php echo $text['loginTitle'];?></button>
    </div>
    <?php do_action('woocommerce_register_form_end');?>
  </form>
  <?php endif; ?>
  </div>
</div></div>

<?php do_action('woocommerce_after_customer_login_form'); ?>
