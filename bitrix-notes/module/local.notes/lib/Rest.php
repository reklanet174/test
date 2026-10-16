<?php

namespace Local\Notes;

use Throwable;

class Rest
{
    private $notes;

    public function __construct(?Notes $notes = null)
    {
        $this->notes = $notes ?: new Notes();
    }

    public function run()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Accept');

        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'OPTIONS') {
            $this->send(null, 204);
            return;
        }

        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/') ?: '/';

        try {
            if ($method === 'GET' && $path === '/api/health') {
                $this->ok(['status' => 'ok']);
                return;
            }

            if ($method === 'GET' && $path === '/api/notes') {
                $this->ok($this->notes->all());
                return;
            }

            if ($method === 'POST' && $path === '/api/notes') {
                $this->ok($this->notes->add($this->body()), 201);
                return;
            }

            if (preg_match('#^/api/notes/(\d+)$#', $path, $m)) {
                $id = (int) $m[1];

                if ($method === 'GET') {
                    $this->ok($this->notes->get($id));
                    return;
                }

                if ($method === 'PUT') {
                    $this->ok($this->notes->update($id, $this->body()));
                    return;
                }

                if ($method === 'DELETE') {
                    $this->notes->delete($id);
                    $this->send(['message' => 'Заметка удалена']);
                    return;
                }
            }

            $this->send(['message' => 'Не найдено'], 404);
        } catch (ValidationException $e) {
            $this->send([
                'message' => $e->getMessage(),
                'errors' => $e->getErrors(),
            ], 422);
        } catch (NoteNotFoundException $e) {
            $this->send(['message' => $e->getMessage()], 404);
        } catch (Throwable $e) {
            $this->send([
                'message' => 'Ошибка сервера',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function body()
    {
        $raw = file_get_contents('php://input') ?: '';
        if ($raw === '') {
            return [];
        }

        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    private function ok($data, $status = 200)
    {
        $this->send(['data' => $data], $status);
    }

    private function send($payload, $status = 200)
    {
        http_response_code($status);

        if (class_exists('\\Bitrix\\Main\\Context')) {
            $response = \Bitrix\Main\Context::getCurrent()->getResponse();
            if ($response) {
                $response->setStatus($status);
            }
        }

        if (class_exists('\\CHTTP')) {
            \CHTTP::SetStatus((string) $status);
        }

        if ($status === 204) {
            die;
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        die;
    }
}
