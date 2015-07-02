<?php

namespace Roots\Sage\Options;

/**
 * Theme Options
 */
function get_options() {
  static $options;

  if (!isset($options)) {
    $options = function_exists('get_field') ? get_fields('options') : false;
  }
  return $options;
}
