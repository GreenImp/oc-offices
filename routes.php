<?php
Route::group(['prefix' => 'greenimp-offices/map'], function(){
  Route::any(
    '{map_type}/{group_id}/offices.geojson',
    [
      'as'  => 'greenimp::offices::map::group::offices',
      function($mapType, $groupID){
        $response = GreenImp\Offices\Classes\Map::getGeoJSON($mapType, $groupID);

        if(is_null($response)){
          \App::abort(404);
        }else{
          return response()->json($response);
        }
      }
    ]
  )
    ->where(['map_type' => '[a-z]+', 'group_id' => '[0-9]+']);

  // I've had to add this as a second route as having an optional `office_id` in the first one didn't seem to work (throws a 404 if no office id specified)
  Route::any(
    '{map_type}/{group_id}/{office_id}/offices.geojson',
    [
      'as'  => 'greenimp::offices::map::group::offices::office',
      function($mapType, $groupID, $officeID){
        $response = GreenImp\Offices\Classes\Map::getGeoJSON($mapType, $groupID, $officeID);

        if(is_null($response)){
          \App::abort(404);
        }else{
          return response()->json($response);
        }
      }
    ]
  )
    ->where(['map_type' => '[a-z]+', 'group_id' => '[0-9]+', 'office_id' => '[0-9]+']);
});
