<?php

/**************************************
*
** CREATED 30.08.2021 -- 
** SYSTEM FOR MANUAL STATUS SWITCHING - FOR AGENTS
*
*
** Last update - Ismail Suljic - Date: 
*
*/

include('query-parametars.php');
?>


<div class="container-fluid">
	<div class = "row">
		<div class="col-md-12 col-xs-12">
			<div class = "row">
				<div class="col-md-6 col-xs-12">
					<div class="idk_box idk_box_shadow" style = "min-height: 0px !important;">
						<div class = "row">
							<div class="col-md-12 col-xs-12">
								<span style = "margin-bottom: 10px;" class="span_style_1 label label-default material-label material-label_default main-container__column text-center">
									Prosjek Naplate
								</span>
							</div>
							<div class="col-md-4 col-xs-12" style = "margin-top: 15px;">
								<span class="span_style_1 label label-success material-label material-label_success main-container__column text-center" title = "Zadnjih 7 dana">
									<small class = "small_font_size">Zadnjih 7 dana</small><br><br>
									<span class = "span_style_broj_1"><a class="a_link_pr" target="_blank" href = "statistike.php?page=prosjek_uplata&tip=7"><?php echo $pn_sed ?? ""; ?></a></span>
								</span>
							</div>
							<div class="col-md-4 col-xs-12" style = "margin-top: 15px;">
								<span class="span_style_1 label label-success material-label material-label_success main-container__column text-center" title = "Zadnjih 30 dana">
									<small class = "small_font_size">Zadnjih 30 dana</small><br><br>
									<span class = "span_style_broj_1"><a class="a_link_pr" target="_blank" href = "statistike.php?page=prosjek_uplata&tip=30"><?php echo $pn ?? ""; ?></a></span>
								</span>
							</div>
							<div class="col-md-4 col-xs-12" style = "margin-top: 15px;">
								<span class="span_style_1 label label-success material-label material-label_success main-container__column text-center" title = "Zadnjih 90 dana">
									<small class = "small_font_size">Zadnjih 90 dana</small><br><br>
									<span class = "span_style_broj_1"><a class="a_link_pr" target="_blank" href = "statistike.php?page=prosjek_uplata&tip=90"><?php echo $pn_90dana ?? ""; ?></a></span>
								</span>
							</div>
							<div class="col-md-2 col-xs-12" style = "margin-top: 15px;">
							</div>
							<div class="col-md-4 col-xs-12" style = "margin-top: 15px;">
								<span class="span_style_1 label label-success material-label material-label_success main-container__column text-center" title = "Zadnjih 6 mjeseci">
									<small class = "small_font_size">Zadnjih 6 mjeseci</small><br><br>
									<span class = "span_style_broj_1"><a class="a_link_pr" target="_blank" href = "statistike.php?page=prosjek_uplata&tip=180"><?php echo $pn_6mjeseci ?? ""; ?></a></span>
								</span>
							</div>
							<div class="col-md-4 col-xs-12" style = "margin-top: 15px;">
								<span class="span_style_1 label label-success material-label material-label_success main-container__column text-center" title = "Zadnju godinu">
									<small class = "small_font_size">Zadnju godinu</small><br><br>
									<span class = "span_style_broj_1"><a class="a_link_pr" target="_blank" href = "statistike.php?page=prosjek_uplata&tip=365"><?php echo $pn_12mjeseci ?? ""; ?></a></span>
								</span>
							</div>
							<div class="col-md-2 col-xs-12" style = "margin-top: 15px;">
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6 col-xs-12">
					<div class="idk_box idk_box_shadow" style = "min-height: 0px !important;">
						<div class = "row">
							<div class="col-md-12 col-xs-12">
								<span style = "margin-bottom: 10px;" class="span_style_1 label label-default material-label material-label_default main-container__column text-center">
									Prosjek Prodaje
								</span>
							</div>
							<div class="col-md-4 col-xs-12" style = "margin-top: 15px;">
								<span class="span_style_1 label label-primary material-label material-label_primary main-container__column text-center" title = "Zadnjih 7 dana">
									<small class = "small_font_size">Zadnjih 7 dana</small><br><br>
									<span class = "span_style_broj_1"><a class="a_link_pr" target="_blank" href = "statistike.php?page=prosjek_predracuna&tip=7"><?php echo $pp_sed ?? ""; ?></a></span>
								</span>
							</div>
							<div class="col-md-4 col-xs-12" style = "margin-top: 15px;">
								<span class="span_style_1 label label-primary material-label material-label_primary main-container__column text-center" title = "Zadnjih 30 dana">
									<small class = "small_font_size">Zadnjih 30 dana</small><br><br>
									<span class = "span_style_broj_1"><a class="a_link_pr" target="_blank" href = "statistike.php?page=prosjek_predracuna&tip=30"><?php echo $pp ?? ""; ?></a></span>
								</span>
							</div>
							<div class="col-md-4 col-xs-12" style = "margin-top: 15px;">
								<span class="span_style_1 label label-primary material-label material-label_primary main-container__column text-center" title = "Zadnjih 90 dana">
									<small class = "small_font_size">Zadnjih 90 dana</small><br><br>
									<span class = "span_style_broj_1"><a class="a_link_pr" target="_blank" href = "statistike.php?page=prosjek_predracuna&tip=90"><?php echo $pp_90dana ?? ""; ?></a></span>
								</span>
							</div>
							<div class="col-md-2 col-xs-12" style = "margin-top: 15px;">
							</div>
							<div class="col-md-4 col-xs-12" style = "margin-top: 15px;">
								<span class="span_style_1 label label-primary material-label material-label_primary main-container__column text-center" title = "Zadnjih 6 mjeseci">
									<small class = "small_font_size">Zadnjih 6 mjeseci</small><br><br>
									<span class = "span_style_broj_1"><a class="a_link_pr" target="_blank" href = "statistike.php?page=prosjek_predracuna&tip=180"><?php echo $pp_6mjeseci ?? ""; ?></a></span>
								</span>
							</div>
							<div class="col-md-4 col-xs-12" style = "margin-top: 15px;">
								<span class="span_style_1 label label-primary material-label material-label_primary main-container__column text-center" title = "Zadnju godinu">
									<small class = "small_font_size">Zadnju godinu</small><br><br>
									<span class = "span_style_broj_1"><a class="a_link_pr" target="_blank" href = "statistike.php?page=prosjek_predracuna&tip=365"><?php echo $pp_12mjeseci ?? ""; ?></a></span>
								</span>
							</div>
							<div class="col-md-2 col-xs-12" style = "margin-top: 15px;">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>