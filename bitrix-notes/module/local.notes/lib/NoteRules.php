<?php

namespace Local\Notes;

class NoteRules
{
    public static function check(array $data)
    {
        $errors = [];
        $title = isset($data['title']) ? trim((string) $data['title']) : '';
        $content = isset($data['content']) ? trim((string) $data['content']) : '';

        if ($title === '') {
            $errors['title'] = 'Укажите заголовок';
        } elseif (mb_strlen($title) > 255) {
            $errors['title'] = 'Заголовок слишком длинный';
        }

        if ($content === '') {
            $errors['content'] = 'Укажите текст';
        } elseif (mb_strlen($content) > 10000) {
            $errors['content'] = 'Текст слишком длинный';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        return [
            'title' => $title,
            'content' => $content,
        ];
    }

    public static function checkId($id)
    {
        if (!is_numeric($id) || (int) $id <= 0) {
            throw new ValidationException(['id' => 'Некорректный id']);
        }

        return (int) $id;
    }
}
