<?php 

namespace Corp\Repositories;

use Corp\Menu;
use Gate;

class MenusRepository extends Repository {

    public function __construct(Menu $menu) {

        $this->model = $menu;
        
    }

    public function addMenu($request) {

        if(Gate::denies('save', $this->model)) {
            abort(403);
        }

        $data = $request->only('type', 'title', 'parent_id');

        if(empty($data)) {
            return ['error' => 'No data'];
        }

        // dd($request->all());

        switch ($data['type']) {
            case 'customLink':
                $data['path'] = $request->input('custom_link');
                break;

            case 'blogLink':
                if($request->input('category_alias')) {
                    if($request->input('category_alias') == 'parent') {
                        $data['path'] = route('articles.index');
                    }
                    else {
                        $data['path'] = route('articlesCat', ['cat_alias' => $request->input('category_alias')]);
                    }
                }
                else if($request->input('article_alias')) {
                    $data['path'] = route('articles.show', ['alias' => $request->input('article_alias')]);
                }
                break;

            case 'portfolioLink':
                if($request->input('filter_alias') && $request->input('portfolio_alias')) {

                    if($request->input('filter_alias') != 'parent') {
                        $data['path'] = route('portfolios.show', ['alias' => $request->input('portfolio_alias')]);
                    }
                }

                else if($request->input('filter_alias') && $request->input('portfolio_alias') == 0) {
                    if($request->input('filter_alias') == 'parent') {
                        $data['path'] = route('portfolios.index');
                    }

                    else {
                        // filters page create for this
                    }

                }
                else if($request->input('portfolio_alias') && $request->input('filter_alias') == 0) {
                        $data['path'] = route('portfolios.show', ['alias' => $request->input('portfolio_alias')]);
                    }

                break;
                default: 
                    // or abort(404) or ...
                    $data['path'] = route('portfolios.index');
        
        }

        unset($data['type']);

        if($this->model->fill($data)->save()) {
            return ['status' => 'Link added'];
        }
        // dd($data);
        
    }

    public function updateMenu($request, $menu) {

        if(Gate::denies('update', $this->model)) {
            abort(403);
        }

        $data = $request->only('type', 'title', 'parent_id');

        if(empty($data)) {
            return ['error' => 'No data'];
        }

        switch ($data['type']) {
            case 'customLink':
                $data['path'] = $request->input('custom_link');
                break;

            case 'blogLink':
                if($request->input('category_alias')) {
                    if($request->input('category_alias') == 'parent') {
                        $data['path'] = route('articles.index');
                    }
                    else {
                        $data['path'] = route('articlesCat', ['cat_alias' => $request->input('category_alias')]);
                    }
                }
                else if($request->input('article_alias')) {
                    $data['path'] = route('articles.show', ['alias' => $request->input('article_alias')]);
                }
                break;

            case 'portfolioLink':
                if($request->input('filter_alias') && $request->input('portfolio_alias')) {

                    if($request->input('filter_alias') != 'parent') {
                        $data['path'] = route('portfolios.show', ['alias' => $request->input('portfolio_alias')]);
                    }
                }

                else if($request->input('filter_alias') && $request->input('portfolio_alias') == 0) {
                    if($request->input('filter_alias') == 'parent') {
                        $data['path'] = route('portfolios.index');
                    }

                    else {
                        // filters page create for this
                    }

                }
                else if($request->input('portfolio_alias') && $request->input('filter_alias') == 0) {
                        $data['path'] = route('portfolios.show', ['alias' => $request->input('portfolio_alias')]);
                    }

                break;
                default: 
                    // or abort(404) or ...
                    $data['path'] = route('portfolios.index');
        
        }

        unset($data['type']);

        if($menu->fill($data)->update()) {
            return ['status' => 'Link updated'];
        }
        
    }



    public function deleteMenu($menu) {
        if(Gate::denies('delete', $this->model)) {
            abort(403);
        }

        if($menu->delete()) {
            return ['status' => 'Link deleted'];
        }
    }
}

?>