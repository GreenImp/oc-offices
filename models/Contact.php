<?php namespace GreenImp\Offices\Models;

use Model;

/**
 * Group Model
 * @link https://octobercms.com/docs/database/model
 */
class Contact extends Model
{

    /**
     * @var string The database table used by the model.
     */
    public $table = 'greenimp_offices_contacts';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

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

  /**
   * Returns a list of available contact types
   *
   * @var string $keyValue
   * @return array
   */
  public function getTypeOptions($keyValue = null){
    return [
      'tel'   => 'Telephone',
      'fax'   => 'Fax',
      'email' => 'Email',
      'other' => 'Other'
    ];
  }
}
