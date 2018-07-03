<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use App\Models\Slider;
use App\Models\Category;
use App\Models\Game;

class PropertyController extends FrontController {

    public function __construct() {
        parent::__construct();
    }

    public function categories() {
        $this->data['categories'] = $this->getCategories('categories');
        return $this->_view('property.categories');
    }

    public function category_details($category_slug) {
        $category = $this->getCategories('category_details');
        if (!$category) {
            return $this->err404();
        }
        $this->data['games'] = $this->getGames('category_details');
        $this->data['category'] = $category;
        return $this->_view('property.category_details');
    }

    public function games(Request $request) {
        $this->data['games'] = $this->getGames('games_page');
        $this->data['filter_categories'] = $this->getFilterCategories();
        if ($request->all()) {
            foreach ($request->all() as $key => $value) {
                if ($value) {
                    $this->data[$key] = $value;
                }
            }
        }
        return $this->_view('property.games');
    }

    public function game_details($game_slug) {
        $game = $this->getGames('game_details');
        if (!$game) {
            return $this->err404();
        }
        //dd($game);
        $this->data['game'] = $game;
        return $this->_view('property.game_details');
    }

    public function game_reserve($game_slug) {
        $game = $this->getGames('game_details');
        $this->data['game'] = $game;
        return $this->_view('property.game_reserve');
    }

    private function getFilterCategories() {
        return Category::join('categories_translations as trans', 'categories.id', '=', 'trans.category_id')
                        ->select('categories.id', "categories.slug", "trans.title")
                        ->orderBy('categories.this_order', 'ASC')
                        ->where('trans.locale', $this->lang_code)
                        ->where('categories.active', 1)
                        ->get();
    }

}
