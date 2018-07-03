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
use App\Models\AccountType;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Device;
use App\Models\Noti;
use App\Helpers\Fcm;
use Carbon\Carbon;
use DB;

class BasicController extends ApiController {

    private $contact_rules = array(
        'message' => 'required',
        'email' => 'required|email',
        'type' => 'required',
        'name' => 'required'
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
                    $new_token->device_id = $token->device_id;
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
            if(isset($settings['phone'])){
                $settings['phone']->value= explode(',', $settings['phone']->value);
            }
            if(isset($settings['email'])){
                $settings['email']->value= explode(',', $settings['email']->value);
            }
            $settings['info'] = SettingTranslation::where('locale', $this->lang_code)->first();

            return _api_json($settings);
        } catch (\Exception $e) {
            return _api_json(new \stdClass(), ['message' => $e->getMessage()], 400);
        }
    }

   

  

    public function home() {
        try {
            $user = $this->auth_user();
            $categories = Category::join('categories_translations', 'categories.id', '=', 'categories_translations.category_id')
                    ->where('categories_translations.locale', $this->lang_code)
                    ->where('categories.active', 1)
                    ->orderBy('categories.this_order')
                    ->select("categories.id", "categories_translations.title")
                    ->get();
            $data['categories']=$categories;
            $data['offers']=Product::getProductsApi(['user'=>$user,'option'=>'offers']);
            $data['latest']=Product::getProductsApi(['user'=>$user],'products.created_at','desc');
            $data['site_url']='';
            return _api_json($data);
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }
    public function getCategories() {
        try {
            $categories = Category::join('categories_translations', 'categories.id', '=', 'categories_translations.category_id')
                    ->where('categories_translations.locale', $this->lang_code)
                    ->where('categories.active', 1)
                    ->orderBy('categories.this_order')
                    ->select("categories.id", "categories_translations.title")
                    ->get();

            return _api_json(Category::transformCollection($categories));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }
    public function getAccountTypes() {
        try {
            $account_types = AccountType::join('account_types_translations', 'account_types.id', '=', 'account_types_translations.account_type_id')
                    ->where('account_types_translations.locale', $this->lang_code)
                    ->where('account_types.active', 1)
                    ->orderBy('account_types.this_order')
                    ->select("account_types.id", "account_types_translations.title")
                    ->get();

            return _api_json(AccountType::transformCollection($account_types));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }
    public function getBranches() {
        try {
            $branches = Branch::join('branches_translations', 'branches.id', '=', 'branches_translations.branch_id')
                    ->where('branches_translations.locale', $this->lang_code)
                    ->where('branches.active', 1)
                    ->orderBy('branches.this_order')
                    ->select("branches.id","branches.email","branches.mobile","branches.phone", "branches.lat","branches.lng","branches_translations.title")
                    ->get();

            return _api_json(Branch::transformCollection($branches));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    

}
