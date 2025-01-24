<?php

namespace App\Repositories;

use App\Models\FoodMenu;
use App\Repositories\Interfaces\FoodMenuRepositoryInterface;

class FoodMenuRepository extends AbstractRepository implements FoodMenuRepositoryInterface
{
    public $errors;

    public function __construct(FoodMenu $menu)
    {
        parent::__construct($menu);
    }

    public function index($limit=10, $category_id=null)
    {
        if(!empty($catefory_id)){
            $menus = $this->findBy(['menu_category_id' => $category_id], [['name', 'asc']], $limit);
        }
    }

    public function store(array $data) {

    }
}