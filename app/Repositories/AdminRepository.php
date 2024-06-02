<?php

namespace App\Repositories;

use App\Events\AdminRegistered;
use App\Mail\Admin\ForgotPasswordMail;
use App\Models\Admin;
use App\Repositories\Interfaces\AdminRepositoryInterface;
use App\Services\AuthService;
use App\Services\FileManagerService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminRepository extends AbstractRepository implements AdminRepositoryInterface
{
    public $errors = "";
    public function construct(Admin $admin){
        parent::__construct($admin);
    }

    private $auth = new AuthService('admin-api');

    public function store(Request $request){
        $all = $request->except(['photo']);
        if(!empty($request->photo)){
            $photo = FileManagerService::upload_file($request->file('photo'), env('FILESYSTEM_DISK'));
            if($photo){
                $all['photo'] = $photo->id;
            }
        }
        $verification_token = Str::random(20).time();
        $all['uuid'] = Str::uuid().'-'.time();
        $all['verification_token'] = $verification_token;
        $all['verification_token_expiry'] = date('Y-m-d H:i:s', time() + (60 * 60 * 24));

        $admin = $this->create($all);
        if(!$admin){
            return false;
        }

        event(new AdminRegistered($admin));

        return $this->admin($admin);
    }

    public function admin(Admin $admin) : Admin
    {
        if(!empty($admin->photo)){
            $admin->photo = FileManagerService::fetch_file($admin->photo);
        } else {
            $admin->photo = null;
        }
        $admin->bank_account_details = $admin->account()->first(['bank_name', 'account_number', 'account_name']);
        return $admin;
    }

    public function activate(Request $request)
    {
        try {
            $admin = $this->findFirstBy(['verification_token' => $request->token, 'activated' => 0]);
            if(empty($admin)){
                $this->errors = "Wrong Link";
                return false;
            }
            if(strtotime($admin->verification_token_expiry) < time()){
                $this->errors = "Expired Link";
                return false;
            }
    
            $admin->verification_token = null;
            $admin->verification_token_expiry = null;
            $admin->password = Hash::make($request->password);
            $admin->activated = 1;
            $this->save($admin);
    
            return $this->admin($admin);
        } catch(Exception $e){
            $this->errors = "Server Error";
            return false;
        }
    }

    public function findByEmail($email){
        $admin = $this->findFirstBy(['email' => $email]);
        if(!$admin){
            return false;
        }
        return $this->admin($admin);
    }

    public function findByVerificationToken(string $token)
    {
        if(!$admin = $this->findFirstBy(['verification_token' => $token])){
            return false;
        }

        return $this->admin($admin);
    }

    public function findByToken($token){
        if(empty($admin = $this->findFirstBy(['token' => $token]))){
            return false;
        }

        return $this->admin($admin);
    }

    public function forgot_password(Request $request) : bool
    {
        $admin = $this->findByEmail($request->email);
        if(empty($admin)){
            return false;
        }
        $token = Str::random(20).time();
        $this->update($admin->id, [
            'token' => $token,
            'token_expiry' => date('Y-m-d H:i:s', time() + (60 * 10))
        ]);

        $admin->name = $admin->firstname;
        Mail::to($admin)->send(new ForgotPasswordMail($admin->name, $token));

        return true;
    }

    public function reset_password(Request $request)
    {
        if(empty($admin = $this->findByToken($request->token))){
            $this->errors = "Wrong Link";
            return false;
        }
        if($admin->token_expiry < date('Y-m-d H:i:s')){
            $this->errors = "Expired Link";
            return false;
        }

        $this->update($admin->id, [
            'token' => null,
            'token_expiry' => null,
            'password' => Hash::make($request->password)
        ]);

        return true;
    }

    public function update_account_details(Request $request)
    {
        $all = $request->all();
        $admin = $this->find($this->auth->logged_in_user()->id);
        $account = $admin->account()->first();
        $account->update($all);

        return $this->admin($admin);
    }

    public function update_profile(Request $request)
    {
        $admin = $this->auth->logged_in_user();
        try {
            $all = $request->except(['photo']);
            if(!empty($request->photo)){
                $photo = FileManagerService::upload_file($request->file('photo'), env('FILESYSTEM_DISK'));
                if($photo){
                    $all['photo'] = $photo->id;
                    if(!empty($admin->photo)){
                        FileManagerService::delete($admin->photo);
                    }
                }
            }
            if($this->update($admin->id, $request->all())){
                return false;
            }

            return $this->admin($admin);
        } catch(Exception $e){
            return false;
        }
    }
}