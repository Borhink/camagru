<?php
	include "database.php";
	$DB_NAME = "camagru_qhonore";

	try {
	    $conn = new PDO($DB_DSN."dbname=$DB_NAME", $DB_USER, $DB_PASSWORD);
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e) {
	    echo $sql."<br>".$e->getMessage()."<br>";
		die();
	}
?>
