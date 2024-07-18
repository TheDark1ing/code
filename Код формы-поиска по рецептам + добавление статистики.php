<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php'); ?>
<?php
CModule::IncludeModule('iblock');

// Функция для обновления статистики
function updateStat($stats, $category, $data)
{
    if (!isset($stats[$category])) {
        $stats[$category] = []; // Создаем пустой массив, если категория еще не существует
    }

    foreach ($data as $item) {
        $item = trim($item); // Удаляем лишние пробелы вокруг элемента
        if (!empty($item)) { // Проверяем, что элемент не пустой
            if (!isset($stats[$category][$item])) {
                $stats[$category][$item] = 1; // Инициализируем элемент счетчиком 1, если он не существует
            } else {
                $stats[$category][$item]++; // Увеличиваем счетчик, если элемент уже существует
            }
        }
    }

    return $stats;
}

// Функция для преобразования первой буквы в слове в верхний регистр для UTF-8
function mb_ucfirst($str, $encoding = 'UTF-8')
{
    $str = mb_ereg_replace('^[\ ]+', '', $str); // Удаляем начальные пробелы
    $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding) .
        mb_substr($str, 1, mb_strlen($str), $encoding); // Преобразуем первую букву в верхний регистр
    return $str;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
?>
    <!-- Стили для страницы -->
    <style>
        body div.section:nth-child(3) {
            padding-top: 150px;
        }

        body div.section {
            margin-bottom: 30px;
            border-bottom: 1px solid black;
            padding-bottom: 30px;
            padding-left: 30px;
        }
    </style>
    <?php
    // Получаем данные из формы
    $favoriteProducts = $_POST['favorite_products'];
    $hatedProducts = $_POST['hated_products'];
    $favoriteDishTypes = $_POST['favorite_dish_types'];
    $hatedDishTypes = $_POST['hated_dish_types'];
    $kklPerDay = $_POST['kkl_per_day'];
    $diagnosis = $_POST['diagnosis'];
    $allergy = $_POST['allergy'];
    $speedCreateDishes = $_POST['speed_create_dishes'];

    // Функция для обработки и преобразования элементов массива
    function processArray($input)
    {
        $output = explode(',', $input); // Разделяем строку по запятым
        $output = array_map('trim', $output); // Удаляем лишние пробелы вокруг элементов
        $output = array_map('mb_ucfirst', $output); // Преобразуем первую букву каждого элемента в верхний регистр
        return $output;
    }

    // Обработка каждого массива переменных
    $favoriteProductsArray = processArray($favoriteProducts);
    $hatedProductsArray = processArray($hatedProducts);
    $favoriteDishTypesArray = processArray($favoriteDishTypes);
    $hatedDishTypesArray = processArray($hatedDishTypes);
    $diagnosisArray = processArray($diagnosis);
    $allergyArray = processArray($allergy);

    // Преобразование $speedCreateDishes и $kklPerDay (если нужно)
    $speedCreateDishes = mb_ucfirst(trim($speedCreateDishes)); // Преобразуем первую букву в верхний регистр и удаляем лишние пробелы
    $kklPerDay = trim($kklPerDay); // Удаляем лишние пробелы

    // Инициализируем или загружаем статистику из файла
    $statsFile = 'statistic.json';

    if (file_exists($statsFile)) {
        $jsonString = file_get_contents($statsFile); // Получаем содержимое файла
        $stats = json_decode($jsonString, true); // Декодируем JSON в ассоциативный массив

        if ($stats === null) {
            // Если JSON декодирование вернуло null, это может быть пустой файл
            // В этом случае создаем пустой массив
            $stats = [];
        }
    } else {
        // Если файл не существует, создаем пустой массив
        $stats = [];
    }

    // Обновляем статистику
    $stats = updateStat($stats, 'Любимые продукты', $favoriteProductsArray);
    $stats = updateStat($stats, 'Нелюбимые продукты', $hatedProductsArray);
    $stats = updateStat($stats, 'Любимые блюда', $favoriteDishTypesArray);
    $stats = updateStat($stats, 'Нелюбимые блюда', $hatedDishTypesArray);
    $stats = updateStat($stats, 'Заболевания, диагнозы', $diagnosisArray);
    $stats = updateStat($stats, 'Аллергии и непереносимости', $allergyArray);
    $stats = updateStat($stats, 'Скорость приготовления блюд', [$speedCreateDishes]);
    $stats = updateStat($stats, 'Калорийность в день', [$kklPerDay]);

    // Сохраняем обновленную статистику в файл
    $prettyJson = json_encode($stats, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    file_put_contents($statsFile, $prettyJson);

    $iblockId = 29;

    // Условия выборки элементов (по умолчанию выберутся все элементы)
    $filter = array(
        'IBLOCK_ID' => $iblockId, // ID инфоблока
    );

    // Список полей элементов, которые нужно выбрать
    $selectFields = array(
        'ID',
        'NAME',
        'PREVIEW_PICTURE',
    );

    // Выполняем выборку элементов
    $elementList = CIBlockElement::GetList(
        array(),
        $filter,
        false,
        false,
        $selectFields
    );

    $elementsTrue = array();
    // Проходим по полученным элементам
    while ($element = $elementList->Fetch()) {
        $elementProperties = array();

        // Обращение к свойствам элемента
        $properties = CIBlockElement::GetProperty($iblockId, $element['ID']);
        while ($property = $properties->Fetch()) {
            // Проверяем, существует ли уже массив для данного свойства
            if (!isset($elementProperties[$property['CODE']])) {
                $elementProperties[$property['CODE']] = array();
            }

            // Добавляем значение свойства в массив
            $elementProperties[$property['CODE']][] = $property['VALUE'];
        }

        $foundMatchFavoriteProducts = false;
        $foundMatchHatedProducts = false;
        $foundMatchFavoriteDishTypes = false;
        $foundMatchHatedDishTypes = false;
        $foundMatchDiagnosis = false;
        $foundMatchAllergy = false;
        $foundMatchFast = false;
        $foundMatchNormal = false;
        $foundMatchLong = false;
        $foundMatchKkal = false;

        // Проверяем соответствие элемента заданным условиям
        foreach ($favoriteProductsArray as $favoriteProduct) {
            foreach ($elementProperties['P_ING'] as $value) {
                if ($value == $favoriteProduct) {
                    $foundMatchFavoriteProducts = true;
                    break;
                }
            }
            if ($foundMatchFavoriteProducts) {
                break;
            }
        }
        // Аналогично проверяем другие условия...
        foreach ($hatedProductsArray as $hatedProduct) {
            foreach ($elementProperties['P_ING'] as $value) {
                if ($value == $hatedProduct) {
                    $foundMatchHatedProducts = true;
                    break;
                }
            }
            if ($foundMatchHatedProducts) {
                break;
            }
        }
        foreach ($favoriteDishTypesArray as $favoriteDishType) {
            if ($elementProperties['P_TYP'][0] == $favoriteDishType) {
                $foundMatchFavoriteDishTypes = true;
                break;
            }
        }
        foreach ($hatedDishTypesArray as $hatedDishType) {
            if ($elementProperties['P_TYP'][0] == $hatedDishType) {
                $foundMatchHatedDishTypes = true;
                break;
            }
        }
        foreach ($diagnosisArray as $diagnosis) {
            foreach ($elementProperties['P_ZAB'] as $value) {
                if ($value == $diagnosis) {
                    $foundMatchDiagnosis = true;
                    break;
                }
            }
            if ($foundMatchDiagnosis) {
                break;
            }
        }
        foreach ($allergyArray as $allergy) {
            foreach ($elementProperties['P_ALL'] as $value) {
                if ($value == $allergy) {
                    $foundMatchAllergy = true;
                    break;
                }
            }
            if ($foundMatchAllergy) {
                break;
            }
        }
        if (isset($elementProperties['P_TIM'])) {
            if ($elementProperties['P_TIM'][0] <= 1 && $speedCreateDishes == 'Быстро') {
                $foundMatchSpeed = true;
            }
            if ($elementProperties['P_TIM'][0] > 1 && $elementProperties['P_TIM']['0'] < 2 && $speedCreateDishes == 'Средне') {
                $foundMatchSpeed = true;
            }
            if ($elementProperties['P_TIM'][0] >= 2 && $speedCreateDishes == 'Долго') {
                $foundMatchSpeed = true;
            }
        }
        if (isset($elementProperties['P_CAT'][0])) {
            if ($elementProperties['P_CAT'][0] == 'Завтрак') {
                $timeDay = 'breakfast';
                if ($elementProperties['P_CAL'][0] <= ($kklPerDay * 0.25)) {
                    $foundMatchKkal = true;
                }
            }
            if ($elementProperties['P_CAT'][0] == 'Обед') {
                $timeDay = 'dinner';
                if ($elementProperties['P_CAL'][0] <= ($kklPerDay * 0.35)) {
                    $foundMatchKkal = true;
                }
            }
            if ($elementProperties['P_CAT'][0] == 'Ужин') {
                $timeDay = 'supper';
                if ($elementProperties['P_CAL'][0] <= ($kklPerDay * 0.15)) {
                    $foundMatchKkal = true;
                }
            }
            if ($elementProperties['P_CAT'][0] == 'Перекус 1') {
                $timeDay = 'snack_1';
                if ($elementProperties['P_CAL'][0] <= ($kklPerDay * 0.15)) {
                    $foundMatchKkal = true;
                }
            }
            if ($elementProperties['P_CAT'][0] == 'Перекус 2') {
                $timeDay = 'snack_2';
                if ($elementProperties['P_CAL'][0] <= ($kklPerDay * 0.1)) {
                    $foundMatchKkal = true;
                }
            }
        }

        if (
            $foundMatchFavoriteProducts === true &&
            $foundMatchHatedProducts === false &&
            $foundMatchFavoriteDishTypes === true &&
            $foundMatchHatedDishTypes === false &&
            $foundMatchDiagnosis === false &&
            $foundMatchAllergy === false &&
            $foundMatchSpeed === true &&
            $foundMatchKkal === true
        ) { ?>
            <div class="element" data-time="<?= $timeDay ?>">
                <p><?= $element['NAME'] ?></p>
                <p><?= $element['ID'] ?></p>
            </div>
    <? }
    }
    ?>
    <!-- HTML разметка для отображения результатов -->
    <div class="section" data-time="breakfast">
        <h2>Завтрак</h2>
        <div class="elems-block"></div>
    </div>
    <div class="section" data-time="snack_1">
        <h2>Перекус 1</h2>
        <div class="elems-block"></div>
    </div>
    <div class="section" data-time="dinner">
        <h2>Обед</h2>
        <div class="elems-block"></div>
    </div>
    <div class="section" data-time="snack_2">
        <h2>Перекус 2</h2>
        <div class="elems-block"></div>
    </div>
    <div class="section" data-time="supper">
        <h2>Ужин</h2>
        <div class="elems-block"></div>
    </div>

    <script>
        $(document).ready(function() {
            // Находим все элементы с классом 'element'
            $('.element').each(function() {
                var elementTime = $(this).data('time'); // Получаем значение атрибута 'data-time'
                var $targetSection = $('.section[data-time="' + elementTime + '"] .elems-block');

                // Проверяем, существует ли соответствующая секция
                if ($targetSection.length > 0) {
                    $(this).appendTo($targetSection);
                }
            });
        });
    </script>
<?
} else {
?>
    <style>
        h2 {
            padding-top: 150px;
            padding-left: 30px;
        }

        p {
            padding-left: 30px;
        }

        .input-row {
            display: flex;
            gap: 30px;
            margin-bottom: 15px;
        }

        .input-row p {
            padding-left: 0px;
            min-width: 300px;
        }

        form {
            margin-top: 50px;
            border-top: 1px solid black;
            padding: 0 30px;
            padding-top: 50px;
        }

        .input-row input {
            border: 1px solid black;
            border-radius: 5px;
            padding: 0 10px;
        }

        button {
            background: orange;
            border-radius: 5px;
            padding: 10px 15px;
        }
    </style>
    <h2>Рацион питания</h2>
    <p>Рацион будет подобран исходя из следующих условий: учтены Ваши предпочтения, диагнозы и прочее.</p>
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
        <div class="input-row">
            <p>Рекомендованная калорийность: *</p>
            <input type="text" name="kkl_per_day" placeholder="ККал/сутки">
        </div>
        <div class="input-row">
            <p>Любимые продукты: *</p>
            <input type="text" name="favorite_products" placeholder="Баклажан, рис, морковь">
        </div>
        <div class="input-row">
            <p>Нелюбимые продукты: *</p>
            <input type="text" name="hated_products" placeholder="Лук, болгарский перец">
        </div>
        <div class="input-row">
            <p>Любимые типы блюд: *</p>
            <input type="text" name="favorite_dish_types" placeholder="Супчек">
        </div>
        <div class="input-row">
            <p>Нелюбимые типы блюд: *</p>
            <input type="text" name="hated_dish_types" placeholder="С курицей">
        </div>
        <div class="input-row">
            <p>Диагнозы: **</p>
            <input type="text" name="diagnosis" placeholder="ИБС">
        </div>
        <div class="input-row">
            <p>Аллергии и непереносимости: **</p>
            <input type="text" name="allergy" placeholder="Молоко">
        </div>
        <div class="input-row">
            <p>Скорость приготовления блюд: *</p>
            <input type="text" name="speed_create_dishes" placeholder="Долго/Средне/Быстро">
        </div>
        <button type="submit">Подобрать рацион</button>
    </form>
<? } ?>
<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>