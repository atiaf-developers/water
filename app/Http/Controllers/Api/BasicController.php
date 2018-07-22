<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Setting;
use App\Models\Vehicle;
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
            $settings = Setting::select('name', 'value')->get();
            $new=array();
            if($settings->count()>0){
                foreach($settings as $one){
                    if($one->name=='about'|| $one->name == 'rating_message_active' || $one->name == 'rating_message_not_active'){
                        $value=json_decode($one->value);
                        $one->value=$value->{$this->lang_code};
                    }
                    if($one->name=='phone'||$one->name=='email'){
                        $one->value=explode(',',$one->value);
                    }
                    $new[$one->name]=$one->value;
                }
            }
            return _api_json($new);
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
            $vehicle_types = VehicleType::getAllApi();
            return _api_json(VehicleType::transformCollection($vehicle_types));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getRejectionReasons(Request $request) {
        try {
            $rejection_reasons = RejectionReason::getAllApi();
            return _api_json(RejectionReason::transformCollection($rejection_reasons));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }
    
    
}
