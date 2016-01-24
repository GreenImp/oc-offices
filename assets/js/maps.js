/* ==========================================================================
 * Example JS component file
 * ========================================================================== */

;(function (window, document, undefined){
  "use strict";

  /**
   * Create our component
   *
   * @type {maps}
   */
  window.maps  = new function(){
    /**
     * The component scope.
     * Used within functions instead of `this`
     *
     * @type {window.maps}
     */
    var lib = this;

    /**
     * Component namespace.
     * Used for things like data attributes for event handlers.
     * It's public (using `this`) so we can use it from other components:
     * `console.log('The example namespace is: ', app.example.namespace);`
     *
     * @type {string}
     */
    this.namespace  = 'map';


    this.init = function(){
      //var map = new lib.map('pk.eyJ1IjoiZ3JlZW5pbXAiLCJhIjoiY2lobms3NjA2MDBocHY0a3F4cWw2eGZyMiJ9.yJWMqYT_z2Wvfy23bqTzgA');

      mapboxgl.accessToken = 'pk.eyJ1IjoiZ3JlZW5pbXAiLCJhIjoiY2lobms3NjA2MDBocHY0a3F4cWw2eGZyMiJ9.yJWMqYT_z2Wvfy23bqTzgA';

      if(!mapboxgl.supported()){
        alert('Your browser does not support Mapbox GL');

        return false;
      }


      // loop through each map container and initialise the map
      $('[data-' + lib.namespace + ']').each(function(i, elm){
        var $container  = $(this),
            geoJSON     = $.Deferred();


        // create the map
        var map = new mapboxgl.Map({
          container:  this, // container id/element
          style:      'mapbox://styles/greenimp/cijrkanv2006xcakwexb7leby' //hosted style id
        });

        // store the map on the container
        $container.data('map', map);


        // check for data
        if($container.attr('data-' + lib.namespace + '-data')){
          try{
            geoJSON.resolve(JSON.parse($container.attr('data-' + lib.namespace + '-data')));
          }catch(e){
            // data isn't JSON - check if URL
            console.log('Unable to parse map data: ', e);
            geoJSON.reject(e);
          }
        }else if($container.attr('data-' + lib.namespace + '-data-url')){
          $.get($container.attr('data-' + lib.namespace + '-data-url'))
            .then(
              geoJSON.resolve,
              function(jqXHR, textStatus, errorThrown){
                console.log('Error loading map data URL: ', textStatus, errorThrown);
                geoJSON.reject(jqXHR, textStatus, errorThrown);
              }
            );
        }


        map.on('style.load', function(){
          geoJSON.done(function(data){
            map.addSource('markers', data);

            map.addLayer({
              "id":           "markers",
              "interactive":  true,
              "type":         "symbol",
              "source":       "markers",
              "layout":       {
                "icon-image":   "{marker-symbol}-18",
                "text-field":   "{label}",
                "text-font":    ["Open Sans Semibold", "Arial Unicode MS Bold"],
                "text-offset":  [0, 0.6],
                "text-anchor":  "top"
              },
              "paint":        {
                "text-size": 12
              }
            });


            // get the marker bounds
            var bounds = [];

            $.each(data.data.features, function(i, feature){
              var geometry  = feature.geometry;

              if(geometry.type  == 'Point'){
                // ensure min bound array exists
                bounds[0] = bounds[0] || [];
                // ensure max bound array exists
                bounds[1] = bounds[1] || [];

                // calculate the min bounds
                bounds[0][0] = bounds[0][0] ? Math.min(bounds[0][0], geometry.coordinates[0] - .5) : geometry.coordinates[0] - .5;
                bounds[0][1] = bounds[0][1] ? Math.min(bounds[0][1], geometry.coordinates[1] - .5) : geometry.coordinates[1] - .5;

                // calculate the min bounds
                bounds[1][0] = bounds[1][0] ? Math.max(bounds[1][0], geometry.coordinates[0] + .5) : geometry.coordinates[0] + .5;
                bounds[1][1] = bounds[1][1] ? Math.max(bounds[1][1], geometry.coordinates[1] + .5) : geometry.coordinates[1] + .5;
              }
            });

            // fit the map to the markers
            map.fitBounds(bounds);
          });
        });


        // Create a popup, but don't add it to the map yet.
        var popup = new mapboxgl.Popup({
          closeButton: false,
          closeOnClick: false
        });

        /**
         * Show a tooltip on hover of a marker
         */
        map.on('mousemove', function(e){
          map.featuresAt(
            e.point,
            {
              radius: 7.5, // Half the marker size (15px).
              includeGeometry: true,
              layer: 'markers'
            },
            function(err, features){
              // Change the cursor style as a UI indicator.
              map.getCanvas().style.cursor = (!err && features.length) ? 'pointer' : '';

              if(err || !features.length){
                popup.remove();
                return;
              }

              var feature = features[0];

              // Initialize a popup and set its coordinates
              // based on the feature found.
              popup.setLngLat(feature.geometry.coordinates)
                .setHTML(feature.properties.description)
                .addTo(map);
            });
        });

        /**
         * Show the marker information when clicking it
         */
        map.on('click', function (e) {
          map.featuresAt(e.point, {layer: 'markers', radius: 10, includeGeometry: true}, function (err, features) {
            if (err || !features.length)
              return;

            var feature = features[0];

            if(feature.properties.url){
              window.location = feature.properties.url;
            }
          });
        });
      });


      return true;
    };
  };

  maps.init();
}(window, window.document));