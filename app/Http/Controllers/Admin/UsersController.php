<?php

namespace Corp\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Corp\Http\Controllers\Controller;
use Corp\Http\Controllers\Admin\AdminController;
use Corp\Repositories\RolesRepository;
use Corp\Repositories\UsersRepository;

use Gate;
use Corp\Http\Requests\UserRequest;
use Corp\User;

class UsersController extends AdminController
{
    public function __construct(RolesRepository $rol_rep, UsersRepository $us_rep) {
        parent::__construct();

        // Check for permission 
        if(Gate::denies('ADMIN_USERS')) {
            abort(403);
        }

        $this->rol_rep = $rol_rep;
        $this->us_rep = $us_rep;

        $this->template = env('THEME').'.admin.users';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = $this->us_rep->get();

        $this->content = view(env('THEME').'.admin.users_content')->with(['users' => $users])->render();
        return $this->renderOutput();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $this->title = 'New user';

        $roles = $this->getRoles()->reduce(function($returnRoles, $role) {
            $returnRoles[$role->id] = $role->name;
            return $returnRoles;
        }, []);

        $this->content = view(env('THEME').'.admin.users_create_content')->with('roles', $roles)->render();
        return $this->renderOutput();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $result = $this->us_rep->addUser($request);

        if(is_array($result) && !empty($result['error'])) {

            return back()->with($result);
        }
        
        return redirect('/admin/users')->with($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if(Gate::denies('edit', new User)) {
            abort(403);
        }

        $this->title = 'Edit user - ' . $user->name;

        $roles = $this->getRoles()->reduce(function($returnRoles, $role) {
            $returnRoles[$role->id] = $role->name;
            return $returnRoles;
        }, []);

        $this->content = view(env('THEME').'.admin.users_create_content')->with(['roles' =>  $roles, 'user' => $user])->render();
        return $this->renderOutput();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        $result = $this->us_rep->updateUser($request, $user);

        if(is_array($result) && !empty($result['error'])) {
            return back()->with($result);
        }
        
        return redirect('/admin/users')->with($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $result = $this->us_rep->deleteUser($user);
        
        if(is_array($result) && !empty($result['error'])) {
            return back()->with($result);
        }
        
        return redirect('/admin/users')->with($result);
    }

    public function getRoles() {
        return \Corp\Role::all();
    }
}
