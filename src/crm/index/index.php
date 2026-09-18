<?php
// ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); 

	include("../includes/functions.php");
	include("../includes/common.php");


	// /**************************************
	// *
	// ** FUNCTIONS FOR FUNCTIONS USED IN INDEX.PHP
	// *
	// *
	// */
	include("index/index-functions.php");
	
?>


<!DOCTYPE html>
<html>
<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
	<title><?php getTitle(); ?></title>
	<?php include('../includes/head.php'); ?>
	
	<style>
		.a_link{
			color: #000000 !important;
		}
		.a_link:hover{
			color: rgb(80 255 0) !important;
			text-decoration: none !important;
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
			padding: 25px 50px;
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
		<?php include('../header.php'); ?>
	</header>
	<div id="sidebar">
		<?php include('../menu.php'); ?>
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
		if ($logged_employee_id == 87){ 
		
			include('agent-components/agent-statistics.php');
			include('agent-components/agent-status-tracker.php');
	
		} 
		/**
		*	AGENT DASHBOARD VIEW -- ENDS
		*
		**/
		?>
		
		
		<?php 
		/**
		*	
		*	_________ LEVEL ACCESS
		*
		**/
	
		if((in_array( "15" , $employee_supervizor)) OR in_array("69", $employee_status) OR (in_array( "1" , $employee_status)) OR (in_array( "9" , $employee_status)) OR ($logged_employee_id == 11) OR ($logged_employee_id == 32) OR ($logged_employee_id == 33)  OR ($logged_employee_id == 108) )
		{
			include('statistike/prosjeci-dipl.php');
			include('statistike/sales-graph.php');
			include('statistike/statistika-poslovnica.php');
	
		} 
		/**
		*	_________ LEVEL ACCESS -- ENDS
		*
		**/
		?>
		
		<?php 
		/**
		*	
		*	_________ LEVEL ACCESS
		*
		**/
	
		if(getEmployeeStatus() != 4 AND getEmployeeStatus() != 11 AND (!in_array( "15" , $employee_status)))
		{
			include('statistike/pie-chart-statistics.php');
	
		} 
		/**
		*	_________ LEVEL ACCESS -- ENDS
		*
		**/
		?>
		
		<?php 
		/**
		*	
		*	_________ LEVEL ACCESS
		*
		**/
	
		if($logged_employee_id == 75 OR $logged_employee_id == 20 OR $logged_employee_id == 412 OR in_array("69", $employee_status))
		{
			include('statistike/statistika-drzava.php');
	
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
