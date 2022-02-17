<?php

namespace Database\Factories;


use Database\Factories\GenerateCNPJ;
use Database\Factories\SelectState;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    use GenerateCNPJ;
    use SelectState;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company(),
            'state' => $this->randState(),
            'CNPJ' =>  $this->CNPJ(),
        ];
    }
}
