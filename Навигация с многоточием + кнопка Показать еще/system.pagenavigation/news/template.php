<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);


?>
<?

$arResult["nPageWindow"] = 7;
if (!$arResult["NavShowAlways"]) {
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"] . "&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?" . $arResult["NavQueryString"] : "");
?>

<div id="loading-spinner" style="display: none;"><img src="<?= SITE_TEMPLATE_PATH ?>/images/spinner.gif" alt="spinner"></div>

<? if ($arResult["NavPageNomer"] + 1 <= $arResult["NavPageCount"]) : ?>
	<?
	$plus = $arResult["NavPageNomer"] + 1;
	$url = $arResult["sUrlPathParams"] . "PAGEN_" . $arResult["NavNum"] . "=" . $plus;
	?>
	<div class="referenceSingleAll aos-init" id="show-more-button" data-aos="fade-up" data-aos-anchor-placement="center-bottom">
		<a data-url="<?= $url ?>" class="load-more-items"><?= GetMessage("ELSE") ?></a>
	</div>
<? endif; ?>

<div class="referenceSinglePagination" data-aos="fade-up" data-aos-anchor-placement="center-bottom">
	<ul>
		<? if ($arResult["NavPageNomer"] > 1) : ?>

			<li><a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= ($arResult["NavPageNomer"] - 1) ?>"><img src="<?= SITE_TEMPLATE_PATH ?>/images/reference/referenceFilterArrow.png" alt="arrow"></a></li>

		<? else : ?>
			<li><a href="#"><img src="<?= SITE_TEMPLATE_PATH ?>/images/reference/referenceFilterArrow.png" alt="arrow"></a></li>
		<? endif ?>

		<? if ($arResult["NavPageCount"] <= 7) : ?>
			<? if ($arResult["nStartPage"] == $arResult["NavPageNomer"]) : ?>
				<li><a class="active" href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["nStartPage"] ?>"><?= $arResult["nStartPage"] ?></a></li>
			<? else : ?>
				<li><a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["nStartPage"] ?>"><?= $arResult["nStartPage"] ?></a></li>
			<? endif ?>
			<? $arResult["nStartPage"]++ ?>
		<? else : ?>
			<? if (($arResult["NavPageNomer"] >= 3) && ($arResult["NavPageNomer"] <= ($arResult["NavPageCount"] - 2))) : ?>
				<? if ($arResult["NavPageNomer"] - 3 != 0) : ?>
					<li><a href="#">...</a></li>
				<? endif; ?>
				<li><a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["NavPageNomer"] - 2 ?>"><?= $arResult["NavPageNomer"] - 2 ?></a></li>
				<li><a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["NavPageNomer"] - 1 ?>"><?= $arResult["NavPageNomer"] - 1 ?></a></li>

				<li><a class="active" href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["NavPageNomer"] ?>"><?= $arResult["NavPageNomer"] ?></a></li>

				<li><a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["NavPageNomer"] + 1 ?>"><?= $arResult["NavPageNomer"] + 1 ?></a></li>
				<li><a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["NavPageNomer"] + 2 ?>"><?= $arResult["NavPageNomer"] + 2 ?></a></li>
				<? if ($arResult["NavPageNomer"] + 2 != $arResult["NavPageCount"]) : ?>
					<li><a href="#">...</a></li>
				<? endif; ?>
			<? else : ?>
				<li><a <?= ($arResult["NavPageNomer"] == 1) ? 'class="active"' : '' ?> href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=1">1</a></li>
				<li><a <?= ($arResult["NavPageNomer"] == 2) ? 'class="active"' : '' ?> href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=2">2</a></li>
				<li><a <?= ($arResult["NavPageNomer"] == 3) ? 'class="active"' : '' ?> href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=3">3</a></li>
				<li><a href="#">...</a></li>
				<li><a <?= ($arResult["NavPageNomer"] == ($arResult["NavPageCount"] - 2)) ? 'class="active"' : '' ?> href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["NavPageCount"] - 2 ?>"><?= $arResult["NavPageCount"] - 2 ?></a></li>
				<li><a <?= ($arResult["NavPageNomer"] == ($arResult["NavPageCount"] - 1)) ? 'class="active"' : '' ?> href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["NavPageCount"] - 1 ?>"><?= $arResult["NavPageCount"] - 1 ?></a></li>
				<li><a <?= ($arResult["NavPageNomer"] == $arResult["NavPageCount"]) ? 'class="active"' : '' ?> href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["NavPageCount"] ?>"><?= $arResult["NavPageCount"] ?></a></li>
			<? endif; ?>
			<? $arResult["nStartPage"]++ ?>
		<? endif; ?>

		<? if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]) : ?>
			<li><a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= ($arResult["NavPageNomer"] + 1) ?>"><img src="<?= SITE_TEMPLATE_PATH ?>/images/reference/referenceFilterArrow.png" alt="arrow"></a></li>
		<? else : ?>
			<li><a href="#"><img src="<?= SITE_TEMPLATE_PATH ?>/images/reference/referenceFilterArrow.png" alt="arrow"></a></li>
		<? endif ?>
	</ul>
</div>


<script>
	$(document).ready(function() {

		$(document).on('click', '.load-more-items', function() {

			var targetContainer = $('.newsBlockList'),
				url = $('.load-more-items').attr('data-url');

			if (url !== undefined) {
				$('#loading-spinner').show();
				$.ajax({
					type: 'GET',
					url: url,
					dataType: 'html',
					success: function(data) {

						$('.load-more-items').remove();

						var elements = $(data).find('.productNewsSliderItem'),
							pagination = $(data).find('.load-more-items');

						targetContainer.append(elements);
						$('#show-more-button').append(pagination);

						$('#loading-spinner').hide();

					}
				});
			}

		});

	});
</script>