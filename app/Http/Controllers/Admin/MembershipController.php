<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
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
        $users = $this->user->all_members($limit);

        return $this->success_response("Members fetched successfully", UserResource::collection($users)->response()->getData(true));
    }

    public function store_g5_members(){
        $users = $this->user->fetch_g5_customers();

        return $this->success_response("Users added to DB", $users);
    }
}
