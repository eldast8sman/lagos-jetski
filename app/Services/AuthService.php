<?php

namespace App\Services;

use App\Models\Admin;
use Exception;
use PDO;

class AuthService
{
    private $guard;
    public function __construct($guard='user-api'){
        $this->guard = $guard;
    }

    public function attempt($data) : array|bool
    {
        if(!$token = auth($this->guard)->attempt($data)){
            return false;
        }

        if($this->guard == 'admin-api'){
            $user = Admin::where('email', $data['email'])->first();
        } elseif($this->guard == 'user-api') {
            //
        }
        $user->prev_login = $user->last_login;
        $user->last_login = date('Y-m-d H:i:s');
        $user->save();

        return [
            'token' => $token,
            'type' => 'Bearer',
            'expires' => env('JWT_TTL') * 60
        ];
    }

    public function logout() : void
    {
        auth($this->guard)->logout();
    }

    public function logged_in_user(){
        return auth($this->guard)->user();
    }

    public function login($user) : array|bool
    {
        if(!$token = auth($this->guard)->login($user)){
            return false;
        }

        if($this->guard == 'admin-api'){
            $user = Admin::find($user->id);
        } elseif($this->guard == 'user-api') {
            //
        }
        $user->prev_login = $user->last_login;
        $user->last_login = date('Y-m-d H:i:s');
        $user->save();

        return [
            'token' => $token,
            'type' => 'Bearer',
            'expires' => env('JWT_TTL') * 60
        ];
    }

    public function refresh_token() : array|bool
    {
        try {
            $token = auth($this->guard)->refresh();

            $data = [
                'token' => $token,
                'type' => 'Bearer',
                'expires' => env('JWT_TTL') * 60
            ];

            return $data;
        } catch(Exception $e){
            return false;
        }
    }
}