<?php

namespace App\Repositories;

use App\Jobs\SaveOrderJob;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Services\G5PosService;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class OrderRepository extends AbstractRepository implements OrderRepositoryInterface
{
    public $errors;

    public function __construct(Order $order)
    {
        parent::__construct($order);
    }

    public function fetch_g5_order($user_id){
        try {
            $user = User::find($user_id);
            if(empty($user)){
                return;
            }

            $g5_id = $user->g5_id;
            $g5 = new G5PosService();

            $orders = $g5->getOrders(['CustomerID' => $g5_id]);
            $orders = json_decode($orders, true);

            foreach($orders as $order){
                SaveOrderJob::dispatch($order, $user->id);
            }
        } catch (Exception $e){
            Log::error($e->getMessage());
            return false; 
        }
    }
}