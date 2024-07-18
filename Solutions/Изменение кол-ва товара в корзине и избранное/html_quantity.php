<!-- кнопки для количества -->
<div id="quantity-controls-<?= $arElement["ID"] ?>" class="quantity-controls" style="display: none;">
    <button class="decrease-quantity" data-id="<?= $arElement["ID"] ?>">-</button>
    <input type="text" class="quantity-input" data-id="<?= $arElement["ID"] ?>" value="1">
    <button class="increase-quantity" data-id="<?= $arElement["ID"] ?>">+</button>
</div>

<!-- кнопка в корзину -->
<input type="button" class="add-to-cart" data-id="<? echo $arElement["ID"] ?>" value="<? echo GetMessage("CATALOG_ADD") ?>">