<?php

namespace Corp;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at'];


    protected $fillable = ['title','img','alias','text','desc','keywords','meta_desc','category_id'];

    /**
    * Get the route key for the model.
    *
    * @return string
    */
    public function getRouteKeyName()
    {
      return 'alias';
    }

    
    // Get information about the user who added the article
    public function user() {
        return $this->belongsTo('Corp\User');
    }

    // Get information about the category of a separate article
    public function category() {
        return $this->belongsTo('Corp\Category');
    }

    // Selects entries from the comment table that are associated with the specified article
    public function comments() {
        return $this->hasMany('Corp\Comment');
    }


}
