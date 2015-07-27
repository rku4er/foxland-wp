<?php

namespace Roots\Sage\Shortcodes;

//use Roots\Sage\Config;
//  if (Config\display_sidebar()) {
//
//  }
/**
 * Fullscreen slider shortcode
 */
add_shortcode( 'slider', __NAMESPACE__.'\\slider_init' );
function slider_init( $attr ){
    extract(
        shortcode_atts( array(
            "name"       => __("Name", 'sage'),
            "animation"  => 'fade',
            "interval"   => 5000,
            "pause"      => 'hover',
            "wrap"       => true,
            "keyboard"   => true,
            "arrows"     => true,
            "bullets"    => true,
            "fullscreen" => false,
            "parallax"   => true,
        ), $attr )
    );


    if( isset($GLOBALS['carousel_count']) )
      $GLOBALS['carousel_count']++;
    else
      $GLOBALS['carousel_count'] = 0;

    $sliders = function_exists('get_field') ? get_field('sliders', 'options') : false;

    if($sliders) {

        $i = -1;

        foreach($sliders as $slider):
            $i++;

            if($name === $slider['slider_name']) {

                $defaults    = $slider['slider_defaults'][0];
                $animation   = $defaults['animation'];
                $interval    = $defaults['interval'] * 1000;
                $pause       = $defaults['pause'];
                $wrap        = $defaults['wrap'];
                $keyboard    = $defaults['keyboard'];
                $arrows      = $defaults['arrows'];
                $bullets     = $defaults['bullets'];
                $fullscreen  = $defaults['fullscreen'];
                $parallax    = $defaults['parallax'];

                $div_class   = 'row carousel carousel-inline' . (($animation === 'fade') ? ' slide carousel-fade' : ' slide') . ($fullscreen ? ' fullscreen' : '');
                $inner_class = 'carousel-inner';
                $id          = 'custom-carousel-'. $GLOBALS['carousel_count'];

                if( is_array($slider['slides']) ) {

                    $indicators = array();
                    $items = array();

                    $i = -1;

                    foreach($slider['slides'] as $slide):
                        $i++;

                        $active_class = ($i == 0) ? ' active' : '';
                        $image_obj = wp_get_attachment_image_src($slide['image'], 'slider');
                        $image_original = preg_replace("/-\d+x\d+/", "$2", $image_obj[0]);;
                        $slide_url = $slide['url'] ? $slide['url'] : '';

                        $image = sprintf(
                            '%s<img src="%s" alt="" %s>%s',
                            $slide_url ? '<a href="'.$slide_url.'" target="_blank" >' : '',
                                $image_obj[0],
                                $slide_url ? '' : 'data-run="intense" data-image="'.$image_original.'"',
                            $slide_url ? '</a>' : ''
                        );

                        $background = sprintf(
                            'background-image: url(%s); background-attachment: %s;',
                            $image_obj[0],
                            $parallax ? 'fixed' : 'scroll'
                        );

                        if($slide['title_text']){
                            $anim_title = $slide['title_animation'] ? 'animated '
                                . $slide['title_animation'] : '';
                            $title_style = '
                                color: '. $slide['title_color'] .';
                                animation-delay: '. $slide['title_animation_delay'] .'s;
                                animation-duration: '. $slide['title_animation_duration'] .'s;
                            ';
                            $title_html = '<h3 class="slide-title" data-animation="'. $anim_title .'" style="'
                                . $title_style .'">'
                                . $slide['title_text'] . '</h3>';
                        }

                        if($slide['caption_text']){
                            $anim_caption = $slide['caption_animation'] ? 'animated '
                                . $slide['caption_animation'] : '';
                            $caption_style = '
                                color: '. $slide['caption_color'] .';
                                animation-delay: '. $slide['caption_animation_delay'] .'s;
                                animation-duration: '. $slide['caption_animation_duration'] .'s;
                            ';
                            $caption_html = '<div class="slide-caption hidden-xs" data-animation="'. $anim_caption .'" style="'
                                . $caption_style .'"><p>'
                                . $slide['caption_text'] . '</p></div>';
                        }

                        if($slide['caption']){
                            $caption = sprintf(
                                '<div class="carousel-caption container %s %s"><div><div>%s%s</div></div></div>',
                                $slide['align'] ? 'align-'.$slide['align'] : 'align-center',
                                $slide['vertical_align'] ? 'valign-'.$slide['vertical_align'] : 'valign-bottom',
                                $slide['title_text'] ? $title_html : '',
                                $slide['caption_text'] ? $caption_html : ''
                            );
                        }

                        $indicators[] = sprintf(
                          '<li class="%s" data-target="%s" data-slide-to="%s"></li>',
                          $active_class,
                          esc_attr( '#' . $id ),
                          esc_attr( $i )
                        );

                        $items[] = sprintf(
                          '<div class="%s" style="%s">%s%s</div>',
                          'item' . $active_class,
                          $background,
                          $image,
                          $caption
                        );
                    endforeach;

                    return sprintf(
                      '<div class="%s" id="%s" data-ride="carousel" %s%s%s%s>'
                          . '%s<div class="%s">%s</div>%s</div>',
                      esc_attr( $div_class ),
                      esc_attr( $id ),
                      ( $parallax )   ? sprintf( ' data-type="%s"', 'parallax' ) : '',
                      ( $interval )   ? sprintf( ' data-interval="%d"', $interval ) : '',
                      ( $pause )      ? sprintf( ' data-pause="%s"', esc_attr( $pause ) ) : '',
                      ( $wrap )       ? sprintf( ' data-wrap="%s"', esc_attr( $wrap ) ) : '',
                      ( $bullets ) ? '<ol class="carousel-indicators hidden-xs">' . implode( $indicators ) . '</ol>' : '',
                      esc_attr( $inner_class ),
                      implode($items),
                      ( $arrows ) ? sprintf( '%s%s',
                          '<a class="left carousel-control"  href="' . esc_url( '#' . $id ) . '" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>',
                          '<a class="right carousel-control" href="' . esc_url( '#' . $id ) . '" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>'
                      ) : ''
                    );

                };

            }

        endforeach;

    }
}

