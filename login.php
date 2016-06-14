<?php
	session_start();
	header('Location: index.php');

	include "config/conn.php";

	function auth($login, $passwd, $conn)
	{
		$req = $conn->prepare("SELECT passwd, valid FROM account WHERE login = :login");
	    $req->execute(array(':login' => $login));
		if ($req->rowCount() > 0)
		{
			$passwd = hash("whirlpool", $passwd);
			$res = $req->fetch(PDO::FETCH_ASSOC);
			if ($res["valid"] && $res["passwd"] == $passwd)
			{
				$_SESSION["user"] = $login;
				return (TRUE);
			}
		}
		$_SESSION["user"] = NULL;
		return (FALSE);
	}

	if ($_POST["submit"] == "Login")
	{
		try {
			if (!$_POST["login"] || !$_POST["passwd"] || !auth($_POST["login"], $_POST["passwd"], $conn))
				$_SESSION["loglog"] = "Login or password incorrect.";
		}
		catch(PDOException $e) {
	    	echo "Error: ".$e->getMessage()."<br>";
			die();
		}
	}
	else if ($_POST["submit"] == "Reset")
	{
		$_SESSION["logres"] = "Login not found.";
		if ($_POST["login"])
		{
			try {
				$req = $conn->prepare("SELECT mail FROM account WHERE login = :login");
			    $req->execute(array(':login' => $_POST["login"]));
				if ($req->rowCount() > 0)
				{
					$res = $req->fetch(PDO::FETCH_ASSOC);
					$hash = hash("whirlpool", $res["mail"]);
					mail($res["mail"], "Camagru - reset password" , "Hello ".$_POST["login"].",\r\nYou requested to reset your account's password, click the link bellow and choose your new password:\r\nhttp://localhost:8080/reset.php?reset=".$hash);
					$_SESSION["logres"] = "An email was sent to reset your password.";
				}
			}
			catch(PDOException $e) {
		    	echo "Error: ".$e->getMessage()."<br>";
				die();
			}
		}
	}
?>
