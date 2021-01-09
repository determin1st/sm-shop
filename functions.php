<?php
# check wordpress loaded {{{
if (!defined('ABSPATH') || !function_exists('add_action')) {
  exit;
}
# }}}
class StorefrontModern {
  # data {{{
  private static
    $I = null;    # singleton instance
  public
    $URI   = '',  # URI of the theme
    $ERROR = '',  # fatal error message
    # current page flags
    $isCharged     = false, # theme ready
    $isAdmin       = false,
    $isGutenberg   = false,
    $isCustomizer  = false,
    $isAccountPage = false,
    $isFrontPage   = false,
    $isCart        = false,
    $isShop        = false;
  public static
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
      ['index', ['gsap']],
      ['catalog', ['sm-blocks']],
      ['login', ['gsap']],
    ],
    # }}}
    $styles = [ # {{{
      'index',
      'catalog',
      'login',
    ];
    # }}}
  # }}}
  # constructor {{{
  private function __construct()
  {
    # check requirements {{{
    ###
    global $wp_version;
    $I = $this;
    ###
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
    # initialize {{{
    $I->URI = get_stylesheet_directory_uri();
    $I->isAdmin = is_admin();
    $I->isGutenberg = function_exists('register_block_type');
    $I->isCustomizer = is_customize_preview();
    ###
    add_action('wp', function() use ($I) {
      ###
      $I->isAccountPage = is_account_page();
      $I->isFrontPage   = is_front_page();
      $I->isCart        = is_cart();
      $I->isShop        = is_shop();
      if ($I->isShop) {
        StorefrontModernBlocks::init();
      }
      add_action('wp_enqueue_scripts', function() use ($I) {
        $I->enqueue();
      });
    });
    $I->isCharged = true;
    # }}}
    # set theme features {{{
    add_theme_support('woocommerce');
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
    add_theme_support('editor-styles');
    add_editor_style('inc/admin-editor.css');
    register_nav_menus([
      'primary' => 'Primary menu',
    ]);
    ###
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
    # register scripts and styles {{{
    # some scripts are separate projects that may be included
    # locally (from the same-origin) or remotely (from cdn)
    wp_register_script(
      'gsap',
      (file_exists(__DIR__.'/inc/gsap')
        ? $I->URI.'/inc/gsap/gsap.min.js'
        : 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.2.6/gsap.min.js'),
      [], false, true
    );
    # internal
    foreach (self::$scripts as $a)
    {
      wp_register_script(
        'sm-shop-'.$a[0],
        $I->URI.'/inc/'.$a[0].'.js',
        $a[1], false, true
      );
    }
    foreach (self::$styles as $a) {
      wp_register_style('sm-shop-'.$a, $I->URI.'/inc/'.$a.'.css');
    }
    # }}}
    # tune wordpress environment {{{
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
  public static function isReady() {
    return (self::$I && self::$I->isCharged) ? true : false;
  }
  public static function getError() {
    return self::$I->ERROR;
  }
  public static function parse($html, $data)
  {
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
  }
  # }}}
  # enqueue scripts and styles {{{
  private function enqueue()
  {
    wp_enqueue_script('sm-shop-index');
    wp_enqueue_style('sm-shop-index');
    if ($I->isShop)
    {
      wp_enqueue_script('sm-shop-catalog');
      wp_enqueue_style('sm-shop-catalog');
    }
    ###
    /***
    if ($me->isFrontPage)
    {
      wp_enqueue_style('flickity-css', $d.'/inc/flickity/flickity.min.css');
      wp_enqueue_script('flickity-js', $d.'/inc/flickity/flickity.pkgd.min.js');
    }
    /***/
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
class StorefrontModernMenu extends Walker_Nav_Menu {
  # {{{
  # данные {{{
  # woocommerce
  private $wc_active      = false;
  private $wc_shop_active = false;
  private $wc_shop_id     = 0;
  # блог
  private $wp_blog_active = false;
  private $wp_blog_id     = 0;
  # иконки
  public $svg = [
    # маркер выпадающего списка
    'dropdown_arrow' => '
    <svg preserveAspectRatio="none" viewBox="0 0 64 64">
      <path stroke="#000" stroke-linejoin="bevel" stroke-miterlimit="10" stroke-width="4" d="M15 24l17 17 17-17"/>
    </svg>
    ',
    # отступ для вложенных
    'pad' => '
    <svg preserveAspectRatio="none" viewBox="0 0 100 100">
      <path d="M72.4 48.6l-42-42c-.8-.8-2-.8-2.8 0-.8.8-.8 2 0 2.8L68.2 50 27.6 90.6c-.8.8-.8 2 0 2.8.4.4.9.6 1.4.6s1-.2 1.4-.6l42-42c.8-.8.8-2 0-2.8z"/>
    </svg>
    ',
  ];
  # }}}
  # конструктор {{{
  function __construct($woocommerce_plugin = false)
  {
    global $wp_query;
    ###
    # инициализация параметров woocommerce
    if ($woocommerce_plugin)
    {
      if ($this->wc_active = is_woocommerce())
      {
        # проверим выбрана ли страница:
        # - конкретного товара
        # - категории товара
        # - тэга товара
        if (is_product() ||
            is_product_category() ||
            is_product_tag())
        {
          # эти страницы логически связаны со страницей магазина, но,
          # по-умолчанию элемент меню не получает класс с соответствующей индикацией.
          # необходимо определить идентификатор страницы магазина,
          # чтобы при выводе элементов исправить это недоразумение.
          $this->wc_shop_active = true;
          $this->wc_shop_id = wc_get_page_id('shop');
        }
      }
    }
    # инициализация параметров блога
    # определим идентификатор основной страницы блога
    while (($a = get_option('page_for_posts')) !== false) {
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
  }
  # }}}
  # обработчики начала и конца уровня {{{
  function start_lvl(&$output, $depth = 0, $args = array())
  {
    if ($depth < 1) {
      $a = '<div class="dropdown">';
    }
    else {
      $a = '';
    }
    $output .= "\n".$a;
  }
  function end_lvl(&$output, $depth = 0, $args = array())
  {
    if ($depth < 1) {
      $a = '</div>';
    }
    else {
      $a = '';
    }
    $output .= $a;
  }
  # }}}
  # обработчики начала и конца элемента {{{
  function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
  {
    # определяем наименование
    $title = apply_filters('the_title', $item->title, $item->ID);
    # определяем параметры ссылки
    $param  = !empty($item->attr_title) ? ' title="'  .esc_attr($item->attr_title).'"' : '';
    $param .= !empty($item->target)     ? ' target="' .esc_attr($item->target    ).'"' : '';
    $param .= !empty($item->xfn)        ? ' rel="'    .esc_attr($item->xfn       ).'"' : '';
    $param .= !empty($item->url)        ? ' href="'   .esc_attr($item->url       ).'"' : '';
    # определяем набор классов
    $classes = '';
    if (is_array($item->classes)) {
      $classes = trim(implode(' ', $item->classes));
    }
    # определяем идентификатор объекта на который ссылается элемент
    $object_id = intval($item->object_id);
    # определяем тип элемента
    if ($depth === 0) {
      # основной
      # проверяем необходимо ли установить дополнительный класс
      # для логической связи данного пункта меню
      if (($this->wc_shop_active &&
           $this->wc_shop_id === $object_id) ||
          ($this->wp_blog_active &&
           $this->wp_blog_id === $object_id))
      {
        $classes = trim($classes.' current-menu-ancestor');
      }
      # проверяем наличие выпадающего меню
      $svg = '';
      if (strpos($classes, 'menu-item-has-children') !== false) {
        $svg = $this->svg['dropdown_arrow'];
      }
      # готово
      $output .= <<<EOD

      <li class="{$classes}">
      <a{$param}>
        <div>{$title}</div>{$svg}
      </a>
      <hr>

EOD;
    }
    else {
      # внутренний
      # проверим подтип элемента
      switch ($item->url) {
      case '#':
        # заголовок
        $output .= <<<EOD

        <h3>{$title}</h3>

EOD;
        break;
      default:
        # ссылка
        # формируем отступ
        $pad = '';
        if (($a = $depth) > 1) {
          while (--$a) {
            $pad .= $this->svg['pad'];
          }
        }
        # готово
        $output .= <<<EOD

        <a{$param} class="{$classes}">
          {$pad}<div>{$title}</div>
        </a>

EOD;
        break;
      }
    }
  }
  function end_el(&$output, $item, $depth = 0, $args = array())
  {
    if ($depth === 0) {
      $output .= '</li>';
    }
	}
  # }}}
  # }}}
}
# setup hook {{{
add_action('after_setup_theme', function() {
  StorefrontModern::init();
});
# }}}
?>
