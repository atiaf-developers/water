<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Device;
use DB;

class RegisterController extends ApiController {

    private $client_rules_step_one = array(
        'step' => 'required',
        'type' => 'required',
        'mobile' => 'required|unique:users,mobile,NULL,id,deleted_at,NULL',
    );
    private $client_rules_step_two = array(
        'step' => 'required',
        'mobile' => 'required|unique:users,mobile,NULL,id,deleted_at,NULL',
        'username' => 'required|unique:users,username,NULL,id,deleted_at,NULL',
        'email' => 'email|unique:users,email,NULL,id,deleted_at,NULL',
        'password' => 'required',
        'confirm_password' => 'required|same:password',
        'device_id' => 'required',
        'device_token' => 'required',
        'device_type' => 'required',
    );
    private $delegate_rules_step_one = array(
        'step' => 'required',
        'type' => 'required',
        'mobile' => 'required|unique:users,mobile,NULL,id,deleted_at,NULL',
    );
    private $delegate_rules_step_two = array(
        'step' => 'required',
        'type' => 'required',
        'vehicle_image' => 'required',
        'license_image' => 'required',
        'plate_letter_ar' => 'required',
        'plate_letter_en' => 'required',
        'plate_num_ar' => 'required',
        'plate_num_en' => 'required',
        'name' => 'required',
        'username' => 'required|unique:users,username,NULL,id,deleted_at,NULL',
        'email' => 'email|unique:users,email,NULL,id,deleted_at,NULL',
        'mobile' => 'required|unique:users,mobile,NULL,id,deleted_at,NULL',
        'password' => 'required',
        'confirm_password' => 'required|same:password',
    );

    public function __construct() {
        parent::__construct();
    }

    public function register(Request $request) {

        if ($request->type == 1) {
            if ($request->step == 1) {
                $rules = $this->client_rules_step_one;
            } else if ($request->step == 2) {
                $rules = $this->client_rules_step_two;
            } else {
                return _api_json(new \stdClass(), ['message' => _lang('app.error_is_occured')], 400);
            }
        } else if ($request->type == 2) {
            if ($request->step == 1) {
                $rules = $this->delegate_rules_step_one;
            } else if ($request->step == 2) {
                $rules = $this->delegate_rules_step_two;
            } else {
                return _api_json(new \stdClass(), ['message' => _lang('app.error_is_occured')], 400);
            }
        } else {
            return _api_json(new \stdClass(), ['message' => _lang('app.error_is_occured')], 400);
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json(new \stdClass(), ['errors' => $errors], 400);
        }
        DB::beginTransaction();
        try {
            if ($request->type == 1) {
                if ($request->step == 1) {
                    //$verification_code = (int) Random(4);
                    $verification_code = 1234;
                    return _api_json(new \stdClass(), ['code' => $verification_code]);
                } else if ($request->step == 2) {
                    $user = $this->createClient($request);
                }
            } else if ($request->type == 2) {
                if ($request->step == 1) {
                    //$verification_code = (int) Random(4);
                    $verification_code = 1234;
                    return _api_json(new \stdClass(), ['code' => $verification_code]);
                } else if ($request->step == 2) {
                    $user = $this->createDelegate($request);
                }
            }


            //dd($user);
            $token = new \stdClass();
            $token->id = $user->id;
            $token->expire = strtotime('+' . $this->expire_no . $this->expire_type);
            $token->device_id = $request->input('device_id');
            $expire_in_seconds = $token->expire;
            DB::commit();
            return _api_json(new \stdClass(), ['token' => AUTHORIZATION::generateToken($token), 'expire' => $expire_in_seconds], 201);
        } catch (\Exception $e) {
            //dd($e);
            DB::rollback();
            $message = _lang('app.error_is_occured');
            return _api_json(new \stdClass(), ['message' => $e->getMessage()], 400);
        }
    }

    private function createClient($request) {
        //user
        $User = new User;
        $User->mobile = $request->input('mobile');
        $User->username = $request->input('username');
        $User->email = $request->input('email');
        $User->password = bcrypt($request->input('password'));
        $User->type = 1;
        $User->image = "default.png";
        $User->active = 1;
        $User->save();

        //device
        Device::updateOrCreate(
                ['device_id' => $request->input('device_id')], ['user_id' => $User->id, 'device_token' => $request->input('device_token'), 'device_type' => $request->input('device_type')]
        );
//        $Device = new Device;
//        $Device->device_id = $request->input('device_id');
//        $Device->device_token = $request->input('device_token');
//        $Device->device_type = $request->input('device_type');
//        $Device->user_id = $User->id;
//        $Device->save();


        return $User;
    }

    private function createDelegate($request) {
        //user
        $User = new User;
        $User->name = $request->input('name');
        $User->username = $request->input('username');
        $User->email = $request->input('email');
        $User->password = bcrypt($request->input('password'));
        $User->type = $request->type;
        $User->image = "default.png";
        $User->active = 0;
        $User->save();

        //vehicle
        $Vehicle = new Vehicle;
        $Vehicle->plate_letter_ar = $request->input('plate_letter_ar') ? $request->input('plate_letter_ar') : '';
        $Vehicle->plate_letter_en = $request->input('plate_letter_en') ? $request->input('plate_letter_en') : '';
        $Vehicle->plate_num_ar = $request->input('plate_num_ar') ? $request->input('plate_num_ar') : '';
        $Vehicle->plate_num_en = $request->input('plate_num_en') ? $request->input('plate_num_en') : '';
        $vehicle_image = preg_replace("/\r|\n/", "", $request->input('vehicle_image'));
        $license_image = preg_replace("/\r|\n/", "", $request->input('license_image'));
        if (isBase64image($vehicle_image)) {
            $Vehicle->vehicle_image = Vehicle::upload($vehicle_image, 'vehicles', true, false, true);
        }
        if (isBase64image($license_image)) {
            $Vehicle->license_image = Vehicle::upload($license_image, 'vehicles', true, false, true);
        }
        $Vehicle->delegate_id = $User->id;
        $Vehicle->save();

        //device
        Device::updateOrCreate(
                ['device_id' => $request->input('device_id')], ['user_id' => $User->id, 'device_token' => $request->input('device_token'), 'device_type' => $request->input('device_type')]
        );
//        $Device = new Device;
//        $Device->device_id = $request->input('device_id');
//        $Device->device_token = $request->input('device_token');
//        $Device->device_type = $request->input('device_type');
//        $Device->user_id = $User->id;
//        $Device->save();

        return $User;
    }

}
