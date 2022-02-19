<?php

namespace App\Http\Middleware;

use App\Http\Requests\SupplierStorePostRequest;
use App\Http\Requests\SupplierUpdateRequest;
use App\Models\Company;
use App\Repositories\CompanyRepository;
use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Http\Request;

class ageVerificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        if(request()->getMethod() == 'PUT') app(SupplierUpdateRequest::class);
        if(request()->getMethod() == 'POST') app(SupplierStorePostRequest::class);
        if($company = app(CompanyRepository::class)->getById($request->get('company_id'))){
            $age = Carbon::now()->diffInYears($request->get('birth_date'));
            if ($company->state == 'PR' && $request->get('CPF') && $age < 18)
                return response()->json(['error' => "Fornecedor não pode ser menor de idade na empresa {$company->name}"], 422);
        }

        return $next($request);
    }
}
