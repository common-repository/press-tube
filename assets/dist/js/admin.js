/*!
* Package: press-tube - v0.0.3 
* Description: The easiest way to integrate YouTube in your WordPress site, with many functionality. 
* Last build: 2017-04-03 
* @author codekraft-studio 
* @license GPL2 
*/
(function ($) {
  'use strict';

  $(document).ready(function() {

    var settings = WPTUBE;
    var tubeFrame = null;

    var notifyTemplate =  $.templates(
      '<div id="message" class="notice {{:type}}">' +
        '<p>{{:message}}</p>' +
      '</div>'
    );

    // The ajax error message template
    var errorTemplate = $.templates(
      '<div id="message" class="notice notice-error">' +
        '<p>{{:errorMessage}}</p>' +
      '</div>'
    );

    // In all pages where there is a button but not
    // in the playlist post type page
    if( !$('#wt-playlist').length ) {

      var selectCallback = function(video) {

        // IF tinymce is enabled add frame to it
        // as raw html element
        if( tinymce && tinymce.activeEditor ) {
          window.parent.send_to_editor( video.iframe );
        }

      };

      // On main youtube action button init the
      // video selection frame
      $('#wt-open-frame').click(function(e) {
        e.preventDefault();
        // init new frame with options
        if( !tubeFrame ) {
          tubeFrame = new window.WPTubeFrame({
            key: settings.key,
            restrict: ['video', 'playlist'],
            onSelect: selectCallback
          });
        }
        // Open the frame
        tubeFrame.open();
      });

    }

    // Backend stuff on option page
    if( $('.wp-tube-wrap').length ) {

      if( $('#playlists').length ) {

        var options = {
          valueNames: [ 'title', 'count' ],
          page: 5,
          pagination: true
        };

        var userList = new List('playlists', options);

      }

    }

    // Only on page post type
    if( $('#wt-page').length ) {

      // on page playlist select change update the view link nearby
      $('#wt-playlist-select').change(function(e) {

        // get the playlist id value
        var value = $(this).val();

        if( value === '' ) {
          // hide since no playlist is selected
          $('#wt-playlist-link').hide();
          $('.options-secondary').hide();
        } else {
          // Change url and show
          $('#wt-playlist-link').attr('href', 'https://www.youtube.com/playlist?list=' + value).show();
          $('.options-secondary').show();
        }

      });

      // the shortcode result text
      var shortCodeResult = $('div.shortcode').find('.shortcode-result');

      // The form with the shortcode options
      var shortCodeOptions = $('div.shortcode').find('.shortcode-options');

      var shortcodeActions = $('div.shortcode').find('.shortcode-actions');

      // Add shortcode text to tinymce on click
      shortcodeActions.find('.shortcode-add').click(function(e) {
        e.preventDefault();
        // IF tinymce is enabled add frame to it
        // as raw html element
        if( tinymce && tinymce.activeEditor ) {
          window.parent.send_to_editor( shortCodeResult.find('code').text() );
        }
      });

      // On input fields change reload the shortcode
      shortCodeOptions.find(':input').change(function(e) {
        return renderShortcode( getShortCodeOptionString() );
      });

      /**
       * Get the options array and join it as string formatted for the shortcode
       * @method getShortCodeOptionString
       * @return {string} The shortcode options string formatted as html attribute
       */
      var getShortCodeOptionString = function() {
        return getShortCodeOptionArray().join(" ");
      };

      /**
       * Get the shortcode options array
       * @method getShortCodeOptionArray
       * @return {array} The shortcode options array from the form
       */
      var getShortCodeOptionArray = function() {

        // get only options that are not empty
        return shortCodeOptions.find(':input').serializeArray().filter(function(object) {
          return object.value;
        }).map(function(object) {
          return object.name + '="' + object.value + '"';
        });

      };

      /**
       * Render the shortcode text with the given options
       * @method renderShortcode
       * @param {string} options The shortcode options string formatted as html attribute
       * @return {string} The final shortcode text to use in frontend
       */
      var renderShortcode = function(options) {
        // Generate the output text
        var output = '[playlist ' + options + '][/playlist]';
        // Update the shortode result text
        shortCodeResult.find('code').text( output );
      };

      // Render the initial playlist shortcode
      renderShortcode( getShortCodeOptionString() );

    }

  });

})(jQuery);

