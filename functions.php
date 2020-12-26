<?php
# check requirements
# {{{
if (version_compare('7.2', phpversion(), '>')) {
  wp_die('storefront-modern: missing requirement: PHP 7.2 or above', 'fail');
}
if (version_compare('5.3', $wp_version, '>')) {
  wp_die('storefront-modern: missing requirement: WordPress 5.3 or above', 'fail');
}
# }}}
# check dependencies
# {{{
if (!(@include_once __DIR__.'/icons.php')) {
  wp_die('storefront-modern: missing dependency: icons', 'fail');
}
if (!(@include_once __DIR__.'/menu.php')) {
  wp_die('storefront-modern: missing dependency: menu', 'fail');
}
#if (!(@include_once __DIR__.'/woo.php')) {
#  wp_die('storefront-modern: missing dependency: woo', 'fail');
#}
#if (!(@include_once __DIR__.'/blocks.php')) {
#  wp_die('storefront-modern: missing dependency: blocks', 'fail');
#}
# }}}
# define classes
class StorefrontModern {
  # base {{{
  public $rootUri       = '';     # URI to the theme root
  public $woo           = null;   # StorefrontModernWoo
  public $icons         = null;   # StorefrontModernIcons
  public $blocks        = null;   # StorefrontModernBlocks
  public $isGutenberg   = false;
  public $isAdmin       = false;
  public $isAccountPage = false;
  public $isFrontPage   = false;
  ###
  function __construct($init)
  {
    # set properties
    $this->rootUri     = get_stylesheet_directory_uri();
    $this->isGutenberg = function_exists('register_block_type');
    $this->isAdmin     = is_admin();
    $this->icons       = new StorefrontModernIcons();
    # create woocommerce helper
    if (defined('WC_VERSION')) {
      #$this->woo = new StorefrontModernWoo();
    }
    # run helpers
    $this->disableTrash();
    $this->enqueueScripts();
    # create blocks
    if ($this->isGutenberg && $this->woo) {
      #$this->blocks = new StorefrontModernBlocks($this);
    }
    # determine page flags
    $me = $this;
    add_action('wp', function() use ($me)
    {
      $me->isFrontPage = is_front_page();
      if (function_exists('is_account_page')) {
        $me->isAccountPage = is_account_page();
      }
    });
    # activate custom initializer
    # when parent theme is ready
    add_action('after_setup_theme', Closure::bind($init, $this), 20);
  }
  # }}}
  # scripts and styles {{{
  private function registerScripts()
  {
    # prepare
    $s = $this->rootUri;
    # some scripts are separate projects that may be included
    # locally (from the same-origin) or remotely (from cdn)
    $a = file_exists(__DIR__.'/inc/httpFetch')
      ? $s.'/inc/httpFetch/httpFetch.js'
      : 'https://cdn.jsdelivr.net/npm/http-fetch-json@2/httpFetch.js';
    $b = file_exists(__DIR__.'/inc/gsap')
      ? $s.'/inc/gsap/gsap.min.js'
      : 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.2.6/gsap.min.js';
    # scripts
    wp_register_script('http-fetch',   $a, [], false, true);
    wp_register_script('gsap',         $b, [], false, true);
    wp_register_script('index',        $s.'/js/index.js', [], false, true);
    wp_register_script('myaccount',    $s.'/js/myaccount.js', [], false, true);
    wp_register_script('admin-script', $s.'/js/admin.js', [], false, true);
    # styles
    wp_register_style('admin-style',   $s.'/css/admin.css');
  }
  private function enqueueScripts()
  {
    # prepare
    $x = $this;
    # scripts must be registered and enqueued
    # at the certain wordpress hooks only!
    # they are used for enqueuing both scripts AND styles..
    add_action('wp_enqueue_scripts', function() use ($x)
    {
      # register first
      $x->registerScripts();
      # enqueue only those scripts
      # that are needed..
      wp_enqueue_script('gsap');# TODO: remove!
      wp_enqueue_script('index');
      if ($x->isAccountPage) {
        wp_enqueue_script('myaccount');
      }
      /***
      if ($me->isFrontPage) {
        wp_enqueue_style('flickity-css', $d.'/inc/flickity/flickity.min.css');
        wp_enqueue_script('flickity-js', $d.'/inc/flickity/flickity.pkgd.min.js');
      }
      /***/
    });
    add_action('admin_enqueue_scripts', function() use ($x)
    {
      # register first
      $x->registerScripts();
      # admin_enqueue_scripts is the proper hook to use when
      # enqueuing scripts and styles that are meant to be used
      # in the administration panel..
      wp_enqueue_script('admin-script');
      wp_enqueue_style('admin-style');
    });
  }
  # }}}
  # wp optimization {{{
  private function disableTrash()
  {
    ###
    # scripts and styles
    add_action('wp_enqueue_scripts', function()
    {
      wp_dequeue_style('storefront-fonts');# parent theme
    });
    ###
    # parent theme features
    add_action('after_setup_theme', function() {
      remove_theme_support('automatic-feed-links');
    }, 20);
    ###
    # wordpress features
    # отключаем rss-feed ссылки в <head>
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);
    # отключаем DNS-prefetch
    remove_action('wp_head', 'wp_resource_hints', 2);
    # отключаем XMLRPC
    add_filter('xmlrpc_enabled', '__return_false');
    remove_action('wp_head', 'rsd_link');
    # отключаем Windows Live Writer Manifest Link
    remove_action('wp_head', 'wlwmanifest_link');
    # отключаем shortlink
    remove_action('wp_head', 'wp_shortlink_wp_head');
    # отключаем вывод информации о версии WP
    remove_action('wp_head', 'wp_generator');
    # отключаем emoji
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('embed_head', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    ###
    # Отключаем принудительную проверку новых версий WP,
    # плагинов и темы в админке, чтобы она не тормозила,
    # когда долго не заходил и зашел... Все проверки будут происходить
    # незаметно через крон или при заходе на страницу: Консоль -> Обновления
    #
    # @see https://wp-kama.ru/filecode/wp-includes/update.php
    # @author Kama (https://wp-kama.ru)
    # @version 1.0
    ###
    if ($this->isAdmin)
    {
      // отключим проверку обновлений при любом заходе в админку...
      remove_action('admin_init', '_maybe_update_core');
      remove_action('admin_init', '_maybe_update_plugins');
      remove_action('admin_init', '_maybe_update_themes');
      // отключим проверку обновлений при заходе на специальную страницу в админке...
      remove_action('load-plugins.php', 'wp_update_plugins');
      remove_action('load-themes.php', 'wp_update_themes');
      // оставим принудительную проверку при заходе на страницу обновлений...
      //remove_action( 'load-update-core.php', 'wp_update_plugins' );
      //remove_action( 'load-update-core.php', 'wp_update_themes' );
      // внутренняя страница админки "Update/Install Plugin" или "Update/Install Theme" - оставим не мешает...
      //remove_action( 'load-update.php', 'wp_update_plugins' );
      //remove_action( 'load-update.php', 'wp_update_themes' );
      // событие крона не трогаем, через него будет проверяться наличие обновлений - тут все отлично!
      //remove_action( 'wp_version_check', 'wp_version_check' );
      //remove_action( 'wp_update_plugins', 'wp_update_plugins' );
      //remove_action( 'wp_update_themes', 'wp_update_themes' );
      ###
      # отключим проверку необходимости обновить браузер в консоли,
      # мы всегда юзаем топовые браузеры!
      # эта проверка происходит раз в неделю...
      #
      # @see https://wp-kama.ru/function/wp_check_browser_version
      ###
      add_filter('pre_site_transient_browser_'.md5($_SERVER['HTTP_USER_AGENT']), '__return_true');
    }
  }
  # }}}
  # multi-domain {{{
  # enables constant navigation (fixes absolute URLs)
  # when site is accessed under different domain names
  private function enableMultiDomainConfig()
  {
    ###
    # ...
    #add_filter('content_url', [ $this, 'fixUrl' ]);
    #add_filter('option_siteurl', [ $this, 'fixUrl' ]);
    #add_filter('option_home', [ $this, 'fixUrl' ]);
    #add_filter('plugins_url', [ $this, 'fixUrl' ]);
    #add_filter('wp_get_attachment_url', [ $this, 'fixUrl' ]);
    #add_filter('get_the_guid', [ $this, 'fixUrl' ]);
    # ...
    #add_filter('upload_dir', [ $this, 'fixUploadDir' ]);
    #add_filter('the_content', [ $this, 'fixContentUrls' ], 20);
    #add_filter('allowed_http_origins', [ $this, 'addAllowedOrigins' ]);
    ###
    # incorporate domain style class into <body>
    add_filter('body_class', function($c) {
      # {{{
      # replace all special characters with hyphens
      $a = str_replace('.', '-', $_SERVER['HTTP_HOST']);
      $a = str_replace(':', '-', $a);
      # add it
      if (!empty($a)) {
        $c[] = strtolower($a);
      }
      return $c;
      # }}}
    });
  }
  # }}}
  # SMTP (phpmailer/wp_mail) {{{
  private function initSMTP() {
    add_action('phpmailer_init', function($m) {
      ###
      $m->isSMTP();
      $m->Host       = 'smtp.list.ru';
      $m->SMTPAuth   = true;
      $m->Port       = 465;
      $m->Username   = 'i__one@list.ru';
      $m->Password   = 'litvalan321';
      $m->SMTPSecure = 'ssl';
      $m->CharSet    = 'UTF-8';
      $m->From       = $m->Username;
      #$m->FromName   = 'test';
    });
  }
  # }}}
  # helpers {{{
  public static function array_move_before($arr, $find, $move)
  {
    if (!isset($arr[$find], $arr[$move])) {
        return $arr;
    }
    $elem = [$move=>$arr[$move]];  // cache the element to be moved
    $start = array_splice($arr, 0, array_search($find, array_keys($arr)));
    unset($start[$move]);  // only important if $move is in $start
    return $start + $elem + $arr;
  }
  public static function array_insert_before($a, $e, $k, $v)
  {
    if (!isset($a[$e]))
    {
      $a[$k] = $v;
      return $a;
    }
    if (($i = array_search($e, array_keys($a))) === 0) {
      $b = [$k => $v];
    }
    else
    {
      $b = array_slice($a, 0, $i);
      $b[$k] = $v;
    }
    return $b + array_slice($a, $i);
  }
  # }}}
}
# create theme instance
$modernStore = new StorefrontModern(function() {
  # подготовка
  $me = $this;
  # инициализация темы
  # подключаем "carbon fields" {{{
  /***
  # если плагин не установлен или не активирован
  # подключаем его напрямую из каталога темы
  if (!function_exists('carbon_field_exists')) {
    require_once(__DIR__.'/inc/carbon-fields/carbon-fields-plugin.php');
  }
  # добавляем контейнеры и поля в интерфейс
  add_action('carbon_fields_register_fields', function() {
    require_once(__DIR__.'/custom-fields.php');
  });
  /***/
  # }}}
  # добавляем возможность загрузки изображений формата WebP в медиатеку {{{
  add_filter('mime_types', function($mimes) {
    # добавляем тип изображения
    $mimes['webp'] = 'image/webp';
    #$mimes['svg']  = 'image/svg+xml';
    return $mimes;
  });
  add_action('upload_mimes', function($mimes = []) {
    # добавляем разрешение для загрузки изображений данного типа
    $mimes['webp'] = 'image/webp';
    #$mimes['svg']  = 'image/svg+xml';
    return $mimes;
  });
  add_filter('file_is_displayable_image', function($result, $path) {
    if (!$result)
    {
      # разрешаем показ миниатюры WEBP
      $info = @getimagesize($path);
      if ($info && $info[2] === IMAGETYPE_WEBP) {
        return true;
      }
    }
    return $result;
  }, 10, 2);
  /***
  # отключаем генерацию миниатюр медиатеки
  add_filter('intermediate_image_sizes_advanced', function($sizes, $meta, $id) {
    unset($sizes['thumbnail']);
    unset($sizes['medium']);
    unset($sizes['large']);
    return $sizes;
  }, 10, 3);
  /***/
  # }}}
  # фильтр для ленивой загрузки <iframe> {{{
  add_filter('embed_oembed_html', function($html) {
    # проверим что это iframe
    if (stripos($html, '<iframe') === false) {
      return $html;
    }
    # проверим наличие источника
    if (($a = strpos($html, ' src=')) === false) {
      return $html;
    }
    # преобразуем источник в пассивный атрибут данных,
    $html = substr($html, 0, $a).' data-src='.substr($html, $a + 5);
    # добавим атрибут loading=lazy (для новых браузеров)
    if (strpos($html, ' loading=') === false)
    {
      $a = strpos($html, '>');
      $html = substr($html, 0, $a).' loading="lazy"'.substr($html, $a);
    }
    return $html;
  });
  # }}}
  # отключаем ненужные пункты меню в админке {{{
  #remove_theme_support('custom-header');
  add_action('admin_menu', function() {
    global $submenu;
    # список индексов внутри /wp-admin/menu.php
    unset($submenu['themes.php'][15]);
    unset($submenu['themes.php'][20]);
    /***/
  }, 999);
  # }}}
  # разное {{{
  # отключаем вывод информации о Wordpress (TODO: test)
  add_filter('the_generator' , function() {
    return '';
  });
  # дополнительные стили и блоки для редактора gutenberg?
  add_theme_support('editor-styles');
  #add_theme_support('dark-editor-style'); # для темного фона
  #add_theme_support('wp-block-styles');
  #add_theme_support('responsive-embeds');
  add_editor_style('css/editor-style.css');
  #add_action('enqueue_block_editor_assets', function() {
  #  $d = get_template_directory_uri();
  #  wp_enqueue_script('editor-blocks', $d.'/js/editor.js', ['wp-blocks','wp-editor']);
  #});
  # }}}
  # настройка woocommerce/storefront
  # {{{
  ###
  ### заголовок
  ###
  # отключаем блоки
  # {{{
  /***
  # storefront_header_container                 - 0
  # storefront_skip_links                       - 5
  # storefront_social_icons                     - 10
  # storefront_site_branding                    - 20
  # storefront_secondary_navigation             - 30
  # storefront_product_search                   - 40
  # storefront_header_container_close           - 41
  # storefront_primary_navigation_wrapper       - 42
  # storefront_primary_navigation               - 50
  # storefront_header_cart                      - 60
  # storefront_primary_navigation_wrapper_close - 68
  /***/
  remove_action('storefront_header', 'storefront_secondary_navigation', 30);
  remove_action('storefront_header', 'storefront_product_search', 40);
  remove_action('storefront_header', 'storefront_header_cart', 60);
  # }}}
  # подключаем блоки
  add_action('storefront_header', function() {
    # контактные данные {{{
    $a = get_theme_mod('header_phone', '+7 800 123-45-67');
    echo <<<EOD

    <div class="site-contacts">
      <a href="tel:{$a}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 473.806 473.806">
          <path d="M374.456 293.506c-9.7-10.1-21.4-15.5-33.8-15.5-12.3 0-24.1 5.3-34.2 15.4l-31.6 31.5c-2.6-1.4-5.2-2.7-7.7-4-3.6-1.8-7-3.5-9.9-5.3-29.6-18.8-56.5-43.3-82.3-75-12.5-15.8-20.9-29.1-27-42.6 8.2-7.5 15.8-15.3 23.2-22.8 2.8-2.8 5.6-5.7 8.4-8.5 21-21 21-48.2 0-69.2l-27.3-27.3c-3.1-3.1-6.3-6.3-9.3-9.5-6-6.2-12.3-12.6-18.8-18.6-9.7-9.6-21.3-14.7-33.5-14.7s-24 5.1-34 14.7l-.2.2-34 34.3c-12.8 12.8-20.1 28.4-21.7 46.5-2.4 29.2 6.2 56.4 12.8 74.2 16.2 43.7 40.4 84.2 76.5 127.6 43.8 52.3 96.5 93.6 156.7 122.7 23 10.9 53.7 23.8 88 26 2.1.1 4.3.2 6.3.2 23.1 0 42.5-8.3 57.7-24.8.1-.2.3-.3.4-.5 5.2-6.3 11.2-12 17.5-18.1 4.3-4.1 8.7-8.4 13-12.9 9.9-10.3 15.1-22.3 15.1-34.6 0-12.4-5.3-24.3-15.4-34.3l-54.9-55.1zm35.8 105.3c-.1 0-.1.1 0 0-3.9 4.2-7.9 8-12.2 12.2-6.5 6.2-13.1 12.7-19.3 20-10.1 10.8-22 15.9-37.6 15.9-1.5 0-3.1 0-4.6-.1-29.7-1.9-57.3-13.5-78-23.4-56.6-27.4-106.3-66.3-147.6-115.6-34.1-41.1-56.9-79.1-72-119.9-9.3-24.9-12.7-44.3-11.2-62.6 1-11.7 5.5-21.4 13.8-29.7l34.1-34.1c4.9-4.6 10.1-7.1 15.2-7.1 6.3 0 11.4 3.8 14.6 7l.3.3c6.1 5.7 11.9 11.6 18 17.9 3.1 3.2 6.3 6.4 9.5 9.7l27.3 27.3c10.6 10.6 10.6 20.4 0 31-2.9 2.9-5.7 5.8-8.6 8.6-8.4 8.6-16.4 16.6-25.1 24.4-.2.2-.4.3-.5.5-8.6 8.6-7 17-5.2 22.7l.3.9c7.1 17.2 17.1 33.4 32.3 52.7l.1.1c27.6 34 56.7 60.5 88.8 80.8 4.1 2.6 8.3 4.7 12.3 6.7 3.6 1.8 7 3.5 9.9 5.3.4.2.8.5 1.2.7 3.4 1.7 6.6 2.5 9.9 2.5 8.3 0 13.5-5.2 15.2-6.9l34.2-34.2c3.4-3.4 8.8-7.5 15.1-7.5 6.2 0 11.3 3.9 14.4 7.3l.2.2 55.1 55.1c10.3 10.2 10.3 20.7.1 31.3zM256.056 112.706c26.2 4.4 50 16.8 69 35.8s31.3 42.8 35.8 69c1.1 6.6 6.8 11.2 13.3 11.2.8 0 1.5-.1 2.3-.2 7.4-1.2 12.3-8.2 11.1-15.6-5.4-31.7-20.4-60.6-43.3-83.5s-51.8-37.9-83.5-43.3c-7.4-1.2-14.3 3.7-15.6 11s3.5 14.4 10.9 15.6zM473.256 209.006c-8.9-52.2-33.5-99.7-71.3-137.5s-85.3-62.4-137.5-71.3c-7.3-1.3-14.2 3.7-15.5 11-1.2 7.4 3.7 14.3 11.1 15.6 46.6 7.9 89.1 30 122.9 63.7 33.8 33.8 55.8 76.3 63.7 122.9 1.1 6.6 6.8 11.2 13.3 11.2.8 0 1.5-.1 2.3-.2 7.3-1.1 12.3-8.1 11-15.4z"/>
        </svg>
      </a>
      <p>{$a}</p>
    </div>

EOD;
    # }}}
  }, 31);
  add_action('customize_register', function($custom) {
    # кастомайзер контактных данных {{{
    $custom->add_setting('header_phone', [
      'default' => '',
      'type'    => 'theme_mod',
    ]);
    $custom->add_control('header_phone', [
      'label'   => 'Телефон',
      'section' => 'title_tagline',
      'type'    => 'text',
    ]);
    # }}}
  });
  add_action('storefront_header', 'storefront_product_search', 32);
  add_action('storefront_header', function() {
    # мини-меню {{{
    # подготовка
    $url   = wc_get_page_permalink('myaccount');
    $class = is_account_page() ?
      ' is-current' : '';
    #$name  = is_user_logged_in() ?
    #  'Личный кабинет' : 'Авторизация';
    # вывод
    echo <<<EOD

    <div class="site-minimenu">
      <div class="modern-link noscript{$class}">
        <a href="{$url}"><div>Личный кабинет</div></a>
        <hr>
      </div>
    </div>

EOD;
    # }}}
  }, 33);
  add_action('storefront_header', function() {
    # корзина {{{
    # подготовка
    $url = wc_get_cart_url();
    $cnt = WC()->cart->get_cart_contents_count();
    $sum = WC()->cart->get_cart_total();
    # определяем набор классов
    $is_current = is_cart() ?
      ' is-current' : '';
    $hidden = $cnt === 0 ?
      ' hidden' : '';
    # вывод
    echo <<<EOD

    <div class="site-cart{$is_current}">
      <a href="{$url}" title="корзина товаров">
        <div>
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 446.843 446.843">
            <path d="M444.09 93.103a14.343 14.343 0 00-11.584-5.888H109.92c-.625 0-1.249.038-1.85.119l-13.276-38.27a14.352 14.352 0 00-8.3-8.646L19.586 14.134c-7.374-2.887-15.695.735-18.591 8.1-2.891 7.369.73 15.695 8.1 18.591l60.768 23.872 74.381 214.399c-3.283 1.144-6.065 3.663-7.332 7.187l-21.506 59.739a11.928 11.928 0 001.468 10.916 11.95 11.95 0 009.773 5.078h11.044c-6.844 7.616-11.044 17.646-11.044 28.675 0 23.718 19.298 43.012 43.012 43.012s43.012-19.294 43.012-43.012c0-11.029-4.2-21.059-11.044-28.675h93.776c-6.847 7.616-11.048 17.646-11.048 28.675 0 23.718 19.294 43.012 43.013 43.012 23.718 0 43.012-19.294 43.012-43.012 0-11.029-4.2-21.059-11.043-28.675h13.433c6.599 0 11.947-5.349 11.947-11.948s-5.349-11.947-11.947-11.947H143.647l13.319-36.996c1.72.724 3.578 1.152 5.523 1.152h210.278a14.33 14.33 0 0013.65-9.959l59.739-186.387a14.33 14.33 0 00-2.066-12.828zM169.659 409.807c-10.543 0-19.116-8.573-19.116-19.116s8.573-19.117 19.116-19.117 19.116 8.574 19.116 19.117-8.573 19.116-19.116 19.116zm157.708 0c-10.543 0-19.117-8.573-19.117-19.116s8.574-19.117 19.117-19.117c10.542 0 19.116 8.574 19.116 19.117s-8.574 19.116-19.116 19.116zm75.153-261.658h-73.161V115.89h83.499l-10.338 32.259zm-21.067 65.712h-52.094v-37.038h63.967l-11.873 37.038zm-146.882 0v-37.038h66.113v37.038h-66.113zm66.113 28.677v31.064h-66.113v-31.064h66.113zm-161.569-65.715h66.784v37.038h-53.933l-12.851-37.038zm95.456-28.674V115.89h66.113v32.259h-66.113zm-28.673-32.259v32.259h-76.734l-11.191-32.259h87.925zm-43.982 126.648h43.982v31.064h-33.206l-10.776-31.064zm167.443 31.065v-31.064h42.909l-9.955 31.064h-32.954z"/>
          </svg>
          <div><span class="site-cart-count{$hidden}">{$cnt}</span></div>
        </div>
        <span class="site-cart-sum">{$sum}</span>
      </a>
    </div>

EOD;
    # }}}
  }, 60);
  ###
  ### AJAX-запросы
  ###
  # обновление корзины {{{
  add_filter('woocommerce_add_to_cart_fragments', function($x) {
    # подготовка
    # определяем количество
    $cnt = WC()->cart->get_cart_contents_count();
    $hidden = $cnt === 0 ? ' hidden' : '';
    $cnt = <<<EOD
    <span class="site-cart-count{$hidden}">{$cnt}</span>
EOD;
    # определяем сумму
    $sum = WC()->cart->get_cart_total();
    $sum = <<<EOD
    <span class="site-cart-sum">{$sum}</span>
EOD;
    # задаем значения
    $x['span.site-cart-count'] = $cnt;
    $x['span.site-cart-sum']   = $sum;
    return $x;
  });
  # }}}
  ###
  ### оформление заказа
  ###
  add_filter('woocommerce_default_address_fields' , function($a) {
    # убираем неиспользуемые поля {{{
    unset($a['company']);
    unset($a['last_name']);
    unset($a['address_2']);
    unset($a['state']);
    return $a;
    # }}}
  });
  add_filter('woocommerce_checkout_fields' , function($fields) {
    # настройка полей билинга с контактной информацией {{{
    $a = &$fields['billing'];
    $a['billing_phone']['required'] = false;
    return $fields;
    # }}}
  });
  add_filter('woocommerce_order_button_html', function($html) {
    # настройка кнопы оплаты {{{
    return <<<EOD

<button type="submit"
        class="button alt"
        name="woocommerce_checkout_place_order"
        id="place_order">Оплатить</button>

EOD;
    # }}}
  });
  ###
  ### дополнительные поля
  ###
  # товары {{{
  # список вкладок:
  # - Основные, woocommerce_product_options_general_product_data
  # - Запасы, woocommerce_product_options_inventory_product_data
  # - Доставка, woocommerce_product_options_shipping
  # - Сопутствующие, woocommerce_product_options_related
  # - Атрибуты, woocommerce_product_options_attributes
  # - Дополнительно, woocommerce_product_options_advanced
  /***
  add_action('woocommerce_product_options_advanced', function() {
    woocommerce_wp_text_input([
      'id'       => '_test',
      'label'    => 'Test',
      'type'     => 'number',
      'value'    => '0',
      'desc_tip' => true,
      'description' => 'bla bla bla',
    ]);
  });
  add_action('woocommerce_process_product_meta', function() {
    # сохраняем данные
  });
  /***/
  # }}}
  # заказы {{{
  add_action('woocommerce_checkout_update_order_meta', function($id, $data = null) {
    # сохраняем дополнительные метаданные заказа
    # текущий домен/хост покупателя
    update_post_meta($id, '_customer_domain', $_SERVER['HTTP_HOST']);
  }, 10, 2);
  add_filter('manage_shop_order_posts_columns', function($col) {
    # интерфейс таблицы заказов
    # добавляем дополнительные колонки
    $col = StorefrontModern::array_insert_before($col, 'order_total', 'customer_domain', 'Домен');
    return $col;
  }, 11, 1);
  add_action('manage_shop_order_posts_custom_column', function($col, $id) {
    # интерфейс таблицы заказов
    # вывод значений
    if ($col === 'customer_domain') {
      echo get_post_meta($id, '_'.$col, true);
    }
  }, 10, 2);
  add_action('woocommerce_admin_order_data_after_billing_address', function($order) {
    # редактирование заказа => после биллинг полей
    # ...
    #$order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
    #echo '<p><strong>'.__('My Field Name').':</strong> ' . get_post_meta( $order_id, 'my_field_name', true ) . '</p>';
    #var_export($order);
  }, 10, 1);
  # }}}
  ###
  ### настройки
  ###
  add_filter('woocommerce_account_settings', function($settings) {
    # удаляем настройки аккаунта которые заданы жёстко {{{
    $res = [];
    $del = [
      'woocommerce_registration_generate_username',
    ];
    foreach ($settings as $s)
    {
      $flag = true;
      foreach ($del as $id) {
        if ($s['id'] === $id) {
          $found = false;
          break;
        }
      }
      if ($flag) {
        $res[] = $s;
      }
    }
    #var_dump($res);
    return $res;
    # }}}
  });
  ###
  ### разное
  ###
  # не отображаем заголовок на странице магазина
  add_filter('woocommerce_show_page_title', '__return_empty_string');
  # отключаем woo-стили (storefront это уже сделала)
  #add_filter('woocommerce_enqueue_styles', '__return_empty_array');
  # не отображаем элементы выбора сортировки
  # в шапке на странице магазина
  remove_action('woocommerce_before_shop_loop', 'storefront_sorting_wrapper', 9);
  remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10);
  remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
  remove_action('woocommerce_before_shop_loop', 'storefront_woocommerce_pagination', 30);
  remove_action('woocommerce_before_shop_loop', 'storefront_sorting_wrapper_close', 31);
  # задаем текст для кнопы добавления в корзину
  add_filter('woocommerce_product_add_to_cart_text', function() {
    return 'КУПИТЬ';
  });
  # убираем вывод сообщения о политике в форме регистрации аккаунта
  remove_action('woocommerce_register_form', 'wc_registration_privacy_policy_text', 20);
  # ...
  # }}}
});
# customize storefront
# {{{
function storefront_site_title_or_logo($echo = true) {
  # определяем логотип сайта (изображение)
  $img = <<<EOD

  <svg preserveAspectRatio="none" viewBox="0 0 18 16">
    <path fill-rule="evenodd" d="M17 0H1v2h16V0zm1 10V8l-1-5H1L0 8v2h1v6h10v-6h4v6h2v-6h1zm-9 4H3v-4h6v4z"/>
  </svg>

EOD;
  # определяем описание
  $info = get_bloginfo('description', 'display');
  if (!empty($info)) {
    $info = '<p class="site-description">'.esc_html($info).'</p>';
  }
  # определяем ссылку на главную
  $home = get_home_url();
  # вывод результата
  $html = '<a href="'.$home.'" class="site-title">'.$img.$info.'</a>';
  if ($echo) {
    echo $html;
  }
  return $html;
}
function storefront_primary_navigation() {
  # копия оригинальной функции за исключением Walker'a
  ?>
  <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_html_e( 'Primary Navigation', 'storefront' ); ?>">
  <button class="menu-toggle" aria-controls="site-navigation" aria-expanded="false"><span><?php echo esc_attr( apply_filters( 'storefront_menu_toggle_text', __( 'Menu', 'storefront' ) ) ); ?></span></button>
    <?php
    wp_nav_menu(
      array(
        'theme_location'  => 'primary',
        'container_class' => 'primary-navigation',
        'walker'          => new StorefrontModernMenu(true),
      )
    );

    wp_nav_menu(
      array(
        'theme_location'  => 'handheld',
        'container_class' => 'handheld-navigation',
      )
    );
    ?>
  </nav><!-- #site-navigation -->
  <?php
}
function storefront_credit()
{
  $links_output = '';
  if (apply_filters('storefront_credit_link', true)) {
    $links_output .= '<a href="https://woocommerce.com" target="_blank" rel="noreferrer" title="'.esc_attr__('WooCommerce - The Best eCommerce Platform for WordPress', 'storefront').'" rel="author">'.esc_html__('Built with Storefront &amp; WooCommerce', 'storefront').'</a>.';
  }
  if (apply_filters('storefront_privacy_policy_link', true) && function_exists('the_privacy_policy_link')) {
    $separator = '<span role="separator" aria-hidden="true"></span>';
    $links_output = get_the_privacy_policy_link( '', ( ! empty( $links_output ) ? $separator : '' ) ) . $links_output;
  }
  $links_output = apply_filters('storefront_credit_links_output', $links_output);
  $copyright = esc_html(apply_filters('storefront_copyright_text', $content = '&copy; '.get_bloginfo('name').' '.date('Y')));
  echo <<<EOD

  <div class="site-info">
    {$copyright}<br>
    {$links_output}
  </div>

EOD;
}
function storefront_page_header() {
  global $modernStore;
  ###
  # не отображаем заголовок
  if (($modernStore->isFrontPage && is_page_template('template-fullwidth.php')) ||
      $modernStore->isAccountPage)
  {
    return;
  }
  # вывод
  ?>
  <header class="entry-header">
    <?php
    storefront_post_thumbnail('full');
    the_title( '<h1 class="entry-title">', '</h1>');
    ?>
  </header><!-- .entry-header -->
  <?php
}
# }}}
?>