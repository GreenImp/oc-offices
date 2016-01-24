<?php namespace GreenImp\Offices\Models;

use Model;

/**
 * Group Model
 * @link https://octobercms.com/docs/database/model
 */
class Group extends Model
{
  use \October\Rain\Database\Traits\Sluggable;
  use \October\Rain\Database\Traits\Sortable;
  use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'greenimp_offices_groups';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['id', 'url_slug'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
      'offices' => [
        'GreenImp\Offices\Models\Office',
        'scope' => 'isActive'
      ]
    ];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

  public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

  public $translatable  = ['name', 'description'];

  protected $slugs = ['url_slug' => 'name'];

  public $rules = [
    'name'        => 'required|string|min:1',
    //'url_slug'    => 'required|string|min:1',
    'description' => 'string|min:1',
    'sort_order'  => 'integer',
    'active'      => 'required|boolean'
  ];

  public function scopeIsActive($query){
    return $query->where('active', true);
  }
}
