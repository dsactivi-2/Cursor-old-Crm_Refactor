<?php

if (isset($_FILES) && !empty($_FILES)) {
    $messageFile = $_FILES['file'];
    //File properties
    $file_name = $messageFile['name'];
    $file_tmp = $messageFile['tmp_name'];
	
	
    //File extension
    $file_ext = explode('.', $file_name);
    $file_ext = strtolower(end($file_ext));

    $allowed = array('jpg', 'jpeg', 'gif', 'png');
    $allowed_2 = array('pdf');

    if(in_array($file_ext, $allowed)) {

        $file_name_new = uniqid() . '.' . $file_ext;
        echo $file_name_new;
        $file_destination = "files/" . $file_name_new;

        if(move_uploaded_file($file_tmp, $file_destination)){

                //Thumbs
                $path_to_image_directory = "files/";
                $path_to_thumbs_directory = "files/thumbs/";
                $final_width_of_image = 400;

                if(preg_match('/[.](jpg)$/', $file_name_new)) {
                    $im = imagecreatefromjpeg($path_to_image_directory . $file_name_new);
                } else if (preg_match('/[.](gif)$/', $file_name_new)) {
                    $im = imagecreatefromgif($path_to_image_directory . $file_name_new);
                } else if (preg_match('/[.](png)$/', $file_name_new)) {
                    $im = imagecreatefrompng($path_to_image_directory . $file_name_new);
                }

                $ox = imagesx($im);
                $oy = imagesy($im);

                $nx = $final_width_of_image;
                $ny = floor($oy * ($final_width_of_image / $ox));

                $nm = imagecreatetruecolor($nx, $ny);

                imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);

                if(!file_exists($path_to_thumbs_directory)) {
                if(!mkdir($path_to_thumbs_directory)) {
                    die("Error! Please try again.");
                }
                }

                imagejpeg($nm, $path_to_thumbs_directory . $file_name_new);
        }
    }else if(in_array($file_ext, $allowed_2)) {
        
        $file_name_new = uniqid() . '.' . $file_ext;
        echo $file_name_new;
        $file_destination = "files/" . $file_name_new;	
        
        
        if(move_uploaded_file($file_tmp, $file_destination)){	
            //SAVED :)
        } 
    }
}	

?>