<?php

    include("includes/functions.php");

    //One min cron job
    $query = $db->prepare("
                        SELECT url_id, url_link
                        FROM idk_triggerurl
                        WHERE url_datetime <= 'NOW()';");

    $query->execute();

        while($row = $query->fetch()){

            $url_id = $row['url_id'];
            $url_link = $row['url_link'];

            //Curl start
            $ch = curl_init($url_link);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);

            //Delete link from db after curl_exec
            $del_query = $db->prepare("
                                DELETE FROM idk_triggerurl
                                WHERE url_id = :url_id");

            $del_query->execute(array(
                                ':url_id' => $url_id));
        }

?>
