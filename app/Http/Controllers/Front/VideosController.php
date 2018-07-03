<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use Validator;
use App\Models\Video;


class VideosController extends FrontController
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
         $videos = Video::Join('videos_translations','videos.id','=','videos_translations.video_id')
                                   ->where('videos_translations.locale',$this->lang_code)
                                   ->where('videos.active',true)
                                   ->orderBy('videos.this_order')
                                   ->select("videos.id","videos_translations.title",'videos.url','videos.youtube_url')
                                   ->paginate($this->limit);
          $this->data['videos'] =  $videos;
          return $this->_view('videos.index');

        } catch (\Exception $e) {
            session()->flash('msg',_lang('app.error_is_occured_try_again_later'));
            return redirect()->back();
        }
    }

  

}
