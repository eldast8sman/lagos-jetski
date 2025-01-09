<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Services\G5PosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MembershipController extends Controller
{
    protected $user;

    public function __construct(MemberRepositoryInterface $member)
    {
        $this->user = $member;
    }

    public function index(Request $request){
        $limit = $request->has('limit') ? $request->limit : 20;
        $users = $this->user->index($limit);

        return $this->success_response("Members fetched successfully", UserResource::collection($users)->response()->getData(true));
    }

    public function store_g5_members(){
        $users = $this->user->fetch_g5_customers();

        return $this->success_response("Users added to DB", $users);
    }

    public function resend_activation_link(User $user){
        if(!$this->user->resend_activation_link($user)){
            return $this->failed_response($this->user->errors, 400);
        }
        return $this->success_response("Email verification Link successfully sent");
    }

    public function add_test_user(){
        $data = $this->test_data();
        $store = $this->user->store($data, 0);

        return $this->success_response("User added", $store);
    }
}
