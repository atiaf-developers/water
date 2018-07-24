<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelTrait;
use DB;

class Order extends Model {

    use ModelTrait;

    protected $table = "orders";
    public static $payment_method = [
        1 => "cash_on_delivery",
        2 => "visa",
    ];
    protected $casts = [
        'price' => 'float',
        'driverValue' => 'float',
        'companyValue' => 'float'
    ];

    public function drivers() {
        $lang_code = static::getLangCode();
        return $this->belongsToMany(User::class, 'orders_drivers', 'order_id', 'driver_id');
    }

    public static function getOrdersAdmin($where_array = array()) {
        $lang_code = static::getLangCode();
//        $orders = static::join(DB::raw("(SELECT max(od.created_at) as orderDriverId, od.driver_id,od.order_id,od.status,od.created_at
//                                        FROM orders_drivers as od
//                                        GROUP BY od.order_id
//                                        ) as orders_drivers"), function($join) {
//
//                    $join->on("orders.id", "=", "orders_drivers.order_id");
//                });
        $orders = static::join('orders_drivers', function ($join) {
                    $join->on('orders.id', '=', 'orders_drivers.order_id')
                            ->where('orders_drivers.is_final_destination', 1);
                });
        $orders->join('users as drivers', 'drivers.id', '=', 'orders_drivers.driver_id');
        $orders->join('users as clients', 'clients.id', '=', 'orders.client_id');
        $orders->join('vehicles', 'drivers.id', '=', 'vehicles.driver_id');
        $orders->leftJoin('rejection_reasons', 'rejection_reasons.id', '=', 'orders.rejection_reason_id');
        $orders->leftJoin('rejection_reasons_translations', 'rejection_reasons.id', '=', 'rejection_reasons_translations.rejection_reason_id');
        $orders->select(["orders.*", "orders_drivers.driver_id as driverId", "clients.id as clientId", "orders_drivers.status", "vehicles.vehicle_image", "clients.username as clientName", "clients.image as clientImage",
            "drivers.name as driverName", 'rejection_reasons_translations.title as rejectionReasonTitle', "drivers.id as driverId",
            "orders.rating", "orders.closed", "orders.created_at", "orders.price AS driverValue",
            DB::RAW("(((orders.price*orders.commission)/100)+((orders.price*orders.taxes)/100)+orders.delivery_cost) AS companyValue")]);

        if (isset($where_array['order_id'])) {
            $orders->where('orders.id', $where_array['order_id']);
            $orders = $orders->first();
            if ($orders) {
                $orders = static::transformAdmin($orders);
            }
        } else {
            //dd($where_array);
            $orders = static::handleWhereAdmin($orders, $where_array);
            $orders->orderBy('orders.created_at','desc');
//            $orders->groupBy('orders.id');
            $orders = $orders->paginate(static::$limit);
            $orders->getCollection()->transform(function($order, $key) {
                return static::transformAdmin($order);
            });
        }

        return $orders;
    }

