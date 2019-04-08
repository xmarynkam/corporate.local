<?php 

namespace Corp\Repositories;

use Config;

abstract class Repository {

    protected $model = FALSE;

    public function get($select = '*', $take = FALSE, $pagination = FALSE, $where = FALSE) {

        $builder = $this->model->select($select);
        
        if($take) {
            $builder->take($take);
        }

        if($where) {
            $builder->where($where[0], $where[1]);
        }

        if($pagination) {
            return $this->check($builder->paginate(Config::get('settings.paginate')));
        }

        return $this->check($builder->get());
    }

    // Returns data from one instance from the table of the specified model
    public function one($alias, $attr = array()) {

        $result = $this->model->where('alias', $alias)->first();

        return $result;
    }

    // A collection of models - $result
    protected function check($result) {

        
        if($result->isEmpty()) {
            return FALSE;
        }

        
        $result->transform(function($item, $key) {

            if(is_string($item->img) && is_object(json_decode($item->img)) && (json_last_error() == JSON_ERROR_NONE)) {

                $item->img = json_decode($item->img);
            }

            return $item;
        });

        // dd($result);
        return $result;
    }

    // Conducts a transliterated string translation
    public function transliterate($string) {

        $str = mb_strtolower($string, 'UTF-8');

        $letter_array = array(
            'a' => 'а',
            'b' => 'б',
            'v' => 'в',
            'g' => 'г,ґ',
            'd' => 'д',
            'e' => 'е,є,э',
            'jo' => 'ё',
            'zh' => 'ж',
            'z' => 'з',
            'i' => 'и,і',
            'ji' => 'ї',
            'j' => 'й',
            'k' => 'к',
            'l' => 'л',
            'm' => 'м',
            'n' => 'н',
            'o' => 'о',
            'p' => 'п',
            'r' => 'р',
            's' => 'с',
            't' => 'т',
            'u' => 'у',
            'f' => 'ф',
            'kh' => 'х',
            'ts' => 'ц',
            'ch' => 'ч',
            'sh' => 'ш',
            'shch' => 'щ',
            '' => 'ъ',
            'y' => 'ы',
            '' => 'ь',
            'yu' => 'ю',
            'ya' => 'я',
        );

        foreach ($letter_array as $letter => $kyr) {
            $kyr = explode(',', $kyr);

            $str = str_replace($kyr, $letter , $str);
        }

        // a-z A-Z 0-9 - spaces  
        // All other characters will be replaced with the dash
        $str = preg_replace('/(\s|[^A-Za-z0-9\-])+/', '-', $str);

        $str = trim($str, '-');
        return $str;
    }
}

?>