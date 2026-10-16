<?php

define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

use Bitrix\Main\Loader;
use Local\Notes\Rest;

if (!Loader::includeModule('local.notes')) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode(['message' => 'Модуль local.notes не установлен'], JSON_UNESCAPED_UNICODE);
    die;
}

(new Rest())->run();