    public static function getOrdersApi($where_array = array()) {

        $lang_code = static::getLangCode();
        $orders = static::join('orders_drivers', function ($join) {
                    $join->on('orders.id', '=', 'orders_drivers.order_id')
                            ->where('orders_drivers.is_final_destination', 1);
                });
        $orders->join('users as drivers', 'drivers.id', '=', 'orders_drivers.driver_id');
        $orders->leftJoin('users as clients', 'clients.id', '=', 'orders.client_id');
        $orders->join('vehicles', 'drivers.id', '=', 'vehicles.driver_id');
        $orders->join('vehicle_types', 'vehicle_types.id', '=', 'vehicles.vehicle_type_id');
        $orders->join('vehicle_types_translations', 'vehicle_types.id', '=', 'vehicle_types_translations.vehicle_type_id');
        $orders->join('vehicle_weights', 'vehicle_weights.id', '=', 'vehicles.vehicle_weight_id');
        $orders->join('vehicle_weights_translations', 'vehicle_weights.id', '=', 'vehicle_weights_translations.vehicle_weight_id');
        $orders->leftJoin('rejection_reasons', 'rejection_reasons.id', '=', 'orders.rejection_reason_id');
        $orders->leftJoin('rejection_reasons_translations', 'rejection_reasons.id', '=', 'rejection_reasons_translations.rejection_reason_id');
        $orders->where('vehicle_types_translations.locale', $lang_code);
        $orders->where('vehicle_weights_translations.locale', $lang_code);
        $orders->select(["orders.*", "orders_drivers.status", 'vehicles.id as vehicleId', "vehicles.lat as vehicleLat", "vehicles.lng as vehicleLng", "vehicles.vehicle_image", "clients.username as clientName", "clients.image as clientImage",
            "drivers.name as driverName", 'vehicle_types_translations.title as vehicleTypeTitle', 'vehicle_weights_translations.title as vehicleWeightTitle', 'rejection_reasons_translations.title as rejectionReasonTitle',
            "orders.rating", "orders.closed"]);
        
        if (isset($where_array['order_id'])) {
            $orders->where('orders.id', $where_array['order_id']);
            $orders = $orders->first();
            if ($orders) {
                $orders = static::transformApi($orders);
            }
        } else {
            //dd($where_array);
            $orders = static::handleWhereApi($orders, $where_array);
            $orders->orderBy('orders.created_at','desc');
            $orders->groupBy('orders.id');
            $orders = $orders->paginate(static::$limit);
            $orders = $orders->getCollection()->transform(function($order, $key) use($where_array) {
                return static::transformApi($order, $where_array);
            });
        }

        return $orders;
    }
  

    public static function getOrdersCountWithDistinctClients($where_array = array()) {

        $lang_code = static::getLangCode();
        $orders = static::join('orders_drivers', function ($join) {
                    $join->on('orders.id', '=', 'orders_drivers.order_id')
                            ->where('orders_drivers.is_final_destination', 1);
                });

        $orders->where("orders_drivers.driver_id", $where_array['driver']);
        $orders->whereIn("orders_drivers.status", $where_array['status']);
        $orders->groupBy('orders.client_id');


        return $orders->count();
    }

    public static function getOrdersInfoAdmin($where_array) {
        $orders = static::join('orders_drivers', function ($join) {
                    $join->on('orders.id', '=', 'orders_drivers.order_id')
                            ->where('orders_drivers.is_final_destination', 1);
                });

        $orders->join('users as drivers', 'drivers.id', '=', 'orders_drivers.driver_id');
        $orders->join('users as clients', 'clients.id', '=', 'orders.client_id');
        $orders->join('vehicles', 'drivers.id', '=', 'vehicles.driver_id');
        $orders = static::handleWhereAdmin($orders, $where_array);
        $orders->select([
            DB::RAW("sum(orders.total_price) AS totalPrice"),
            DB::RAW("sum(IF( orders.closed = 0, orders.price, 0)) AS driverValue"),
            DB::RAW("sum(IF( orders.closed = 0, ((orders.price*orders.commission)/100)+((orders.price*orders.taxes)/100)+orders.delivery_cost, 0)) AS companyValue")
        ]);


        return $orders->first();
    }

    public static function getOrderInfoApi($where_array) {
        $orders = static::join('orders_drivers', function ($join) {
                    $join->on('orders.id', '=', 'orders_drivers.order_id')
                            ->where('orders_drivers.is_final_destination', 1);
                });
        $orders->join('users as drivers', 'drivers.id', '=', 'orders_drivers.driver_id');
        $orders->leftJoin('users as clients', 'clients.id', '=', 'orders.client_id');
        $orders->join('vehicles', 'drivers.id', '=', 'vehicles.driver_id');
        $orders = static::handleWhereApi($orders, $where_array);
        $orders->select([
            DB::RAW("sum(IF( closed = 0, orders.price, 0)) AS driverValue"),
            DB::RAW("sum(IF( closed = 0, ((orders.price*orders.commission)/100)+((orders.price*orders.taxes)/100)+orders.delivery_cost, 0)) AS companyValue")
        ]);


        return $orders->first();
    }

