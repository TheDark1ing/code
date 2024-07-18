<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

header('Content-Type: application/json');

if (!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog")) {
    echo json_encode(["success" => false, "message" => "Не удалось подключить модули"]);
    die();
}

$response = ["success" => false, "message" => "Неизвестная ошибка"]; // Подготовка ответа по умолчанию

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["product_id"]) && isset($_POST["quantity"]) && isset($_POST["user_id"])) {
    $productId = intval($_POST["product_id"]);
    $quantity = intval($_POST["quantity"]);
    $fUserId = intval($_POST["user_id"]);

    if ($productId > 0 && $quantity > 0) {
        \Bitrix\Main\Loader::includeModule('sale');

        $siteId = 's1';
        $productByBasketItem = null;
        $bProductInBasket = false;

        $basket = \Bitrix\Sale\Basket::loadItemsForFUser($fUserId, $siteId);
        $basketItems = $basket->getBasketItems();

        if ($basketItems) {
            foreach ($basketItems as $basketItem) {
                if ($basketItem->getField('PRODUCT_ID') == $productId) {
                    $productByBasketItem = $basketItem;
                    $bProductInBasket = true;
                    break;
                }
            }

            if ($bProductInBasket) {
                $productByBasketItem->setField('QUANTITY', $quantity);

                // Сохранить изменения в корзине
                $result = $basket->save();
                if ($result->isSuccess()) {
                    $response = ["success" => true];
                } else {
                    $errorMessages = $result->getErrorMessages();
                    $response = ["success" => false, "message" => implode(", ", $errorMessages)];
                }
            } else {
                $response = ["success" => false, "message" => "Товар не найден в корзине"];
            }
        } else {
            $response = ["success" => false, "message" => "Корзина пуста"];
        }
    } else {
        $response = ["success" => false, "message" => "Некорректные данные"];
    }
} else {
    $response = ["success" => false, "message" => "Некорректный запрос"];
}

echo json_encode($response);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
