<?php

namespace Roots\Sage\Media;

use Roots\Sage\Options;

/**
 * Remove image attributes
 */
add_filter( 'post_thumbnail_html', __NAMESPACE__ . '\\remove_thumbnail_dimensions', 10 );
add_filter( 'image_send_to_editor', __NAMESPACE__ . '\\remove_thumbnail_dimensions', 10 );
add_filter( 'the_content', __NAMESPACE__ . '\\remove_thumbnail_dimensions', 10 );

function remove_thumbnail_dimensions( $html ) {
    $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
    return $html;
}

/** Register the html5 figure-non-responsive code fix. */
add_filter( 'img_caption_shortcode', __NAMESPACE__ . '\\myfix_img_caption_shortcode_filter', 10, 3 );

function myfix_img_caption_shortcode_filter($dummy, $attr, $content) {
  $atts = shortcode_atts( array(
      'id'      => '',
      'align'   => 'alignnone',
      'width'   => '',
      'caption' => '',
      'class'   => '',
  ), $attr, 'caption' );

  $atts['width'] = (int) $atts['width'];
  if ( $atts['width'] < 1 || empty( $atts['caption'] ) )
      return $content;

  if ( ! empty( $atts['id'] ) )
      $atts['id'] = 'id="' . esc_attr( $atts['id'] ) . '" ';

  $class = trim( 'wp-caption ' . $atts['align'] . ' ' . $atts['class'] );

  if ( current_theme_supports( 'html5', 'caption' ) ) {
      return '<figure ' . $atts['id'] . 'style="max-width: ' . (int) $atts['width'] . 'px;" class="' . esc_attr( $class ) . '">'
      . do_shortcode( $content ) . '<figcaption class="wp-caption-text">' . $atts['caption'] . '</figcaption></figure>';
  }

  // Return nothing to allow for default behaviour!!!
  return '';
}

/**
 *  Favicon
 */
add_action('wp_head', __NAMESPACE__ . '\\site_favicon');
function site_favicon() {
    $options = Options\get_options();
    $favicon = $options['favicon'] ? $options['favicon'] : get_template_directory_uri().'/favicon.ico';
	echo '<link rel="shortcut icon" href="'.$favicon.'">';
}

/**
 *  Allow upload SVG
 */
function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', __NAMESPACE__ . '\\cc_mime_types');

