<?php namespace GreenImp\Offices\Models;

use Model;

/**
 * Office Model
 * @link https://octobercms.com/docs/database/model
 */
class Office extends Model
{

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

  public function scopeIsActive($query){
    return $query->where('active', true);
  }
}
