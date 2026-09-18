<?php
include("includes/functions.inc.php");

$form="";

if(isset($_REQUEST["form"])) {
	$form = $_REQUEST["form"];

switch ($form)
{

	case "contact_form":

		Global $lang_code;

		$nazi_firme = $_POST['nazi_firme'];
		$kontakt_osoba = $_POST['kontakt_osoba'];
		$telefon = $_POST['telefon'];
		$email = $_POST['email'];
		$poruka = $_POST['poruka'];

		$mail_subject = "Upit sa web stranice SWK";

		$mail_body = "Upit sa web stranice SWK<br>
						<ul>
							<li>Naziv firme: " . $nazi_firme . "</li>
							<li>Kontakt osoba: " . $kontakt_osoba . "</li>
							<li>Telefon: " . $telefon . "</li>
							<li>Email: " . $email . "</li>
							<li>Poruka: " . $poruka . "</li>
						</ul>";

		$mail_altbody = "Upit sa web stranice SWK<br>
						<ul>
							<li>Naziv firme: " . $nazi_firme . "</li>
							<li>Kontakt osoba: " . $kontakt_osoba . "</li>
							<li>Telefon: " . $telefon . "</li>
							<li>Email: " . $email . "</li>
							<li>Poruka: " . $poruka . "</li>
						</ul>";


		sendEmail($email, $kontakt_osoba, $mail_subject, $mail_body, $mail_altbody);

		header("Location: " . getSiteUrlFrontFiles() . "$lang_code/za-firme/1");

	break;

	case "profil":

		Global $lang_code;

		$ime_prezime = $_POST['ime_prezime'];
		$telefon = $_POST['telefon'];
		$email = $_POST['email'];
		$datum_rodjenja = $_POST['datum_rodjenja'];
		$poruka = $_POST['poruka'];

		if($_FILES['doc_cv']['size'] !== 0){

			$doc_cv = $_FILES['doc_cv'];

			//File properties
			$file_name = $doc_cv['name'];
			$file_tmp = $doc_cv['tmp_name'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png');

			if(in_array($file_ext, $allowed)) {

				$doc_cv = uniqid() . '.' . $file_ext;
				$file_destination = "files/attachments/" . $doc_cv;

				if(move_uploaded_file($file_tmp, $file_destination)){}
			}
		}

		if($_FILES['doc_dip']['size'] !== 0){

			$doc_dip = $_FILES['doc_dip'];

			//File properties
			$file_name = $doc_dip['name'];
			$file_tmp = $doc_dip['tmp_name'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png');

			if(in_array($file_ext, $allowed)) {

				$doc_dip = uniqid() . '.' . $file_ext;
				$file_destination = "files/attachments/" . $doc_dip;

				if(move_uploaded_file($file_tmp, $file_destination)){}
			}
		}

		if($_FILES['doc_doc']['size'] !== 0){

			$doc_doc = $_FILES['doc_doc'];

			//File properties
			$file_name = $doc_doc['name'];
			$file_tmp = $doc_doc['tmp_name'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png');

			if(in_array($file_ext, $allowed)) {

				$doc_doc = uniqid() . '.' . $file_ext;
				$file_destination = "files/attachments/" . $doc_doc;

				if(move_uploaded_file($file_tmp, $file_destination)){}
			}
		}

		$mail_subject = "Upit sa web stranice SWK";

		$mail_body = "Upit sa web stranice SWK<br>
						<ul>
							<li>Ime i prezime: " . $ime_prezime . "</li>
							<li>Telefon: " . $telefon . "</li>
							<li>Email: " . $email . "</li>
							<li>Datum rođenja: " . $datum_rodjenja . "</li>
							<li>Poruka: " . $poruka . "</li>
						</ul>";

		$mail_altbody = "Upit sa web stranice SWK<br>
						<ul>
							<li>Ime i prezime: " . $ime_prezime . "</li>
							<li>Telefon: " . $telefon . "</li>
							<li>Email: " . $email . "</li>
							<li>Datum rođenja: " . $datum_rodjenja . "</li>
							<li>Poruka: " . $poruka . "</li>
						</ul>";


		sendEmail($email, $kontakt_osoba, $mail_subject, $mail_body, $mail_altbody, $doc_cv, $doc_dip, $doc_doc);

		header("Location: " . getSiteUrlFrontFiles() . "$lang_code/popuni-profil/1");

	break;

}
}

?>
