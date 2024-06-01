<?php

namespace App\Repositories;

use App\Repositories\Interfaces\AbstractRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Model;

class AbstractRepository implements AbstractRepositoryInterface
{
    protected $model;

    public function __construct($model)
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
        try {
            $data = $this->model->find($id);
            return $data;
        } catch(Exception $e){
            return false;
        }
    }

    public function findBy(array $criteria, $orderBy=[], $limit=null){
        try {
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
        } catch(Exception $e){
            return false;
        }
    }

    public function findFirstBy(array $criteria, $orderBy=[])
    {
        try {
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
        } catch(Exception $e){
            return false;
        }
    }

    public function paginate($limit){
        return $this->model->paginate($limit);
    }

    public function update($id, $data=[]){
        try {
            if(!$model = $this->find($id)){
                return false;
            }
    
            $model->update($data);
        } catch(Exception $e){
            return false;
        }
    }

    public function save(Model $model){
        $model = $model->save();
    }

    public function delete(Model $model){
        $model->delete();
    }
}