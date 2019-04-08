<?php

namespace Corp\Http\Controllers;

use Illuminate\Http\Request;
use Corp\Http\Requests;

use Corp\Repositories\SlidersRepository;
use Corp\Repositories\PortfoliosRepository;
use Corp\Repositories\ArticlesRepository;

use Config;


class IndexController extends SiteController
{   

    public function __construct(SlidersRepository $s_rep, PortfoliosRepository $p_rep, ArticlesRepository $a_rep) {
        parent::__construct(new \Corp\Repositories\MenusRepository(new \Corp\Menu));

        $this->s_rep = $s_rep;
        $this->p_rep = $p_rep;
        $this->a_rep = $a_rep;

        // According to template styles
        $this->bar = 'right';

        // According to the theme of the template
        $this->template = env('THEME').'.index';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Portfolio
        $portfolios = $this->getPortfolio();
        $content = view(env('THEME').'.content')->with('portfolios', $portfolios)->render();
        $this->vars = array_add($this->vars, 'content', $content);

        // Slider
        $sliderItems = $this->getSliders();
        $sliders = view(env('THEME').'.slider')->with('sliders', $sliderItems)->render();
        $this->vars = array_add($this->vars, 'sliders', $sliders);

        // Articles
        $articles = $this->getArticles();
        $this->contentRightBar = view(env('THEME').'.indexBar')->with('articles', $articles)->render();

        $this->keywords = "Home Page";
        $this->meta_desc = "Home Page";
        $this->title = "Home Page";

        return $this->renderOutput(); 
    }

    protected function getArticles() {

        $articles = $this->a_rep->get(['title', 'created_at', 'img', 'alias'], Config::get('settings.home_articles_count'))->sortByDesc('created_at');
        return $articles;
    }

    protected function getPortfolio() {

        $portfolio = $this->p_rep->get('*', Config::get('settings.home_port_count'))->sortByDesc('created_at');

        return $portfolio;
    }

    public function getSliders() {

        $sliders = $this->s_rep->get();

        if($sliders->isEmpty()) {
            return FALSE;
        }
        else {
            $sliders->transform(function($item, $key) {
                $item->img = Config::get('settings.slider_path').'/'.$item->img;
                return $item;
            });
        }

        // dd($sliders);

        return $sliders;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
