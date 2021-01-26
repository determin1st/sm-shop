<?php
class StorefrontModern {
  # data {{{
  private static
    $I = null,# singleton instance
    $icons = [ # {{{
      # https://www.iconfinder.com/iconsets/ionicons
      'filing' => '
      <svg preserveAspectRatio="none" viewBox="0 0 512 512">
        <path d="M381 128.6H132.1c-12.1 0-19.5 0-19.5 20.4v28.1h288V149c0-20.4-7.4-20.4-19.6-20.4zM333 96.5H180c-13.1 0-19.5.3-19.5 18.7h192c-.1-18.4-6.4-18.7-19.5-18.7z"/>
        <path d="M432.4 169.6l-15.9-9.4v32.3h-321v-32.3l-15.2 9.4c-14.3 8.9-17.8 15.3-15 40.9l17.5 184.8c3.7 20.7 15.9 21.2 24 21.2h299.9c8.1 0 20.2-.5 23.9-21.2l17.2-184.4c2.3-24.4-2-32.8-15.4-41.3z"/>
      </svg>
      ',
      'box' => '
      <svg preserveAspectRatio="none" viewBox="0 0 512 512">
        <path d="M112 400h288V208H112v192zm112-160h64c8.8 0 16 7.2 16 16s-7.2 16-16 16h-64c-8.8 0-16-7.2-16-16s7.2-16 16-16zM96 112v80h320v-80z"/>
      </svg>
      ',
      'arrowFwd' => '
      <svg preserveAspectRatio="none" viewBox="0 0 512 512">
        <path d="M160 128.4L192.3 96 352 256 192.3 416 160 383.6 287.3 256z"/>
      </svg>
      ',
      'arrowBwd' => '
      <svg preserveAspectRatio="none" viewBox="0 0 512 512">
        <path d="M352 128.4L319.7 96 160 256l159.7 160 32.3-32.4L224.7 256z"/>
      </svg>
      ',
      'arrowLeft' => '
      <svg preserveAspectRatio="none" viewBox="0 0 512 512">
        <path d="M327.3 98.9l-2.1 1.8-156.5 136c-5.3 4.6-8.6 11.5-8.6 19.2 0 7.7 3.4 14.6 8.6 19.2L324.9 411l2.6 2.3c2.5 1.7 5.5 2.7 8.7 2.7 8.7 0 15.8-7.4 15.8-16.6V112.6c0-9.2-7.1-16.6-15.8-16.6-3.3 0-6.4 1.1-8.9 2.9z"/>
      </svg>
      ',
      'arrowDown' => '
      <svg preserveAspectRatio="none" viewBox="0 0 512 512">
        <path d="M98.9 184.7l1.8 2.1 136 156.5c4.6 5.3 11.5 8.6 19.2 8.6 7.7 0 14.6-3.4 19.2-8.6L411 187.1l2.3-2.6c1.7-2.5 2.7-5.5 2.7-8.7 0-8.7-7.4-15.8-16.6-15.8H112.6c-9.2 0-16.6 7.1-16.6 15.8 0 3.3 1.1 6.4 2.9 8.9z"/>
      </svg>
      ',
      ###
    ],
    # }}}
    $scripts = [ # {{{
      'index' => ['sm-blocks'],
      'auth'  => ['sm-index'],
    ],
    # }}}
    $styles = [ # {{{
      'index'   => ['sm-blocks'],
      'auth'    => ['sm-index'],
      'catalog' => ['sm-index'],
    ];
    # }}}
  public
    $BRAND = 'sm-shop',
    $URI   = '',# URI of the theme
    $LANG  = '',# current language
    $ERROR = '',# fatal message
    $PAGE  = 'error',# current page template
    ###
    $isExclusive  = true,
    $isLoggedIn   = false,
    $isAdmin      = false,
    $isGutenberg  = false,
    ###
    $inCustomizer = false,
    $inCart       = false,
    $inProduct    = false,
    $inShop       = false;
  # }}}
  # constructor {{{
  private function __construct()
  {
    # checks {{{
    global $wp_version;
    $I = $this;
    if (version_compare('7.4', phpversion(), '>'))
    {
      $I->ERROR = 'newer PHP version required';
      return;
    }
    if (version_compare('5.5', $wp_version, '>'))
    {
      $I->ERROR = 'newer WordPress version required';
      return;
    }
    if (!class_exists('WooCommerce', false))
    {
      $I->ERROR = 'WooCommerce plugin required';
      return;
    }
    if (!class_exists('StorefrontModernBlocks', false))
    {
      $I->ERROR = 'sm-blocks plugin required';
      return;
    }
    # }}}
    # initialization {{{
    # after theme setup
    $I->URI  = get_stylesheet_directory_uri();
    $I->LANG = substr(get_locale(), 0, 2);
    $I->isLoggedIn  = is_user_logged_in();
    $I->isAdmin     = is_admin();
    $I->isGutenberg = function_exists('register_block_type');
    add_action('wp', function() use ($I)
    {
      # after wordpress is ready
      $I->inCustomizer = is_customize_preview();
      #$I->inAccountPage = is_account_page();
      #$I->inFrontPage   = is_front_page();
      $I->inCart    = is_cart();
      $I->inProduct = (is_product() || is_product_category() || is_product_tag());
      $I->inShop    = is_shop();
      # determine page template
      if (!$I->isLoggedIn) {
        $I->PAGE = 'auth';
      }
      else
      {
        $I->isExclusive = false;
        if ($I->inShop) {
          $I->PAGE = 'catalog';
        }
        else {
          $I->PAGE = 'index';
        }
      }
      # set enqueue hook
      add_action('wp_enqueue_scripts', function() use ($I)
      {
        if ($I->isExclusive)
        {
          wp_enqueue_script('sm-'.$I->PAGE);
          wp_enqueue_style('sm-'.$I->PAGE);
        }
        else
        {
          wp_enqueue_script('sm-index');
          wp_enqueue_style('sm-index');
          if (array_key_exists($I->PAGE, $I::$styles)) {
            wp_enqueue_style('sm-'.$I->PAGE);
          }
          # add sm-blocks launcher with initial configuration
          $cfg = StorefrontModernBlocks::config($I->BRAND);
          wp_add_inline_script(
            'sm-index',
            'SM().attach(document,'.$cfg.');'
          );
        }
      });
      /***
      global $wp_query;
      ###
      # инициализация параметров блога
      # определим идентификатор основной страницы блога
      while (($a = get_option('page_for_posts')) !== false)
      {
        # сохраняем как число
        $this->wp_blog_id = $a = intval($a);
        # определим идентификатор текущего объекта
        if (($b = $wp_query->get_queried_object_id()) === 0) {
          break;
        }
        # получаем данные
        if (($b = get_post($b)) === null) {
          break;
        }
        # определим является ли объект постом блога
        if ($b->ID !== $a && $b->post_type === 'post') {
          $this->wp_blog_active = true;
        }
        break;
      }
      /***/
    });
    # }}}
    # registration {{{
    # external scripts
    # these scripts are separate projects that may be stored
    # locally (same-origin) or remotely (cdn)
    wp_register_script(
      'gsap',
      (file_exists(__DIR__.'/inc/gsap')
        ? $I->URI.'/inc/gsap/gsap.min.js'
        : 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.2.6/gsap.min.js'),
      [], false, true
    );
    # internal scripts
    foreach (self::$scripts as $a => $b)
    {
      wp_register_script(
        'sm-'.$a,
        $I->URI.'/inc/'.$a.'.js',
        $b, false, true
      );
    }
    # internal styles
    foreach (self::$styles as $a => $b)
    {
      wp_register_style(
        'sm-'.$a,
        $I->URI.'/inc/'.$a.'.css',
        $b
      );
    }
    # navigation menus
    $a = [];
    $a[$I->BRAND] = ($I->LANG === 'ru')
      ? 'основное меню '.$I->BRAND
      : $I->BRAND.'primary menu';
    register_nav_menus($a);
    # }}}
    # features {{{
    #add_theme_support('woocommerce');
    /***
    add_theme_support('woocommerce', [
      'thumbnail_image_width' => 150,
      'single_image_width'    => 300,
      'product_grid' => [
          'default_rows'    => 3,
          'min_rows'        => 2,
          'max_rows'        => 8,
          'default_columns' => 4,
          'min_columns'     => 2,
          'max_columns'     => 5,
      ],
    ]);
    /***/
    #add_theme_support('editor-styles');
    #add_editor_style('inc/admin-editor.css');
    ###
    # <iframe> lazy loading
    add_filter('embed_oembed_html', function($html)
    {
      # check
      if (stripos($html, '<iframe') === false ||
          ($a = strpos($html, ' src=')) === false)
      {
        return $html;
      }
      # convert source into data attribute
      $html = substr($html, 0, $a).' data-src='.substr($html, $a + 5);
      # add loading attribute (for modern browsers)
      if (strpos($html, ' loading=') === false)
      {
        $a = strpos($html, '>');
        $html = substr($html, 0, $a).' loading="lazy"'.substr($html, $a);
      }
      return $html;
    });
    # explicit media uploads
    add_filter('mime_types', function($mimes) {
      $mimes['webp'] = 'image/webp';
      #$mimes['svg']  = 'image/svg+xml';
      return $mimes;
    });
    add_action('upload_mimes', function($mimes = []) {
      $mimes['webp'] = 'image/webp';
      #$mimes['svg']  = 'image/svg+xml';
      return $mimes;
    });
    add_filter('file_is_displayable_image', function($result, $path) {
      if (!$result)
      {
        # allow WEBP miniature
        $info = @getimagesize($path);
        if ($info && $info[2] === IMAGETYPE_WEBP) {
          return true;
        }
      }
      return true;
    }, 10, 2);
    # }}}
    # tuning {{{
    # disable rss-feed in <head>
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);
    # disable DNS-prefetch
    remove_action('wp_head', 'wp_resource_hints', 2);
    # disable XMLRPC
    add_filter('xmlrpc_enabled', '__return_false');
    remove_action('wp_head', 'rsd_link');
    # disable Windows Live Writer Manifest Link
    remove_action('wp_head', 'wlwmanifest_link');
    # disable shortlink
    remove_action('wp_head', 'wp_shortlink_wp_head');
    # disable wordpress info markers (fingerprint)
    remove_action('wp_head', 'wp_generator');
    add_filter('the_generator', '__return_empty_string');
    # disable emoji
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
    if ($I->isAdmin)
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
      ###
      # disable superficial menu options
      add_action('admin_menu', function()
      {
        global $submenu;
        # more: /wp-admin/menu.php
        unset($submenu['themes.php'][15]);
        unset($submenu['themes.php'][20]);
      }, 999);
    }
    # }}}
  }
  # }}}
  # api {{{
  public static function init()
  {
    if (self::$I === null) {
      self::$I = new StorefrontModern();
    }
  }
  public static function error()      {return self::$I->ERROR;}
  public static function page()       {return self::$I->PAGE;}
  public static function exclusive()  {return self::$I->isExclusive;}
  public static function parse($html, $data)
  {
    # {{{
    # TODO: triple brackets
    # get template tokens
    $list = [];
    if (!preg_match_all('/{{([^}]+)}}/', $html, $list) ||
        count($list) < 2 || count($list[1]) === 0)
    {
      return $html;# nothing to substitute
    }
    # iterate
    foreach ($list[1] as $a)
    {
      # extract value
      $b = (array_key_exists($a, $data) && is_string($data[$a]))
        ? $data[$a]
        : '';
      # substitute
      $html = str_replace('{{'.$a.'}}', $b, $html);
    }
    # tidy gaps and complete
    return preg_replace('/>\s+</', '><', $html);
    # }}}
  }
  # }}}
  # {{{
  # multi-domain {{{
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
  # }}}
}
# hookup {{{
# check wordpress loaded
if (defined('ABSPATH') || function_exists('add_action'))
{
  add_action('after_setup_theme', function() {
    StorefrontModern::init();
  });
}
# }}}
?>
