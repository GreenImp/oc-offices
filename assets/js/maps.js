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


    /**
     * Takes two bounds combines them and returns
     * the largest bounds from both bounds
     *
     * @param boundsA
     * @param boundsB
     * @returns {*}
       */
    var getLargestBounds = function(boundsA, boundsB){
      var bounds = [[], []];

      if(!boundsA && !boundsB){
        // no bounds defined - return empty bounds
        return bounds;
      }else if(!boundsA){
        // no bounds A defined - return bounds B
        return boundsB;
      }else if(!boundsB){
        // no bounds B defined - return bounds A
        return boundsA;
      }


      // calculate the min bounds
      bounds[0][0] = boundsA[0][0] ? Math.min(boundsA[0][0], boundsB[0][0] ? boundsB[0][0] : boundsA[0][0]) : boundsB[0][0];
      bounds[0][1] = boundsA[0][1] ? Math.min(boundsA[0][1], boundsB[0][1] ? boundsB[0][1] : boundsA[0][1]) : boundsB[0][1];

      // calculate the min bounds
      bounds[1][0] = boundsA[1][0] ? Math.max(boundsA[1][0], boundsB[1][0] ? boundsB[1][0] : boundsA[1][0]) : boundsB[1][0];
      bounds[1][1] = boundsA[1][1] ? Math.max(boundsA[1][1], boundsB[1][1] ? boundsB[1][1] : boundsA[1][1]) : boundsB[1][1];

      return bounds;
    };

    /**
     * Returns the bounds for the given feature.
     * Most useful for Polygons
     *
     * @param feature
     * @returns {*[]}
     */
    var getFeatureBounds  = function(feature, padding){
      var geometry  = feature.geometry,
          bounds    = [[], []];

      padding = (padding > 0) ? padding : 0;

      switch(geometry.type){
        case 'Point':
          // calculate the min bounds
          bounds[0][0] = geometry.coordinates[0] - padding;
          bounds[0][1] = geometry.coordinates[1] - padding;

          // calculate the min bounds
          bounds[1][0] = geometry.coordinates[0] + padding;
          bounds[1][1] = geometry.coordinates[1] + padding;
          break;
        case 'Polygon':
          // loop through only the first coordinate array, as this is the exterior shape
          // @link http://geojson.org/geojson-spec.html#polygon
          $.each(geometry.coordinates[0], function(i, coords){
            bounds = getLargestBounds(bounds, [
              [coords[0] - padding, coords[1] - padding],
              [coords[0] + padding, coords[1] + padding]
            ]);
          });
          break;
        case 'MultiPolygon':
          // loop through each polygon shape (A MultiPolygon feature type has multiple separate polygons that make it up)
          // @link http://geojson.org/geojson-spec.html#multipolygon
          $.each(geometry.coordinates, function(i, section){
            // get the individual Poly feature bounds
            var sBounds = getFeatureBounds({
              geometry: {
                type: 'Polygon',
                coordinates: section
              }
            }, padding);

            bounds = getLargestBounds(bounds, sBounds);
          });
          break;
      }

      return bounds;
    };

    /**
     * Returns the outer bounds for all features within the
     * given collection
     *
     * @param collection
     * @returns {*[]}
     */
    var getCollectionBounds = function(collection, padding){
      // get the marker bounds
      var bounds    = [[], []];

      $.each(collection, function(i, feature){
        bounds = getLargestBounds(bounds, getFeatureBounds(feature, padding));
      });

      return bounds;
    };

    /**
     * Returns the centre point for the given feature.
     * Most useful for Polygons
     *
     * @param feature
     * @returns {*[]}
       */
    var getFeatureCenter  = function(feature){
      var bounds = getFeatureBounds(feature);

      return [
        (bounds[0][0] + bounds[1][0]) / 2,
        (bounds[0][1] + bounds[1][1]) / 2
      ]
    };


    this.init = function(){
      mapboxgl.accessToken = 'pk.eyJ1IjoiZ3JlZW5pbXAiLCJhIjoiY2lobms3NjA2MDBocHY0a3F4cWw2eGZyMiJ9.yJWMqYT_z2Wvfy23bqTzgA';

      if(!mapboxgl.supported()){
        alert('Your browser does not support Mapbox GL');

        return false;
      }


      // loop through each map container and initialise the map
      $('[data-' + lib.namespace + ']').each(function(i, elm){
        var $container      = $(this),
            mapType         = $container.attr('data-' + lib.namespace + '-type'),
            geoJSON         = $.Deferred(),
            featuredFilter;


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
            map.addSource('features', data);

            switch(mapType){
              case 'country':
                map.addLayer({
                  'id': mapType,
                  'interactive': true,
                  'type': 'fill',
                  'source': 'features',
                  'layout': {
                    'line-join': 'round',
                    'line-cap': 'round'
                  },
                  'paint': {
                    'fill-color': '#72808B'
                  }
                });

                map.addLayer({
                  'id': mapType + '-hover',
                  'type': 'fill',
                  'source': 'features',
                  'layout': {
                    'line-join': 'round',
                    'line-cap': 'round'
                  },
                  'paint': {
                    'fill-color': '#FF6600'
                  },
                  'filter': ['==', 'reference_id', '']
                });
                break;
              case 'office':
                map.addLayer({
                  'id': mapType,
                  'interactive': true,
                  "type": 'circle',
                  "source": "features",
                  "paint": {
                    'circle-color': '#2398d3',
                    'circle-radius': 8
                  }
                });

                map.addLayer({
                  'id': mapType + '-hover',
                  'type': 'circle',
                  'source': 'features',
                  'paint': {
                    'circle-color': '#FF6600',
                    'circle-radius': 10
                  },
                  'filter': ['==', 'reference_id', '']
                });
                break;
              default:
                console.warn('Unrecognised map type:', mapType);
                break;
            }

            // fit the map to the markers
            map.fitBounds(getCollectionBounds(data.data.features, 5));

            // find the featured feature (if any) and pan to it
            $.each(data.data.features, function(i, feature){
              // check if this is a featured marker
              if(feature.properties.featured){
                // store the filter for the featured feature
                featuredFilter = ['==', 'reference_id', feature.properties.reference_id];

                // filter the hover effect layer
                if(map.getLayer(mapType + '-hover')){
                  map.setFilter(mapType + '-hover', featuredFilter);
                }

                // pan to the featured marker
                map.panTo(getFeatureCenter(feature));
              }
            });
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
              radius: 5, // 7.5 Half the marker size (15px).
              includeGeometry: true,
              layer: mapType
            },
            function(err, features){
              if(!err && features.length){
                // Change the cursor style as a UI indicator.
                map.getCanvas().style.cursor = (!err && features.length) ? 'pointer' : '';

                var feature = features[0];

                // Initialize a popup and set its coordinates
                // based on the feature found.
                if(feature.properties.description) {
                  popup.setLngLat(getFeatureCenter(feature))
                    .setHTML(feature.properties.description)
                    .addTo(map);
                }

                // filter the hover effect layer
                if(map.getLayer(mapType + '-hover')){
                  // get the filter for the hovered feature
                  var filter = ['==', 'reference_id', features[0].properties.reference_id];

                  // set the filter (including the featured filter, if set)
                  map.setFilter(mapType + '-hover', featuredFilter ? ['any', featuredFilter, filter] : filter);
                }
              }else{
                // remove the popup
                popup.remove();

                // hide the hover effect layer
                if(map.getLayer(mapType + '-hover')){
                  // if we have a featured filter we still want to show it
                  map.setFilter(mapType + '-hover', featuredFilter || ['==', 'reference_id', '']);
                }
              }
            });
        });

        /**
         * Show the marker information when clicking it
         */
        map.on('click', function(e){
          map.featuresAt(e.point, {layer: mapType, radius: 10}, function(err, features){
            if(err || !features.length){
              return;
            }

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
