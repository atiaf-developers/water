<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\VehicleWeight;
use App\Models\VehicleWeightTranslation;
use Validator;
use DB;

class VehicleWeightsController extends BackendController {

    private $rules = array(
        'active' => 'required',
        'this_order' => 'required'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:vehicle_weights,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:vehicle_weights,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:vehicle_weights,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:vehicle_weights,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('vehicle_weights/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return $this->_view('vehicle_weights/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $this->rules = array_merge($this->rules,  $this->lang_rules(['title' => 'required|unique:vehicle_weights_translations,title']));
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            $vehicle_weight = new VehicleWeight;
            $vehicle_weight->active = $request->input('active');
            $vehicle_weight->this_order = $request->input('this_order');

            $vehicle_weight->save();

            $vehicle_weight_translations = array();
            $vehicle_weight_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $vehicle_weight_translations[] = array(
                    'locale' => $key,
                    'title' => $vehicle_weight_title[$key],
                    'vehicle_weight_id' => $vehicle_weight->id
                );
            }
            VehicleWeightTranslation::insert($vehicle_weight_translations);
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
        $find = VehicleWeight::find($id);

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
        $vehicle_weight = VehicleWeight::find($id);

        if (!$vehicle_weight) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

        $this->data['translations'] = VehicleWeightTranslation::where('vehicle_weight_id', $id)->get()->keyBy('locale');
        $this->data['vehicle_weight'] = $vehicle_weight;

        return $this->_view('vehicle_weights/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $vehicle_weight = VehicleWeight::find($id);
        if (!$vehicle_weight) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->rules = array_merge($this->rules, $this->lang_rules(['title' =>'required|unique:vehicle_weights_translations,title,' . $id . ',vehicle_weight_id']));

        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        DB::beginTransaction();
        try {

            $vehicle_weight->active = $request->input('active');
            $vehicle_weight->this_order = $request->input('this_order');

            $vehicle_weight->save();

            $vehicle_weight_translations = array();

            VehicleWeightTranslation::where('vehicle_weight_id', $vehicle_weight->id)->delete();

            $vehicle_weight_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $vehicle_weight_translations[] = array(
                    'locale' => $key,
                    'title' => $vehicle_weight_title[$key],
                    'vehicle_weight_id' => $vehicle_weight->id
                );
            }
            VehicleWeightTranslation::insert($vehicle_weight_translations);

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
        $vehicle_weight = VehicleWeight::find($id);
        if (!$vehicle_weight) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $vehicle_weight->delete();
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

        $vehicle_weights = VehicleWeight::Join('vehicle_weights_translations', 'vehicle_weights.id', '=', 'vehicle_weights_translations.vehicle_weight_id')
                ->where('vehicle_weights_translations.locale', $this->lang_code)
                ->select([
            'vehicle_weights.id', "vehicle_weights_translations.title", "vehicle_weights.this_order", 'vehicle_weights.active',
        ]);

        return \Datatables::eloquent($vehicle_weights)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('vehicle_weights', 'edit') || \Permissions::check('vehicle_weights', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> ' . _lang('app.options');
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('vehicle_weights', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('vehicle_weights.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('vehicle_weights', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "VehicleWeights.delete(this);return false;" data-id = "' . $item->id . '">';
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
