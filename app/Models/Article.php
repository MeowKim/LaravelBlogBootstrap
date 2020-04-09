<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Article
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string|null $image
 * @property string|null $image_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $updated_by
 * @property bool|null $is_published
 * @property-read \App\Models\User|null $creator
 * @property-read mixed $image_path
 * @property-read mixed $image_type
 * @property-read \App\Models\User|null $updater
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereImageName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereUpdatedBy($value)
 * @mixin \Eloquent
 */
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
