<?php

$valid_pages = array( //Also used for menu
	'home' => 'Home',
	'production' => 'Current Productions',
	'shows' => 'Upcoming Shows',
	'book' => 'Book Seats',
	'location' => 'Location',
	);

$page = 'home';
if (isset($_GET['page'])) {
	if (array_key_exists($_GET['page'], $valid_pages)) {
		$page = $_GET['page'];
	}
}

//when in production, this should revert to $page = 'home'
//if there is an error
include($page.'_settings.php'); //get the page settings
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
		<div id="wrapper">
			<div id="header">
				<h1><a href="/">Caspar's Theatre</a></h1>
				<p>The beginning of something beautiful...</p>
			</div>
			<div style="clear: both;"></div>

			<div id="menu">
				<ul class="main-menu">
					<?php

					function make_menu_item($menu_link, $menu_link_name) {
						global $page;
						if ($menu_link == $page) {
							$current_page_item = " class=\"current_page_item\"";
						}
						else {
							$current_page_item = '';
						}
						echo "<li$current_page_item><a href=\"/?page=$menu_link\">$menu_link_name</a></li>\n";
					}

					foreach ($valid_pages as $menu_link => $menu_link_name){
						make_menu_item($menu_link, $menu_link_name);
					}

					?>
				</ul>
			</div>

			<div id="content">
				<?php include($page_content); ?>
			</div>

			<div id="footer">
				<p>Created by Caspar Nonclercq, cn249@kent.ac.uk</p>
				<p>Copyright 2015</p>
			</div>
		</div>

	</body>
</html> 