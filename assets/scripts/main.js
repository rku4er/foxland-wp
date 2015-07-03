/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can
 * always reference jQuery with $, even when in .noConflict() mode.
 * ======================================================================== */

(function($) {

  // Use this variable to set up the common and page specific functions. If you
  // rename this variable, you will also need to rename the namespace below.
  var Sage = {
    // All pages
    'common': {
      init: function() {
        // JavaScript to be fired on all pages
        var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent
        );
        var windowHeight = $(window).height();
        var windowWidth = $(window).width();


        //var map = null, markers = [], newMarkers = [], markerCluster = null, bounds = [], infobox = [];

            //var customIcon = new google.maps.MarkerImage(
                //'http://w.indiaondesk.com/selling-home-direct/wp-content/themes/realty/lib/images/map-marker/map-marker-green-fat.png',
                //null, // size is determined at runtime
              //null, // origin is 0,0
              //null, // anchor is bottom center of the scaled image
              //new google.maps.Size(50, 69)
            //);
                    //var markerClusterOptions = {
                //gridSize: 60, // Default: 60
                //maxZoom: 14,
                //styles: [{
                    //width: 50,
                    //height: 50,
                    //url: "http://w.indiaondesk.com/selling-home-direct/wp-content/themes/realty/lib/images/map-marker/map-marker-red-round.png"
                //}]
            //};

        //var mapOptions = {
            //center: new google.maps.LatLng(0, 0),
            //zoom: 14,
            //scrollwheel: false,
            //streetViewControl: true,
            //disableDefaultUI: true,
        //};

        //google.maps.event.addDomListener(window, 'load', initMap);

        $(window).load(function(){
            $('.navbar-fixed-top').css({
                'margin-top' : $('html').css('margin-top')
            });

            $('body').addClass('loaded');

            $('.carousel').each(function() {
                var $myCarousel = $(this),
                    $firstAnimatingElems = $myCarousel.find('.item:first').find("[data-animation ^= 'animated']");

                //Initialize carousel
                //$myCarousel.carousel();

                //Animate captions in first slide on page load
                doAnimations($firstAnimatingElems);

                //Pause carousel
                $myCarousel.carousel('pause');

                //Other slides to be animated on carousel slide event
                $myCarousel.on('slide.bs.carousel', function(e) {
                    var $animatingElems = $(e.relatedTarget).find("[data-animation ^= 'animated']");
                    doAnimations($animatingElems);
                });
            });

            $('.bs-tooltip').tooltip();

            $("[data-toggle=popover]")
              .on('click', function(e) {e.preventDefault(); return true;})
              .popover();

            new Intense($('img'));

            $('.datepicker').datepicker({
                language:   'en',
                autoclose:  true,
                format: "yyyymmdd"
            });

            $('select').chosen({
                width: "100%",
                search_contains: true,
                //disable_search: true,
                disable_search_threshold: 5
            });

            $('#main').fitVids();

            $('.video-lightbox').magnificPopup({
                type: 'iframe'
            });

            $('.image-gallery-lightbox').magnificPopup({
                type:       'image',
                gallery:    {
                    enabled:    true,
                    tPrev:      '',
                    tNext:      '',
                    tCounter: '%curr% | %total%'
                }
            });

            if ( $('#price-range').length ) {

                var priceFormat;
                $('#price-range').noUiSlider({

                    start: [ 0, 100000 ],
                    step: 10000,
                    range: {
                        'min': [  0 ],
                        'max': [  100000 ]
                    },
                    format: wNumb({
                        decimals: 0,
                        thousand: ',',prefix: '$',  }),
                    connect: true,

                });

                priceFormat = wNumb({
                    decimals: 0,
                    thousand: ',',prefix: '$',});

                $('#price-range').Link('lower').to($('#price-range-min'));
                $('#price-range').Link('upper').to($('#price-range-max'));

            }

            //// Fire Search Results Ajax On Search Field Change (Exclude Datepicker)
            //$('#price-range, .property-search-form select, .property-search-form input').not('.datepicker').change(function() {
                //tt_ajax_search_results();
                //if ( $('#google-map').length > 0 ) {
                    //removeMarkers();
                //}
            //});

            //// Fire Search Results Ajax On Search Field "Datepicker" Change
            //$('.property-search-form input.datepicker').on('changeDate', function() {
                //tt_ajax_search_results();
                //if ( $('#google-map').length > 0 ) {
                    //removeMarkers();
                //}
            //});
        });

        // AJAX
        function tt_ajax_search_results() {

            if ( $('#price-range').length ) {

                var price_range, min_price, max_price;

                price_range         =   $('#price-range').val();
                min_price           =   priceFormat.from( price_range[0] );
                max_price           =   priceFormat.from( price_range[1] );

                $('#property-search-price-range-min').val(min_price);
                $('#property-search-price-range-max').val(max_price);

            }

            if ( $('.property-search-feature') ) {
                var feature = [];
                $('.property-search-feature:checked').each(function() {
                  feature.push( $(this).val() );
                });
            }

            var ajaxData = $('.property-search-form').first().serialize() + "&action=tt_ajax_search&base=" + window.location.pathname;

            $.ajax({

              type: 'GET',
              url: ajaxURL,
              data: ajaxData,
              success: function (response) {
                $('#property-items').html(response); // Show response from function tt_ajax_search()
              },
              error: function () {
                console.log( 'failed' );
              }

            });

        }

        // Remove Map Markers & Marker Cluster
        function removeMarkers() {
            // http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/examples/speed_test.js
          for( i = 0; i < newMarkers.length; i++ ) {
            newMarkers[i].setMap(null);
                // Close Infoboxes
            if ( newMarkers[i].infobox.getVisible() ) {
                newMarkers[i].infobox.hide();
            }
          }
          if ( markerCluster ) {
            markerCluster.clearMarkers();
          }
          markers = [];
          newMarkers = [];
          bounds = [];
        }

        //Function to animate slider captions
        function doAnimations(elems) {
            //Cache the animationend event in a variable
            var animEndEv = 'webkitAnimationEnd animationend';

            elems.each(function() {
                var $this = $(this),
                    $animationType = $this.data('animation');
                $this.addClass($animationType).one(animEndEv, function() {
                    $this.removeClass($animationType);
                });
            });
        }

        //google map initialization
        function initMap() {

            map = new google.maps.Map(document.getElementById("google-map"), mapOptions);

            bounds = new google.maps.LatLngBounds();

            markers = initMarkers(map, [ { permalink:"http://w.indiaondesk.com/selling-home-direct/property/property-submit/", title:"Property Submit", price:"", latLng: new google.maps.LatLng(23.0509388,  72.58586300000002), thumbnail: "//placehold.it/300x100/eee/ccc/&text=.." },
                { permalink:"http://w.indiaondesk.com/selling-home-direct/property/lush-villa/", title:"Lush Villa", price:"$12,000<span>/month</span>", latLng: new google.maps.LatLng(51.48623550000001, -0.16945559999999205), thumbnail: "http://w.indiaondesk.com/selling-home-direct/wp-content/uploads/2014/07/unsplash_mansion-300x205.jpg" },
                { permalink:"http://w.indiaondesk.com/selling-home-direct/property/for-the-minimalist/", title:"For The Minimalist", price:"$290,000", latLng: new google.maps.LatLng(51.5153445, -0.141115500000069), thumbnail: "http://w.indiaondesk.com/selling-home-direct/wp-content/uploads/2014/07/picjumbo_house_front-300x200.jpg" },
                { permalink:"http://w.indiaondesk.com/selling-home-direct/property/loft-above-the-city/", title:"Loft Above The City", price:"$2,400<span>/week</span>", latLng: new google.maps.LatLng(51.5018318, -0.11682380000002013), thumbnail: "http://w.indiaondesk.com/selling-home-direct/wp-content/uploads/2014/07/unsplash_houses_narrow-300x183.jpg" },
                { permalink:"http://w.indiaondesk.com/selling-home-direct/property/state-of-the-art/", title:"State of The Art", price:"$2,250<span>/week</span>", latLng: new google.maps.LatLng(51.5100995, -0.1350800000000163), thumbnail: "http://w.indiaondesk.com/selling-home-direct/wp-content/uploads/2014/07/unsplash_house_top-300x200.jpg" },
                { permalink:"http://w.indiaondesk.com/selling-home-direct/property/modern-office/", title:"Modern Office", price:"$850,000", latLng: new google.maps.LatLng(48.8526377, 2.2619350999999597), thumbnail: "http://w.indiaondesk.com/selling-home-direct/wp-content/uploads/2014/07/picjumbo_office1-300x201.jpg" },
                { permalink:"http://w.indiaondesk.com/selling-home-direct/property/central-with-garden/", title:"Central With Garden", price:"$2,400,000", latLng: new google.maps.LatLng(51.4956918, -0.12703690000000734), thumbnail: "http://w.indiaondesk.com/selling-home-direct/wp-content/uploads/2014/07/unsplash_house_trees-300x162.jpg" },
                { permalink:"http://w.indiaondesk.com/selling-home-direct/property/apartment-on-stamford/", title:"Apartment On Stamford", price:"$3,000<span>/month</span>", latLng: new google.maps.LatLng(51.50648349999999, -0.10942760000000362), thumbnail: "http://w.indiaondesk.com/selling-home-direct/wp-content/uploads/2014/07/unsplash_rooftops-300x198.jpg" },
                { permalink:"http://w.indiaondesk.com/selling-home-direct/property/spacious-bright-mansion/", title:"Spacious &#038; Bright Mansion", price:"$12,000,000", latLng: new google.maps.LatLng(51.5000211, -0.15626220000001467), thumbnail: "http://w.indiaondesk.com/selling-home-direct/wp-content/uploads/2014/07/unsplash_castle-300x200.jpg" },
                { permalink:"http://w.indiaondesk.com/selling-home-direct/property/living-the-dream/", title:"Living The Dream", price:"$7,500<span>/week</span>", latLng: new google.maps.LatLng(51.5069927, -0.13605989999996382), thumbnail: "http://w.indiaondesk.com/selling-home-direct/wp-content/uploads/2013/07/picjumbo_dubai_skyscraper-300x225.jpg" },
                { permalink:"http://w.indiaondesk.com/selling-home-direct/property/city-of-london-office/", title:"City Of London Office", price:"$4,500<span>/month</span>", latLng: new google.maps.LatLng(51.5126397, -0.09013279999999213), thumbnail: "http://w.indiaondesk.com/selling-home-direct/wp-content/uploads/2014/07/picjumbo_office2-300x225.jpg" },
                { permalink:"http://w.indiaondesk.com/selling-home-direct/property/2bhk/", title:"2BHK", price:"$3,200,000", latLng: new google.maps.LatLng(48.8576463, 2.2735946999999896), thumbnail: "http://w.indiaondesk.com/selling-home-direct/wp-content/uploads/2014/07/unsplash_house_field-300x188.jpg" },
                { permalink:"http://w.indiaondesk.com/selling-home-direct/property/westminster-beauty/", title:"Westminster Beauty", price:"$4,500,000", latLng: new google.maps.LatLng(51.5007153, -0.13608049999993455), thumbnail: "http://w.indiaondesk.com/selling-home-direct/wp-content/uploads/2014/07/picjumbo_house_street-300x200.jpg" },
            ]);

            markerCluster = new MarkerClusterer(map, newMarkers, markerClusterOptions);

            // Maps Fully Loaded: Hide + Remove Spinner
            google.maps.event.addListenerOnce(map, 'idle', function() {
                $('.spinner').fadeTo(800, 0.5);
                setTimeout(function() {
                  $('.spinner').remove();
                }, 800);
            });

            // Spiderfier
            var oms = new OverlappingMarkerSpiderfier(map, { markersWontMove: true, markersWontHide: true, keepSpiderfied: true, legWeight: 5 });

            function omsMarkers( markers ) {
              for ( var i = 0; i < markers.length; i++ ) {
                oms.addMarker( markers[i] );
              }
            }

            omsMarkers(markers);

        }

        function initMarkers(map, markerData) {

            markerData.map(function(markedDataItem){

                marker = new google.maps.Marker({
                    map: map,
                    position: markerData[i].latLng,
                        icon: customIcon,
                    //animation: google.maps.Animation.DROP
                });

                bounds.extend(markerData[i].latLng);

                infoboxOptions = {
                    content:'<div class="map-marker-wrapper">'+
                                '<div class="map-marker-container">'+
                                    '<div class="arrow-down"></div>'+
                                        '<img src="'+markerData[i].thumbnail+'" />'+
                                        '<div class="content">'+
                                        '<a href="'+markerData[i].permalink+'">'+
                                        '<h5 class="title">'+markerData[i].title+'</h5>'+
                                        '</a>'+
                                        markerData[i].price+
                                    '</div>'+
                                '</div>'+
                            '</div>',
                    disableAutoPan: false,
                    pixelOffset: new google.maps.Size(-33, -90),
                    zIndex: null,
                    isHidden: true,
                    alignBottom: true,
                    closeBoxURL: "http://w.indiaondesk.com/selling-home-direct/wp-content/themes/realty/lib/images/close.png",
                    infoBoxClearance: new google.maps.Size(25, 25)
                };

                newMarkers.push(marker);

                newMarkers[i].infobox = new InfoBox(infoboxOptions);
                newMarkers[i].infobox.open(map, marker);

                google.maps.event.addListener(marker, 'click', (function(marker, i) {

                    return function() {

                        if ( newMarkers[i].infobox.getVisible() ) {
                            newMarkers[i].infobox.setVisible(false);
                        }
                        else {
                            $('.infoBox').hide();
                            newMarkers[i].infobox.setVisible(true);
                        }

                        newMarkers[i].infobox.open(map, this);
                        map.panTo(markerData[i].latLng);

                    };

                })( marker, i ) );

                google.maps.event.addListener(map, 'click', function() {
                    $('.infoBox').hide();
                });

            });

            // Set Map Bounds And Max. Zoom Level
            google.maps.event.addListenerOnce(map, 'bounds_changed', function(event) {
                map.fitBounds(bounds);
                    if (this.getZoom() > 13) {
                    this.setZoom(13);
                  }
            });

            return newMarkers;

        } // initMarkers();

      },
      finalize: function() {
        // JavaScript to be fired on all pages, after page specific JS is fired
      }
    },
    // Home page
    'home': {
      init: function() {
        // JavaScript to be fired on the home page
      },
      finalize: function() {
        // JavaScript to be fired on the home page, after the init JS
      }
    },
    // About us page, note the change from about-us to about_us.
    'about_us': {
      init: function() {
        // JavaScript to be fired on the about us page
      }
    }
  };

  // The routing fires all common scripts, followed by the page specific scripts.
  // Add additional events for more control over timing e.g. a finalize event
  var UTIL = {
    fire: function(func, funcname, args) {
      var fire;
      var namespace = Sage;
      funcname = (funcname === undefined) ? 'init' : funcname;
      fire = func !== '';
      fire = fire && namespace[func];
      fire = fire && typeof namespace[func][funcname] === 'function';

      if (fire) {
        namespace[func][funcname](args);
      }
    },
    loadEvents: function() {
      // Fire common init JS
      UTIL.fire('common');

      // Fire page-specific init JS, and then finalize JS
      $.each(document.body.className.replace(/-/g, '_').split(/\s+/), function(i, classnm) {
        UTIL.fire(classnm);
        UTIL.fire(classnm, 'finalize');
      });

      // Fire common finalize JS
      UTIL.fire('common', 'finalize');
    }
  };

  // Load Events
  $(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.
