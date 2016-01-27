<?php
Route::group(['prefix' => 'greenimp-offices'], function(){
  Route::any(
    'map/{group_id}/offices.geojson',
    [
      'as'  => 'greenimp::offices::map::group::offices',
      function($groupID){
        $response = GreenImp\Offices\Classes\Map::getGroupOfficesGeoJSON($groupID);

        if(is_null($response)){
          \App::abort(404);
        }else{
          return response()->json($response);
        }
      }
    ]
  )
    ->where('group_id', '[0-9]+');

  // I've had to add this as a second route as having an optioal `office_id` in the first one didn't seem to work (throws a 404 if no office id specified)
  Route::any(
    'map/{group_id}/{office_id}/offices.geojson',
    [
      'as'  => 'greenimp::offices::map::group::offices::office',
      function($groupID, $officeID){
        $response = GreenImp\Offices\Classes\Map::getGroupOfficesGeoJSON($groupID, $officeID);

        if(is_null($response)){
          \App::abort(404);
        }else{
          return response()->json($response);
        }
      }
    ]
  )
    ->where(['group_id' => '[0-9]+', 'office_id' => '[0-9]+']);
});
