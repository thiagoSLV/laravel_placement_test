<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Repositories\CompanyRepository;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\TestCase;

class CompanyTest extends TestCase
{
    public function __construct() {
        parent::__construct();
        $this->repository = app('App\Repositories\CompanyRepository');
        $app = require __DIR__.'/../../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
    }

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:fresh');
    }
    /** @test */
    public function getAll()
    {
        Company::factory()->count(5)->create();
        $response = $this->repository->all();
        $this->assertCount(5, $response);
    }
    /** @test */
    public function success_getById()
    {
        $company = Company::factory([
            'name' => 'test_company',
            'state' => 'PR',
            'CNPJ' => 000000000000000,
        ])->create();
        $response = $this->repository->getById($company->id);
        $this->assertNotNull($response);
        $this->assertInstanceOf(Company::class, $response);
    }
    /** @test */
    public function fail_getById()
    {
        $company = Company::factory([
            'name' => 'test_company',
            'state' => 'PR',
            'CNPJ' => 000000000000000,
        ])->create();
        $response = $this->repository->getById(10);
        $this->assertNull($this->repository->getById(10));
        $this->assertNull($this->repository->getById('wrong_value'));

    }
    /** @test */
    public function success_getByKey()
    {
        $company = Company::factory([
            'name' => 'test_company',
            'state' => 'PR',
            'CNPJ' => 000000000000000,
        ])->create();
        $responses[] = $this->repository->getByKey('id', $company->id);
        $responses[] = $this->repository->getByKey('name', $company->name);
        $responses[] = $this->repository->getByKey('CNPJ', $company->CNPJ);

        foreach($responses as $response){
            $this->assertNotNull($response);
        }
    }
    /** @test */
    public function fail_getByKey()
    {
        $company = Company::factory([
            'name' => 'test_company',
            'state' => 'PR',
            'CNPJ' => 000000000000000,
        ])->create();

        $response = $this->repository->getByKey('wrong_column', $company->id);
        $this->assertEquals($response->getStatusCode(), 500);
        $this->assertArrayHasKey('error', $response->getOriginalContent());

        $response = $this->repository->getByKey('id', 'wrong_value_type');
        $this->assertNull($response);
    }
    /** @test */
    public function success_create()
    {
        $response = $this->repository->store([
            'name' => 'test_company',
            'state' => 'PR',
            'CNPJ' => 000000000000000,
        ]);
        $this->assertInstanceOf(Company::class, $response);
    }
    /** @test */
    public function fail_create()
    {
        $responses[] = $this->repository->store([
            'state' => 'PR',
            'CNPJ' => 000000000000000,
        ]);
        $responses[] = $this->repository->store([
            'name' => 'test_company',
            'CNPJ' => 000000000000000,
        ]);
        $responses[] = $this->repository->store([
            'name' => 'test_company',
            'state' => 'PR',
        ]);

        foreach($responses as $response){
            $this->assertEquals($response->getStatusCode(), 500);
            $this->assertArrayHasKey('error', $response->getOriginalContent());
        }
    }
    public function success_edit()
    {
        $newName = 'new_name';
        $supplier = Company::factory(['name' => 'test_name'])->create();
        $this->assertTrue($this->repository->edit($company->id, [
            'name' => $newName
        ]));
        $response = Company::where(['name' => $newName])->first();
        $this->assertNotNull($response);
        $this->assertEquals($response['name'], $newName);
    }
    /** @test */
    public function fail_edit()
    {
        $newName = 'new_name';
        $company = Company::factory(['name' => 'test_name'])->create();
        $response = $this->repository->edit('wrong_value', [
            'name' => $newName
        ]);
        $this->assertEquals($response->getStatusCode(), 404);
        $this->assertArrayHasKey('error', $response->getOriginalContent());

        $response = $this->repository->edit($company->id, [
            'CNPJ' => $newName
        ]);
        $this->assertEquals($response->getStatusCode(), 500);
        $this->assertArrayHasKey('error', $response->getOriginalContent());
    }
    /** @test */
    public function success_destroy()
    {
        Company::factory()->count(5)->create();
        $response = $this->repository->destroy(1);
        $this->assertTrue($response);
    }
    /** @test */
    public function fail_destroy()
    {
        Company::factory()->count(5)->create();
        $response = $this->repository->destroy('wrong_value');
        $this->assertEquals($response->getStatusCode(), 404);
        $this->assertArrayHasKey('error', $response->getOriginalContent());
    }

}
