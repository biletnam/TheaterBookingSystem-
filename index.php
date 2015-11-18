<?php

if (isset($_GET['page'])) {
	$page = 'home';
}
else {
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

		<?php include($page_content); ?>

	</body>
</html> 