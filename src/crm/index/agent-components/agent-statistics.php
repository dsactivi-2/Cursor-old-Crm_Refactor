<?php
	
	/**************************************
	*
	** CREATED 27.08.2021 -- 
	** AGENT PERSONAL STATISTICS COMPONENT
	*
	*
	** Last update - Ismail Suljic - Date: 
	*
	*/
	
?>

<style>
	.p-1{
		padding: 1rem;
	}
	.p-2{
		padding: 2rem;
	}
	.p-3{
		padding: 3rem;
	}
	.p-4{
		padding: 4rem;
	}
	.p-5{
		padding: 5rem;
	}
	
	.font-1{
		font-size: 1rem !important;
	}
	
	.font-2{
		font-size: 2rem !important;
		
	}
	
	.font-25{
		font-size: 2.5rem !important;
		
	}
	
	.font-3{
		font-size: 3rem !important;
		
	}
	
	.font-4{
		font-size: 4rem !important;
		
	}
	
		
	hr {
		border: 1px solid #dedddd;
		width: 70%;
	}
	
	.shadow{
		box-shadow: 20px 6px 2px 0px #f5f5f53d;
	}
	
	.border-bottom{
		border-bottom: 4px solid #000;
	}
	
	.mx-2{
		margin-left: 1rem;
		margin-right: 1rem;
	}
	
	
	.agent-statistics-window{
		color: #000;
		border-radius: 10px;
		width:100%;
	}
	.statistical-cards{
 		width:100%;
		padding:0;
		border-radius:15px;
		margin-left: 0;
		margin-right: 0;
		background-color: #fff;
	}

	.statistical-cards > * {
		
		background-color: #fff;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		font-size: 3rem;
	}
	
	.time-period-header{
		padding-bottom: 0.5rem;
		padding-top: 0.5rem;
		font-size:2.8rem;
		font-weight: 600;
	}
	.agent_statistics:hover{
		color: #68c368 !important;
		cursor: pointer;
	}
	
	
	@media only screen and (min-width: 768px)  {
		
		.card-2{
			border-left: 3px solid #000;
			border-right: 3px solid #000;
		}
	}
	
	
</style>
<?php

/**
*	GET AGENT STATS PER TIME PERIOD
* 	FUNCTIONS @getProdajeUplatePerioda - prvi red - sve kolone
*			  @getUplateProvizije - drugi red - prva kolona
*/

$employeeTimId =  getLoggedEmployeeTeam($logged_employee_id);
$aktuleniMjesec_pocetak  	=  		date('Y-m-01 00:00:00');
$aktuelniMjesec_kraj 	 	= 		date('Y-m-31 23:59:59');

$prosliMjesec_pocetak	 	=  		date('Y-m-01 00:00:00', strtotime("-1 months")); 
$prosliMjesec_kraj 		 	=  		date('Y-m-31 23:59:59', strtotime("-1 months"));

$zadnja3Mjeseca_pocetak		=  		date('Y-m-01 00:00:00', strtotime("-2 months"));
$zadnja3Mjeseca_kraj	 	= 		date('Y-m-31 23:59:59');


$ukupnoSviMjeseci_pocetak	=  		date('1970-01-01 00:00:00');

$array_AktuelniMjesec 	= 	getProdajeUplatePerioda($aktuleniMjesec_pocetak, $aktuelniMjesec_kraj, $logged_employee_id );
$array_ProsliMjesec	 	= 	getProdajeUplatePerioda($prosliMjesec_pocetak, $prosliMjesec_kraj, $logged_employee_id );
$array_Prosla3Mjesec 	= 	getProdajeUplatePerioda($zadnja3Mjeseca_pocetak, $zadnja3Mjeseca_kraj, $logged_employee_id );

$array_AktuelniMjesec['prodano_link']	= 'predracuni_perioda.php?employee_id='.$logged_employee_id.'&period_od='.$aktuleniMjesec_pocetak.'&period_do='.$aktuelniMjesec_kraj.'&tip=3';
$array_ProsliMjesec['prodano_link']   	= 'predracuni_perioda.php?employee_id='.$logged_employee_id.'&period_od='.$prosliMjesec_pocetak.'&period_do='.$prosliMjesec_kraj.'&tip=3';
$array_Prosla3Mjesec['prodano_link']  	= 'predracuni_perioda.php?employee_id='.$logged_employee_id.'&period_od='.$zadnja3Mjeseca_pocetak.'&period_do='.$zadnja3Mjeseca_kraj.'&tip=3';
$link_aktuelni_mjesec_provizije 		= 'predracuni_perioda.php?employee_id='.$logged_employee_id.'&period_od='.$aktuleniMjesec_pocetak.'&period_do='.$aktuelniMjesec_kraj.'&tip=4';

