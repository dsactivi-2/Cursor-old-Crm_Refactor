<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getUserStatus = getUserStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: navigation?page=list");
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php getTitle(); ?></title>

	<?php include('includes/head.php'); ?>

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
		<?php
			switch ($page){

				case "list":
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fas fa-bars idk_color_green"></i> Navigacija</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>navigation?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-xs-12">
								<?php
									if(isset($_GET['mess'])) {
										$mess = $_GET['mess'];
									}else{
										$mess = 0;
									}

									if($mess == 1){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu navigaciju.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili navigaciju.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali navigaciju.</div>';
									}
								?>
								<?php getNavigation(); ?>
                                <script>
                                  $(".obrisi").click(function() {
                                    var addressValue = $(this).attr("data");
                                    document.getElementById("obrisi_link").href = addressValue;
                                  });
                                </script>
                                <!-- Modal delete-->
                                <div class="modal material-modal material-modal_danger fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="modalDeleteLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content material-modal__content">
                                            <div class="modal-header material-modal__header">
                                                <button class="close material-modal__close" data-dismiss="modal">&times;</span><span class="sr-only">Zatvori</span></button>
                                                <h4 class="modal-title material-modal__title" id="modalDeleteLabel">Brisanje</h4>
                                            </div>
                                            <div class="modal-body material-modal__body">
                                                <p>Jeste li sigurni da želite obrisati kategoriju?</p>
                                                <p><strong>Napomena: Sve potkategorije će biti obrisane!</strong></p>
                                            </div>
                                            <div class="modal-footer material-modal__footer">
                                                <button type="button" class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                                                <a id="obrisi_link" href=""><button type="button" class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
                                            </div>
                                        </div>
                                    </div>
    							</div>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
		<?php
				break;

				case "add":
					if($getUserStatus  == 1 OR $getUserStatus  == 2){

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fas fa-bars idk_color_green"></i> Dodaj novi link u navigaciju</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>navigation?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
                        <br>
                        <div id="myTabs" class="panel-group material-tabs-group">
                            <ul class="nav nav-tabs material-tabs material-tabs_primary">
                                <li class="active"><a href="#tab1" class="material-tabs__tab-link" data-toggle="tab">Sadržaj</a></li>
                                <li><a href="#tab2" class="material-tabs__tab-link" data-toggle="tab">Link</a></li>
                            </ul>
                            <div class="tab-content materail-tabs-content">
                                <div class="tab-pane fade active in" id="tab1">
                                    <div class="row">
            							<div class="col-md-3"></div>
            							<div class="col-md-6">
            								<form id="idk_form" action="<?php getSiteURL(); ?>do?form=add_nav_content" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
                                                <div class="form-group">
                                                    <label for="nav_lang_name" class="control-label">Naziv navigacije:</label>
            										<input class="form-control" type="text" name="nav_lang_name" id="nav_lang_name" placeholder="Naziv navigacije" required>
            									</div>
                                                <div class="form-group">
                                                    <label for="nav_contentid" class="control-label">Izaberi stranicu:</label>
                                                    <select class="form-control" id="nav_contentid" name="nav_contentid" required>
                                                        <option value="">Izaberi stranicu</option>
                                                        <?php
                                                            $query_content = $db->prepare("
                                                                                    SELECT content_id, content_lang_name
                                                                                    FROM idk_content
                                                                                    INNER JOIN idk_content_lang ON idk_content.content_id = idk_content_lang.content_lang_postid
                                                                                    WHERE content_lang_langid = :content_lang_langid AND content_status != :content_status
                                                                                    GROUP BY content_id");

                                                            $query_content->execute(array(
                                                                                    ':content_lang_langid' => 1,
                                                                                    ':content_status' => 0));

                                                            while($row_content = $query_content->fetch()){

                                                                $content_id = $row_content['content_id'];
                                                                $content_lang_name = $row_content['content_lang_name'];
                                                        ?>
                                                            <option value="<?php echo $content_id; ?>"><?php echo $content_lang_name; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="nav_sub" class="control-label">Pripada navigaciji:</label>
                                                    <select class="form-control" id="nav_sub" name="nav_sub" required>
                                                        <option value="0">Samostalna</option>
                                                        <?php
                                                            $query_nav = $db->prepare("
                                                                                    SELECT nav_id, nav_lang_name
                                                                                    FROM idk_navigation
                                                                                    INNER JOIN idk_navigation_lang ON idk_navigation.nav_id = idk_navigation_lang.nav_lang_navid
                                                                                    WHERE nav_lang_langid = :nav_lang_langid
                                                                                    GROUP BY nav_id");

                                                            $query_nav->execute(array(
                                                                                    ':nav_lang_langid' => 1));

                                                            while($row_nav = $query_nav->fetch()){

                                                                $nav_id = $row_nav['nav_id'];
                                                                $nav_lang_name = $row_nav['nav_lang_name'];
                                                        ?>
                                                            <option value="<?php echo $nav_id; ?>"><?php echo $nav_lang_name; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group row">
            										<label for="nav_target" class="col-sm-3 control-label">Novi prozor:</label>
            										<div class="col-sm-9">
            											<div class="main-container__column materail-switch materail-switch_success">
            												<input class="materail-switch__element" name="nav_target" value="1" type="checkbox" id="nav_target">
            												<label class="materail-switch__label" for="nav_target"></label>
            											</div>
            										</div>
            									</div>
                                                <div class="form-group row">
            										<label for="nav_sort" class="col-sm-3 control-label">Pozicija:</label>
            										<div class="col-sm-2">
            											<input class="form-control" type="number" name="nav_sort" id="nav_sort" placeholder="Pozicija" value="0" required>
            										</div>
            									</div>
            									<div class="form-group">
            										<div class="text-right">
                                                        <hr>
            											<ul class="list-inline">
            												<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
            												<li>
            													<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></button>
            												</li>
            											</ul>
            										</div>
            									</div>
                                            </div>
                                            <div class="col-md-3"></div>
            							</form>
            						</div>
                                </div>
                                <div class="tab-pane fade in" id="tab2">
                                    <div class="row">
            							<div class="col-md-3"></div>
            							<div class="col-md-6">
            								<form id="idk_form" action="<?php getSiteURL(); ?>do?form=add_nav" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
                                                <div class="form-group">
                                                    <label for="nav_lang_name" class="control-label">Naziv navigacije:</label>
            										<input class="form-control" type="text" name="nav_lang_name" id="nav_lang_name" placeholder="Naziv navigacije" required>
            									</div>
                                                <div class="form-group">
                                                    <label for="nav_lang_link" class="control-label">Link:</label>
            										<input class="form-control" type="text" name="nav_lang_link" id="nav_lang_link" placeholder="Link" required>
            									</div>
                                                <div class="form-group">
                                                    <label for="nav_sub" class="control-label">Pripada navigaciji:</label>
                                                    <select class="form-control" id="nav_sub" name="nav_sub" required>
                                                        <option value="0">Samostalna</option>
                                                        <?php
                                                            $query_nav = $db->prepare("
                                                                                    SELECT nav_id, nav_lang_name
                                                                                    FROM idk_navigation
                                                                                    INNER JOIN idk_navigation_lang ON idk_navigation.nav_id = idk_navigation_lang.nav_lang_navid
                                                                                    WHERE nav_lang_langid = :nav_lang_langid
                                                                                    GROUP BY nav_id");

                                                            $query_nav->execute(array(
                                                                                    ':nav_lang_langid' => 1));

                                                            while($row_nav = $query_nav->fetch()){

                                                                $nav_id = $row_nav['nav_id'];
                                                                $nav_lang_name = $row_nav['nav_lang_name'];
                                                        ?>
                                                            <option value="<?php echo $nav_id; ?>"><?php echo $nav_lang_name; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group row">
            										<label for="nav_target1" class="col-sm-3 control-label">Novi prozor:</label>
            										<div class="col-sm-9">
            											<div class="main-container__column materail-switch materail-switch_success">
            												<input class="materail-switch__element" name="nav_target" value="1" type="checkbox" id="nav_target1">
            												<label class="materail-switch__label" for="nav_target1"></label>
            											</div>
            										</div>
            									</div>
                                                <div class="form-group row">
            										<label for="nav_sort" class="col-sm-3 control-label">Pozicija:</label>
            										<div class="col-sm-2">
            											<input class="form-control" type="number" name="nav_sort" id="nav_sort" placeholder="Pozicija" value="0" required>
            										</div>
            									</div>
            									<div class="form-group">
            										<div class="text-right">
                                                        <hr>
            											<ul class="list-inline">
            												<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
            												<li>
            													<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></button>
            												</li>
            											</ul>
            										</div>
            									</div>
                                            </div>
                                            <div class="col-md-3"></div>
            							</form>
            						</div>
                                </div>
                            </div>
                        </div>
					</div>
				</div>
			</div>
		<?php
					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}
				break;

				case "edit":
					if($getUserStatus  == 1 OR $getUserStatus  == 2){

						$nav_id = $_GET['id'];

						//Set language
						if(isset($_GET['lang'])){
							$lang_id = $_GET['lang'];
						}else{
							$query_lang = $db->prepare("
											SELECT lang_id
											FROM idk_langs
											WHERE lang_default = :lang_default");

							$query_lang->execute(array(
				                            ':lang_default' => 1));

							$row_lang = $query_lang->fetch();

							$lang_id = $row_lang['lang_id'];
						}

						//Edit content
						$query = $db->prepare("
										SELECT nav_target, nav_contentid, nav_sub, nav_sort, nav_lang_name, nav_lang_link
										FROM idk_navigation
										INNER JOIN idk_navigation_lang ON idk_navigation.nav_id = idk_navigation_lang.nav_lang_navid
										WHERE nav_id = :nav_id AND nav_lang_langid = :nav_lang_langid");

						$query->execute(array(
									':nav_id' => $nav_id,
									':nav_lang_langid' => $lang_id));

						$row = $query->fetch();

							$nav_target = $row['nav_target'];
							$nav_contentid = $row['nav_contentid'];
							$nav_sub = $row['nav_sub'];
							$nav_sort = $row['nav_sort'];
							$nav_lang_name = $row['nav_lang_name'];
							$nav_lang_link = $row['nav_lang_link'];

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fas fa-bars idk_color_green" aria-hidden="true"></i> Uredi navigaciju</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>navigation?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
                        <br>
						<div class="row">
							<div class="col-md-1"></div>
							<div class="col-md-6">
                                <form id="idk_form" action="<?php getSiteURL(); ?>do?form=edit_nav" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
                                    <input type="hidden" name="nav_lang_langid" value="<?php echo $lang_id; ?>">
                                    <input type="hidden" name="nav_id" value="<?php echo $nav_id; ?>">
                                    <div class="form-group">
                                        <label for="nav_lang_name" class="control-label">Naziv navigacije:</label>
                                        <input class="form-control" type="text" name="nav_lang_name" id="nav_lang_name" value="<?php echo $nav_lang_name; ?>" placeholder="Naziv navigacije" required>
                                    </div>
                                    <?php
                                        if($nav_contentid == NULL){
                                    ?>
                                    <div class="form-group">
                                        <label for="nav_lang_link" class="control-label">Link:</label>
                                        <input class="form-control" type="text" name="nav_lang_link" id="nav_lang_link" value="<?php echo $nav_lang_link; ?>" placeholder="Link" required>
                                    </div>
                                    <?php
                                        }else{
                                    ?>
                                    <div class="form-group">
                                        <label for="nav_contentid" class="control-label">Izaberi stranicu:</label>
                                        <select class="form-control" id="nav_contentid" name="nav_contentid" required>
                                            <option value="">Izaberi stranicu</option>
                                            <?php
                                                $query_content = $db->prepare("
                                                                        SELECT content_id, content_lang_name
                                                                        FROM idk_content
                                                                        INNER JOIN idk_content_lang ON idk_content.content_id = idk_content_lang.content_lang_postid
                                                                        WHERE content_lang_langid = :content_lang_langid AND content_status != :content_status
                                                                        GROUP BY content_id");

                                                $query_content->execute(array(
                                                                        ':content_lang_langid' => 1,
                                                                        ':content_status' => 0));

                                                while($row_content = $query_content->fetch()){

                                                    $content_id = $row_content['content_id'];
                                                    $content_lang_name = $row_content['content_lang_name'];

                                                    if($content_id == $nav_contentid){ $selected = "selected"; }else{ $selected = ""; }
                                            ?>
                                                <option value="<?php echo $content_id; ?>" <?php echo $selected; ?>><?php echo $content_lang_name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <?php
                                        }
                                    ?>
                                    <div class="form-group">
                                        <label for="nav_sub" class="control-label">Pripada navigaciji:</label>
                                        <select class="form-control" id="nav_sub" name="nav_sub" required>
                                            <option value="0">Samostalna</option>
                                            <?php
                                                $query_nav = $db->prepare("
                                                                        SELECT nav_id, nav_lang_name
                                                                        FROM idk_navigation
                                                                        INNER JOIN idk_navigation_lang ON idk_navigation.nav_id = idk_navigation_lang.nav_lang_navid
                                                                        WHERE nav_lang_langid = :nav_lang_langid
                                                                        GROUP BY nav_id");

                                                $query_nav->execute(array(
                                                                        ':nav_lang_langid' => 1));

                                                while($row_nav = $query_nav->fetch()){

                                                    $nav_id_q = $row_nav['nav_id'];
                                                    $nav_lang_name = $row_nav['nav_lang_name'];

                                                    if($nav_id_q == $nav_sub){ $selected = "selected"; }else{ $selected = ""; }
                                            ?>
                                                <option value="<?php echo $nav_id_q; ?>" <?php echo $selected; ?>><?php echo $nav_lang_name; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group row">
                                        <label for="nav_target" class="col-sm-3 control-label">Novi prozor:</label>
                                        <div class="col-sm-9">
                                            <div class="main-container__column materail-switch materail-switch_success">
                                                <input class="materail-switch__element" name="nav_target" value="1" type="checkbox" id="nav_target" <?php if($nav_target == NULL){ echo ""; }else{ echo "checked"; } ?>>
                                                <label class="materail-switch__label" for="nav_target"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="nav_sort" class="col-sm-3 control-label">Pozicija:</label>
                                        <div class="col-sm-2">
                                            <input class="form-control" type="number" name="nav_sort" id="nav_sort" value="<?php echo $nav_sort; ?>" placeholder="Pozicija" value="0" required>
                                        </div>
                                    </div>
									<div class="form-group">
										<div class="col-sm-12 text-right">
                                            <hr>
											<ul class="list-inline">
												<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
												<li>
													<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-save" aria-hidden="true"></i> <span>Snimi</span></button>
												</li>
											</ul>
										</div>
									</div>
                                </div>
                                <div class="col-md-1"></div>
                                <div class="col-md-4">
                                    <div class="idk_side_form">
                                        <div class="form-group row">
                                            <div class="col-xs-6">
                                                <?php
													$query_lang_name = $db->prepare("
																				SELECT lang_language
																				FROM idk_langs
																				WHERE lang_id = :lang_id");

													$query_lang_name->execute(array(
																':lang_id' => $lang_id));

													$row_lang_name = $query_lang_name->fetch();

													echo '<p>Jezik: <b>' . $row_lang_name['lang_language'] . '</b></p>';
												?>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                <?php
													$lang_query = $db->prepare("
																		SELECT lang_id, lang_code
																		FROM idk_langs");

													$lang_query->execute();

													echo '<ul class="list-inline">';

													while($lang_row = $lang_query->fetch()) {

														$lang_id = $lang_row['lang_id'];
														$lang_code = $lang_row['lang_code'];

														echo '<li class="list-inline-item"><a href="' . getSiteUrlr() . 'navigation?page=edit&id=' . $nav_id . '&lang=' . $lang_id . '">' . $lang_code . '</a></li>';

													}

													echo '</ul>';
												?>
                                            </div>
                                        </div>
										<br>
                                    </div>
                                </div>
							</form>
						</div>
					</div>
				</div>
			</div>
		<?php
					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}

				break;

				case "archive":
					if($getUserStatus  == 1){

						$nav_id = $_GET['id'];

						//Get
						$query_select = $db->prepare("
												SELECT nav_lang_name
												FROM idk_navigation_lang
												WHERE nav_lang_navid = :nav_lang_navid");

						$query_select->execute(array(
											':nav_lang_navid' => $nav_id));

						$row_select = $query_select->fetch();

						$nav_lang_name = $row_select['nav_lang_name'];

						//Delete files
						$del_query = $db->prepare("
												DELETE FROM idk_navigation
												WHERE nav_id = :nav_id");

						$del_query->execute(array(
												':nav_id' => $nav_id));

						//Delete files1
						$del_query1 = $db->prepare("
												DELETE FROM idk_navigation_lang
												WHERE nav_lang_navid = :nav_lang_navid");

						$del_query1->execute(array(
												':nav_lang_navid' => $nav_id));

						//Add to LOGS
						$log_desc = "Obrisao navigaciju: " . $nav_lang_name . "";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_userid, log_desc, log_date)
										VALUES
											(:log_userid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_userid' => $logged_user_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));


						header("Location: " . getSiteURLr() . "navigation?page=list&mess=3");

					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}
				break;

			}
		?>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>
</html>
