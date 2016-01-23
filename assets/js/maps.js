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


        var map = new mapboxgl.Map({
          container:  this, // container id/element
          style:      'mapbox://styles/greenimp/cijrkanv2006xcakwexb7leby' //hosted style id
        });

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
            map.addSource("markers", data);
            //map.addSource("markers", data);

            map.addLayer({
              "id":     "markers",
              "type":   "symbol",
              "source": "markers",
              "layout": {
                "icon-image": "{marker-symbol}-15",
                "text-field": "{title}",
                "text-font": ["Open Sans Semibold", "Arial Unicode MS Bold"],
                "text-offset": [0, 0.6],
                "text-anchor": "top"
              },
              "paint": {
                "text-size": 12
              }
            });
          });
        });
      });

      return true;
    };
  };

  maps.init();
}(window, window.document));
