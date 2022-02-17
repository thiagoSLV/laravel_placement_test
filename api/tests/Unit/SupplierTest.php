<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Models\Supplier;
use Carbon\Carbon;
use Database\Factories\GenerateCNPJ;
use Database\Factories\GenerateCPF;
use Database\Factories\GenerateRG;
use PHPUnit\Framework\TestCase;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;

class SupplierTest extends TestCase
{
    use GenerateCPF;
    use GenerateCNPJ;
    use GenerateRG;
    public function __construct() {
        parent::__construct();
        $this->repository = app('App\Repositories\SupplierRepository');
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
        Supplier::factory()->count(5)->create();
        $response = $this->repository->all();
        $this->assertCount(5, $response);
    }
    /** @test */
    public function success_getById()
    {
        $supplier = Supplier::factory()->create();
        $response = $this->repository->getById($supplier->id);
        $this->assertNotNull($response);
        $this->assertInstanceOf(Supplier::class, $response);
    }
    /** @test */
    public function fail_getById()
    {
        $supplier = Supplier::factory()->create();
        $response = $this->repository->getById(10);
        $this->assertNull($this->repository->getById(10));
        $this->assertNull($this->repository->getById('wrong_value'));

    }
    /** @test */
    public function success_getByKey()
    {
        $supplier = Supplier::factory()->create();
        $responses[] = $this->repository->getByKey('id', $supplier->id);
        $responses[] = $this->repository->getByKey('name', $supplier->name);
        $responses[] = $this->repository->getByKey('CNPJ', $supplier->CNPJ);

        foreach($responses as $response){
            $this->assertNotNull($response);
        }
    }
    /** @test */
    public function fail_getByKey()
    {
        $supplier = Supplier::factory()->create();

        $response = $this->repository->getByKey('wrong_column', $supplier->id);
        $this->assertEquals($response->getStatusCode(), 500);
        $this->assertArrayHasKey('error', $response->getOriginalContent());

        $response = $this->repository->getByKey('id', 'wrong_value_type');
        $this->assertNull($response);
    }
    /** @test */
    public function success_create()
    {
        $birth_date = Carbon::today()
            ->subDays(rand(0, 30))
            ->subMonths(rand(0, 12))
            ->subYears(rand(0,40))
            ->format('Y-m-d');
        $company = Company::factory()->create();

        $responses[] = $this->repository->store([
            'company_id' => $company->id,
            'name' => 'test_name',
            'state' => 'SC',
            'CNPJ' => $this->CNPJ(),
            'phone_numbers' => json_encode([0000000]),
        ]);

        $responses[] = $this->repository->store([
            'company_id' => $company->id,
            'name' => 'test_name',
            'state' => 'SC',
            'CPF' => $this->CPF(),
            'RG' => $this->RG(),
            'birth_date' => $birth_date,
            'phone_numbers' =>  json_encode([0000000]),
        ]);
        foreach ($responses as $response) {
            $this->assertInstanceOf(Supplier::class, $response);
        }
    }
    /** @test */
    public function fail_create()
    {
        $birth_date = Carbon::today()
            ->subDays(rand(0, 30))
            ->subMonths(rand(0, 12))
            ->subYears(rand(0,40))
            ->format('Y-m-d');
        $company = Company::factory()->create();

        $responses[] = $this->repository->store([
            'company_id' => $company->id,
            'name' => 'test_name',
            'state' => 'SC',
            'CPF' => $this->CPF(),
            'RG' => $this->RG(),
            'birth_date' => $birth_date,
            'phone_numbers' =>  [0000000],
        ]);

        $responses[] = $this->repository->store([
            'name' => 'test_name',
            'state' => 'SC',
            'CPF' => $this->CPF(),
            'RG' => $this->RG(),
            'birth_date' => $birth_date,
            'phone_numbers' =>  json_encode([0000000]),
        ]);

        foreach($responses as $response){
            $this->assertEquals($response->getStatusCode(), 500);
            $this->assertArrayHasKey('error', $response->getOriginalContent());
        }
    }
    /** @test */
    public function success_edit()
    {
        $newName = 'new_name';
        $supplier = Supplier::factory(['name' => 'test_name'])->create();
        $this->assertTrue($this->repository->edit($supplier->id, [
            'name' => $newName
        ]));
        $response = Supplier::where(['name' => $newName])->first();
        $this->assertNotNull($response);
        $this->assertEquals($response['name'], $newName);
    }
    /** @test */
    public function fail_edit()
    {
        $newName = 'new_name';
        $supplier = Supplier::factory(['name' => 'test_name'])->create();
        $response = $this->repository->edit('wrong_value', [
            'name' => $newName
        ]);
        $this->assertEquals($response->getStatusCode(), 404);
        $this->assertArrayHasKey('error', $response->getOriginalContent());
        $response = $this->repository->edit($supplier->id, [
            'CNPJ' => $newName
        ]);
        $this->assertEquals($response->getStatusCode(), 500);
        $this->assertArrayHasKey('error', $response->getOriginalContent());
    }
    /** @test */
    public function success_destroy()
    {
        Supplier::factory()->count(5)->create();
        $response = $this->repository->destroy(1);
        $this->assertTrue($response);
    }
    /** @test */
    public function fail_destroy()
    {
        Supplier::factory()->count(5)->create();
        $response = $this->repository->destroy('wrong_value');
        $this->assertEquals($response->getStatusCode(), 404);
        $this->assertArrayHasKey('error', $response->getOriginalContent());
    }

}
