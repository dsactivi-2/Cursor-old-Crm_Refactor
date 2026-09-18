<?php
	// $languageUser = getLanguageForUser($userId);
	
	// Moguci rezultatu su:
	//						0 - BH 
	//						1 - DE
	//						2 - EN
	
	//Nizu se pristupa npr: $txtArray["Dobro došli u vaš Dashboard!"][$languageUser];
	
	//Na nultom elementu niza $txtArray["Dobro došli u vaš Dashboard!"][0] - Mora se uvijek nalaziti BH jezik 
	//Na prvom elementu niza $txtArray["Dobro došli u vaš Dashboard!"][1] - Mora se uvijek nalaziti DE jezik 
	//Na drugom elementu niza $txtArray["Dobro došli u vaš Dashboard!"][0] - Mora se uvijek nalaziti EN jezik 
	
	$txtArray = array(

		//+++++++++++++++++++++++++ LANDING FILE +++++++++++++++++++++++++
		"Prijava" => array("Prijava", "Anmelden", "Login"),
		"Sva prava pridržana - Jobstep IT Solutions" => array("Sva prava pridržana - Jobstep IT Solutions", "Alle Rechte vorbehalten - Jobstep IT Solutions", "All rights reserved - Jobstep IT Solutions"),
		//+++++++++++++++++++++++++ LANDING FILE +++++++++++++++++++++++++
		//+++++++++++++++++++++++++ LOGIN FILE +++++++++++++++++++++++++
		"JobStep" => array("JobStep", "JobStep", "JobStep"),
		"Dobrodošli na JobStep platformu!" => array("Dobrodošli na JobStep platformu!", "Willkommen auf der JobStep-Plattform!", "Welcome to the JobStep platform!"),
		"Email:" => array("Email:", "E-mail:", "E-mail:"),
		"Lozinka:" => array("Lozinka:", "Passwort:", "Password:"),
		"Nova lozinka:" => array("Nova lozinka:", "Neues Passwort:", "New Password:"),
		"Ponovite lozinku:" => array("Ponovite lozinku:", "Passwort wiederholen:", "Confirm Password:"),
		"Lozinke se ne poklapaju" => array("Lozinke se ne poklapaju", "Passwörter stimmen nicht überein", "Passwords do not match"),
		"Uspješno ste postavili novu lozinku. Klikom na dugme ispod se možete vratiti na log in page" => array("Uspješno ste postavili novu lozinku. Klikom na dugme ispod se možete vratiti na log in page", "Sie haben erfolgreich ein neues Passwort festgelegt. Sie können zur Anmeldeseite zurückkehren, indem Sie auf die Schaltfläche unten klicken", "You have successfully set a new password. You can return to the log in page by clicking the button below"),
		"Zapamti me" => array("Zapamti me", "Anmeldung speichern", "Remember me"),
		"Zaboravili ste lozinku?" => array("Zaboravili ste lozinku?", "Haben Sie Ihr Passwort vergessen?", "Forgot your password?"),
		"Prijavi se" => array("Prijavi se", "Anmelden", "Sign in"),
		"Pogrešan email" => array("Pogrešan email", "Falsche E-mail", "Wrong e-mail"),
		"Pogrešna lozinka" => array("Pogrešna lozinka", "Falsches Passwort", "Wrong password"),
		"Korisnički profil je deaktiviran" => array("Korisnički profil je deaktiviran", "Benutzerprofil ist deaktiviert", "User profile is deactivated"),
		"Ne brinite! Svima se dešava. Unesite email adresu povezanu sa Vašim računom." => array("Ne brinite! Svima se dešava. Unesite email adresu povezanu sa Vašim računom.", "Keine Sorge! Es passiert jedem. Geben Sie die mit Ihrem Konto verbundene E-Mail-Adresse ein.", "Do not worry! It happens to everyone. Enter the email address associated with your account."),
		"Potvrdi" => array("Potvrdi", "Bestätigen", "Confirm"),
		"Mail je poslan na Vašu adresu. Mail sadrži link na kojem možete da unesete Vašu novu šifru." => array("Mail je poslan na Vašu adresu. Mail sadrži link na kojem možete da unesete Vašu novu šifru.", "Ihnen wurde eine E-Mail geschickt. Die E-Mail enthält einen Link, wo Sie Ihr neues Passwort eingeben können.", "Email has been sent to your address. Email contains a link where you can enter your new password."),
		"Unesite Vašu novu lozinku" => array("Unesite Vašu novu lozinku", "Gib dein neues Passwort ein", "Enter your new password" ),
		"Lozinku možete promjeniti na sljedećem linku:" => array("Lozinku možete promjeniti na sljedećem linku: ", "Unter folgendem Link können Sie Ihr Passwort ändern:", "You can change your password at the following link:" ),
		"Promjena lozinke" => array("Promjena lozinke", "Passwort ändern", "Password change" ),
		"Link za promjenu lozinke" => array("Link za promjenu lozinke", "Link zum Ändern des Passworts", "Link for password change" ),
		//+++++++++++++++++++++++++ LOGIN FILE +++++++++++++++++++++++++
		//++++++++++++++++++++ NOTIFICATIONS FILE ++++++++++++++++++++++
		"Obavijesti" => array("Obavijesti", "Benschrichtigungen", "Notifications"),
		"Nema novih obavijesti!" => array("Nema novih obavijesti!", "Keine neuen Benachrichtigungen!", "No new notifications!"),
		//++++++++++++++++++++ NOTIFICATIONS FILE ++++++++++++++++++++++
		//+++++++++++++++++++++++++ USER FILE ++++++++++++++++++++++++++
		"Korisnički račun" => array("Korisnički račun", "Benutzerkonto", "User Account"),
		"Postavke" => array("Postavke", "Einstellungen", "Settings"),
		"Profil" => array("Profil", "Benutzerprofil", "Profile"),
		"Odjavite se" => array("Odjavite se", "Abmelden", "Log out"),
		//+++++++++++++++++++++++++ USER FILE ++++++++++++++++++++++++++
		//+++++++++++++++++++++++ PROFILE FILE +++++++++++++++++++++++++
		"Profil kandidata" => array("Profil kandidata", "Kandidatenprofil", "Candidate profile"),
		"Jezik" => array("Jezik", "Sprache", "Language"),
		"Edukacija" => array("Edukacija", "Ausbildung", "Education"),
		"Naziv kvalifikacije" => array("Naziv kvalifikacije", "Berufsbezeichnung", "Name of qualification"),
		"Naziv" => array("Naziv", "Name", "Name"),
		"Od" => array("Od", "Von", "Of"),
		"Do" => array("Do", "Bis", "To"),
		"Grad" => array("Grad", "Stadt", "City"),
		"Vrsta" => array("Vrsta", "Grad der Ausbildung", "Degree"),
		"Radno iskustvo" => array("Radno iskustvo", "Berufserfahrung", "Work experience"),
		"Pozicija" => array("Pozicija", "Position", "Position"),
		"Poslodavac" => array("Poslodavac", "Arbeitgeber", "Employer"),
		"Komentar o radniku" => array("Komentar o radniku", "Kommentar", "Comment about the employee"),
		"Status kandidata" => array("Status kandidata", "Kandidatenstatus", "Candidate status"),
		"Detalji o intervjuu" => array("Detalji o intervjuu", "Details zum Vorstellungsgespräch", "Interview details"),
		"Detalji ostalih intervjua" => array("Detalji ostalih intervjua", "Details zu weiteren Vorstellungsgesprächen", "Details of other interviews"),
		"Pitanja i ocjenjivanje" => array("Pitanja i ocjenjivanje", "Fragen und Bewertung", "Questions and evaluation"),
		"Ocjenite intervju" => array("Ocjenite intervju", "Interviewbewertung", "Evaluate the interview"),
		"Pitanje" => array("Pitanje", "Frage", "Question"),
		"Ocjena" => array("Ocjena", "Bewertung", "Evaluation"),
		"Komentar" => array("Komentar", "Kommentar", "Comment"),
		"Ukupna ocjena" => array("Ukupna ocjena", "Gesamtbewertung", "Overall evaluation"),
		"Njemački" => array("Njemački", "Deutsch", "German"),
		"Casting" => array("Casting", "Casting", "Casting"),
		"Intervju" => array("Intervju", "Interview", "Interview"),
		"Odbijen" => array("Odbijen", "Absage", "Rejected"),
		"Prihvaćen" => array("Prihvaćen", "Zusage", "Accepted"),
		"Čeka ugovor" => array("Čeka ugovor", "Arbeitsvertrag in Erstellung", "Awaiting contract"),
		"Poslan ugovor" => array("Poslan ugovor", "Arbeitsvertrag verschickt", "Contract sent"),
		"Potpisan ugovor" => array("Potpisan ugovor", "Arbeitsvertrag unterschrieben", "Contract Signed"),
		"Počeo raditi" => array("Počeo raditi", "Arbeitsbeginn", "Started working"),
		"Datum" => array("Datum", "Datum", "Date"),
		"Vrijeme" => array("Vrijeme", "Uhrzeit", "Time"),
		"Aktuelno" => array("Aktuelno", "Aktuell", "Current"),
		"Vozačka dozvola" => array("Vozačka dozvola", "Führerschein", "Driving licence"),
		"Mogući početak rada" => array("Mogući početak rada", "Vorgesehener Arbeitsbeginn", "Possible start of work"),
		"Bez znanja" => array("Bez znanja", "Ohne Kenntnisse", "Without knowledge"),
		"Opšti komentar" => array("Opšti komentar", "Notizen", "General comment"),
		"Prihvati kandidata" => array("Prihvati kandidata", "Kandidat annehmen", "Accept candidate"),
		"Prihvatam" => array("Prihvatam", "Annehmen", "Accept"),
		"Jeste li sigurni da prihvatate ovog kandidata?" => array("Jeste li sigurni da prihvatate ovog kandidata?", "Möchten Sie diesen Kandidaten annehmen?", "Would you like to accept this candidate?"),
		"Odbij kandidata" => array("Odbij kandidata", "Kandidat absagen", "Reject candidate"),
		"Unesite razlog odbijanja kandidata" => array("Unesite razlog odbijanja kandidata", "Grund der Ablehnung", "Reason for rejection"),
		"Razlog odbijanja" => array("Razlog odbijanja", "Grund der Ablehnung", "Reason for rejection"),
		"Odbijam" => array("Odbijam", "Absage", "Reject"),
		"Jeste li sigurni da odbijate ovog kandidata?" => array("Jeste li sigurni da odbijate ovog kandidata?", "Möchten Sie diesen Kandidaten absagen?", "Do you want to reject this candidate?"),
		"Dodijeli partneru" => array("Dodijeli partneru", "Partner zuordnen", "Assign partner"),
		"Dodijeli" => array("Dodijeli", "Zuordnen", "Assign"),
		"Kasnije" => array("Kasnije", "Später", "Later"),
		"Partner" => array("Partner", "Partner", "Partner"),
		"Lokacija Partnera" => array("Lokacija Partnera", "Einsatzort", "Partner location"),
		"Radna pozicija" => array("Radna pozicija", "Position", "Work Position"),
		"Iznos plate" => array("Iznos plate", "Gehalt", "Salary"),
		"Engleski" => array("Engleski", "Englisch", "English"),
		//+++++++++++++++++++++++ PROFILE FILE +++++++++++++++++++++++++
		//+++++++++++++++++++++++ LISTA KANDIDATA +++++++++++++++++++++++++
		"ss_naziv" => array("ss_naziv", "ss_naziv_de", "ss_naziv"),
		"srednje" => array("SSS", "Mittelschule", "High School"),
		"visoko" => array("VSS", "Hochschule", "College"),
		"Prijavljenih" => array("Prijavljenih", "Bewerber", "Registered"),
		"Ime kandiadta" => array("Ime kandidata", "Name", "Name"),
		"Jezik" => array("Jezik", "Sprache", "Language"),
		"Edukacija" => array("Edukacija", "Ausbildung", "Education"),
		"Odaberi partnera" => array("Odaberi partnera", "Partnerauswahl", "Choose partner"),
		"Smjer" => array("Smjer", "Berufsbezeichnung", "Major"),
		"Vozačka dozvola" => array("Vozačka dozvola", "Führerschein", "Driving license"),
		"Radno iskustvo" => array("Radno iskustvo", "Berufserfahrung", "Work experience"),
		"Akcija" => array("Akcija", "Aktion", "Action"),
		"ODABERITETERMINE" => array("ODABERITE TERMINE", "Termine auswählen", "Select dates"),
		"Ažuriraj podatke" => array("Ažuriraj podatke", "Daten ändern", "Edit data"),
		"Ažuriraj" => array("Ažuriraj", "Ändern", "Edit"),
		"Nije poznato" => array("Nije poznato", "Unbekannt", "Unknown"),
		//+++++++++++++++++++++++ LISTA KANDIDATA +++++++++++++++++++++++++
		//+++++++++++++++++++++++ PROGRESS BARS +++++++++++++++++++++++++
		"ceka_ugovor_1" => array("Čeka", "Arbeitsvertrag", "Employment contract"),
		"ceka_ugovor_2" => array("ugovor", "in Erstellung", "In creation"),
		"poslan_ugovor_1" => array("Poslan", "Arbeitsvertrag", "Employment contract"),
		"poslan_ugovor_2" => array("ugovor", "verschickt", "Sent"),
		"potpisan_ugovor_1" => array("Potpisan", "Arbeitsvertrag", "Employment contract"),
		"potpisan_ugovor_2" => array("ugovor", "unterschrieben", "Signed"),
		//+++++++++++++++++++++++ PROGRESS BARS +++++++++++++++++++++++++
		//+++++++++++++++++++++++ DASHBOARD +++++++++++++++++++++++++
		"Nalog" => array("Nalog", "Auftrag", "Recruitment Order"),
		"Partneri" => array("Partneri", "Partner", "Partner"),
		//+++++++++++++++++++++++ DASHBOARD +++++++++++++++++++++++++
		//+++++++++++++++++++++++ DASHBOARD PERSONAL+++++++++++++++++++++++++
		"Ugovor dashboard" => array("Ugovor", "Arbeitsvertrag", "Employment Contract"),
		"Aktuelni status dashboard" => array("Aktuelni status", "Aktueller status", "Current status"),
		"Viza dashboard" => array("Viza", "Visum", "Visa"),
		//+++++++++++++++++++++++ DASHBOARD PERSONAL+++++++++++++++++++++++++
		//PREVOD TREBA ČEKIRATI ZA :
		"Upload ugovora" => array("Upload ugovora", "Vertrag hochladen", "Upload contract"),
		"Upload" => array("Upload", "Hochladen", "Upload"),
		"Detalji ugovora" => array("Detalji ugovora", "Vertrag Details", "Contract details"),
		"Ugovor" => array("Ugovor", "Vertrag", "Contract"),
		"Mjesto" => array("Mjesto", "Einsatzort", "Place"),
		"Da li je ugovor potpisan?" => array("Da li je ugovor potpisan", "Ist der Vertrag unterschrieben?", "Is the contract signed?"),
		"Datum rođenja" => array("Datum rođenja", "Geburtsdatum", "Date of birth"),
		"Bračno stanje" => array("Bračno stanje", "Familienstand", "Marriage status"),
		"Neoženjen/Neudana" => array("Neoženjen/Neudana", "Ledig", "Single"),
		"Oženjen/Udana" => array("Oženjen/Udana", "Verheiratet", "Married"),
		"Udovac/Udovica" => array("Udovac/Udovica", "Verwitwet", "Widowed"),
		"Razveden/Razvedena" => array("Razveden/Razvedena", "Geschieden", "Divorced"),
		"Nema informacije" => array("Nema informacije", "Keine Information", "No information"),
		"Odaberite razlog odbijanja" => array("Odaberite razlog odbijanja", "Wählen Sie den Ablehnungsgrund aus", "Select the reason for rejection"),
		"Dokumenti" => array("Dokumenti", "Unterlagen", "Documents"),
		"Pregled dokumenata" => array("Pregled dokumenata", "Dokumentenprüfung", "Documents Review"),
		"Potpisan ugovor" => array("Potpisan ugovor", "Unterschriebener Vertrag", "Contract Signed "),
		"Jeste li sigurni da želite obrisati ovaj dokument?" => array("Jeste li sigurni da želite obrisati ovaj dokument?", "Möchten Sie dieses Dokument wirklich löschen?", "Are you sure that you want to delete this document?"),
		"Skini" => array("Skini", "Herunterladen", "Download"),
		"Obriši" => array("Obriši", "Löschen", "Delete"),
		"Pregled" => array("View", "Anzeigen", "View"),
		"Da" => array("Da", "Ja", "Yes"),
		"Ne" => array("Ne", "Nein", "No"),
		"Odaberi dokument" => array("Odaberi dokument", "Dokument auswählen", "Choose document"),
		"5 MB je najveća dozvoljena veličina fajla" => array("5 MB je najveća dozvoljena veličina fajla", "5 MB ist die maximal zulässige Dateigröße", "5 MB is the maximum allowed file size"),
		"Odabrani tip fajla nije dozvoljen" => array("Odabrani tip fajla nije dozvoljen", "Der ausgewählte Dateityp ist nicht zulässig", "The selected file type is not allowed"),
		"Vrijeme odbijanja" => array("Vrijeme odbijanja", "Ablehnungszeit", "Rejection time"),
		"Vrijeme prihvatanja" => array("Vrijeme prihvatanja", "Annahmezeit", "Acceptance time"),
		//REMINDERS START
		"Postoji Reminder koji nije vama namijenjen!" => array("Postoji Reminder koji nije vama namijenjen!", "Es besteht ein Reminder, der Ihnen nicht zugewiesen wurde!", "There is a reminder that is not suitable for you!"),
		"Ovaj reminder je završen od strane" => array("Ovaj reminder je završen od strane", "Dieser Reminder ist von {{username}} abgeschlossen.", "This reminder was completed by {{username}}."),
		"Ovaj reminder je prihvaćen od strane" => array("Ovaj reminder je prihvaćen od strane", "Dieser Reminder ist von {{username}} abgeschlossen.", "This reminder was completed by {{username}}."),
		"Istekla Vam je sesija." => array("Istekla Vam je sesija.", "Sitzung abgelaufen. Aktualisieren Sie die Seite.", "Session timed out. Refresh the page."),
		"Prihvatili ste reminder." => array("Prihvatili ste reminder.", "Sie haben den Reminder angenommen.", "You have accepted the reminder."),
		"Završili ste reminder." => array("Završili ste reminder.", "Sie haben den Reminder abgeschlossen.", "You have completed the reminder."),
		// gogole translate:
		"Djelimično ocijenjen" => array("Djelimično ocijenjen", "Teilweise Bewertet", "Partialy evaluated"),
		"Prihvati / Odbij" => array("Prihvati/ Odbij", "Annehmen/ Ablehnen", "Accept/ Reject"),
		"Dodijeli partneru" => array("Dodijeli partneru", "Partner zuordnen", "Assign a partner"),
		// "Detalji ugovora"" => array("Detalji ugovora", "Vertrag Details", "Contract details"),
		"Upload ugovora" => array("Upload ugovora", "Vertrag hochladen", "Contract upload"),
		"Odaberi tip u filteru" => array("Odaberi tip u filteru", "Wählen Sie im Filter einen Typ aus", "Select a type in the filter"),
		"Neprihvaćen reminder" => array("Neprihvaćen", "Nicht angenommen", "Not accepted"),
		"Prihvaćen reminder" => array("Prihvaćen", "Angenommen", "Accepted"),
		//REMINDERS END

		//Potreban prevod START
		"Pretraga kandidata" => array("Pretraga kandidata", "Kandidatensuche", "Candidate search"),
		"Unesite ime kandidata" => array("Unesite ime kandidata", "Name eingeben", "Enter candidate name"),
		"Nema rezultata za:" => array("Nema rezultata za: ", "Keine Ergebnisse für: ", "No results for: "),
		"Uklonite kandidata iz liste" => array("Uklonite kandidata iz liste", "Entfernen Sie den Kandidaten aus der Liste", "Remove the candidate from the list"),
		"Jeste li sigurni da želite uklonuti kandidata iz liste?" => array("Jeste li sigurni da želite uklonuti kandidata iz liste?", "Sind Sie sicher, dass Sie den Kandidaten aus der Liste entfernen möchten?", "Are you sure that you want to remove the candidate from the list?"),
		"Nemate privilegije za uklanjanje kandidata iz liste!" => array("Nemate privilegije za uklanjanje kandidata iz liste!", "Sie haben keine Berechtigung, den Kandidaten aus der Liste zu entfernen! ", "You do not have the privileges to remove candidates from the list!"),
		"Da, siguran sam" => array("Da", "Ja, ich bin mir sicher", "Yes, I am sure"),
		"Zatvori" => array("Zatvori", "Schließen", "Close"),
		"Lista" => array("Lista", "Liste", "List"),
		"Kompanija" => array("Kompanija", "Unternehmen", "Company"),
		//Potreban prevod END
		"U obradi Lead" => array("U obradi Lead", "Lead in Bearbeitung", "Lead in process"),
		"Prikupljanje dokumentacije" => array("Prikupljanje dokumentacije", "Sammlung der Unterlagen", "Collecting documents"),
		"Poslan zahtjev" => array("Poslan zahtjev", "Antrag geschickt", "Request sent"),
		"U obradi na" => array("U obradi na", "In Bearbeitung bei", "In Progress at"),
		"U obradi" => array("U obradi", "In Bearbeitung", "In Progress"),
		"Dopuna dokumentacije" => array("Dopuna dokumentacije", "Nachforderung der Unterlagen", "Additional request for documents"),

		//Potrebno provjeriti prevod START
		"Nostrifikacija" => array("Nostrifikacija", "Anerkennung", "Nostrification"),
		"U pripremi" => array("U pripremi", "in Vorbereitung", "In preparation"),
		"Zatražen" => array("Zatražen", "Beantragt", "Requested"),
		"Završeno" => array("Završeno", "Abgeschlosen", "Finished"),
		"Certifikat jezika" => array("Certifikat jezika", "Sprachzertifikat", "Language Certificate"),
		"Skuplja dokumentaciju 1" => array("Skuplja", "Sammelt", "Collecting"),
		"Skuplja dokumentaciju 2" => array("dokumentaciju", "Unterlagen", "Documents"),
		"Čeka vizu" => array("Čeka vizu", "Wartet aufs Visum", "Waiting for Visa"),
		"Rezultat" => array("Rezultat", "Ergebnis", "Result"),
		"Status nostrifikacije" => array("Status nostrifikacije", "Anerkennungsstatus", "Nostrification Status"),
		"Svi" => array("Svi", "Alle", "All"),
		"Zakazan termin na" => array("Zakazan termin na", "Termin gebucht am", "Date appointed at"),

		"Zaposlen" => array("Zaposlen", "Eingestellt", "Hired"),
		"Datum termina" => array("Datum termina", "Endgültiger Termin", "Appointment date"),
		"Početak rada" => array("Početak rada", "Arbeitsbeginn", "Start of emplyment"),
		"Skuplja dokumentaciju" => array("Skuplja dokumentaciju", "Sammelt Unterlagen", "Collecting documents"),
		"Čeka termin" => array("Čeka termin", "Warten auf den Termin", "Waiting for the appointment"),
		"Dobio vizu" => array("Dobio vizu", "Visum bekommen", "Got visa"),
		"Odbijena viza" => array("Odbijena viza", "Visum abgelehnt", "Visa rejected"),


		//Potrebno provjeriti prevod END

		//Dokumenti procesa odlaska START - Adis - Provjeriti prevod 24082022 - Prevedeno 10102022 od 14:00 do 16:00 Emina
		"Potrebna dokumentacija" => array("Potrebna dokumentacija", "Erforderliche Unterlagen", "Required documents"),
		"Naziv dokumenta" => array("Naziv dokumenta", "Dokument", "Document name"),
		"Pregled dokumenta" => array("Pregled dokumenta", "Dokumentenüberblick", "Document Overview"),
		"Status dokumenta" => array("Status dokumenta", "Dokumentstatus", "Document status"),
		"Korisnik" => array("Korisnik", "Benutzer", "User"),
		"Dokument nije uploadan" => array("Dokument nije uploadan", "Das Dokument wurde nicht hochgeladen", "The document has not been uploaded"),
		"Izvršite upload skeniranog dokumenta" => array("Izvršite upload skeniranog dokumenta", "Laden Sie das gescannte Dokument hoch", "Upload the scanned document"),
		"Završi" => array("Završi", "Beenden", "Finish"),
		"Odustani" => array("Odustani", "Aufgeben", "Quit"),
		"Dokument koji pokuštavate dodati je veći od dozvoljene veličine!" => array("Dokument koji pokuštavate dodati je veći od dozvoljene veličine!", "Das Dokument, das Sie hinzufügen möchten, ist größer als die erlaubte Größe!", "The document you are trying to add is larger than the allowed size!"),
		"Format dokumenta koji pokušavate dodati nije dozvoljen!<br>Koristite format '.pdf', '.doc' ili '.docx'." => array("Format dokumenta koji pokušavate dodati nije dozvoljen!<br>Koristite format '.pdf', '.doc' ili '.docx'.", "Das Dokumentformat, das Sie hochladen möchten, ist nicht erlaubt!<br>Bitte verwenden Sie das Format „.pdf“, „.doc“ oder „.docx“."),
		"Unesite vaš komentar ovdje..." => array("Unesite vaš komentar ovdje...", "Geben Sie hier Ihren Kommentar ein...", "Enter your comment here..."),
		"Nema podataka o korisniku!" => array("Nema podataka o korisniku!", "Keine Informationen über den Benutzer!", "There is no information about the user!"),
		"Čeka se provjera dokumenta" => array("Čeka se provjera dokumenta", "Ausstehende Bestätigung des Dokuments", "Document verification is pending"),
		"Čeka se provjera svih dokumenata nakon čega će biti omogućena opcija označavanja da su orginalni dokumenti poslani!" => array("Čeka se provjera svih dokumenata nakon čega će biti omogućena opcija označavanja da su orginalni dokumenti poslani!", "Die Überprüfung aller Dokumente steht noch aus, danach wird die Option zur Markierung, dass die Originaldokumente gesendet wurden, aktiviert!", "Verification of all documents is pending, after which the option to mark that the original documents have been sent will be activated!"),
		"Detalji" => array("Detalji", "Details", "Details"),
		"Detalji dokumenta" => array("Detalji dokumenta", "Dokumentdetails", "Document details"),
		"Komentar nije napisan" => array("Komentar nije napisan", "Kein Kommentar", "No comment"),
		"Historija statusa" => array("Historija statusa", "Statusverlauf", "Status history"),
		"Broj dana" => array("Broj dana", "Anzahl der Tage", "Number of days"),
		"Pošalji dokumente" => array("Pošaljite dokumente", "Senden Sie die Unterlagen", "Send the documents"),
		"Akcija služi da bi se označilo da su originalni dokumenti poslani!" => array("Akcija služi da bi se označilo da su originalni dokumenti poslani!", "Die Aktion dient zur Anzeige, dass die Originaldokumente versendet wurden!", "The action serves to indicate that the original documents have been sent!"),
		"Koristite opciju iznad tabele za označavanje da su dokumenti poslani!" => array("Koristite opciju iznad tabele za označavanje da su dokumenti poslani!", "Verwenden Sie die Option über der Tabelle, um anzuzeigen, dass die Dokumente gesendet wurden!", "Use the option above the table to indicate that the documents have been sent!"),
		"Dokumenti za slanje su nedostupni! Osvježite stranicu!" => array("Dokumenti za slanje su nedostupni! Osvježite stranicu!", "Dokumente, die gesendet werden müssen, sind nicht worhanden!", "Documents to send are unavailable! Refresh the page!"),
		"Čeka se prijem dokumenata!" => array("Čeka se prijem dokumenata!", "Warten auf Empfang der Unterlagen!", "Waiting to receive the documents!"),
		"Na trenutnom statusu dokumenta nema predviđenih akcija." => array("Na trenutnom statusu dokumenta nema predviđenih akcija.", "Es bestehen keine Aktionen zum aktuellen Status des Dokuments.", "There are no planned actions on the current status of the document."),
		"Izvršite upload ispravke dokumenta" => array("Izvršite upload ispravke dokumenta", "Dokumentkorrektur hochladen", "Upload document corrections"),
		"Rok predaje" => array("Rok predaje", "Zusendungsfrist", "Application deadline"),
		"Progres dokumenata" => array("Progres dokumenata", "Progress der Dokumente", "Documents progress"),
		"Datum isteka vize" => array("Datum isteka vize", "Ablaufdatum des Visums", "Visa Expiration date"),
		"U procesu prikupljanja dokumenata niste zaduženi ni za jedan dokument" => array("U procesu prikupljanja dokumenata niste zaduženi ni za jedan dokument", "Sie sind bei der Sammlung der Dokumente für kein Dokument zuständig", "In the process of collecting documents, you are not in responsible for any document"),
		//Dokumenti procesa odlaska END - Adis - Provjeriti prevod 24082022 - Prevedeno 10102022 od 14:00 do 16:00 Emina

		//Dokumenti proces odbijenice/dopune START - Adis - Provjeriti prevod 13092022 - Prevedeno 10102022 od 14:00 do 16:00 Emina
		"Nepotpuna viza" => array("Nepotpuna viza", "Unvollständiges Visum", "Incomplete visa"),
		"Zaprimio" => array("Zaprimio", "Erhalten", "Received"),
		"Datum prijema" => array("Datum prijema", "Eingangsdatum", "Date of receipt"),
		"Krajnji datum" => array("Krajnji datum", "Zusendungsfrist", "End date"),
		"Kandidat" => array("Kandidat", "Kandidat", "Candidate"),
		"Poslodavac" => array("Poslodavac", "Arbeitgeber", "Employer"),
		"Dopuna" => array("Dopuna", "Nachforderung", "Additional request"),
		"Odbijenica" => array("Odbijenica", "Ablehnung", "Rejection"),
		"Za ovu dopunu/odbijenicu niste zaduženi ni za jedan dokument" => array("Za ovu dopunu/odbijenicu niste zaduženi ni za jedan dokument", "Bei dieser Ablehnung/Nachforderung sind Sie für kein Dokument zuständig", "You are not responsible for any document in the event of this rejection/subsequent request"),
		//Dokumenti proces odbijenice/dopune END -Adis - Provjeriti prevod 13092022 - Prevedeno 10102022 od 14:00 do 16:00 Emina

		//Historija dokumenata START - Adis - Provjeriti prevod 15092022 - Prevedeno 10102022 od 14:00 do 16:00 Emina
		"Historija dokumenata" => array("Historija dokumenata", "Dokumentenverlauf", "Document history"),
		"Pogledajte detalje" => array("Pogledajte detalje", "Details Ansehen", "See details"),
		"Ne postoje informacije o prošlim dopunama/odbijenicama za ovog kandidata!" => array("Ne postoje informacije o prošlim dopunama/odbijenicama za ovog kandidata!", "Für diesen Kandidaten liegen keine Informationen über vergangene Nachforderungen/Ablehnungen vor!", "For this candidate there is no information about past additional requests/rejections!"),
		"Kod odbijenice nema uslova za žalbu!" => array("Kod odbijenice nema uslova za žalbu!", "Bei dieser Ablehnung besteht keine Remonstrationsmöglichkeit!", "There are no conditions for appeal in case of rejection!"),
		//Historija dokumenata END - Adis - Provjeriti prevod 15092022 - Prevedeno 10102022 od 14:00 do 16:00 Emina
		"Dana na statusu" => array("Dana na statusu", "Tage auf Status", "Days on status"),

		"Nivo" => array("Nivo", "Sprachniveau", "Level"),
		"Januar" => array("Januar", "Januar", "January"),
		"Februar" => array("Februar", "Februar", "February"),
		"Mart" => array("Mart", "März", "March"),
		"April" => array("April", "April", "April"),
		"Maj" => array("Maj", "Mai", "May"),
		"Juni" => array("Juni", "Juni", "June"),
		"Juli" => array("Juli", "Juli", "July"),
		"August" => array("August", "August", "August"),
		"Septembar" => array("Septembar", "September", "September"),
		"Oktobar" => array("Oktobar", "Oktober", "October"),
		"Novembar" => array("Novembar", "November", "November"),
		"Decembar" => array("Decembar", "Dezember", "December"),
		"Dashboard kandidati" => array("Dashboard kandidat", "Dashboard Kandidaten", "Dashboard Candidates"),
		"Dashboard personal" => array("Dashboard personal", "Dashboard Personal", "Dashboard Personal"),
		"Samoprocjena" => array("Samoprocjena", "Selbsteinschätzung", "Self-assessment"),
		"Sam uči" => array("Sam uči", "Lernt alleine", "Learn alone"),
		"Pohađa podnivo 1" => array("Pohađa podnivo 1", "Besucht die Unterstufe 1", "Studying sublevel 1"),
		"Pohađa podnivo 2" => array("Pohađa podnivo 2", "Besucht die Unterstufe 2", "Studying sublevel 2"),
		"Čeka datum polaganja" => array("Čeka datum polaganja", "Wartet auf den Prüfungstermin", "Waiting for exam date"),
		"Čeka polaganje" => array("Čeka polaganje", "Wartet auf die Prüfung", "Waiting for exam"),
		"Čeka razultat" => array("Čeka razultat", "Wartet auf die Ergebnisse", "Waiting for exam result"),
		"Ima certifikat" => array("Ima certifikat", "Zertifikat vorhanden", "Has Certificate"),
		"Certifikat istekao" => array("Certifikat istekao", "Zertifikat abgelaufen", "Certificate expired"),
		"Napreduje na veći nivo" => array("Napreduje na veći nivo", "Macht Fortschritte", "Advancing to higher level"),
		"Nije položio" => array("Nije položio", "Nicht bestanden", "Failed exam"),
		"Odustao" => array("Odustao", "Hat aufgegeben", "Gave up"),
		"Arhiva" => array("Arhiva ", "Archiv", "Archive"),
		"Ukupno:" => array("Ukupno:", "Insgesamt:", "Total:"),

		//Poslati za prevod:
		"Označavanje slanja ugovora" => array("Označavanje slanja ugovora", "Markierung des Versands von Verträgen", "Marking the dispatch of contracts"),
		"Datum slanja ugovora" => array("Datum slanja ugovora", "Datum des Versands von Verträgen", "Date of dispatch of contracts"),
		"Tracking code ugovora" => array("Tracking code ugovora", "Tracking code von Verträgen", "Contract tracking code"),
		"Link za provjeru" => array("Link za provjeru", "Link zur Überprüfung", "Review Link"),
		"Informacije o poslanom ugovoru nije moguće spremiti" => array("Informacije o poslanom ugovoru nije moguće spremiti", "Informationen über den gesendeten Vertrag können nicht gespeichert werden", "Information about the sent contract cannot be saved"),
		"Greška" => array("Greška", "Fehler", "Error"),
		"Informacije o poslanom ugovoru već postoje" => array("Informacije o poslanom ugovoru već postoje", "Informationen über den gesendeten Vertrag existieren bereits", "Information about the sent contract already exists"),
		// Novi prevodi za taskmanager
		"Preuzeti zadaci" => array("Preuzeti zadaci", "Angenommene Aufgaben", "Accepted tasks"),
		"Preuzmi zadatak" => array("Preuzmi zadatak", "Annehmen", "Accept"),
		"Preuzet" => array("Preuzet", "Ausgewählt", "Accepted"),
		"Nije preuzet" => array("Nije preuzet", "Ausgewählt", "Not Accepted"),
		
		/*
			NAPOMENA: U slucaju promjena pogledati funkcije getOtherTranslations i getRemindersName u file-u cron_reminders_email.php START
			*/
			"Djelimično ocijenjeni" => array("Djelimično ocijenjeni", "Teilweise bewertet", "Partialy evaluated"),
			"Neprihvaceni/neodbijeni" => array("Neprihvaceni/neodbijeni", "Offen", "Unaccepted/unrejected"),
			"Bez partnera" => array("Bez partnera", "Ohne Partner", "Without a partner"),
			"Bez detalja o ugovoru" => array("Bez detalja o ugovoru", "Ohne Vertragsdetails", "No contract details"),
			"Posalji ugovor postom" => array("Posalji ugovor postom", "Vertrag per Post senden", "Send the contract by post"),
			"Preuzmi nostrifikovanu diplomu (i zatrazi qualiplan)" => array("Preuzmi nostrifikovanu diplomu (i zatrazi qualiplan)", "Anerkennungsbescheid übernehmen und Qualiplan anfordern", "Download the certified diploma and request a qualiplan"),
			"Uploaduj qualiplan" => array("Uploaduj qualiplan", "Qualiplan hochladen", "Upload qualiplan"),
			"Upload dokumente A" => array("Upload dokumente A", "Unterlagen hochladen ", "Upload documents"),
			"Upload ispravljenih dokumenata" => array("Upload ispravljenih dokumenata", "Korrigierte Unterlagen hochladen ", "Upload of corrected documents"),
			"Posalji dokumente" => array("Posalji dokumente", "Unterlagen senden", "Send the documents"),
			"Upload dokumente dopune/odbijenice A" => array("Upload dokumente dopune/odbijenice A", "Unterlagen für Ergänzung/Ablehnung hochladen", "Upload documents for addition/rejection"),
			"Upload ispravke dokumenata dopune/odbijenice" => array("Upload ispravke dokumenata dopune/odbijenice", "Korrigierte Unterlagen für Ergänzung/Ablehnung hochladen", "Upload corrected documents for addition/rejection"),
			"Posalji dokumente dopune/odbijenice" => array("Posalji dokumente dopune/odbijenice", "Unterlagen für Ergänzung/Ablehnung senden", "Send documents for addition/rejection"),
			/*
			NAPOMENA: U slucaju promjena pogledati funkcije getOtherTranslations i getRemindersName u file-u cron_reminders_email.php END
		*/
		"Dodjela zadataka" => array("Dodjela zadataka", "Aufgabenzuweisung", "Tasks assignment"),
		"novi zadatak Vam je dodijeljen!" => array("novi zadatak Vam je dodijeljen!", "neue Aufgabe wurde Ihnen zugewiesen!", "new task assigned to you!"),
		"nova zadatka su Vam dodijeljena!" => array("nova zadatka su Vam dodijeljena!", "neue Aufgaben wurden Ihnen zugewiesen!", "new tasks assigned to you!"),
		"odabrani zadatak je već zauzet!" => array("odabrani zadatak je već zauzet!", "ausgewählte Aufgabe ist bereits zugewiesen!", "selected task is already assigned!"),
		"odabrana zadatka su već zauzeta!" => array("odabrana zadatka su već zauzeta!", "ausgewählte Aufgaben sind bereits zugewiesen!", "selected tasks are already assigned!"),
		"odabrani zadatak je već završen!" => array("odabrani zadatak je već završen!", "ausgewählte Aufgabe ist bereits abgeschlossen!", "selected task is already completed!"),
		"odabrana zadatka su već završena!" => array("odabrana zadatka su već završena!", "ausgewählte Aufgaben sind bereits abgeschlossen!", "selected tasks have already been completed!"),

		/*
			Prevesti Adis - 05.09.2023 START
			*/
			"Intervjuer" => array("Intervjuer", "Interviewer", "Interviewer"),
			"Preporuka" => array("Preporuka", "Decision Recommendation", "Decision Recommendation"),
			"Glavni razlog za (odluku o zapošljavanju) / konačna presuda" => array("Glavni razlog za (odluku o zapošljavanju) / konačna presuda", "Main Reason for (Hiring Decision) / Final Verdict", "Main Reason for (Hiring Decision) / Final Verdict"),
			"Odaberite opciju" => array("Odaberite opciju", "Select an option", "Select an option"),
			"Zaposliti" => array("Zaposliti", "Hire", "Hire"),
			"Odbiti" => array("Odbiti", "Reject", "Reject"),
			"Neodlučno" => array("Neodlučno", "Not sure", "Not sure"),
			/*
			Prevesti Adis - 05.09.2023 START
		*/

		"Ostalo" => array("Ostalo", "Anderes", "Other"),


		//+++++++++++++++++++++++++ PP PREVOD DRŽAVE - START +++++++++++++++++++++++++
		"Bosna i Hercegovina" 	=> array("Bosna i Hercegovina", 	"Bosnien und Herzegowina", 		"Bosnia and Herzegovina"),
		"Hrvatska" 				=> array("Hrvatska", 				"Kroatien", 					"Croatia"),
		"Njemačka" 				=> array("Njemačka", 				"Deutschland", 					"Germany"),
		"Srbija" 				=> array("Srbija", 					"Serbien", 						"Serbia"),
		"Albanija" 				=> array("Albanija", 				"Albanien", 					"Albanija"),
		"Austrija" 				=> array("Austrija", 				"Österreich", 					"Austria"),
		"Bugarska" 				=> array("Bugarska", 				"Bulgarien", 					"Bulgaria"),
		"Crna Gora" 			=> array("Crna Gora", 				"Montenegro", 					"Montenegro"),
		"Danska" 				=> array("Danska", 					"Dänemark", 					"Denmark"),
		"Italija" 				=> array("Italija", 				"Italien", 						"Italy"),
		"Kosovo" 				=> array("Kosovo", 					"Kosovo", 						"Kosovo"),
		"Mađarska" 				=> array("Mađarska", 				"Ungarn", 						"Hungary"),
		"Makedonija" 			=> array("Makedonija", 				"Mazedonien", 					"Macedonia"),
		"Slovenija" 			=> array("Slovenija", 				"Slowenien", 					"Slovenia"),
		"Švicarska" 			=> array("Švicarska", 				"Schweiz", 						"Switzerland"),
		//+++++++++++++++++++++++++ PP PREVOD DRŽAVE -  END  +++++++++++++++++++++++++

		/*
			Prijevodi za cron_reminders_email.php START
			NAPOMENA: U slucaju promjena pogledati funkcije getOtherTranslations i getRemindersName u file-u cron_reminders_email.php
			*/
				"Lista zadataka" => array("Lista zadataka", "Aufgabenliste", "Task list"),
				"Hitno" => array("Hitno", "Dringend", "Urgent"), 
				"Poštovana" => array("Poštovana", "Sehr geehrte Frau", "Dear"),
				"Poštovani" => array("Poštovani", "Sehr geehrter Herr", "Dear"),
				"Mail body text za izvršnog usera" => array("U aplikaciji JobSoft imate aktivnih zadataka.","Sie haben aktive Aufgaben in der JobSoft-Anwendung.","You have active tasks in the JobSoft application."),
				"Mail body text za controlling usera" => array("Navedeni zadaci u JobSoft aplikaciji su dostupni i već su dva puta poslani vašim zaposlenicima. Ljubazno vas podsjećamo da je važno redovno i pravovremeno obavljati ove zadatke. Na taj način možemo olakšati proces posredovanja koliko je god moguće. Cijenimo vašu podršku.", "Die nachstehenden Aufgaben in der JobSoft-App liegen bereits vor und wurden bereits zweimal an Ihre Mitarbeiter gesendet. Wir möchten Sie höflich daran erinnern, dass es wichtig ist, diese Aufgaben regelmäßig und pünktlich zu erledigen. Auf diese Weise können wir den Vermittlungsprozess so effizient wie möglich gestalten. Wir schätzen Ihre Unterstützung in dieser Angelegenheit.", "The specified tasks in the JobSoft application are available and have already been sent to your employees twice. We kindly remind you that it is important to regularly and timely perform these tasks. This way, we can facilitate the mediation process as much as possible. We appreciate your support."),
				"Na sljedećoj listi se nalazi više informacija." => array("Na narednoj listi se nalazi više informacija.", "Die folgende Liste enthält weitere Informationen.", "More information is available in the following list."), 
				"RB" => array("RB", "Nr.", "№"),
				"Tip" => array("Tip", "Typ", "Type"),
				"Level" => array("Level", "Level", "Level"),
				"Dodijeljeni korisnici" => array("Dodijeljeni korisnici", "Zugewiesene Benutzer", "Assigned users"), 
			/*
			Prijevodi za cron_reminders_email.php END 
		*/

	);
?>