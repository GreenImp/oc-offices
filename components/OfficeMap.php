<?php namespace GreenImp\Offices\Components;

use Cms\Classes\ComponentBase;
use GreenImp\Offices\Models\Group;
use GreenImp\Offices\Models\Office;

class OfficeMap extends ComponentBase
{
  public $group;
  public $office;

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


    $officeID = $this->param('office_id');

    $query  = Office::isActive();

    if(!is_numeric($officeID) || ($officeID < 1)){
      $this->office  = $query->where('url_slug', $officeID)->first();
    }else{
      $this->office  = $query->find($officeID);
    }



    // add the mapbox CSS/JS
    $this->addCss('https://api.tiles.mapbox.com/mapbox-gl-js/v0.12.4/mapbox-gl.css');
    $this->addJs('https://api.tiles.mapbox.com/mapbox-gl-js/v0.12.4/mapbox-gl.js');

    $this->addCss('assets/css/plugin.css');
    $this->addJs('assets/js/maps.js');
  }

  public function mapDataURL(){
    return \Url::route('greenimp::offices::map::group::offices' . (!is_null($this->office) ? '::office' : ''), [
      'group_id'  => $this->group->id,
      'office_id' => !is_null($this->office) ? $this->office->id : null,
      'map_type'  => !is_null($this->group->map_type) ? $this->group->map_type : 'office'
    ]);
  }
}
