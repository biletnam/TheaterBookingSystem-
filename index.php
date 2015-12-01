<?php
//EXTRA DEBUGGING STUFF TO REFORM THE DATABASE IF NEED BE.
include('debugging.php');//EXTRA DEBUGGING STUFF TO REFORM THE DATABASE IF NEED BE.

include('database/db_properties.php'); //gets the local db settings
include('database/DB.php'); //for the DB

$DB = new DB($servername, $username, $password, $dbname);
$DB->connect($recreate_database_from_new);

include('template.php');

//SET PAGE VARIABLES
$page_title = "Caspar's Theater::Home Page";
$page_description = "Home Page of Caspar's Theatre, come here to see the best shows in town. Online Booking Available NOW!";
$current_page = "index";

//START THE TEMPALTE
$Template = new Template($page_title, $page_description, $current_page);

$Template->pre_content();

//NOW FOR THE CONTENT
?>
<div class="post highlighted">
<img src="images/logo.png" width="280" align="left">
<h2>Welcome</h2>
<p>Welcome to Caspar's Theater. This is the home of good productions. We have every kind of show you could care to watch. Here is the place to be. The place to be is here. The place to be is not there, it's here. Here. From this website you can browse our productions, present, future and now! There is also a link to see which shows will be playing soon, in the near future, tonight and tomorrow and in the far future, as well as all in between now and then, and even more importantly, then and now! If you wish to book a show, you can choose which show to book first by going to the productions, or to the shows, or to the booking place, where you will be presented with a list, a huge list, a list of all, yes all, of our shows. Can you believe it, yes all of them are available for booking! Except of course the ones that are fully booked. Of course!</p>

<p>Below is a list of our most recent productions. Check back soon for more!</p>
</div>

<?php
$newest_productions = $DB->getNewestProductions();

foreach ($newest_productions as $production) {
	$Template->display_production($production, 0, FALSE);
}

//FINISH UP TEMPLATE
$Template->post_content();

$DB->close();

?>