<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\VehicleType;
use App\Models\VehicleWeight;
use Validator;
use DB;

class DelegatesController extends BackendController {

    private $rules = array(

        'letter_english.0' => 'required',
        'letter_english.1' => 'required',
        'letter_english.2' => 'required',
        'num_english.0' => 'required',
        'num_english.1' => 'required',
        'num_english.2' => 'required',
        'num_english.3' => 'required',

        'letter_arabic.0' => 'required',
        'letter_arabic.1' => 'required',
        'letter_arabic.2' => 'required',
        'num_arabic.0' => 'required',
        'num_arabic.1' => 'required',
        'num_arabic.2' => 'required',
        'num_arabic.3' => 'required',

        'vehicle_type' => 'required',
        'vehicle_weight' => 'required',
        'license_number' => 'required|unique:vehicles,license_number,NULL,id,deleted_at,NULL',
        'price' => 'required',
        'vehicle_image' => 'required|image|mimes:gif,png,jpeg|max:1000',
        'license_image' => 'required|image|mimes:gif,png,jpeg|max:1000',
        
        
        'name' => 'required',
        'username' => 'required|unique:users,username,NULL,id,deleted_at,NULL',
        'email' => 'email|unique:users,email,NULL,id,deleted_at,NULL',
        'mobile' => 'required|unique:users,mobile,NULL,id,deleted_at,NULL',
        'password' => 'required',
        'image' => 'required|image|mimes:gif,png,jpeg|max:1000',
        'active' => 'required'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:delegates,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:delegates,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:delegates,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:delegates,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {

        return $this->_view('delegates/index', 'backend');
    }

    public function status($id){
        DB::beginTransaction();
        try {
            $vehicle = Vehicle::find($id);
            if (!$vehicle) {
                return _json('error', _lang('app.not_found'));
            }  
            $delegate = User::find($vehicle->delegate_id);
            $delegate->active = !$delegate->active;
            $delegate->save();

            DB::commit();
            return _json('success', _lang('app.success'));
        } catch (\Exception $e) {
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $this->data['vehicle_types'] = VehicleType::getAllAdmin();
        $this->data['vehicle_weights'] = VehicleWeight::getAllAdmin();
        return $this->_view('delegates/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

         if ($this->check_vehicle_plate($request->all())) {
            $message = _lang('messages.This_plate_number_is_already_exist');
            return _json('error', $message, 400);
        } 

        DB::beginTransaction();
        try {
           
            $delegate = new User;
            $delegate->name = $request->input('name');
            $delegate->username = $request->input('username');
            $delegate->email = $request->input('email');
            $delegate->mobile = $request->input('mobile');
            $delegate->password = bcrypt($request->input('password'));
            $delegate->image = User::upload($request->file('image'), 'users', true);
            $delegate->type = 2;
            $delegate->active = $request->input('active');
            $delegate->save();

            $vehicle = new Vehicle;
            if ($this->lang_code == 'en') {
                $vehicle->plate_letter_ar = implode(' ', array_reverse($request->input('letter_arabic')));
                $vehicle->plate_letter_en = implode(' ', $request->input('letter_english'));
                $vehicle->plate_num_ar = implode(' ', $request->input('num_arabic'));
                $vehicle->plate_num_en = implode(' ', $request->input('num_english'));
            } else {
                $vehicle->plate_letter_ar = implode(' ', $request->input('letter_arabic'));
                $vehicle->plate_letter_en = implode(' ', array_reverse($request->input('letter_english')));
                $vehicle->plate_num_ar = implode(' ', array_reverse($request->input('num_arabic')));
                $vehicle->plate_num_en = implode(' ', array_reverse($request->input('num_english')));
            }

            $vehicle->vehicle_type_id = $request->input('vehicle_type');
            $vehicle->vehicle_weight_id = $request->input('vehicle_weight');
            $vehicle->license_number = $request->input('license_number');
            $vehicle->price = $request->input('price');
            $vehicle->vehicle_image = Vehicle::upload($request->file('vehicle_image'), 'vehicles', true);
            $vehicle->license_image = Vehicle::upload($request->file('license_image'), 'vehicles', true);
            $vehicle->delegate_id = $delegate->id;

            $vehicle->save();

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
        $find = Vehicle::find($id);

        if ($find) {
            return _json('success', $find);
        } else {
            return $this->err404();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $vehicle = Vehicle::find($id);
         if (!$vehicle) {
            return $this->err404();
        }

        $this->data['user'] = User::find($vehicle->delegate_id);
        $this->data['vehicle_types'] = VehicleType::getAllAdmin();
        $this->data['vehicle_weights'] = VehicleWeight::getAllAdmin();

        if ($this->lang_code == 'en') {
                $vehicle->plate_letter_ar = array_reverse(explode(' ', $vehicle->plate_letter_ar));
                $vehicle->plate_letter_en = explode(' ', $vehicle->plate_letter_en);
                $vehicle->plate_num_ar = explode(' ', $vehicle->plate_num_ar);
                $vehicle->plate_num_en = explode(' ', $vehicle->plate_num_en);
        } else {
                $vehicle->plate_letter_ar = explode(' ', $vehicle->plate_letter_ar);
                $vehicle->plate_letter_en = array_reverse(explode(' ', $vehicle->plate_letter_en));
                $vehicle->plate_num_ar = array_reverse(explode(' ', $vehicle->plate_num_ar));
                $vehicle->plate_num_en = array_reverse(explode(' ', $vehicle->plate_num_en));
        }
        $this->data['vehicle'] = $vehicle;

        return $this->_view('delegates/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $vehicle = Vehicle::find($id);
        if (!$vehicle) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $delegate = User::find($vehicle->delegate_id);

        unset($this->rules['vehicle_image'],$this->rules['license_image'],$this->rules['image'],$this->rules['password']);

        $this->rules['username'] = "required|unique:users,username,{$delegate->id},id,deleted_at,NULL";
        $this->rules['email'] = "required|unique:users,email,{$delegate->id},id,deleted_at,NULL";
        $this->rules['mobile'] = "required|unique:users,mobile,{$delegate->id},id,deleted_at,NULL";
        $this->rules['license_number'] = "required|unique:vehicles,license_number,{$vehicle->id},id,deleted_at,NULL";

        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        if ($this->check_vehicle_plate($request->all(),$vehicle->id)) {
            $message = _lang('messages.This_plate_number_is_already_exist');
            return _json('error', $message, 400);
        } 

        DB::beginTransaction();
        try {
            if ($this->lang_code == 'en') {
                $vehicle->plate_letter_ar = implode(' ', array_reverse($request->input('letter_arabic')));
                $vehicle->plate_letter_en = implode(' ', $request->input('letter_english'));
                $vehicle->plate_num_ar = implode(' ', $request->input('num_arabic'));
                $vehicle->plate_num_en = implode(' ', $request->input('num_english'));
            } else {
                $vehicle->plate_letter_ar = implode(' ', $request->input('letter_arabic'));
                $vehicle->plate_letter_en = implode(' ', array_reverse($request->input('letter_english')));
                $vehicle->plate_num_ar = implode(' ', array_reverse($request->input('num_arabic')));
                $vehicle->plate_num_en = implode(' ', array_reverse($request->input('num_english')));
            }

            $vehicle->vehicle_type_id = $request->input('vehicle_type');
            $vehicle->vehicle_weight_id = $request->input('vehicle_weight');
            $vehicle->license_number = $request->input('license_number');
            $vehicle->price = $request->input('price');

            if ($request->file('vehicle_image')) {
                Vehicle::deleteUploaded('vehicles', $vehicle->vehicle_image);
                $vehiclee->vehicle_image = Vehicle::upload($request->file('vehicle_image'), 'vehicles', true);
            }
            if ($request->file('license_image')) {
                Vehicle::deleteUploaded('vehicles', $vehicle->license_image);
                $vehicle->license_image = Vehicle::upload($request->file('license_image'), 'vehicles', true);
            }
            $vehicle->save();
           

            $delegate->name = $request->input('name');
            $delegate->username = $request->input('username');
            $delegate->email = $request->input('email');
            $delegate->mobile = $request->input('mobile');
            $delegate->active = $request->input('active');
            if ($request->input('password')) {
                $delegate->password = bcrypt($request->input('password'));
            }
            if ($request->file('image')) {
                User::deleteUploaded('users', $user->image);
                $user->image = User::upload($request->file('image'), 'users', true);
            }
            $delegate->save();

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
        $vehicle = Vehicle::find($id);
        if (!$vehicle) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $vehicle->delete();
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

        $delegates = Vehicle::Join('users', 'users.id', '=', 'vehicles.delegate_id')
        ->select([
            'vehicles.id', "users.name", "vehicles.vehicle_image", 'users.image', 'users.active',
        ]);

        return \Datatables::eloquent($delegates)
        ->addColumn('options', function ($item) {

            $back = "";
            if (\Permissions::check('delegates', 'edit') || \Permissions::check('delegates', 'delete')) {
                $back .= '<div class="btn-group">';
                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> ' . _lang('app.options');
                $back .= '<i class="fa fa-angle-down"></i>';
                $back .= '</button>';
                $back .= '<ul class = "dropdown-menu" role = "menu">';
                if (\Permissions::check('delegates', 'edit')) {
                    $back .= '<li>';
                    $back .= '<a href="' . route('delegates.edit', $item->id) . '">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                    $back .= '</a>';
                    $back .= '</li>';
                }

                if (\Permissions::check('delegates', 'delete')) {
                    $back .= '<li>';
                    $back .= '<a href="" data-toggle="confirmation" onclick = "Delegates.delete(this);return false;" data-id = "' . $item->id . '">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                    $back .= '</a>';
                    $back .= '</li>';
                }

                $back .= '</ul>';
                $back .= ' </div>';
            }
            return $back;
        })
        ->addColumn('vehicle_image', function ($item) {
            $back = '<img src="' . url('public/uploads/vehicles/' . $item->vehicle_image) . '" style="height:64px;width:64px;"/>';
            return $back;
        })
        ->editColumn('image', function ($item) {
            $back = '<img src="' . url('public/uploads/users/' . $item->image) . '" style="height:64px;width:64px;"/>';
            return $back;
        })
        ->editColumn('active', function ($item) {
           if ($item->active == 1) {
                $message = _lang('app.active');
                $class = 'btn-info';
            } else {
                $message = _lang('app.not_active');
                $class = 'btn-danger';
            }
            $back = '<a class="btn ' . $class . '" onclick = "Delegates.status(this);return false;" data-id = "' . $item->id . '" data-status = "' . $item->active . '">' . $message . ' <a>';
            return $back;
        })
        ->escapeColumns([])
        ->make(true);
    }

    private function check_vehicle_plate($data, $id = false) {
        if ($this->lang_code == 'en') {
                $plate_letter_ar = implode(' ', array_reverse($data['letter_arabic']));
                $plate_letter_en = implode(' ', $data['letter_english']);
                $plate_num_ar = implode(' ', $data['num_arabic']);
                $plate_num_en = implode(' ', $data['num_english']);
            } else {
                $plate_letter_ar = implode(' ', $data['letter_arabic']);
                $plate_letter_en = implode(' ', array_reverse($data['letter_english']));
                $plate_num_ar = implode(' ', array_reverse($data['num_arabic']));
                $plate_num_en = implode(' ', array_reverse($data['num_english']));
            }

        $vehicle = Vehicle::where('plate_letter_ar', $plate_letter_ar);
        $vehicle->where('plate_letter_en', $plate_letter_en);
        $vehicle->where('plate_num_ar', $plate_num_ar);
        $vehicle->where('plate_num_en',  $plate_num_en);
        if ($id) {
            $vehicle->where('id', '!=', $id);
        }
        $find = $vehicle->first();
        return $find ? true : false;
    }

}
