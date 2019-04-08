<?php

namespace Corp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->canDo('ADD_ARTICLES');
    }

    /**
     * Get the validator instance for the request.
     *
     * @return Illuminate\Foundation\Http\FormRequest;
     */
    protected function getValidatorInstance() {
        /**
         * Get the validator instance for the request.
         *
         * @return \Illuminate\Contracts\Validation\Validator
         */

        // 1. Validation alias when editing article when creating an article
        $validator = parent::getValidatorInstance();

        $validator->sometimes('alias', 'unique:articles|max:255', function($input) {
            
            // 2. Check your alias when editing an article
            if($this->route()->hasParameter('article')) {

                $model = $this->route()->parameter('article');                
                
                return ($model->alias !== $input->alias)  && !empty($input->alias);
            }
            
            return !empty($input->alias);
        });

        return $validator;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            //
            'title' => 'required|max:255',
            'text' => 'required',
            'category_id' => 'required|integer'
        ];
    }
}
