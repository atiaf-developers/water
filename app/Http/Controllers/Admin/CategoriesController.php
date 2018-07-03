<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Category;
use App\Models\CategoryTranslation;
use Validator;
use DB;

class CategoriesController extends BackendController {

    private $rules = array(
        'active' => 'required',
        'this_order' => 'required|unique:categories,this_order',
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:categories,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:categories,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:categories,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:categories,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('categories/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return $this->_view('categories/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $columns_arr = array(
            'title' => 'required|unique:categories_translations,title'
        );
        $this->rules = array_merge($this->rules, $this->lang_rules($columns_arr));
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {


            $category = new Category;
            $category->active = $request->input('active');
            $category->this_order = $request->input('this_order');
            $category->save();

            $category_translations = array();
            $title = $request->input('title');
            foreach ($title as $key => $value) {
                $category_translations[] = array(
                    'locale' => $key,
                    'title' => $value,
                    'category_id' => $category->id
                );
            }
            CategoryTranslation::insert($category_translations);

            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            dd($ex);
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
        $find = Category::find($id);

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
        $category = Category::find($id);

        if (!$category) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->data['translations'] = CategoryTranslation::where('category_id', $id)->get()->keyBy('locale');
        $this->data['category'] = $category;

        return $this->_view('categories/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {


        $category = Category::find($id);
        if (!$category) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $columns_arr = array(
            'title' => "required|unique:categories_translations,title,$id,category_id"
        );
        $this->rules['this_order'] = "required|unique:categories,this_order,$id,id";
        $this->rules = array_merge($this->rules, $this->lang_rules($columns_arr));
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }


        DB::beginTransaction();
        try {
            $category->active = $request->input('active');
            $category->this_order = $request->input('this_order');
            $category->save();
            CategoryTranslation::where('category_id', $category->id)->delete();
            $category_translations = array();
            $title = $request->input('title');
            foreach ($title as $key => $value) {
                $category_translations[] = array(
                    'locale' => $key,
                    'title' => $value,
                    'category_id' => $category->id
                );
            }
            CategoryTranslation::insert($category_translations);
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
        $category = Category::find($id);
        if (!$category) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $category->delete();
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

        $categories = Category::Join('categories_translations', 'categories.id', '=', 'categories_translations.category_id')
                ->where('categories_translations.locale', $this->lang_code)
                ->orderBy('categories.this_order')
                ->select([
            'categories.id', "categories_translations.title", "categories.this_order", "categories.active"
        ]);

        return \Datatables::eloquent($categories)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('categories', 'edit') || \Permissions::check('categories', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> ' . _lang('app.options');
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('categories', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('categories.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('categories', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "Categories.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                $back .= '</ul>';
                                $back .= ' </div>';
                            }
                            return $back;
                        })
                        ->addColumn('active', function ($item) {
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
