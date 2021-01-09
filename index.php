<?php defined('ABSPATH') || exit; ?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body>
  <?php wp_body_open(); ?>
  <?php include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'index.inc'; ?>
  <?php wp_footer(); ?>
</body>
</html>
