<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface AdsRepositoryInterface extends AbstractRepositoryInterface
{
    public function store(Request $request);

    public function index($limit=10);

    public function show(int $id);

    public function edit(int $id, Request $request);

    public function destroy($id);
}