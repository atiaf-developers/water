<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Device;

class LoginController extends ApiController {

    private $rules = array(
        'username' => 'required',
        'password' => 'required',
        'device_id' => 'required',
        'device_token' => 'required',
        'device_type' => 'required',
    );
    private $social_rules = array(
        'social_id' => 'required',
        'social_type' => 'required',
        'device_id' => 'required',
        'device_token' => 'required',
        'device_type' => 'required',
    );

    public function login(Request $request) {
        if ($request->type == 1) {
            unset($this->rules['password']);
        }
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json(new \stdClass(), ['errors' => $errors], 400);
        } else {
            $credentials = $request->only('username', 'password', 'type');
            if ($user = $this->auth_check($credentials)) {
                $token = new \stdClass();
                $token->id = $user->id;
                $token->device_id = $request->input('device_id');
                $token->expire = strtotime('+' . $this->expire_no . $this->expire_type);
                $expire_in_seconds = $token->expire;
                //dd($user->id);
                Device::updateOrCreate(
                        ['device_id' => $request->input('device_id'), 'user_id' => $user->id], ['device_token' => $request->input('device_token'), 'device_type' => $request->input('device_type')]
                );

                $user = User::transform($user);
                return _api_json($user, ['message' => _lang('app.login_done_successfully'), 'token' => AUTHORIZATION::generateToken($token), 'expire' => $expire_in_seconds]);
            }
            return _api_json(new \stdClass(), ['message' => _lang('app.invalid_credentials')], 400);
        }
    }

    public function social(Request $request) {

        $validator = Validator::make($request->all(), $this->social_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json(new \stdClass(), ['errors' => $errors], 400);
        } else {
            try {
                if (!in_array($request->social_type, [1, 2, 3])) {
                    return _api_json(new \stdClass(), ['message' => _lang('app.error_is_occured')], 400);
                }
                //dd($this->check_social($request));
                if ($user = $this->check_social($request)) {
                    Device::updateOrCreate(
                            ['device_id' => $request->input('device_id'), 'user_id' => $user->id], ['device_token' => $request->input('device_token'), 'device_type' => $request->input('device_type')]
                    );
                } else {
                    $user = $this->create_social_user($request);
                }
                $token = new \stdClass();
                $token->id = $user->id;
                $token->device_id = $request->input('device_id');
                $token->expire = strtotime('+' . $this->expire_no . $this->expire_type);
                $expire_in_seconds = $token->expire;


                $user = User::transform($user);
                return _api_json($user, ['message' => _lang('app.login_done_successfully'), 'token' => AUTHORIZATION::generateToken($token), 'expire' => $expire_in_seconds]);
            } catch (\Exception $ex) {
                return _api_json(new \stdClass(), ['message' => $ex->getMessage()], 400);
            }
        }
    }

    private function auth_check($credentials) {

        $find = User::join('account_types', 'account_types.id', '=', 'users.account_type_id')
                ->join('account_types_translations', 'account_types.id', '=', 'account_types_translations.account_type_id')
                ->where(function ($query) use($credentials) {
                    $query->where('users.email', $credentials['username']);
                    $query->orWhere('users.mobile', $credentials['username']);
                })
                ->select('users.*', 'account_types_translations.title as accountTypeTitle')
                ->where('users.active', 1)
                ->where('account_types_translations.locale', $this->lang_code)
                ->first();
        if ($find) {
            if (isset($credentials['password'])) {
                if (password_verify($credentials['password'], $find->password)) {
                    return $find;
                } else {
                    return false;
                }
            }
            return $find;
        }
        return false;
    }

    private function check_social($request) {

        $find = User::leftJoin('account_types', 'account_types.id', '=', 'users.account_type_id')
                ->leftJoin('account_types_translations', function ($join) {
                    $join->on('account_types.id', '=', 'account_types_translations.account_type_id')
                    ->where('account_types_translations.locale', $this->lang_code);
                })
                ->where(function ($query) use($request) {
                    $query->where('users.facebook_id', $request->input('social_id'));
                    $query->orWhere('users.twitter_id', $request->input('social_id'));
                    $query->orWhere('users.google_id', $request->input('social_id'));
                    $query->orWhere('users.email', $request->input('email'));
                })
                ->select('users.*', 'account_types_translations.title as accountTypeTitle')
                //->where('users.active', 1)
                ->first();
        if ($find) {
            return $find;
        }
        return false;
    }

    private function create_social_user($request) {

        $User = new User;
        $User->name = $request->input('name');
        $User->mobile = $request->input('mobile');
        $User->email = $request->input('email');
        if ($request->input('social_type') == 1) {
            $User->facebook_id = $request->input('social_id');
        }
        if ($request->input('social_type') == 2) {
            $User->twitter_id = $request->input('social_id');
        }
        if ($request->input('social_type') == 3) {
            $User->google_id = $request->input('social_id');
        }

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
