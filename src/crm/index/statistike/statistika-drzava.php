<?php 

/**************************************
*
** CREATED 30.08.2021 -- 
** KOMPONENTA KOJA PRIKAZUJE STATISTIKU PRODAJE PO DRŽAVAMA
*
*
** Last update - Ismail Suljic - Date: 
*
*/
$query_period_naplate_bih_sum = $db->prepare("
	SELECT 
		SUM(datediff(pr_datum_uplate, DATE_FORMAT(pr_datum_kreiranja, '%Y-%m-%d'))) AS sum_b
	FROM idk_predracuni 
	WHERE pr_vrsta_predracuna = 1 AND pr_uplaceno = 1 AND pr_rata = 1 AND pr_domaca_valuta LIKE 'BAM'
");
$query_period_naplate_bih_sum->execute();
$row_period_naplate_bih_sum = $query_period_naplate_bih_sum->fetch();
$q_period_naplate_bih_count = $db->prepare("
	SELECT 
		COUNT(datediff(pr_datum_uplate, DATE_FORMAT(pr_datum_kreiranja, '%Y-%m-%d'))) AS count_b
	FROM idk_predracuni 
	WHERE pr_vrsta_predracuna = 1 AND pr_uplaceno = 1 AND pr_rata = 1 AND pr_domaca_valuta LIKE 'BAM'
");
$q_period_naplate_bih_count->execute();
$row_period_naplate_bih_count = $q_period_naplate_bih_count->fetch();

$q_period_naplate_srb_sum = $db->prepare("
	SELECT 
		SUM(datediff(pr_datum_uplate, DATE_FORMAT(pr_datum_kreiranja, '%Y-%m-%d'))) AS sum_s
	FROM idk_predracuni 
	WHERE pr_vrsta_predracuna = 1 AND pr_uplaceno = 1 AND pr_rata = 1 AND pr_domaca_valuta LIKE 'RSD'
");
$q_period_naplate_srb_sum->execute();
$row_period_naplate_srb_sum = $q_period_naplate_srb_sum->fetch();
$q_period_naplate_srb_count = $db->prepare("
	SELECT 
		COUNT(datediff(pr_datum_uplate, DATE_FORMAT(pr_datum_kreiranja, '%Y-%m-%d'))) AS count_s
	FROM idk_predracuni 
	WHERE pr_vrsta_predracuna = 1 AND pr_uplaceno = 1 AND pr_rata = 1 AND pr_domaca_valuta LIKE 'RSD'
");
$q_period_naplate_srb_count->execute();
$row_period_naplate_srb_count = $q_period_naplate_srb_count->fetch();

$q_period_naplate_uk_sum = $db->prepare("
	SELECT 
		SUM(datediff(pr_datum_uplate, DATE_FORMAT(pr_datum_kreiranja, '%Y-%m-%d'))) AS sum_u
	FROM idk_predracuni 
	WHERE pr_vrsta_predracuna = 1 AND pr_uplaceno = 1 AND pr_rata = 1
");
$q_period_naplate_uk_sum->execute();
$row_period_naplate_uk_sum = $q_period_naplate_uk_sum->fetch();
$q_period_naplate_uk_count = $db->prepare("
	SELECT 
		COUNT(datediff(pr_datum_uplate, DATE_FORMAT(pr_datum_kreiranja, '%Y-%m-%d'))) AS count_u
	FROM idk_predracuni 
	WHERE pr_vrsta_predracuna = 1 AND pr_uplaceno = 1 AND pr_rata = 1
");
$q_period_naplate_uk_count->execute();
$row_period_naplate_uk_count = $q_period_naplate_uk_count->fetch();

$prosjecan_period_uplate_bih = number_format(((($row_period_naplate_bih_sum["sum_b"]) / $row_period_naplate_bih_count["count_b"])), 2, ',', '');
$prosjecan_period_uplate_srb = number_format(((($row_period_naplate_srb_sum["sum_s"]) / $row_period_naplate_srb_count["count_s"])), 2, ',', '');
$prosjecan_period_uplate_uk = number_format(((($row_period_naplate_uk_sum["sum_u"]) / $row_period_naplate_uk_count["count_u"])), 2, ',', '');

?>



<div class="container-fluid">	
	<div class="row">
		<div class="col-md-12 col-xs-12">
			<div class="idk_box idk_box_shadow" style = "min-height: 0px !important;">
				<div class = "row">
					<div class="col-md-12 col-xs-12">
						<span style = "margin-bottom: 10px;" class="span_style_1 label label-default material-label material-label_default main-container__column text-center">
							Prosječan period uplate
						</span>
					</div>
					<div class="col-md-4 col-xs-12" style = "margin-top: 15px;">
						<span class="span_style_1 label label-warning material-label material-label_warning main-container__column text-center">
							<small class = "small_font_size"><img src="images/bs3d.png" width="25"></small><br><small class = "small_font_size"><?php echo $row_period_naplate_bih_sum["sum_b"]."/".$row_period_naplate_bih_count["count_b"]; ?></small><br><br>
							<span class = "span_style_broj_1"><?php echo $prosjecan_period_uplate_bih; ?></span>
						</span>
					</div>
					<div class="col-md-4 col-xs-12" style = "margin-top: 15px;">
						<span class="span_style_1 label label-warning material-label material-label_warning main-container__column text-center">
							<small class = "small_font_size"><img src="images/sr3d.png" width="25"></small><br><small class = "small_font_size"><?php echo $row_period_naplate_srb_sum["sum_s"]."/".$row_period_naplate_srb_count["count_s"]; ?></small><br><br>
							<span class = "span_style_broj_1"><?php echo $prosjecan_period_uplate_srb; ?></span>
						</span>
					</div>
					<div class="col-md-4 col-xs-12" style = "margin-top: 15px;">
						<span class="span_style_1 label label-warning material-label material-label_warning main-container__column text-center">
							<small class = "small_font_size"><img src="images/globe3d.png" width="25"></small><br><small class = "small_font_size"><?php echo $row_period_naplate_uk_sum["sum_u"]."/".$row_period_naplate_uk_count["count_u"]; ?></small><br><br>
							<span class = "span_style_broj_1"><?php echo $prosjecan_period_uplate_uk; ?></span>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>