<?php

namespace Tests\Unit;

use App\Models\Supplier;
use App\Repositories\CompanyRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;
// use PHPUnit\Framework\TestCase;


class CompanyTest extends TestCase
{
    // use RefreshDatabase;
    protected $repository;
    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app('App\Repositories\CompanyRepository');
    }
    /**  @test **/
    public function getAll()
    {

        $cp = Company::factory()->create();
        // $response = $this->repository->all();
        // $this->assertTrue(true);
    }

    public function destroy()
    {
    }

    public function edit()
    {
    }
}
