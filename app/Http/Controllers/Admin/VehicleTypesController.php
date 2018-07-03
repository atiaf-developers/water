<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\VehicleType;
use App\Models\VehicleTypeTranslation;
use Validator;
use DB;

class VehicleTypesController extends BackendController {

    private $rules = array(
        'active' => 'required',
        'this_order' => 'required',
        'image' => 'required|image|mimes:gif,png,jpeg|max:1000'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:vehicle_types,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:vehicle_types,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:vehicle_types,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:vehicle_types,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('vehicle_types/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return $this->_view('vehicle_types/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $this->rules = array_merge($this->rules, $this->lang_rules(['title' => 'required|unique:vehicle_types_translations,title']));
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            $vehicle_type = new VehicleType;
            $vehicle_type->image = VehicleType::upload($request->file('image'), 'vehicle_types', true);
            $vehicle_type->active = $request->input('active');
            $vehicle_type->this_order = $request->input('this_order');

            $vehicle_type->save();

            $vehicle_type_translations = array();
            $vehicle_type_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $vehicle_type_translations[] = array(
                    'locale' => $key,
                    'title' => $vehicle_type_title[$key],
                    'vehicle_type_id' => $vehicle_type->id
                );
            }
            VehicleTypeTranslation::insert($vehicle_type_translations);
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
        $find = VehicleType::find($id);

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
        $vehicle_type = VehicleType::find($id);

        if (!$vehicle_type) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

        $this->data['translations'] = VehicleTypeTranslation::where('vehicle_type_id', $id)->get()->keyBy('locale');
        $this->data['vehicle_type'] = $vehicle_type;

        return $this->_view('vehicle_types/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $vehicle_type = VehicleType::find($id);
        if (!$vehicle_type) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->rules['image'] = "image|mimes:gif,png,jpeg|max:1000";
        $this->rules = array_merge($this->rules, $this->lang_rules(['title' => 'required|unique:vehicle_types_translations,title,' . $id . ',vehicle_type_id']));

        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        DB::beginTransaction();
        try {
            if ($request->file('image')) {
                $vehicle_type->image = VehicleType::upload($request->file('image'), 'vehicle_types', true);
            }

            $vehicle_type->active = $request->input('active');
            $vehicle_type->this_order = $request->input('this_order');

            $vehicle_type->save();

            $vehicle_type_translations = array();

            VehicleTypeTranslation::where('vehicle_type_id', $vehicle_type->id)->delete();

            $vehicle_type_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $vehicle_type_translations[] = array(
                    'locale' => $key,
                    'title' => $vehicle_type_title[$key],
                    'vehicle_type_id' => $vehicle_type->id
                );
            }
            VehicleTypeTranslation::insert($vehicle_type_translations);

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
        $vehicle_type = VehicleType::find($id);
        if (!$vehicle_type) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $vehicle_type->delete();
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

        $vehicle_types = VehicleType::Join('vehicle_types_translations', 'vehicle_types.id', '=', 'vehicle_types_translations.vehicle_type_id')
                ->where('vehicle_types_translations.locale', $this->lang_code)
                ->select([
            'vehicle_types.id', "vehicle_types_translations.title", "vehicle_types.this_order", 'vehicle_types.image', 'vehicle_types.active',
        ]);

        return \Datatables::eloquent($vehicle_types)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('vehicle_types', 'edit') || \Permissions::check('vehicle_types', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> ' . _lang('app.options');
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('vehicle_types', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('vehicle_types.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('vehicle_types', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "VehicleTypes.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                $back .= '</ul>';
                                $back .= ' </div>';
                            }
                            return $back;
                        })
                        ->addColumn('image', function ($item) {
                            $back = '<img src="' . url('public/uploads/vehicle_types/' . $item->image) . '" style="height:64px;width:64px;"/>';
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
