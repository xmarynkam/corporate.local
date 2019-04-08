<?php

namespace Corp;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //
    public function users() {
        return $this->belongsToMany('Corp\User', 'role_user');
    }

    public function permission() {
        return $this->belongsToMany('Corp\Permission', 'permission_role');
    }

    // string  or ['permission1', 'permission1']
    public function hasPermission($name, $require = false)
    {
        if (is_array($name)) {
        	// If there is a array in the $name
            foreach ($name as $permissionName) {
                $hasPermission = $this->hasPermission($permissionName);
                if ($hasPermission && !$require) {
                    return true;
                } elseif (!$hasPermission && $require) {
                    return false;
                }
            }
            return $require;
        } else {
        	// If there is a string in the $name
            foreach ($this->permission as $permission) {
                if ($permission->name == $name) {
                    return true;
                }
            }
        }
        return false;
    }

    public function savePermissions($inputPermissions) 
    {
        
        if(!empty($inputPermissions)) {
            // Create links in linked tables
            $this->permission()->sync($inputPermissions);
        }
        else {
            // For a specific role, all privileges in the linked tables will be deleted
            $this->permission()->detach();
        }
        
        return TRUE;
    }
}
