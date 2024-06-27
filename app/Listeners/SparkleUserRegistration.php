<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\User;
use App\Services\v1\SparkleService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SparkleUserRegistration implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        $user = User::find($event->user->id);
        if(!empty($user->parent_id)){
            try {
                $service = new SparkleService();
    
                $reference = "SPK_Jetski_".$user->uuid;
                $customerPayload = [
                    "name" => "{$user->firstname} {$user->lastname}",
                    "external_reference" =>  $reference,
                    "email" => $user->email,
                    "bank_verification_number" => "12345678",
                    "metadata" => []
                  ];
    
                $customer = $service->createCustomer($customerPayload);
                if($customer){
                    $user->update(['external_sparkle_reference' => $customer['data']['id']]);
    
                    $accountPayload = [
                        "customer_id" => $customer["data"]["id"],
                        "external_reference" => $reference,
                        "is_permanent" => 1
                    ];
    
                    $account = $service->createAccount($accountPayload);
                    if($account){
                        $user->update(['account_number' => $account['data']['accounts'][0]['account_number']]);
                    }
                }
            } catch (Exception $e){
                Log::error($e->getMessage());
            }
        }
    }
}
