<?php

namespace Corp\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Corp\Http\Controllers\Controller;
use Corp\Http\Controllers\Admin\AdminController;
use Gate;

class IndexController extends AdminController
{
    //
    public function __construct() {
        parent::__construct();

        // Check for permission to the user to the next view - VIEW_ADMIN
        if(Gate::denies('VIEW_ADMIN')){

            abort(403);
        }

        $this->template = env('THEME').'.admin.index';
    }

    public function index() {
        $this->title = 'Admin Panel';

        return $this->renderOutput();
    }
}
