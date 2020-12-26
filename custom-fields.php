<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

/***
Container::make('post_meta', 'Дополнительные данные')
  ->where('post_parent_id', '=', 65)
  ->add_fields([
    Field::make('image', 'crb_image', 'Дополнительное изображение'),
  ]);
/***/
?>
