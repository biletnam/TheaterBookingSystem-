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
					<h1><a href=\"/\">Caspar's Theatre</a></h1>
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

	function showGallery($title){
		foreach (range(1,4) as $i){
			echo "<image src=\"images/$title/$i.jpg\" class=\"gallery\" width=\"450px\" alt=\"Look at some wonderful photos of $title\">";
		}
	}

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
					$date = date('l, F jS o',strtotime(str_replace('-','/', $show['date_time'])));
					$link = "shows.php?show=".$show['id'];
					echo "<li><a href=\"$link\">$date</a>, $num_tickets, <a href=\"book.php?pid=$pid\">Book Now!</a></li>";
				}
			
			echo "</ul>";
			}//end show listing

		if ($show_gallery){
			$this->showGallery($title);
		}//end gallery
		}
		echo "
		</div>";
	}

	//////////////////
	// Show
	function display_performance($performance, $DB=null, $LONG = FALSE) {
		$title = $performance['title'];
		$date_time = str_replace('-','/', $performance['date_time']);
		$id = $performance['id'];
		$description = $performance['description'];
		$mins = $performance['mins'];
		$genre = $performance['genre'];

		//process data
		$heading = date('l, F jS o', strtotime($date_time))." ".$title;
		$num_tickets = $this->writeTicketsAvailable(sizeof($DB->getTicketsAvailable($id)));
		
		if (!$LONG){
			$desc = $this->shortenText($description);
			
			//TODO give this a query that works the way it ex[ects]
			//render data


			echo "<div class=\"post show\">
				<h2><a href=\"shows.php?show=$id\">$heading</a></h2>
				<p>$desc</p>
				<p>$num_tickets</p>
				<p><a href=\"book.php?pid=$id\">Book Now!</a></p>
				</div>";
		}
		else {
			echo "<div class=\"post show\">
				<h2><a href=\"shows.php?show=$id\">$heading</a></h2>
				<p>$description</p>
				<p>$num_tickets</p>
				<p><a href=\"book.php?pid=$id\">Book Now!</a></p>";

			$this->showGallery($title);
			echo "</div>";
		}


	}

	////////////////////////////////
	// BOOKING FORM TEMPLATES
	////////////////////////////////

	function process_booking_form($performance, $seats, $customer_name, $email, $DB){
		//Takes in booking information. If this information is blank
		$has_error = FALSE;
		$error_messages = "";
		///MUST HAVE A NAME
		if ($customer_name == NULL){
			$has_error = TRUE;
			$error_messages.="Please provide a name for the booking. ";
		}
		//MUST BE A AN EMAIL
		if ($email == NULL){
			$has_error = TRUE;
			$error_messages.="Please provide an email address. ";
		}
		elseif (!filter_var($email, FILTER_VALID_EMAIL)){ //AND VALID
			$has_error = TRUE;
			$error_messages .= "Please provide a valid email address. ";
		}
		//MUST HAVE VALID SEATS 
		//lets also calculate a cost
		$cost = 0;
		if (sizeof($seats)<1){ //MUST BOOK AT LEAST ONE SEAT
			$has_error = TRUE;
			$error_messages .= "Please choose at least one seat to book. "
		}
		else { //EACH SEAT MUST BE BOOKABLE
			foreach ($seats as $seat){
				if ($DB->seatBooked(intval($performance['id']), $seat)){
					$has_error = TRUE;
					$error_messages .= "Error: Seat ($seat) Unavailable. ";
				}
				else {
					$cost += $DB->getCostOfSeat($performance['id'], $seat);
				}
			}
		}
			
		if ($has_error){
			$this->display_booking_form($DB, $performance, $seats, $customer_name, $email, $error_messages, $cost);
		}
		else {
			//here we actually book the seats
			$success = $DB->bookSeats(intval($performance['id']), $seats, $customer_name, $email);
			if ($success){
				$this->booking_success($DB, $customer_name, $email, $seats, $cost);
			}
			else{
				$this->booking_fail($DB);
			}
		}
		
	}
	
	function display_booking_form($DB, $performance, $seats = array(), $customer_name = '', $email = '',  $error_messages = array(), $cost = 0.00) {
		//pre-process the data
		$title = $performance['title'];
		$date_time = $performance['date_time'];
		$pid = $performance['id'];
		$perf = $title. " ".date('l, F jS o',strtotime(str_replace('-','/', $date_time)));
		
		echo "<form action=\"book.php\" method=\"POST\">
			<h1>Booking Form</h1>
			<h2>Booking seats for: $perf</h2>
			<input type=\"hidden\" name=\"pid\" value=$pid>
			<b></b><br>";
		if ($error_messages){
			echo "<p>$error_messages</p>";
		}
		
		$this->ticket_selection($pid, $DB, $seats);
		
		echo "<h3>Your Name:</h3>
			<input type=\"text\" name=\"customer_name\" value=\"$customer_name\">
			<h3>Your Email:</h3>
			<input type=\"text\" name=\"email\" value=\"$email\">
			<br>
			<span id=\"total\">TOTAL: &pound;$cost</span>
			<script type=\"text/javascript\">
				var total = $cost;
				updateTotal = function (row, val){
					if (document.getElementById(row).checked){
						total += val;
					}
					else {
						total -= val;
					}
					document.getElementById(\"total\").innerHTML =\"TOTAL: &pound;\"+ total;
				}
			</script>
			<br>
			<input type=\"submit\" value=\"Book\">
			</form>";			
			
	}

	///////////
	//for use within the dispay booking form
	
	private function ticket_selection($pid, $DB, $selected_seats = array()){
		$avail = $DB->getTicketsAvailableByZone($pid);
		$nos = range(1,20);
		foreach ($avail as $zone => $zone_info) {
			$Zone = ucwords($zone);
			$price = $zone_info["price"];
			$description = $zone_info["description"];
			echo "<h3>$Zone - &pound;$price</h3>";
			echo "<p>$description</p>";
			echo "<table>";
			ksort($zone_info["rows"]);
			foreach ($zone_info["rows"] as $row => $seats){
				echo "<tr><td class=\"row-identifier\">$row</td>";
				foreach ($seats as $no => $seat_is_available) {
					$row_no = $row.str_pad($no, 2, "0", STR_PAD_LEFT);
					if ($DB->seatExists($row_no, $zone)){
						if ($seat_is_available){
							$checked = "";
							if (in_array($row_no,$selected_seats)){$checked = " checked";}
							echo "<td class=\"booking available\">";
							echo "<input type=\"checkbox\" 
										 id =\"$row_no\" 
										 name=\"$row_no\" 
										 value=\"1\" $checked
										 onclick=\"updateTotal('$row_no', $price)\">";
						}
						else {
							echo "<td class=\"booking booked\">";
							echo "X";
						}
						echo "</td>";
					}
				}
				echo "</tr>";
			}
			echo "</table>";
		}
	}

	private function booking_success($DB, $name, $email, $seats, $cost){
		echo "
		<div class=\"post highlighted\">
		<img src=\"images/logo.png\" height=\"100\" align=\"left\">
		<h2>Thanks $name, Your booking has been made</h2>
		<p>Your booking has been successful! Thanks you'll have a wonderful time we know we will ;)</p>
		<p>Please book another show with us below</p>
		</div>";
		//TODO add seats into confirmation
		$next_shows = $DB->getNextPerformances(50);
		foreach ($next_shows as $show) {
			$this->display_performance($show, $DB);
		}
	}

	private function booking_fail($DB){
		echo "
		<div class=\"post highlighted\">
		<img src=\"images/logo.png\" height=\"100\" align=\"left\">
		<h2>BOOO!! your booking was not successful</h2>
		<p>We're sorry, please contact us by telephone to figure this out with us.</p>
		</div>";
		$next_shows = $DB->getNextPerformances(50);
		foreach ($next_shows as $show) {
			$Template->display_performance($show, $DB);
		}
	}
}//end class template