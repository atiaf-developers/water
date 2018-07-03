<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\AdminNotification;
use App\Models\Device;
use App\Models\Noti;
use App\Models\News;
use App\Models\Activity;
use App\Models\DonationRequest;
use DB;

class NotificationsController extends ApiController {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $user = $this->auth_user();
        $where_array['notifier_id'] = $user->id;
        $where_array['created_at'] = $user->created_at;
        $where_array['notifiable_type'] = 1;
        $this->notiMarkAsReadByNotifier($user->id, 1, 1);
        $noti = Noti::getNoti($where_array, 'ForApi');
        return _api_json($noti);
    }

    private function notiMarkAsReadByNotifier($notifier_id, $notifiable_type, $read_status) {
        $sql = "UPDATE noti_object n_o 
                JOIN noti n ON n_o.id = n.noti_object_id And n.read_status=0 And n.notifier_id=$notifier_id And n_o.notifiable_type=$notifiable_type             
                SET n.read_status = $read_status";
        DB::statement($sql);
    }

    public function getUnReadNoti(Request $request) {
        $user = $this->auth_user();
    
        $notifications = DB::table('noti_object as n_o')->join('noti as n', 'n.noti_object_id', '=', 'n_o.id');
        $notifications->where('n.notifier_id', $user->id);
        $notifications->where('n_o.notifiable_type', 1);
        $notifications->where('n.read_status', 0);
        return $notifications->count();
    }

}
