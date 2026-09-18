<?php
	$nalogid = $_GET['nalogid'];
	$mjesec = $_GET['mjesec'];
	$godina = $_GET['godina'];
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title></title>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

</head>
<body>
<h2>Redirecting</h2>
<script>
document.location.href = "https://crm.job-step.com/finances?page=info&nalogid=<?php echo $nalogid; ?>&mjesec=<?php echo $mjesec; ?>&godina=<?php echo $godina; ?>"; 
</script>

</body>

</html>