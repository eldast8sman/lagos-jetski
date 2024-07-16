<?php

namespace App\Repositories;

use App\Events\UserRegistered;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\Interfaces\UserRelativeRepositoryInterface;
use App\Services\FileManagerService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserRelativeRepository extends AbstractRepository implements UserRelativeRepositoryInterface
{
    public $errors = "";
    
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    private function check(){
        if(!empty(auth('user-api')->user()->parent_id)){
            $this->errors = "s a relative, you do not have access to this feature";
            return false;
        }

        return true;
    }

    public function store(Request $request)
    {
        if(!$this->check()){
            return false;
        }
        $all = $request->except(['photo']);
        if(!empty($request->photo)){
            $photo = FileManagerService::upload_file($request->file('photo'), env('FILESYSTEM_DISK'));
            if($photo){
                $all['photo'] = $photo->url;
            }
        }
        $verification_token = Str::random(20).time();
        $all['uuid'] = Str::uuid().'-'.time();
        $all['verification_token'] = $verification_token;
        $all['verification_token_expiry'] = date('Y-m-d H:i:s', time() + (60 * 60 * 24));
        $all['parent_id'] = auth('user-api')->user()->id;
        $all['g5_id'] = auth('user-api')->user()->g5_id;

        $user = $this->create($all);
        if(!$user){
            $this->errors = $this->error_msg;
            return false;
        }

        Wallet::create([
            'uuid' => Str::uuid().'-'.time(),
            'user_id' => $user->id,
            'balance' => 0
        ]);

        UserRegistered::dispatch($user);

        return $user;
    }

    public function getRelatives()
    {
        if(!$this->check()){
            return false;
        }

        $data = ['parent_id' => auth()->user()->id];
        $relatives = $this->findBy($data);

        return $relatives;
    }

    public function getRelative($id)
    {
        if(!$this->check()){
            return false;
        }

        $data = ['parent_id' => auth('user-api')->user()->id, 'id' => $id];
        $relative = $this->findFirstBy($data);
        if(empty($relative)){
            $this->errors = "No Relative was fetched";
            return false;
        }

        return $relative;
    }

    public function updateRelative(Request $request, int $id)
    {
        if(!$this->check()){
            return false;
        }

        $data = ['parent_id' => auth('user-api')->user()->id, 'id' => $id];
        $old_rel = $this->findFirstBy($data);
        if(empty($old_rel)){
            $this->errors = "No Relative was fetched";
            return false;
        }
        $new_photo = false;
        $all = $request->except(['photo']);
        if(!empty($request->photo)){
            $photo = FileManagerService::upload_file($request->file('photo'), env('FILESYSTEM_DISK'));
            if($photo){
                $all['photo'] = $photo->url;
                $new_photo = true;
            }
        }
        $relative = $this->update($id, $all);
        if(!empty($old_rel->photo) and $new_photo){
            $old_photo = FileManagerService::findByUrl($old_rel->photo);
            if(!empty($old_photo)){
                FileManagerService::delete($old_photo->id);
            }
        }
        return $relative;
    }

    public function deleteRelative(int $id)
    {
        if(!$this->check()){
            return false;
        }
        $data = ['parent_id' => auth('user-api')->user()->id, 'id' => $id];
        $relative = $this->findFirstBy($data);
        if(empty($relative)){
            $this->errors = "No Relative was found";
            return false;
        }

        $this->delete($relative);
        if(!empty($relative->photo)){
            $pics = FileManagerService::findByUrl($relative->photo);
            FileManagerService::delete($pics->id);
        }
        return true;
    }
}