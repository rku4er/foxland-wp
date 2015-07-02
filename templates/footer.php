<?php $hide_footer = function_exists('get_field') ? get_field('hide_footer') : false; ?>
<?php if(!$hide_footer): ?>
<footer class="content-info" role="contentinfo">
  <div class="container">
    <?php dynamic_sidebar('sidebar-footer'); ?>
  </div>
</footer>
<?php endif; ?>
