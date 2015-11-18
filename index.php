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
	</head>
	<body>

		<?php include($page_content); ?>

	</body>
</html> 