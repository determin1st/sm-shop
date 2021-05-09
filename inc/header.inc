<?php
ob_start();
?>

<div class="head">
<div>
  <div class="a">
    <!-- {{{ -->
    <div class="linkbox"><a href="{:home_url:}">
      <svg viewBox="0 0 18 16" preserveAspectRatio="none">
        <path fill-rule="evenodd" d="M17 0H1v2h16V0zm1 10V8l-1-5H1L0 8v2h1v6h10v-6h4v6h2v-6h1zm-9 4H3v-4h6v4z"/>
      </svg>
    </a></div>
    <!-- }}} -->
  </div>
  <form class="b" method="get" action="{:home_url:}">
    <!-- {{{ -->
    <div class="inputbox">
      <input type="text" name="s" value="{:search_query:}" placeholder="{:search_label:}" autocomplete="off">
      <button type="button" class="ok" disabled>
        <svg viewBox="0 0 18 18" preserveAspectRatio="none">
          <path fill-rule="evenodd" d="M12.5 11h-.8l-.3-.3c1-1.1 1.6-2.6 1.6-4.2a6.5 6.5 0 10-13 0C0 10.1 2.9 13 6.5 13c1.6 0 3.1-.6 4.2-1.6l.3.3v.8l5 5 1.5-1.5-5-5zm-6 0C4 11 2 9 2 6.5S4 2 6.5 2 11 4 11 6.5 9 11 6.5 11z"/>
        </svg>
      </button>
    </div>
    <input type="hidden" name="post_type" value="product">
    <input type="hidden" name="title" value="1">
    <!--
    <input type="hidden" name="excerpt" value="1">
    <input type="hidden" name="content" value="1">
    <input type="hidden" name="categories" value="1">
    <input type="hidden" name="attributes" value="1">
    <input type="hidden" name="tags" value="1">
    <input type="hidden" name="sku" value="1">
    <input type="hidden" name="orderby" value="date-DESC">
    <input type="hidden" name="ixwps" value="1">
    //-->
    <!-- }}} -->
  </form>
  <div class="c">
    <!-- {{{ -->
    <div class="linkbox"><a href="{:contact_telegram:}" target="_blank">
      <svg viewBox="0 0 100 100" preserveAspectRatio="none">
        <path d="M95 9.9c-1.3-1.1-3.4-1.2-7-.1a1189.4 1189.4 0 00-77.9 31.8c-1.9.6-6 2.4-6.2 5.2-.1 1.8 1.4 3.4 4.3 4.7 3.1 1.6 16.8 6.2 19.7 7.1l7.2 24.5a5.2 5.2 0 002.2 3.2l.5.4c.3.2.7.3 1.2.3a4 4 0 002.2-.8c3.7-3 10.1-9.7 11.9-11.6 7.9 6.2 16.5 13.1 17.3 13.9l.1.1a9.3 9.3 0 005.7 2.5l1.8-.3c2.1-.7 3.6-2.7 4.1-5.4l.3-1.2c3.4-14.8 6.1-27.8 8.3-38.7 2.1-10.7 3.8-21.2 4.8-26.8l.5-3.2c.3-2 .5-4.3-1-5.6zM30 58.3l47.7-31.6.4-.3.2-.1.2-.1-.4.6L66 38.1 39.3 62.5v.1l-.1.1-.1.1-.1.2v.2l-1.8 19.5v-.1L30 58.3z"/>
      </svg>
    </a></div>
    <div class="linkbox"><a class="" href="{:logout_url:}">
      <svg viewBox="0 0 18 18" preserveAspectRatio="none">
        <path fill-rule="evenodd" d="M7.1 12.6L8.5 14l5-5-5-5-1.4 1.4L9.7 8H0v2h9.7l-2.6 2.6zM16 0H2a2 2 0 00-2 2v4h2V2h14v14H2v-4H0v4c0 1.1.9 2 2 2h14a2 2 0 002-2V2a2 2 0 00-2-2z"/>
      </svg>
    </a></div>
    <div class="sm-blocks minicart v">
      <div class="v">
        <a href="{:cart_url:}" title="корзина заказа">
          <svg viewBox="0 0 446.843 446.843" preserveAspectRatio="none">
            <path d="M444.09 93.103a14.343 14.343 0 00-11.584-5.888H109.92c-.625 0-1.249.038-1.85.119l-13.276-38.27a14.352 14.352 0 00-8.3-8.646L19.586 14.134c-7.374-2.887-15.695.735-18.591 8.1-2.891 7.369.73 15.695 8.1 18.591l60.768 23.872 74.381 214.399c-3.283 1.144-6.065 3.663-7.332 7.187l-21.506 59.739a11.928 11.928 0 001.468 10.916 11.95 11.95 0 009.773 5.078h11.044c-6.844 7.616-11.044 17.646-11.044 28.675 0 23.718 19.298 43.012 43.012 43.012s43.012-19.294 43.012-43.012c0-11.029-4.2-21.059-11.044-28.675h93.776c-6.847 7.616-11.048 17.646-11.048 28.675 0 23.718 19.294 43.012 43.013 43.012 23.718 0 43.012-19.294 43.012-43.012 0-11.029-4.2-21.059-11.043-28.675h13.433c6.599 0 11.947-5.349 11.947-11.948s-5.349-11.947-11.947-11.947H143.647l13.319-36.996c1.72.724 3.578 1.152 5.523 1.152h210.278a14.33 14.33 0 0013.65-9.959l59.739-186.387a14.33 14.33 0 00-2.066-12.828zM169.659 409.807c-10.543 0-19.116-8.573-19.116-19.116s8.573-19.117 19.116-19.117 19.116 8.574 19.116 19.117-8.573 19.116-19.116 19.116zm157.708 0c-10.543 0-19.117-8.573-19.117-19.116s8.574-19.117 19.117-19.117c10.542 0 19.116 8.574 19.116 19.117s-8.574 19.116-19.116 19.116zm75.153-261.658h-73.161V115.89h83.499l-10.338 32.259zm-21.067 65.712h-52.094v-37.038h63.967l-11.873 37.038zm-146.882 0v-37.038h66.113v37.038h-66.113zm66.113 28.677v31.064h-66.113v-31.064h66.113zm-161.569-65.715h66.784v37.038h-53.933l-12.851-37.038zm95.456-28.674V115.89h66.113v32.259h-66.113zm-28.673-32.259v32.259h-76.734l-11.191-32.259h87.925zm-43.982 126.648h43.982v31.064h-33.206l-10.776-31.064zm167.443 31.065v-31.064h42.909l-9.955 31.064h-32.954z"/>
          </svg>
          <div class="count"><div></div></div>
        </a>
      </div>
    </div>
    <!-- }}} -->
  </div>
</div>
</div>

{{menu}}

<?php
echo \SM\Blocks::parse(ob_get_clean(), [
  'home_url'     => esc_url(home_url('/')),
  'logout_url'   => wp_logout_url('/'),
  'search_label' => 'поиск товара..',
  'search_query' => get_search_query(),
  'contact_telegram' => 'https://t.me/sm_blocks',
  'cart_url'     => wc_get_cart_url(),
], '{: :}');
?>

<div class="body">
