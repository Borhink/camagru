<?php
session_start();
date_default_timezone_set("Europe/Paris");
?>
<html>
<head>
	<link rel="stylesheet" href="css/style.css" type="text/css">
	<title>Camagru</title>
</head>
<body>
	<CENTER><div id="header">
		<h1>Camagru</h1>
		<?php
		if ($_SESSION["user"])
		{
			echo "Logged as ".$_SESSION["user"]." : ";
			?><a href="logout.php" class="link2">Logout</a><?php
		}
		?>
	</div>
	<div id="nav">
		<table align="center">
			<tr>
				<td><div class="button1"><a href="index.php" class="link1">Index</a></div></td>
				<td><div class="button1"><a href="newpic.php" class="link1">New Pic</a></div></td>
				<td><div class="button1"><a href="gallery.php" class="link1">Gallery</a></div></td>
			</tr>
		</table>
	</div>
