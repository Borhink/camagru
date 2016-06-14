<?php
	include "database.php";
	$DB_NAME = "camagru_qhonore";

	try {
	    $conn = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
	    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    $sql = "CREATE DATABASE IF NOT EXISTS $DB_NAME";
	    $conn->exec($sql);
	    $sql = "USE $DB_NAME;
				CREATE TABLE IF NOT EXISTS account (
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				login VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				passwd VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				mail VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				valid TINYINT(1) NOT NULL)";
	    $conn->exec($sql);
	    $sql = "USE $DB_NAME;
				CREATE TABLE IF NOT EXISTS image (
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				author VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				likes TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,
				date INT(14) NOT NULL)";
	    $conn->exec($sql);
	    $sql = "USE $DB_NAME;
				CREATE TABLE IF NOT EXISTS comment (
				id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				author VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				text TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
				img_id INT NOT NULL,
				date INT(14) NOT NULL)";
	    $conn->exec($sql);
		echo "Setup done";
	}
	catch(PDOException $e) {
	    echo $sql."<br>".$e->getMessage()."<br>";
		die();
	}
	$conn = null;
?>
