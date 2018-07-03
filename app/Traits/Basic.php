<?php

namespace App\Traits;

use App\Models\Setting;
use Image;
use App\Models\NotiObject;
use App\Models\Noti;
use App\Helpers\Fcm;
use App\Models\Device;

trait Basic {

    protected $languages = array(
        'ar' => 'arabic',
        'en' => 'english'
    );

    protected function inputs_check($model, $inputs = array(), $id = false, $return_errors = true) {
        $errors = array();
        foreach ($inputs as $key => $value) {
            $where_array = array();
            $where_array[] = array($key, '=', $value);
            if ($id) {
                $where_array[] = array('id', '!=', $id);
            }

            $find = $model::where($where_array)->get();

            if (count($find)) {

                $errors[$key] = array(_lang('app.' . $key) . ' ' . _lang("app.added_before"));
            }
        }

        return $errors;
    }

    public function _view($main_content, $type = 'front') {
        $main_content = "main_content/$type/$main_content";
        return view($main_content, $this->data);
    }

    protected function settings() {
        $settings = Setting::get();
        $settings[0]->noti_status = json_decode($settings[0]->noti_status);
        return $settings[0];
    }

    protected function slugsCreate() {
        $this->title_slug = 'title_' . $this->lang_code;
        $this->data['title_slug'] = $this->title_slug;
    }

    protected function create_noti($entity_id, $notifier_id, $entity_type, $notifible_type = 1) {
        $NotiObject = new NotiObject;
        $NotiObject->entity_id = $entity_id;
        $NotiObject->entity_type_id = $entity_type;
        $NotiObject->notifiable_type = $notifible_type;
        $NotiObject->save();
        $Noti = new Noti;
        $Noti->notifier_id = $notifier_id;
        $Noti->noti_object_id = $NotiObject->id;
        if ($notifier_id == null) {
            $Noti->read_status = 2;
        }
        $Noti->save();
    }

    protected function send_noti_fcm($notification, $user_id = false, $device_token = false, $device_type = false) {
        $Fcm = new Fcm;
        if ($user_id) {
            $token_and = Device::where('user_id', $user_id)
                    ->where('device_type', 1)
                    ->pluck('device_token');
            $token_ios = Device::where('user_id', $user_id)
                    ->where('device_type', 2)
                    ->pluck('device_token');
            $token_and = $token_and->toArray();
            $token_ios = $token_ios->toArray();
            //dd($token_ios);
            if (count($token_and) > 0) {
                $Fcm->send($token_and, $notification, 'and');
            } 
            if (count($token_ios) > 0) {
              
                $Fcm->send($token_ios, $notification, 'ios');
            }
        } else {
            $device_type = $device_type == 1 ? 'and' : 'ios';
            return $Fcm->send($device_token, $notification, $device_type);
        }
    }

    

    protected function lang_rules($columns_arr = array()) {
        $rules = array();

        if (!empty($columns_arr)) {
            foreach ($columns_arr as $column => $rule) {
                foreach ($this->languages as $lang_key => $locale) {
                    $key = $column . '.' . $lang_key;
                    $rules[$key] = $rule;
                }
            }
        }
        return $rules;
    }

}
