<?php namespace GreenImp\Offices\Models;

use Model;
use Validator;

/**
 * Contact Model
 * @link https://octobercms.com/docs/database/model
 */
class Contact extends Model
{
  use \October\Rain\Database\Traits\Validation;

  /**
   * @var string The database table used by the model.
   */
  public $table = 'greenimp_offices_contacts';

  /**
   * @var array Guarded fields
   */
  protected $guarded = [];

  /**
   * @var array Fillable fields
   */
  protected $fillable = ['type', 'value', 'label'];

  /**
   * @var array Relations
   */
  public $hasOne = [];
  public $hasMany = [];
  public $belongsTo = [
    'office' => 'GreenImp\Offices\Models\Office'
  ];
  public $belongsToMany = [];
  public $morphTo = [];
  public $morphOne = [];
  public $morphMany = [];
  public $attachOne = [];
  public $attachMany = [];

  public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

  public $translatable  = ['label'];

  public $rules = [
    'office_id' => 'integer|exists:greenimp_offices_offices,id',
    'type'      => 'required|isContactType',
    'value'     => 'required|string|min:1',
    'label'     => 'string|min:1'
  ];


  public static function boot(){
    parent::boot();

    /**
     * Create a custom validation rule for the contact type
     */
    Validator::extend('isContactType', function($attribute, $value, $parameters){
      return in_array($value, array_keys(self::getTypeOptions()));
    });
  }

  /**
   * Returns a list of available contact types
   *
   * @var string $keyValue
   * @return array
   */
  public static function getTypeOptions($keyValue = null){
    return [
      'tel'   => 'Telephone',
      'fax'   => 'Fax',
      'email' => 'Email',
      'other' => 'Other'
    ];
  }
}
