<?php namespace GreenImp\Offices\Models;

use Model;
use Cms\Classes\Page;

/**
 * Settings Model
 * @link https://octobercms.com/docs/database/model
 */
class Settings extends Model
{
  public $implement       = ['System.Behaviors.SettingsModel'];

  public $settingsCode    = 'greenimp_offices_code';

  public $settingsFields  = 'fields.yaml';

  public function getGroupPageOptions(){
    return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
  }

  public function getOfficePageOptions(){
    return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
  }

  public function getCountryPageOptions(){
    return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
  }
}
