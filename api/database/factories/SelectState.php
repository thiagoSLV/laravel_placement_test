<?php
namespace Database\Factories;


trait SelectState {
    protected $brazilianStates = [
        'RO',
        'AC',
        'AM',
        'RR',
        'PA',
        'AP',
        'TO',
        'MA',
        'PI',
        'CE',
        'RN',
        'PB',
        'PE',
        'AL',
        'SE',
        'BA',
        'MG',
        'ES',
        'RJ',
        'SP',
        'PR',
        'SC',
        'RS',
        'MS',
        'MT',
        'GO',
        'DF'
    ];

    public function randState()
    {
        $index = rand(0, sizeof($this->brazilianStates)-1);
        return $this->brazilianStates[$index];

    }
}
