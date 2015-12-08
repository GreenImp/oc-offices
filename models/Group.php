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

    /**
     * @var string The database table used by the model.
     */
    public $table = 'greenimp_offices_groups';

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
    public $hasMany = [
      'office' => [
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

  public function scopeIsActive($query){
    return $query->where('active', true);
  }
}
