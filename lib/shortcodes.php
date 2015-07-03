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

                $div_class   = 'carousel carousel-inline' . (($animation === 'fade') ? ' slide carousel-fade' : ' slide') . ($fullscreen ? ' fullscreen' : '');
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

                        $image = sprintf(
                            '<img src="%s" alt="">',
                            $image_obj[0]
                        );

                        $background = sprintf(
                            'background-image: url(%s);',
                            $image_obj[0]
                        );

                        if($slide['title_text']){
                            $anim_title = $slide['title_animation'] ? 'animated '
                                . $slide['title_animation'] : '';
                            $title_style = '
                                font-family: '. $slide['title_font-family']['font'] .';
                                font-size: '. $slide['title_font-size'] .';
                                color: '. $slide['title_color'] .';
                                animation-delay: '. $slide['title_animation_delay'] .';
                                animation-duration: '. $slide['title_animation_duration'] .';
                            ';
                            $title_html = '<h3 data-animation="'. $anim_title .'" style="'
                                . $title_style .'">'
                                . $slide['title_text'] . '</h3>';
                        }

                        if($slide['caption_text']){
                            $anim_caption = $slide['caption_animation'] ? 'animated '
                                . $slide['caption_animation'] : '';
                            $caption_style = '
                                font-family: '. $slide['caption_font-family']['font'] .';
                                font-size: '. $slide['caption_font-size'] .';
                                color: '. $slide['caption_color'] .';
                                animation-delay: '. $slide['caption_animation_delay'] .';
                                animation-duration: '. $slide['caption_animation_duration'] .';
                            ';
                            $caption_html = '<div data-animation="'. $anim_caption .'" style="'
                                . $caption_style .'">'
                                . $slide['caption_text'] . '</div>';
                        }

                        if($slide['more_options']){
                            $caption = sprintf(
                                '<div class="carousel-caption %s %s"><div><div>%s%s</div></div></div>',
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
                          '<div class="%s">%s%s</div>',
                          'item' . $active_class,
                          $image,
                          $caption
                        );
                    endforeach;

                    return sprintf(
                      '<div class="%s" id="%s" data-ride="carousel"%s%s%s>'
                          . '%s<div class="%s">%s</div>%s</div>',
                      esc_attr( $div_class ),
                      esc_attr( $id ),
                      ( $interval )   ? sprintf( ' data-interval="%d"', $interval ) : '',
                      ( $pause )      ? sprintf( ' data-pause="%s"', esc_attr( $pause ) ) : '',
                      ( $wrap )       ? sprintf( ' data-wrap="%s"', esc_attr( $wrap ) ) : '',
                      ( $bullets ) ? '<ol class="carousel-indicators">' . implode( $indicators ) . '</ol>' : '',
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

add_shortcode( 'services', __NAMESPACE__.'\\services_init' );
function services_init( $attr ){
    extract( shortcode_atts( array(), $attr ));

    $s_title = function_exists('get_field') ? get_field('services_title', 'options') : false;
    $s_url = function_exists('get_field') ? get_field('services_url', 'options') : false;
    $s_image = function_exists('get_field') ? get_field('services_image', 'options') : false;

    $buffer = '' ;

    if($s_url){
        $buffer = '<div class="services" style="background-image: url('. $s_image .')">';
        $buffer .= '<h3>'. $s_title . '</h3>';
        $buffer .= '<a href="'. esc_url($s_url) . '" target="_blank" class="btn btn-custom">View Our Services</a>';
        $buffer .= '</div>';

        return $buffer;
    }
}

