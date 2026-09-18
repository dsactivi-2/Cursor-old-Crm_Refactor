<?php 
    include("includes/functions.php");

    if( isset($_REQUEST["page"]) AND isset($_REQUEST["data"]) AND $_REQUEST["data"] != "" AND $_REQUEST["page"] != "" ) {
        
        $page = $_REQUEST["page"]; // case value
        $data = $_REQUEST["data"]; // appointment key

        switch($page){

            case "confirmation":
                
                $result = getInfoAppointmentInviteLinkArrayR($data);

                if($result["message"] != "Success!"){
        
                    header("Location: public_appointment_invite_link.php");
        
                }

                $check = checkAvailabilityLink($result["data"]["date_sent"], $result["data"]["pap_first_sending_number_days"], $result["data"]["pap_second_sending_number_days"], $result["data"]["pap_date"], $result["data"]["counter_sent"]); 
                
                $numberOfDays = getNumberOfDaysToCasting($result["data"]["pap_date"]);

                if(($result["data"]["status"] == 1 OR $result["data"]["status"] == 2) AND $check == 1){
        
                    if($result["data"]["status"] == 1){
        
                        $status_result = setStatusAppointmentInviteLinkR($data, 2);
        
                        $result = getInfoAppointmentInviteLinkArrayR($data);
        
                    }
        
                }else{
        
                    header("Location: public_appointment_invite_link.php?page=message&data=".$data);
        
                }

            break;
            
            case "message":
                
                $result = getInfoAppointmentInviteLinkArrayR($data);

                if($result["message"] != "Success!"){

                    header("Location: public_appointment_invite_link.php");

                }

                $check = checkAvailabilityLink($result["data"]["date_sent"], $result["data"]["pap_first_sending_number_days"], $result["data"]["pap_second_sending_number_days"], $result["data"]["pap_date"], $result["data"]["counter_sent"]);

                $numberOfDays = getNumberOfDaysToCasting($result["data"]["pap_date"]);

                if(($result["data"]["status"] == 1 OR $result["data"]["status"] == 2) AND $check == 1){

                    header("Location: public_appointment_invite_link.php?page=confirmation&data=".$data."");

                }

                $flag_location_button = 0;

            break;

            case "status_change":

                $data = $_GET["data"];
                $status = 3; 

                $result = getInfoAppointmentInviteLinkArrayR($data);

                if($result["message"] != "Success!"){

                    header("Location: public_appointment_invite_link.php");

                }

                if($result["data"]["status"] == 3){

                    header("Location: public_appointment_invite_link.php?page=message&data=".$data);

                }

            break; 

            default:
                //name nikakvih redirektanja i slicno za neodređeno stanje
            break;

        }

    }else{
        
        echo "Expired link or undefined page!";
        exit();

    }

?>

