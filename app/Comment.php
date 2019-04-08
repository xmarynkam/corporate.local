<?php

namespace Corp;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

	protected $fillable = ['name','text','site','user_id','article_id','parent_id','email'];

    // Get information about the article of a separate comment
    public function article() {
        return $this->belongsTo('Corp\Article');
    }

    // Get information about the user of a separate comment
    public function user() {
        return $this->belongsTo('Corp\User');
    }
}
