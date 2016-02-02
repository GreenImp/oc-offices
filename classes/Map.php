<?php namespace GreenImp\Offices\Classes;

use URL;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use RainLab\Location\Models\Country;
use GreenImp\Offices\Models\Group;

/**
 * Handles maps
 *
 * @package greenimp\map
 */
class Map{
  protected static function buildGeoJSON($data, $type = 'FeatureCollection'){
    return [
      'type'  => 'geojson',
      'data'  => [
        'type'      => $type,
        'features'  => $data
      ]
    ];
  }

  /**
   * Returns the office GeoJSON
   *
   * @param number $groupID
   * @param number $officeID
   * @return array|null
   */
  public static function getOfficesGeoJSON($groupID = null, $officeID = null){
    if(is_numeric($groupID)){
      // group defined - get the group and its offices
      $offices = Group::isActive()->findOrFail($groupID)->offices;
    }else{
      $offices = Office::isActive()->get();
    }

    $data = [];
    $offices->each(function($item) use(&$data, $officeID){
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
          'url'           => $item->url(),
          'featured'      => $isFeatured
        ]
      ];
    });

    // build and return the data
    return self::buildGeoJSON($data);
  }

  /**
   * Returns the country GeoJSON
   *
   * @param null $groupID
   * @param null $officeID
   * @return array|null
   */
  public static function getCountryGeoJSON($groupID = null, $officeID = null){
    if(is_numeric($groupID)){
      // group defined - get the group and its offices
      $offices = Group::isActive()->findOrFail($groupID)->offices();
    }else{
      // no group - get all offices
      $offices = Office::isActive();
    }


    // get a list of country IDs for the offices
    $countryIDs = $offices->groupBy('country_id')->lists('country_id');

    $countries = Country::whereIn('id', $countryIDs)->get();

    // loop through the countries and build up their data
    $data = [];
    foreach($countries as $country){
      $isFeatured = false;

      $data[] = [
        'properties'  => [
          'iso_a2'        => $country->code,
          'description'   => '<div class="marker-title">' . $country->name . '</div><p>Click to view</p>',
          'url'           => '#',
          'featured'      => $isFeatured
        ]
      ];
    }


    // build and return the data
    return self::buildGeoJSON($data, 'countryCollection');
  }

  public static function getGeoJSON($mapType, $groupID = null, $officeID = null){
    switch($mapType){
      case 'office':
        return self::getOfficesGeoJSON($groupID, $officeID);
        break;
      case 'country':
        return self::getCountryGeoJSON($groupID, $officeID);
        break;
      default:
        return null;
        break;
    }
  }
}
