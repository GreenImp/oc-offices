<?php namespace GreenImp\Offices\Components;

use Cms\Classes\ComponentBase;
use GreenImp\Offices\Models\Group;

class OfficeMap extends ComponentBase
{
  public $group;

  public function componentDetails()
  {
    return [
      'name'        => 'Office map',
      'description' => 'Outputs an interactive map of offices.'
    ];
  }

  public function defineProperties()
  {
      return [];
  }

  public function onRun(){
    $groupID = $this->param('group_id');

    $query  = Group::isActive();

    if(!is_numeric($groupID) || ($groupID < 1)){
      $this->group  = $query->where('url_slug', $groupID)->firstOrFail();
    }else{
      $this->group  = $query->findOrFail($groupID);
    }


    // add the mapbox CSS/JS
    //$this->addCss('https://api.mapbox.com/mapbox.js/v2.2.4/mapbox.css');
    //$this->addJs('https://api.mapbox.com/mapbox.js/v2.2.4/mapbox.js');

    $this->addCss('https://api.tiles.mapbox.com/mapbox-gl-js/v0.12.4/mapbox-gl.css');
    $this->addJs('https://api.tiles.mapbox.com/mapbox-gl-js/v0.12.4/mapbox-gl.js');

    $this->addCss('assets/css/plugin.css');
    $this->addJs('assets/js/maps.js');
  }

  public function mapDataURL(){
    return \Url::route('greenimp::offices::map::group::offices', ['id' => $this->group->id]);
  }
}
