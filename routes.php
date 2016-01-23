<?php
Route::group(['prefix' => 'greenimp-offices'], function() {
  Route::any(
    'map/group/{id}/offices.geojson',
    [
      'as'  => 'greenimp::offices::map::group::offices',
      function($id){
        $response = GreenImp\Offices\Classes\Map::getGroupOfficesGeoJSON($id);

        if(is_null($response)){
          \App::abort(404);
        }else{
          return response()->json($response);
        }
      }
    ]
  )
    ->where('id', '[0-9]+');
});