<!DOCTYPE html>
<html class="h-100">
    
    <head>

        <!-- 
		    Required meta tags START ++++++++++++++++++++++++++++++++++++++++++++
		-->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- 
		    Required meta tags END ++++++++++++++++++++++++++++++++++++++++++++++ 
		-->

        <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;500&display=swap" rel="stylesheet">
        

        <style>
            body,html{
                margin: 0;
                padding: 0;
                font-family: 'Montserrat', sans-serif;
                overflow-x: hidden;
                overflow-y: hidden;
            }
            .leftUntil{
                background-color: #fff;
                border-radius: 30px;
                height: 15px;
                width: 60%;
                margin: auto;
            }
            .ui-widget-header{
                background: #6097a0;
            }
            .buttonStyle{
                display:inline-block;
                text-decoration: none;
                height: 55px;
                width: 200px;    
                border-radius: 15px;
                font-weight: 600;
                border: none;
                background-color: #fff;
                color: #6097a0;
                font-size: 20px;
            }
            .buttonStyle:hover{
                background-color: #6097a0;
                color: #fff;
                border: 2px solid #fff;
            }
            @media (min-width: 1020px) {
                .buttonStyle{
                    border: 2px solid #6097a0;
                }
            }
            .container{
                position: absolute;
                display: flex;
                align-items: center;
                flex-direction: column;
                justify-content: space-around;
                height: 100vh;
                width: 100%;
                text-align: center;
            }
            .waveBackground{
                z-index: -1;
                position: absolute;
                bottom: -5px;
                width: 100%;
                max-height: 40%; 
            }
            .brojDanaNumber{
                font-size: 700%;
                font-weight: 900;
                margin: 0;
                margin-top: 40px;
            }
            .brojDanaTekst{
                font-weight: 600;
                width: 40%;
                margin: 0px auto 15px auto ;
            }
            .tekstPrviDio,.tekstDrugiDio{
                font-weight: 500;
            }
            .buttonContainer{
                height: 5%;
                margin-bottom: 55px;
            }
            .message_style{
                padding-top: 50px;
                padding-bottom: 50px;
            }
        </style>
    </head>
    
    <body>

        <?php

            switch($page){

                case "confirmation":

                    ?>
                        <script>

                            let brojDanaDo = parseInt("<?php echo $numberOfDays; ?>");
                            
                            $( function() {
                                $( "#progressbar" ).progressbar({
                                    value: 30 + ((70 - brojDanaDo * 4))
                                });
                            } );
                        </script>
                        <div class="container"  style="overflow: scroll;">
                            <div>
                                <div style="width: 70%;  margin: auto;">    
                                    <?php 
                                        //Ovdje mozes pogledati kako ti vraca rezultat
                                        //print("<pre>".print_r($result,true)."</pre>");
                                    ?>                
                                    <div>
                                        <svg width="20%" viewBox="0 0 62 71" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_29_109)">
                                                <path d="M2.24252 70.4317C2.08134 70.3896 1.94819 70.3195 1.84307 70.2212C1.73794 70.123 1.66786 69.9897 1.62582 69.8143C1.58377 69.6389 1.56274 69.4214 1.56274 69.1619V67.9902L2.78214 67.7377V69.4285H5.15786V64.8542H6.36324V69.1548C6.36324 69.4144 6.34222 69.6319 6.30017 69.8073C6.25812 69.9827 6.18804 70.116 6.08292 70.2142C5.9778 70.3125 5.85166 70.3826 5.68346 70.4247C5.52228 70.4668 5.31905 70.4879 5.07376 70.4879H2.84521C2.60694 70.4879 2.40371 70.4668 2.23551 70.4247L2.24252 70.4317Z" fill="#60959D"/>
                                                <path d="M8.77401 65.5347C8.81606 65.3593 8.89315 65.226 8.99126 65.1278C9.08937 65.0296 9.22252 64.9594 9.39072 64.9173C9.55891 64.8752 9.75513 64.8542 10.0004 64.8542H13.8268C14.0651 64.8542 14.2683 64.8752 14.4365 64.9173C14.6047 64.9594 14.7308 65.0296 14.836 65.1278C14.9341 65.226 15.0112 65.3593 15.0532 65.5347C15.0952 65.7101 15.1163 65.9276 15.1163 66.1872V69.1548C15.1163 69.4144 15.0952 69.6319 15.0532 69.8073C15.0112 69.9827 14.9341 70.116 14.836 70.2142C14.7378 70.3125 14.6047 70.3826 14.4365 70.4247C14.2753 70.4668 14.0721 70.4879 13.8268 70.4879H10.0004C9.75513 70.4879 9.5519 70.4668 9.39072 70.4247C9.22953 70.3826 9.09638 70.3125 8.99126 70.2142C8.89315 70.116 8.81606 69.9827 8.77401 69.8073C8.73196 69.6319 8.71094 69.4144 8.71094 69.1548V66.1872C8.71094 65.9276 8.73196 65.7101 8.77401 65.5347ZM13.9039 69.4355V65.8644H9.91632V69.4355H13.9039Z" fill="#60959D"/>
                                                <path d="M17.6602 64.8612H22.1103C22.5098 64.8612 22.8111 64.9454 22.9933 65.1137C23.1755 65.2821 23.2736 65.5628 23.2736 65.9556V66.6432C23.2736 66.9168 23.1965 67.1413 23.0424 67.2957C22.8882 67.457 22.6639 67.5552 22.3766 67.5903C22.678 67.6324 22.9092 67.7517 23.0704 67.9341C23.2316 68.1165 23.3087 68.3621 23.3087 68.6708V69.3934C23.3087 70.1301 22.7971 70.4949 21.7809 70.4949H17.6532V64.8612H17.6602ZM18.8516 65.7803V67.1203H21.6127C21.8019 67.1203 21.9421 67.0922 22.0332 67.0431C22.1243 66.994 22.1664 66.8817 22.1664 66.7133V66.1802C22.1664 66.0118 22.1243 65.9065 22.0332 65.8504C21.9421 65.7943 21.8019 65.7732 21.6127 65.7732H18.8516V65.7803ZM18.8516 68.0534V69.4846H21.6127C21.823 69.4846 21.9701 69.4565 22.0612 69.3934C22.1523 69.3303 22.1944 69.1969 22.1944 68.9865V68.5585C22.1944 68.348 22.1523 68.2077 22.0612 68.1516C21.9701 68.0885 21.823 68.0604 21.6127 68.0604H18.8516V68.0534Z" fill="#60959D"/>
                                                <path d="M26.7496 65.8434V67.0571H29.9943C30.2396 67.0571 30.4428 67.0782 30.611 67.1203C30.7792 67.1624 30.9124 67.2325 31.0105 67.3307C31.1086 67.429 31.1857 67.5623 31.2277 67.7377C31.2698 67.9131 31.2908 68.1305 31.2908 68.3901V69.1619C31.2908 69.4214 31.2698 69.6389 31.2277 69.8143C31.1857 69.9897 31.1086 70.123 31.0105 70.2212C30.9124 70.3195 30.7722 70.3896 30.611 70.4317C30.4428 70.4738 30.2396 70.4949 29.9943 70.4949H26.7846C26.5394 70.4949 26.3361 70.4738 26.1679 70.4317C25.9997 70.3896 25.8666 70.3195 25.7685 70.2212C25.6704 70.123 25.5933 69.9897 25.5512 69.8143C25.5092 69.6389 25.4882 69.4214 25.4882 69.1619V69.0075L26.5534 68.769V69.4355H30.2326V68.1446H26.9879C26.7496 68.1446 26.5464 68.1235 26.3782 68.0814C26.217 68.0393 26.0838 67.9692 25.9787 67.871C25.8736 67.7727 25.8035 67.6394 25.7615 67.464C25.7194 67.2886 25.6984 67.0712 25.6984 66.8116V66.1872C25.6984 65.9276 25.7194 65.7101 25.7615 65.5347C25.8035 65.3593 25.8736 65.226 25.9787 65.1278C26.0838 65.0296 26.21 64.9594 26.3782 64.9173C26.5464 64.8752 26.7426 64.8542 26.9879 64.8542H29.9383C30.1695 64.8542 30.3657 64.8752 30.5269 64.9103C30.6881 64.9454 30.8283 65.0155 30.9264 65.1067C31.0315 65.2049 31.1086 65.3312 31.1506 65.4856C31.1997 65.6469 31.2207 65.8434 31.2207 66.0889V66.2082L30.1555 66.4818V65.8364H26.7496V65.8434Z" fill="#60959D"/>
                                                <path d="M36.2174 70.4949H35.0121V65.8644H32.6223V64.8612H38.6142V65.8644H36.2174V70.4949Z" fill="#60959D"/>
                                                <path d="M40.0999 64.8612H45.4189V65.8223H41.3122V67.0782H43.709V68.0393H41.3122V69.4355H45.468V70.4949H40.0999V64.8612Z" fill="#60959D"/>
                                                <path d="M47.6966 64.8612H52.1467C52.5461 64.8612 52.8475 64.9454 53.0297 65.1137C53.2119 65.2821 53.31 65.5628 53.31 65.9556V67.2115C53.31 67.6044 53.2189 67.885 53.0297 68.0534C52.8405 68.2218 52.5461 68.3059 52.1467 68.3059H48.8809V70.5019H47.6896V64.8682L47.6966 64.8612ZM48.8879 65.8364V67.3307H51.6071C51.7963 67.3307 51.9294 67.3027 52.0205 67.2466C52.1046 67.1904 52.1537 67.0712 52.1537 66.8817V66.2784C52.1537 66.0889 52.1116 65.9697 52.0205 65.9135C51.9364 65.8574 51.7963 65.8294 51.6071 65.8294H48.8879V65.8364Z" fill="#60959D"/>
                                                <path d="M36.8763 33.9986C26.161 30.4697 19.4333 20.1845 19.0619 8.60138C16.2236 21.7069 22.664 34.6862 34.4725 38.5729C44.501 41.8774 55.4265 37.6398 61.993 28.835C54.8659 34.616 45.5662 36.8611 36.8763 33.9986Z" fill="#60959D"/>
                                                <path d="M23.1965 7.22628C25.2169 7.22628 26.8547 5.60863 26.8547 3.61314C26.8547 1.61766 25.2169 0 23.1965 0C21.1762 0 19.5383 1.61766 19.5383 3.61314C19.5383 5.60863 21.1762 7.22628 23.1965 7.22628Z" fill="#60959D"/>
                                                <path d="M42.6578 14.5648C37.8573 14.5648 33.9679 18.4025 33.9679 23.1381C33.9679 27.8738 37.8573 31.7115 42.6578 31.7115C47.4583 31.7115 51.3478 27.8738 51.3478 23.1381C51.3478 18.4025 47.4583 14.5648 42.6578 14.5648ZM39.4271 23.4468C37.4088 23.4468 35.769 21.8332 35.769 19.8337C35.769 17.8342 37.4088 16.2206 39.4271 16.2206C41.4455 16.2206 43.0853 17.8342 43.0853 19.8337C43.0853 21.8332 41.4455 23.4468 39.4271 23.4468Z" fill="#60959D"/>
                                                <path d="M27.2262 47.4339C38.5441 47.4339 48.867 53.0465 53.3941 63.9421C51.6772 49.8473 40.0018 40.6075 26.5324 40.6075C11.8786 40.6005 0 53.4815 0 69.3794C0 69.9266 0.014016 70.4668 0.0420481 71C3.81237 57.1508 16.3357 47.4339 27.2192 47.4339H27.2262Z" fill="#60959D"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_29_109">
                                                    <rect width="62" height="71" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>

                                    </div>
                                    <p class="brojDanaNumber"><?php echo $numberOfDays; ?></p>
                                    <?php 
                                        if( $numberOfDays > 0){
                                            ?>
                                                <p class="brojDanaTekst">Još <?php echo $numberOfDays; ?> <?php echo $numberOfDays == 1 ? "dan" : "dana"; ?> do razgovora sa poslodavcem</p>
                                            <?php 
                                        }
                                    ?>
                                    <div id="progressbar" class="leftUntil"></div>
                                    <p class="tekstPrviDio">Molimo da potvrdite vaš dolazak dana <span style="font-weight: 700;"><?php echo date("d.m.Y", strtotime($result["data"]["pap_date"])). " u " . date("H:i", strtotime($result["data"]["pca_time"])); ?> </span> </p>
                                    <?php 
                                        if( $result["data"]["pap_location_name"] != NULL ){
                                            ?>
                                                <p class="tekstDrugiDio">Lokacija: <?php echo $result["data"]["pap_location_name"]; ?></p>
                                            <?php 
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class="buttonContainer">
                                <a href="public_appointment_invite_link.php?page=status_change&data=<?php echo $data; ?>"><button class="buttonStyle">Potvrdi</button></a>
                            </div>
                            
                        </div>
                        <div class="waveBackground">
                            <svg width="100%" viewBox="0 0 393 280" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M393 111.04C357.033 88.1578 316.193 76.7812 271.373 83.0326C128.284 102.997 46.4678 47.6125 0 0L-12.5 280C-11.4478 283.316 3.19077 293.5 3.19077 280H393V111.04Z" fill="#6097A0"/>
                            </svg>

                        </div>
                    <?php 
                    
                    unset($result); 

                break;

                case "message":

                    ?>

                        <script>

                            let brojDanaDo = parseInt("<?php echo $numberOfDays; ?>");

                            $( function() {
                                $( "#progressbar" ).progressbar({
                                    value: 30 + ((70 - brojDanaDo * 4))
                                });
                            });
                        </script>

                        <div class="container">
                            <div>
                                <div style="width: 70%;  margin: auto;">   
                                    <div>
                                        <svg width="20%" viewBox="0 0 62 71" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_29_109)">
                                                <path d="M2.24252 70.4317C2.08134 70.3896 1.94819 70.3195 1.84307 70.2212C1.73794 70.123 1.66786 69.9897 1.62582 69.8143C1.58377 69.6389 1.56274 69.4214 1.56274 69.1619V67.9902L2.78214 67.7377V69.4285H5.15786V64.8542H6.36324V69.1548C6.36324 69.4144 6.34222 69.6319 6.30017 69.8073C6.25812 69.9827 6.18804 70.116 6.08292 70.2142C5.9778 70.3125 5.85166 70.3826 5.68346 70.4247C5.52228 70.4668 5.31905 70.4879 5.07376 70.4879H2.84521C2.60694 70.4879 2.40371 70.4668 2.23551 70.4247L2.24252 70.4317Z" fill="#60959D"/>
                                                <path d="M8.77401 65.5347C8.81606 65.3593 8.89315 65.226 8.99126 65.1278C9.08937 65.0296 9.22252 64.9594 9.39072 64.9173C9.55891 64.8752 9.75513 64.8542 10.0004 64.8542H13.8268C14.0651 64.8542 14.2683 64.8752 14.4365 64.9173C14.6047 64.9594 14.7308 65.0296 14.836 65.1278C14.9341 65.226 15.0112 65.3593 15.0532 65.5347C15.0952 65.7101 15.1163 65.9276 15.1163 66.1872V69.1548C15.1163 69.4144 15.0952 69.6319 15.0532 69.8073C15.0112 69.9827 14.9341 70.116 14.836 70.2142C14.7378 70.3125 14.6047 70.3826 14.4365 70.4247C14.2753 70.4668 14.0721 70.4879 13.8268 70.4879H10.0004C9.75513 70.4879 9.5519 70.4668 9.39072 70.4247C9.22953 70.3826 9.09638 70.3125 8.99126 70.2142C8.89315 70.116 8.81606 69.9827 8.77401 69.8073C8.73196 69.6319 8.71094 69.4144 8.71094 69.1548V66.1872C8.71094 65.9276 8.73196 65.7101 8.77401 65.5347ZM13.9039 69.4355V65.8644H9.91632V69.4355H13.9039Z" fill="#60959D"/>
                                                <path d="M17.6602 64.8612H22.1103C22.5098 64.8612 22.8111 64.9454 22.9933 65.1137C23.1755 65.2821 23.2736 65.5628 23.2736 65.9556V66.6432C23.2736 66.9168 23.1965 67.1413 23.0424 67.2957C22.8882 67.457 22.6639 67.5552 22.3766 67.5903C22.678 67.6324 22.9092 67.7517 23.0704 67.9341C23.2316 68.1165 23.3087 68.3621 23.3087 68.6708V69.3934C23.3087 70.1301 22.7971 70.4949 21.7809 70.4949H17.6532V64.8612H17.6602ZM18.8516 65.7803V67.1203H21.6127C21.8019 67.1203 21.9421 67.0922 22.0332 67.0431C22.1243 66.994 22.1664 66.8817 22.1664 66.7133V66.1802C22.1664 66.0118 22.1243 65.9065 22.0332 65.8504C21.9421 65.7943 21.8019 65.7732 21.6127 65.7732H18.8516V65.7803ZM18.8516 68.0534V69.4846H21.6127C21.823 69.4846 21.9701 69.4565 22.0612 69.3934C22.1523 69.3303 22.1944 69.1969 22.1944 68.9865V68.5585C22.1944 68.348 22.1523 68.2077 22.0612 68.1516C21.9701 68.0885 21.823 68.0604 21.6127 68.0604H18.8516V68.0534Z" fill="#60959D"/>
                                                <path d="M26.7496 65.8434V67.0571H29.9943C30.2396 67.0571 30.4428 67.0782 30.611 67.1203C30.7792 67.1624 30.9124 67.2325 31.0105 67.3307C31.1086 67.429 31.1857 67.5623 31.2277 67.7377C31.2698 67.9131 31.2908 68.1305 31.2908 68.3901V69.1619C31.2908 69.4214 31.2698 69.6389 31.2277 69.8143C31.1857 69.9897 31.1086 70.123 31.0105 70.2212C30.9124 70.3195 30.7722 70.3896 30.611 70.4317C30.4428 70.4738 30.2396 70.4949 29.9943 70.4949H26.7846C26.5394 70.4949 26.3361 70.4738 26.1679 70.4317C25.9997 70.3896 25.8666 70.3195 25.7685 70.2212C25.6704 70.123 25.5933 69.9897 25.5512 69.8143C25.5092 69.6389 25.4882 69.4214 25.4882 69.1619V69.0075L26.5534 68.769V69.4355H30.2326V68.1446H26.9879C26.7496 68.1446 26.5464 68.1235 26.3782 68.0814C26.217 68.0393 26.0838 67.9692 25.9787 67.871C25.8736 67.7727 25.8035 67.6394 25.7615 67.464C25.7194 67.2886 25.6984 67.0712 25.6984 66.8116V66.1872C25.6984 65.9276 25.7194 65.7101 25.7615 65.5347C25.8035 65.3593 25.8736 65.226 25.9787 65.1278C26.0838 65.0296 26.21 64.9594 26.3782 64.9173C26.5464 64.8752 26.7426 64.8542 26.9879 64.8542H29.9383C30.1695 64.8542 30.3657 64.8752 30.5269 64.9103C30.6881 64.9454 30.8283 65.0155 30.9264 65.1067C31.0315 65.2049 31.1086 65.3312 31.1506 65.4856C31.1997 65.6469 31.2207 65.8434 31.2207 66.0889V66.2082L30.1555 66.4818V65.8364H26.7496V65.8434Z" fill="#60959D"/>
                                                <path d="M36.2174 70.4949H35.0121V65.8644H32.6223V64.8612H38.6142V65.8644H36.2174V70.4949Z" fill="#60959D"/>
                                                <path d="M40.0999 64.8612H45.4189V65.8223H41.3122V67.0782H43.709V68.0393H41.3122V69.4355H45.468V70.4949H40.0999V64.8612Z" fill="#60959D"/>
                                                <path d="M47.6966 64.8612H52.1467C52.5461 64.8612 52.8475 64.9454 53.0297 65.1137C53.2119 65.2821 53.31 65.5628 53.31 65.9556V67.2115C53.31 67.6044 53.2189 67.885 53.0297 68.0534C52.8405 68.2218 52.5461 68.3059 52.1467 68.3059H48.8809V70.5019H47.6896V64.8682L47.6966 64.8612ZM48.8879 65.8364V67.3307H51.6071C51.7963 67.3307 51.9294 67.3027 52.0205 67.2466C52.1046 67.1904 52.1537 67.0712 52.1537 66.8817V66.2784C52.1537 66.0889 52.1116 65.9697 52.0205 65.9135C51.9364 65.8574 51.7963 65.8294 51.6071 65.8294H48.8879V65.8364Z" fill="#60959D"/>
                                                <path d="M36.8763 33.9986C26.161 30.4697 19.4333 20.1845 19.0619 8.60138C16.2236 21.7069 22.664 34.6862 34.4725 38.5729C44.501 41.8774 55.4265 37.6398 61.993 28.835C54.8659 34.616 45.5662 36.8611 36.8763 33.9986Z" fill="#60959D"/>
                                                <path d="M23.1965 7.22628C25.2169 7.22628 26.8547 5.60863 26.8547 3.61314C26.8547 1.61766 25.2169 0 23.1965 0C21.1762 0 19.5383 1.61766 19.5383 3.61314C19.5383 5.60863 21.1762 7.22628 23.1965 7.22628Z" fill="#60959D"/>
                                                <path d="M42.6578 14.5648C37.8573 14.5648 33.9679 18.4025 33.9679 23.1381C33.9679 27.8738 37.8573 31.7115 42.6578 31.7115C47.4583 31.7115 51.3478 27.8738 51.3478 23.1381C51.3478 18.4025 47.4583 14.5648 42.6578 14.5648ZM39.4271 23.4468C37.4088 23.4468 35.769 21.8332 35.769 19.8337C35.769 17.8342 37.4088 16.2206 39.4271 16.2206C41.4455 16.2206 43.0853 17.8342 43.0853 19.8337C43.0853 21.8332 41.4455 23.4468 39.4271 23.4468Z" fill="#60959D"/>
                                                <path d="M27.2262 47.4339C38.5441 47.4339 48.867 53.0465 53.3941 63.9421C51.6772 49.8473 40.0018 40.6075 26.5324 40.6075C11.8786 40.6005 0 53.4815 0 69.3794C0 69.9266 0.014016 70.4668 0.0420481 71C3.81237 57.1508 16.3357 47.4339 27.2192 47.4339H27.2262Z" fill="#60959D"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_29_109">
                                                    <rect width="62" height="71" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <?php 
                                        
                                        if($result["data"]["status"] == 3 AND $numberOfDays >= 0 AND $check == 1){

                                            ?>

                                                <?php 
                                                    
                                                    if($numberOfDays > 0){

                                                        $flag_location_button = 1;
                                                        
                                                        ?>
                                    
                                                        <p class="brojDanaNumber"><?php echo $numberOfDays; ?></p>
                                                        <p class="brojDanaTekst">Još <?php echo $numberOfDays; ?> <?php echo $numberOfDays == 1 ? "dan" : "dana"; ?> do razgovora sa poslodavcem</p>
                                                        <div id="progressbar" class="leftUntil"></div>
                                                        <p class="tekstPrviDio">Potvrdili ste Vaš dolazak dana <span style="font-weight: 700;"><?php echo date("d.m.Y", strtotime($result["data"]["pap_date"])). " u " . date("H:i", strtotime($result["data"]["pca_time"])); ?> </span> </p>
                                                        <?php 
                                                            if( $result["data"]["pap_location_name"] != NULL ){
                                                                ?>
                                                                    <p class="tekstDrugiDio">Lokacija: <?php echo $result["data"]["pap_location_name"]; ?></p>
                                                                <?php 
                                                            }
                                                        ?> 

                                                        <?php 
                                                
                                                    }else{

                                                        $flag_location_button = 1;
                                                        
                                                        ?>

                                                        <p class="brojDanaNumber"><?php echo $numberOfDays; ?></p>
                                                        <div id="progressbar" class="leftUntil"></div>
                                                        <p class="tekstPrviDio">Potvrdili ste Vaš dolazak za današnji termin!<br> Vrijeme termina: <?php echo date("H:i", strtotime($result["data"]["pca_time"])); ?> </p>
                                                        <?php 
                                                            if( $result["data"]["pap_location_name"] != NULL ){
                                                                ?>
                                                                    <p class="tekstDrugiDio">Lokacija: <?php echo $result["data"]["pap_location_name"]; ?></p>
                                                                <?php 
                                                            }
                                                        ?> 

                                                        <?php 

                                                    } 
                                                        
                                                ?>
                                            
                                            <?php

                                        }else{

                                            $flag_location_button = 0;

                                            ?>

                                                <style>
                                                    svg[viewBox="0 0 62 71"]{
                                                        width: 50%;
                                                    }
                                                </style>

                                                <div class="message_style">
                                                    <h3>Link kojem želite pristupiti je istekao!<h3>
                                                </div>

                                            <?php

                                        }

                                    ?> 

                                    
                                </div>
                                <?php
                                    
                                    if($flag_location_button == 1 AND $result["data"]["pap_google_maps_location"] != NULL){

                                        ?>

                                        <div class="buttonContainer">
                                            <a target="_blank" href="<?php echo $result["data"]["pap_google_maps_location"]; ?>"><button class="buttonStyle">Vidi lokaciju</button></a>
                                        </div>

                                        <?php 
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="waveBackground">
                            <svg width="100%" viewBox="0 0 393 280" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M393 111.04C357.033 88.1578 316.193 76.7812 271.373 83.0326C128.284 102.997 46.4678 47.6125 0 0L-12.5 280C-11.4478 283.316 3.19077 293.5 3.19077 280H393V111.04Z" fill="#6097A0"/>
                            </svg>
                        </div>

                    <?php 

                    unset($result); 

                break;

                case "status_change":
                    
                    $status_result = setStatusAppointmentInviteLinkR($data, $status);

                    $tf_candidate_id = $result["data"]["candidate_id"];
                    $tf_nalog_id = $result["data"]["pap_nalog_id"];
                    $tf_project_id = getLastTFProjekt($tf_candidate_id, 1);
                    $tf_casting_id = $result["data"]["pap_group_id"];
                    $pap_date = $result["data"]["pap_date"];

                    if($result["data"]["counter_sent"] == 1){
                        $tf_status_id = 16; // Pristao

                        // arhiviraj stari Pristao i dodaj novi pristao
                        updateLastActiveTaskForCandidate($tf_candidate_id, 1);
                        updateTaskForceStatusForCandidate($tf_candidate_id, $tf_status_id);

                        // postavi novi appointment termin
                        $pap_second_sending_number_days = $result["data"]["pap_second_sending_number_days"];

                        $tf_call_appointment =  date("Y-m-d 09:00", strtotime($pap_date."-$pap_second_sending_number_days days +1 days"));

                        $tf_note = "Kandidat potvrdio dolazak na prvoj poruci.";

                        $query = $db->prepare("
								INSERT INTO idk_task_force
									(tf_candidate_id, tf_nalog_id, tf_project_id, tf_casting_id, tf_status_id, tf_call_appointment, tf_note, tf_important_note, tf_last_active_task, tf_brojac_neuspjela_komunikacija)
								VALUES
									(:tf_candidate_id, :tf_nalog_id, :tf_project_id, :tf_casting_id, :tf_status_id, :tf_call_appointment, :tf_note, :tf_important_note, :tf_last_active_task, :tf_brojac_neuspjela_komunikacija)");

                        $query->execute(array(
                                        ':tf_candidate_id' => $tf_candidate_id,
                                        ':tf_nalog_id' =>  $tf_nalog_id,
                                        ':tf_project_id' => $tf_project_id,
                                        ':tf_casting_id' => $tf_casting_id,
                                        ':tf_status_id' => $tf_status_id,
                                        ':tf_call_appointment' => $tf_call_appointment,
                                        ':tf_note' => $tf_note,
                                        ':tf_important_note' => 1,
                                        ':tf_last_active_task' => 1,
                                        ':tf_brojac_neuspjela_komunikacija' => NULL));

                        $query = $db->prepare("
                            INSERT INTO idk_notes
                                (note_txt, note_datetime, note_group, note_dataid, note_employeeid)
                            VALUES
                                (:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");

                        $query->execute(array(
                                    ':note_txt' => $tf_note,
                                    ':note_datetime' => date('Y-m-d H:i:s'),
                                    ':note_group' => 2,
                                    ':note_dataid' =>  $tf_candidate_id,
                                    ':note_employeeid' => $logged_employee_id));
                        
                        $log_desc = "Dodao novu bilješku: " .$tf_note. " za kandidata: " .getCandidateFullnameR($tf_candidate_id).".";

                        addToLogs($log_desc,3);

                    }else if($result["data"]["counter_sent"] == 2){
                        $tf_status_id = 18;
                        // arhiviraj stari Pristao i dodaj novi Dolazi
                        updateLastActiveTaskForCandidate($tf_candidate_id, 1);
                        updateTaskForceStatusForCandidate($tf_candidate_id, $tf_status_id);

                        $pap_second_sending_number_days = $result["data"]["pap_second_sending_number_days"];

                        $tf_call_appointment =  NULL;

                        $tf_note = "Kandidat potvrdio dolazak na drugoj poruci.";

                        $query = $db->prepare("
								INSERT INTO idk_task_force
									(tf_candidate_id, tf_nalog_id, tf_project_id, tf_casting_id, tf_status_id, tf_call_appointment, tf_note, tf_important_note, tf_last_active_task, tf_brojac_neuspjela_komunikacija)
								VALUES
									(:tf_candidate_id, :tf_nalog_id, :tf_project_id, :tf_casting_id, :tf_status_id, :tf_call_appointment, :tf_note, :tf_important_note, :tf_last_active_task, :tf_brojac_neuspjela_komunikacija)");

                        $query->execute(array(
                                        ':tf_candidate_id' => $tf_candidate_id,
                                        ':tf_nalog_id' =>  $tf_nalog_id,
                                        ':tf_project_id' => $tf_project_id,
                                        ':tf_casting_id' => $tf_casting_id,
                                        ':tf_status_id' => $tf_status_id,
                                        ':tf_call_appointment' => $tf_call_appointment,
                                        ':tf_note' => $tf_note,
                                        ':tf_important_note' => 1,
                                        ':tf_last_active_task' => 1,
                                        ':tf_brojac_neuspjela_komunikacija' => NULL));
                        
                        $query = $db->prepare("
                            INSERT INTO idk_notes
                                (note_txt, note_datetime, note_group, note_dataid, note_employeeid)
                            VALUES
                                (:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");

                        $query->execute(array(
                                    ':note_txt' => $tf_note,
                                    ':note_datetime' => date('Y-m-d H:i:s'),
                                    ':note_group' => 2,
                                    ':note_dataid' =>  $tf_candidate_id,
                                    ':note_employeeid' => $logged_employee_id));
                        
                        $tsr_id = checkTFstatsForCandidate($tf_candidate_id, $tf_casting_id);
                        if($tsr_id != null){
                            updateTFStat($tsr_id, 5);
                        }

                        $log_desc = "Dodao novu bilješku: " .$tf_note. " za kandidata: " .getCandidateFullnameR($tf_candidate_id).".";

                        addToLogs($log_desc,3);
                    }
                    
                    unset($result);
                    
                    if($status_result == "Successfully updated!"){

                        header("Location: public_appointment_invite_link.php?page=message&data=".$data);

                    }else{

                        header("Location: public_appointment_invite_link.php");
                    
                    }
                    
                break; 

                default:

                    ?>
                    
                        <div class="container">
                            <div>
                                <div style="width: 70%;  margin: auto;">                    
                                    <div>
                                        <svg width="20%" viewBox="0 0 62 71" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g clip-path="url(#clip0_29_109)">
                                                <path d="M2.24252 70.4317C2.08134 70.3896 1.94819 70.3195 1.84307 70.2212C1.73794 70.123 1.66786 69.9897 1.62582 69.8143C1.58377 69.6389 1.56274 69.4214 1.56274 69.1619V67.9902L2.78214 67.7377V69.4285H5.15786V64.8542H6.36324V69.1548C6.36324 69.4144 6.34222 69.6319 6.30017 69.8073C6.25812 69.9827 6.18804 70.116 6.08292 70.2142C5.9778 70.3125 5.85166 70.3826 5.68346 70.4247C5.52228 70.4668 5.31905 70.4879 5.07376 70.4879H2.84521C2.60694 70.4879 2.40371 70.4668 2.23551 70.4247L2.24252 70.4317Z" fill="#60959D"/>
                                                <path d="M8.77401 65.5347C8.81606 65.3593 8.89315 65.226 8.99126 65.1278C9.08937 65.0296 9.22252 64.9594 9.39072 64.9173C9.55891 64.8752 9.75513 64.8542 10.0004 64.8542H13.8268C14.0651 64.8542 14.2683 64.8752 14.4365 64.9173C14.6047 64.9594 14.7308 65.0296 14.836 65.1278C14.9341 65.226 15.0112 65.3593 15.0532 65.5347C15.0952 65.7101 15.1163 65.9276 15.1163 66.1872V69.1548C15.1163 69.4144 15.0952 69.6319 15.0532 69.8073C15.0112 69.9827 14.9341 70.116 14.836 70.2142C14.7378 70.3125 14.6047 70.3826 14.4365 70.4247C14.2753 70.4668 14.0721 70.4879 13.8268 70.4879H10.0004C9.75513 70.4879 9.5519 70.4668 9.39072 70.4247C9.22953 70.3826 9.09638 70.3125 8.99126 70.2142C8.89315 70.116 8.81606 69.9827 8.77401 69.8073C8.73196 69.6319 8.71094 69.4144 8.71094 69.1548V66.1872C8.71094 65.9276 8.73196 65.7101 8.77401 65.5347ZM13.9039 69.4355V65.8644H9.91632V69.4355H13.9039Z" fill="#60959D"/>
                                                <path d="M17.6602 64.8612H22.1103C22.5098 64.8612 22.8111 64.9454 22.9933 65.1137C23.1755 65.2821 23.2736 65.5628 23.2736 65.9556V66.6432C23.2736 66.9168 23.1965 67.1413 23.0424 67.2957C22.8882 67.457 22.6639 67.5552 22.3766 67.5903C22.678 67.6324 22.9092 67.7517 23.0704 67.9341C23.2316 68.1165 23.3087 68.3621 23.3087 68.6708V69.3934C23.3087 70.1301 22.7971 70.4949 21.7809 70.4949H17.6532V64.8612H17.6602ZM18.8516 65.7803V67.1203H21.6127C21.8019 67.1203 21.9421 67.0922 22.0332 67.0431C22.1243 66.994 22.1664 66.8817 22.1664 66.7133V66.1802C22.1664 66.0118 22.1243 65.9065 22.0332 65.8504C21.9421 65.7943 21.8019 65.7732 21.6127 65.7732H18.8516V65.7803ZM18.8516 68.0534V69.4846H21.6127C21.823 69.4846 21.9701 69.4565 22.0612 69.3934C22.1523 69.3303 22.1944 69.1969 22.1944 68.9865V68.5585C22.1944 68.348 22.1523 68.2077 22.0612 68.1516C21.9701 68.0885 21.823 68.0604 21.6127 68.0604H18.8516V68.0534Z" fill="#60959D"/>
                                                <path d="M26.7496 65.8434V67.0571H29.9943C30.2396 67.0571 30.4428 67.0782 30.611 67.1203C30.7792 67.1624 30.9124 67.2325 31.0105 67.3307C31.1086 67.429 31.1857 67.5623 31.2277 67.7377C31.2698 67.9131 31.2908 68.1305 31.2908 68.3901V69.1619C31.2908 69.4214 31.2698 69.6389 31.2277 69.8143C31.1857 69.9897 31.1086 70.123 31.0105 70.2212C30.9124 70.3195 30.7722 70.3896 30.611 70.4317C30.4428 70.4738 30.2396 70.4949 29.9943 70.4949H26.7846C26.5394 70.4949 26.3361 70.4738 26.1679 70.4317C25.9997 70.3896 25.8666 70.3195 25.7685 70.2212C25.6704 70.123 25.5933 69.9897 25.5512 69.8143C25.5092 69.6389 25.4882 69.4214 25.4882 69.1619V69.0075L26.5534 68.769V69.4355H30.2326V68.1446H26.9879C26.7496 68.1446 26.5464 68.1235 26.3782 68.0814C26.217 68.0393 26.0838 67.9692 25.9787 67.871C25.8736 67.7727 25.8035 67.6394 25.7615 67.464C25.7194 67.2886 25.6984 67.0712 25.6984 66.8116V66.1872C25.6984 65.9276 25.7194 65.7101 25.7615 65.5347C25.8035 65.3593 25.8736 65.226 25.9787 65.1278C26.0838 65.0296 26.21 64.9594 26.3782 64.9173C26.5464 64.8752 26.7426 64.8542 26.9879 64.8542H29.9383C30.1695 64.8542 30.3657 64.8752 30.5269 64.9103C30.6881 64.9454 30.8283 65.0155 30.9264 65.1067C31.0315 65.2049 31.1086 65.3312 31.1506 65.4856C31.1997 65.6469 31.2207 65.8434 31.2207 66.0889V66.2082L30.1555 66.4818V65.8364H26.7496V65.8434Z" fill="#60959D"/>
                                                <path d="M36.2174 70.4949H35.0121V65.8644H32.6223V64.8612H38.6142V65.8644H36.2174V70.4949Z" fill="#60959D"/>
                                                <path d="M40.0999 64.8612H45.4189V65.8223H41.3122V67.0782H43.709V68.0393H41.3122V69.4355H45.468V70.4949H40.0999V64.8612Z" fill="#60959D"/>
                                                <path d="M47.6966 64.8612H52.1467C52.5461 64.8612 52.8475 64.9454 53.0297 65.1137C53.2119 65.2821 53.31 65.5628 53.31 65.9556V67.2115C53.31 67.6044 53.2189 67.885 53.0297 68.0534C52.8405 68.2218 52.5461 68.3059 52.1467 68.3059H48.8809V70.5019H47.6896V64.8682L47.6966 64.8612ZM48.8879 65.8364V67.3307H51.6071C51.7963 67.3307 51.9294 67.3027 52.0205 67.2466C52.1046 67.1904 52.1537 67.0712 52.1537 66.8817V66.2784C52.1537 66.0889 52.1116 65.9697 52.0205 65.9135C51.9364 65.8574 51.7963 65.8294 51.6071 65.8294H48.8879V65.8364Z" fill="#60959D"/>
                                                <path d="M36.8763 33.9986C26.161 30.4697 19.4333 20.1845 19.0619 8.60138C16.2236 21.7069 22.664 34.6862 34.4725 38.5729C44.501 41.8774 55.4265 37.6398 61.993 28.835C54.8659 34.616 45.5662 36.8611 36.8763 33.9986Z" fill="#60959D"/>
                                                <path d="M23.1965 7.22628C25.2169 7.22628 26.8547 5.60863 26.8547 3.61314C26.8547 1.61766 25.2169 0 23.1965 0C21.1762 0 19.5383 1.61766 19.5383 3.61314C19.5383 5.60863 21.1762 7.22628 23.1965 7.22628Z" fill="#60959D"/>
                                                <path d="M42.6578 14.5648C37.8573 14.5648 33.9679 18.4025 33.9679 23.1381C33.9679 27.8738 37.8573 31.7115 42.6578 31.7115C47.4583 31.7115 51.3478 27.8738 51.3478 23.1381C51.3478 18.4025 47.4583 14.5648 42.6578 14.5648ZM39.4271 23.4468C37.4088 23.4468 35.769 21.8332 35.769 19.8337C35.769 17.8342 37.4088 16.2206 39.4271 16.2206C41.4455 16.2206 43.0853 17.8342 43.0853 19.8337C43.0853 21.8332 41.4455 23.4468 39.4271 23.4468Z" fill="#60959D"/>
                                                <path d="M27.2262 47.4339C38.5441 47.4339 48.867 53.0465 53.3941 63.9421C51.6772 49.8473 40.0018 40.6075 26.5324 40.6075C11.8786 40.6005 0 53.4815 0 69.3794C0 69.9266 0.014016 70.4668 0.0420481 71C3.81237 57.1508 16.3357 47.4339 27.2192 47.4339H27.2262Z" fill="#60959D"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_29_109">
                                                    <rect width="62" height="71" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                    <style>
                                        svg[viewBox="0 0 62 71"]{
                                            width: 50%;
                                        }
                                    </style>

                                    <div class="message_style">
                                        <h3>Nevažeći link!<h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="waveBackground">
                            <svg width="100%" viewBox="0 0 393 280" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M393 111.04C357.033 88.1578 316.193 76.7812 271.373 83.0326C128.284 102.997 46.4678 47.6125 0 0L-12.5 280C-11.4478 283.316 3.19077 293.5 3.19077 280H393V111.04Z" fill="#6097A0"/>
                            </svg>
                        </div>

                    <?php 

                break;
            
            }

        ?>

    </body>
</html>
