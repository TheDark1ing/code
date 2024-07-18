<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Bitrix\Main\Web\Json;
use CSearch;

Loader::includeModule('search');

$request = Context::getCurrent()->getRequest();
$query = htmlspecialcharsbx($request->getQuery("q"));

$response = [
    'status' => 'error',
    'data' => [],
    'message' => ''
];

if (strlen($query) < 2) {
    $response['message'] = 'Query is too short';
    header('Content-Type: application/json');
    echo Json::encode($response);
    die();
}

// Настройка параметров поиска
$obSearch = new CSearch();
$obSearch->Search([
    "QUERY" => $query,
    "SITE_ID" => 's1', // Указываем идентификатор сайта, если поиск нужно ограничить текущим сайтом
]);

$results = [];
if ($obSearch->errorno == 0) {
    while ($result = $obSearch->GetNext()) {
        $results[] = [
            "TITLE" => $result["TITLE"],
            "URL" => $result["URL"]
        ];
    }
    $response['status'] = 'success';
    $response['data'] = array_slice($results, 0, 5); // Ограничиваем результаты первыми 5 элементами
} else {
    $response['message'] = 'Search error: ' . $obSearch->error;
}

// Добавим отладочный вывод
file_put_contents('debug.log', print_r($response, true), FILE_APPEND);

header('Content-Type: application/json');
echo Json::encode($response);
