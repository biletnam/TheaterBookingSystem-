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

	private function writeTicketsAvailable($num_tickets){
		if ($num_tickets == 0){
			return "Sold Out!";
		}
		elseif ($num_tickets>50) {
			return "Tickets are still available ($num_tickets).";
		}
		elseif ($num_tickets == 1) {
			return "Only 1 ticket left.";
		}
		else{
			return "Only $num_tickets tickts left.";
		}
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
					$pid = $show['id'];
					$num_tickets = $this->writeTicketsAvailable(sizeof($DB->getTicketsAvailable($pid)));
					//var_dump($num_tickets);
					$date = date('l, F jS o',strtotime(str_replace('-','/', $show['date_time'])));
					$link = "shows.php?show=".$show['id'];
					echo "<li><a href=\"$link\">$date</a>, $num_tickets, <a href=\"book.php?pid=$pid\">Book Now!</a></li>";
				}
			
			echo "</ul>";
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
	function display_performance($performance, $DB=null) {
		$title = $performance['title'];
		$date_time = str_replace('-','/', $performance['date_time']);
		$id = $performance['id'];
		$description = $performance['description'];
		$mins = $performance['mins'];
		$genre = $performance['genre'];

		//process data
		$heading = date('l, F jS o', strtotime($date_time))." ".$title;
		$desc = $this->shortenText($description);
		$num_tickets = $this->writeTicketsAvailable(sizeof($DB->getTicketsAvailable($id)));
		//TODO give this a query that works the way it ex[ects]
		//render data


		echo "<div class=\"post show\">
			<h2><a href=\"shows.php?show=$id\">$heading</a></h2>
			<p>$desc</p>
			<p>$num_tickets</p>
			<p><a href=\"book.php?pid=$id\">Book Now!</a></p>
			</div>";

		// $tickets_sold = $DB->getSoldTickets($id);
		// var_dump($tickets_sold);

	}
	
	function process_booking_form($performance, $seats, $customer_name, $DB){
		$has_error = FALSE;
		$error_messages = array();
		try {
			$available_seats = $DB->getTicketsAvailable(intval($performance['id']));
			$available_seat_row_nos = array();
			foreach ($available_seats as $a) {
				array_push($available_seat_row_nos, $a['row_no']);
			}
			foreach ($seats as $seat) {
				if (!in_array($seat, $available_seats)){
					$has_error = TRUE;
					$e_name = 'seats_error';
					if (array_key_exists($e_name)){
						$error_messages[$e_name] = "Error: Seat Unavailable ($seat)";
					}
					else {
						$error_messages[$e_name] .= "($seat)";
					}
				}
				echo "loop";
			}
		}
		catch (Exception $ex){
			echo "Could not get available seats";
			$has_error = TRUE;
		}
		
		if ($has_error){
			$this->display_booking_form($DB, $performance, $seats, $customer_name, $error_messages);
		}
		else {
			//here we actually book the seats
			echo "Some seats it appears could be booked. still to implement";
		}
		
	}
	
	function display_booking_form($DB, $performance, $seats = array(), $customer_name = '', $error_messages = array()) {
		//pre-process the data
		$title = $performance['title'];
		$date_time = $performance['date_time'];
		$pid = $performance['id'];
		$num_seats = sizeof($seats);
		if ($num_seats==0) {$num_seats = 2;}
		
		echo "<form action=\"book.php\" method=\"POST\">
			Performance:<br>
			<input type=\"hidden\" name=\"pid\" value=$pid>
			<b>$title on $date_time</b><br>";
		
		$this->ticket_selection($pid, $DB);
		
		echo "Your Name:<br>
			<input type=\"text\" value=\"$customer_name\">
			<br><br>
			<input type=\"submit\" value=\"Book\">
			</form>";			
			
	}
	
	function ticket_selection($pid, $DB){
		$avail = $DB->getTicketsAvailableByZone($pid);
		$nos = range(1,20);
		foreach ($avail as $zone => $zone_info) {
			$price = $zone_info["price"];
			echo "<h3>$zone - &pound$price</h3>";
			echo "<table>";
			foreach ($zone_info["rows"] as $row => $seats){
				echo "<tr><td>$row</td>";
				foreach ($seats as $no => $seat_is_available) {
					if ($seat_is_available){
						$row_no = $row.$no;	
						echo "<td class=\"available\">";
						echo "<input type=\"checkbox\" name=\"$row_no\" value=\"1\">";
						//echo $row_no."<br>$".$price;
					}
					else {
						echo "<td class=\"booked\">";
						echo "X";
					}
					
					echo "</td>";
				}
				
				echo "</tr>";
			}
			echo "</table>";
		}
	}
}//end class template