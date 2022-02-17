<?php

namespace App\Repositories;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
// use Illuminate\Http\Request as HttpRequest;

abstract class BaseRepository {

    protected $model;
    protected $request;
    public function __construct(\Illuminate\Http\Request $request){
        $this->model = $this->resolveModel();
        $this->request = $request;
    }

    protected function resolveModel(){
        return app($this->model);
    }


    public function all()
    {
        return $this->model->all();
    }

    public function getById($id){
        return $this->model->find($id);
    }

    public function getByKey($key, $value){
        try{
            return $this->model->where($key, $value)->first();
        } catch(Exception $err) {
            return response()->json(['error' => $err->getMessage()], 500)->withException($err);
        }
    }

    public function store($request)
    {
        try{
            return $this->model->create($request);
        } catch(Exception $err) {
            return response()->json(['error' => $err->getMessage()], 500)->withException($err);
        }
    }

    public function edit($id, $request)
    {
        try {
            if($this->model->find($id))
                return $this->model->find($id)->update($request);
            else
                throw new Exception('Register not found!!!', 404);
        } catch(QueryException $err){
            return response()->json(['error' => $err->getMessage()], 500);
        } catch(Exception $err) {
            // dd($err);
            return response()->json(['error' => $err->getMessage()], $err->getCode());
        }
    }

    public function destroy($id)
    {
        try {
            if($this->model->find($id))
                return $this->model->find($id)->delete();
            else
                throw new Exception('Register not found!!!', 404);
        } catch(Exception $err) {
            return response()->json(['error' => $err->getMessage()], $err->getCode());
        } catch(QueryException $err){
            return response()->json(['error' => $err->getMessage()], 500);
        }
    }


}
