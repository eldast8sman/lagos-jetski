<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface AdsRepositoryInterface extends AbstractRepositoryInterface
{
    public function store(Request $request);

    public function index($limit=10);

    public function show(string $id);

    public function edit(string $id, Request $request);

    public function destroy($id);
}