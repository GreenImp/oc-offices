<?php namespace GreenImp\Offices\Components;

use Cms\Classes\ComponentBase;
use GreenImp\Offices\Models\Group;

class GroupList extends ComponentBase
{
  public $groupPage       = '';
  public $includeHeading  = false;

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
          'includeHeading'  => [
            'title'             => 'Include heading',
            'type'              => 'checkbox',
            'default'           => true
          ],
          'maxItems'        => [
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

  public function onRun(){
    $settings   = \GreenImp\Offices\Models\Settings::instance();

    $this->groupPage  = $settings->groupPage;

    $this->includeHeading = $this->property('includeHeading', true);
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
