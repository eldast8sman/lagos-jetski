<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExcelUploadRequest;
use App\Http\Resources\UserResource;
use App\Imports\MembershipImport;
use App\Models\User;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Services\G5PosService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

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

    public function store_bulk(ExcelUploadRequest $request){
        try {
            $file = $request->file('file');
            $path = $file->store('temp');

            Excel::import(new MembershipImport, $path);

            return $this->success_response("File Processing in progress");
        } catch(Exception $e){
            Log::error('Excel Error '. $e->getMessage());
            return $this->failed_response('Excel File Processing Failed', 500);
        }
    }

    public function add_test_user(){
        $data = $this->test_data();
        $store = $this->user->store($data, 0);

        return $this->success_response("User added", $store);
    }
}
