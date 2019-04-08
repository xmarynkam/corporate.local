<?php

namespace Corp;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Selects entries from a table of articles that are related to the specified category
    public function articles() {
        return $this->hasMany('Corp\Article');
    }
}
