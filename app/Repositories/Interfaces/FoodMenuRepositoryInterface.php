<?php

namespace App\Repositories\Interfaces;

interface FoodMenuRepositoryInterface extends AbstractRepositoryInterface
{
    public function index($limit=10, $category_id=null);

    public function store(array $data);

    public function show(string $identifier);

    public function update_menu(string $identifier, array $data);

    public function availability(string $identifier);
}