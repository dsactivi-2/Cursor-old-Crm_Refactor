<?php

	/**************************************
	*
	** CREATED 30.08.2021 -- 
	** SYSTEM FOR MANUAL STATUS SWITCHING - FOR AGENTS
	*
	*
	** Last update - Ismail Suljic - Date: 02.12.2021.
	*
	*/
	
	
	/**
	* 	BEGIN AGENT TRACKING -- RUNS ONLY ONCE PER DAY
	*
	**/
	session_start();
	if(!isset($_SESSION['timetables_set'])){
		/**
		* U slučaju da je ovo prvi put da korisnik otvara index page 
		*  		POSTAVI REDOVE ZA DANAS
		**/
		global $logged_employee_id;
		
		if(createTimetablesForToday()){
			addToLogs('Sistem zapocinje praćenje rada agenta - ID broj : ' .$logged_employee_id ,8);
		}else{
			addToLogs('Sistem nije u mogućnosti pratiti rad agenta - ID broj : ' .$logged_employee_id ,8);
		}
	}
	session_destroy();
?>

<script>

$(document).ready(function ()
	{
		
	/*************************************************************************
		AJAX POZIVI SU SRŽ SKRIPTE 
	**************************************************************************/
	

	/**
	* AJAX CALL - ON WINDOW LOAD
	* PROVJERI KOLIKO VREMENA JE PROVEO AGENT NA SVAKOM STATUSU I ZABILJEŽI TO VIZUELNO
	**/
	$.ajax({
		url: '/ajax_data.php?page=agentStatusTimeCheck',
		type: 'POST',
		data: {
			'status':null
		},
		dataType: 'html', // zašto html a parsam json? Ne znam
		success: function(data)
		{
			let data2 = JSON.parse(data); // niz sa vremenima, gdje indeks svakog elementa predstavlja status 0 - idle , 1 - pauza ... 
			data2.forEach( 
				(element, index) => 
				{
					let vrijeme = parseStringToTime ( element ); // vrijeme jednog statusa
					
					let hr = vrijeme[0];
					let min = vrijeme[1];
					let sec = vrijeme[2];
							
					$ ( '#' + getStatusAssociation( index , 2) ) . html( hr + ':' + min + ':' + sec ); // prikazi za taj status vrijeme na htmlu

				}
			);
			
			checkCurrentActivity(); // nakon što se izlistaju vremena, tek onda provjeriti da li ima trenutno aktivnih statusa
		},
		error: function (xhr, ajaxOptions, thrownError) {
			alert(xhr.status);
			alert(thrownError);
		}
	});
	
	

	

	
	
	// KAD SE KOD KLIKNE BUTTON ZA PROMJENU STATUSA 
	// ZAUSTAVI SVA BROJANJA AKO IH IMA - I AKTIVIRAJ/ZAUSTAVI U BAZI BROJANJE
	$('.status-button').on('click', function() 
	{
		var status_string 	 = $ ( this ) . data( 'target'   )  ; 
		var status_val		 = getStatusAssociation ( status_string, 1 );
		var choosen_element  = $ ( this )  ;
		stopCounters();
		
		if( choosen_element . hasClass 		( 'timer-set' ) )
		{
			cleanCSSForActiveStatus();

			/**
			* AJAX CALL
			* ZAUSTAVI BROJANJE STATUSA
			**/
			$.ajax({
				url: '/ajax_data.php?page=updateStatusStop',
				type: 'POST',
				data: {
					'status':status_val
				},
				dataType: 'json',
				success: function(data)
				{
					// PRIKAŽI DA SI ZAUSTAVIO
					choosen_element . removeClass	( 'timer-set' );
					
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(xhr.status);
					alert(thrownError);
				}
			});
		}
		else
		{
			setButtonActivityStyle( choosen_element  ); 
			
			/**
			* AJAX CALL
			* ZAPOČNI BROJANJE STATUSA
			**/
			$.ajax({
				url: '/ajax_data.php?page=startTimerForAgentStatus',
				type: 'POST',
				data: {
					'status':status_val
				},
				dataType: 'json',
				success: function(data)
				{
					// prikaži da si započeo brojanje
					$( '.status-button' ). removeClass( 'timer-set' );
					choosen_element		 . addClass   ( 'timer-set' );
					
					startCountingTime( choosen_element , data  ); 
					
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(xhr.status);
					alert(thrownError);
				}
			});
		}
			
	});
});
	
		
	/*************************************************************************
									FUNKCIJE
	**************************************************************************/
	
	/**
	* AJAX CALL FUNKCIJA - POZIVA SE NAKON ŠTO SE SAZNAJU VREMENA NA SVIM STATUSIMA
	* PROVJERI AKO JE AGENT VEĆ AKTIVAN NA NEKOM STATUSU I ZABILJEŽI TO VIZUELNO
	**/
	function checkCurrentActivity()
	{
		$.ajax({
			url: '/ajax_data.php?page=getAgentCurrentStatus',
			type: 'GET',
			dataType: 'html', // zašto html a parsam json? Ne znam
			success: function(data) 
			{
				let parsed_data = JSON.parse(data);
				if( parsed_data !== false )
				{
					var status_val = getStatusAssociation ( parseInt(parsed_data), 2 );
					activateHtmlElement ( status_val );
				}
			},
			error: function (xhr, ajaxOptions, thrownError) 
			{
				alert(xhr.status);
				alert(thrownError);
			}
		});
	}
	
	/**
	* Funkcija za vizuelni prikaz obračuna vremena ( otkucaji sekundi ) 
	* @params - vrijeme od kojeg se računa 
	*		  - status/div/ gdje se ispisuje to vrijeme
	*/
    function timeTicker( vrijeme , element)
	{
		let target_element = $( element ) . data('target');
		
		if( vrijeme == null ) 
		{
			let vrijeme_iz_htmla = $( '#' + target_element ) . text();
			vrijeme				 = parseStringToTime ( vrijeme_iz_htmla );
		}
		
		hr	= parseInt( vrijeme [ 0 ] );
		min = parseInt( vrijeme [ 1 ] );
		sec = parseInt( vrijeme [ 2 ] );
		

        sec = sec + 1;

        if (sec == 60)
		{
            min = min + 1;
            sec = 0;
        }
        if (min == 60)
		{
            hr = hr + 1;
            min = 0;
            sec = 0;
        }

        if (sec < 10 || sec == 0)
		{
            sec = '0' + sec;
        }
        if (min < 10 || min == 0)
		{
            min = '0' + min;
        }
        if (hr < 10 || hr == 0)
		{
            hr = '0' + hr;
        }
		
		$('#' + target_element ) . html( hr + ':' + min + ':' + sec );
		vrijeme_za_rekurziju = [ hr , min , sec ];
		// console.log(new Date(hr + ':' + min + ':' + sec));
		// console.log(new Date(vrijeme_za_rekurziju));
		let timer = setTimeout( () => 
							{ 
								timeTicker(vrijeme_za_rekurziju, element);
							}, 1000
						);
		localStorage.setItem('timerId', timer);
	}
	
	/**
	* Funkcija za transformisanje vrste statusa iz inta u string ili obratno
	* @params - string ili int
	*		  - opcija = 1 ili 2
	*/
	function getStatusAssociation(stringOrInt, option)
	{
		// if option 1
			//from string to number 
		// else
			// from number to string
		
		
		if( option == 1 )
		{
			switch(stringOrInt){
				case	'idle' :
					return 0; 
				break; 
				case	'pauza' : 
					return 1; 
				break; 
				case	'live-prodaja' : 
					return 2; 
				break; 
				case	'viber-razgovor' :  
					return 3; 
				break; 
				case	'obuka' :  
					return 4; 
				break; 
				case	'inbound-poziv' :
					return 5; 
				break; 
			}
		}
		else
		{
			switch(stringOrInt){
				case	0 :
					return 'idle'; 
				break; 
				case	1 : 
					return 'pauza'; 
				break; 
				case	2 : 
					return 'live-prodaja'; 
				break; 
				case	3 :  
					return 'viber-razgovor'; 
				break; 
				case	4 :  
					return 'obuka'; 
				break; 
				case    5 :
					return	'inbound-poziv' ;
				break; 
			}
		}
	}
	
	function startCountingTime(element, vrijeme)
	{
		if( vrijeme !== null)
			vrijeme = parseStringToTime( vrijeme );
		
		timeTicker(vrijeme, element);
	}		
		
	function activateHtmlElement ( status )
	{	
		// pronađi element koji treba stilizirati i aktivirati
		let jquery_search_string  = "[data-target=" + status + "]";
		let element_za_aktivirati = $ ( ".status-buttons-container" ).find( jquery_search_string );
		
		// stiliziraj element odgovarajućeg statusa , da naznači se aktivacija
		setButtonActivityStyle( element_za_aktivirati ) ; 
		element_za_aktivirati . addClass ( 'timer-set' );
		
		startCountingTime	  ( element_za_aktivirati , null  ); 
	}
	
	/**
	* VISUAL CHANGES TO THE UI
	* @param  - element that has its style changed 
	*/
	function setButtonActivityStyle(element)
	{
		cleanCSSForActiveStatus();
		
		// set css for new / clicked element
		element.toggleClass('active');
		element.append( "<span class='span-active small-text'>aktivan</span>" );
	}
	
	function parseStringToTime( timeString )
	{
		var vrijeme = timeString.split(":");
		return vrijeme ;
	}
	
	function styleTimeOutput(vrijeme)
	{
		let	hr = parseInt( vrijeme [ 0 ] );
		let	min = parseInt( vrijeme [ 1 ] );
		let sec  = parseInt( vrijeme [ 2 ] );     


		if (sec < 10 || sec == 0) {
			sec = '0' + sec;
		}
		if (min < 10 || min == 0) {
			min = '0' + min;
		}
		if (hr < 10 || hr == 0) {
			hr = '0' + hr;
		}
		let novo_vrijeme = [hr, min, sec];
		
		return novo_vrijeme;
	}
	
	function cleanCSSForActiveStatus()
	{
		// remove css for previous elemnts
		$('.span-active').remove();
		$('.status-button').removeClass('active');
	}
		
	
	function stopCounters(element)
	{
		let timerId = localStorage.getItem('timerId');
		if(timerId)
			clearTimeout(timerId);
	}
	
	
	/*************************************************************************
									KRAJ SKRIPTE
	**************************************************************************/
