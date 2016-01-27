<?php namespace GreenImp\Offices\Models;

use Model;

/**
 * Office Model
 * @link https://octobercms.com/docs/database/model
 */
class Office extends Model
{
  use \October\Rain\Database\Traits\Sluggable;
  use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'greenimp_offices_offices';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['id', 'url_slug'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'name',
        'address',
        'image',
        'location',
        'country_id',
        'latitude',
        'longitude',
        'active',
        'group_id',
        'description'
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [
    ];
    public $hasMany = [
      'contacts' => 'GreenImp\Offices\Models\Contact'
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

  public $implement = [
    'RainLab.Translate.Behaviors.TranslatableModel',
    'RainLab.Location.Behaviors.LocationModel'
  ];

  public $translatable  = ['name', 'description'];

  protected $slugs = ['url_slug' => 'name'];

  public $rules = [
    'name'          => 'required|string|min:1',
    'image'         => 'string|min:1|max:2000',
    'description'   => 'string|min:1',
    'group_id'      => 'required|integer|exists:greenimp_offices_groups,id',
    'active'        => 'required|boolean',

    'address'       => 'string|min:1',
    'city'          => 'string',
    'zip'           => 'string',
    'country_id'    => 'required|string|exists:rainlab_location_countries,id',
    'state_id'      => 'string|exists:rainlab_location_states,id',
    'latitude'      => 'required|string|min:1',
    'longitude'     => 'required|string|min:1'
  ];

  public function scopeIsActive($query){
    return $query->where('active', true);
  }

  /**
   *
   *
   * @param Group $group
   * @return string
   */
  public function url($group = null){
    return \GreenImp\Offices\Classes\Groups::getOfficeURL($this, $group);
  }
}
