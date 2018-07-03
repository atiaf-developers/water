<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AUTHORIZATION;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Address;
use DB;

class OrdersController extends ApiController {

    public function __construct() {
        parent::__construct();
    }

    public function index(Request $request) {
        try {
            $user = $this->auth_user();
            $orders = Order::getOrdersApi(['user_id' => $user->id]);
            if ($orders->count() > 0) {
                foreach ($orders as $order) {
                    $order->details = Order::getOrderDetailsApi($order->id);
                }
            }
            return _api_json($orders);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json([], ['message' => $e->getMessage()], 400);
        }
    }

    public function show($id) {
        try {
            $user = $this->auth_user();
            $orders = Order::getOrdersApi(['user_id' => $user->id, 'order_id' => $id]);
            if (!$orders) {
                return _api_json(new \stdClass(), ['message' => _lang('app.resource_not_found')], 404);
            }
            $orders->details = Order::getOrderDetailsApi($orders->id);
            return _api_json($orders);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json(new \stdClass(), ['message' => $e->getMessage()], 400);
        }
    }
    
    public function downloadCard($id){
        $data = [
           'foo' => 'bar'
       ];
       $filename = 'card';
       $pdf = PDF::loadView('main_content.backend.pdf.card', $data);
       return $pdf->stream('card.pdf');
    }

    public function store(Request $request) {
        DB::beginTransaction();
        try {
            $cart = json_decode($request->input('cart'));
            //dd($cart);
            if (!$cart) {
                return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
            }
            $address = $this->create_address($cart->address);
            //dd($address);
            $cart->info->user_id = $this->auth_user()->id;
            $cart->info->address_id = $address->id;
            $order = $this->create_order($cart->info);
            $order_details = array();
            if (count($cart->details) > 0) {
                foreach ($cart->details as $one) {
                    $order_details[] = array(
                        'order_id' => $order->id,
                        'product_id' => $one->product_id,
                        'price' => $one->price,
                        'quantity' => $one->quantity,
                        'total_price' => $one->total_price
                    );
                    $this->updatequantity($one->product_id, $one->quantity);
                }
                OrderDetail::insert($order_details);
            }
            DB::commit();
            return _api_json('', ['message' => _lang('app.sent_successfully'),'order_id'=>$order->id]);
        } catch (\Exception $e) {
            DB::rollback();
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $e->getMessage() . $e->getLine()], 400);
        }
    }

    private function create_address($address_obj) {

        $address = new Address;
        $address->city = $address_obj->city;
        $address->region = $address_obj->region;
        $address->street = $address_obj->street;
        $address->building = $address_obj->building;
        $address->lat = $address_obj->lat;
        $address->lng = $address_obj->lng;
        $address->save();
        return $address;
    }

    private function create_order($order_obj) {

        $order = new Order;
        $order->user_id = $order_obj->user_id;
        $order->branch_id = $order_obj->branch_id;
        $order->address_id = $order_obj->address_id;
        $order->payment_method = $order_obj->payment_method;
        $order->total_price = $order_obj->total_price;
        $order->date = date('Y-m-d');

        $order->save();
        return $order;
    }
    private function updatequantity($id,$quantity) {
        $sql = "UPDATE products SET quantity = quantity-$quantity WHERE id = $id ";
        DB::statement($sql);
    }

}
