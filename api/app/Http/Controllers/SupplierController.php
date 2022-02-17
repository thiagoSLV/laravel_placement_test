<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierStorePostRequest;
use App\Http\Requests\SupplierUpdateRequest;
use App\Repositories\SupplierRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SupplierController extends Controller
{
    public function __construct(){
        $this->repository = app(SupplierRepository::class);
    }

    public function all(){
        return $this->repository->all();
    }

    public function getById($id){
        return response()->json($this->repository->getById($id));
    }

    public function store(SupplierStorePostRequest $request){
        return $this->repository->store($request->all());
    }

    public function edit($id, SupplierUpdateRequest $request){
        $supplier = $this->getById($id);
        if($supplier->getData()->CNPJ && Arr::hasAny($request->all(), ['RG', 'birth_date'])){
            $request = Arr::except($request, 'RG', 'birth_date');
        }
        return $this->repository->edit($id, $request->all());
    }

    public function destroy($id){
        return $this->repository->destroy($id);
    }
}
