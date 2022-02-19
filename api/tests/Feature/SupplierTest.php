<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Supplier;
use Carbon\Carbon;
use Database\Factories\GenerateCNPJ;
use Database\Factories\GenerateCPF;
use Database\Factories\GenerateRG;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Session\Session;
use Tests\TestCase;
class SupplierTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use GenerateCPF;
    use GenerateCNPJ;
    use GenerateRG;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    /** @test */
    public function getAll()
    {
        Supplier::factory()->count(5)->create();
        $response = $this->getJson('/api/supplier');
        $response->assertOk();
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has(5)->each(fn ($json) =>
                $json->hasAll(
                    'company_id',
                    'name',
                    'state',
                    'phone_numbers',)
                ->hasAny(
                    'CNPJ',
                    'CPF',
                    'RG',
                    'birth_date')
                ->etc()
            )
        );
    }
    /** @test */
    public function success_getByKey(){
        $supplier = Supplier::factory()->create();
        $response = $this->getJson("/api/supplier/{$supplier->id}");
        $response->assertOk();
        $response->assertJson(fn (AssertableJson $json) =>
            $json->where('id', $supplier->id)
                ->hasAll(
                    'company_id',
                    'name',
                    'state',
                    'phone_numbers',)
                ->hasAny(
                    'CNPJ',
                    'CPF',
                    'RG',
                    'birth_date')
                ->etc()
        );
        $response = $this->getJson('/api/supplier/2');
        $response->assertOk();
        $response->assertExactJson([]);
    }
    /** @test */
    public function success_store(){
        $responses[] = $this->postJson('/api/supplier', [
            'company_id' => Company::factory()->create()->id,
            'name' => 'test_name',
            'state' => 'SC',
            'CNPJ' => $this->CNPJ(),
            'phone_numbers' => json_encode([0000000]),
        ]);

        $responses[] = $this->postJson('/api/supplier', [
            'company_id' => Company::factory()->create()->id,
            'name' => 'test_name',
            'state' => 'SC',
            'CPF' => $this->CPF(),
            'RG' => $this->RG(),
            'birth_date' => $this->faker->date('Y-m-d'),
            'phone_numbers' =>  json_encode([0000000]),
        ]);

        $responses[] = $this->postJson('/api/supplier', [
            'company_id' => Company::factory()->create()->id,
            'name' => 'test_name',
            'state' => 'SC',
            'CPF' => $this->CPF(),
            'RG' => $this->RG(),
            'birth_date' => '2000-08-12',
            'phone_numbers' =>  json_encode([0000000]),
        ]);

        foreach ($responses as $response) {
            $response->assertCreated();
            $response->assertJson(fn (AssertableJson $json) =>
                $json
                ->hasAll(
                    'company_id',
                    'name',
                    'state',
                    'phone_numbers',)
                ->hasAny(
                    'CNPJ',
                    'CPF',
                    'RG',
                    'birth_date')
                ->etc()
            );
        }

    }
    /** @test */
    public function fail_store(){
        $responses[] = $this->postJSON("/api/supplier", [
            'company_id' => Company::factory()->create()->id,
            'name' => $this->faker->name(),
            'state' => 'SC',
            'birth_date' => $this->faker->date('Y-m-d'),
            'phone_numbers' =>  json_encode([0000000]),
        ]);

        $responses[] = $this->postJSON("/api/supplier", [
            'company_id' => Company::factory()->create()->id,
            'name' => $this->faker->name(),
            'state' => 'SC',
            'CNPJ' => $this->CNPJ(),
            'CPF' => $this->CPF(),
            'RG' => $this->RG(),
            'birth_date' => $this->faker->date('Y-m-d'),
            'phone_numbers' =>  json_encode([0000000]),
        ]);

        $responses[] = $this->postJSON("/api/supplier", [
            'company_id' => Company::factory()->create()->id,
            'name' => $this->faker->name(),
            'state' => 'SC',
            'CNPJ' => $this->CNPJ(),
            'RG' => $this->RG(),
            'birth_date' => $this->faker->date('Y-m-d'),
            'phone_numbers' =>  json_encode([0000000]),
        ]);
        foreach($responses as $response){
            $response->assertUnprocessable();
            $response->assertJson(fn (AssertableJson $json) =>
                $json->hasAll('errors', 'failed')
                    ->etc()
            );
        }
    }
    /** @test */
    public function check_if_request_has_cpf_and_birth_date(){
        $responses[] = $this->postJSON("/api/supplier", [
            'company_id' => Company::factory()->create()->id,
            'name' => $this->faker->name(),
            'state' => 'SC',
            'CPF' => $this->CPF(),
            'birth_date' => $this->faker->date('Y-m-d'),
            'phone_numbers' =>  json_encode([0000000]),
        ]);

        $responses[] = $this->postJSON("/api/supplier", [
            'company_id' => Company::factory()->create()->id,
            'name' => $this->faker->name(),
            'state' => 'SC',
            'RG' => $this->RG(),
            'CPF' => $this->CPF(),
            'phone_numbers' =>  json_encode([0000000]),
        ]);

        foreach($responses as $response){
            $response->assertUnprocessable();
            $response->assertJson(fn (AssertableJson $json) =>
                $json->hasAny(
                    'failed.RG.RequiredWith',
                    'failed.birth_date.RequiredWith')
                    ->etc()
            );
        }
    }
    /** @test */
    public function check_if_cnpj_is_unique() {
        $supplier = Supplier::factory(['CNPJ' => $this->CNPJ()])->create();
        $response = $this->postJSON("/api/supplier", [
            'company_id' => Company::factory()->create()->id,
            'name' => $this->faker->name(),
            'state' => 'SC',
            'CNPJ' => $supplier->CNPJ,
            'phone_numbers' =>  json_encode([0000000]),
        ]);
        $response->assertUnprocessable();
        $response->assertJson(fn (AssertableJson $json) =>
            $json->hasAll('failed.CNPJ.Unique')
                    ->etc()
            );

    }
    /** @test */
    public function check_if_cpf_and_rg_are_unique() {
        $supplier = Supplier::factory(["CPF" => $this->CPF(), "RG" => $this->RG()])->create();
        $response = $this->postJSON("/api/supplier", [
            'company_id' => Company::factory()->create()->id,
            'name' => 'test_name',
            'state' => 'SC',
            'CPF' => $supplier->CPF,
            'RG' => $supplier->RG,
            'birth_date' => $this->faker->date('Y-m-d'),
            'phone_numbers' =>  json_encode([0000000]),
        ]);
        $response->assertUnprocessable();
        $response->assertJson(fn (AssertableJson $json) =>
            $json->hasAll('failed.CPF.Unique', 'failed.RG.Unique')
                    ->etc()
            );
    }
    /** @test */
    public function check_if_cnpj_is_valid(){
        $responses[] = $this->postJSON("/api/supplier", [
            'company_id' => Company::factory()->create()->id,
            'name' => $this->faker->name(),
            'state' => 'SC',
            'CNPJ' => 00000,
            'phone_numbers' =>  json_encode([0000000]),
        ]);
        $responses[] = $this->postJSON("/api/supplier", [
            'company_id' => Company::factory()->create()->id,
            'name' => $this->faker->name(),
            'state' => 'SC',
            'CNPJ' => 000000000000000,
            'phone_numbers' =>  json_encode([0000000]),
        ]);
        foreach($responses as $response){
            $response->assertUnprocessable();
            $response->assertJson(fn (AssertableJson $json) =>
                $json->hasAll('failed.CNPJ.Digits')
                    ->etc()
            );
        }
    }
    /** @test */
    public function check_if_cpf_and_rg_are_valid(){
        $responses[] = $this->postJSON("/api/supplier", [
            'company_id' => Company::factory()->create()->id,
            'name' => $this->faker->name(),
            'state' => 'SC',
            'CPF' => 00000,
            'RG' => 00000,
            'phone_numbers' =>  json_encode([0000000]),
        ]);
        $responses[] = $this->postJSON("/api/supplier", [
            'company_id' => Company::factory()->create()->id,
            'name' => $this->faker->name(),
            'state' => 'SC',
            'CPF' => 000000000000000,
            'RG' => 000000000000000,
            'phone_numbers' =>  json_encode([0000000]),
        ]);
        foreach($responses as $response){
            $response->assertUnprocessable();
            $response->assertJson(fn (AssertableJson $json) =>
                $json->hasAny('failed.CPF.Digits', 'failed.RG.Digits')
                    ->etc()
            );
        }
    }
    /** @test */
    public function check_if_birth_date_is_valid(){

        $responses[] = $this->postJson('/api/supplier', [
            'company_id' => Company::factory()->create()->id,
            'name' => 'test_name',
            'state' => 'SC',
            'CPF' => $this->CPF(),
            'RG' => $this->RG(),
            'birth_date' => '25-50-1999',
            'phone_numbers' =>  json_encode([0000000]),
        ]);

        $responses[] = $this->postJson('/api/supplier', [
            'company_id' => Company::factory()->create()->id,
            'name' => 'test_name',
            'state' => 'SC',
            'CPF' => $this->CPF(),
            'RG' => $this->RG(),
            'birth_date' => '1999-50-12',
            'phone_numbers' =>  json_encode([0000000]),
        ]);

        $responses[] = $this->postJson('/api/supplier', [
            'company_id' => Company::factory()->create()->id,
            'name' => 'test_name',
            'state' => 'SC',
            'CPF' => $this->CPF(),
            'RG' => $this->RG(),
            'birth_date' => '1999-08-50',
            'phone_numbers' =>  json_encode([0000000]),
        ]);
        $responses[] = $this->postJson('/api/supplier', [
            'company_id' => Company::factory()->create()->id,
            'name' => 'test_name',
            'state' => 'SC',
            'CPF' => $this->CPF(),
            'RG' => $this->RG(),
            'birth_date' => '1999-08-  12',
            'phone_numbers' =>  json_encode([0000000]),
        ]);
        foreach($responses as $response){
            $response->assertUnprocessable();
            $response->assertJson(fn (AssertableJson $json) =>
                $json->hasAny('failed.birth_date')
                    ->etc()
            );
        }
    }
    /** @test  */
    public function success_edit(){
        $supplier = Supplier::factory(['CPF' => $this->CPF(), 'CNPJ' => null])->create();
        $responses[] = $this->putJSON("/api/supplier/{$supplier->id}", [
            'name' => $this->faker->name(),
            'state' => "SC",
            'CPF' => $this->CPF()
        ]);
        $responses[] = $this->putJSON("/api/supplier/{$supplier->id}", [
            'state' => "SC",
            'birth_date' => $this->faker->date('Y-m-d')
        ]);

        $supplier = Supplier::factory(['CNPJ' => $this->CNPJ(), 'CPF' => null])->create();
        $responses[] = $this->putJSON("/api/supplier/{$supplier->id}", [
            'name' => $this->faker->company(),
            'state' => "SC",
            'CNPJ' => $this->CNPJ()
        ]);
        $responses[] = $this->putJSON("/api/supplier/{$supplier->id}", [
            'state' => "SC",
            'birth_date' => $this->faker->date('Y-m-d'),
            'RG' => $this->RG()
        ]);

        foreach ($responses as $response) {
            $response->assertOk();
        }
    }
    /** @test  */
    public function fail_edit(){
        $supplier = Supplier::factory()->create();
        $responses[] = $this->putJSON("/api/supplier/{$supplier->id}", [
            'name' => '',
            'state' => "SC"
        ]);
        $responses[] = $this->putJSON("/api/supplier/{$supplier->id}", [
            'state' => ""
        ]);
        $responses[] = $this->putJSON("/api/supplier/{$supplier->id}", [
            'CNPJ' => ""
        ]);
        $responses[] = $this->putJSON("/api/supplier/{$supplier->id}", [
            'state' => "asqeawe"
        ]);
        $responses[] = $this->putJSON("/api/supplier/{$supplier->id}", [
            'state' => 12345
        ]);
        foreach ($responses as $response) {
            $response->assertUnprocessable();
            $response->assertJson(fn (AssertableJson $json) =>
                $json->hasAll('failed', 'errors')
                    ->etc()
            );
        }
    }
    /** @test */
    public function success_destroy(){
        $supplier = Supplier::factory()->create();
        $response = $this->delete("/api/supplier/{$supplier->id}");
        $response->assertOk();
    }
    /** @test */
    public function fail_destroy(){
        $response = $this->delete("/api/supplier/");
        $response->assertStatus(405);

        $response = $this->delete("/api/supplier/1");
        $response->assertNotFound();
    }
    /** @test */
    public function check_age_verify_middleware(){
        $response = $this->postJson('/api/supplier', [
            'company_id' =>  Company::factory(['state' => 'PR'])->create()->id,
            'name' => 'test_name',
            'state' => 'SC',
            'CPF' => $this->CPF(),
            'RG' => $this->RG(),
            'birth_date' => Carbon::now()->subYears(15)->format('Y-m-d'),
            'phone_numbers' =>  json_encode([0000000]),
        ]);
        $response->assertUnprocessable();

        $response = $this->postJson('/api/supplier', [
            'company_id' => Company::factory(['state' => 'PR'])->create()->id,
            'name' => 'test_name',
            'state' => 'SC',
            'CPF' => $this->CPF(),
            'RG' => $this->RG(),
            'birth_date' => Carbon::now()->subYears(25)->format('Y-m-d'),
            'phone_numbers' =>  json_encode([0000000]),
        ]);
        $response->assertCreated();

        $response = $this->postJson('/api/supplier', [
            'company_id' => Company::factory(['state' => 'SC'])->create()->id,
            'name' => 'test_name',
            'state' => 'SC',
            'CPF' => $this->CPF(),
            'RG' => $this->RG(),
            'birth_date' => Carbon::now()->subYears(15)->format('Y-m-d'),
            'phone_numbers' =>  json_encode([0000000]),
        ]);
        $response->assertCreated();

    }
}
