<?php namespace GreenImp\Offices\Classes;

use URL;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use RainLab\Location\Models\Country;
use GreenImp\Offices\Models\Group;
use GreenImp\Offices\Models\Office;

/**
 * Represents groups
 *
 * @package greenimp\groups
 */
class Groups{
  public static function getGroupPage(){
    $settings   = \GreenImp\Offices\Models\Settings::instance();
    return $settings->groupPage;
  }

  public static function getOfficePage(){
    $settings   = \GreenImp\Offices\Models\Settings::instance();
    return $settings->officePage;
  }

  public static function getCountryPage(){
    $settings   = \GreenImp\Offices\Models\Settings::instance();
    return $settings->countryPage;
  }

  /**
   * Returns the URL for the given group
   *
   * @param Group $group
   * @return string
   */
  public static function getGroupPageURL(Group $group){
    return Page::url(
      self::getGroupPage(),
      [
        'group_id'  => $group->url_slug
      ]
    );
  }

  /**
   * Returns the URL for the given office
   *
   * @param Office     $office
   * @param Group|null $group
   * @return string
   */
  public static function getOfficeURL(Office $office, Group $group = null){
    return Page::url(
      self::getOfficePage(),
      [
        'group_id'  => !is_null($group) ? $group->url_slug : null,
        'office_id' => $office->url_slug
      ]
    );
  }

  /**
   * Returns the URL for the given country
   *
   * @param Country    $country
   * @return string
   */
  public static function getCountryURL(Country $country){
    return Page::url(
      self::getCountryPage(),
      [
        'country_code'  => $country->code
      ]
    );
  }

  /**
   * Handler for the pages.menuitem.getTypeInfo event.
   * Returns a menu item type information. The type information is returned as array
   * with the following elements:
   * - references - a list of the item type reference options. The options are returned in the
   *   ["key"] => "title" format for options that don't have sub-options, and in the format
   *   ["key"] => ["title"=>"Option title", "items"=>[...]] for options that have sub-options. Optional,
   *   required only if the menu item type requires references.
   * - nesting - Boolean value indicating whether the item type supports nested items. Optional,
   *   false if omitted.
   * - dynamicItems - Boolean value indicating whether the item type could generate new menu items.
   *   Optional, false if omitted.
   * - cmsPages - a list of CMS pages (objects of the Cms\Classes\Page class), if the item type requires a CMS page reference to
   *   resolve the item URL.
   * @param string $type Specifies the menu item type
   * @return array Returns an array
   */
  public static function getMenuTypeInfo($type)
  {
    if($type == 'offices-all-groups'){
      return [
        'dynamicItems' => true
      ];
    }elseif($type == 'offices-group'){
      return [
        'references'   => self::listGroupMenuOptions()
      ];
    }

    return [];
  }

  /**
   * Returns a list of options for the Reference drop-down menu in the
   * menu item configuration form, when the group item type is selected.
   * @return array Returns an array
   */
  protected static function listGroupMenuOptions()
  {
    $result = [];

    foreach(Group::isActive()->get() as $item){
      $result[$item->id]  = $item->name;
    }

    return $result;
  }

  /**
   * Handler for the pages.menuitem.resolveItem event.
   * Returns information about a menu item. The result is an array
   * with the following keys:
   * - url - the menu item URL. Not required for menu item types that return all available records.
   *   The URL should be returned relative to the website root and include the subdirectory, if any.
   *   Use the URL::to() helper to generate the URLs.
   * - isActive - determines whether the menu item is active. Not required for menu item types that
   *   return all available records.
   * - items - an array of arrays with the same keys (url, isActive, items) + the title key.
   *   The items array should be added only if the $item's $nesting property value is TRUE.
   * @param \RainLab\Pages\Classes\MenuItem $item Specifies the menu item.
   * @param \Cms\Classes\Theme $theme Specifies the current theme.
   * @param string $url Specifies the current page URL, normalized, in lower case
   * The URL is specified relative to the website root, it includes the subdirectory name, if any.
   * @return mixed Returns an array. Returns null if the item cannot be resolved.
   */
  public static function resolveMenuItem($item, $url, $theme)
  {
    $result = [];

    if($item->type == 'offices-group'){
      $group = Group::isActive()->find($item->reference);

      if(!is_null($group)){
        $result['url'] = self::getGroupPageURL($group);
        $result['isActive'] = $result['url'] == $url;
      }
    }

    if($item->nesting || ($item->type == 'offices-all-groups')){
      $iterator = function($items) use (&$iterator, $url){
        $branch = [];

        foreach($items as $item){
          $branchItem = [];
          $branchItem['url']      = self::getGroupPageURL($item);
          $branchItem['isActive'] = $branchItem['url'] == $url;
          $branchItem['title']    = $item->name;

          $branch[] = $branchItem;
        }

        return $branch;
      };

      $result['items'] = $iterator($item->type == 'offices-group' ? (isset($group) ? [$group] : []) : Group::isActive()->get());
    }

    return $result;
  }
}
