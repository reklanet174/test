<?php

namespace Local\Notes;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\Type\DateTime;

class NoteTable extends DataManager
{
    public static function getTableName()
    {
        return 'b_local_notes';
    }

    public static function getMap()
    {
        return [
            new IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new StringField('TITLE', [
                'required' => true,
                'size' => 255,
            ]),
            new TextField('CONTENT', [
                'required' => true,
            ]),
            new DatetimeField('CREATED_AT', [
                'required' => true,
                'default_value' => function () {
                    return new DateTime();
                },
            ]),
            new DatetimeField('UPDATED_AT', [
                'required' => true,
                'default_value' => function () {
                    return new DateTime();
                },
            ]),
        ];
    }
}
