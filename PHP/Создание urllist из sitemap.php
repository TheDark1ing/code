<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("test");
?>
<?
$lines = file('sitemap-files.xml');
$allMatches = array();

foreach ($lines as $line_number => $line) {
	$line = trim($line);
	preg_match_all('/<loc[^>]*>(.*?)<\/loc>/i', $line, $matches, PREG_SET_ORDER);
	if ($matches) {
		foreach ($matches as $match) {
			if ($match[1] != '') {
				$allMatches[] = $match[1];
			}
		}
	}
}

$list = implode("\n", $allMatches);
file_put_contents('urllist.txt', $list, FILE_APPEND); // Добавляем, а не перезаписываем

// Вывод списка для обратной связи
echo $list; 
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
