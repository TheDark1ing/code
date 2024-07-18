<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Избранное");

global $USER;

if (!$USER->IsAuthorized()) {
    echo "<p>Необходимо авторизоваться для просмотра избранного.</p>";
} else {
    $userId = $USER->GetID();
    $user = CUser::GetByID($userId)->Fetch();
    $favorites = $user["UF_FAVORITES"] ?: [];

    if (empty($favorites)) {
        echo "<p>У вас нет избранных товаров.</p>";
    } else {
        $arFilter = ["ID" => $favorites, "ACTIVE" => "Y"];
        $res = CIBlockElement::GetList([], $arFilter, false, false, ["ID", "NAME", "DETAIL_PAGE_URL"]);

        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            echo "<a href='" . $arFields["DETAIL_PAGE_URL"] . "'>" . $arFields["NAME"] . "</a><br>";
        }
    }
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
