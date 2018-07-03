<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\RejectionReason;
use App\Models\RejectionReasonTranslation;
use Validator;
use DB;

class RejectionReasonsController extends BackendController {

    private $rules = array(
        'active' => 'required',
        'this_order' => 'required'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:rejection_reasons,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:rejection_reasons,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:rejection_reasons,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:rejection_reasons,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('rejection_reasons/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return $this->_view('rejection_reasons/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $this->rules = array_merge($this->rules,  $this->lang_rules(['title' => 'required|unique:rejection_reasons_translations,title']));
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            $rejection_reason = new RejectionReason;
            $rejection_reason->active = $request->input('active');
            $rejection_reason->this_order = $request->input('this_order');

            $rejection_reason->save();

            $rejection_reason_translations = array();
            $rejection_reason_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $rejection_reason_translations[] = array(
                    'locale' => $key,
                    'title' => $rejection_reason_title[$key],
                    'rejection_reason_id' => $rejection_reason->id
                );
            }
            RejectionReasonTranslation::insert($rejection_reason_translations);
            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $find = RejectionReason::find($id);

        if ($find) {
            return _json('success', $find);
        } else {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $rejection_reason = RejectionReason::find($id);

        if (!$rejection_reason) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

        $this->data['translations'] = RejectionReasonTranslation::where('rejection_reason_id', $id)->get()->keyBy('locale');
        $this->data['rejection_reason'] = $rejection_reason;

        return $this->_view('rejection_reasons/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $rejection_reason = RejectionReason::find($id);
        if (!$rejection_reason) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->rules = array_merge($this->rules, $this->lang_rules(['title' =>'required|unique:rejection_reasons_translations,title,' . $id . ',rejection_reason_id']));

        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        DB::beginTransaction();
        try {

            $rejection_reason->active = $request->input('active');
            $rejection_reason->this_order = $request->input('this_order');

            $rejection_reason->save();

            $rejection_reason_translations = array();

            RejectionReasonTranslation::where('rejection_reason_id', $rejection_reason->id)->delete();

            $rejection_reason_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $rejection_reason_translations[] = array(
                    'locale' => $key,
                    'title' => $rejection_reason_title[$key],
                    'rejection_reason_id' => $rejection_reason->id
                );
            }
            RejectionReasonTranslation::insert($rejection_reason_translations);

            DB::commit();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $rejection_reason = RejectionReason::find($id);
        if (!$rejection_reason) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $rejection_reason->delete();
            DB::commit();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            if ($ex->getCode() == 23000) {
                return _json('error', _lang('app.this_record_can_not_be_deleted_for_linking_to_other_records'), 400);
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    public function data(Request $request) {

        $rejection_reasons = RejectionReason::Join('rejection_reasons_translations', 'rejection_reasons.id', '=', 'rejection_reasons_translations.rejection_reason_id')
                ->where('rejection_reasons_translations.locale', $this->lang_code)
                ->select([
            'rejection_reasons.id', "rejection_reasons_translations.title", "rejection_reasons.this_order", 'rejection_reasons.active',
        ]);

        return \Datatables::eloquent($rejection_reasons)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('rejection_reasons', 'edit') || \Permissions::check('rejection_reasons', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> ' . _lang('app.options');
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('rejection_reasons', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('rejection_reasons.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('rejection_reasons', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "RejectionReasons.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                $back .= '</ul>';
                                $back .= ' </div>';
                            }
                            return $back;
                        })
                        ->editColumn('active', function ($item) {
                            if ($item->active == 1) {
                                $message = _lang('app.active');
                                $class = 'label-success';
                            } else {
                                $message = _lang('app.not_active');
                                $class = 'label-danger';
                            }
                            $back = '<span class="label label-sm ' . $class . '">' . $message . '</span>';
                            return $back;
                        })
                        ->escapeColumns([])
                        ->make(true);
    }

}
