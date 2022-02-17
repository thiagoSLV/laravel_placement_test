<?php
namespace Database\Factories;
use Illuminate\Support\Str;

trait GenerateRG {

    protected $rg;
    /**
     * Generates random RG.
    */
    public function RG($withPontuation = false)
    {
        $this->rg = rand(100000000, 999999999);
        Str::of($this->rg)
            ->when($withPontuation, function() {
                $this->addRGPontuation();
            });
        return $this->rg;
    }

    private function addRGPontuation()
    {
        $aux = Str::of($this->rg)->substr(0, 2)->append('.');
        $aux .= Str::of($this->rg)->substr(2, 3)->append('.');
        $aux .= Str::of($this->rg)->substr(5, 3)->append('-');
        $aux .= Str::of($this->rg)->substr(8, 1);

        $this->rg = $aux;
    }

}
