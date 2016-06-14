<?php
include "header.php";
include "config/conn.php";
?>
<div id="center">
<table>
<?php

if ($_GET["like"])
{
	if ($_SESSION["user"])
	{
		try {
			$name = $_GET["like"];
			$req = $conn->prepare("SELECT name, likes FROM image WHERE name = :name");
			$req->execute(array(':name' => $name));
			if ($req->rowCount())
			{
				$res = $req->fetch(PDO::FETCH_ASSOC);
				$regex = "/".$_SESSION["user"].";/";
				if (preg_match($regex, $res["likes"]))
					$likes = preg_replace($regex, "", $res["likes"]);
				else
					$likes = $res["likes"].$_SESSION["user"].";";
				$req = $conn->prepare("UPDATE image SET likes = '$likes' WHERE name = :name");
				$req->execute(array(':name' => $name));
			}
		}
		catch(PDOException $e) {
			echo "Error: ".$e->getMessage()."<br>";
			die();
		}
	}
	else
		echo "Login in to like pictures</br>";
}
else if ($_GET["del"])
{
	try {
		$name = $_GET["del"];
		$req = $conn->prepare("SELECT name, id, author FROM image WHERE name = :name");
		$req->execute(array(':name' => $name));
		if ($req->rowCount())
			$res = $req->fetch(PDO::FETCH_ASSOC);
		if ($_SESSION["user"] == $res["author"])
		{
			if (file_exists("gallery") && file_exists($name))
				unlink($name);
			$img_id = $res["id"];
			$req = $conn->prepare("DELETE FROM comment WHERE img_id = $img_id");
			$req->execute();
			$req = $conn->prepare("DELETE FROM image WHERE name = :name");
			$req->execute(array(':name' => $name));
		}
		else
			echo "You can't delete pictures you don't own</br>";
	}
	catch(PDOException $e) {
		echo "Error: ".$e->getMessage()."<br>";
		die();
	}
}

try {
	$req = $conn->prepare("SELECT * FROM image ORDER BY date DESC");
	$req->execute();
	for ($i = 0; $i < $req->rowCount();)
	{
		$res = $req->fetch(PDO::FETCH_ASSOC);
		$img_id = $res["id"];
		$req2 = $conn->prepare("SELECT * FROM comment WHERE img_id = $img_id");
		$req2->execute();
		$coms = $req2->rowCount();
		$likes = preg_match_all("/;/", $res["likes"]);
		if ($i % 3 == 0)
			echo "<tr>";
		echo "<td><CENTER><img src='".$res["name"]."' class='images'></br>";
		echo "Author: ".$res["author"];
		if ($res["author"] == $_SESSION["user"])
			echo " <a class='link2' href='gallery.php?del=".$res["name"]."'>(Delete)</a>";
		if ($likes > 1)
			echo "</br><a class='link2' href='gallery.php?like=".$res["name"]."'>".$likes." likes</a>";
		else
			echo "</br><a class='link2' href='gallery.php?like=".$res["name"]."'>".$likes." like</a>";
		if ($coms > 1)
			echo " - <a class='link2' href='comment.php?img=".$res["name"]."'>".$coms." comments</a>";
		else
			echo " - <a class='link2' href='comment.php?img=".$res["name"]."'>".$coms." comment</a>";
		echo "</td></CENTER>";
		$i++;
		if ($i % 3 == 0 || $i == $req->rowCount())
			echo "</tr>";
	}
}
catch(PDOException $e) {
	echo "Error: ".$e->getMessage()."<br>";
	die();
}
?>
</table>
</div>
<?php
include "footer.php";
?>
