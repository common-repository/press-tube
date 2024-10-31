/*!
* Package: press-tube - v0.0.3 
* Description: The easiest way to integrate YouTube in your WordPress site, with many functionality. 
* Last build: 2017-04-03 
* @author codekraft-studio 
* @license GPL2 
*/
(function ($) {
  'use strict';

  if( $('.press-tube-widget').length ) {

    $('.press-tube-widget.video-results').each(function() {

      var videosContainer = $(this);

      if( videosContainer.hasClass('normal') ) {

      } else if( videosContainer.hasClass('slider') ) {
        videosContainer.slick();
      }

    });

  }

  if( $('.playlist-container').length ) {

    // Replace youtube video id from embed url
    var replaceVideoId = function(url, id) {
      return url.replace(/(\/)([^"&?/ ]{11})(\/|\?.*|$)/, function(a, b, c, d) {
        return b + id + d;
      });
    };

    $('.playlist-container').each(function() {

      var playlistContainer = $(this);
      var playlistMainFrame = playlistContainer.find('.playlist-frame');

      playlistContainer.find('.playlist-item').click(function(e) {
        e.preventDefault();

        var currentUrl = playlistMainFrame.find('iframe').attr('src');
        var videoId = $(this).data('video-id');

        $(this).addClass('active').siblings().removeClass('active');

        playlistMainFrame.find('iframe').attr('src', replaceVideoId(currentUrl, videoId) );

      });

    });

  }

  // Check if any shortcode exist since the class is by default
  // in all the shortcodes to identify the appartenance with the plugin
  if( $('.press-tube-shortcode').length ) {

    // For each plugin shortcode present in the page do various tasks
    $('.press-tube-shortcode').each(function() {

      // Store the shortcode element istance
      var shortcode = $(this);

      // Check if is a playlist shortcode
      if( shortcode.hasClass('playlist') ) {

        // When in list mode
        if( shortcode.find('.playlist-content.list').length ) { }

        // When in slider mode (do slick)
        if( shortcode.find('.playlist-content.slider').length ) {

          // init the slick plugin on the slider playlist items container
          shortcode.find('.playlist-items-container').slick({
            slidesToShow: 3,
            slidesToScroll: 3,
            infinite: false // loop to false so is clear when the playlist is finished
          });

        }

        // When in gallery mode
        if( shortcode.find('.playlist-content.gallery').length ) {

          // the playlist item item produce a smooth animation to the top
          // when is clicked since the video want to be seen
          shortcode.find('.playlist-item').click(function() {

            $('html, body').animate({
              scrollTop: shortcode.offset().top - 20
            }, 1000);

          });

        }

      }

    });

  }

})(jQuery);
