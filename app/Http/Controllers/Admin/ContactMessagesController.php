<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Module;
use App\Models\ContactMessage;
use App\Models\ContactMessageReply;
use Validator;

class ContactMessagesController extends BackendController {

    private $rules = array(
        'reply' => 'required'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:contact_messages,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:contact_messages,add', ['only' => ['store']]);
    }

    public function index() {
        return $this->_view('contact_messages/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $find = ContactMessage::leftJoin('contact_messages_reply', 'contact_messages.id', '=', 'contact_messages_reply.contact_message_id')
                ->select('contact_messages.id', 'contact_messages.message', 'contact_messages_reply.message as reply')
                ->where('contact_messages.id', $id)
                ->first();
        if ($find) {
            return response()->json([
                        'type' => 'success',
                        'message' => $find
            ]);
        } else {
            return response()->json([
                        'type' => 'success',
                        'message' => 'error'
            ]);
        }
    }

    public function reply(Request $request) {

        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } else {


            try {

                $ContactMessageReply = new ContactMessageReply;

                $ContactMessageReply->message = $request->input('reply');
                $ContactMessageReply->contact_message_id = $request->input('contact_message_id');
                $ContactMessageReply->save();
                if ($ContactMessageReply->contact_message->type == 0) {
                    $notification = ['title' => 'RedBricks', 'body' => implode("\n", ['تم الرد على الاقتراح', $ContactMessageReply->message]),'id'=>$ContactMessageReply->contact_message_id, 'type' => 2];
                } else {
                    $notification = ['title' => 'RedBricks', 'body' => implode("\n", ['تم الرد على الشكوى', $ContactMessageReply->message]), 'id'=>$ContactMessageReply->contact_message_id,'type' => 2];
                }
                
               $send= $this->send_noti_fcm($notification, [$ContactMessageReply->contact_message->user_id]);
//               $send= $this->send_noti_fcm($notification, false,"fmC5HNF03NQ:APA91bG4W5Mo9im49zuoTAlel9wFpWo9PIZTW_4eEK7VIzWF6DhB44Gkqc0WOAhm_wzGh01enI6f4KP32-dxDoi8uFh6o8wTHttTJTMfFRw9zQP56fZnbwwZ69vzwNuQg8XuS7nRIp4BXGjIBNnWFhm7TgApch-Afw",2);
//                dd($send);
                return _json('success', _lang('app.sent_successfully'));
            } catch (\Exception $ex) {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    public function destroy(Request $request) {
        $ids = $request->input('ids');
        try {
            ContactMessage::destroy($ids);
            return _json('success', _lang('app.deleted_successfully'));
        } catch (Exception $ex) {
            return _json('error', _lang('app.error_is_occured'));
        }
    }

    public function data() {
        $blog = ContactMessage::select(['id', 'email', 'type', 'name', 'created_at']);

        return \Datatables::eloquent($blog)
                        ->addColumn('options', function ($item) {

                            $back = "";

                            $back .= '<a href="" class="btn btn-info" onclick = "Contact_messages.viewMessage(this);return false;" data-id = "' . $item->id . '">';
                            $back .= '' . _lang('app.view') . '';
                            $back .= '</a>';
                            return $back;
                        })
                        ->addColumn('input', function ($item) {

                            $back = '';

                            $back = '<div class="md-checkbox col-md-4" style="margin-left:40%;">';
                            $back .= '<input type="checkbox" id="' . $item->id . '" data-id="' . $item->id . '" class="md-check check-one-message"  value="">';
                            $back .= '<label for="' . $item->id . '">';
                            $back .= '<span></span>';
                            $back .= '<span class="check"></span>';
                            $back .= '<span class="box"></span>';
                            $back .= '</label>';
                            $back .= '</div>';

                            return $back;
                        })
                        ->editColumn('type', function ($item) {
                            $back = '';
                            if (isset(ContactMessage::$types[$item->type])) {
                                $back = _lang('app.' . ContactMessage::$types[$item->type]);
                            }
                            return $back;
                        })
                        ->escapeColumns([])
                        ->make(true);
    }

}
