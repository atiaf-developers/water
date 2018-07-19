<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelTrait;

class Order extends Model {

    use ModelTrait;

    protected $table = "orders";
    public static $status_arr = [
        0 => [
            'status_no' => 0,
            'client_message' => 'waiting_for_the_driver\'s_approval',
            'driver_message' => '',
            'admin_message' => 'new_order'
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
        'driver' => [1, 3, 5,6,7],
        'client' => [0,1,2, 3, 5,6,7],
        'driver_noti' => [0],
    );
    public static $payment_method = [
        1 => "cash_on_delivery",
        2 => "visa",
    ];

    public function drivers() {
        $lang_code = static::getLangCode();
        return $this->belongsToMany(User::class, 'orders_drivers', 'order_id', 'driver_id');
    }

    public static function getOrdersApi($where_array = array()) {

        $lang_code = static::getLangCode();
        $orders = static::join('orders_drivers', 'orders.id', '=', 'orders_drivers.order_id');
        $orders->join('users as drivers', 'drivers.id', '=', 'orders_drivers.driver_id');
        $orders->join('users as clients', 'clients.id', '=', 'orders.client_id');
        $orders->join('vehicles', 'drivers.id', '=', 'vehicles.driver_id');
        $orders->join('vehicle_types', 'vehicle_types.id', '=', 'vehicles.vehicle_type_id');
        $orders->join('vehicle_types_translations', 'vehicle_types.id', '=', 'vehicle_types_translations.vehicle_type_id');
        $orders->join('vehicle_weights', 'vehicle_weights.id', '=', 'vehicles.vehicle_weight_id');
        $orders->join('vehicle_weights_translations', 'vehicle_weights.id', '=', 'vehicle_weights_translations.vehicle_weight_id');
        $orders->join('rejection_reasons', 'rejection_reasons.id', '=', 'orders.rejection_reason_id');
        $orders->leftJoin('rejection_reasons_translations', 'rejection_reasons.id', '=', 'rejection_reasons_translations.rejection_reason_id');
        $orders->where('vehicle_types_translations.locale', $lang_code);
        $orders->where('vehicle_weights_translations.locale', $lang_code);
        $orders->select(["orders.*", 'vehicles.id as vehicleId', "vehicles.lat as vehicleLat", "vehicles.lng as vehicleLng", "drivers.name as driverName",
            'vehicle_types_translations.title as vehicleTypeTitle', 'vehicle_weights_translations.title as vehicleWeightTitle','rejection_reasons_translations.title as rejectionReasonTitle']);
        $orders->groupBy('orders.id');
        if (isset($where_array['order_id'])) {
            $orders->where('orders.id', $where_array['order_id']);
            $orders = $orders->first();
            if ($orders) {
                $orders = static::transformApi($orders);
            }
        } else {
            $orders = $orders->paginate(static::$limit);
            $orders = $orders->getCollection()->transform(function($order, $key) {
                return static::transformApi($order);
            });
        }

        return $orders;
    }

    public static function transformApi($item) {
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->vehicleId = $item->vehicleId;
        $transformer->price = $item->price;
        $transformer->taxes = $item->taxes;
        $transformer->deliveryCost = $item->delivery_cost;
        $transformer->totalPrice = $item->total_price;
        $transformer->paymentMethodNo = $item->payment_method;
        $transformer->driverName = $item->driverName;
        $transformer->orderAddress = $item->driverName;
        $transformer->vehicleLat = $item->vehicleLat;
        $transformer->vehicleLng = $item->vehicleLng;
        $transformer->paymentMethodText = isset(static::$payment_method[$item->payment_method]) ? _lang('app.' . static::$payment_method[$item->payment_method]) : '';
        $duration = GetDrivingDistance($item->vehicleLat, $item->vehicleLng, $item->lat, $item->lng, static::getLangCode());
        $transformer->time = $duration['time'];
        $transformer->originAddress = $duration['origin_address'];
        $transformer->destinationAddress = $duration['destination_address'];
        $transformer->vehicleTypeTitle = $item->vehicleTypeTitle;
        $transformer->vehicleWeightTitle = $item->vehicleWeightTitle;
        $transformer->rejectionReasonTitle = $item->rejectionReasonTitle;
        return $transformer;
    }

}
