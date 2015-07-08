<?php
    use Roots\Sage\Options;

    $options = Options\get_options();
    $hide_footer = $options['hide_footer'] ? $options['hide_footer'] : false;
    $copyright = $options['copyright'];
?>
<?php if(!$hide_footer): ?>
<footer class="content-info" role="contentinfo">
  <div class="container">
    <div class="holder"><?php dynamic_sidebar('sidebar-footer'); ?></div>
  <div class="copyright"><?php echo $copyright; ?></div>
  </div>
</footer>
<?php endif; ?>
