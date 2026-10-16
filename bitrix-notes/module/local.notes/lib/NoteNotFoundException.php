<?php

namespace Local\Notes;

use Exception;

class NoteNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Заметка не найдена', 404);
    }
}
