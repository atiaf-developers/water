<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Setting;
use App\Models\SettingTranslation;
use App\Models\Category;
use App\Models\Location;
use App\Models\ContactMessage;
use App\Models\RejectionReason;
use App\Models\VehicleType;
use App\Helpers\Fcm;
use Carbon\Carbon;
use DB;

class BasicController extends ApiController {

    private $contact_rules = array(
        'name' => 'required',
        'email' => 'required|email',
        'message' => 'required',
        'type' => 'required',
    );
    private $raters_rules = array(
        'ad_id' => 'required',
    );
    private $basic_data_rules = array(
        'form_type' => 'required|in:1,3',
        'category_id' => 'required'
    );
    private $package_rules = array(
        'package_id' => 'required',
    );

    public function getToken(Request $request) {
        $token = $request->header('authorization');
        if ($token != null) {
            $token = Authorization::validateToken($token);
            if ($token) {
                $new_token = new \stdClass();
                $find = User::find($token->id);
                if ($find != null) {
                    $new_token->id = $find->id;
                    $new_token->expire = strtotime('+ ' . $this->expire_no . $this->expire_type);
                    $expire_in_seconds = $new_token->expire;
                    return _api_json('', ['token' => AUTHORIZATION::generateToken($new_token), 'expire' => $expire_in_seconds]);
                } else {
                    return _api_json('', ['message' => 'user not found'], 401);
                }
            } else {
                return _api_json('', ['message' => 'invalid token'], 401);
            }
        } else {
            return _api_json('', ['message' => 'token not provided'], 401);
        }
    }

    public function getSettings() {
        try {
            $settings = Setting::select('name', 'value')->get()->keyBy('name');
            $settings['social_media'] = json_decode($settings['social_media']->value);
            $settings['phone'] = explode(",", $settings['phone']->value);
            $settings['email'] = explode(",", $settings['email']->value);
            $settings['info'] = SettingTranslation::where('locale', $this->lang_code)->first();
            unset($settings['num_free_ads']);
            return _api_json($settings);
        } catch (\Exception $e) {
            return _api_json(new \stdClass(), ['message' => $e->getMessage()], 400);
        }
    }

    public function sendContactMessage(Request $request) {
        $validator = Validator::make($request->all(), $this->contact_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json('', ['errors' => $errors], 400);
        } else {
            try {
                $ContactMessage = new ContactMessage;
                $ContactMessage->name = $request->input('name');
                $ContactMessage->email = $request->input('email');
                $ContactMessage->message = $request->input('message');
                $ContactMessage->type = $request->input('type');
                $ContactMessage->save();
                return _api_json('', ['message' => _lang('app.message_is_sent_successfully')]);
            } catch (\Exception $ex) {
                return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
            }
        }
    }


    public function getVehiclesTypes(Request $request) {
        try {
            $vehicle_types = VehicleType::getAll();
            return _api_json(VehicleType::transformCollection($vehicle_types));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getRejectionReasons(Request $request) {
        try {
            $rejection_reasons = RejectionReason::getAll();
            return _api_json(RejectionReason::transformCollection($rejection_reasons));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }
    
    
}
