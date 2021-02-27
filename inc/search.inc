<div id="search">
<?php
  ###
  echo '<div class="list">';
  while (have_posts())
  {
    the_post();
    $img   = get_the_post_thumbnail();
    $title = get_the_title();
    $link  = get_the_permalink();
    $item  = wc_get_product($post->ID);
    $price = $item->get_price();
    echo <<<EOD

  <div class="product" data-href="{$link}">
    <div class="a">{$img}</div>
    <div class="b">{$title}</div>
    <div class="c">{$price}</div>
  </div>

EOD;
  }
  echo '</div>';
  ###
  the_posts_pagination([
    'type'      => 'list',
    'next_text' => 'далее',
    'prev_text' => 'назад',
  ]);
  ###
?>
</div>
