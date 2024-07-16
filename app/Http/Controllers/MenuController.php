<?php

namespace App\Http\Controllers;

use App\Repositories\Interfaces\MenuRepositoryInterface;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    protected $menu;

    public function __construct(MenuRepositoryInterface $menu)
    {
        $this->menu = $menu;
    }

    public function index(){
        
    }
}
