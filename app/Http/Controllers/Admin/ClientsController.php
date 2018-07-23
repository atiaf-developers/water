<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Client;
use App\Models\User;
use Validator;
use DB;

class ClientsController extends BackendController {

    public function __construct() {
        parent::__construct();
        $this->middleware('CheckPermission:clients,open');
        $this->middleware('CheckPermission:clients,view', ['only' => ['show']]);
    }

    public function index(Request $request) {
        return $this->_view('clients/index', 'backend');
    }

    public function show($id) {
        $User = User::find($id);
        if ($User == null) {
            return $this->err404();
        }
        $this->data['user'] = $User;
        return $this->_view('clients/view', 'backend');
    }

    public function destroy($id) {
        $User = User::find($id);
        if ($User == null) {
            return _json('error', _lang('app.error_is_occured'), 500);
        }
        DB::beginTransaction();
        try {
            $old_image = $User->image;
            $User->delete();
            USer::deleteUploaded('users', $old_image);
            DB::commit();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            if ($ex->getCode() == 23000) {
                return _json('error', _lang('app.this_record_can_not_be_deleted_for_linking_to_other_records'), 400);
            } else {
                return _json('error', $ex->getMessage() . $ex->getLine(), 400);
            }
        }
    }

    public function status($id) {
        $User = User::where('id', $id)->first();
        try {

            $User->active = ($User->active == 1) ? 0 : 1;
            $User->save();
            return _json('success', '');
        } catch (\Exception $ex) {
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    public function data(Request $request) {
        $user = User::where('type', 1)->select(['id', 'created_at', 'email', 'mobile', 'username',
            'image', 'active']);


        $datatable = \Datatables::eloquent($user);

        $datatable->addColumn('options', function ($item) {

            $back = "";

            if (\Permissions::check('clients', 'view') || \Permissions::check('clients', 'delete')) {
                if (\Permissions::check('clients', 'view')) {
                    $back .= '<a href="' . url('admin/clients/' . $item->id) . '" class="btn btn-xs blue"><i class="fa fa-eye"></i> ' . _lang('app.view') . ' </a>';
                }
                if (\Permissions::check('clients', 'delete')) {
                    $back .= '<a href="javascript:;" onclick = "Clients.delete(this);return false;" class="btn btn-xs red"><i class="fa fa-trash-o"></i> ' . _lang('app.delete') . ' </a>';
                }
            }


            return $back;
        });
        $datatable->addColumn('image', function ($item) {
            $back = '<img src="' . url('public/uploads/users/' . $item->image) . '" style="height:64px;width:64px;"/>';
            return $back;
        });

        $datatable->addColumn('active', function ($item) {
            if ($item->active == 1) {
                $message = _lang('app.active');
                $class = 'btn-info';
            } else {
                $message = _lang('app.not_active');
                $class = 'btn-danger';
            }
            $back = '<a class="btn ' . $class . '" onclick = "Clients.status(this);return false;" data-id = "' . $item->id . '" data-status = "' . $item->active . '">' . $message . ' <a>';
            return $back;
        });
        $datatable->escapeColumns([]);
        return $result = $datatable->make(true);
    }

}
