<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'articles';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'content', 'image', 'image_name', 'created_at', 'created_by', 'updated_at', 'updated_by', 'is_published'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = ['is_published' => 'boolean'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    public function creator()
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo('App\Models\User', 'updated_by');
    }

    public function getImagePathAttribute()
    {
        return $this->image ? '/' . config('CONST.UPLOAD_PATH_ARTICLES') . '/' . $this->image : null;
    }

    public function getImageTypeAttribute()
    {
        list($width, $height) = @getimagesize(public_path($this->ImagePath));

        return $width < 500 || $width < $height ? 'vertical' : 'horizontal';
    }
}
