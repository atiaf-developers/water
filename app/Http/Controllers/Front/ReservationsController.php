<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use App\Models\Slider;
use App\Models\Category;
use App\Models\Game;
use App\Models\Reservation;

class ReservationsController extends FrontController {

    public function __construct() {
        parent::__construct();
        $this->middleware('auth');
    }

    public function index() {
        $this->data['reservations'] = $this->getReservations();
        return $this->_view('customer.reservations.index');
    }

    private function getReservations() {
        $reservations = Reservation::join('games', 'games.id', '=', 'reservations.game_id')
                ->join('games_translations as trans', 'games.id', '=', 'trans.game_id')
                ->select('games.id', "reservations.reservation_date","games.slug", "games.gallery", "trans.title", "games.price", "games.discount_price")
                ->orderBy('games.offers_order', 'ASC')
                ->where('trans.locale', $this->lang_code)
                ->where('reservations.user_id', $this->User->id)
                ->paginate($this->limit);
         $reservations->getCollection()->transform(function($reservation, $key) {
                return Reservation::transform($reservation);
            });
        return $reservations;
    }

}
