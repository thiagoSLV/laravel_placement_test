<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Nette\Utils\Json;

class SupplierFactory extends Factory
{
    use GenerateCNPJ;
    use GenerateCPF;
    use GenerateRG;
    use SelectState;
    protected $types = ['CPF', 'CNPJ'];
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $phone_numbers = [];
        for ($i = 0; $i < rand(1,3); $i++){
            $phone_numbers[] = $this->faker->numberBetween(00000000, 99999999);
        }

        $type = $this->types[rand(0,1)];
        return [
            'name' => $type == 'CPF' ? $this->faker->name : $this->faker->company,
            'state' =>  $this->randState(),
            $type => $type == 'CPF' ?$this->CPF(): $this->CNPJ(),
            'phone_numbers' => json_encode($phone_numbers),
            'RG' => $type == 'CPF' ? $this->RG(): NULL,
            'birth_date' => $type == 'CPF' ?  $this->faker->date('Y-m-d') : NULL,
            'company_id' => Company::factory()->create()->id
        ];
    }
}
