<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Nette\Utils\Json;

class SupplierFactory extends Factory
{
    protected $UFs = ['RO','AC','AM','RR','PA','AP','TO','MA','PI','CE','RN','PB','PE','AL','SE','BA','MG','ES','RJ','SP','PR','SC','RS','MS','MT','GO','DF'];
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
            'state' =>  $this->UFs[rand(0, sizeof($this->UFs)-1)],
            $type => $type == 'CPF' ?
                $this->faker->numberBetween(10000000000000, 99999999999999):
                $this->faker->numberBetween(10000000000, 99999999999),
            'phone_numbers' => json_encode($phone_numbers),
            'RG' => $type == 'CPF' ? $this->faker->numberBetween(100000000, 999999999): NULL,
            'birth_date' => $type == 'CPF' ?  $this->faker->date('Y-m-d') : NULL
        ];
    }
}
