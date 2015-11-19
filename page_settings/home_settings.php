<?php

include('connect_to_database.php');

$page_title = "Home Page Title";

$page_content = "home_content.php";

$page_description = "Home Page of Caspar's Theatre, come here to see the best shows in town. Online Booking Available NOW!";

//gets the performances in the order
$newest_performances_sql = "
SELECT DISTINCT pr.* 
FROM Production pr
  JOIN Performance pe on pe.title = pr.title
ORDER BY pe.date_time DESC
LIMIT 10;
";
$newest_performances = $conn->query($newest_performances_sql)



?>