<?php namespace GreenImp\Offices;

use Event;
use Backend\Facades\Backend;
use System\Classes\PluginBase;

/**
 * Offices Plugin Information File
 */
class Plugin extends PluginBase
{
  /**
   * @var array Plugin dependencies
   */
  public $require = ['RainLab.Location'];

  /**
   * Returns information about this plugin.
   *
   * @return array
   */
  public function pluginDetails(){
    return [
      'name'        => 'greenimp.offices::lang.app.name',
      'description' => 'No description provided yet...',
      'author'      => 'GreenImp',
      'icon'        => 'icon-building'
    ];
  }

  public function registerComponents(){
    return [
      'GreenImp\Offices\Components\OfficeList'  => 'officeList',
      'GreenImp\Offices\Components\GroupList'   => 'groupList',
      'GreenImp\Offices\Components\GroupPage'   => 'groupPage',
      'GreenImp\Offices\Components\OfficeMap'   => 'officeMap',
      'GreenImp\Offices\Components\OfficePage'   => 'officePage'
    ];
  }

  public function registerPermissions(){
    return [
      'greenimp.offices.manage_groups'  => [
        'tab'   => 'Offices',
        'label' => 'Manage Groups',
        'order' => 200
      ],
      'greenimp.offices.manage_offices'  => [
        'tab'   => 'Offices',
        'label' => 'Manage offices',
        'order' => 200
      ]
    ];
  }

  public function registerNavigation(){
    return [
      'offices'  => [
        'label'       => 'greenimp.offices::lang.app.name',
        'url'         => Backend::url('greenimp/offices/offices'),
        'icon'        => 'icon-building',
        'permissions' => ['greenimp.offices.*'],
        'order'       => 500,

        'sideMenu'    => [
          'offices' => [
            'label'       => 'greenimp.offices::lang.general.offices',
            'url'         => Backend::url('greenimp/offices/offices'),
            'icon'        => 'icon-building',
            'permissions' => ['greenimp.offices.manage_offices']
          ],
          'groups'  => [
            'label'       => 'greenimp.offices::lang.general.groups',
            'url'         => Backend::url('greenimp/offices/groups'),
            'icon'        => 'icon-globe',
            'permissions' => ['greenimp.offices.manage_groups'],
            'order'       => 500
          ]
        ]
      ]
    ];
  }

  public function registerSettings(){
    return [
      'settings'  => [
        'label'       => 'Offices',
        'description' => 'Manage office settings.',
        'icon'        => 'icon-building',
        'class'       => 'GreenImp\Offices\Models\Settings'
      ]
    ];
  }

  public function boot(){
    Event::listen('pages.menuitem.listTypes', function(){
      return [
        'offices-group'      => 'Office Group',
        'offices-all-groups' => 'All office groups'
      ];
    });

    Event::listen('pages.menuitem.getTypeInfo', function($type){
      if(($type == 'offices-group') || ($type == 'offices-all-groups')){
        return \GreenImp\Offices\Classes\Groups::getMenuTypeInfo($type);
      }
    });

    Event::listen('pages.menuitem.resolveItem', function($type, $item, $url, $theme){
      if(($type == 'offices-group') || ($type == 'offices-all-groups')){
        return \GreenImp\Offices\Classes\Groups::resolveMenuItem($item, $url, $theme);
      }
    });
  }
}
