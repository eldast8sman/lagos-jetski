<?php

namespace App\Repositories;

use App\Events\AdminRegistered;
use App\Models\Admin;
use App\Repositories\Interfaces\AdminRepositoryInterface;
use App\Services\AuthService;
use App\Services\FileManagerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminRepository extends AbstractRepository implements AdminRepositoryInterface
{
    public $errors = "";
    public function construct(Admin $admin){
        parent::__construct($admin);
    }

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

        return $admin;
    }

    public function activate(Request $request)
    {
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

        return $admin;
    }
}