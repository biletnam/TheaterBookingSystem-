<?php

$page = 'home';
if (isset($_GET['page'])) {
	$page = 'home';
}

include($page.'_settings.php');


?>


<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $page_title; ?></title>
		<link rel="stylesheet" href="styles/style.css">
		<meta name="keywords" content="Caspars Theater, Bookings, Theater, Seats, Shows, Musicals, Plays">
		<meta name="description" content="<?php echo $page_description; ?>">
		<meta name="author" content="Caspar Nonclercq">
	</head>
	<body>
		<div id="header">
			<h1><a href="/">Caspar's Theatre</a></h1>
			<p>The beginning of something beautiful...</p>
		</div>

		<div id="menu">
			<?php include('main_menu.php'); ?>
		</div>

		<div id="content">
			<?php include($page_content); ?>
		</div>

		<div id="footer">
			<p>Created by Caspar Nonclercq, cn249@kent.ac.uk</p>
			<p>Copyright 2015</p>
		</div>

	</body>
</html> 