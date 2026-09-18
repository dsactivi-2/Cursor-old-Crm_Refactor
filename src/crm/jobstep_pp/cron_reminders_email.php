<?php 
    exit(); // File ugašen povodom taska: https://app.clickup.com/t/24391024/CRM-3152
    include("includes/connect.php");
    require 'mail/Exception.php';
	require 'mail/PHPMailer.php';
	require 'mail/SMTP.php';
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

    /*
        Functions START
        */
            function getRemindersName($userLanguage) {
                include("includes/language/language.php");
                $result = array(
                    "1" => $txtArray["Djelimično ocijenjeni"][$userLanguage],
                    "2" => $txtArray["Neprihvaceni/neodbijeni"][$userLanguage], 
                    "3" => $txtArray["Bez partnera"][$userLanguage], 
                    "4" => $txtArray["Bez detalja o ugovoru"][$userLanguage], 
                    "5" => $txtArray["Posalji ugovor postom"][$userLanguage], 
                    "8" => $txtArray["Preuzmi nostrifikovanu diplomu (i zatrazi qualiplan)"][$userLanguage], 
                    "9" => $txtArray["Uploaduj qualiplan"][$userLanguage], 
                    "11" => $txtArray["Upload dokumente A"][$userLanguage], 
                    "13" => $txtArray["Upload ispravljenih dokumenata"][$userLanguage], 
                    "14" => $txtArray["Posalji dokumente"][$userLanguage], 
                    "21" => $txtArray["Upload dokumente dopune/odbijenice A"][$userLanguage],  
                    "23" => $txtArray["Upload ispravke dokumenata dopune/odbijenice"][$userLanguage], 
                    "24" => $txtArray["Posalji dokumente dopune/odbijenice"][$userLanguage],
                );
                unset($txtArray); 
                unset($userLanguage); 

                return $result;
            }

            function getOtherTranslations($userLanguage) {
                include("includes/language/language.php");

                $result = array(
                    "Sva prava pridržana - Jobstep IT Solutions" => $txtArray["Sva prava pridržana - Jobstep IT Solutions"][$userLanguage],
                    "Lista zadataka" => $txtArray["Lista zadataka"][$userLanguage],
                    "Hitno" => $txtArray["Hitno"][$userLanguage], 
                    "Poštovani" => $txtArray["Poštovani"][$userLanguage], 
                    "Poštovana" => $txtArray["Poštovana"][$userLanguage], 
                    "Mail body text za izvršnog usera" => $txtArray["Mail body text za izvršnog usera"][$userLanguage], 
                    "Mail body text za controlling usera" => $txtArray["Mail body text za controlling usera"][$userLanguage], 
                    "Na sljedećoj listi se nalazi više informacija." => $txtArray["Na sljedećoj listi se nalazi više informacija."][$userLanguage], 
                    "RB" => $txtArray["RB"][$userLanguage], 
                    "Kandidat" => $txtArray["Kandidat"][$userLanguage],
                    "Tip" => $txtArray["Tip"][$userLanguage], 
                    "Level" => $txtArray["Level"][$userLanguage], 
                    "Datum" => $txtArray["Datum"][$userLanguage],
                    "Dodijeljeni korisnici" => $txtArray["Dodijeljeni korisnici"][$userLanguage], 
                ); 
                unset($txtArray); 
                unset($userLanguage); 

                return $result;
            }

            function getMailBodyMessage($userLastName, $userGender, $type, $otherTranslate) {
                $result = '
                    <!-- Email message START -->
                    <table class="s-6 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                      <tbody>
                        <tr>
                          <td style="line-height: 24px; font-size: 24px; width: 100%; height: 24px; margin: 0;" align="left" width="100%" height="24">
                            &#160;
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="row" style="margin-right: -24px;">
                    <table class="" role="presentation" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; width: 100%;" width="100%">
                        <tbody>
                        <tr>
                            <td class="col-12" style="line-height: 24px; font-size: 16px; min-height: 1px; font-weight: normal; padding-right: 24px; width: 100%; margin: 0;" align="left" valign="top">
                            <div class="space-y-2">
                                <p class="text-gray-700" style="line-height: 24px; font-size: 16px; color: #4a5568; width: 100%; margin: 0;" align="left">
                                '.(($userGender != null) ? ( ($userGender == 'Female' OR $userGender == 'female') ? $otherTranslate["Poštovana"] : $otherTranslate["Poštovani"] ) : '').' '.$userLastName.',
                                </p>
                                <table class="s-2 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                <tbody>
                                    <tr>
                                    <td style="line-height: 8px; font-size: 8px; width: 100%; height: 8px; margin: 0;" align="left" width="100%" height="8">
                                        &#160;
                                    </td>
                                    </tr>
                                </tbody>
                                </table>
                                <p class="text-gray-700" style="line-height: 24px; font-size: 16px; color: #4a5568; width: 100%; margin: 0;" align="left">
                                '.(($type == 1) ? $otherTranslate["Mail body text za izvršnog usera"] : $otherTranslate["Mail body text za controlling usera"]).'
                                </p>
                                <table class="s-2 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                <tbody>
                                    <tr>
                                    <td style="line-height: 8px; font-size: 8px; width: 100%; height: 8px; margin: 0;" align="left" width="100%" height="8">
                                        &#160;
                                    </td>
                                    </tr>
                                </tbody>
                                </table>
                                <p class="text-gray-700" style="line-height: 24px; font-size: 16px; color: #4a5568; width: 100%; margin: 0;" align="left">
                                '.$otherTranslate["Na sljedećoj listi se nalazi više informacija."].'
                                </p>
                            </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                    <table class="s-6 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                      <tbody>
                        <tr>
                          <td style="line-height: 24px; font-size: 24px; width: 100%; height: 24px; margin: 0;" align="left" width="100%" height="24">
                            &#160;
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <!-- Email message END -->
                ';

                unset($userLastName);
                unset($userGender);
                unset($type);
                unset($otherTranslate);

                return $result; 
            }

            function getMailBodyFooter($otherTranslate) {
                $result = '
                    <!-- Email footer START -->
                    <div class="row" style="margin-right: -24px;">
                    <table class="" role="presentation" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; width: 100%;" width="100%">
                        <tbody>
                        <tr>
                            <td class="col-12 ax-center" align="center" style="line-height: 24px; font-size: 16px; min-height: 1px; font-weight: normal; padding-right: 24px; width: 100%; margin: 0;" valign="top">
                            <img class="max-w-16" src="https://job-step.net/images/jobstep/logo.png" alt="Some Image" style="height: auto; line-height: 100%; outline: none; text-decoration: none; display: block; max-width: 64px; width: 100%; border-style: none; border-width: 0;" width="64">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                    <table class="ax-center" role="presentation" align="center" border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
                    <tbody>
                        <tr>
                        <td style="line-height: 24px; font-size: 16px; margin: 0;" align="left">
                            <div class="row" style="margin-right: -24px;">
                            <table class="" role="presentation" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; width: 100%;" width="100%">
                                <tbody>
                                <tr>
                                    <td class="col-12" style="line-height: 24px; font-size: 16px; min-height: 1px; font-weight: normal; padding-right: 24px; width: 100%; margin: 0;" align="left" valign="top">
                                    <p class="text-gray-700 text-center" style="line-height: 24px; font-size: 16px; color: #4a5568; width: 100%; margin: 0;" align="center">
                                        &#169;'.date("Y").' '.$otherTranslate["Sva prava pridržana - Jobstep IT Solutions"].'
                                    </p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            </div>
                        </td>
                        </tr>
                    </tbody>
                    </table>
                    <table class="s-6 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                    <tbody>
                        <tr>
                        <td style="line-height: 24px; font-size: 24px; width: 100%; height: 24px; margin: 0;" align="left" width="100%" height="24">
                            &#160;
                        </td>
                        </tr>
                    </tbody>
                    </table>
                    <!-- Email footer END -->
                ';
                unset($otherTranslate);
                return $result; 
            }

            function getMailBodyLevelBadge($level) {
                $result = '';
                switch($level) {
                    case 'Level 5+':
                        $result = '
                            <!-- Level 5+ -->
                            <table class="badge bg-danger text-white" align="left" role="presentation" border="0" cellpadding="0" cellspacing="0" style="color: #ffffff;" bgcolor="#dc3545">
                                <tbody>
                                <tr>
                                    <td style="line-height: 1; font-size: 75%; display: inline-block; font-weight: 700; white-space: nowrap; border-radius: 4px; color: #ffffff; margin: 0; padding: 4px 6.4px;" align="center" bgcolor="#dc3545" valign="baseline">
                                    <span>Level 5+</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <!-- Level 5+ -->
                        ';
                    break;

                    case 'Level 4':
                        $result = '
                            <!-- Level 4 -->
                            <table class="badge bg-warning text-dark" align="left" role="presentation" border="0" cellpadding="0" cellspacing="0" style="color: #1a202c;" bgcolor="#ffc107">
                                <tbody>
                                <tr>
                                    <td style="line-height: 1; font-size: 75%; display: inline-block; font-weight: 700; white-space: nowrap; border-radius: 4px; color: #1a202c; margin: 0; padding: 4px 6.4px;" align="center" bgcolor="#ffc107" valign="baseline">
                                    <span>Level 4</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <!-- Level 4 -->
                        ';
                    break;

                    case 'Level 3':
                        $result = '
                            <!-- Level 3 -->
                            <table class="badge bg-primary text-white" align="left" role="presentation" border="0" cellpadding="0" cellspacing="0" style="color: #ffffff;" bgcolor="#0d6efd">
                                <tbody>
                                <tr>
                                    <td style="line-height: 1; font-size: 75%; display: inline-block; font-weight: 700; white-space: nowrap; border-radius: 4px; color: #ffffff; margin: 0; padding: 4px 6.4px;" align="center" bgcolor="#0d6efd" valign="baseline">
                                    <span>Level 3</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <!-- Level 3 -->
                        ';
                    break;

                    case 'Level 2':
                        $result = '
                            <!-- Level 2 -->
                            <table class="badge bg-info text-dark" align="left" role="presentation" border="0" cellpadding="0" cellspacing="0" style="color: #1a202c;" bgcolor="#0dcaf0">
                                <tbody>
                                <tr>
                                    <td style="line-height: 1; font-size: 75%; display: inline-block; font-weight: 700; white-space: nowrap; border-radius: 4px; color: #1a202c; margin: 0; padding: 4px 6.4px;" align="center" bgcolor="#0dcaf0" valign="baseline">
                                    <span>Level 2</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <!-- Level 2 -->
                        ';
                    break;

                    case 'Level 1':
                        $result = '
                            <!-- Level 1 -->
                            <table class="badge bg-light text-dark" align="left" role="presentation" border="0" cellpadding="0" cellspacing="0" style="color: #1a202c;" bgcolor="#f7fafc">
                                <tbody>
                                <tr>
                                    <td style="line-height: 1; font-size: 75%; display: inline-block; font-weight: 700; white-space: nowrap; border-radius: 4px; color: #1a202c; margin: 0; padding: 4px 6.4px;" align="center" bgcolor="#f7fafc" valign="baseline">
                                    <span>Level 1</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <!-- Level 1 -->
                        ';
                    break;
                }
                unset($level);
                return $result;
            }

            function getMailBodyTable($remindersData, $remindersName, $type, $otherTranslate) {

                $rows = '';
                $cnt = 0;

                foreach($remindersData AS $reminder) {
                    $cnt = $cnt + 1;
                    $row = '
                        <!-- One Row -->
                        <tr>
                            <td style="line-height: 24px; font-size: 16px; color: #4a5568; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$cnt.'</td>
                            <td style="line-height: 24px; font-size: 16px; color: #4a5568; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">
                                <table class="btn btn-light" role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-radius: 6px; border-collapse: separate !important;">
                                    <tbody>
                                        <tr>
                                            <td style="line-height: 24px; font-size: 16px; border-radius: 6px; margin: 0;" align="center" bgcolor="#f7fafc">
                                                <a target="_blank" href="https://js.job-step.com/profile?kandidat_id=' . $reminder["candidateKey"] . '&n=' . $reminder["reminderOrder"] . '" style="color: #111111; font-size: 16px; font-family: Helvetica, Arial, sans-serif; text-decoration: none; border-radius: 6px; line-height: 20px; display: block; font-weight: normal; white-space: nowrap; background-color: #f7fafc; padding: 8px 12px; border: 1px solid #f7fafc;">'.$reminder["candidatInfo"].'</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td style="line-height: 24px; font-size: 16px; color: #4a5568; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$remindersName[$reminder['reminderType']].'</td>
                            <td style="line-height: 24px; font-size: 16px; color: #4a5568; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">
                            '.getMailBodyLevelBadge($reminder["reminderLevel"]).'
                            </td>
                            <td style="line-height: 24px; font-size: 16px; color: #4a5568; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$reminder["reminderDateSent"].'</td>
                            '.(($type == 2) ? '<td style="line-height: 24px; font-size: 16px; color: #4a5568; margin: 0; padding: 12px; border: 1px solid #e2e8f0;" align="center" valign="top">'.$reminder["reminderUsersInfo"].'</td>' : '').'
                        </tr>
                        <!-- One Row -->
                    ';
                    $rows = $rows . '
                     
                    ' . $row;
                    unset($reminder);
                    unset($row);  
                }

                unset($cnt);
                unset($remindersData); 
                unset($remindersName);

                $result = '
                    <!-- Email table START -->
                    <div class="row" style="margin-right: -24px;">
                    <table class="" role="presentation" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed; width: 100%;" width="100%">
                        <tbody>
                        <tr>
                            <td class="col-12" style="line-height: 24px; font-size: 16px; min-height: 1px; font-weight: normal; padding-right: 24px; width: 100%; margin: 0;" align="left" valign="top">
                            <table class="table thead-default table-bordered text-center text-gray-700" border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 100%; color: #4a5568; text-align: center !important; border: 1px solid #e2e8f0;">
                                <thead>
                                <!-- Table head -->
                                <tr>
                                    <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">'.$otherTranslate["RB"].'</th>
                                    <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">'.$otherTranslate["Kandidat"].'</th>
                                    <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">'.$otherTranslate["Tip"].'</th>
                                    <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">'.$otherTranslate["Level"].'</th>
                                    <th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">'.$otherTranslate["Datum"].'</th>
                                    '.(($type == 2) ? '<th style="line-height: 24px; font-size: 16px; margin: 0; padding: 12px; border-color: #e2e8f0; border-style: solid; border-width: 1px 1px 2px;" align="left" valign="top">'.$otherTranslate["Dodijeljeni korisnici"].'</th>' : '').'
                                </tr>
                                <!-- Table head -->
                                </thead>
                                <tbody>
                                '.$rows.'
                                </tbody>
                            </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                    <table class="s-6 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                    <tbody>
                        <tr>
                        <td style="line-height: 24px; font-size: 24px; width: 100%; height: 24px; margin: 0;" align="left" width="100%" height="24">
                            &#160;
                        </td>
                        </tr>
                    </tbody>
                    </table>
                    <!-- Email table END -->
                ';
                unset($type); 
                unset($rows);
                unset($otherTranslate); 

                return $result; 
            }

            function getMailBodyFull($userData, $type) {
                $otherTranslate = getOtherTranslations($userData["userLanguage"]);
                $remindersName = getRemindersName($userData["userLanguage"]);
                $result = '
                    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
                    <html>
                    <head>
                        <!-- Compiled with Bootstrap Email version: 1.4.0 --><meta http-equiv="x-ua-compatible" content="ie=edge">
                        <meta name="x-apple-disable-message-reformatting">
                        <meta name="viewport" content="width=device-width, initial-scale=1">
                        <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                        <style type="text/css">
                        body,table,td{font-family:Helvetica,Arial,sans-serif !important}.ExternalClass{width:100%}.ExternalClass,.ExternalClass p,.ExternalClass span,.ExternalClass font,.ExternalClass td,.ExternalClass div{line-height:150%}a{text-decoration:none}*{color:inherit}a[x-apple-data-detectors],u+#body a,#MessageViewBody a{color:inherit;text-decoration:none;font-size:inherit;font-family:inherit;font-weight:inherit;line-height:inherit}img{-ms-interpolation-mode:bicubic}table:not([class^=s-]){font-family:Helvetica,Arial,sans-serif;mso-table-lspace:0pt;mso-table-rspace:0pt;border-spacing:0px;border-collapse:collapse}table:not([class^=s-]) td{border-spacing:0px;border-collapse:collapse}@media screen and (max-width: 600px){.max-w-32,.max-w-32>tbody>tr>td{max-width:128px !important;width:100% !important}.w-full,.w-full>tbody>tr>td{width:100% !important}*[class*=s-lg-]>tbody>tr>td{font-size:0 !important;line-height:0 !important;height:0 !important}.s-2>tbody>tr>td{font-size:8px !important;line-height:8px !important;height:8px !important}.s-5>tbody>tr>td{font-size:20px !important;line-height:20px !important;height:20px !important}.s-6>tbody>tr>td{font-size:24px !important;line-height:24px !important;height:24px !important}}
                        </style>
                    </head>
                    <body class="bg-white" style="outline: 0; width: 100%; min-width: 100%; height: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 24px; font-weight: normal; font-size: 16px; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: #000000; margin: 0; padding: 0; border-width: 0;" bgcolor="#ffffff">
                        <table class="bg-white body" valign="top" role="presentation" border="0" cellpadding="0" cellspacing="0" style="outline: 0; width: 100%; min-width: 100%; height: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 24px; font-weight: normal; font-size: 16px; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: #000000; margin: 0; padding: 0; border-width: 0;" bgcolor="#ffffff">
                        <tbody>
                            <tr>
                            <td valign="top" style="line-height: 24px; font-size: 16px; margin: 0;" align="left" bgcolor="#ffffff">
                                <table class="container-fluid" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                                <tbody>
                                    <tr>
                                    <td style="line-height: 24px; font-size: 16px; width: 100%; margin: 0; padding: 0 16px;" align="left">
                                        
                                        '.getMailBodyMessage($userData["userLastName"], $userData["userGender"], $type, $otherTranslate).'
                                        
                                        '.getMailBodyTable($userData['data'], $remindersName, $type, $otherTranslate).'
                                    
                                        <table class="s-5 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                        <tbody>
                                            <tr>
                                            <td style="line-height: 20px; font-size: 20px; width: 100%; height: 20px; margin: 0;" align="left" width="100%" height="20">
                                                &#160;
                                            </td>
                                            </tr>
                                        </tbody>
                                        </table>
                                        <table class="hr" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                                        <tbody>
                                            <tr>
                                            <td style="line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e2e8f0; border-top-style: solid; height: 1px; width: 100%; margin: 0;" align="left">
                                            </td>
                                            </tr>
                                        </tbody>
                                        </table>
                                        <table class="s-5 w-full" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;" width="100%">
                                        <tbody>
                                            <tr>
                                            <td style="line-height: 20px; font-size: 20px; width: 100%; height: 20px; margin: 0;" align="left" width="100%" height="20">
                                                &#160;
                                            </td>
                                            </tr>
                                        </tbody>
                                        </table>
                                        
                                        '.getMailBodyFooter($otherTranslate).'
                                        
                                    </td>
                                    </tr>
                                </tbody>
                                </table>
                            </td>
                            </tr>
                        </tbody>
                        </table>
                    </body>
                    </html>
                ';
                unset($userData); 
                unset($type);
                unset($otherTranslate);
                unset($remindersName); 
                return $result;
            }

            function getAltMailBodyFull($userData, $type) {
                $otherTranslate = getOtherTranslations($userData["userLanguage"]);
                $remindersName = getRemindersName($userData["userLanguage"]);
                
                $body_message = '
                    <br><br>
                    '.(($userData["userGender"] != null) ? ( ($userData["userGender"] == 'Female') ? $otherTranslate["Poštovana"] : $otherTranslate["Poštovani"] ) : '').' '.$userData["userLastName"].',
                    <br>
                    '.(($type == 1) ? $otherTranslate["Mail body text za izvršnog usera"] : $otherTranslate["Mail body text za controlling usera"]).'
                    <br>
                    '.$otherTranslate["Na sljedećoj listi se nalazi više informacija."].'
                    <br>
                ';

                $rows = '';
                $cnt = 0;
                foreach($userData['data'] AS $reminder) {
                    $cnt = $cnt + 1;
                    $row = '
                        '.$cnt.'&emsp;&emsp;
                        <a target="_blank" href="https://js.job-step.com/profile?kandidat_id=' . $reminder["candidateKey"] . '&n=' . $reminder["reminderOrder"] . '">'.$reminder["candidatInfo"].'</a>&emsp;&emsp;
                        '.$remindersName[$reminder['reminderType']].'&emsp;&emsp;
                        '.$reminder["reminderLevel"].'&emsp;&emsp;
                        '.$reminder["reminderDateSent"].'&emsp;&emsp;
                        '.(($type == 2) ? ''.$reminder["reminderUsersInfo"].'' : '').'
                    ';
                    $rows = $rows . '
                    <br>
                    ' . $row;
                    unset($row); 
                    unset($reminder); 
                }

                unset($userData);
                unset($cnt);  

                $body_table = '
                    <br>
                    '.$otherTranslate["RB"].'&emsp;&emsp;
                    '.$otherTranslate["Kandidat"].'&emsp;&emsp;
                    '.$otherTranslate["Tip"].'&emsp;&emsp;
                    '.$otherTranslate["Level"].'&emsp;&emsp;
                    '.$otherTranslate["Datum"].'&emsp;&emsp;
                    '.(($type == 2) ? ''.$otherTranslate["Dodijeljeni korisnici"].'' : '').'
                    <br>
                    '.$rows.'
                    <br>
                ';

                unset($type); 
                unset($rows); 
                unset($cnt); 

                $body_footer = '
                    <br>
                    &#169;'.date("Y").' '.$otherTranslate["Sva prava pridržana - Jobstep IT Solutions"].'
                    <br><br>
                ';

                $result = '
                    '.$body_message.'
                    '.$body_table.'
                    '.$body_footer.'
                ';

                unset($body_message); 
                unset($body_table); 
                unset($body_footer); 
                unset($otherTranslate);
                unset($remindersName);

                return $result; 
            }
            
            function sendRemindersEmail($userEmail, $userLanguage, $type, $bodyEmail, $altBodyEmail) {
                $otherTranslate = getOtherTranslations($userLanguage);

                $result = array(
                    "status" => "default", 
                    "message" => ""
                );

                /*
                    SETUP START 
                    */
                        $hostName 	= 'smtp.gmail.com';
                        $userName 	= 'support@job-step.com';
                        $password 	= 'eooc nnxo aylp lqkh'; 
                        $setFrom 	= 'support@job-step.com';
                        
                    /*
                    SETUP END
                */

                $mail = new PHPMailer;
                $mail->isSMTP();
                $mail->Host 		= $hostName;
                $mail->SMTPAuth 	= true;
                $mail->Username 	= $userName;
                $mail->Password 	= $password;
                $mail->SMTPSecure 	= 'ssl';
                $mail->Port 		= 465;
                $mail->CharSet 		= 'UTF-8';
                
                $mail->setFrom($setFrom , 'JobStep');
                
                $mail->addAddress($userEmail);
                $mail->addBcc("e.bender@job-step.com");

                $mail->isHTML(true);
                $mail->Subject 		= $otherTranslate["Lista zadataka"].' '. date("Y-m-d") . ' ' . (($type == 2) ? $otherTranslate["Hitno"] : ''); 
                $mail->Body 		= $bodyEmail;
                $mail->AltBody 		= $altBodyEmail;
                
                if(!$mail->send()) {
                    $result["status"] = "Error"; 
                    $result["message"] = "".$mail->ErrorInfo."";
                } else {
                    $result["status"] = "Okay"; 
                    $result["message"] = "Email has been sent";
                }
                
                unset($userEmail); 
                unset($userLanguage); 
                unset($type); 
                unset($bodyEmail); 
                unset($altBodyEmail); 
                unset($otherTranslate);
                
                return $result;
            }

            function insertLog($logDesc) {
                Global $db; 
                $query = $db->prepare("
                    INSERT INTO idk_logs
                        (log_employeeid, log_desc, log_date, log_type)
                    VALUES
                        (:log_employeeid, :log_desc, :log_date, :log_type)"
                );
                $query->execute(array(
                    ':log_employeeid' => 0,
                    ':log_desc' => $logDesc,
                    ':log_date' => date('Y-m-d H:i:s'),
                    ':log_type' => 10
                ));
            }

        /*
        Functions START
    */
    
    $key        = $_GET["key"];
    $key_check  = md5("pokretanje crona za slanje emaila");
    $type       = $_GET["type"];  
    
    $desc_send_okay         = ""; 
    $desc_send_not_okay     = ""; 

    $time_start = microtime(true);

    if ($key == $key_check AND in_array($type, array(1,2))) {
        /*
            Correct access parameters  START
            */
                switch($type) {
                    case 1:

                        $query_data = $db ->prepare("
                            SELECT 
                                pu.pu_id AS userId, 
                                CONCAT(pu.pu_fname, ' ', pu.pu_lname) AS userInfo,
                                pu.pu_fname AS userFirstName,
                                pu.pu_lname AS userLastName,
                                pu.pu_email AS userEmail, 
                                pu.pu_language AS userLanguage, 
                                pu.pu_gender AS userGender, 
                                pua.pua_id AS userAccessId, 
                                pua.pua_type AS userAccessType, 
                                pua.pua_partner_id AS userAccessPartnerId, 
                                pua.pua_nalog_id AS userAccessOrderId, 
                                allRemindersInfo.reminderType, 
                                allRemindersInfo.reminderUserType, 
                                allRemindersInfo.reminderSetting,
                                allRemindersInfo.reminderDocumentSetting,
                                allRemindersInfo.reminderId,
                                allRemindersInfo.reminderLevel,
                                allRemindersInfo.reminderDateSent, 
                                allRemindersInfo.reminderUsers, 
                                allRemindersInfo.candidatInfo, 
                                allRemindersInfo.reminderOrder, 
                                allRemindersInfo.candidateKey
                                /*
                                    allRemindersInfo.reminderOrderInfo, 
                                    allRemindersInfo.reminderPartnerInfo
                                */
                            FROM 
                                idk_pp_users pu 
                            JOIN 
                                idk_pp_user_access pua 
                            ON 
                                pu.pu_id = pua.pua_user_id
                                AND 
                                pu.pu_status = 1
                                AND 
                                pua.pua_status = 1
                            JOIN 
                                (
                                    /*
                                        Query koji uzima sve remindere koji imaju aktivnu postavku i aktivan reminder
                                        */
                            
                                            SELECT 
                                                prt1.prt_id 			AS reminderType,
                                                prt1.prt_user_type 		AS reminderUserType,
                                                prs1.prs_id 			AS reminderSetting, 
                                                null 					AS reminderDocumentSetting, 
                                                pr1.pr_id 				AS reminderId,
                                                (
                                                    CASE
                                                        WHEN pr1.pr_level = 1 THEN 'Level 1'
                                                        WHEN pr1.pr_level = 2 THEN 'Level 2'
                                                        WHEN pr1.pr_level = 3 THEN 'Level 3'
                                                        WHEN pr1.pr_level = 4 THEN 'Level 4'
                                                        ELSE 'Level 5+'
                                                    END
                                                )                       AS reminderLevel,
                                                pr1.pr_level            AS reminderLevelValue,
                                                CAST(pr1.pr_date_sent AS date) AS reminderDateSent,
                                                prs1.prs_pua_ids 		AS reminderUsers,
                                                prs1.prs_nalog_id       AS reminderOrder, 
                                                CONCAT(kan1.kandidat_ime, ' ', kan1.kandidat_prezime) AS candidatInfo, 
                                                kan1.kandidat_check AS candidateKey
                                                /*
                                                    CONCAT(nal1.nalog_naziv, ' - ', com1.company_name) AS reminderOrderInfo,
                                                    parcom1.company_name AS reminderPartnerInfo
                                                */
                                            FROM 
                                                idk_pp_reminder_types prt1
                                            JOIN 
                                                idk_pp_reminder_settings prs1
                                            ON 
                                                prt1.prt_id = prs1.prs_reminder_type_id
                                                AND 
                                                prt1.prt_user_type IN (1,2)
                                                AND 
                                                prt1.prt_has_documents = 0
                                                AND 
                                                prs1.prs_active = 1	
                                            JOIN 
                                                idk_pp_reminders pr1
                                            ON 
                                                prs1.prs_id = pr1.pr_reminder_setting_id
                                                AND 
                                                pr1.pr_reminder_document_id is null 
                                                /*AND 
                                                CAST(pr1.pr_date_sent AS date) = CURRENT_DATE()*/
                                                AND 
                                                pr1.pr_status IN (1,2)
                                            JOIN 
                                                idk_kandidati kan1
                                            ON 
                                                pr1.pr_candidate_id = kan1.kandidat_id
                                            /*
                                                JOIN 
                                                    idk_nalozi nal1
                                                ON 
                                                    nal1.nalog_id = prs1.prs_nalog_id
                                                JOIN 
                                                    idk_companies com1
                                                ON 
                                                    com1.company_id = nal1.kompanija_id
                                                LEFT JOIN 
                                                    idk_pp_partners par1
                                                ON 
                                                    par1.ppa_id = prs1.prs_partner_id
                                                LEFT JOIN 
                                                    idk_companies parcom1
                                                ON 
                                                    parcom1.company_id = par1.ppa_company_id
                                            */
                            
                                            UNION 
                            
                                            SELECT 
                                                prt2.prt_id 			AS reminderType,
                                                prt2.prt_user_type 		AS reminderUserType,
                                                prs2.prs_id 			AS reminderSetting,
                                                prd2.prd_id 			AS reminderDocumentSetting,
                                                pr2.pr_id 				AS reminderId,
                                                (
                                                    CASE
                                                        WHEN pr2.pr_level = 1 THEN 'Level 1'
                                                        WHEN pr2.pr_level = 2 THEN 'Level 2'
                                                        WHEN pr2.pr_level = 3 THEN 'Level 3'
                                                        WHEN pr2.pr_level = 4 THEN 'Level 4'
                                                        ELSE 'Level 5+'
                                                    END
                                                )                       AS reminderLevel,
                                                pr2.pr_level            AS reminderLevelValue,
                                                CAST(pr2.pr_date_sent AS date) AS reminderDateSent,
                                                prs2.prs_pua_ids 		AS reminderUsers, 
                                                prs2.prs_nalog_id       AS reminderOrder,
                                                CONCAT(kan2.kandidat_ime, ' ', kan2.kandidat_prezime) AS candidatInfo,
                                                kan2.kandidat_check AS candidateKey 
                                                /*
                                                    CONCAT(nal2.nalog_naziv, ' - ', com2.company_name) AS reminderOrderInfo,
                                                    parcom2.company_name AS reminderPartnerInfo
                                                */
                                            FROM 
                                                idk_pp_reminder_types prt2
                                            JOIN 
                                                idk_pp_reminder_settings prs2
                                            ON 
                                                prt2.prt_id = prs2.prs_reminder_type_id
                                                AND 
                                                prt2.prt_user_type IN (1,2)
                                                AND 
                                                prt2.prt_has_documents = 1
                                                AND 
                                                prs2.prs_active = 1	
                                            JOIN 
                                                idk_pp_reminder_documents prd2
                                            ON 
                                                prs2.prs_id = prd2.prd_prs_id
                                                AND 
                                                prd2.prd_nrd_id is not null 
                                                AND 
                                                prd2.prd_crd_id is null
                                                AND 
                                                prd2.prd_active = 1
                                            JOIN 
                                                idk_pp_reminders pr2
                                            ON 
                                                prd2.prd_id = pr2.pr_reminder_document_id
                                                AND 
                                                pr2.pr_reminder_setting_id is null 
                                                /*AND 
                                                CAST(pr2.pr_date_sent AS date) = CURRENT_DATE()*/
                                                AND 
                                                pr2.pr_status IN (1,2)
                                            JOIN 
                                                idk_kandidati kan2
                                            ON 
                                                pr2.pr_candidate_id = kan2.kandidat_id
                                            /*
                                                JOIN 
                                                    idk_nalozi nal2
                                                ON 
                                                    nal2.nalog_id = prs2.prs_nalog_id
                                                JOIN 
                                                    idk_companies com2
                                                ON 
                                                    com2.company_id = nal2.kompanija_id
                                                LEFT JOIN 
                                                    idk_pp_partners par2
                                                ON 
                                                    par2.ppa_id = prs2.prs_partner_id
                                                LEFT JOIN 
                                                    idk_companies parcom2
                                                ON 
                                                    parcom2.company_id = par2.ppa_company_id
                                            */

                                            ORDER BY 
                                                reminderType, 
                                                reminderSetting, 
                                                reminderDocumentSetting
                                            ASC
                            
                                        /*
                                        Query koji uzima sve remindere koji imaju aktivnu postavku i aktivan reminder
                                    */
                                )
                                AS allRemindersInfo
                            ON
                                FIND_IN_SET(pua.pua_id, allRemindersInfo.reminderUsers) > 0
                                
                            ORDER BY 
                                pu.pu_id,
                                allRemindersInfo.reminderLevelValue
                            DESC
                        ");
                        $query_data->execute();

                        if ($query_data->rowCount() > 0) { 
                            $rows_data = $query_data->fetchAll(PDO::FETCH_ASSOC);
                            $grouped_data = array();
                            foreach ( $rows_data AS $row_data ) {
                                if ( isset( $grouped_data[$row_data['userId']] ) ) {
                                    $grouped_data[$row_data['userId']]['data'][] = array(
                                        'userAccessId'              => $row_data['userAccessId'],
                                        'userAccessType'            => $row_data['userAccessType'],
                                        'userAccessPartnerId'       => $row_data['userAccessPartnerId'],
                                        'userAccessOrderId'         => $row_data['userAccessOrderId'],
                                        'reminderType'              => $row_data['reminderType'],
                                        'reminderUserType'          => $row_data['reminderUserType'],
                                        'reminderSetting'           => $row_data['reminderSetting'],
                                        'reminderDocumentSetting'   => $row_data['reminderDocumentSetting'],
                                        'reminderId'                => $row_data['reminderId'],
                                        'reminderLevel'             => $row_data['reminderLevel'],
                                        'candidatInfo'              => $row_data['candidatInfo'],
                                        'candidateKey'              => $row_data['candidateKey'],
                                        'reminderDateSent'          => $row_data['reminderDateSent'],
                                        'reminderUsers'             => $row_data['reminderUsers'], 
                                        'reminderOrder'             => $row_data['reminderOrder']
                                    );
                                } else {
                                    $grouped_data[$row_data['userId']] = array(
                                        'userId'            => $row_data['userId'],
                                        'userInfo'          => $row_data['userInfo'],
                                        'userFirstName'     => $row_data['userFirstName'],
                                        'userLastName'      => $row_data['userLastName'],
                                        'userEmail'         => $row_data['userEmail'],
                                        'userLanguage'      => $row_data['userLanguage'],
                                        'userGender'        => $row_data['userGender'],
                                        'data' => array(
                                            array(
                                                'userAccessId'              => $row_data['userAccessId'],
                                                'userAccessType'            => $row_data['userAccessType'],
                                                'userAccessPartnerId'       => $row_data['userAccessPartnerId'],
                                                'userAccessOrderId'         => $row_data['userAccessOrderId'],
                                                'reminderType'              => $row_data['reminderType'],
                                                'reminderUserType'          => $row_data['reminderUserType'],
                                                'reminderSetting'           => $row_data['reminderSetting'],
                                                'reminderDocumentSetting'   => $row_data['reminderDocumentSetting'],
                                                'reminderId'                => $row_data['reminderId'],
                                                'reminderLevel'             => $row_data['reminderLevel'],
                                                'candidatInfo'              => $row_data['candidatInfo'],
                                                'candidateKey'              => $row_data['candidateKey'],
                                                'reminderDateSent'          => $row_data['reminderDateSent'],
                                                'reminderUsers'             => $row_data['reminderUsers'], 
                                                'reminderOrder'             => $row_data['reminderOrder']
                                            )
                                        )
                                    );
                                }
                            }
                            
                            /*
                                print("<pre>".print_r($grouped_data,true)."</pre>");
                                exit();
                            */
                            $send_emails_okay = array();
                            $send_emails_not_okay = array();
                            foreach ($grouped_data as $userData) {
                                $bodyEmail      = getMailBodyFull($userData, $type);
                                $altBodyEmail   = getAltMailBodyFull($userData, $type);
                                $result_send_email = sendRemindersEmail($userData["userEmail"], $userData["userLanguage"], $type, $bodyEmail, $altBodyEmail);
                                if ($result_send_email["status"] != 'default') {
                                    if ($result_send_email["status"] == 'Okay') {
                                        array_push($send_emails_okay, $userData["userEmail"]);
                                    } else {
                                        array_push($send_emails_not_okay, '{Email: '.$userData["userEmail"].', Message: '.$result_send_email["message"].'}');
                                    }
                                }
                                unset($result_send_email);
                                unset($bodyEmail); 
                                unset($altBodyEmail); 
                                unset($userData);
                            }

                            unset($grouped_data); 

                            if(count($send_emails_okay) > 0){
                                $desc_send_okay = "Cron reminders email -> Poslan email userima: >>".implode(", ", $send_emails_okay)."<<. ";
                            }
                            if(count($send_emails_not_okay) > 0){
                                $desc_send_not_okay = "Cron reminders email -> Nije poslan email userima: >>".implode(", ", $send_emails_not_okay)."<<. "; 
                            }
                            unset($send_emails_okay);
                            unset($send_emails_not_okay); 

                        } else {
                            echo "<h5>No results found!<br>"; 
                        }

                    break;

                    case 2:

                        $query_data = $db ->prepare("
                            SELECT 
                                pu.pu_id AS userId, 
                                CONCAT(pu.pu_fname, ' ', pu.pu_lname) AS userInfo,
                                pu.pu_fname AS userFirstName,
                                pu.pu_lname AS userLastName, 
                                pu.pu_email AS userEmail, 
                                pu.pu_language AS userLanguage,
                                pu.pu_gender AS userGender,  
                                pua.pua_id AS userAccessId, 
                                pua.pua_type AS userAccessType, 
                                pua.pua_partner_id AS userAccessPartnerId, 
                                pua.pua_nalog_id AS userAccessOrderId, 
                                allRemindersInfo.reminderType, 
                                allRemindersInfo.reminderUserType, 
                                allRemindersInfo.reminderSetting,
                                allRemindersInfo.reminderDocumentSetting,
                                allRemindersInfo.reminderId,
                                allRemindersInfo.reminderLevel,
                                allRemindersInfo.reminderDateSent, 
                                allRemindersInfo.reminderUsersInfo,
                                allRemindersInfo.reminderUsers, 
                                allRemindersInfo.reminderOrder, 
                                allRemindersInfo.candidatInfo,
                                allRemindersInfo.candidateKey
                                /*
                                    allRemindersInfo.reminderOrderInfo, 
                                    allRemindersInfo.reminderPartnerInfo
                                */
                            FROM 
                                idk_pp_users pu 
                            JOIN 
                                idk_pp_user_access pua 
                            ON 
                                pu.pu_id = pua.pua_user_id
                                AND 
                                pu.pu_status = 1
                                AND 
                                pua.pua_status = 1
                            JOIN 
                                (
                                    /*
                                        Query koji uzima sve remindere koji imaju aktivnu postavku i aktivan reminder
                                        */
                            
                                            SELECT 
                                                prt1.prt_id 			AS reminderType,
                                                prt1.prt_user_type 		AS reminderUserType,
                                                prs1.prs_id 			AS reminderSetting, 
                                                null 					AS reminderDocumentSetting, 
                                                pr1.pr_id 				AS reminderId,
                                                (
                                                    CASE
                                                        WHEN pr1.pr_level = 1 THEN 'Level 1'
                                                        WHEN pr1.pr_level = 2 THEN 'Level 2'
                                                        WHEN pr1.pr_level = 3 THEN 'Level 3'
                                                        WHEN pr1.pr_level = 4 THEN 'Level 4'
                                                        ELSE 'Level 5+'
                                                    END
                                                )                       AS reminderLevel,
                                                pr1.pr_level            AS reminderLevelValue,
                                                CAST(pr1.pr_date_sent AS date) AS reminderDateSent,
                                                prs1.prs_pua_ids 		AS reminderUsers,
                                                prs1.prs_nalog_id       AS reminderOrder, 
                                                prs1.prs_controlling_pua_ids AS controllingReminderUsers,
                                                GROUP_CONCAT(accessUserInfo1.puUserInfo) AS reminderUsersInfo,
                                                CONCAT(kan1.kandidat_ime, ' ', kan1.kandidat_prezime) AS candidatInfo,
                                                kan1.kandidat_check AS candidateKey  
                                                /*
                                                    CONCAT(nal1.nalog_naziv, ' - ', com1.company_name) AS reminderOrderInfo,
                                                    parcom1.company_name AS reminderPartnerInfo
                                                */
                                            FROM 
                                                idk_pp_reminder_types prt1
                                            JOIN 
                                                idk_pp_reminder_settings prs1
                                            ON 
                                                prt1.prt_id = prs1.prs_reminder_type_id
                                                AND 
                                                prt1.prt_user_type IN (1,2)
                                                AND 
                                                prt1.prt_has_documents = 0
                                                AND 
                                                prs1.prs_active = 1	
                                                AND 
                                                prs1.prs_controlling_pua_ids is not null
                                            JOIN 
                                                idk_pp_reminders pr1
                                            ON 
                                                prs1.prs_id = pr1.pr_reminder_setting_id
                                                AND 
                                                pr1.pr_reminder_document_id is null 
                                                /*AND 
                                                CAST(pr1.pr_date_sent AS date) = CURRENT_DATE()*/
                                                AND 
                                                pr1.pr_status IN (1,2)
                                                AND 
                                                pr1.pr_level > 2
                                            JOIN 
                                                idk_kandidati kan1
                                            ON 
                                                pr1.pr_candidate_id = kan1.kandidat_id
                                            JOIN 
                                                (
                                                    SELECT 
                                                        pua1.pua_id 								AS puaUserId, 
                                                        pu1.pu_id 									AS puUserId, 
                                                        CONCAT(pu1.pu_fname, ' ', pu1.pu_lname)		AS puUserInfo
                                                    FROM 
                                                        idk_pp_user_access pua1
                                                    JOIN 
                                                        idk_pp_users pu1
                                                    ON 
                                                        pua1.pua_user_id = pu1.pu_id
                                                    ORDER BY 
                                                        pu1.pu_id
                                                )
                                                AS 
                                                accessUserInfo1
                                            ON 
                                                FIND_IN_SET(accessUserInfo1.puaUserId, prs1.prs_pua_ids)
                                            GROUP BY 
                                                pr1.pr_id
                                            /*
                                                JOIN 
                                                    idk_nalozi nal1
                                                ON 
                                                    nal1.nalog_id = prs1.prs_nalog_id
                                                JOIN 
                                                    idk_companies com1
                                                ON 
                                                    com1.company_id = nal1.kompanija_id
                                                LEFT JOIN 
                                                    idk_pp_partners par1
                                                ON 
                                                    par1.ppa_id = prs1.prs_partner_id
                                                LEFT JOIN 
                                                    idk_companies parcom1
                                                ON 
                                                    parcom1.company_id = par1.ppa_company_id
                                            */
                            
                                            UNION 
                            
                                            SELECT 
                                                prt2.prt_id 			AS reminderType,
                                                prt2.prt_user_type 		AS reminderUserType,
                                                prs2.prs_id 			AS reminderSetting,
                                                prd2.prd_id 			AS reminderDocumentSetting,
                                                pr2.pr_id 				AS reminderId,
                                                (
                                                    CASE
                                                        WHEN pr2.pr_level = 1 THEN 'Level 1'
                                                        WHEN pr2.pr_level = 2 THEN 'Level 2'
                                                        WHEN pr2.pr_level = 3 THEN 'Level 3'
                                                        WHEN pr2.pr_level = 4 THEN 'Level 4'
                                                        ELSE 'Level 5+'
                                                    END
                                                )                       AS reminderLevel,
                                                pr2.pr_level            AS reminderLevelValue,
                                                CAST(pr2.pr_date_sent AS date) AS reminderDateSent,
                                                prs2.prs_pua_ids 		AS reminderUsers,
                                                prs2.prs_nalog_id       AS reminderOrder,
                                                prs2.prs_controlling_pua_ids AS controllingReminderUsers,
                                                GROUP_CONCAT(accessUserInfo2.puUserInfo) AS reminderUsersInfo,
                                                CONCAT(kan2.kandidat_ime, ' ', kan2.kandidat_prezime) AS candidatInfo,
                                                kan2.kandidat_check AS candidateKey  
                                                /*
                                                    CONCAT(nal2.nalog_naziv, ' - ', com2.company_name) AS reminderOrderInfo,
                                                    parcom2.company_name AS reminderPartnerInfo
                                                */
                                            FROM 
                                                idk_pp_reminder_types prt2
                                            JOIN 
                                                idk_pp_reminder_settings prs2
                                            ON 
                                                prt2.prt_id = prs2.prs_reminder_type_id
                                                AND 
                                                prt2.prt_user_type IN (1,2)
                                                AND 
                                                prt2.prt_has_documents = 1
                                                AND 
                                                prs2.prs_active = 1	
                                                AND 
                                                prs2.prs_controlling_pua_ids is not null
                                            JOIN 
                                                idk_pp_reminder_documents prd2
                                            ON 
                                                prs2.prs_id = prd2.prd_prs_id
                                                AND 
                                                prd2.prd_nrd_id is not null 
                                                AND 
                                                prd2.prd_crd_id is null
                                                AND 
                                                prd2.prd_active = 1
                                            JOIN 
                                                idk_pp_reminders pr2
                                            ON 
                                                prd2.prd_id = pr2.pr_reminder_document_id
                                                AND 
                                                pr2.pr_reminder_setting_id is null 
                                                /*AND 
                                                CAST(pr2.pr_date_sent AS date) = CURRENT_DATE()*/
                                                AND 
                                                pr2.pr_status IN (1,2)
                                                AND 
                                                pr2.pr_level > 2
                                            JOIN 
                                                idk_kandidati kan2
                                            ON 
                                                pr2.pr_candidate_id = kan2.kandidat_id
                                            JOIN 
                                                (
                                                    SELECT 
                                                        pua2.pua_id 								AS puaUserId, 
                                                        pu2.pu_id 									AS puUserId, 
                                                        CONCAT(pu2.pu_fname, ' ', pu2.pu_lname)		AS puUserInfo
                                                    FROM 
                                                        idk_pp_user_access pua2
                                                    JOIN 
                                                        idk_pp_users pu2
                                                    ON 
                                                        pua2.pua_user_id = pu2.pu_id
                                                    ORDER BY 
                                                        pu2.pu_id
                                                )
                                                AS 
                                                accessUserInfo2
                                            ON 
                                                FIND_IN_SET(accessUserInfo2.puaUserId, prs2.prs_pua_ids)
                                            GROUP BY 
                                                pr2.pr_id
                                            /*
                                                JOIN 
                                                    idk_nalozi nal2
                                                ON 
                                                    nal2.nalog_id = prs2.prs_nalog_id
                                                JOIN 
                                                    idk_companies com2
                                                ON 
                                                    com2.company_id = nal2.kompanija_id
                                                LEFT JOIN 
                                                    idk_pp_partners par2
                                                ON 
                                                    par2.ppa_id = prs2.prs_partner_id
                                                LEFT JOIN 
                                                    idk_companies parcom2
                                                ON 
                                                    parcom2.company_id = par2.ppa_company_id
                                            */
                            
                                            ORDER BY 
                                                reminderType, 
                                                reminderSetting, 
                                                reminderDocumentSetting
                                            ASC
                            
                                        /*
                                        Query koji uzima sve remindere koji imaju aktivnu postavku i aktivan reminder
                                    */
                                )
                                AS allRemindersInfo
                            ON
                                FIND_IN_SET(pua.pua_id, allRemindersInfo.controllingReminderUsers) > 0
                            ORDER BY 
                                pu.pu_id,
                                allRemindersInfo.reminderLevelValue
                            DESC
                        ");
                        $query_data->execute();
                        if ($query_data->rowCount() > 0) {
                            $rows_data = $query_data->fetchAll(PDO::FETCH_ASSOC);
                            $grouped_data = array();
                            foreach ( $rows_data AS $row_data ) {
                                if ( isset( $grouped_data[$row_data['userId']] ) ) {
                                    $grouped_data[$row_data['userId']]['data'][] = array(
                                        'userAccessId'              => $row_data['userAccessId'],
                                        'userAccessType'            => $row_data['userAccessType'],
                                        'userAccessPartnerId'       => $row_data['userAccessPartnerId'],
                                        'userAccessOrderId'         => $row_data['userAccessOrderId'],
                                        'reminderType'              => $row_data['reminderType'],
                                        'reminderUserType'          => $row_data['reminderUserType'],
                                        'reminderSetting'           => $row_data['reminderSetting'],
                                        'reminderDocumentSetting'   => $row_data['reminderDocumentSetting'],
                                        'reminderId'                => $row_data['reminderId'],
                                        'reminderLevel'             => $row_data['reminderLevel'],
                                        'candidatInfo'              => $row_data['candidatInfo'],
                                        'candidateKey'              => $row_data['candidateKey'],
                                        'reminderDateSent'          => $row_data['reminderDateSent'],
                                        'reminderUsers'             => $row_data['reminderUsers'],
                                        'reminderUsersInfo'         => $row_data['reminderUsersInfo'],
                                        'reminderOrder'             => $row_data['reminderOrder']
                                    );
                                } else {
                                    $grouped_data[$row_data['userId']] = array(
                                        'userId'            => $row_data['userId'],
                                        'userInfo'          => $row_data['userInfo'],
                                        'userFirstName'     => $row_data['userFirstName'],
                                        'userLastName'      => $row_data['userLastName'],
                                        'userEmail'         => $row_data['userEmail'],
                                        'userLanguage'      => $row_data['userLanguage'],
                                        'userGender'        => $row_data['userGender'],
                                        'data' => array(
                                            array(
                                                'userAccessId'              => $row_data['userAccessId'],
                                                'userAccessType'            => $row_data['userAccessType'],
                                                'userAccessPartnerId'       => $row_data['userAccessPartnerId'],
                                                'userAccessOrderId'         => $row_data['userAccessOrderId'],
                                                'reminderType'              => $row_data['reminderType'],
                                                'reminderUserType'          => $row_data['reminderUserType'],
                                                'reminderSetting'           => $row_data['reminderSetting'],
                                                'reminderDocumentSetting'   => $row_data['reminderDocumentSetting'],
                                                'reminderId'                => $row_data['reminderId'],
                                                'reminderLevel'             => $row_data['reminderLevel'],
                                                'candidatInfo'              => $row_data['candidatInfo'],
                                                'candidateKey'              => $row_data['candidateKey'],
                                                'reminderDateSent'          => $row_data['reminderDateSent'],
                                                'reminderUsers'             => $row_data['reminderUsers'], 
                                                'reminderUsersInfo'         => $row_data['reminderUsersInfo'], 
                                                'reminderOrder'             => $row_data['reminderOrder']
                                            )
                                        )
                                    );
                                }
                            }
                            
                            /*
                                print("<pre>".print_r($grouped_data,true)."</pre>");
                                exit();
                            */
                            $send_emails_okay = array();
                            $send_emails_not_okay = array();
                            foreach ($grouped_data as $userData) {
                                $bodyEmail      = getMailBodyFull($userData, $type);
                                $altBodyEmail   = getAltMailBodyFull($userData, $type);
                                $result_send_email = sendRemindersEmail($userData["userEmail"], $userData["userLanguage"], $type, $bodyEmail, $altBodyEmail);
                                if ($result_send_email["status"] != 'default') {
                                    if ($result_send_email["status"] == 'Okay') {
                                        array_push($send_emails_okay, $userData["userEmail"]);
                                    } else {
                                        array_push($send_emails_not_okay, '{Email: '.$userData["userEmail"].', Message: '.$result_send_email["message"].'}');
                                    }
                                }
                                unset($result_send_email);
                                unset($bodyEmail); 
                                unset($altBodyEmail); 
                                unset($userData);
                            }

                            unset($grouped_data);

                            if(count($send_emails_okay) > 0){
                                $desc_send_okay = "Cron reminders email -> Poslan email userima: >>".implode(", ", $send_emails_okay)."<<. ";
                            }
                            if(count($send_emails_not_okay) > 0){
                                $desc_send_not_okay = "Cron reminders email -> Nije poslan email userima: >>".implode(", ", $send_emails_not_okay)."<<. "; 
                            }
                            unset($send_emails_okay);
                            unset($send_emails_not_okay);
                            
                        } else {
                            echo "<h5>No results found!<br>";
                        }

                    break; 
                }
            /*
            Correct access parameters  END
        */

    } else {
        echo "<h5>Invalid access parameter or type parameter</h5>"; 
    }

    $time_end = microtime(true);
    $time = $time_end - $time_start;

    /*
        Log insert START 
        */
            if ($desc_send_okay != "") {
                $desc_send_okay = $desc_send_okay. " Execution time: ".$time.""; 
                insertLog($desc_send_okay);
            }
            if ($desc_send_not_okay != "") {
                $desc_send_not_okay = $desc_send_not_okay. " Execution time: ".$time."";
                insertLog($desc_send_not_okay);
            }
        /*
        Log insert END 
    */

	echo "<hr><h3>Execution time: ".$time."</h3>";

    /*

        *********************************************************************
        *   TEMPLATE FOR MAIL BODY                                          *
        *   COPY CODE AND PASTE ON https://app.bootstrapemail.com/editor    *
        *********************************************************************
        *   START                                                           *
        *********************************************************************
    
        */

            /*
                
                <html>
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    </head>
                    <body class="bg-white">
                        <div class="container-fluid">
                        
                            <div class="row my-6">
                                <div class="col-12 ax-center">
                                    <img class="max-w-32" src="https://job-step.net/images/jobstep/logo.png" alt="Some Image" />
                                </div>
                            </div>
                            
                            <hr>
                            
                            <!-- Email message START -->
                            <div class="row mb-6">
                                <div class="col-12">
                                    <div class="space-y-2">
                                        <p class="text-gray-700">
                                            Poštovani
                                        </p>
                                        <p class="text-gray-700">
                                            Dovršite ocjenjivanje <span class="text-black">10</span> kandidata za nalog <span class="text-black">Photovoltaik Monteur</span>.
                                        </p>
                                        <p class="text-gray-700">
                                            U listi se nalaze kandidati po levelima.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!-- Email message END -->
                            
                            <!-- Email table START -->
                            <div class="row mb-6">
                                <div class="col-12">
                                    <table class="table thead-default table-bordered text-center text-gray-700">
                                        <thead>
                                            <!-- Table head -->
                                            <tr>
                                                <th>RB</th>
                                                <th>Candidate</th>
                                                <th>Type</th>
                                                <th>Level</th>
                                                <th>Date</th>
                                            </tr>
                                            <!-- Table head -->
                                        </thead>
                                        <tbody>
                                            <!-- One Row -->
                                            <tr>
                                                <td>1</td>
                                                <td>Adis Toromanović</td>
                                                <td>vrefvrvbre</td>
                                                <td>
                                                    <!-- Level 5+ -->
                                                    <span class="badge bg-danger text-white">Level 5+</span>
                                                    <!-- Level 5+ -->
                                                </td>
                                                <td>10.11.2023. 05:00</td>
                                            </tr>
                                            <!-- One Row -->
                                            <tr>
                                                <td>2</td>
                                                <td>Emir Bender</td>
                                                <td>vrefvrvbre</td>
                                                <td>
                                                    <!-- Level 4 -->
                                                    <span class="badge bg-warning text-dark">Level 4</span>
                                                    <!-- Level 4 -->
                                                </td>
                                                <td>10.11.2023. 05:00</td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>Harun Kučuk</td>
                                                <td>vrefvrvbre</td>
                                                <td>
                                                    <!-- Level 3 -->
                                                    <span class="badge bg-primary text-white">Level 3</span>
                                                    <!-- Level 3 -->
                                                </td>
                                                <td>10.11.2023. 05:00</td>
                                            </tr>
                                            <tr>
                                                <td>4</td>
                                                <td>Zinaid Kapić</td>
                                                <td>vrefvrvbre</td>
                                                <td>
                                                    <!-- Level 2 -->
                                                    <span class="badge bg-info text-dark">Level 2</span>
                                                    <!-- Level 2 -->
                                                </td>
                                                <td>10.11.2023. 05:00</td>
                                            </tr>
                                            <tr>
                                                <td>5</td>
                                                <td>Mirza Mujagić</td>
                                                <td>vrefvrvbre</td>
                                                <td>
                                                    <!-- Level 1 -->
                                                    <span class="badge bg-light text-dark">Level 1</span>
                                                    <!-- Level 1 -->
                                                </td>
                                                <td>10.11.2023. 05:00</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- Email table END -->
                            
                            <hr>
                            
                            <!-- Email footer START -->
                            <div class="row mb-6 ax-center">
                                <div class="col-12">
                                    <p class="text-gray-700 text-center">
                                        ©2023 Alle Rechte vorbehalten - Jobstep IT Solutions
                                    </p>
                                </div>
                            </div>
                            <!-- Email footer END -->
                        </div>
                    </body>
                </html>

            */

        /*

        *********************************************************************
        *   END                                                             *
        *********************************************************************
        *   TEMPLATE FOR MAIL BODY                                          *
        *   COPY CODE AND PASTE ON https://app.bootstrapemail.com/editor    *
        *********************************************************************

    */
?>