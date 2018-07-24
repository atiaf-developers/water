<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BackendController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Pagination\LengthAwarePaginator;
use App;
use Auth;
use DB;
use Redirect;
use Validator;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderDriver;
use Config;

class OrdersReportsController extends BackendController {

    private $limit = 10;

    public function __construct() {


        parent::__construct();
        $this->middleware('CheckPermission:orders_reports,open', ['only' => ['index']]);
    }

    public function index(Request $request) {

        if ($request->all()) {
            foreach ($request->all() as $key => $value) {

                if ($value) {
                    $this->data[$key] = $value;
                }
            }
        }
        $this->data['orders'] = Order::getOrdersAdmin($request->all());
        //dd( $this->data['orders']);
        $this->data['info'] = Order::getOrdersInfoAdmin($request->all());
        $this->data['status_arr'] = OrderDriver::$status_arr;
//        $this->data['users'] = $this->getUsers();
        return $this->_view('orders_reports.index', 'backend');
    }

    public function show(Request $request, $id) {
        $order = Order::getOrdersAdmin(['order_id'=>$id]);
        if (!$order) {
            return $this->err404();
        }
      
        $this->data['order'] = $order;

        return $this->_view('orders_reports/view', 'backend');
    }

      public function closed($id) {
        $Order = Order::where('id', $id)->first();
        try {

            $Order->closed = ($Order->closed == 1) ? 0 : 1;
            $Order->save();
            return _json('success', '');
        } catch (\Exception $ex) {
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    private function getUsers() {
        $Users = User::select('id', "name")->get();
        return $Users;
    }

}
