<?php

use Bitrix\Main\Loader;

Loader::registerAutoLoadClasses('local.notes', [
    'Local\\Notes\\NoteTable' => 'lib/NoteTable.php',
    'Local\\Notes\\Notes' => 'lib/Notes.php',
    'Local\\Notes\\Rest' => 'lib/Rest.php',
    'Local\\Notes\\NoteRules' => 'lib/NoteRules.php',
    'Local\\Notes\\ValidationException' => 'lib/ValidationException.php',
    'Local\\Notes\\NoteNotFoundException' => 'lib/NoteNotFoundException.php',
]);
