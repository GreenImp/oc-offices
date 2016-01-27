<?php namespace GreenImp\Offices\Classes;

use URL;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use GreenImp\Offices\Models\Group;

/**
 * Handles maps
 *
 * @package greenimp\map
 */
class Map{
  /**
   * @param number $groupID
   * @param number $officeID
   * @return array|null
   */
  public static function getGroupOfficesGeoJSON($groupID, $officeID = null){
    // get the group
    $group = Group::isActive()->find($groupID);

    // if the group doesn't exist throw a 404
    if(is_null($group)){
      return null;
    }


    // get the group's offices
    $offices = $group->offices;

    $data = [];
    $offices->each(function($item) use(&$data, $group, $officeID){
      $isFeatured = (is_numeric($officeID) && ($officeID == $item->id));

      $data[] = [
        'type'        => 'Feature',
        'geometry'    => [
          'type'        => 'Point',
          'coordinates' => [
            floatval($item->longitude),
            floatval($item->latitude)
          ]
        ],
        'properties'  => [
          //'title' => $item->name,
          'marker-symbol' => $isFeatured ? 'star' : 'circle',
          'description'   => '<div class="marker-title">' . $item->name . '</div><p>Click to view</p>',
          'url'           => $item->url($group),
          'featured'      => $isFeatured
        ]
      ];
    });

    // return the data
    return [
      'type'  => 'geojson',
      'data'  => [
        'type'      => 'FeatureCollection',
        'features'  => $data
      ]
    ];
  }
}
