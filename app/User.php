<?php

namespace Corp;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'login'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Selects entries from table of articles that are related to a particular user
    public function articles() {
        return $this->hasMany('Corp\Article');
    }

    public function roles() {
        return $this->belongsToMany('Corp\Role', 'role_user');
    }

    // $permission - string or array('View_Admin', 'ADD_ARTICLES')
    // $require = TRUE then $permission - array
    public function canDo($permission, $require = FALSE) {


        if(is_array($permission)) {
            foreach($permission as $permName) {
                $permName = $this->canDo($permName);

                if($permName && !$require) {
                    return TRUE;
                }

                elseif(!$permName && $require) {
                    return FALSE;
                }
            }
            return $require;
            
        }
        else {

            foreach($this->roles as $role) {

                foreach($role->permission as $perm) {

                    //foo*    foobar
                    if(str_is($permission, $perm->name)) {
                        return TRUE;
                    }
                }
            }
            
        }
    }


    // string  or ['role1', 'role2']
    public function hasRole($name, $require = false)
    {
        if (is_array($name)) {
            // If there is a array in the $name
            foreach ($name as $roleName) {
                $hasRole = $this->hasRole($roleName);
                if ($hasRole && !$require) {
                    return true;
                } elseif (!$hasRole && $require) {
                    return false;
                }
            }
            return $require;
        } else {

            // If there is a string in the $name
            foreach ($this->roles as $role) {
                if ($role->name == $name) {
                    return true;
                }
            }
        }
        return false;
    }
}
