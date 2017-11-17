<?php namespace GreenImp\Offices\Components;

use Cms\Classes\ComponentBase;
use GreenImp\Offices\Models\Group;
use GreenImp\Offices\Models\Office;

class OfficePage extends ComponentBase
{
  public $group;
  public $office;

  public function componentDetails()
  {
    return [
      'name'        => 'Office page',
      'description' => 'Outputs an office page in a CMS layout.'
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
      $this->group  = $query->find($groupID);
    }


    $officeID = $this->param('office_id');

    $query  = Office::isActive();

    if(!is_numeric($officeID) || ($officeID < 1)){
      $this->office  = $query->where('url_slug', $officeID)->firstOrFail();
    }else{
      $this->office  = $query->find($officeID);
    }
  }
}
