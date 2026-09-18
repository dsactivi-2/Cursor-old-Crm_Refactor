<?php 
	//include("includes/function.php");
	
	// Global $userAccess;
	
	// $userAccess = array(
		// "superAdmin" => array(0,1,2),
		// "admin" => array(0,1,2)
	// );
	// echo "User: ".getFirstAndLastNameUserR($userId)."<br>";
	// $companyId = getUserCompanyIdR($userId);
	// echo "Kompanija: ".getCompanyNameR($companyId)."<br>";
	// $naloziId = getAktivniNaloziKompanijeArrayIdR($companyId);
	// $naloziId = implode(",",$naloziId);
	// echo "Aktivni nalozi: ".$naloziId."<br>";
	// $naloziId = explode(",", $naloziId);
	// //print_r($naloziId);
	// foreach($naloziId AS $nalogId){
		// echo " -> Nalog: ".getNazivNalogaR($nalogId)."<br>";
		// $checkPartner = getCheckPartnersR($nalogId);
		// if($checkPartner == 1){
			// $partnersId = getPartneriNalogaArrayIdR($nalogId);
			// foreach($partnersId AS $partnerId){
				// echo "Partner: ";
				// echo $partnerId." Kompanija: ";
				// $companyPartnersId = getCompanyForPartnerIdR($partnerId);
				// echo $companyPartnersId."<br>";
			// }
		// }else{
			// echo "Nema";
		// }
	// }
	//------
	
	// $userAccess = array(
		// "superAdminAccess" => array(
			// "count" => array(0,1,2,3),
			// "nalog" => array(15,16,17,18),
			// "permission" => array(1,1,1,1)
		// ),
		// "adminAccess" => array(
			// "count" => array(0,1,2,3,4),
			// "nalog" => array(40,16,16,17,50),
			// "partner" => array(172,48,100,49,850),
			// "permission" => array(3,2,2,2,3)
		// )
	// );
	
	// if(count($userAccess["superAdminAccess"]["count"]) != 0){
		// foreach($userAccess["superAdminAccess"]["count"] AS $valueCnt){
			// $nalogId = $userAccess["superAdminAccess"]["nalog"][$valueCnt];
			// $paermisionId = $userAccess["superAdminAccess"]["permission"][$valueCnt];
			// echo " NALOG: ".$nalogId." PERMISION: ".$paermisionId." <br> ";
			// $adminPosition = getPositionElementInArrayR($nalogId, $userAccess["adminAccess"]["nalog"]);
			// if(count($adminPosition) != 0){
				// foreach($adminPosition AS $valAdminPos){
					// $partnerId = $userAccess["adminAccess"]["partner"][$valAdminPos];
					// $parmisionAdmin = $userAccess["adminAccess"]["permission"][$valAdminPos];
					// echo $valAdminPos."---> PARTNER: ".$partnerId." PERMISION ".$parmisionAdmin."<br>";
				// }
			// }
			// echo "<br>";
		// }
	// }else{
		// if(count($userAccess["adminAccess"]["count"]) != 0){
			// foreach($userAccess["adminAccess"]["count"] AS $valueCnt){
				// $nalogId = $userAccess["adminAccess"]["nalog"][$valueCnt];
				// $partnerId = $userAccess["adminAccess"]["partner"][$valueCnt];
				// $paermisionId = $userAccess["adminAccess"]["permission"][$valueCnt];
				// echo " NALOG: ".$nalogId." <br> ";
				// echo $valueCnt."---> PARTNER: ".$partnerId." PERMISION ".$paermisionId."<br>";
			// }
		// }else{
			// //dio koda koji ce uraditi unset cookie i vratiti usera na login page sa ispisom poruke da pristup jos nije aktiviran
			// echo "Redirect login";
		// }
	// }
?>