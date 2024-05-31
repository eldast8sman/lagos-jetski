<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ActivateAccountRequest;
use App\Http\Requests\Admin\ChangeAccountDetailsRequest;
use App\Http\Requests\Admin\ChangePasswordRequest;
use App\Http\Requests\Admin\ForgotPasswordRequest;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Mail\Admin\AddAdminNotificationMail;
use App\Mail\Admin\ForgotPasswordMail;
use App\Models\Admin;
use App\Repositories\Interfaces\AdminRepositoryInterface;
use App\Services\AuthService;
use App\Services\FileManagerService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private $auth;
    protected $admin;

    public function __construct(AdminRepositoryInterface $admin)
    {
        $this->admin = $admin;
        $this->auth = new AuthService('admin-api');
    }

    public function store(Request $request){
        if(Admin::count() > 0){
            return $this->failed_response("There is already an Admin", 409);
        }
        $admin = $this->admin->store($request);
        if(!$admin){
            return $this->failed_response("Failed to add Admin", 500);
        }

        return $this->success_response("Admin added successfully", $admin);
    }

    public function fetch_token($token){
        if(empty($admin = $this->admin->findFirstBy(['verification_token' => $token]))){
            return $this->failed_response("No Account was fetched", 404);
        }

        return $this->success_response("Account fetched successfully", $admin);
    }

    public function activate_account(ActivateAccountRequest $request){
        $admin = $this->admin->activate($request);
        if(!$admin){
            return $this->failed_response($this->admin->errors, 409);
        }        
        $admin->authorization = $this->auth->login($admin);

        return $this->success_response("Account activated succssfully", $admin);
    }

    public function login(LoginRequest $request){
        $all = $request->all();
        if(!$token = $this->auth->attempt($all)){
            $this->failed_response("Wrong Login Credentials");
        }

        $admin = Admin::where('email', $request->email)->first();
        $admin->prev_login = $admin->last_login;
        $admin->last_login = date('Y-m-d H:i:s');
        $admin->save();

        $admin->authorization = $token;

        return $this->success_response("LogIn successful", $admin);
    }

    public function refresh_token(){
        if(!$token = $this->auth->refresh_token()){
            return $this->failed_response("Login Expired", 409);
        }

        return $this->success_response("Token successfully refreshed", $token);
    }

    public function forgot_password(ForgotPasswordRequest $request){
        $admin = Admin::where('email', $request->email)->first();
        $admin->token = Str::random(20).time();
        $admin->token_expiry = date('Y-m-d H:i:s', time() + (60 * 10));
        $admin->save();

        $admin->name = $admin->firstname;
        Mail::to($admin)->send(new ForgotPasswordMail($admin->name, $admin->token));

        return $this->success_response("Reset Password Link sent to ".$admin->email);
    }

    public function reset_password(ResetPasswordRequest $request){
        $admin = Admin::where('token', $request->token)->first();
        if(empty($admin)){
            return $this->failed_response("Wrong Link", 400);
        }
        if($admin->token_expiry < date('Y-m-d H:i:s')){
            return $this->failed_response("Expired Link", 400);
        }

        $admin->password = bcrypt($request->password);
        $admin->token = null;
        $admin->token_expiry = null;
        $admin->save();

        $this->success_response("Password changed successfully");
    }

    public function change_password(ChangePasswordRequest $request){
        $user = $this->auth->logged_in_user();

        if(!Hash::check($request->old_password, $user->password)){
            return $this->failed_response("Wrong Password", 409);
        }

        $user = Admin::find($user->id);
        $user->password = bcrypt($request->password);
        $user->save();

        $this->success_response("Password successfully changed");
    }

    public function update_account_details(ChangeAccountDetailsRequest $request){
        $admin = $this;
    }
}
