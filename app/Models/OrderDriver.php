<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OrderDriver extends MyModel
{
    protected $table = "orders_drivers";
    
   public static $status_arr = [
        0 => [
            'status_no' => 0,
            'client_message' => 'waiting_for_the_driver\'s_approval',
            'driver_message' => '',
            'admin_message' => 'assigned'
        ],
        1 => [
            'status_no' => 1,
            'client_message' => 'choose_payment_method',
            'driver_message' => 'waiting_for_payment_to_be_completed',
            'admin_message' => 'accepted'
        ],
        2 => [
            'status_no' => 2,
            'client_message' => 'request_not_accepted',
            'driver_message' => '',
            'admin_message' => 'rejected'
        ],
        3 => [
            'status_no' => 3,
            'client_message' => 'request_is_canceled',
            'driver_message' => '',
            'admin_message' => 'canceled by driver'
        ],
        4 => [
            'status_no' => 4,
            'client_message' => '',
            'driver_message' => '',
            'admin_message' => 'canceled by client'
        ],
        5 => [
            'status_no' => 5,
            'client_message' => 'I\'m_on_the_road',
            'driver_message' => 'you_have_reached',
            'admin_message' => 'driver_on_the_road'
        ],
        6 => [
            'status_no' => 6,
            'client_message' => 'driver_has_reached',
            'driver_message' => 'request_has_been_completed',
            'admin_message' => 'driver_has_reached'
        ],
        7 => [
            'status_no' => 7,
            'client_message' => 'request_has_been_completed',
            'driver_message' => 'request_has_been_completed',
            'admin_message' => 'request_has_been_completed'
        ],
    ];
    public static $user_status = array(
        'driver' => ['noti' => [0], 'current' => [1, 5, 6], 'latest' => [3, 4, 7]],
        'client' => ['current' => [0, 1, 5, 6], 'previous' => [3, 4, 7]],
    );
}
