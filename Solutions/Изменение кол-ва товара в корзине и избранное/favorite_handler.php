<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

header('Content-Type: application/json');

global $USER;

if (!$USER->IsAuthorized()) {
    echo json_encode(["success" => false, "message" => "Необходимо авторизоваться"]);
    die();
}

$userId = $USER->GetID();

if (!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog")) {
    echo json_encode(["success" => false, "message" => "Не удалось подключить модули"]);
    die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && isset($_POST["product_id"])) {
    $productId = intval($_POST["product_id"]);

    // Получение текущего списка избранного
    $user = CUser::GetByID($userId)->Fetch();
    $favorites = $user["UF_FAVORITES"] ?: [];

    if ($_POST["action"] == "add") {
        if (!in_array($productId, $favorites)) {
            $favorites[] = $productId;
            $user = new CUser;
            $user->Update($userId, ["UF_FAVORITES" => $favorites]);
            echo json_encode(["success" => true, "action" => "added"]);
        } else {
            echo json_encode(["success" => false, "message" => "Товар уже в избранном"]);
        }
    } elseif ($_POST["action"] == "remove") {
        if (($key = array_search($productId, $favorites)) !== false) {
            unset($favorites[$key]);
            $user = new CUser;
            $user->Update($userId, ["UF_FAVORITES" => $favorites]);
            echo json_encode(["success" => true, "action" => "removed"]);
        } else {
            echo json_encode(["success" => false, "message" => "Товар не найден в избранном"]);
        }
    }
} else {
    echo json_encode(["success" => false, "message" => "Некорректный запрос"]);
}
