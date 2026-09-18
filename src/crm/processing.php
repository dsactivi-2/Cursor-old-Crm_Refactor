<?php
header('Access-Control-Allow-Origin: *');
// $validate_number = preg_replace("/[^0-9+]/", '', $_POST['kandidat_telefon_dipl']);
/************************************
*	VALIDACIJA BEGIN
*************************************/
$string_broj 		= $_POST[	'kandidat_telefon_dipl'	];
$string_ime  		= $_POST[	'kandidat_ime_dipl'		];
$string_prezime  	= $_POST[	'kandidat_prezime_dipl'	];


$number_formated = preg_replace("/[+\s+]/", '', $string_broj); // uklonjeni plus znakovi i razmaci


if( !is_numeric($number_formated) )
{
	echo json_encode('kandidat_telefon_dipl');
	return false;
}

if( strlen($number_formated) < 8 ) // najmanje 8 cifri uključujući i pozivni
{
	echo json_encode('kandidat_telefon_dipl');
	return false;
}

if(preg_match('~[0-9]+~', $string_ime) || preg_match('~[0-9]+~', $string_prezime) ) {
	echo json_encode('String enthaelt Zahlen.');
	return false;
}
/************************************
*	DODATNU VALIDACIJU PISATI ISPOD
*	PO PRIMJERU, GORE NAVEDENOG KODA
*************************************/

/************************************
*	VALIDACIJA END
*************************************/

echo json_encode(true);
return true;


exit();

?>
