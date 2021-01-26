<?php
# prepare
$path = __DIR__.DIRECTORY_SEPARATOR;
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
  if ($page === 'index')
  {
    # front page
    # ...
  }
  else
  {
    # specific page
    ob_start();
    include $path.$page.'.inc';
    echo StorefrontModernBlocks::parse(ob_get_clean());
  }
}
?>
