<?

use Bitrix\Main\Loader; // Подключаем модули
Loader::includeModule('subscribe'); // Подключаем модуль Подписка, рассылки
if (!empty($_POST['email'])) {

    global $USER;
    $email = $_POST['email'];

    $subscribeFields = [
        'USER_ID' => ($USER->IsAuthorized() ? $USER->GetID() : false),
        'FORMAT' => 'html',
        'EMAIL' => $email,
        'ACTIVE' => 'Y',
        'CONFIRMED' => 'Y', // Подтверждаем подписку без подтверждения по почте
        'SEND_CONFIRM' => 'N', // Не отправялем письмо с подтверждение подписчику
        'RUB_ID' => [1] // Указываем ID инфоблока, например у моих новостей ID == 1
    ];

    $subscr = new CSubscription;
    $ID = $subscr->Add($subscribeFields);
?>
    <?
    if ($ID > 0) {
        CSubscription::Authorize($ID);
    ?>
        <div class="notification notification_active">
            <button class="btn notification__close"></button>
            <h4>Подписка на рассылку</h4>
            <p>Вы успешно подписались на рассылку с адресом <?= $email; ?></p>
        </div>
    <? } else { ?>
        <div class="notification notification_active">
            <button class="btn notification__close"></button>
            <h4>Подписка на рассылку</h4>
            <p>Адрес <?= $email; ?> уже подписан на рассылку</p>
        </div>
    <? } ?>
<? } ?>