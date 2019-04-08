<?php

namespace Corp\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Corp\Http\Controllers\Controller;
use Corp\Repositories\MenusRepository;
use Corp\Repositories\ArticlesRepository;
use Corp\Repositories\PortfoliosRepository;
use Gate;
use Menu;
use Corp\Http\Requests\MenusRequest;


class MenusController extends AdminController
{
    public function __construct(MenusRepository $m_rep, ArticlesRepository $a_rep, PortfoliosRepository $p_rep) {
        parent::__construct();

        // Check for permission to the user to the next view - VIEW_ADMIN
        if(Gate::denies('VIEW_ADMIN_MENU')) {
            abort(403);
        }

        $this->m_rep = $m_rep;    
        $this->a_rep = $a_rep;
        $this->p_rep = $p_rep;

        $this->template = env('THEME').'.admin.menus';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu = $this->getMenus();

        $this->content = view(env('THEME').'.admin.menus_content')->with('menus', $menu)->render();

        return $this->renderOutput();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Title page
        $this->title = 'New item menu';

        // Menu array for html select
        $tmp = $this->getMenus()->roots();
        $menus = $tmp->reduce(function($returnMenus, $menu) {

            $returnMenus[$menu->id] = $menu->title;
            return $returnMenus;

        }, ['0' => 'Parent item menu']);

        // Categories array for html select
        $categories = \Corp\Category::select(['title', 'alias', 'parent_id','id'])->get();

        $list = array();
        $list = array_add($list, '0', 'Not used');
        $list = array_add($list, 'parent', 'Blog section');

        foreach ($categories as $category) {
            if($category->parent_id == 0) {
                $list[$category->title] = array();

            }
            else {
                $list[$categories->where('id', $category->parent_id)->first()->title][$category->alias] = $category->title;
            }
        }

        // Articles array for html select
        $articles = $this->a_rep->get(['id', 'title', 'alias']);

        $articles = $articles->reduce(function($returnArticles, $article) {

            $returnArticles[$article->alias] = $article->title;
            return $returnArticles;

        }, []);

        // Filters array for html select
        $filters = \Corp\Filter::select('id', 'title', 'alias')->get()->reduce(function($returnFilters, $filter) {

            $returnFilters[$filter->alias] = $filter->title;
            return $returnFilters;

        }, ['parent' => 'Portfolio section']);

        // Portfolio array for html select
        $portfolios = $this->p_rep->get(['id', 'title', 'alias'])->reduce(function($returnPortfolios, $portfolio) {

            $returnPortfolios[$portfolio->alias] = $portfolio->title;
            return $returnPortfolios;

        }, []);


        // Content page
        $this->content = view(env('THEME').'.admin.menus_create_content')->with(['menus' => $menus, 'categories' => $list, 'articles' => $articles, 'filters' => $filters, 'portfolios' => $portfolios])->render();

        return $this->renderOutput();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MenusRequest $request)
    {
        $result = $this->m_rep->addMenu($request);

        if(is_array($result) && !empty($result['error'])) {

            return back()->with($result);
        }
        
        return redirect('/admin/menus')->with($result);
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
    public function edit(\Corp\Menu $menu)
    {   
        
        // Type of menu item
        $type = FALSE;

        // For backlight active option with a html-select
        $option = FALSE;

        // Title page
        $this->title = 'Edit item menu - ' . $menu->title;

        // path - http://corporate.loc/admin/menus/17/edit
        // From this link you need to get route
        // Route (uri: "admin/menus/{menu}/edit) - app('router')->getRoutes()->match(app('request'))

        // Get a route that is being edited
        $route = app('router')->getRoutes()->match(app('request')->create($menu->path));

        // To save the alias of the route, which corresponds to the path of the link to be edited
        $aliasRoute = $route->getName();

        // An array of route parameters that corresponds to the path of the link to be edited
        $parameters = $route->parameters();

        if($aliasRoute == 'articles.index' || $aliasRoute == 'articlesCat') {
            $type = 'blogLink';
            $option = isset($parameters['cat_alias']) ? $parameters['cat_alias'] : 'parent';
        }

        else if($aliasRoute == 'articles.show') {
            $type = 'blogLink';
            $option = isset($parameters['alias']) ? $parameters['alias'] : '';
        }

        else if($aliasRoute == 'portfolios.index') {
            $type = 'portfolioLink';
            $option = 'parent';
        }

        else if($aliasRoute == 'portfolios.show') {
            $type = 'portfolioLink';
            $option = isset($parameters['alias']) ? $parameters['alias'] : '';
        }

        else {
            $type = 'customLink';
        }

        dump($option);

        // Menu array for html select
        $tmp = $this->getMenus()->roots();
        $menus = $tmp->reduce(function($returnMenus, $menu) {

            $returnMenus[$menu->id] = $menu->title;
            return $returnMenus;

        }, ['0' => 'Parent item']);

        // Categories array for html select
        $categories = \Corp\Category::select(['title', 'alias', 'parent_id','id'])->get();

        $list = array();
        $list = array_add($list, '0', 'Not used');
        $list = array_add($list, 'parent', 'Blog section');

        foreach ($categories as $category) {
            if($category->parent_id == 0) {
                $list[$category->title] = array();

            }
            else {
                $list[$categories->where('id', $category->parent_id)->first()->title][$category->alias] = $category->title;
            }
        }

        // Articles array for html select
        $articles = $this->a_rep->get(['id', 'title', 'alias']);

        $articles = $articles->reduce(function($returnArticles, $article) {

            $returnArticles[$article->alias] = $article->title;
            return $returnArticles;

        }, []);

        // Filters array for html select
        $filters = \Corp\Filter::select('id', 'title', 'alias')->get()->reduce(function($returnFilters, $filter) {

            $returnFilters[$filter->alias] = $filter->title;
            return $returnFilters;

        }, ['parent' => 'Portfolio section']);

        // Portfolio array for html select
        $portfolios = $this->p_rep->get(['id', 'title', 'alias'])->reduce(function($returnPortfolios, $portfolio) {

            $returnPortfolios[$portfolio->alias] = $portfolio->title;
            return $returnPortfolios;

        }, []);


        // Content page
        $this->content = view(env('THEME').'.admin.menus_create_content')->with(['menu' => $menu, 'type' => $type, 'option' => $option, 'menus' => $menus, 'categories' => $list, 'articles' => $articles, 'filters' => $filters, 'portfolios' => $portfolios])->render();

        return $this->renderOutput();
        // dd($menu);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, \Corp\Menu $menu)
    {
        //
        $result = $this->m_rep->updateMenu($request, $menu);

        if(is_array($result) && !empty($result['error'])) {

            return back()->with($result);
        }
        
        return redirect('/admin/menus')->with($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(\Corp\Menu $menu)
    {
        $result = $this->m_rep->deleteMenu($menu);

        if(is_array($result) && !empty($result['error'])) {

            return back()->with($result);
        }
        
        return redirect('/admin/menus')->with($result);
    
    }

    public function getMenus()
    {
        $menu = $this->m_rep->get();

        if($menu->isEmpty()) {
            return FALSE;
        }

        return Menu::make('forMenuPart', function($m) use ($menu) {
            foreach ($menu as $item) {
                if($item->parent_id == 0) {
                    // Parent menu item
                    $m->add($item->title, $item->path)->id($item->id);
                }
                else {
                    // Child menu item
                    if($m->find($item->parent_id)) {
                        $m->find($item->parent_id)->add($item->title, $item->path)->id($item->id);
                    }
                }
            }
        });
    }
}
