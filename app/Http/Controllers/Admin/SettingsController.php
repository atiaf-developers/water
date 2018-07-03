<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\BackendController;
use App\Models\Setting;
use App\Models\Supervisor;
use App\Models\SettingTranslation;
use DB;

class SettingsController extends BackendController {

   
    private $rules = array(
        'setting.email' => 'required|email',
        'setting.phone' => 'required',
        'setting.commission' => 'required',
        'setting.tax' => 'required',
        'setting.map_range' => 'required',
    );

    public function index() {

        $this->data['settings'] = Setting::get()->keyBy('name');
        $this->data['settings']['about'] = json_decode($this->data['settings']['about']->value);
        dd($this->data['settings']);
        return $this->_view('settings/index', 'backend');
    }


    public function store(Request $request) {
  
        $validator = Validator::make($request->all(), $this->lang_rules(['setting.about'=>'required']));
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        DB::beginTransaction();
        try {

            $settings = Setting::get()->keyBy('name');
            $setting = $request->input('setting');

            $data_update = [];
         

            foreach ($setting as $key => $value) {
                if ($key == 'about') {
                    $value = json_encode($value);
                }
                $data_update['value'][] = [
                    'value' => $value,
                    'cond' => [['name', '=', "'$key'"]],
                ];
            }
        
            $this->updateValues2('\App\Models\Setting', $data_update, true);

            DB::commit();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            dd($ex);
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    public function store2(Request $request) {

        if ($request->file('setting.about_video_url')) {
            $this->rule['setting.about_video_url'] = 'required|mimes:mp4';
        }
        if ($request->file('setting.declarative_video_url')) {
            $this->rule['setting.declarative_video_url'] = 'required|mimes:mp4';
        }
        $columns_arr = array(
            'about_text' => 'required',
        );
        $this->rules = array_merge($this->rules, $this->lang_rules($columns_arr));
        $validator = Validator::make($request->all(), $this->rules);


        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        //dd($request->all());

        DB::beginTransaction();
        try {

            $setting = $request->input('setting');
            $old_settings = Setting::get()->keyBy('name');

            if ($request->file('setting.about_video_url')) {
                $name = Setting::upload_simple($request->file('setting.about_video_url'), 'videos');
                $setting['about_video_url'] = $name;
            } else if (!$request->input('setting.about_video_url')) {
                $setting['about_video_url'] = $old_settings['about_video_url']->value;
                $setting['youtube_url'] = $old_settings['youtube_url']->value;
            }

            if ($request->file('setting.declarative_video_url')) {
                $name = Setting::upload_simple($request->file('setting.declarative_video_url'), 'videos');
                $setting['declarative_video_url'] = $name;
            } else if (!$request->input('setting.declarative_video_url')) {
                $setting['declarative_video_url'] = $old_settings['declarative_video_url']->value;
                $setting['declarative_video_youtube_url'] = $old_settings['declarative_video_youtube_url']->value;
            }

            if ($request->file('setting.muzdalifah_supervisor.image')) {
                $name = Supervisor::upload($request->file('setting.muzdalifah_supervisor.image'), 'supervisors');
                $setting['muzdalifah_supervisor']['image'] = $name;
            }

            if ($request->file('setting.mena_supervisor.image')) {
                $name = Supervisor::upload($request->file('setting.mena_supervisor.image'), 'supervisors');
                $setting['mena_supervisor']['image'] = $name;
            }

            if ($request->file('setting.arafat_supervisor.image')) {
                $name = Supervisor::upload($request->file('setting.arafat_supervisor.image'), 'supervisors');
                $setting['arafat_supervisor']['image'] = $name;
            }
            foreach ($setting as $key => $value) {

                if (in_array($key, ['social_media', 'mena_supervisor', 'muzdalifah_supervisor', 'arafat_supervisor'])) {
                    Setting::updateOrCreate(
                            ['name' => $key], ['value' => json_encode($value)]);
                } else {
                    Setting::updateOrCreate(
                            ['name' => $key], ['value' => $value]);
                }
            }
            $about = $request->input('about_text');
            foreach ($about as $key => $value) {
                SettingTranslation::updateOrCreate(
                        ['locale' => $key], [
                    'locale' => $key, 'about_text' => $about[$key]
                ]);
            }
            DB::commit();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            dd($ex);
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

}
