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

    public function store(Request $request)
    {
        $all = $request->except(['image_banner']);
        if($request->has('image_banner') and !empty($request->image_banner)){
            $photo = FileManagerService::upload_file($request->file('image_banner'), env('FILESYSTEM_DISK'));
            if($photo){
                $all['image_banner'] = $photo->id;
            }
        }
        $all['uuid'] = Str::uuid().'-'.time();
        if(!$ad = $this->create($all)){
            $this->errors = "Advert Upload Failed";
        }
        return $ad;
    }

    public function index($limit = 10)
    {
        $ads = $this->all([], $limit);
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