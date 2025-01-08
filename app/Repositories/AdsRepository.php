<?php

namespace App\Repositories;

use App\Models\Advert;
use App\Repositories\Interfaces\AdsRepositoryInterface;
use App\Services\FileManagerService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdsRepository extends AbstractRepository implements AdsRepositoryInterface
{
    public $errors;

    public function __construct(Advert $advert)
    {
        parent::__construct($advert);
    }   

    public function store(Request $request, $type="regular")
    {
        if($type == "popup"){
            $count = $this->findBy(['type' => 'popup'], [], null, true);
            if($count >= 2){
                $this->errors = "You can only have 2 popup Ads at a time";
                return false;
            }
        }
        $all = $request->except(['image_banner']);
        $all['status'] = 1;
        if($request->has('image_banner') and !empty($request->image_banner)){
            $photo = FileManagerService::upload_file($request->file('image_banner'), env('FILESYSTEM_DISK'));
            if($photo){
                $all['image_banner'] = $photo->id;
            }
        }
        $all['uuid'] = Str::uuid().'-'.time();
        $all['type'] = $type;
        if(!$ad = $this->create($all)){
            $this->errors = "Advert Upload Failed";
        }
        return $ad;
    }

    public function index($limit = 10, $type="regular")
    {
        $ads = $this->findBy(['type' => $type], [], $limit);
        return $ads;
    }

    public function user_index($type = "regular")
    {
        $ads = $this->findBy(['type' => $type, 'status' => 1], []);
        return $ads;
    }

    public function show(string $id)
    {
        $ad = $this->findFirstBy(['uuid' => $id]);
        if(empty($ad)){
            $this->errors = "No Advert was fetched";
            return false;
        }
        return $ad;
    }

    public function edit($id, $request)
    {
        $ad = $this->findFirstBy(['uuid' => $id]);
        if(empty($ad)){
            $this->errors = "No Advert was fetched";
            return false;
        }
        $all = $request->except(['image_banner']);
        if($request->has('image_banner') and !empty($request->image_banner)){
            $photo = FileManagerService::upload_file($request->file('image_banner'), env('FILESYSTEM_DISK'));
            if($photo){
                $all['image_banner'] = $photo->id;
                $old_photo = $ad->image_banner;
            }
        }
        $ad->update($all);
        if(isset($old_photo)){
            FileManagerService::delete($old_photo);
        }

        return $ad;
    }
    
    public function change_status(string $id)
    {
        if(empty($ad = $this->findFirstBy(['uuid' => $id]))){
            $this->errors = "No Advert was fetched";
            return false;
        }
        $ad->status = ($ad->status == 0) ? 1 : 0;
        $ad->save();

        return $ad;
    }

    public function destroy($id)
    {
        $ad = $this->findFirstBy(['uuid' => $id]);
        if(empty($ad)){
            $this->errors = "No Advert was fetched";
            return false;
        }

        $this->delete($ad);
        if(!empty($ad->image_banner)){
            FileManagerService::delete($ad->image_banner);
        }

        return true;
    }
}