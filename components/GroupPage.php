<?php namespace GreenImp\Offices\Components;

use Cms\Classes\ComponentBase;
use GreenImp\Offices\Models\Group;

class GroupPage extends ComponentBase
{
  public $group;

  public function componentDetails()
  {
    return [
      'name'        => 'Group page',
      'description' => 'Outputs a group page in a CMS layout.'
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
  }
}
