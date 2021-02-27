<?php defined('ABSPATH') || exit; ?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="data:image/x-icon;," type="image/x-icon">
  <?php
  # {{{
  foreach (StorefrontModern::$fonts as $a) {
    echo '<link rel="preload" href="'.$a.'" as="font" type="font/woff2" crossorigin>';
  }
  wp_head();
  # }}}
  ?>
</head>
<body>
  <?php
  # {{{
  # prepare
  wp_body_open();
  $path = __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR;
  $page = StorefrontModern::page();
  # output
  if (StorefrontModern::exclusive())
  {
    # exclusive
    include $path.$page.'.inc';
  }
  else
  {
    # inclusive
    include $path.'header.inc';
    if ($page !== 'index')
    {
      # specific page
      ob_start();
      include $path.$page.'.inc';
      #echo StorefrontModernBlocks::parse(ob_get_clean());
      echo apply_filters('the_content', ob_get_clean());
    }
    else
    {
      # front page
      # ...
    }
    include $path.'footer.inc';
  }
  wp_footer();
  # }}}
  ?>
</body>
</html>