(function ($) {
  'use strict';

  // Case insensitive contains function (new selector)
  jQuery.expr[':'].Contains = function(a, i, m) {
    return jQuery(a).text().toUpperCase()
        .indexOf(m[3].toUpperCase()) >= 0;
  };

  // Case insensitive contains function (old selector)
  jQuery.expr[':'].contains = function(a, i, m) {
    return jQuery(a).text().toUpperCase()
        .indexOf(m[3].toUpperCase()) >= 0;
  };

  $(document).ready(function() {

    // We are in the playlist post type page
    if( $('#wt-playlist').length ) {

      // get the total item count to avoid dupes on index
      var totalItems = $('.playlist-item').length;

      // the playlist item template to show on playlist items container
      var playlistItemTemplate = $.templates(
        '<div class="playlist-item unselectable">' +

          '<div class="handle">{{:index}}</div>' +

          '<div class="playlist-thumb">' +
            '<img src="{{:thumbnail}}" alt="{{:title}}">' +
          '</div>' +

          '<div class="playlist-details">' +

            '<div class="playlist-title">' +
              '<h3>{{:title}}</h3>' +
              '<p><a class="video-channel-title" href="{{:channelUrl}}" target="_blank">{{:channelTitle}}</a> - <span class="video-date">{{:publishedAt}}</span></p>' +
            '</div>' +

            '<div class="playlist-footer"></div>' +

            '<div class="playlist-data hidden" style="display: none;">' +

              '<input type="hidden" name="wt_playlist_items[{{:index}}][id]" value="{{:id}}" />' +
              '<input type="hidden" name="wt_playlist_items[{{:index}}][title]" value="{{:title}}" />' +
              '<input type="hidden" name="wt_playlist_items[{{:index}}][description]" value="{{:description}}" />' +
              '<input type="hidden" name="wt_playlist_items[{{:index}}][thumbnail]" value="{{:thumbnail}}" />' +
              '<input type="hidden" name="wt_playlist_items[{{:index}}][publishedAt]" value="{{:publishedAt}}" />' +
              '<input type="hidden" name="wt_playlist_items[{{:index}}][channelUrl]" value="{{:channelUrl}}" />' +
              '<input type="hidden" name="wt_playlist_items[{{:index}}][channelTitle]" value="{{:channelTitle}}" />' +

            '</div>' +

          '</div>' +

          '<div class="playlist-item-remove"><span class="dashicons dashicons-no-alt"></span></div>' +

        '</div>'
      );

      var settings = WPTUBE;
      var tubeFrame = null;

      // Update the clientside sort index from 1 to end
      var updateSortIndex = function() {
        $('.playlist-items-container').find('.playlist-item').each(function(index, element) {
          $(element).find('.handle').text(++index);
        });
      };

      // On toolbar input change filter the playlist
      // items based on the input value
      $('.meta-toolbar-search input').keyup(function(e) {
        var val = $(this).val();
        $('.playlist-item:not(:contains(' + val + '))').hide();
        $('.playlist-item:contains(' + val + ')').show();
      });

      // Init the sortable functions on update
      // reset the index numbers on the handles
      $('.playlist-items-container').sortable({
        handle: ".handle",
        update: updateSortIndex
      });

      // Bind remove function to X button
      $('.playlist-items-container').find('.playlist-item-remove').click(function(e) {
        e.preventDefault();
        $(this).parent().remove();
      });

      // The callback to add new video to playlist items
      var addToPlaylist = function(video) {

        var playlistItem = {
          id: video.id.videoId,
          index: totalItems++, // to avoid dupes the index is always more than the last original index
          title: video.snippet.title,
          description: video.snippet.description,
          videoUrl: 'https://www.youtube.com/watch?v=' + video.id.videoId,
          channelUrl: 'https://www.youtube.com/channel/' + video.snippet.channelId,
          channelTitle: video.snippet.channelTitle,
          publishedAt: video.snippet.publishedAt,
          channelId: video.snippet.channelId,
          thumbnail: video.snippet.thumbnails.high.url
        };

        // Create the video element
        var videoElement = $( playlistItemTemplate.render(playlistItem) );

        // Bind remove function to X button
        videoElement.find('.playlist-item-remove').click(function(e) {
          e.preventDefault();
          $(this).parent().remove();
        });

        // Append to the container and show it in the view
        $('.playlist-items-container').append(videoElement);

        // Update the sort index to match current items
        updateSortIndex();

      };

      // On main youtube action button init the
      // video selection frame
      $('#wt-open-frame').click(function(e) {
        e.preventDefault();

        if( !tubeFrame ) {
          tubeFrame = new window.WPTubeFrame({
            key: settings.key,
            restrict: ['video'],
            embedOptions: false,
            onSelect: addToPlaylist
          });
        }

        // Open the frame
        tubeFrame.open();
      });

    }

    // If the playlist shortcode metabox exists
    if( $('#wt-playlist-shortcode').length ) {

      // Get the playlist id value from post id
      var playlistId = $('#post_ID').val();

      // the shortcode result text
      var shortCodeResult = $('#wt-playlist-shortcode').find('.shortcode-result');

      // The form with the shortcode options
      var shortCodeOptions = $('#wt-playlist-shortcode').find('.shortcode-options');

      // On input fields change reload the shortcode
      shortCodeOptions.find(':input').change(function(e) {
        return renderShortcode( getShortCodeOptionString() );
      });

      /**
       * Get the options array and join it as string formatted for the shortcode
       * @method getShortCodeOptionString
       * @return {string} The shortcode options string formatted as html attribute
       */
      var getShortCodeOptionString = function() {
        return getShortCodeOptionArray().join(" ");
      };

      /**
       * Get the shortcode options array
       * @method getShortCodeOptionArray
       * @return {array} The shortcode options array from the form
       */
      var getShortCodeOptionArray = function() {

        // get only options that are not empty
        return shortCodeOptions.serializeArray().filter(function(object) {
          return object.value;
        }).map(function(object) {
          return object.name + '="' + object.value + '"';
        });

      };

      /**
       * Render the shortcode text with the given options
       * @method renderShortcode
       * @param {string} options The shortcode options string formatted as html attribute
       * @return {string} The final shortcode text to use in frontend
       */
      var renderShortcode = function(options) {
        // Generate the output text
        var output = '[playlist ' + options + '][/playlist]';
        // Update the shortode result text
        shortCodeResult.find('code').text( output );
      };

      // Render the initial playlist shortcode
      renderShortcode( getShortCodeOptionString() );

    }

  });

})(jQuery);

