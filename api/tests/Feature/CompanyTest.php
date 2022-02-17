<?php

namespace Tests\Feature;

use App\Models\Company;
use Database\Factories\GenerateCNPJ;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
class CompanyTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use GenerateCNPJ;

    /** @test */
    public function getAll()
    {
        Company::factory()->count(5)->create();
        $response = $this->getJson('/api/company');
        $response->assertOk();
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has(5)->each(fn ($json) =>
                $json->hasAll('id', 'name', 'state')
                ->etc()
            )
        );
    }
    /** @test */
    public function success_getByKey(){
        $company = Company::factory()->create();
        $response = $this->getJson("/api/company/{$company->id}");
        $response->assertOk();
        $response->assertJson(fn (AssertableJson $json) =>
            $json->where('id', $company->id)
                ->hasAll('id', 'name', 'state')
                ->etc()
        );
        $response = $this->getJson('/api/company/2');
        $response->assertOk();
        $response->assertExactJson([]);
    }
    /** @test */
    public function success_store(){
        $response = $this->postJSON("/api/company", [
            'CNPJ' => $this->CNPJ(),
            'name' => $this->faker->company(),
            'state' => 'PR'
        ]);
        $response->assertCreated();
        $response->assertJson(fn (AssertableJson $json) =>
            $json->hasAll('id', 'name', 'state')
                ->etc()
        );
    }
    /** @test */
    public function fail_store(){
        $responses[] = $this->postJSON("/api/company", [
            'name' => $this->faker->company(),
            'state' => 'PR'
        ]);
        $responses[] = $this->postJSON("/api/company", [
            'CNPJ' => $this->CNPJ(),
            'state' => 'PR'
        ]);
        $responses[] = $this->postJSON("/api/company", [
            'CNPJ' => $this->CNPJ(),
            'name' => $this->faker->company(),
        ]);
        $responses[] = $this->postJSON("/api/company", [
            'CNPJ' => $this->faker->name(),
            'name' => $this->faker->company(),
            'state' => 'PR'
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
    public function check_if_cnpj_is_unique(){
        $company = Company::factory()->create();
        $response = $this->postJSON("/api/company", [
            'CNPJ' => $company->CNPJ,
            'name' => $this->faker->company(),
            'state' => 'PR'
        ]);
        $response->assertUnprocessable();

        $response->assertJson(fn (AssertableJson $json) =>
            $json->hasAll('failed.CNPJ.Unique')
                    ->etc()
            );

    }
    /** @test */
    public function check_if_cnpj_is_valid(){
        $responses[] = $this->postJSON("/api/company", [
            'CNPJ' => 735220350001651,
            'name' => $this->faker->company(),
            'state' => 'PR'
        ]);
        $responses[] = $this->postJSON("/api/company", [
            'CNPJ' => 73522035000,
            'name' => $this->faker->company(),
            'state' => 'PR'
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
    public function check_if_state_is_valid(){
        $responses[] = $this->postJSON("/api/company", [
            'CNPJ' => $this->CNPJ(),
            'name' => $this->faker->company(),
            'state' => rand(0, 999)
        ]);
        $responses[] = $this->postJSON("/api/company", [
            'CNPJ' => $this->CNPJ(),
            'name' => $this->faker->company(),
            'state' => 'test'
        ]);

        $responses[] = $this->postJSON("/api/company", [
            'CNPJ' => $this->CNPJ(),
            'name' => $this->faker->company(),
            'state' => 'a'
        ]);

        foreach($responses as $response){
            $response->assertUnprocessable();
            $response->assertJson(fn (AssertableJson $json) =>
                $json->hasAny('failed.state.Size', 'failed.state.alpha')
                    ->etc()
            );
        }
    }
    /** @test  */
    public function success_edit(){
        $company = Company::factory()->create();
        $responses[] = $this->putJSON("/api/company/{$company->id}", [
            'name' => $this->faker->company(),
            'state' => "SC"
        ]);
        $responses[] = $this->putJSON("/api/company/{$company->id}", [
            'state' => "SC"
        ]);
        foreach ($responses as $response) {
            $response->assertOk();
        }
    }
    /** @test  */
    public function fail_edit(){
        $company = Company::factory()->create();
        $responses[] = $this->putJSON("/api/company/{$company->id}", [
            'name' => '',
            'state' => "SC"
        ]);
        $responses[] = $this->putJSON("/api/company/{$company->id}", [
            'state' => ""
        ]);
        $responses[] = $this->putJSON("/api/company/{$company->id}", [
            'CNPJ' => ""
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
        $company = Company::factory()->create();
        $response = $this->delete("/api/company/{$company->id}");
        $response->assertOk();
    }
    /** @test */
    public function fail_destroy(){
        $response = $this->delete("/api/company/");
        $response->assertStatus(405);

        $response = $this->delete("/api/company/1");
        $response->assertNotFound();
    }

}
