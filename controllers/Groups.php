<?php namespace GreenImp\Offices\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Groups Back-end Controller
 */
class Groups extends Controller
{
  public $implement = [
    'Backend.Behaviors.FormController',
    'Backend.Behaviors.ListController',
    'Backend.Behaviors.ReorderController'
  ];

  public $formConfig    = 'config_form.yaml';
  public $listConfig    = 'config_list.yaml';
  public $reorderConfig = 'config_reorder.yaml';

  public function __construct()
  {
    parent::__construct();

    BackendMenu::setContext('GreenImp.Offices', 'offices', 'groups');
  }

  public function index(){
    // Call the ListController behavior index() method
    $this->asExtension('ListController')->index();
  }
}
