<?php

$menu_items = array(
	'index' => 'Home',
	'productions' => 'Current Productions',
	'shows' => 'Upcoming Shows',
	'book' => 'Book Seats',
	);

function template_top($page_title, $page_description, $menu) {

	return "
	<!DOCTYPE html>
	<html>
		<head>
			<title><?php echo $page_title; ?></title>
			<link rel=\"stylesheet\" href=\"styles/style.css\">
			<meta name=\"keywords\" content=\"Caspars Theater, Bookings, Theater, Seats, Shows, Musicals, Plays\">
			<meta name=\"description\" content=\"<?php echo $page_description; ?>\">
			<meta name=\"author\" content=\"Caspar Nonclercq\">
		</head>
		<body>
			<div id=\"wrapper\">
				<div id=\"header\">
					<h1><a href=\"\">Caspar's Theatre</a></h1>
					<p>The beginning of something beautiful...</p>
				</div>
				<div style=\"clear: both;\"></div>

				<div id=\"menu\">
					<ul class=\"main-menu\">
						$menu
					</ul>
				</div>

				<div style=\"clear: both;\"></div>

				<div id=\"content\">

	";
}

function make_menu_item($menu_link, $menu_link_name, $is_current_page) {
		if ($is_current_page) {
			$current_page_item = " class=\"current_page_item\"";
		}
		else {
			$current_page_item = '';
		}
		return "<li$current_page_item><a href=\"$menu_link.php\">$menu_link_name</a></li>\n";
	}

function menu($menu_items, $current_page) {
	$menu = "";
	foreach ($menu_items as $menu_link => $menu_link_name){
		$menu .= make_menu_item($menu_link, $menu_link_name, $current_page == $menu_link);
	}
	return $menu;
}

function template_bottom(){
	return "
			<div style=\"clear: both;\"></div>
		
			<div id=\"footer\">
				<p>Created by Caspar Nonclercq, cn249@kent.ac.uk</p>
				<p>Copyright 2015</p>
			</div>
		</div>

		</body>
	</html>";
}