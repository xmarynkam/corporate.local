<?php

namespace Corp;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'path', 'parent_id'
    ];
    

    public function delete(array $options = []) {

        $child = self::where('parent_id', $this->id)->delete();

        return parent::delete($options);
    }
}
