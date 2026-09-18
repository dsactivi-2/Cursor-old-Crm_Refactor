<?php
	include("includes/functions.php");
	include("includes/common.php");
	
		
	/**************************************
	*
	**  FUNCTIONS USED IN INDEX.PHP
	*
	*
	*/
	include("index/index-functions.php");
	
	/**
	* Statusi:
	* 1  	Administrator
	* 2  	Projekt Mendadžer
	* 3 	Projekt Asistent
	* 4 	Obrada
	* 5  	Front Office
	* 6  	Tehnika
	* 7  	Marketing
	* 8   	Vanjski Saradnik
	* 9   	Financije
	* 10 	First Call Agent
	* 11 	DAK
	* 69 	Jebena Markus Permisija
	* 15 	Dipl Agent
	*/
?>
<!DOCTYPE html>
<html>
<head>


<!-- Fix za dropdown, nije radio samo na ovoj stranici -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>




	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
	<title>
		<?php
			getTitle(); 
		?>
	</title>
	<?php include('includes/head.php'); ?>
	
	<style>
		.a_link{
			color: #000000 !important;
		}
		.a_link:hover{
			color: rgb(80 255 0) !important;
			// text-decoration: none !important;
		}
		.a_link_pr{
			color: #ffffff !important;
		}
		.a_link_pr:hover{
			color: rgb(115 232 255) !important;
			text-decoration: none !important;
		}
		.span_style_1{
			font-size: 1.75rem !important;
			border-radius: 13px;
			width: 100%;
			padding: 25px 5px;
		}
		.span_style_broj_1{
			font-size: xxx-large;
		}
		.small_font_size{
			font-size: 70% !important;
		}

    </style>
</head>
<body>
	<header>
		<?php include('header.php'); ?>
	</header>
	<div id="sidebar">
		<?php include('menu.php'); ?>
	</div>
	
	<div id="content">
		<?php
		/**
		*	WELCOME USER-NAME -- BEGINS
		*
		**/
			
			include('index/welcome-name.php');
			
		/**
		*	AGENT DASHBOARD VIEW -- ENDS
		*
		**/
		?>
		
		<?php 
		/**
		*	AGENT DASHBOARD VIEW -- BEGINS
		*
		**/
		if (in_array("15", $employee_status) OR $logged_employee_id == 121 OR $logged_employee_id == 216 OR $logged_employee_id == 227 OR $logged_employee_id == 166 OR $logged_employee_id == 163 OR $logged_employee_id == 204 OR $logged_employee_id == 132 OR $logged_employee_id == 262 OR $logged_employee_id == 360){ 
			//DODANI I ID-EVI TEAM LEADERA
			include('index/agent-components/agent-statistics.php');
			// include('index/agent-components/agent-status-tracker.php');
	
		} 
		/**
		*	AGENT DASHBOARD VIEW -- ENDS
		*
		**/
		?>
		
		
		<?php 
		/**
		*	
		*	DASHBOARD LEVEL ACCESS
		*	@Administrator
		*	@Markus
		*	@Financije
		*	@Agent supervizor
		*	@Employe IDs -> 11, 32, 33 , 108
		**/
	
		if((in_array( "15" , $employee_supervizor)) OR in_array("69", $employee_status) OR (in_array( "1" , $employee_status)) OR (in_array( "9" , $employee_status)) OR ($logged_employee_id == 11) OR ($logged_employee_id == 32) OR ($logged_employee_id == 33)  OR ($logged_employee_id == 108) )
		{
			include('index/statistike/prosjeci-dipl.php');
			include('index/statistike/sales-graph.php');
			// if($logged_employee_id == 75 OR $logged_employee_id == 67){
				// include('index/statistike/statistike_test.php');
			// }else{
				// include('index/statistike/statistika-poslovnica.php');
			// }
			include('index/statistike/statistika-poslovnica.php');
			//include('index/statistike/statistike_test.php');
		} 
		/**
		*	DASHBOARD LEVEL ACCESS -- ENDS
		*
		**/
		?>
		
		<?php 
		/**
		*	
		*	ALL LEVEL ACCESS
		*	@SVI 
		*	OSIM 
		*	@Agent
		*	@Obrada
		*	@Dak
		**/
	
		if(getEmployeeStatus() != 4 AND getEmployeeStatus() != 11 AND (!in_array( "15" , $employee_status)) AND $logged_employee_id == 121 AND $logged_employee_id == 166 AND $logged_employee_id == 163 AND $logged_employee_id == 204 AND $logged_employee_id == 132)
		{
			include('index/statistike/pie-chart-statistics.php'); 
	
		} 
		/**
		*	ALL LEVEL ACCESS -- ENDS
		*
		**/
		?>
		
		<?php 
		/**
		*	
		*	SPECIAL LEVEL ACCESS
		*	@ID -> 75, 20
		*	@Markus
		*
		**/
	
		if($logged_employee_id == 75 OR $logged_employee_id == 20 OR $logged_employee_id == 412 OR in_array("69", $employee_status))
		{
			include('index/statistike/statistika-drzava.php');
	
		} 
		/**
		*	_________ LEVEL ACCESS -- ENDS
		*
		**/
		?>
		
		<?php 
		/**
		*	
		*	SPECIAL LEVEL ACCESS - DEVELOPERI
		*	@ID -> 67, 75, 87, 134
		*
		**/
	
		if($logged_employee_id == 67 OR $logged_employee_id == 75  OR $logged_employee_id == 87  OR $logged_employee_id == 134 )
		{
			include('index/agent-components/agent-status-tracker.php');
		} 
		/**
		*	_________ LEVEL ACCESS -- ENDS
		*
		**/
		?>
		
		
		<footer>
				<?php getCopyright(); ?>
		</footer>
	</div>
</body>
</html>
