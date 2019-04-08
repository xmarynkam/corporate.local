<?php

namespace Corp\Http\Controllers;

use Illuminate\Http\Request;

use Corp\Repositories\PortfoliosRepository;
use Corp\Repositories\ArticlesRepository;
use Corp\Repositories\CommentsRepository;

use Corp\Category;

class ArticlesController extends SiteController
{
    public function __construct(PortfoliosRepository $p_rep, ArticlesRepository $a_rep, CommentsRepository $c_rep) {
        parent::__construct(new \Corp\Repositories\MenusRepository(new \Corp\Menu));
        
        $this->p_rep = $p_rep;
        $this->a_rep = $a_rep;
        $this->c_rep = $c_rep;

        // According to template styles
        $this->bar = 'right';

        // According to the theme of the template
        $this->template = env('THEME').'.articles';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($cat_alias = FALSE)
    {
        $this->title = 'Блог';
        $this->keywords = 'блог, статті';
        $this->meta_desc = 'Блог сайту з цікавими статтями';
        // Articles
        $articles = $this->getArticles($cat_alias);

        $content = view(env('THEME').'.articles_content')->with('articles', $articles)->render();
        $this->vars = array_add($this->vars, 'content', $content);

        $comments = $this->getComments(config('settings.resent_comments'));

        $portfolios = $this->getPortfolios(config('settings.resent_portfolios'));

        $this->contentRightBar = view(env('THEME').'.articlesBar')->with(['comments' => $comments, 'portfolios' => $portfolios]);
        
        return $this->renderOutput();
    }

    public function show($alias = FALSE) {

        $article = $this->a_rep->one($alias, ['comments' => TRUE]);


        if($article) {
            $article->img = json_decode($article->img);
        }

        if(isset($article->id)) {
            $this->title = $article->title;
            $this->keywords = $article->keywords;
            $this->meta_desc = $article->meta_desc;

            $content = view(env('THEME').'.article_content')->with('article', $article)->render();
            $this->vars = array_add($this->vars, 'content', $content);
        }

        $comments = $this->getComments(config('settings.resent_comments'));

        $portfolios = $this->getPortfolios(config('settings.resent_portfolios'));

        $this->contentRightBar = view(env('THEME').'.articlesBar')->with(['comments' => $comments, 'portfolios' => $portfolios]);

        
        return $this->renderOutput();
    }



    public function getComments($take) {
        $comments = $this->c_rep->get(['text', 'name', 'email', 'site', 'article_id', 'user_id'], $take)->sortByDesc('created_at');
        if($comments) {
            $comments->load('article', 'user');
        }

        return $comments;
    }

    public function getPortfolios($take) {
        $portfolios = $this->p_rep->get(['title', 'text', 'alias', 'customer', 'img', 'filter_alias'], $take)->sortByDesc('created_at');
        return $portfolios;
    }

    protected function getArticles($alias = FALSE) {

        $where = FALSE;

        if($alias) {
            // WHERE `alias` = $alias
            $id = Category::select('id')->where('alias', $alias)->first()->id;
            // WHERE category_id = $id
            $where = ['category_id', $id];
        }

        $articles = $this->a_rep->get(['id', 'title', 'created_at', 'img', 'alias', 'desc', 'user_id', 'category_id', 'keywords', 'meta_desc'], FALSE, TRUE, $where);

        if($articles) {

            // dd($articles);
            $articles->load('user', 'category', 'comments')->sortByDesc('created_at');
        }

        return $articles;
    }


}
