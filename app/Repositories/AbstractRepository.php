<?php

namespace App\Repositories;

use App\Repositories\Interfaces\AbstractRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class AbstractRepository implements AbstractRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create($data){
        if(!$model = $this->model->create($data)){
            return false;
        }
        return $model;
    }

    public function all(){
        return $this->model->all();
    }

    public function find(int $id){
        $data = $this->model->find($id);

        if(empty($data)){
            return false;
        }

        return $data;
    }

    public function findBy(array $criteria, $orderBy=[], $limit=null){
        if(empty($criteria)){
            return false;
        }

        $data = $this->model->where($criteria);
        if(!empty($orderBy)){
            foreach($orderBy as $order){
                $data = $data->orderBy($order[0], $order[2]);
            }
        }

        if(isset($limit)){
            $data = $data->paginate($limit);
        } else {
            $data = $data->get();
        }

        return $data;
    }

    public function findFirstBy(array $criteria, $orderBy=[])
    {
        if(empty($criteria)){
            return false;
        }
        
        $data = $this->model->where($criteria);
        if(!empty($orderBy)){
            foreach($orderBy as $order){
                $data = $data->orderBy($order[0], $order[1]);
            }
        }
        
        return $data->first();
    }

    public function paginate($limit){
        return $this->model->paginate($limit);
    }
}