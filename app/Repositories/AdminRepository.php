<?php

namespace App\Repositories;

use App\Events\AdminRegistered;
use App\Models\Admin;
use App\Repositories\Interfaces\AdminRepositoryInterface;
use App\Services\FileManagerService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminRepository extends AbstractRepository implements AdminRepositoryInterface
{
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
        $all['verification_token'] = $verification_token;
        $all['verification_token_expiry'] = date('Y-m-d H:i:s', time() + (60 * 60 * 24));

        $admin = $this->create($all);

        event(new AdminRegistered($admin));

        return $admin;
    }
}