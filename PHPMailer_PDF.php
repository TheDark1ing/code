<?php

require $_SERVER['DOCUMENT_ROOT'] . '/libs/mailer/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'] . '/libs/mailer/SMTP.php';
require $_SERVER['DOCUMENT_ROOT'] . '/libs/mailer/Exception.php';

mb_internal_encoding("UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdfFile = $_FILES['pdfFile'];
    $emailUser = $_POST['email'];
    $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . '/pdf_upload';
    $nameFile = $pdfFile['name'];

    if (move_uploaded_file($pdfFile['tmp_name'], "$uploadDirectory/$nameFile")) {
        echo 'PDF успешно сохранен на сервере.';
        // Формирование самого письма
        $title = "PDF Рациона питания с сайта «Правильное Питание»";
        $body = "
            <h2><a href='http://pp.eddev.ru/'>«Правильное Питание»</a></h2>
            <p>При прохождение формы «Рацион Питания» на вашу почту был отправлен PDF файл, содержащий рекомендуемый рацион питания.</p>
            <small>Письмо создано автоматически, отвечать на него не нужно</small>
            ";

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        //$mail->isSMTP();
        $mail->CharSet = "UTF-8";
        //$mail->SMTPAuth   = true;
        //$mail->SMTPDebug = 2;
        $mail->Debugoutput = function ($str, $level) {
            $GLOBALS['data']['debug'][] = $str;
        };

        // Настройки вашей почты
        //$mail->Host       = 'smtp.yandex.ru'; // SMTP сервера вашей почты
        //$mail->Username   = 'login'; // Логин на почте
        //$mail->Password   = 'password'; // Пароль на почте
        //$mail->SMTPSecure = 'ssl';
        //$mail->Port       = 465;
        //$mail->setFrom('mail', 'Name'); // Адрес самой почты и имя отправителя

        $mail->From     = 'info@pp.eddev.ru';
        $mail->FromName = 'Правильное Питание';

        $mail->addAddress($emailUser);

        // Прикрипление файлов к письму
        if ($pdfFile['error'] === 0) {
            $mail->addAttachment("$uploadDirectory/$nameFile", $nameFile);
            echo 'Файл успешно отправлен';
        }

        // Отправка сообщения
        $mail->isHTML(true);
        $mail->Subject = $title;
        $mail->Body = $body;

        $filePath = "$uploadDirectory/$nameFile";

        if ($mail->send()) {
            $data['result'] = "success";
            $data['info'] = "Сообщение успешно отправлено!";
            if (file_exists($filePath)) {
                if (unlink($filePath)) {
                    echo 'Файл успешно удален.';
                } else {
                    echo 'Не удалось удалить файл.';
                }
            } else {
                echo 'Файл не существует.';
            }
        } else {
            $data['result'] = "error";
            $data['info'] = "Сообщение не было отправлено. Ошибка при отправке письма";
            $data['desc'] = "Причина ошибки: {$mail->ErrorInfo}";
            if (file_exists($filePath)) {
                if (unlink($filePath)) {
                    echo 'Файл успешно удален.';
                } else {
                    echo 'Не удалось удалить файл.';
                }
            } else {
                echo 'Файл не существует.';
            }
        }
    }

    // Отправка результата
    header('Content-Type: application/json');
    echo json_encode($data);
} else {
    echo 'Ошибка при сохранении PDF на сервере.';
}
