<?php

    include("includes/functions.php");
    include("includes/common.php");

    if(isset($_GET["folder"])) {
		$folder = $_GET["folder"];
	}else{
        echo "Greska: 101 - Folder nije dostupan. Kontaktirajte svoga administratora.";
    }

    $id = $_GET['id'];

    //Get info
    $query_doc = $db->prepare("
                    SELECT document_name, document_file, document_icon
                    FROM idk_documents
                    WHERE document_id = :document_id");

    $query_doc->execute(array(
                ':document_id' => $id));

    $row_doc = $query_doc->fetch();

        $document_name = $row_doc['document_name'];
        $document_file = $row_doc['document_file'];
        $document_icon = $row_doc['document_icon'];

        $local_file = '../../private/' . getSubdomainr() . '_files/' . $folder . '/' . $document_file . '';
		// $local_file = "files/files/employees/" . $document_file . '';

        // set the download rate limit (=> 20,5 kb/s)
        //$download_rate = 20.5;
        if(file_exists($local_file) && is_file($local_file)) {
            header('Cache-control: private');
            header('Content-Type: application/octet-stream');
            header('Content-Length: '.filesize($local_file));
            header('Content-Disposition: filename='.$document_name.'.'.$document_icon);

            flush();
            $file = fopen($local_file, "r");
            while(!feof($file)){

                // send the current file part to the browser
                print fread($file, filesize($local_file));
                // flush the content to the browser
                flush();
                // sleep one second
                //sleep(1);
            }

            fclose($file);
        }else{
            die('Error: The file '.$local_file.' does not exist!');
        }
?>
