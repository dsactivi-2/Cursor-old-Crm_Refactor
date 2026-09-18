<?php 
    include('translations/company-registration-trans.php'); 
    $lang = $_GET['lang'] ?? 'en';
    $trans = $translations[$lang];
    include("includes/functions.php"); 
?>
<html>
    <head>
        <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Jobstep</title>
        <link rel="icon" href="<?php getSiteUrl(); ?>images/jobstep-logo.svg">

        <?php 
            include('includes/head.php'); 
        ?>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
        <link href="<?php getSiteUrl(); ?>includes/css/company-registration.css" rel="stylesheet">
    </head>
    <body>
        <div class="container d-flex align-items-center justify-content-center vh-100">
            <div class="card border-0 rounded-3 w-100 py-5 px-5 mx-auto">
                <div class="row gy-5">
                    <div class="col-md-12">
                        <!-- 
                            Logo START
                            -->
                                <img src="<?php getSiteUrl(); ?>images/jobstep-logo.svg" class="rounded mx-auto d-block" alt="JobStep Logo">
                            <!-- 
                            Logo END
                        -->
                    </div>
                    <div class="col-md-12">
                        <!-- 
                            Message START
                            -->
                                <div class="alert alert-light my-5 h5 inter-500 js-gray-600-color text-center" role="alert">
                                    <?php echo $trans['Hvala Vam! Vaši podaci su uspješno poslani.']; ?> 
                                </div>
                            <!-- 
                            Message END
                        -->
                    </div>
                    <div class="col-md-12">
                        <!-- 
                            Footer START
                            -->
                                <p class="text-center fs-8 inter-400 js-gray-500-color mb-0 border-top-gray-200 pt-3 mt-3"><?php echo '©'. date('Y') . ' ' . $trans['Sva prava pridržana - JOBSTEP']; ?></p>
                            <!-- 
                            Footer END
                        -->
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<?php 
    unset($translations); 
    unset($trans);
?>