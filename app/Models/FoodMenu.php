<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class FoodMenu extends Model
{
    use HasSlug;

    protected $fillable = [
        'uuid',
        'slug',
        'menu_category_id',
        'name',
        'description',
        'amount',
        'availability',
        'g5_id',
        'shelf_life_from',
        'shelf_life_to',
        'ingredients',
        'details',
        'total_orders',
        'parent_id',
        'modifier_id',
        'is_stand_alone',
        'is_add_on',
        'add_ons'
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function add_ons(){
        $add_on_ids = explode(',', $this->add_ons);
    }
}
