<?php
    use Roots\Sage\Options;

    $options = Options\get_options();
    $favicon = $options['favicon'] ? $options['favicon'] : get_template_directory_uri().'/dist/images/favicon.ico';
    ?>
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="<?php echo $favicon; ?>">
  <?php wp_head(); ?>
</head>