</script>


<style>
	.small-text{
		font-size:0.925rem;
	}
	@media only screen and (min-width: 968px)  {
		
		.status-buttons-row{
			width: 100%;
			min-height: 20vh;
			display:flex;
			flex-direction: row;
			justify-content: space-between;
			align-items: center;
			margin-right:0px;
			margin-left:0px;
		}
		
		.center-flex{
			display:flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
		}
	}

	.status-tracker{
		margin:1rem;
		background-color: #fff;
		border-radius:10px;
	}
	.status-buttons-container {
		display:flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
	}
	.status-buttons-column {
		display:flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		
		
		min-width:70%;
		padding: 4rem 0.325rem;
		background-color: #8c0000;
		color:#fff;
		font-size: 2rem;
		border-radius: 10px;
	
	}
	
	.status-button{
		display:flex;
		flex-direction:column;
		justify-content: center;
		align-items: center;
		min-width:70%;
		padding: 3.5rem 0.725rem;
		background-color: #6097a0;
		color:#fff;f
		font-size: 2rem;
		border-radius: 10px;
		box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
	}
	.status-button:hover{
		box-shadow: 9px 15px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
	}
	
	.active{
		 background-image: linear-gradient(#6097a0, #68c368);
		 box-shadow: 0;
	}
	.active:hover{
		 box-shadow: 0;
	}
</style>


<div class="container-fluid status-tracker">
	
	<div class="row status-buttons-row p-3">
		<div class="col-sm-12 status-buttons-container">
			<span class="status-button" data-target = "pauza">
				PAUZA 
			</span>
		</div>
		<div class="col-sm-12 status-buttons-container">
			<span class="status-button" data-target = "live-prodaja">
				LIVE PRODAJA
			</span>
		</div>
		<div class="col-sm-12 status-buttons-container">
			<span class="status-button" data-target = "viber-razgovor">
				VIBER RAZGOVOR
			</span>
		</div>
		<div class="col-sm-12 status-buttons-container">
			<span class="status-button" data-target = "obuka">
				OBUKA/TRAINING
			</span>
		</div>
		<div class="col-sm-12 status-buttons-container">
			<span class="status-button" data-target = "inbound-poziv">
				INBOUND POZIV
			</span>
		</div>
	</div>
	
	
	<div class="row status-buttons-row p-3">
		<div class="col-sm-12 center-flex">
			<div class="status-buttons-column">
				<span>PAUZA</span>
				<span class ="timer" id = "pauza" ></span>
			</div>
		</div>
		<div class="col-sm-12 center-flex">
			<div class="status-buttons-column">
				<span>LIVE PRODAJA</span>
				<span class ="timer"  id = "live-prodaja" ></span>
			</div>
		</div>
		<div class="col-sm-12 center-flex">
			<div class="status-buttons-column">
				<span>VIBER RAZGOVOR</span>
				<span class ="timer" id = "viber-razgovor" ></span>
			</div>
		</div>
		<div class="col-sm-12 center-flex">
			<div class="status-buttons-column">
				<span>OBUKA / TRAINING</span>
				<span class ="timer"  id = "obuka" ></span>
			</div>
		</div>
		<div class="col-sm-12 center-flex">
			<div class="status-buttons-column">
				<span>INBOUND POZIV</span>
				<span class ="timer"  id = "inbound-poziv" ></span>
			</div>
		</div>
	</div>
</div>


