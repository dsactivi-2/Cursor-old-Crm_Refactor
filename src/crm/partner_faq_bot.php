<?php
include("includes/functions.php");
include("includes/common.php");

$getEmployeeStatus = explode(',', getEmployeeStatus());


?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>FAQ</title>

	<?php include('includes/head.php');
	if (in_array($getUserIp, $getIpWhiteList)) {
	?>

</head>

<body>
	<header>
		<?php include('header.php'); ?>
	</header>
	<div id="sidebar">
		<?php include('menu.php'); ?>
	</div>
	<div id="content">
		<div class="container-fluid">

			<div class="row">
				<div class="col-xs-12" >
					<div class="col-xs-4">
						<h1><i class="fa fa-file-text-o" style="color: #337ab7" aria-hidden="true"></i>FAQ</h1>
					</div>
					<div class="col-xs-4 text-center">
						<a href="<?php getSiteURL(); ?>faq_overview" type="button" class="btn btn-primary">
							Pregled vidljivosti
						</a>
					</div>
					<div class="col-xs-4 text-right">
						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addNewQuestion">
							Dodaj novu kategoriju
						</button>
					</div>
				</div>

				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="modal fade" id="addNewQuestion" tabindex="-1" role="dialog" aria-labelledby="addNewQuestionLabel" aria-hidden="true">
					<div class="modal-dialog" role="document">
						<form id="newQuestionForm" onsubmit="submitForm(event)">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="addNewQuestionLabel">Dodaj novo FAQ pitanje</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<label for="newQuestionTextEn">Pitanje (en):</label>
									<input type="text" class="form-control" id="newQuestionTextEn" name="newQuestionTextEn" required>

									<label for="newAnswerTextEn">Odgovor (en):</label>
									<textarea class="form-control" id="newAnswerTextEn" name="newAnswerTextEn"></textarea>

									<label for="newQuestionTextDe">Pitanje (de):</label>
									<input type="text" class="form-control" id="newQuestionTextDe" name="newQuestionTextDe" required>

									<label for="newAnswerTextDe">Odgovor (de):</label>
									<textarea class="form-control" id="newAnswerTextDe" name="newAnswerTextDe"></textarea>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvori</button>
									<button type="submit" class="btn btn-success">Spremi promjene</button>
								</div>
							</div>
						</form>
					</div>
				</div>

				<script>
					function submitForm(event) {
						event.preventDefault()

						var formData = {
							type: 1,
							question_json: {
								en: $("#newQuestionTextEn").val(),
								de: $("#newQuestionTextDe").val(),
							},
							answer_json: {
								en: $("#newAnswerTextEn").val(),
								de: $("#newAnswerTextDe").val(),
							},
							parent_id: null,
						};

						$.ajax({
							url: "<?php echo getSiteURL(); ?>do.php?form=add_question",
							type: "POST",
							contentType: "application/json",
							data: JSON.stringify(formData),
							success: function(response) {
								window.location.reload();
							},
							error: function(error) {
								console.error(error);
							}
						});
					}
				</script>

				<div class="col-md-12">
					<div class="content_box">
						<div class="col-xs-8 col-xs-offset-2" style="padding-bottom: 40px;">
							<div class="col-xs-3 text-center">
							<a disabled id="jobstepButton" type="button" style="min-width: 140px; color: black; background-color: #d3d3d3;" class="btn">
									Jobstep.com
								</a>
							</div>
							<div class="col-xs-3 text-center">
								<a id="paButton" type="button" style="min-width: 140px;" class="btn btn-primary">
									PartnerApp
								</a>
							</div>
							<div class="col-xs-3 text-center">
								<a disabled id="raButton" type="button" style="min-width: 140px; color: black; background-color: #d3d3d3;" class="btn">
									RecruitmentApp
								</a>
							</div>
							<div class="col-xs-3 text-center">
								<a disabled id="jobsoftButton" type="button" style="min-width: 140px; color: black; background-color: #d3d3d3;" class="btn">
									Jobsoft
								</a>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								<?php
								?>
								<h3>Kategorije</h3> <br>
								<div id="app"></div>
								<script src="./components/ChatBotList.js" type="module"></script>

								<script>
									<?php
									$chatbotData = getChatDetails();
									$chatbotDataWithCurrentType = array_map(function ($item) {
										$faq_type = $_GET["type"] ? $_GET["type"] : 1;
										$item['currentType'] = $faq_type;
										return $item;
									}, $chatbotData);
									?>

									const chatbotData = <?php echo json_encode($chatbotDataWithCurrentType); ?>;
									const chatBotList = document.createElement('chat-bot-list');
									chatBotList.setAttribute('data', JSON.stringify(chatbotData));

									document.getElementById('app').appendChild(chatBotList);
								</script>
							</div>
						</div>
					</div>
				</div>
			</div>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>

</html>
<?php } else {
		echo '
				<br/>
				<div class="alert material-alert material-alert_danger">
					<h4>NEMATE PRIVILEGIJE!</h4>
					<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
					<br />
				</div>
';
	} ?>