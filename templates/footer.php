<?php
    use Roots\Sage\Options;

    $options = Options\get_options();
    $copyright = $options['copyright'];
?>
<footer class="content-info" role="contentinfo">
  <div class="container">
    <div class="holder"><?php dynamic_sidebar('sidebar-footer'); ?></div>
  <div class="copyright"><?php echo $copyright; ?></div>
  </div>
</footer>
