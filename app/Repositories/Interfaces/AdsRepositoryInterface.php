<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface AdsRepositoryInterface extends AbstractRepositoryInterface
{
    public function store(Request $request, $type="regular");

    public function index($limit=10, $type="regular");

    public function show(string $id);

    public function edit(string $id, Request $request);

    public function change_status(string $id);

    public function destroy($id);
}