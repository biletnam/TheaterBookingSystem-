<?php

class Template {
	private $menu_items = array(
				'index' => 'Home',
				'productions' => 'Current Productions',
				'shows' => 'Upcoming Performances',
				'book' => 'Book Seats',
				);

	private $page_title = "Caspar's Theater";
	private $page_description = "Welcome to Caspar's Theater";
	private $current_page = "index";

	public function __construct($page_title, $page_description, $current_page) {
		$this->page_title = $page_title;
		$this->page_description = $page_description;
		$this->current_page = $current_page;
	}

	private function open_html(){
		echo "<!DOCTYPE html>
				<html>";
	}
		
	private function head() {
		echo "
			<head>
				<title>$this->page_title</title>
				<link rel=\"stylesheet\" href=\"styles/style.css\">
				<meta name=\"keywords\" content=\"Caspars Theater, Bookings, Theater, Seats, Shows, Musicals, Plays\">
				<meta name=\"description\" content=\"$this->page_description\">
				<meta name=\"author\" content=\"Caspar Nonclercq\">
			</head>";
	}

	private function open_body() {echo "<body>";}

	private function heading() {
		echo "<div id=\"wrapper\">
				<div id=\"header\">
					<h1><a href=\"\">Caspar's Theatre</a></h1>
					<p>The beginning of something beautiful...</p>
				</div>
				<div style=\"clear: both;\"></div>";
	}

	private function menu() {
		echo "<div id=\"menu\">
				<ul class=\"main-menu\">";
		$this->menu_items();
		echo "</ul>
			</div>

			<div style=\"clear: both;\"></div>";
	}

	private function menu_items() {
		$menu = "";
		foreach ($this->menu_items as $menu_link => $menu_link_name){
			$menu .= $this->make_menu_item($menu_link, $menu_link_name);
		}
		echo $menu;
	}

	private function make_menu_item($menu_link, $menu_link_name) {
			if ($menu_link == $this->current_page) {
				$current_page_item = " class=\"current_page_item\"";
			}
			else {
				$current_page_item = '';
			}
			return "<li$current_page_item><a href=\"$menu_link.php\">$menu_link_name</a></li>\n";
		}

	private function open_content() {echo "<div id=\"content\">";}

	private function close_content() {
		echo "</div>
		<div style=\"clear: both;\"></div>";
	}

	private function footer() {
		echo "<div id=\"footer\">
					<p>Created by Caspar Nonclercq, cn249@kent.ac.uk</p>
					<p>Copyright 2015</p>
				</div>";
	}

	private function close_body() {echo "</body>";}

	private function close_html() {echo "</html>";}

	/////////////////
	// BIG WRAPPERS
	/////////////////

	public function pre_content() {
		$this->open_html();
		$this->head();
		$this->open_body();
		$this->heading();
		$this->menu();
		$this->open_content();
	}

	public function post_content() {
		$this->close_content();
		$this->footer();
		$this->close_body();
		$this->close_html();
	}

	////////////////////
	// UTILITY FUNCTIONS
	////////////////////

	private function shortenText($text, $length = 150) {
		if (strlen($text) <= $length) {return $text;}
		$cut_on = strpos($text, ' ', $length);
		return substr($text,0, $cut_on)."...";
	}

	////////////////////////////////
	// CONTENT SPECFIC FUNCTIONS
	////////////////////////////////

	//////////////////////////
	//Production
	function display_production($production, $num_shows_to_display, $show_gallery, $DB=NULL) {
		//get production propeties
		$title = $production['title'];
		$url = $production['url'];
		$description = $production['description'];
		$mins = $production['mins'];
		$genre = $production['genre'];

		$cover_image_src = "images/$title/cover.jpg";
		$coverimage = "<img src=\"$cover_image_src\" height=\"300\" align=\"right\">";

		echo "<div class=\"post production\">
			$coverimage
			<h2><a href=\"productions.php?production=$url\">$title</a></h2>
			<p>$description</p>
			<ul class=\"production-details\">
				<li>Runtime: $mins minutes</li>
				<li>Genre: $genre</li>
			</ul>";

		if ($num_shows_to_display > 0 && $DB) {
			echo "<h3><a href=\"productions.php?production=$url\">Next Performances</a></h3>
			<ul>";

			$next_performances = $DB->getProductionsNextPerformances($title, $num_shows_to_display);
			
			if ($next_performances){
				foreach($next_performances as $show) {
					$num_tickets = $DB->getNumTicketsAvailable($show['id']);
					//var_dump($num_tickets);
					$date = date('l, F jS o',strtotime(str_replace('-','/', $show['date_time'])));
					$link = "shows.php?show=".$show['id'];
					echo "<li><a href=\"$link\">$date</a></li>";
				}
			}//end show listing

		if ($show_gallery){
			echo "<p>A very pretty gallery</p>";
		}//end gallery
		}
		echo "
		</div>";
	}

	//////////////////
	// Show
	function display_performance($performance, $DB= null) {
		$title = $performance['title'];
		$date_time = str_replace('-','/', $performance['date_time']);
		$id = $performance['id'];
		$description = $performance['description'];
		$mins = $performance['mins'];
		$genre = $performance['genre'];

		//process data
		$heading = date('l, F jS o', strtotime($date_time))." ".$title;
		$desc = $this->shortenText($description);

		//render data

		echo "<div class=\"post show\">
			<h2><a href=\"shows.php?show=$id\">$heading</a></h2>
			<p>$desc</p>
			</div>";

		$tickets_available = $DB->getTicketsAvailable($id);
		var_dump($tickets_available);
	}
}//end class template