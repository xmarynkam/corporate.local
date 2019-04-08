<?php

namespace Corp\Http\Controllers;

use Illuminate\Http\Request;

use Corp\Http\Requests;

use Corp\Repositories\MenusRepository;

use Menu;


class SiteController extends Controller
{
    // For storage of the PortfolioRepository class
    protected $p_rep;

    // For storage of the SliderRepository class
    protected $s_rep;

    // For storage of the ArticlesRepository class
    protected $a_rep;

    // For storage of the MenuRepository class
    protected $m_rep;

    // Page keywords
    protected $keywords;

    // Page description in tags <meta>
    protected $meta_desc;

    // Page title
    protected $title;

    // To store the template name for a specific page.
    protected $template;

    // An array of variables that will be passed to the template
    protected $vars = array();


    // The presence of the right sitebar
    protected $contentRightBar =FALSE;

    // The presence of the left sitebar
    protected $contentLeftBar =FALSE;

    // Indicates the presence of sitebar
    protected $bar = 'no';



    public function __construct(MenusRepository $m_rep) {
        $this->m_rep = $m_rep;
    }

    protected function renderOutput() {

        $menu = $this->getMenu();

        $navigation = view(env('THEME').'.navigation')->with('menu', $menu)->render();

        $this->vars = array_add($this->vars, 'navigation', $navigation);

        if($this->contentRightBar) {
            $rightBar = view(env('THEME').'.rightBar')->with('content_rightBar', $this->contentRightBar)->render();

            $this->vars = array_add($this->vars, 'rightBar', $rightBar);
        }

        if($this->contentLeftBar) {
            $leftBar = view(env('THEME').'.leftBar')->with('content_leftBar', $this->contentLeftBar)->render();

            $this->vars = array_add($this->vars, 'leftBar', $leftBar);
        }

        $this->vars = array_add($this->vars, 'bar', $this->bar);

        // Footer
        $footer = view(env('THEME').'.footer')->render();
        $this->vars = array_add($this->vars, 'footer', $footer);        

        // Page keywords
        $this->vars = array_add($this->vars, 'keywords', $this->keywords);

        // Page description in tags <meta>
        $this->vars = array_add($this->vars, 'meta_desc', $this->meta_desc);

        // Page title
        $this->vars = array_add($this->vars, 'title', $this->title);



        return view($this->template)->with($this->vars);
    }

    public function getMenu() {

        $menu = $this->m_rep->get();        

        $mBuilder = Menu::make('MyNav', function($m) use ($menu) {
            
            foreach($menu as $item) {
                if($item->parent_id == 0) {
                    $m->add($item->title, $item->path)->id($item->id);
                }
                else {
                    if($m->find($item->parent_id)) {
                        $m->find($item->parent_id)->add($item->title, $item->path)->id($item->id);
                    }
                }
            }

        });

        // dd($mBuilder);

        return $mBuilder;        
    }
}
