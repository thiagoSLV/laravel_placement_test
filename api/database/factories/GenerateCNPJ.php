<?php
namespace Database\Factories;
use Illuminate\Support\Str;

trait GenerateCNPJ {

    protected $cnpj;
    /**
     * Generates random CNPJ.
    */
    public function CNPJ($withPontuation = false)
    {
        $this->cnpj = rand(10000000000000, 99999999999999);
        Str::of($this->cnpj)
            ->when($withPontuation, function() {
                $this->addCNPJPontuation();
            });
        return $this->cnpj;
    }

    private function addCNPJPontuation()
    {
        $aux = Str::of($this->cnpj)->substr(0, 2)->append('.');
        $aux .= Str::of($this->cnpj)->substr(2, 3)->append('.');
        $aux .= Str::of($this->cnpj)->substr(5, 3)->append('/');
        $aux .= Str::of($this->cnpj)->substr(8, 4)->append('-');
        $aux .= Str::of($this->cnpj)->substr(12, 2);

        $this->cnpj = $aux;
    }

}
