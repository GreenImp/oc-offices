<?php namespace GreenImp\Offices\Components;

use Cms\Classes\ComponentBase;
use RainLab\Location\Models\Country;
use GreenImp\Offices\Models\Office;

class OfficeList extends ComponentBase
{
  public $country;

  public function componentDetails()
  {
    return [
      'name'        => 'Office list',
      'description' => 'Displays a list of offices'
    ];
  }

    public function defineProperties()
    {
        return [
          'group'     => [
            'title'             => 'Group',
            'description'       => 'Only show offices from selected group (optional)',
            'default'           => '',
            'type'              => 'dropdown'
          ],
          'maxItems'  => [
            'title'             => 'Max items',
            'description'       => 'The most amount of offices to show',
            'default'           => '',
            'type'              => 'string',
            'validationPattern' => '^[0-9]*$',
            'validationMessage' => 'The `Max items` property can only contain numeric symbols',
            'placeholder'       => '0 = unlimited'
          ]
        ];
    }

    public function getgroupOptions()
    {
      $groups   = Group::isActive()->get();
      $options  = [
        ''  => 'All'
      ];

      foreach($groups as $group){
        $options[$group->id]  = $group->name;
      }

      return $options;
    }

  public function onRun(){
    $countryCode = $this->param('country_code');

    if(!is_null($countryCode)){
      $this->country = Country::isEnabled()->where('code', '=', $countryCode)->firstOrFail();
    }
  }

  public function offices(){
    $limit  = $this->property('maxItems', 0);
    $group  = $this->property('group', '');

    $query  = Office::isActive();

    // limit the results
    if($limit > 0){
      $query->take($limit);
    }

    // only show the selected group (if specified)
    if($group){
      $query->where('group_id', $group);
    }


    // filter by country
    if(!is_null($this->country)){
      $query->where('country_id', '=', $this->country->id);
    }


    return $query->get();
  }
}
