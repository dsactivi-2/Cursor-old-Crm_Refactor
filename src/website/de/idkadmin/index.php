<?php
	include("includes/functions.php");
	include("includes/common.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php getTitle(); ?></title>

	<?php include('includes/head.php'); ?>

	<script src="<?php getSiteURL(); ?>js/Chart.js"></script>

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
				<div class="col-sm-4">
					<h1><i class="fa fa-angle-double-right idk_color_green" aria-hidden="true"></i> Dobrodošli <?php getUserFullname(); ?></h1>
				</div>
				<div class="col-sm-8 text-right idk_margin_top10">

				</div>
				<div class="col-xs-12">
					<?php
						if(isset($_GET['mess'])) {
							$mess = $_GET['mess'];
						}else{
							$mess = 0;
						}

						if($mess == 1){
							echo '<br><div class="alert material-alert material-alert_success">Hvala! Uspješno ste poslali poruku IDK CRM agentima za podršku.</div>';
						}
					?>
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
					<div class="idk_time_box idk_box_shadow">
						<h2 id="idk_clock"><?php echo date('H:i'); ?></h2>
						<p><?php getAdminDate(); ?></p>
					</div>
				</div>
				<div class="col-md-9">
					<div class="idk_events_box idk_box_shadow">
						<div class="row">
							<div class="col-sm-4">
								<div class="idk_events_box_top">
									<ul class="list-unstyled">
										<?php
											$query_post_top10 = $db->prepare("
																		SELECT post_lang_title, post_count, post_lang_url
																		FROM idk_posts
																		LEFT JOIN idk_posts_lang ON idk_posts.post_id = idk_posts_lang.post_lang_postid
																		WHERE post_status != :post_status
																		GROUP BY post_id
																		ORDER BY post_count DESC
																		LIMIT 10");

											$query_post_top10->execute(array(':post_status' => 0));

											while($row_post_top10 = $query_post_top10->fetch()){

												$post_lang_title = $row_post_top10['post_lang_title'];
												$post_count = $row_post_top10['post_count'];
												$post_lang_url = $row_post_top10['post_lang_url'];
										?>
											<li><a href="<?php getSiteUrlFront(); ?><?php echo $post_lang_url; ?>" target="_blank"><span class="label label-primary material-label material-label_xs material-label_primary main-container__column"><?php echo $post_lang_title; ?> - <?php echo $post_count; ?></span></a></li>
										<?php } ?>
									</ul>
								</div>
								<div class="idk_events_box_bottom">
									<strong>Novosti Top 10</strong>
								</div>
							</div>
							<div class="col-sm-8">
								<div class="idk_events_box_top">
									<ul class="list-unstyled">
										<?php
											$query_content_top10 = $db->prepare("
																			SELECT content_count, content_lang_name, content_lang_url
																			FROM idk_content
																			LEFT JOIN idk_content_lang ON idk_content.content_id = idk_content_lang.content_lang_postid
																			WHERE content_status != :content_status
																			GROUP BY content_id
																			ORDER BY content_count DESC
																			LIMIT 10");

											$query_content_top10->execute(array(':content_status' => 0));

											while($row_content_top10 = $query_content_top10->fetch()){

												$content_count = $row_content_top10['content_count'];
												$content_lang_name = $row_content_top10['content_lang_name'];
												$content_lang_url = $row_content_top10['content_lang_url'];
										?>
											<li><a href="<?php getSiteUrlFront(); ?><?php echo $content_lang_url; ?>" target="_blank"><span class="label label-primary material-label material-label_xs material-label_primary main-container__column"><?php echo $content_lang_name; ?>: <?php echo $content_count; ?></span></a></li>
										<?php } ?>
									</ul>
								</div>
								<div class="idk_events_box_bottom">
									<strong>Sadržaj Top 10</strong>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="idk_box idk_box_shadow">
						<h5>Statistika posjeta</h5>
						<div style="width: 95%; margin: 40px auto 0px auto;">
							<canvas id="canvas1" width="100%" height="40"></canvas>
						</div>
					</div>
					<script>
						var randomScalingFactor = function(){ return Math.round(Math.random()*100)};
						var lineChartData = {

							labels : ["Jan","Feb","Mar","Apr","Maj","Jun","Jul","Aug","Sep","Okt","Nov","Dec"],
							datasets : [

								{
									fillColor : "rgba(51,122,183,0.2)",
									strokeColor : "rgba(51,122,183,1)",
									pointColor : "rgba(51,122,183,1)",
									pointStrokeColor : "#fff",
									pointHighlightFill : "#fff",
									pointHighlightStroke : "rgba(51,122,183,1)",
									data : [
									<?php
										//January
										$current_year = date('Y');
										$jan_query = $db->prepare("
															SELECT stats_month, SUM(stats_count) AS num_jan
															FROM idk_stats
															WHERE YEAR(stats_month) = $current_year AND MONTH(stats_month) = 1");

										$jan_query->execute();

										$jan = $jan_query->fetch();

										$num_jan = $jan['num_jan'];

										//February
										$feb_query = $db->prepare("
															SELECT stats_month, SUM(stats_count) AS num_feb
															FROM idk_stats
															WHERE YEAR(stats_month) = $current_year AND MONTH(stats_month) = 2");

										$feb_query->execute();

										$feb = $feb_query->fetch();

										$num_feb = $feb['num_feb'];

										//March
										$mar_query = $db->prepare("
															SELECT stats_month, SUM(stats_count) AS num_mar
															FROM idk_stats
															WHERE YEAR(stats_month) = $current_year AND MONTH(stats_month) = 3");

										$mar_query->execute();

										$mar = $mar_query->fetch();

										$num_mar = $mar['num_mar'];

										//April
										$apr_query = $db->prepare("
															SELECT stats_month, SUM(stats_count) AS num_apr
															FROM idk_stats
															WHERE YEAR(stats_month) = $current_year AND MONTH(stats_month) = 4");

										$apr_query->execute();

										$apr = $apr_query->fetch();

										$num_apr = $apr['num_apr'];

										//May
										$may_query = $db->prepare("
															SELECT stats_month, SUM(stats_count) AS num_may
															FROM idk_stats
															WHERE YEAR(stats_month) = $current_year AND MONTH(stats_month) = 5");

										$may_query->execute();

										$may = $may_query->fetch();

										$num_may = $may['num_may'];

										//June
										$jun_query = $db->prepare("
															SELECT stats_month, SUM(stats_count) AS num_jun
															FROM idk_stats
															WHERE YEAR(stats_month) = $current_year AND MONTH(stats_month) = 6");

										$jun_query->execute();

										$jun = $jun_query->fetch();

										$num_jun = $jun['num_jun'];

										//July
										$jul_query = $db->prepare("
															SELECT stats_month, SUM(stats_count) AS num_jul
															FROM idk_stats
															WHERE YEAR(stats_month) = $current_year AND MONTH(stats_month) = 7");

										$jul_query->execute();

										$jul = $jul_query->fetch();

										$num_jul = $jul['num_jul'];

										//August
										$aug_query = $db->prepare("
															SELECT stats_month, SUM(stats_count) AS num_aug
															FROM idk_stats
															WHERE YEAR(stats_month) = $current_year AND MONTH(stats_month) = 8");

										$aug_query->execute();

										$aug = $aug_query->fetch();

										$num_aug = $aug['num_aug'];

										//September
										$sep_query = $db->prepare("
															SELECT stats_month, SUM(stats_count) AS num_sep
															FROM idk_stats
															WHERE YEAR(stats_month) = $current_year AND MONTH(stats_month) = 9");

										$sep_query->execute();

										$sep = $sep_query->fetch();

										$num_sep = $sep['num_sep'];

										//October
										$oct_query = $db->prepare("
															SELECT stats_month, SUM(stats_count) AS num_oct
															FROM idk_stats
															WHERE YEAR(stats_month) = $current_year AND MONTH(stats_month) = 10");

										$oct_query->execute();

										$oct = $oct_query->fetch();

										$num_oct = $oct['num_oct'];

										//November
										$nov_query = $db->prepare("
															SELECT stats_month, SUM(stats_count) AS num_nov
															FROM idk_stats
															WHERE YEAR(stats_month) = $current_year AND MONTH(stats_month) = 11");

										$nov_query->execute();

										$nov = $nov_query->fetch();

										$num_nov = $nov['num_nov'];

										//December
										$dec_query = $db->prepare("
															SELECT stats_month, SUM(stats_count) AS num_dec
															FROM idk_stats
															WHERE YEAR(stats_month) = $current_year AND MONTH(stats_month) = 12");

										$dec_query->execute();

										$dec = $dec_query->fetch();

										$num_dec = $dec['num_dec'];

									?>
											<?php if(!empty($num_jan)){ echo $num_jan; }else{ echo "0"; } ?>,
											<?php if(!empty($num_feb)){ echo $num_feb; }else{ echo "0"; } ?>,
											<?php if(!empty($num_mar)){ echo $num_mar; }else{ echo "0"; } ?>,
											<?php if(!empty($num_apr)){ echo $num_apr; }else{ echo "0"; } ?>,
											<?php if(!empty($num_may)){ echo $num_may; }else{ echo "0"; } ?>,
											<?php if(!empty($num_jun)){ echo $num_jun; }else{ echo "0"; } ?>,
											<?php if(!empty($num_jul)){ echo $num_jul; }else{ echo "0"; } ?>,
											<?php if(!empty($num_aug)){ echo $num_aug; }else{ echo "0"; } ?>,
											<?php if(!empty($num_sep)){ echo $num_sep; }else{ echo "0"; } ?>,
											<?php if(!empty($num_oct)){ echo $num_oct; }else{ echo "0"; } ?>,
											<?php if(!empty($num_nov)){ echo $num_nov; }else{ echo "0"; } ?>,
											<?php if(!empty($num_dec)){ echo $num_dec; }else{ echo "0"; } ?>,

										]
								}
							]

						}

						window.onload = function(){
							var ctx = document.getElementById("canvas1").getContext("2d");
							window.myLine = new Chart(ctx).Line(lineChartData, {
								responsive: true
							});
						}

					</script>
				</div>
				<div class="col-md-3 col-sm-6">
					<div class="idk_box idk_box_shadow">
                        <div class="row">
                            <div class="col-xs-9">
                                <h5>Napomene</h5>
                            </div>
                            <div class="col-xs-3 text-right">
                                <a class="idk_btn_icon" href="#" data-toggle="modal" data-target="#todoModal"><i class="fa fa-plus"></i></a>
                                <div class="modal material-modal material-modal_primary fade text-left" id="todoModal">
                                    <div class="modal-dialog ">
                                        <div class="modal-content material-modal__content">
                                            <div class="modal-header material-modal__header">
                                                <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title material-modal__title">Dodaj zadatak za uraditi</h4>
                                            </div>
                                            <div class="modal-body material-modal__body">
                                                <form action="<?php getSiteURL(); ?>do?form=add_todo" method="post" role="form" class="form-horizontal">
                                                    <input type="hidden" name="todo_userid" value="<?php echo $logged_user_id; ?>" />
                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <div class="materail-input-block materail-input-block_success">
                                                                <input class="form-control materail-input" type="text" name="todo_title" id="todo_title" placeholder="Zadatak ..." required>
                                                                <span class="materail-input-block__line"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="modal-footer material-modal__footer">
                                                <ul class="list-inline">
                                                    <li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
                                                    <li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button></li>
                                                </ul>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="idk_todo_list">
                            <?php
								$query_todo = $db->prepare("
														SELECT todo_id, todo_title, todo_datetime, todo_status
														FROM idk_todo
														WHERE todo_userid = :todo_userid
														ORDER BY todo_status ASC, todo_datetime DESC
														LIMIT 20");

								$query_todo->execute(array(
												':todo_userid' => $logged_user_id));

								while($row_todo = $query_todo->fetch()){

									$todo_id = $row_todo['todo_id'];
									$todo_title = $row_todo['todo_title'];
									$todo_status = $row_todo['todo_status'];
									$todo_datetime = date('d.m.Y. - H:i', strtotime($row_todo['todo_datetime']));

							?>
                            <hr>
                            <div class="row">
                                <div class="col-xs-2">
                                    <div class="main-container__column material-checkbox-group material-checkbox-group_success">
                                        <form action="<?php getSiteURL(); ?>do?form=edit_todo" method="post">
                                            <input type="hidden" name="todo_id" value="<?php echo $todo_id; ?>" />
                                            <input type="checkbox" onChange="this.form.submit()" id="<?php echo $todo_id; ?>" name="todo_status" value="2" class="material-checkbox idk_todo_index" <?php if($todo_status == 2){ echo "checked"; } ?>>
                                            <label class="material-checkbox-group__label" for="<?php echo $todo_id; ?>"></label>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-xs-8">
                                    <label for="<?php echo $todo_id; ?>"><?php if($todo_status == 2){ echo "<strike>" . $todo_title . "</strike>"; }else{ echo $todo_title; } ?></label>
                                </div>
                                <div class="col-xs-2" data-toggle="tooltip" data-placement="left" title="<?php echo $todo_datetime; ?>">
                                    <i class="fa fa-calendar"></i>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
					</div>
				</div>
				<div class="col-md-3 col-sm-6">
					<div class="idk_box idk_box_shadow">
						<section class="main">
							<div class="custom-calendar-wrap">
								<div id="custom-inner" class="custom-inner">
									<div class="custom-header clearfix">
										<nav>
											<span id="custom-prev" class="custom-prev"></span>
											<span id="custom-next" class="custom-next"></span>
										</nav>
										<h2 id="custom-month" class="custom-month"></h2>
										<h3 id="custom-year" class="custom-year"></h3>
									</div>
									<div id="calendar" class="fc-calendar-container"></div>
								</div>
							</div>
						</section>
						<script type="text/javascript">
							$(function() {

								var transEndEventNames = {
										'WebkitTransition' : 'webkitTransitionEnd',
										'MozTransition' : 'transitionend',
										'OTransition' : 'oTransitionEnd',
										'msTransition' : 'MSTransitionEnd',
										'transition' : 'transitionend'
									},
								transEndEventName = transEndEventNames[ Modernizr.prefixed( 'transition' ) ],
								$wrapper = $( '#custom-inner' ),
								$calendar = $( '#calendar' ),
								cal = $calendar.calendario( {
									onDayClick : function( $el, $contentEl, dateProperties ) {

										window.location.href = "employees-reports?page=add&date=" + dateProperties.day + "-" + dateProperties.month + "-" + dateProperties.year;

									},
									displayWeekAbbr : true
								} ),
								$month = $( '#custom-month' ).html( cal.getMonthName() ),
								$year = $( '#custom-year' ).html( cal.getYear() );

								$( '#custom-next' ).on( 'click', function() {
									cal.gotoNextMonth( updateMonthYear );
								} );
								$( '#custom-prev' ).on( 'click', function() {
									cal.gotoPreviousMonth( updateMonthYear );
								} );

								function updateMonthYear() {
									$month.html( cal.getMonthName() );
									$year.html( cal.getYear() );
								}
							});
						</script>
					</div>
				</div>
			</div>
			<footer>
				<?php getCopyright(); ?>
			</footer>
		</div>
	</div>
</body>
</html>
