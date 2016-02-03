<?php namespace GreenImp\Offices\Classes;

use URL;
use File;
use ApplicationException;
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
  protected static function parseGeoJSONFile($filename){
    $path = plugins_path() . '/greenimp/offices/assets/geojson/' . $filename;

    if(!preg_match('/^[\w\d]+\.(geo)?json$/i', $filename) || !File::exists($path)){
      throw new ApplicationException('Invalid File: ' . $path);
    }

    // load and parse the file to an object
    return json_decode(File::get($path));
  }

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
    // get the country geoJSON
    $geoJSON = self::parseGeoJSONFile('countries.geojson');

    // check that data was returned
    if(is_null($geoJSON)){
      return null;
    }



    // get the offices
    if(is_numeric($groupID)){
      // group defined - get the group and its offices
      $offices = Group::isActive()->findOrFail($groupID)->offices();
    }else{
      // no group - get all offices
      $offices = Office::isActive();
    }


    // get a list of country IDs for the offices
    $countryCodes = Country
      ::whereIn('id', $offices->groupBy('country_id')->lists('country_id'))
      ->groupBy('id')
      ->lists('code');

    // loop through the countries and build up their data
    foreach($geoJSON->features as $k =>$feature){
      if(!in_array($feature->properties->iso_a2, $countryCodes)){
        // country doesn't have any office - remove it
        unset($geoJSON->features[$k]);
      }else{
        $isFeatured = false;

        $feature->properties->description = '<div class="marker-title">' . $feature->properties->name . '</div><p>Click to view</p>';
        $feature->properties->url = Groups::getCountryURL(Country::where('code', '=', $feature->properties->iso_a2)->first());
        $feature->properties->featured = $isFeatured;

        $geoJSON->features[$k] = $feature;
      }
    }

    // re-index the features (mapbox expects GeoJSON arrays to be keyed in order without missing keys)
    $geoJSON->features = array_values($geoJSON->features); // normalize index


    // build and return the data
    return self::buildGeoJSON($geoJSON->features);
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
