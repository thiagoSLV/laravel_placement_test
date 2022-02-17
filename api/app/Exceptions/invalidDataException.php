<?php

namespace App\Exceptions;

use Exception;

class invalidDataException extends Exception
{
    protected $model;
    public function __construct($model)
    {
        $this->model = $model;
        foreach($model->getFillable() as $field)
    }

    public function render(Request $request): Response
    {
        $status = 400;
        $error = "Something is wrong";
        $help = "Contact the sales team to verify";

        return response(["error" => $error, "help" => $help], $status);
    }

}
