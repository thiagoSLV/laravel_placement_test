<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyStorePostRequest;
use App\Http\Requests\CompanyUpdateRequest;
use App\Repositories\CompanyRepository;

class CompanyController extends Controller
{

    protected $repository;
    public function __construct(){
        $this->repository = app(CompanyRepository::class);
    }

    public function all(){
        return $this->repository->all();
    }

    public function getById($id){
        return response()->json($this->repository->getById($id));
    }

    public function store(CompanyStorePostRequest $request){
        return $this->repository->store($request->all());
    }

    public function edit($id, CompanyUpdateRequest $request){
        return $this->repository->edit($id, $request->all());
    }

    public function destroy($id){
        return $this->repository->destroy($id);
    }
}
