<?php

namespace App\Repositories;

use App\Events\UserRegistered;
use App\Mail\AddUserNotificationMail;
use App\Models\Product;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Services\G5PosService;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

            $customers = json_decode($service->getCustomers([]), true);

            foreach($customers as $customer){
                $customer = $customers[$i];
                if(empty($customer['Phone'])){
                    $customer['Phone'] = null;
                }
                if(empty($customer['Email'])){
                    continue;
                }
                if(empty($customer['Mobile'])){
                    $customer['Mobile'] = null;
                }

                $email_array = explode(',', $customer['Email']);
                $email = trim(strval(array_shift($email_array)));
                $other_emails = !empty($email_array) ? trim(strval(join(',', $email_array))) : '';

                $sortData = [
                    ['g5_id' => $customer['CustomerID']],
                    ['email' => $customer['Email']],
                    ['phone' => $customer['Mobile']]
                ];

                $user = $this->findByOrFirst($sortData);
                if($user){
                    $user->wallet()->update(['balance' => $customer['Debt'] < 0 ? abs($customer['Debt']) : -1 * abs($customer['Debt'])]);
                    continue;
                }

                $balance = $customer['Debt'] < 0 ? abs($customer['Debt']) : -1 * abs($customer['Debt']);
                $user = $this->store([
                    'firstname' => $customer['CustomerName'],
                    'lastname' => $customer['FamilyName'],
                    'phone' => $customer['Mobile'] != '' ? $customer['Mobile'] : $customer['Phone'],
                    'gender' => ucfirst($customer['Sex']),
                    'marital_status' => ($customer['MartialStatus']) ? ucfirst($customer['MartialStatus']) : 'Single',
                    'address' => $customer['Street'] . ' ' .  $customer['City'] . ' ' . $customer['State'],
                    'photo' => "https://avatars.dicebear.com/api/initials/" . $customer['CustomerName'] . ".svg",
                    'dob' => Carbon::parse($customer['BirthDay'])->format('Y-m-d'),
                    'email' => $email,
                    'other_emails' => $other_emails,
                    'membership_id' => !empty($product = Product::where('name', 'JetSki')->first()) ? $product->id : null
                ], $balance);
                
            }

            return true;
        } catch(Exception $e){
            Log::error($e->getMessage());
            return false;
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

    public function all_members($limit=null)
    {
        $order = [
            ['firstname', 'asc'],
            ['lastname', 'asc'],
            ['created_at', 'asc']
        ];

        $users = $this->all($order, $limit);
        return $users;
    }

    public function resend_activation_link(User $user)
    {
        if($user->email_verified == 1){
            $this->errors = "Email already verified";
            return false;
        }

        $user->verification_token = Str::random(20).time();
        $user->verification_token_expiry = date('Y-m-d H:i:s', time() + (60 * 60 * 24));
        $user->save();
        $user->name = $user->firstname;
        Mail::to($user)->send(new AddUserNotificationMail($user->name, $user->verification_token));

        return true;
    }
}