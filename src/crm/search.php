<?php
	include("includes/functions.php");
	include("includes/common.php");

	$query = $_GET['q'];

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php getTitle(); ?></title>

	<?php include('includes/head.php'); ?>

</head>
<body>
	<header>
		<?php include('header.php'); ?>
	</header>
	<div id="sidebar">
		<?php include('menu.php'); ?>
	</div>
	<div id="content">
		<div class="container-fluid">
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-search idk_color_green" aria-hidden="true"></i> Pretraga: <?php echo $query; ?></h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10"></div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-xs-12">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>Rezultati pretrage:</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$search_query = $db->prepare("
																SELECT search_text, search_tags, search_link
																FROM idk_search
																WHERE search_tags LIKE :query
																ORDER BY search_text ASC");

											$search_query->execute(array(
																':query' => '%'.$query.'%'));

											while($search = $search_query->fetch()){

												$search_text = $search['search_text'];
												$search_link = $search['search_link'];
										?>
										<tr>
											<td><a href="<?php getSiteURL(); ?><?php echo $search_link; ?>"><?php echo $search_text; ?></a></td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>
</html>
