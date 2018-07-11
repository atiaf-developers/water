<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\DesignerCategory;
use App\Models\Designer;
use App\Models\Device;
use Validator;
use DB;

class UserController extends ApiController {

    private $location_rules = array(
        'lat' => 'required',
        'lng' => 'required'
    );

    public function __construct() {
        parent::__construct();
    }

    public function show() {
        $User = $this->auth_user();
        return _api_json(User::transform($User));
    }

    protected function update(Request $request) {
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
                if ($request->input('account_type')) {
                    $User->account_type_id = $request->input('account_type');
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

  

    public function logout() {
        //dd($this->auth_user()->device_id);
        Device::where('user_id', $this->auth_user()->id)->where('device_id', $this->auth_user()->device_id)->update(['device_token' => '']);
        return _api_json(new \stdClass(), array(), 201);
    }

}
