<?php namespace GreenImp\Offices\Models;

use Model;

/**
 * Office Model
 * @link https://octobercms.com/docs/database/model
 */
class Office extends Model
{
  use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'greenimp_offices_offices';

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
    public $hasOne = [
    ];
    public $hasMany = [
      'contact' => 'GreenImp\Offices\Models\Contact'
    ];
    public $belongsTo = [
      'group' => [
        'GreenImp\Offices\Models\Group',
        'scope' => 'isActive'
      ]
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

  public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

  public $translatable  = ['name', 'description'];

  public $rules = [
    'country_code'  => 'required|string|size:2',
    'name'          => 'required|string|min:1',
    'image'         => 'string|min:1|max:2000',
    'description'   => 'string|min:1',
    'address'       => 'string|min:1',
    'group_id'      => 'required|integer|exists:greenimp_offices_groups,id',
    'active'        => 'required|boolean'
  ];

  public function scopeIsActive($query){
    return $query->where('active', true);
  }
}
