<?php 
    $page = $_GET['page'] ?? null; 

    switch ($page) {
        case 'company_registration_trans':
            include_once('company-registration-trans.php'); 

            $current_language = $_POST['current_language'] ?? 'en'; 

            if (array_key_exists($current_language, $translations)) {
                echo json_encode($translations[$current_language]);
            } else {
                echo json_encode([]);
            }
        break; 
        default:
            echo 'Undefined page';
        break; 
    }
?>