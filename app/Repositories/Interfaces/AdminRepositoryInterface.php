<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface AdminRepositoryInterface extends AbstractRepositoryInterface
{
    public function store(Request $request);
}