(function ($) {
  'use strict';

  var WPTubeFrame = function(options) {

    // Exit if google api script is not loaded
    if( !gapi || !window.gapi ) {
      console.warn('Press Tube', 'The Google Api script was not loaded, impossible to start the video frame.');
      return;
    }

    var youtubeEmbedUrl = 'https://www.youtube.com/embed/';

    var self = this;

    // Extend default settings with the given one
    var settings = $.extend({
      key: '',
      maxResults: 10,
      restrict: null,
      embedOptions: true,
      onSelect: null,
      insertText: 'Insert into post'  // The text to display in the primary insert button
    }, options || {});

    /**
     * The video details box template generated with options
     */
    var videoDetailsTemplate = $.templates(
      '<h2>Video Details</h2>' +
      '<div class="attachment-info frame-wrapper">' +
        '<iframe id="wt-video-preview" width="250" height="200" src="{{:resultEmbed}}"></iframe>' +
      '</div>' +
      '<div class="attachment-data">' +
        '<h3><a href="{{:resultUrl}}" target="_blank">{{:resultTitle}}</a></h3>' +
        '<h4><a href="{{:channelUrl}}" target="_blank">{{:channelTitle}}</a><span> - {{:publishedAt}}</span></h4>' +
        '<p>{{:resultDescription}}</p>' +
      '</div>' +
      '{{if embedOptions}}' +
        '<div class="attachment-options">' +
          '<h3>Embed options</h3>' +
          '<form class="embed-options">' +
            '<p><label><input name="controls" type="checkbox" value="0" />Hide Controls</label></p>' +
            '<p><label><input name="fs" type="checkbox" value="0" />Hide Fullscreen</label></p>' +
            '<p><label><input name="loop" type="checkbox" value="1" />Enable loop</label></p>' +
            '<p><label><input name="modestbranding" type="checkbox" value="1" />Modest Branding</label></p>' +
            '<p><label><input name="rel" type="checkbox" value="0" />Hide Relateds</label></p>' +
            '<p><label><input name="showinfo" type="checkbox" value="0" />Hide Info</label></p>' +
          '</form>' +
        '{{/if}}' +
      '</div>'
    );

    var dialogTemplate =  $.templates(
      '<div id="wt-frame">' +
        '<div class="video-modal wp-core-ui">' +
          '<button type="button" class="button-link media-modal-close"><span class="media-modal-icon"><span class="screen-reader-text">Close</span></span></button>' +
          '<div class="media-modal-content">' +

            '<div class="media-frame-title">' +
              '<h1>Insert Videos<span class="dashicons dashicons-arrow-down"></span></h1>' +
            '</div>' +

            '<div class="media-frame-router">' +

              '<div class="media-router">' +
                '<a href="#" data-value="video" class="media-menu-item video active">Videos</a>' +
                '<a href="#" data-value="playlist" class="media-menu-item playlist">Playlists</a> '+
                '<a href="#" data-value="channel" class="media-menu-item channel">Channels</a> '+
              '</div>' +

              '<a href="#!" class="expand-details" title="Toggle">Enlarge</a>' +
            '</div>' +
            '<div class="media-frame-content">' +
              '<div class="media-toolbar">' +
                '<div class="media-toolbar-search">' +
                  '<input id="wt-youtube-input" type="text" placeholder="Search something.." />' +
                  '<button id="wt-youtube-search"></button>' +
                '</div>' +
                '<div class="media-toolbar-view">' +
                  '<button class="button grid"><span class="wp-media-buttons-icon dashicons dashicons-grid-view"></button>' +
                  '<button class="button list active"><span class="wp-media-buttons-icon dashicons dashicons-list-view"></button>' +
                '</div>' +
              '</div>' +
              '<div id="wt-youtube-results" class="media-content list-view"></div>' +
              '<div class="media-sidebar">' +
                '<div class="attachment-details save-ready"></div>' +
              '</div>' +
            '</div>' +
            '<div class="media-frame-toolbar">' +
              '<div class="media-notify" style="display: none"><span class="dashicons dashicons-yes"></span>Video added</div>' +
              '<div class="media-actions">' +
                '<button id="wt-add-video" class="button media-button button-primary button-large" style="display: none">Insert into post</button>' +
              '</div>' +
            '</div>' +
            '<div class="media-frame mode-select wp-core-ui"></div>' +
          '</div>' +
        '</div>' +
        '<div class="video-modal-backdrop"></div>' +
      '</div>'
    );

    // The template for the search result item(s)
    var resultTemplate = $.templates(
      '<div class="video-result" data-result-id="{{:id}}">' +
        '<div class="video-container">' +
          '<div class="video-thumbnail"><img src="{{:thumbnail}}" alt="{{:title}}" /></div>' +
          '<div class="video-details">' +
            '<div class="video-btn">' +
              '<button class="video-add">ADD</button>' +
            '</div>' +
            '<div class="video-title">' +
              '<h3>{{:title}}</h3>' +
              '<p><span class="video-channel-title">{{:channelTitle}}</span> - <span class="video-date">{{:publishedAt}}</span></p>' +
            '</div>' +
            '<div class="video-description"><p>{{:description}}</p></div>' +
          '</div>' +
        '</div>' +
      '</div>'
    );

    /**
     * The frame template to embed into posts or contents
     */
    var frameTemplate = $.templates('<div class="frame-wrapper"><iframe width="{{:width}}" height="{{:height}}" src="{{:url}}" frameborder="0" allowfullscreen></iframe></div>');

    // Generate the dialog from template and hide it by default
    var $dialog = $( dialogTemplate.render({restrict: settings.restrict}) ).hide();

    var searchInputType = 'video';
    var addVideoButton = $dialog.find('#wt-add-video').hide();
    var searchInput = $dialog.find('#wt-youtube-input');
    var searchButton = $dialog.find( '#wt-youtube-search' );
    var searchResults = $dialog.find('#wt-youtube-results');
    var sidebar = $dialog.find('.media-sidebar');
    var videoPreviewFrame = $dialog.find('#wt-video-preview');

    // The array to hold current selected video(s)
    var currentVideo = null;

    // Flag to prevent multiple infinite scroll loadings
    var isLodingNextPage = false;

    // The search options (mainly for the last)
    var searchOptions = {};

    /**
     * Set the next page token to search options
     * @method addPageToken
     */
    var addPageToken = function(response) {

      // Store options for later use
      searchOptions = $.extend({
        pageToken: response.nextPageToken
      }, searchOptions);

    };

    /**
     * Build the watch url for a given id and type
     * @method buildWatchUrl
     */
    var buildWatchUrl = function(id, type) {
      var url;
      switch(type) {
        case 'video':
        url = 'https://www.youtube.com/watch?v=' + id;
        break;
        case 'playlist':
        url = 'https://www.youtube.com/playlist?list=' + id;
        break;
      }
      return url;
    };

    var selectCallback = function(video, options) {

      // Set the video to passed video or current
      // if somewhere else whas selected
      video = video ? video : currentVideo;

      if( video && settings.onSelect && $.isFunction(settings.onSelect) ) {

        var embedUrl = buildEmbedUrl( video, searchInputType, options );

        video = $.extend({}, {
          embedurl: embedUrl,
          watchurl: embedUrl,
          iframe: buildFrame(embedUrl)
        }, video || {});

        // Show the notify box
        $dialog.find('.media-notify').show().delay(2500).fadeOut();

        // return the default callback
        return settings.onSelect( video );

      }

    };

    // TODO: incomplete function get channeò details for channel tab
    var getChannelDetails = function(channel) {

      var request = gapi.client.youtube.channels.list({
        id: channel.id.channelId,
        part: 'contentDetails,statistics,status'
      });

      // Execute the api request
      request.execute(function(response) {
        console.log(response);
      });

    };

    var buildChannelUrl = function(id) {
      return 'https://www.youtube.com/channel/' + id;
    };

    var getResultDetails = function(result) {
      // Get the id from result
      var resultId = getTheId(result);
      // Get the result type
      var resultType = result.id.kind.replace('youtube#', '');
      // build the data object for the rendering view
      var data = {
        id: resultId,
        resultTitle: result.snippet.title,
        resultDescription: result.snippet.description,
        resultUrl: buildWatchUrl(resultId, resultType),
        resultEmbed: buildEmbedUrl( result, resultType, {
          fs: 0,
          rel: 0,
          showinfo: 0
        }),
        channelTitle: result.snippet.channelTitle,
        channelUrl: buildChannelUrl(result.snippet.channelId),
        publishedAt: new Date(Date.parse(result.snippet.publishedAt)).toUTCString(),
        embedOptions: settings.embedOptions
      };

      // if( resultType === 'playlist' ) {
      //
      //   var request = gapi.client.youtube.playlists.list({
      //     id: resultId,
      //     part: 'contentDetails'
      //   });
      //
      //   // Execute the api request
      //   request.execute(function(response) {
      //     console.log(response);
      //   });
      //
      // }

      // Render the video details template
      var videoDetails = $( videoDetailsTemplate.render(data) );
      // Clear the previous attachment details box and append new one
      $dialog.find('.attachment-details').empty().append(videoDetails);
      // Show if not already the add video button
      addVideoButton.show();
    };

    // Get search options from GUI inputs
    var getOptions = function() {

      var options = {
        q: searchInput.val(),
        part: 'snippet',
        type: searchInputType, // the current active tab determine the search type
        maxResults: 20 // Default static max results for each page
      };

      return options;

    };

    /**
     * Build embed url for different types of video element
     * with the given embed options as url params
     * @method buildEmbedUrl
     */
    var buildEmbedUrl = function(video, type, options) {

      // Extend or set default object
      options = options || {};

      var url = 'https://www.youtube.com/embed/';

      switch(type) {

        // In case of normal video simply add videoId at url
        case 'video':
        url = url + video.id.videoId;
          break;

        // if is a playlist set listType and list params
        case 'playlist':
        options.listType = 'playlist';
        options.list = video.id.playlistId;
          break;

        // TODO: In case of channel the format is: https://www.youtube.com/embed?listType=user_uploads&list=USERNAME
        // now we need to know how to get the USERNAME
        case 'channel':
        // options.listType = 'user_uploads';
          break;

      }

      // Build the embed options string
      var embedOptions = options ? $.param(options) : null;

      // Add embed options to url
      if( embedOptions ) {
        url += '?' + embedOptions;
      }

      return url;

    };

    /**
     * Build the frametemplate for current url
     * @method buildFrame
     */
    var buildFrame = function(url) {

      return frameTemplate.render({
        width: 560,
        height: 315,
        url: url
      });

    };

    /**
     * Populate the search results in the view
     * @method
     */
    var populateResults = function(results) {

      // init a empty elements array to hold results
      var elements = [];

      $.each(results, function(key, value) {

        // Get the result data to build the
        // frontend results with basic informations
        var data = {
          id: getTheId(value),
          title: value.snippet.title,
          description: value.snippet.description,
          thumbnail: value.snippet.thumbnails ? value.snippet.thumbnails.medium.url : '',
          channelTitle: value.snippet.channelTitle,
          publishedAt: new Date(Date.parse(value.snippet.publishedAt)).toUTCString()
        };

        // Create new element from template
        var element = $( resultTemplate.render(data) );

        // On video thumbnail click show the preview and the details
        element.find('.video-thumbnail, .video-title').click(function(e) {
          e.preventDefault();
          // Set the current video if
          // the user wants to watch it and add it later
          // is store in the plugin variables as the one active
          currentVideo = value;
          getResultDetails( value );
        });

        // Add the video to the post content
        element.find('.video-add').click(function(e) {
          e.preventDefault();
          // Do directly the callback on current video
          // without loading the preview and the details of it
          selectCallback(value);
        });

        // Push result element to array
        elements.push(element);

      });

      // Append all elements
      searchResults.append(elements);
    };

    var clearVideoPreview = function() {
      // Erase current video
      currentVideo = null;
      // Clear (if any) previous search results
      searchResults.empty();
      // Clear input value
      searchInput.val('');
      // Clear viedeo details and preview box
      $dialog.find('.attachment-details').empty();
    };

    /**
     * Extract the id from the result.id object
     * @method
     */
    var getTheId = function(result) {

      var id;

      switch(result.id.kind) {
        case 'youtube#video':
          id = result.id.videoId;
          break;
        case 'youtube#playlist':
          id = result.id.playlistId;
          break;
      }

      return id;

    };

    this.search = function() {

      // Get the search options from the view
      // erasing previous values
      searchOptions = getOptions();

      var request = gapi.client.youtube.search.list(searchOptions);

      // Execute the api request
      request.execute(function(response) {

        // Add page token to settings
        addPageToken( response );

        // Clear the results box
        searchResults.empty();

        // Return for now if no items
        if( !response.result || !response.result.items.length ) {

          // Do something to inform the empty response
          return;
        }

        // Populate the results
        populateResults(response.items);

        // Scrollt to top when populating results
        searchResults.animate({
          scrollTop: 0
        }, 'slow');

      });

    };

    // Show the dialog
    this.open = function() {
      $dialog.show();
    };

    // Hide the dialog
    this.close = function() {
      clearVideoPreview();
      $dialog.hide();
    };

    // Load the google api client
    gapi.load('client', function() {

      // Init the gapi client for authorization
      gapi.client.init({
       'apiKey': settings.key
      }).then(function() {

        // Load the youtube api
        gapi.client.load('youtube', 'v3').then(function() {

          // Get frame buttons to interaction
          searchInputType = 'video';
          addVideoButton = $dialog.find('#wt-add-video').hide();
          searchInput = $dialog.find('#wt-youtube-input');
          searchButton = $dialog.find( '#wt-youtube-search' );
          searchResults = $dialog.find('#wt-youtube-results');

          sidebar = $dialog.find('.media-sidebar');
          videoPreviewFrame = $dialog.find('#wt-video-preview');

          // The array to hold current selected video(s)
          currentVideo = null;

          // Flag to prevent multiple infinite scroll loadings
          isLodingNextPage = false;

          // The search options (mainly for the last)
          searchOptions = {};

          // Close on close button click
          $dialog.find('.media-modal-close').click(self.close);
          // Close on black overlay click
          $dialog.find('.video-modal-backdrop').click(self.close);

          // On grid button click add class for grid view to results container
          $dialog.find('.media-toolbar-view .grid').click(function(e) {
            e.preventDefault();
            searchResults.removeClass('list-view').addClass('grid-view');
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
          });

          // On list button click add class for list view to results container
          $dialog.find('.media-toolbar-view .list').click(function(e) {
            e.preventDefault();
            searchResults.removeClass('grid-view').addClass('list-view');
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
          });

          if( settings.restrict ) {

            $dialog.find('.media-router .media-menu-item').hide();
            $.each(settings.restrict, function(index, value) {
              $dialog.find('.media-router .media-menu-item.' + value).show();
            });

          }

          // On frame tab change reload the search (if any)
          // with the new type param activated as filter
          $dialog.find('.media-router .media-menu-item').click(function(e) {
            e.preventDefault();
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
            searchInputType = $(this).data('value');
            if( searchInput.val() !== '' ) {
              // perform the search again with the new type param
              self.search();
            }
          });

          $dialog.find('.expand-details').click(function(e) {
            $(this).text( $(this).text() == 'Enlarge' ? 'Reduce' : 'Enlarge' );
            $dialog.find('.video-modal').toggleClass('expanded');
          });

          // Do the infinite scroll loading on bottom of search results
          searchResults.on('scroll', function() {

            if( !isLodingNextPage && $(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {

              // Set flag to prevent multiple actions
              isLodingNextPage = true;

              // Do request with last options plus next page token
              // stored during previus request
              var request = gapi.client.youtube.search.list(searchOptions);

              // Execute the api request
              request.execute(function(response) {

                // Reset flag
                isLodingNextPage = false;

                // Add page token to settings
                addPageToken( response );

                // Return for now if no items
                if( !response.result || !response.result.items.length ) {
                  // Do something to inform the empty response
                  return;
                }

                // Populate the results
                populateResults(response.items);

              });

            }

          });

          // On enter key do the search
          searchInput.keyup(function(e) {

            if(e.which === 13) {
              e.preventDefault();
              self.search();
            }

          });

          // Start the search for the current input
          searchButton.click(function(e) {
            e.preventDefault();
            self.search();
          });

          // On video add (to content) click run the user defined Callback
          addVideoButton.click(function(e) {
            e.preventDefault();
            selectCallback( null, $dialog.find('form.embed-options').serializeArray() );
            self.close();
          });

          // Append the dialog to body
          $("body").append( $dialog );

        });

      });

    });

    return this;

  };

  window.WPTubeFrame = WPTubeFrame;

})(jQuery);