    private static function handleWhereAdmin($orders, $where_array) {
        //dd($where_array);
        if (isset($where_array['from'])) {
            $from = $where_array['from'];
            $orders->where("orders.date", ">=", "$from");
        }
        if (isset($where_array['to'])) {
            $to = $where_array['to'];
            $orders->where("orders.date", "<=", "$to");
        }
        if (isset($where_array['order'])) {
            $orders->where("orders.id", $where_array['order']);
        }
        if (isset($where_array['client'])) {
            $orders->where("orders.client_id", $where_array['client']);
        }
        if (isset($where_array['driver'])) {
            $orders->where("orders_drivers.driver_id", $where_array['driver']);
        }
        if (isset($where_array['status'])) {
            $orders->where("orders_drivers.status", $where_array['status']);
        }

        return $orders;
    }

    private static function handleWhereApi($orders, $where_array) {
        //dd($where_array);
        if (isset($where_array['from'])) {
            $from = $where_array['from'];
            $orders->where("orders.date", ">=", "$from");
        }
        if (isset($where_array['to'])) {
            $to = $where_array['to'];
            $orders->where("orders.date", "<=", "$to");
        }
        if (isset($where_array['client'])) {
            //dd($where_array['client']);
            $orders->where("orders.client_id", $where_array['client']);
        }
        if (isset($where_array['driver'])) {
            $orders->where("orders_drivers.driver_id", $where_array['driver']);
        }
        if (isset($where_array['device_id'])) {
            $orders->where("orders.device_id", $where_array['device_id']);
        }
        if (isset($where_array['status'])) {
            $orders->whereIn("orders_drivers.status", $where_array['status']);
        }

        return $orders;
    }

    public static function transformAdmin($item) {
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->date = date('Y/m/d', strtotime($item->date));
        $transformer->lat = $item->lat;
        $transformer->lng = $item->lng;
        $transformer->price = $item->price;
        $transformer->taxes = $item->taxes;
        $transformer->deliveryCost = $item->delivery_cost;
        $transformer->totalPrice = $item->total_price;
        $transformer->paymentMethodNo = $item->payment_method;
        $transformer->paymentMethodText = isset(static::$payment_method[$item->payment_method]) ? _lang('app.' . static::$payment_method[$item->payment_method]) : '';

        $transformer->rejectionReasonTitle = $item->rejectionReasonTitle;
        $transformer->clientId = $item->clientId;
        $transformer->driverId = $item->driverId;
        $transformer->clientName = $item->clientName;
        $transformer->driverName = $item->driverName;
        $transformer->driverValue = $item->driverValue;
        $transformer->companyValue = $item->companyValue;
        $transformer->vehicleImage = url('public/uploads/vehicles') . '/' . $item->vehicle_image;
        $transformer->clientImage = url('public/uploads/users') . '/' . $item->clientImage;
        $transformer->statusNo = $item->status;
        $transformer->statusText = isset(OrderDriver::$status_arr[$item->status]['admin_message']) ? _lang('app.' . OrderDriver::$status_arr[$item->status]['admin_message']) : '';
        $transformer->rating = $item->rating;
        $transformer->closed = $item->closed;
        $transformer->createdAt = $item->created_at;

        return $transformer;
    }

    public static function transformApi($item, $where_array) {
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->date = date('Y/m/d', strtotime($item->date));
        $transformer->vehicleId = $item->vehicleId;
        $transformer->price = $item->price;
        $transformer->taxes = $item->taxes;
        $transformer->deliveryCost = $item->delivery_cost;
        $transformer->totalPrice = $item->total_price;
        $transformer->paymentMethodNo = $item->payment_method;
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
        $transformer->clientName = $item->clientName;
        $transformer->driverName = $item->driverName;
        $transformer->vehicleImage = url('public/uploads/vehicles') . '/' . $item->vehicle_image;
        $transformer->clientImage = url('public/uploads/users') . '/' . $item->clientImage;
        $transformer->statusNo = $item->status;
        if (isset($where_array['user_type'])) {
            if ($where_array['user_type'] == 1) {
                $transformer->statusText = isset(OrderDriver::$status_arr[$item->status]['client_message']) ? _lang('app.' . OrderDriver::$status_arr[$item->status]['client_message']) : '';
            } else if ($where_array['user_type'] == 2) {
                $transformer->statusText = isset(OrderDriver::$status_arr[$item->status]['driver_message']) ? _lang('app.' . OrderDriver::$status_arr[$item->status]['driver_message']) : '';
            }
        }
        $transformer->rating = $item->rating;
        $transformer->closed = $item->closed;

        return $transformer;
    }

}
