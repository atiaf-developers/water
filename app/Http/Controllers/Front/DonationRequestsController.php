<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use App\Models\DonationType;
use Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use DB;
use App\Models\DonationRequest;

class DonationRequestsController extends FrontController {

    private $rules = array(
        'auth' => array(
            'step_one_rules' => array(
                'donation_type' => 'required',
                'description' => 'required',
                'appropriate_time' => 'required',
                'images.*' => 'required|image|mimes:gif,png,jpeg',
            ),
            'step_two_rules' => array(
                'donation_type' => 'required',
                'description' => 'required',
                'appropriate_time' => 'required',
                'images.*' => 'required|image|mimes:gif,png,jpeg',
                'lat' => 'required',
                'lng' => 'required',
            ),
        ),
        'guest' => array(
            'step_one_rules' => array(
                'donation_type' => 'required',
                'description' => 'required',
                'appropriate_time' => 'required',
                'images.*' => 'required|image|mimes:gif,png,jpeg',
            ),
            'step_two_rules' => array(
                'lat' => 'required',
                'lng' => 'required',
                'name' => 'required',
                'mobile' => 'required',
            ),
            'step_three_rules' => array(
                'donation_type' => 'required',
                'description' => 'required',
                'appropriate_time' => 'required',
                'images.*' => 'required|image|mimes:gif,png,jpeg',
                'lat' => 'required',
                'lng' => 'required',
                'name' => 'required',
                'mobile' => 'required',
                'code.*' => 'required',
            ),
        ),
    );
    private $step_one_rules = array(
        'donation_type' => 'required',
        'description' => 'required',
        'appropriate_time' => 'required',
        'images.*' => 'required|image|mimes:gif,png,jpeg',
    );
    private $step_two_rules = array(
        'lat' => 'required',
        'lng' => 'required',
        'name' => 'required',
        'mobile' => 'required',
    );
    private $step_three_rules = array(
        'code.*' => 'required',
    );

    public function __construct() {
        parent::__construct();
    }

    public function showDonationRequestForm() {
        $this->data['donation_types'] = DonationType::Join('donation_types_translations', 'donation_types.id', '=', 'donation_types_translations.donation_type_id')
                ->where('donation_types_translations.locale', $this->lang_code)
                ->where('donation_types.active', true)
                ->orderBy('donation_types.this_order', 'asc')
                ->select("donation_types.id", "donation_types_translations.title")
                ->get();
        //dd( $this->data['donation_types']);
        return $this->_view('donation_requests.index');
    }

    public function submitDonationRequestForm(Request $request) {
        //dd($request->all());
//        $mobile = '966' + $request->input('step');
//        $request->merge(['mobile' => $mobile]);
        $step = $request->input('step');
        //dd($step);
        if (!$this->User) {
            if ($step == 1) {
                $rules = $this->rules['guest']['step_one_rules'];
            } else if ($step == 2) {
                $rules = $this->rules['guest']['step_two_rules'];
            } else if ($step == 3) {
                $rules = $this->rules['guest']['step_three_rules'];
            } else {
                return _json('error', _lang('app.error_is_occured'));
            }
        } else {
            if ($step == 1) {
                $rules = $this->rules['auth']['step_one_rules'];
            } else if ($step == 2) {
                $rules = $this->rules['auth']['step_two_rules'];
            } else {
                return _json('error', _lang('app.error_is_occured'));
            }
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $this->errors = $validator->errors()->toArray();
            return _json('error', $this->errors);
        }
        if ($step == 1) {
            $images = $request->file('images');
            if ($images) {
                if (count($images) > 4) {
                    return _json('error', ['images[]' => [_lang('app.maximum_images_is_4')]]);
                }
            } else {
                return _json('error', ['images[]' => [_lang('app.please_upload_one_image_at_least')]]);
            }

            return _json('success', ['step' => $step]);
        }

        if (!$this->User) {
            if ($step == 2) {
                $activation_code = Random(4);
                $message = _lang('app.verification_code_is') . ' ' . $activation_code;
                return _json('success', ['step' => $step, 'activation_code' => $activation_code]);
            }
            if ($step == 3) {
                $form_code = implode('', $request->input('code'));
                $ajax_code = $request->input('ajax_code');
                //dd($ajax_code);
                if ($ajax_code != $form_code) {
                    return _json('error', ['activation_code' => [_lang('app.code_is_wrong')]]);
                }
                DB::beginTransaction();
                try {
                    $this->create_donation_request($request);
                    DB::commit();
                    $message = _lang('app.the_delegate_will_come_to_you_to_receive_your_donation_request');
                    return _json('success', ['step' => $step, 'message' => $message]);
                } catch (\Exception $ex) {
                    DB::rollback();
                    dd($ex->getMessage());
                    $message = _lang('app.error_is_occured');
                    return _json('error', $message);
                }
            }
        } else {
            DB::beginTransaction();
            try {
                $this->create_donation_request($request);
                DB::commit();
                $message = _lang('app.the_delegate_will_come_to_you_to_receive_your_donation_request');
                return _json('success', ['step' => $step, 'message' => $message]);
            } catch (\Exception $ex) {
                DB::rollback();
                dd($ex->getMessage());
                $message = _lang('app.error_is_occured');
                return _json('error', $message);
            }
        }
    }

    public function submitDonationRequestForm2(Request $request) {
        //dd($request->file('images'));

        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {


            $this->errors = $validator->errors()->toArray();
            if ($request->ajax()) {
                return _json('error', $this->errors);
            } else {
                return redirect()->back()->withInput($request->all())->withErrors($this->errors);
            }
        }

        try {

            $message = _lang('app.registered_done_successfully');
            if ($request->ajax()) {
                return _json('success', $message);
            } else {
                return redirect()->back()->withInput($request->all())->with(['successMessage' => $message]);
            }
        } catch (\Exception $ex) {
            dd($ex->getMessage());
            $message = _lang('app.error_is_occured');
            if ($request->ajax()) {
                return _json('error', $message);
            } else {
                return redirect()->back()->withInput($request->all())->with(['errorMessage' => $message]);
            }
        }
    }

    private function create_donation_request($request) {

        $donation_request = new DonationRequest;

        $donation_request->description = $request->input('description');
        $donation_request->appropriate_time = $request->input('appropriate_time');
        $donation_request->lat = $request->input('lat');
        $donation_request->lng = $request->input('lng');
        $donation_request->date = date('Y-m-d');
        //dd( $request->input('donation_type'));
        $donation_request->donation_type_id = $request->input('donation_type');
        $donation_images = $request->file('images');
        $images = [];
        foreach ($donation_images as $image) {
            $images[] = DonationRequest::upload($image, 'donation_requests', true);
        }
        $donation_request->images = json_encode($images);
        if ($this->User) {
            $donation_request->name = $this->User->name;
            $donation_request->mobile = $this->User->mobile;
            $donation_request->client_id = $this->User->id;
        } else {
            $donation_request->name = $request->input('name');
            $donation_request->mobile = $request->input('dial_code') . $request->input('mobile');
        }
        $donation_request->save();
    }

}
