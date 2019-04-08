<?php 
    
namespace Corp\Repositories;

use Corp\User;
use Gate;


class UsersRepository extends Repository {

    public function __construct(User $user) {

        $this->model = $user;
        
    }

    // Adding an user
    public function addUser($request) {
        if(Gate::denies('create', $this->model)) {
            abort(403);
        }

        $data = $request->all();

        $user = $this->model->create([
        	'name' => $data['name'],
        	'login' => $data['login'],
        	'email' => $data['email'],
        	'password' => bcrypt($data['password']),
        ]);

        if($user) {
        	$user->roles()->attach($data['role_id']);
        }

        return ['status' => 'User added'];

    }

    // Edit user
    public function updateUser($request, $user) {
        if(Gate::denies('edit', $this->model)) {
            abort(403);
        }

        $data = $request->all();

        if(isset($data['password'])) {
        	$data['password'] = bcrypt($data['password']);
        }
        else {
        	$data['password'] = $user['password'];
        }

        $user->fill($data);

        if($user->update()) {
        	$user->roles()->sync([$data['role_id']]);
            return ['status' => 'User updated'];
        }
        
    }

    // Deleting an user
    public function deleteUser($user) {
        if(Gate::denies('destroy', $this->model)) {
            abort(403);
        }

        // Unlink the current user from the roles
        $user->roles()->detach();

        if($user->delete()) {
            return ['status' => 'User deleted'];
        }
    }
    
}

?>