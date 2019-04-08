<?php 
    
namespace Corp\Repositories;

use Corp\Article;
use Gate;
use Image;
use Config;

class ArticlesRepository extends Repository {

    public function __construct(Article $article) {

        $this->model = $article;
        
        
    }

    // Returns data from one instance from the table of the Article model
    public function one($alias, $attr = array()) {

    	$article = parent::one($alias, $attr);

    	if($article && !empty(@attr)) {
    		$article->load('comments');
    		$article->comments->load('user');
    	}

        return $article;
    }

    // Edit article
    public function updateArticle($request, $article) {
        if(Gate::denies('edit', $this->model)) {
            abort(403);
        }

        $data = $request->except('_token', 'image', '_method');

        if(empty($data)) {
            return array('error' => 'No data');
        }

        // If the field 'alias' is empty
        if(empty($data['alias'])) {
            // Creates an alias of an article, if it does not
            $data['alias'] = $this->transliterate($data['title']);
        }

        // The check or alias is unique
        $result = $this->one($data['alias'], FALSE);

        if(isset($result->id) && $result->id != $article->id) {
            $request->merge(array('alias' => $data['alias']));

            // Saving all data stored in the $request object in session
            $request->flash();

            return ['error' => 'This alias already exists'];
        }

        // Checks whether the file has been downloaded
        if($request->hasFile('image')) {
            $image = $request->file('image');

            if($image->isValid()) {
                // Randomly generated string to specify file names
                $str = str_random(8);

                // Will contain a JSON string
                $obj = new \stdClass;

                $obj->mini = $str.'_mini.jpg';
                $obj->max = $str.'_max.jpg';
                $obj->path = $str.'.jpg';


                $img = Image::make($image);

                // Resize the image - name path
                $img->fit(Config::get('settings.image')['width'], 
                          Config::get('settings.image')['height'])->save(public_path().'/'.env('THEME').'/images/articles/'.$obj->path);

                // Resize the image - name max
                $img->fit(Config::get('settings.articles_img')['max']['width'], 
                          Config::get('settings.articles_img')['max']['height'])->save(public_path().'/'.env('THEME').'/images/articles/'.$obj->max);

                // Resize the image - name mini
                $img->fit(Config::get('settings.articles_img')['mini']['width'], 
                          Config::get('settings.articles_img')['mini']['height'])->save(public_path().'/'.env('THEME').'/images/articles/'.$obj->mini);
                $data['img'] = json_encode($obj);

                

            }
        }

        $article->fill($data);

        if($article->update()) {
            return ['status' => 'Article updated'];
        }

    }

    // Deleting an article
    public function deleteArticle($article) {
        if(Gate::denies('destroy', $article)) {
            abort(403);
        }

        $article->comments()->delete();

        if($article->delete()) {
            return ['status' => 'Article deleted'];
        }
    }

    // Adding an article
    public function addArticle($request) {
        if(Gate::denies('save', $this->model)) {
            abort(403);
        }

        $data = $request->except('_token', 'image');

        if(empty($data)) {
            return array('error' => 'No data');
        }

        // If the field 'alias' is empty
        if(empty($data['alias'])) {
            // Creates an alias of an article, if it does not
            $data['alias'] = $this->transliterate($data['title']);
        }

        // If the generated alias already exists in the database
        if($this->one($data['alias'], FALSE)) {
            $request->merge(array('alias' => $data['alias']));

            // Saving all data stored in the $request object in session
            $request->flash();

            return ['error' => 'This alias already exists'];
        }

        // Checks whether the file has been downloaded
        if($request->hasFile('image')) {
            $image = $request->file('image');

            if($image->isValid()) {
                // Randomly generated string to specify file names
                $str = str_random(8);

                // Will contain a JSON string
                $obj = new \stdClass;

                $obj->mini = $str.'_mini.jpg';
                $obj->max = $str.'_max.jpg';
                $obj->path = $str.'.jpg';


                $img = Image::make($image);

                // Resize the image - name path
                $img->fit(Config::get('settings.image')['width'], 
                          Config::get('settings.image')['height'])->save(public_path().'/'.env('THEME').'/images/articles/'.$obj->path);

                // Resize the image - name max
                $img->fit(Config::get('settings.articles_img')['max']['width'], 
                          Config::get('settings.articles_img')['max']['height'])->save(public_path().'/'.env('THEME').'/images/articles/'.$obj->max);

                // Resize the image - name mini
                $img->fit(Config::get('settings.articles_img')['mini']['width'], 
                          Config::get('settings.articles_img')['mini']['height'])->save(public_path().'/'.env('THEME').'/images/articles/'.$obj->mini);
                $data['img'] = json_encode($obj);

                

            }
        }
        // If the image is not loaded
        else {
            // Will contain a JSON string
            $obj = new \stdClass;

            $obj->mini = Config::get('settings.no_image_name') .'_mini.jpg';
            $obj->max = Config::get('settings.no_image_name').'_max.jpg';
            $obj->path = Config::get('settings.no_image_name').'.jpg';

            $data['img'] = json_encode($obj);
        }

        $this->model->fill($data);

        if($request->user()->articles()->save($this->model)) {
            return ['status' => 'Article added'];
        }

    }

}

?>