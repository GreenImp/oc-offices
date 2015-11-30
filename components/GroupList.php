<?php namespace GreenImp\Offices\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use GreenImp\Offices\Models\Group;

class GroupList extends ComponentBase
{
  public function componentDetails()
  {
    return [
      'name'        => 'Group list',
      'description' => 'Displays a list of office groups'
    ];
  }

    public function defineProperties()
    {
        return [
          'groupPage' => [
            'title'             => 'Group page',
            'type'              => 'dropdown',
            'default'           => $this->groupPage()
          ],
          'maxItems'  => [
            'title'             => 'Max items',
            'description'       => 'The most amount of groups to show',
            'default'           => '',
            'type'              => 'string',
            'validationPattern' => '^[0-9]*$',
            'validationMessage' => 'The `Max items` property can only contain numeric symbols',
            'placeholder'       => '0 = unlimited'
          ]
        ];
    }

  public function getGroupPageOptions(){
    return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
  }

  public function onRun(){
  }

  public function groupPage(){
    return $this->property('groupPage', 'office-group');
  }

  public function groups(){
    $limit  = $this->property('maxItems', 0);

    $query  = Group::isActive();

    // limit the results
    if($limit > 0){
      $query->take($limit);
    }

    return $query->get();
  }
}
