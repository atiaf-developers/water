<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use Validator;
use App\Models\Album;


class AlbumsController extends FrontController
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
          $albums = Album::Join('albums_translations','albums.id','=','albums_translations.album_id')
                                   ->where('albums_translations.locale',$this->lang_code)
                                   ->where('albums.active',true)
                                   ->orderBy('albums.this_order')
                                   ->select("albums.id","albums_translations.title",'albums.slug')
                                   ->paginate($this->limit);

           $albums->getCollection()->transform(function($album, $key) {
                return Album::transformHome($album);
            });

          $this->data['albums'] =  $albums;
          return $this->_view('albums.index');

        } catch (\Exception $e) {
            session()->flash('msg',_lang('app.error_is_occured_try_again_later'));
            return redirect()->back();
        }
    }

    public function show($slug)
    {
        try {
           $album = Album::Join('albums_translations','albums.id','=','albums_translations.album_id')
                                   ->where('albums_translations.locale',$this->lang_code)
                                   ->where('albums.slug',$slug)
                                   ->where('albums.active',true)
                                   ->orderBy('albums.this_order')
                                   ->select("albums.id","albums_translations.title",'albums.slug')
                                   ->first();

            $this->data['album'] = Album::transformDetailes($album); 
            return $this->_view('albums.show');
        } catch (\Exception $e) {
            session()->flash('msg',_lang('app.error_is_occured_try_again_later'));
            return redirect()->back();
        }
    }

}
