<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
	echo "Invalid ID";
	die();
}

$id = (int)$_POST['id'];
CModule::IncludeModule("iblock");

$res = CIBlockElement::GetByID($id);
if ($arItem = $res->GetNext()) {

    // Получаем свойства элемента
    $properties = CIBlockElement::GetProperty($arItem['IBLOCK_ID'], $arItem['ID']);
    while ($prop = $properties->GetNext()) {
        $arItem['PROPERTIES'][$prop['CODE']] = $prop;
    }

    $previewText = $arItem['PREVIEW_TEXT'];
    $previewPicture = CFile::GetPath($arItem['PREVIEW_PICTURE']);
    $name = $arItem['NAME'];
    $detailText = $arItem['DETAIL_TEXT'];
    $phone = isset($arItem['PROPERTIES']['PHONE']['VALUE']) ? $arItem['PROPERTIES']['PHONE']['VALUE'] : '';
	$phone_link = isset($arItem['PROPERTIES']['PHONE_LINK']['VALUE']) ? $arItem['PROPERTIES']['PHONE_LINK']['VALUE'] : '#';
    $site = isset($arItem['PROPERTIES']['SITE']['VALUE']) ? $arItem['PROPERTIES']['SITE']['VALUE'] : '';
	$site_link = isset($arItem['PROPERTIES']['SITE_LINK']['VALUE']) ? $arItem['PROPERTIES']['SITE_LINK']['VALUE'] : '#';

	echo "<div class='img_shop'>";
	echo "<img src='{$previewPicture}' alt='{$name}'>";
	echo "</div>";
	echo "<div class='row_shop'>";
    echo "<p>{$name}</p>";
    echo "<p>{$previewText}</p>";
	echo "</div>";
    echo "<p class='detail_shop'>{$detailText}</p>";
	echo "<div class='row_shop_bottom'>";
	echo "<p><a href='tel:{$phone_link}'>{$phone}</a></p>";
    echo "<p><a href='{$site_link}' target='_blank'>{$site}</a></p>";
	echo "</div>";

}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
