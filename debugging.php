<?php

$debug = FALSE;
$recreate_database_from_new = ($debug && isset($_GET["new_db"]) && $_GET["new_db"]=="YeS") ? TRUE : FALSE;

?>