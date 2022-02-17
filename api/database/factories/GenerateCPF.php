<?php
namespace Database\Factories;
use Illuminate\Support\Str;

trait GenerateCPF {

    protected $cpf;
    /**
     * Generates random CPF.
    */
    public function CPF($withPontuation = false)
    {
        $this->cpf = rand(10000000000, 99999999999);
        Str::of($this->cpf)
            ->when($withPontuation, function() {
                $this->addCPFPontuation();
            });
        return $this->cpf;
    }

    private function addCPFPontuation()
    {
        $aux = Str::of($this->cpf)->substr(0, 3)->append('.');
        $aux .= Str::of($this->cpf)->substr(3, 3)->append('.');
        $aux .= Str::of($this->cpf)->substr(6, 3)->append('-');
        $aux .= Str::of($this->cpf)->substr(9, 2);

        $this->cpf = $aux;
    }

}
