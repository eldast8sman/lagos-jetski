<?php

namespace App\Repositories;

use App\Events\UserRegistered;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Services\G5PosService;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class MemberRepository extends AbstractRepository implements MemberRepositoryInterface
{
    public $errors = "";

    public function __construct(User $user){
        parent::__construct($user);
    }

    public function fetch_g5_customers()
    {
        try {
            $service = new G5PosService();

            $customers = $service->getCustomers([]);

            foreach($customers as $customer){
                if(empty($customer['Phone'])){
                    $customer['Phone'] = null;
                }
                if(empty($customer['Email'])){
                    continue;
                }
                if(empty($customer['Mobile'])){
                    $customer['Mobile'] = null;
                }

                $user = $this->findByOrFirst(['g5_id' => $customer['CustomerID'], 'email' => $customer['Email']]);
                if($user){
                    $user->wallet()->update(['balance' => $customer['Debt'] < 0 ? abs($customer['Debt']) : -1 * abs($customer['Debt'])]);
                    return $user;
                }

                $user = $this->store([
                    'firstname' => $customer['CustomerName'],
                    'lastname' => $customer['FamilyName'],
                    'username' => $customer['FamilyName'] . $customer['CustomerName'],
                    'phone' => $customer['Mobile'] != '' ? $customer['Mobile'] : $customer['Phone'],
                    'password' => bcrypt($customer['CustomerName'] . $customer['FamilyName']),
                    'gender' => $customer['Sex'],
                    'marital_status' => ($customer['MartialStatus']) ? $customer['MartialStatus'] : 'Single',
                    'address' => $customer['Street'] . ' ' .  $customer['City'] . ' ' . $customer['State'],
                    'photo' => "https://avatars.dicebear.com/api/initials/" . $customer['CustomerName'] . ".svg",
                    'dob' => Carbon::parse($customer['BirthDay'])->format('Y-m-d'),
                    'email' => $customer['Email']
                ]);

                return $user;
            }
        } catch(Exception $e){
            
        }
    }

    public function store(array $data, $balance=null)
    {
        $data['uuid'] = Str::uuid().'-'.time();
        $data['verification_token'] = Str::random(20).time();
        $data['verification_token_expiry'] = date('Y-m-d H:i:s', time() + (60 * 60 * 24));

        $user = $this->create($data);
        if(!$user){
            return false;
        }

        if($balance !== null){
            Wallet::create([
                'uuid' => Str::uuid().'-'.time(),
                'user_id' => $user->id,
                'balance' => $balance
            ]);
        }

        UserRegistered::dispatch($user);

        return $user;
    }
}