<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Device;
use DB;

class RegisterController extends ApiController {

    private $rules = array(
        'step' => 'required',
        'name' => 'required',
        'account_type' => 'required',
        'email' => 'email|unique:users',
        'mobile' => 'required|unique:users',
        'password' => 'required',
        'confirm_password' => 'required|same:password',
        'device_id' => 'required',
        'device_token' => 'required',
        'device_type' => 'required',
    );
    private $verification_rules = array(
        'mobile' => 'required|unique:users',
    );

    public function __construct() {
        parent::__construct();
    }

    public function register(Request $request) {

        if ($request->step == 1) {
            $validator = Validator::make($request->all(), $this->verification_rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json(new \stdClass(), ['errors' => $errors], 400);
            }

            $verification_code = Random(4);
            $verification_code = "1234";
            return _api_json(new \stdClass(), ['code' => $verification_code]);
        } else if ($request->step == 2) {
            $validator = Validator::make($request->all(), $this->rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json(new \stdClass(), ['errors' => $errors], 400);
            }
            DB::beginTransaction();
            try {
                $user = $this->create_user($request);
                DB::commit();

                $token = new \stdClass();
                $token->id = $user->id;
                $token->device_id =$request->input('device_id');
                $token->expire = strtotime('+' . $this->expire_no . $this->expire_type);
                $expire_in_seconds = $token->expire;
                return _api_json(User::transform($user), ['token' => AUTHORIZATION::generateToken($token), 'expire' => $expire_in_seconds], 201);
            } catch (\Exception $e) {
                DB::rollback();
                $message = _lang('app.error_is_occured');
                return _api_json(new \stdClass(), ['message' => $e->getMessage()], 400);
            }
        } else {
            return _api_json(new \stdClass(), ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    private function create_user($request) {

        $User = new User;
        $User->name = $request->input('name');
        $User->account_type_id = $request->input('account_type');
        $User->mobile = $request->input('mobile');
        $User->email = $request->input('email');
        $User->password = bcrypt($request->input('password'));
        $User->image = 'default.png';
        $User->active = 1;
        $User->save();

        $Device = new Device;
        $Device->device_id = $request->input('device_id');
        $Device->device_token = $request->input('device_token');
        $Device->device_type = $request->input('device_type');
        $Device->user_id = $User->id;
        $Device->save();
        return $User;
    }

}
