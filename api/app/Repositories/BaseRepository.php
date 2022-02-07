<?php

namespace App\Repositories;

use Illuminate\Http\Client\Request;
// use Illuminate\Http\Request as HttpRequest;

abstract class BaseRepository {

    protected $model;

    public function __construct(){
        $this->model = $this->resolveModel();
    }

    protected function resolveModel(){
        return  app($this->model );
    }


    public function all()
    {
        return $this->model->all();
    }

    public function store(Request $request)
    {
        return $this->model->create($request->all());
    }

    public function destroy($id)
    {
        return $this->model->find($id)->delete();
    }

    public function edit($id,Request $request)
    {
        return $this->model->find($id)->update($request->all());
    }

}