// $array_ProsliMjeseciAll = getProdajeUplatePerioda($ukupnoSviMjeseci_pocetak, $aktuelniMjesec_kraj, 69 );


$provizija_aktuelniMjesec = getUplateProvizije( $logged_employee_id );
?>

<div class="container-fluid agent-statistics-window text-center">

<!-----------------------------------------------------------
			FIRST ROW -- AGENT STATISTICS
------------------------------------------------------------->
	<div class="row container statistical-cards border-bottom">
		<div class="col-md-4 col-sm-12 card-1 p-1 shadow agent_statistics">
		
			<span class="time-period-header"> Aktuelni mjesec</span>
			<span class="font-25 p-1">Prodaja: <?php echo $array_AktuelniMjesec['prodano']/1; ?></span>
			<span class="font-25">Uplate: <?php echo $array_AktuelniMjesec['uplaceno']/1; ?></span>
			<hr>
			<span class="time-period-header">Procenat uplate <?php echo $array_AktuelniMjesec['procenat']; ?> %</span>
			<a href = "<?php echo $array_AktuelniMjesec['prodano_link']; ?>"></a>
		</div>
		<div class="col-md-4 col-sm-12 card-2 p-1 agent_statistics aktuelni_mjesec" style="cursor: pointer;">
		
			<span class="time-period-header">Prošli mjesec</span>
			<span class="font-25 p-1">Prodaja: <?php echo $array_ProsliMjesec['prodano']/1; ?></span>
			<span class="font-25">Uplate: <?php echo $array_ProsliMjesec['uplaceno']/1; ?></span>
			<hr>
			<span class="time-period-header">Procenat uplate <?php echo $array_ProsliMjesec['procenat']; ?> %</span>
			<a href = "<?php echo $array_ProsliMjesec['prodano_link']; ?>"></a>
		</div>
		<div class="col-md-4 col-sm-12 card-3 p-1 agent_statistics aktuelni_mjesec" style="cursor: pointer;">
		
			<span class="time-period-header">Zadnja 3 mjeseca (aktuelni i 2 prije )</span>
			<span class="font-25 p-1">Prodaja: <?php echo $array_Prosla3Mjesec['prodano']/1; ?></span>
			<span class="font-25">Uplate: <?php echo $array_Prosla3Mjesec['uplaceno']/1; ?></span>
			<hr>
			<span class="time-period-header">Procenat uplate <?php echo $array_Prosla3Mjesec['procenat']; ?> %</span>
			<a href = "<?php echo $array_Prosla3Mjesec['prodano_link']; ?>"></a>
		</div>
	</div>
<!----------------------------------------------------------->


<!-----------------------------------------------------------
		SECOND ROW -- AGENT STATISTICS
------------------------------------------------------------->
	<div class="row container statistical-cards border-bottom ">
		<div class="col-md-4 col-sm-12 card-3 p-1 agent_statistics">
		
			<!-- <span class="time-period-header">Zadnja 3 mjeseca</span> -->
			<span class="font-25 p-1">Uplate iz prošlih mjeseci: <?php echo $provizija_aktuelniMjesec['prosli_mjeseci']/1; ?></span>
			<span class="font-25">Ukupno uplate aktuelni mjesec: <?php echo $provizija_aktuelniMjesec['trenutni_mjesec']/1; ?></span> 
			<hr>
			<?php if($employeeTimId == 1){ ?>
				<span class="time-period-header">Provizija aktuelni mjesec : <?php echo getProvizijaAgenta($logged_employee_id ); ?></span>
			<?php } ?>
			<a href = "<?php echo $link_aktuelni_mjesec_provizije; ?>"></a>
		</div>
		<div class="col-md-8 col-sm-12 card-3 p-1">
			<?php include('call-next-button.php'); ?>
		</div>
	</div>
	
	<script>
		$(".agent_statistics").click(function() {
			window.open($(this).find("a").attr("href")); 
			return false;
		});
	</script>
<!----------------------------------------------------------->
	

</div>



<!-----------------------------------------------------------
		<hr> HORIZONTAL LINE </hr>
------------------------------------------------------------->
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12">
			<hr style="width:100%">
		</div>
	</div>
</div>