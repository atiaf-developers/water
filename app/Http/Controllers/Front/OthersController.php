<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use Validator;
use App\Models\Category;


class OthersController extends FrontController
{
    
    public function __construct() {
        parent::__construct();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($slug)
    {

     
        try {
           $category = Category::Join('categories_translations','categories.id','=','categories_translations.category_id')
                          ->where('categories_translations.locale',$this->lang_code)
                          ->where('categories.active',true)
                          ->where('categories.slug',$slug)
                          ->where('categories.parent_id',0)
                          ->select('categories.id','categories_translations.title','categories.slug')
                          ->first();

            if (!$category) {
               return $this->err404();
            }
           $categories = Category::Join('categories_translations','categories.id','=','categories_translations.category_id')
                                  ->where('categories_translations.locale',$this->lang_code)
                                  ->where('categories.active',true)
                                  ->where('categories.parent_id',$category->id)
                                  ->orderBy('categories.this_order')
                                  ->select('categories.slug','categories_translations.title','categories_translations.description','categories.image')
                                  ->paginate($this->limit);

          $this->data['category'] = $category; 
          $this->data['categories'] = $categories; 
          return $this->_view('others.index');

        } catch (\Exception $e) {
            session()->flash('msg',_lang('app.error_is_occured_try_again_later'));
            return redirect()->back();
        }
    }

    public function show($section,$slug)
    {
        try {

           $section = Category::Join('categories_translations','categories.id','=','categories_translations.category_id')
                          ->where('categories_translations.locale',$this->lang_code)
                          ->where('categories.active',true)
                          ->where('categories.slug',$section)
                          ->where('categories.parent_id',0)
                          ->select('categories.id','categories_translations.title','categories.slug')
                          ->first();

            if (!$section) {
               return $this->err404();
            }
             

            $category = Category::Join('categories_translations','categories.id','=','categories_translations.category_id')
                                  ->where('categories_translations.locale',$this->lang_code)
                                  ->where('categories.active',true)
                                  ->where('categories.slug',$slug)
                                  ->where('categories.parent_id','!=',0)
                                  ->orderBy('categories.this_order')
                                  ->select('categories_translations.title','categories_translations.description','categories.image')
                                  ->first();

            $category->image =  substr($category->image, strpos($category->image, '_') + 1);

            if (!$category) {
              return $this->_err404();
            }

            $this->data['category'] = $category;
            return $this->_view('others.show');
        } catch (\Exception $e) {
          
            session()->flash('msg',_lang('app.error_is_occured_try_again_later'));
            return redirect()->back();
        }
    }

}
