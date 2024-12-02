<?php

namespace App\Repositories;

use App\Models\Advert;
use App\Repositories\Interfaces\AdsRepositoryInterface;
use Illuminate\Http\Request;

class AdsRepository extends AbstractRepository implements AdsRepositoryInterface
{
    public $errors;

    public function __construct(Advert $advert)
    {
        parent::__construct($advert);
    }   

    public function store(Request $request)
    {
        
    }
}