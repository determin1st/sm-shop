<?php # shop template
defined( 'ABSPATH' ) || exit;
# generate markup
ob_start();
?>

<div id="sm-shop">
  <div class="body">

    <div class="ctl sm-blocks-resizer">
      <div class="panel">
        <!-- :sm-blocks/panel-slider {} /-->
      </div>
      <div class="grid">
        <!-- wp:sm-blocks/paginator {"gotoMode":3} /-->
        <!-- wp:sm-blocks/rows-selector {} /-->
        <!-- wp:sm-blocks/orderer {
          "switchMode":1
        } /-->
      </div>
    </div>

    <div class="sep"><hr /></div>

    <div class="view">
      <div class="panel">
        <div class="content">
          <!-- wp:sm-blocks/price-filter {
          } /-->
          <!-- wp:sm-blocks/category-filter {

            "hasEmpty":true,
            "hasCount":true

          } /-->
          <!-- :sm-blocks/category-filter {"baseCategory":"37"} /-->
        </div>
      </div>
      <!-- wp:sm-blocks/products {

        "layout":"4:2:1:0",
        "wrapAround":false

      } /-->
      <div class="sep"><hr /></div>
    </div>

  </div>
  <div class="foot">
    <div class="sep"><hr /></div>
    <div class="box"></div>
  </div>
</div>

<?php
$o = ob_get_clean();
# output
get_header('shop');
echo StorefrontModernBlocks::parse($o);
get_footer('shop');
?>
