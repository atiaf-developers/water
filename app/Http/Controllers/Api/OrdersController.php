<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AUTHORIZATION;
use App\Models\Order;
use App\Models\OrderDriver;
use App\Models\Device;
use DB;

class OrdersController extends ApiController {

    private $new_order_rules = array(
        'driver' => 'required',
        'lat' => 'required',
        'lng' => 'required',
        'payment_method' => 'required',
        'price' => 'required',
        'commission' => 'required',
        'taxes' => 'required',
        'delivery_cost' => 'required',
        'total_price' => 'required',
    );
    private $change_driver_rules = array(
        'driver' => 'required',
    );
    private $order_status_rules = array(
        'order' => 'required',
        'status' => 'required',
    );

    public function __construct() {
        parent::__construct();
    }

    public function index(Request $request) {
        try {
            $user = $this->auth_user();
            //dd($user->type);
            $where_array = array();
            if ($user) {
                if ($user->type == 1) {
                    $where_array['client'] = $user->id;
                } else if ($user->type == 2) {
                    $where_array['driver'] = $user->id;
                }
            } else {
                $where_array = ['device_id' => $request->input('device_id')];
            }
            $orders = Order::getOrdersApi($where_array);
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

    public function store(Request $request) {

        try {
            $user = $this->auth_user();
            $order = Order::find($request->input('id'));
            $rules = [];
            if ($order) {
                $rules = $this->change_driver_rules;
            } else {
                $rules = $this->new_order_rules;
            }
            if(!$user){
                $rules['device_id']='required';
                $rules['device_type']='required';
                $rules['device_token']='required';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json(new \stdClass(), ['errors' => $errors], 400);
            } else {

                DB::beginTransaction();

                if (!$order) {
                    $order = new Order;
                    if ($user) {
                        $order->client_id = $user->id;
                    } else {
                        $device = Device::updateOrCreate(
                                        ['device_id' => $request->input('device_id')], ['device_token' => $request->input('device_token'), 'device_type' => $request->input('device_type')]
                        );
                        $order->device_id = $device->id;
                    }

                    $order->payment_method = $request->input('payment_method');
                    $order->price = $request->input('price');
                    $order->taxes = $request->input('taxes');
                    $order->delivery_cost = $request->input('delivery_cost');
                    $order->total_price = $request->input('total_price');
                    $order->commission = $request->input('commission');
                    $order->lat = $request->input('lat');
                    $order->lng = $request->input('lng');
                    $order->date = date('Y-m-d');
                    $order->save();
                }
                $OrderDriver = new OrderDriver;
                $OrderDriver->order_id = $order->id;
                $OrderDriver->driver_id = $request->input('driver');
                $OrderDriver->status = 0;
                $OrderDriver->save();
                DB::commit();

                return _api_json('', ['message' => _lang('app.sent_successfully'), 'order_id' => $order->id]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $e->getMessage() . $e->getLine()], 400);
        }
    }

    public function changeOrderStatus(Request $request) {
        $user = $this->auth_user();

        $OrderDriver = OrderDriver::where('order_id', $request->order)->where('driver_id', $user->id)->first();
        $status = $request->status;
        if (!$OrderDriver) {
            return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
        }
        if ($status == 3) {
            $this->rules['rejection_reason'] = 'required';
        }
        $validator = Validator::make($request->all(), $this->order_status_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json('', ['errors' => $errors], 422);
        } else {
            DB::beginTransaction();
            try {

                if ($OrderDriver) {
                    $OrderDriver->status = $request->status;
                    $OrderDriver->save();
                    $waiting_driver_orders_count = $user->orders()->where('status', 0)->count();
                    if ($status == 3) {
                        DB::table('orders')
                                ->where('id', $OrderDriver->order_id)
                                ->update(['rejection_reason_id' => $request->rejection_reason]);
                    }
                    if ($waiting_driver_orders_count > 0) {
                        DB::table('orders_drivers')
                                ->where('status', 0)
                                ->where('driver_id', $user->id)
                                ->update(['status' => 2]);
                    }
                }




                DB::commit();
                return _api_json('', ['message' => _lang('app.status_changed')], 201);

                return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
            } catch (\Exception $ex) {
                dd($ex);
                DB::rollback();
                return _api_json('', ['message' => $ex->getMessage() . $ex->getLine() . $ex->getFile()], 400);
            }
        }
    }

}
