<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\BackendController;
use App\Models\Setting;
use App\Models\SettingTranslation;
use DB;

class SettingsController extends BackendController {

    private $rules = array(
        'setting.email' => 'required', 'setting.phone' => 'required',
        'setting.site_url' => 'required',
     
   
    );

    public function index() {

        $this->data['settings'] = Setting::get()->keyBy('name');

        $this->data['settings_translations'] = SettingTranslation::get()->keyBy('locale');
        return $this->_view('settings/index', 'backend');
    }

    public function store(Request $request) {

        $columns_arr = array(
            'about' => 'required',
            'policy' => 'required',
        );

        $this->rules = array_merge($this->rules, $this->lang_rules($columns_arr));
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } else {

            DB::beginTransaction();
            try {
                $setting = $request->input('setting');
                //dd($setting);
                foreach($setting as $key=>$value){
                        Setting::updateOrCreate(
                            ['name' => $key], ['name' => $key,'value' => $value]);
                }
                $about = $request->input('about');
                $policy = $request->input('policy');
                foreach ($about as $key => $value) {
                    SettingTranslation::updateOrCreate(
                            ['locale' => $key], [
                                'locale' => $key, 'about' => $about[$key],'policy' => $policy[$key]
                            ]);
                }
                DB::commit();
                return _json('success', _lang('app.updated_successfully'));
            } catch (\Exception $ex) {
                DB::rollback();
                dd($ex->getMessage());
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }



}
