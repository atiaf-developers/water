<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use Validator;
use App\Models\News;


class NewsController extends FrontController
{
    
    public function __construct() {
        parent::__construct();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
           $news = News::Join('news_translations','news.id','=','news_translations.news_id')
                          ->where('news_translations.locale',$this->lang_code)
                          ->where('news.active',true)
                          ->orderBy('news.this_order')
                          ->select('news.id','news.images','news.created_at','news_translations.title','news_translations.description','news.slug')
                          ->paginate($this->limit);

            $news->getCollection()->transform(function($news, $key) {
                return News::transformHome($news);
            });

          $this->data['news'] =  $news;    
          return $this->_view('news.index');

        } catch (\Exception $e) {
            session()->flash('msg',_lang('app.error_is_occured_try_again_later'));
            return redirect()->back();
        }
    }

    public function show($slug)
    {
        try {
             $news = News::Join('news_translations','news.id','=','news_translations.news_id')
                          ->where('news_translations.locale',$this->lang_code)
                          ->where('news.active',true)
                          ->where('news.slug',$slug)
                          ->orderBy('news.this_order')
                          ->select('news.id','news.images','news.created_at','news_translations.title','news_translations.description','news.slug')
                          ->first();

            if (!$news) {
              return $this->_err404();
            }

            $this->data['news'] = News::transformDetailes($news); 
            return $this->_view('news.show');
        } catch (\Exception $e) {
            session()->flash('msg',_lang('app.error_is_occured_try_again_later'));
            return redirect()->back();
        }
    }

}
