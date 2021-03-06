<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Device;
use App\Models\Order;
use App\Models\OrderDriver;
use Validator;
use DB;

class UserController extends ApiController {

    private $complete_data_rules = array(
        'vehicle_type' => 'required',
        'vehicle_weight' => 'required',
        'price' => 'required',
        'license_number' => 'required'
    );
    private $check_driver_price_rules = array(
        'vehicle_weight' => 'required',
        'price' => 'required',
    );

    public function __construct() {
        parent::__construct();
    }

    public function show() {
        $User = $this->auth_user();
        return _api_json(User::transform($User));
    }

    public function update(Request $request) {
        $User = $this->auth_user();
        $rules = array();
        if ($request->input('name')) {
            $rules['name'] = "required";
        }
        if ($request->input('email')) {
            $rules['email'] = "required|email|unique:users,email,$User->id,id,deleted_at,NULL";
        }
        if ($request->input('username')) {
            $rules['username'] = "required|unique:users,username,$User->id,id,deleted_at,NULL";
        }
        if ($request->input('mobile')) {
            $rules['mobile'] = "required|unique:users,mobile,$User->id,id,deleted_at,NULL";
        }
        if ($request->input('old_password')) {
            $rules['password'] = "required";
            $rules['confirm_password'] = "required|same:password";
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json(new \stdClass(), ['errors' => $errors], 400);
        } else {

            DB::beginTransaction();
            try {
                if ($request->input('name')) {
                    $User->name = $request->input('name');
                }
                if ($request->input('email')) {
                    $User->email = $request->input('email');
                }
                if ($request->input('username')) {
                    $User->username = $request->input('username');
                }
                if ($request->input('mobile')) {
                    if ($request->step == 1) {
                        $verification_code = Random(4);
                        return _api_json(new \stdClass(), ['code' => $verification_code]);
                    } else if ($request->step == 2) {
                        $User->mobile = $request->input('mobile');
                    } else {
                        $message = _lang('app.error_is_occured');
                        return _api_json(new \stdClass(), ['message' => $message], 400);
                    }
                }

                if ($old_password = $request->input('old_password')) {
                    if (!password_verify($old_password, $User->password)) {
                        return _api_json(new \stdClass(), ['message' => _lang('app.invalid_old_password')], 400);
                    } else {
                        $User->password = bcrypt($request->input('password'));
                    }
                }
                unset($User->device_id);
                $User->save();
                if ($request->input('price')) {
                    $User->vehicle->price = $request->input('price');
                }
                $User->vehicle->save();
                $User = User::transform($User);
                DB::commit();
                return _api_json($User, ['message' => _lang('app.updated_successfully')]);
            } catch (\Exception $e) {
                $message = _lang('app.error_is_occured');
                return _api_json(new \stdClass(), ['message' => $e->getMessage()], 400);
            }
        }
    }

    public function complete_data(Request $request) {
        $validator = Validator::make($request->all(), $this->complete_data_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json(new \stdClass(), ['errors' => $errors], 400);
        } else {

            DB::beginTransaction();
            try {

                $User = $this->auth_user();
                $vehicle = Vehicle::where('delegate_id', $User->id)->first();
                $vehicle->vehicle_type_id = $request->input('vehicle_type');
                $vehicle->vehicle_weight_id = $request->input('vehicle_weight');
                $vehicle->price = $request->input('price');
                $vehicle->license_number = $request->input('license_number');
                $vehicle->save();
                $User = User::transform($User);
                DB::commit();
                return _api_json($User, ['message' => _lang('app.updated_successfully')]);
            } catch (\Exception $e) {
                $message = _lang('app.error_is_occured');
                return _api_json(new \stdClass(), ['message' => $e->getMessage()], 400);
            }
        }
    }

    public function getAuthUser() {
        try {
            $user = User::transform($this->auth_user());
            return _api_json($user);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json(new \stdClass(), ['message' => $message], 400);
        }
    }

    public function updateLocation(Request $request) {
        try {
            $user = $this->auth_user();
            $vehicle = Vehicle::where('driver_id', $user->id)->first();
            if ($vehicle) {
                $vehicle->lat = $request->lat;
                $vehicle->lng = $request->lng;
                $vehicle->save();
            }

            return _api_json('');
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
        }
    }

    public function changeDriverStatus(Request $request) {
        try {

            $user = $this->auth_user();
            $current_order = Order::getOrdersApi(['driver' => $user->id, 'status' => OrderDriver::$user_status['driver']['current']]);
            //dd($current_order);
            if ($current_order->count() > 0) {
                return _api_json('', ['message' => _lang('app.you_have_an_order_running')]);
            }
            $vehicle = Vehicle::where('driver_id', $user->id)->first();
            //dd($vehicle);
            if ($vehicle) {
                $vehicle->is_ready = $vehicle->is_ready == 1 ? 0 : 1;
                $vehicle->save();
            }

            return _api_json('');
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
        }
    }

    public function checkDriverPrice(Request $request) {
        $validator = Validator::make($request->all(), $this->check_driver_price_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json(new \stdClass(), ['errors' => $errors], 400);
        } else {

            try {
                $vehicle = Vehicle::where('vehicle_weight_id', $request->input('vehicle_weight'))
                        ->where('price', '<', $request->input('price'))
                        ->first();
                $vehicle_obj = new \stdClass();
                if($vehicle){
                     $vehicle_obj->price=$vehicle->price;
                }
               
                return _api_json($vehicle_obj);
            } catch (\Exception $e) {
                $message = _lang('app.error_is_occured');
                return _api_json(new \stdClass(), ['message' => $e->getMessage()], 400);
            }
        }
    }

    public function getNearestDrivers(Request $request) {
        try {
//            dd(137*0.621);
            //dd(GetDrivingDistance(30.01548800, 31.24428800, 30.03727100, 30.03727100));
//            dd(GetDrivingDistance(30.03727100, 30.03727100,30.01548800, 31.24428800));
            $where_array=['lat' => $request->lat, 'lng' => $request->lng, 'vehicle_type' => $request->vehicle_type];
            if($request->price_from){
                $where_array['price_from']=$request->price_from;
            }
            if($request->price_to){
                $where_array['price_to']=$request->price_to;
            }
            $drivers = User::getAllNearestDriversApi($where_array);
            return _api_json($drivers);
        } catch (\Exception $e) {
            dd($e);
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function logout() {
        //dd($this->auth_user()->device_id);
        Device::where('user_id', $this->auth_user()->id)->where('device_id', $this->auth_user()->device_id)->update(['device_token' => '']);
        return _api_json(new \stdClass(), array(), 201);
    }

}
