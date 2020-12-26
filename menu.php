<?php
/**
* Custom WordPress menu handler
* Обработчик нестандартного меню WordPress
*
* Features (Особенности):
* - ...
*/
class StorefrontModernMenu extends Walker_Nav_Menu {
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
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
  <path stroke="#000" stroke-linejoin="bevel" stroke-miterlimit="10" stroke-width="4" d="M15 24l17 17 17-17"/>
</svg>
    ',
    # отступ для вложенных
    'pad' => '
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
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
}
?>