/**
 * Socials
 */

add_shortcode( 'socials', __NAMESPACE__.'\\socials_init' );
function socials_init( $attr ){
    extract( shortcode_atts( array(
        'label' => false
    ), $attr ));

    $socials = function_exists('get_field') ? get_field('socials', 'options') : false;
    if($socials){
        $buffer = '<span class="socials">';
        $buffer .= $label ? '<span>'.$label.'</span>' : '';

        foreach( $socials as $social ){
            $buffer .= '<a href="'. $social['social_url'] . '" target="_blank"><i class="fa fa-'. strtolower($social['social_name']) .'"></i></a>';
        }
        $buffer .= '</span>';
        return $buffer;
    }
}

/**
 * Services
 */

add_shortcode( 'service', __NAMESPACE__.'\\service_init' );
function service_init( $attr ){
    extract( shortcode_atts( array(), $attr ));

    $s_title = function_exists('get_field') ? get_field('services_title', 'options') : false;
    $s_url = function_exists('get_field') ? get_field('services_url', 'options') : false;
    $s_image = function_exists('get_field') ? get_field('services_image', 'options') : false;

    $buffer = '' ;

    if($s_url){
        $buffer = '<div class="service" style="background-image: url('. $s_image .')">';
        $buffer .= '<h3>'. $s_title . '</h3>';
        $buffer .= '<a href="'. esc_url($s_url) . '" target="_blank" class="btn btn-custom">View Our Services</a>';
        $buffer .= '</div>';

        return $buffer;
    }
}


/**
  *
  * bs_tabs
  *
  * @author Filip Stefansson
  * @since 1.0
  * Modified by TwItCh twitch@designweapon.com
  * Now acts a whole nav/tab/pill shortcode solution!
  */
add_shortcode( 'tabs_vertical', __NAMESPACE__.'\\bs_tabs_vertical' );
function bs_tabs_vertical( $atts, $content = null ) {

  if( isset( $GLOBALS['tabs_count'] ) )
    $GLOBALS['tabs_count']++;
  else
    $GLOBALS['tabs_count'] = 0;

  $GLOBALS['tabs_default_count'] = 0;

  $atts = shortcode_atts( array(
    "type"   => false,
    "xclass" => false,
    "data"   => false
  ), $atts );

  $ul_class  = 'nav';
  $ul_class .= ( $atts['type'] )     ? ' nav-' . $atts['type'] : ' nav-tabs';
  $ul_class .= ( $atts['xclass'] )   ? ' ' . $atts['xclass'] : '';
  $ul_class .= ' tabs-vertical tabs-left';

  $div_class = 'tab-content';

  $id = 'custom-tabs-'. $GLOBALS['tabs_count'];

  $data_props = $atts['data'];

  $atts_map = bs_attribute_map( $content );

  // Extract the tab titles for use in the tab widget.
  if ( $atts_map ) {
    $tabs = array();
    $GLOBALS['tabs_default_active'] = true;
    foreach( $atts_map as $check ) {
        if( !empty($check["tab"]["active"]) ) {
            $GLOBALS['tabs_default_active'] = false;
        }
    }
    $i = 0;
    foreach( $atts_map as $tab ) {

      $class  ='';
      $class .= ( !empty($tab["tab"]["active"]) || ($GLOBALS['tabs_default_active'] && $i == 0) ) ? 'active' : '';
      $class .= ( !empty($tab["tab"]["xclass"]) ) ? ' ' . $tab["tab"]["xclass"] : '';

      $tabs[] = sprintf(
        '<li%s><a href="#%s" data-toggle="tab">%s</a></li>',
        ( !empty($class) ) ? ' class="' . $class . '"' : '',
        'custom-tab-' . $GLOBALS['tabs_count'] . '-' . md5($tab["tab"]["title"]),
        $tab["tab"]["title"]
      );
      $i++;
    }
  }
  return sprintf(
    '<div class="col-sm-3"><ul class="%s" id="%s"%s>%s</ul></div><div class="col-sm-9"><div class="%s">%s</div></div>',
    esc_attr( $ul_class ),
    esc_attr( $id ),
    ( $data_props ) ? ' ' . $data_props : '',
    ( $tabs )  ? implode( $tabs ) : '',
    esc_attr( $div_class ),
    do_shortcode( $content )
  );
}
