<?php

namespace Local\Notes;

use Exception;

class ValidationException extends Exception
{
    private $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct('Ошибка валидации');
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
