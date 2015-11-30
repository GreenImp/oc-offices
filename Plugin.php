<?php namespace GreenImp\Offices;

use Backend\Facades\Backend;
use System\Classes\PluginBase;

/**
 * Offices Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
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
      'GreenImp\Offices\Components\GroupPage'   => 'groupPage'
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
}
