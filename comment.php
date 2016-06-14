<?php
include "header.php";
include "config/conn.php";
?>
<div id="center">
<CENTER>
<?php

if ($_POST["submit"] && $_POST["comment"] && $_POST["id"] && $_SESSION["user"] && $_GET["img"])
{
	try {
		$img_id = $_POST["id"];
		$date = time();
		$author = $_SESSION["user"];
		$req = $conn->prepare("INSERT INTO comment (author, text, img_id, date) VALUES ('$author', :text, '$img_id', '$date')");
		$req->execute(array(':text' => $_POST["comment"]));
		$name = $_GET["img"];
		$req = $conn->prepare("SELECT author FROM image WHERE id = '$img_id'");
		$req->execute();
		if ($req->rowCount())
		{
			$res = $req->fetch(PDO::FETCH_ASSOC);
			$login = $res["author"];
			$req = $conn->prepare("SELECT mail FROM account WHERE login = '$login'");
			$req->execute();
			if ($req->rowCount())
			{
				$res = $req->fetch(PDO::FETCH_ASSOC);
				mail($res["mail"], "Picture commented" , "Hello ".$login.",\r\n".$author." has post a comment on your photo, click the link bellow to see it:\r\nhttp://localhost:8080/comment.php?img=$name");
			}
		}
	}
	catch(PDOException $e) {
		echo "Error: ".$e->getMessage()."<br>";
		die();
	}
}

if ($_GET["img"])
{
	try {
		$name = $_GET["img"];
		$req = $conn->prepare("SELECT * FROM image WHERE name = :name");
		$req->execute(array(':name' => $name));
		if ($req->rowCount())
		{
			$res = $req->fetch(PDO::FETCH_ASSOC);
			$date = date('d/m/Y', $res["date"]).' at '.date('H:i:s', $res["date"]);
			echo "</br><img src='".$res['name']."' width='220'></br>";
			echo "author: ".$res["author"]." - creation date: ".$date."</br></br>";
			if ($_SESSION["user"])
			{
				?>
					<form action="comment.php?img=<?php echo $name ?>" method="post">
						<textarea name="comment" rows="6" cols="45" maxlength="500" placeholder="Your comment here..."></textarea></br>
						<input type="hidden" name ="id" value="<?php echo $res['id'] ?>">
						<input type="submit" name ="submit" value="Submit">
					</form></br></br>
				<?php
			}
			else
				echo "Login in to comment pictures</br></br>";
			$img_id = $res["id"];
			$req2 = $conn->prepare("SELECT * FROM comment WHERE img_id = $img_id ORDER BY date DESC");
			$req2->execute();
			$res2 = $req2->fetchAll(PDO::FETCH_ASSOC);
			foreach ($res2 as $comment) {
				$date = date('d/m/Y', $comment["date"]).' at '.date('H:i:s', $comment["date"]);
				echo $comment["author"].", ".$date." :</br><textarea rows='12' cols='45' readonly>".$comment["text"]."</textarea></br></br>";
			}
		}
	}
	catch(PDOException $e) {
		echo "Error: ".$e->getMessage()."<br>";
		die();
	}
}

?>
</CENTER>
</div>
<?php
include "footer.php";
?>
