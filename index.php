<?php
$SM = '\\SM\\Shop';
if (!class_exists($SM, false)) {
  exit(0);
}
?><!DOCTYPE html>
<html lang="<?php $SM::lang();?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="data:image/x-icon;," type="image/x-icon">
  <?php $SM::head(); ?>
</head>
<body>
  <?php $SM::body(); ?>
</body>
</html>
