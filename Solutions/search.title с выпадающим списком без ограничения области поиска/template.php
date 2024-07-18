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
$this->setFrameMode(true); ?>
<?
$INPUT_ID = trim($arParams["~INPUT_ID"]);
if (strlen($INPUT_ID) <= 0)
	$INPUT_ID = "title-search-input";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if (strlen($CONTAINER_ID) <= 0)
	$CONTAINER_ID = "title-search";
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);

if ($arParams["SHOW_INPUT"] !== "N") : ?>
	<div id="<? echo $CONTAINER_ID ?>">
		<form class="search" action="<? echo $arResult["FORM_ACTION"] ?>">
			<input id="<? echo $INPUT_ID ?>" name="q" type="text" placeholder="поиск" autocomplete="off" value="<?= htmlspecialcharsbx($_GET['q']) ?>" class="form-control" />
			<button name="s" type="submit" class="btn">
				<svg>
					<use href="<?= SITE_TEMPLATE_PATH ?>/icons/sprite.svg#search"></use>
				</svg>
			</button>
		</form>
		<div id="search-results"></div>
	</div>
<? endif ?>

<script>
	$(document).ready(function() {
		var $input = $('#<? echo $INPUT_ID ?>');
		var $resultsContainer = $('#search-results');

		$input.on('input', function() {
			var query = $(this).val();
			if (query.length >= 2) {
				$.ajax({
					url: '<?= SITE_TEMPLATE_PATH ?>/ajax/search.php',
					type: 'GET',
					data: {
						q: query
					},
					success: function(data) {
						//console.log('AJAX response:', data);
						try {
							var results = (typeof data === 'string') ? JSON.parse(data) : data;

							$resultsContainer.empty();

							if (results.status === 'success' && results.data.length > 0) {
								$('#search-results').addClass('active');
								var list = $('<ul></ul>');
								results.data.forEach(function(item) {
									list.append('<li><a href="' + item.URL + '">' + item.TITLE + '</a></li>');
								});
								$resultsContainer.append(list);
							} else {
								$('#search-results').removeClass('active');
								$resultsContainer.append('<p>' + results.message + '</p>');
							}
						} catch (e) {
							$('#search-results').removeClass('active');
							console.error('Error parsing JSON:', e);
							console.log('Raw response:', data);
							$resultsContainer.append('<p>Error processing search results</p>');
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						$('#search-results').removeClass('active');
						console.error('AJAX error:', textStatus, errorThrown);
						$resultsContainer.append('<p>Search request failed</p>');
					}
				});
			} else {
				$('#search-results').removeClass('active');
				$resultsContainer.empty();
			}
		});
	});
</script>



<!-- <script>
	BX.ready(function() {
		new JCTitleSearch({
			'AJAX_PAGE': '<? echo CUtil::JSEscape(POST_FORM_ACTION_URI) ?>',
			'CONTAINER_ID': '<? echo $CONTAINER_ID ?>',
			'INPUT_ID': '<? echo $INPUT_ID ?>',
			'MIN_QUERY_LEN': 2
		});
	});
</script> -->

<!-- 
			<div class="form_search_cell">
				<div class="form_search">
					<form action="/search/">
						<div class="input-group" style="margin-top: -5px;">
							<input type="text" id="title-search-input" name="q" class="form-control" placeholder="<?= getmessage('index_search') ?>" value="">
							<span class="input-group-btn">
								<button class="btn" type="submit"><i class="fa fa-search"></i></button>
							</span>
						</div>
					</form>
				</div>
			</div>

 -->