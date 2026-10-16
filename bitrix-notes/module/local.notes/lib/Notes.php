<?php

namespace Local\Notes;

use Bitrix\Main\Type\DateTime;
use RuntimeException;

class Notes
{
    public function all()
    {
        $rows = NoteTable::getList([
            'order' => ['ID' => 'DESC'],
        ])->fetchAll();

        return array_map([$this, 'map'], $rows);
    }

    public function get($id)
    {
        $id = NoteRules::checkId($id);
        $row = NoteTable::getById($id)->fetch();
        if (!$row) {
            throw new NoteNotFoundException();
        }

        return $this->map($row);
    }

    public function add(array $data)
    {
        $data = NoteRules::check($data);
        $now = new DateTime();

        $result = NoteTable::add([
            'TITLE' => $data['title'],
            'CONTENT' => $data['content'],
            'CREATED_AT' => $now,
            'UPDATED_AT' => $now,
        ]);

        if (!$result->isSuccess()) {
            throw new RuntimeException(implode('; ', $result->getErrorMessages()));
        }

        return $this->get((int) $result->getId());
    }

    public function update($id, array $data)
    {
        $id = NoteRules::checkId($id);
        $data = NoteRules::check($data);
        $this->requireExists($id);

        $result = NoteTable::update($id, [
            'TITLE' => $data['title'],
            'CONTENT' => $data['content'],
            'UPDATED_AT' => new DateTime(),
        ]);

        if (!$result->isSuccess()) {
            throw new RuntimeException(implode('; ', $result->getErrorMessages()));
        }

        return $this->get($id);
    }

    public function delete($id)
    {
        $id = NoteRules::checkId($id);
        $this->requireExists($id);

        $result = NoteTable::delete($id);
        if (!$result->isSuccess()) {
            throw new RuntimeException(implode('; ', $result->getErrorMessages()));
        }
    }

    private function requireExists($id)
    {
        if (!NoteTable::getById($id)->fetch()) {
            throw new NoteNotFoundException();
        }
    }

    private function map(array $row)
    {
        $created = $row['CREATED_AT'];
        $updated = $row['UPDATED_AT'];

        return [
            'id' => (int) $row['ID'],
            'title' => (string) $row['TITLE'],
            'content' => (string) $row['CONTENT'],
            'created_at' => $created instanceof DateTime ? $created->format('Y-m-d H:i:s') : (string) $created,
            'updated_at' => $updated instanceof DateTime ? $updated->format('Y-m-d H:i:s') : (string) $updated,
        ];
    }
}
