<?php

namespace Tests;

use Local\Notes\NoteRules;
use Local\Notes\ValidationException;
use PHPUnit\Framework\TestCase;

class NoteRulesTest extends TestCase
{
    public function testOk()
    {
        $data = NoteRules::check([
            'title' => '  Привет  ',
            'content' => 'Текст',
        ]);

        $this->assertSame('Привет', $data['title']);
        $this->assertSame('Текст', $data['content']);
    }

    public function testEmpty()
    {
        $this->expectException(ValidationException::class);
        NoteRules::check(['title' => '', 'content' => '']);
    }

    public function testId()
    {
        $this->assertSame(3, NoteRules::checkId('3'));

        $this->expectException(ValidationException::class);
        NoteRules::checkId(0);
    }
}
