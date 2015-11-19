<?php
//EXTRA DEBUGGING STUFF TO REFORM THE DATABASE IF NEED BE.
$debug = TRUE;
$recreate_database_from_new = ($debug && isset($_GET["new_db"]) && $_GET["new_db"]=="YeS") ? TRUE : FALSE;

function stripUri($uri){
	$strip_after = "?";
	$pos = strpos($uri, "?");
	if ($pos!=0) {$uri = substr($uri,0, $pos);}
	return $uri;
}


function getCurrentUri()
{
	$uri = stripUri($_SERVER['REQUEST_URI']);
	$uri = array_slice(explode("/",$uri),1); //first one seems to be blank always
	return $uri;
}

$valid_pages = array( //Also used for the menu
	//url => Menu Item
	'home' => 'Home',
	'productions' => 'Current Productions',
	'shows' => 'Upcoming Shows',
	'book' => 'Book Seats',
	'location' => 'Location',
);

$uri = getCurrentUri();

function getCurrentPage($uri, $valid_pages){
	if (array_key_exists($uri[0], $valid_pages)) {
		return $uri[0];
	}
	else {
		return 'home';
	}
}

$page = getCurrentPage($uri, $valid_pages);


$script = 'page_settings/'.$page.'_settings.php';
if (file_exists($script)) {
	include($script);
}
else {
	include('page_settings/home_settings.php');
	$error = "The requested page does not exist, you are viewing the home page";
}


?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $page_title; ?></title>
		<link rel="stylesheet" href="/styles/style.css">
		<meta name="keywords" content="Caspars Theater, Bookings, Theater, Seats, Shows, Musicals, Plays">
		<meta name="description" content="<?php echo $page_description; ?>">
		<meta name="author" content="Caspar Nonclercq">
	</head>
	<body>
		<div id="wrapper">
			<div id="header">
				<h1><a href="">Caspar's Theatre</a></h1>
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
							echo "<li$current_page_item><a href=\"/$menu_link\">$menu_link_name</a></li>\n";
						}

						foreach ($valid_pages as $menu_link => $menu_link_name){
							make_menu_item($menu_link, $menu_link_name);
						}

					?>
				</ul>
			</div>

			<div style="clear: both;"></div>

			<div id="content">

				<?php 
					if (isset($error)) echo $error;
					include('page_content/'.$page_content);
				?>
			</div>

			<div style="clear: both;"></div>

			<div id="footer">
				<p>Created by Caspar Nonclercq, cn249@kent.ac.uk</p>
				<p>Copyright 2015</p>
			</div>
		</div>

	</body>
</html> 

<?php if (isset($conn)) {$conn->close();} ?>
