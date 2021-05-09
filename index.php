<?php defined('ABSPATH') || exit; ?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="data:image/x-icon;," type="image/x-icon">
  <?php
  # {{{
  foreach (\SM\Shop::$fonts as $a) {
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
  $page = \SM\Shop::page();
  # output
  if (\SM\Shop::exclusive())
  {
    # exclusive
    include $path.$page.'.inc';
  }
  else
  {
    # inclusive
    ob_start();
    include $path.'header.inc';
    include $path.$page.'.inc';
    include $path.'footer.inc';
    echo \SM\Blocks::parse(ob_get_clean());
  }
  wp_footer();
  # }}}
  ?>
</body>
</html>
