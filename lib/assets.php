<?php

namespace Roots\Sage\Assets;
use Roots\Sage\Options;

/**
 * Scripts and stylesheets
 *
 * Enqueue stylesheets in the following order:
 * 1. /theme/dist/styles/main.css
 *
 * Enqueue scripts in the following order:
 * 1. /theme/dist/scripts/modernizr.js
 * 2. /theme/dist/scripts/main.js
 */

class JsonManifest {
  private $manifest;

  public function __construct($manifest_path) {
    if (file_exists($manifest_path)) {
      $this->manifest = json_decode(file_get_contents($manifest_path), true);
    } else {
      $this->manifest = [];
    }
  }

  public function get() {
    return $this->manifest;
  }

  public function getPath($key = '', $default = null) {
    $collection = $this->manifest;
    if (is_null($key)) {
      return $collection;
    }
    if (isset($collection[$key])) {
      return $collection[$key];
    }
    foreach (explode('.', $key) as $segment) {
      if (!isset($collection[$segment])) {
        return $default;
      } else {
        $collection = $collection[$segment];
      }
    }
    return $collection;
  }
}

function asset_path($filename) {
  $dist_path = get_template_directory_uri() . DIST_DIR;
  $directory = dirname($filename) . '/';
  $file = basename($filename);
  static $manifest;

  if (empty($manifest)) {
    $manifest_path = get_template_directory() . DIST_DIR . 'assets.json';
    $manifest = new JsonManifest($manifest_path);
  }

  if (array_key_exists($file, $manifest->get())) {
    return $dist_path . $directory . $manifest->get()[$file];
  } else {
    return $dist_path . $directory . $file;
  }
}

function assets() {
  $options = Options\get_options();
  $custom_css = preg_replace('/<br\s?\/?>/', '', $options['custom_css']);

  $styles = "
/* Options page CSS */
body {
    font-family: {$options['body_font-family']['font']};
    font-size: {$options['body_font-size_mobile']};
}
@media screen and (min-width: 768px){
    body {
        font-size: {$options['body_font-size_tablet']};
    }
}
@media screen and (min-width: 992px){
    body {
        font-size: {$options['body_font-size_desktop']};
    }
}
@media screen and (min-width: 1200px){
    body {
        font-size: {$options['body_font-size_largedesktop']};
    }
}
h1,h2,h3,h4,h5,h6{
    font-family: {$options['headings_font-family']['font']};
    font-weight: {$options['headings_font-weight']};
}
h1 {
    font-size: {$options['h1_font-size']};
}
h2 {
    font-size: {$options['h2_font-size']};
}
h3 {
    font-size: {$options['h3_font-size']};
}
h4 {
    font-size: {$options['h4_font-size']};
}
h5 {
    font-size: {$options['h5_font-size']};
}
h6 {
    font-size: {$options['h6_font-size']};
}
.navbar-default .navbar-brand{
    font-size: {$options['navbar_brand_font-size']};
    font-family: {$options['navbar_brand_font-family']['font']};
    text-transform: {$options['navbar_brand_text-transform']};
    font-weight: {$options['navbar_brand_font-weight']};
}
.thumb-title,
.navbar-default .navbar-nav,
.navbar-default .navbar-nav .dropdown-menu {
    font-family: {$options['menu_font-family']['font']};
    font-size: {$options['menu_font-size']};
    font-weight: {$options['menu_font-weight']};
    text-transform: {$options['menu_text-transform']};
}
.carousel-inline .carousel-caption .slide-title{
    font-family: {$options['slide_title_font-family']['font']};
    font-size: {$options['slide_title_font-size']};
    text-transform: {$options['slide_title_text-transform']};
}
.carousel-inline .carousel-caption .slide-caption{
    font-family: {$options['slide_caption_font-family']['font']};
    font-size: {$options['slide_caption_font-size']};
}

/* Custom CSS */
{$custom_css}
  ";

  wp_enqueue_style('sage_css', asset_path('styles/main.css'), false, null);
  wp_add_inline_style( 'sage_css', $styles );

  if (is_single() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }

  wp_enqueue_script('modernizr', asset_path('scripts/modernizr.js'), [], null, true);
  wp_enqueue_script('sage_js', asset_path('scripts/main.js'), ['jquery'], null, true);
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 100);
