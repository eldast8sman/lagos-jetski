<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advert extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'type',
        'description',
        'image_banner',
        'ads_link'
    ];

    public function banner(){
        return $this->belongsTo(FileManager::class, 'image_banner', 'id');
    }
}
