<?php
	session_start();
	header('Location: index.php');

	include "config/conn.php";

	function add_user($login, $mail, $passwd, $conn)
	{
		if (strlen($login) < 4)
			$_SESSION["lognew"] = "Error: login lenght < 4.";
		else if (!ctype_alnum($login))
			$_SESSION["lognew"] = "Error: login must be alphanumeric.";
		else if (strlen($passwd) < 6)
		 	$_SESSION["lognew"] = "Error: password lenght < 6.";
		else if (preg_match_all("/[0-9]/", $passwd) < 2)
			$_SESSION["lognew"] = "Error: password contain less than 2 num.";
		else if (!filter_var($mail, FILTER_VALIDATE_EMAIL))
			$_SESSION["lognew"] = "Error: email isn't valid.";
		$req = $conn->prepare("SELECT login FROM account WHERE login = :login");
		$req->execute(array(':login' => $login));
		if ($req->rowCount() > 0)
			$_SESSION["lognew"] = "Error: login already used.";
		$req = $conn->prepare("SELECT login FROM account WHERE mail = :mail");
		$req->execute(array(':mail' => $mail));
		if ($req->rowCount() > 0)
			$_SESSION["lognew"] = "Error: email already used.";
		if ($_SESSION["lognew"])
			return (FALSE);
		$passwd = hash("whirlpool", $passwd);
		$req = $conn->prepare("INSERT INTO account (login, mail, passwd, valid) VALUES (:login, :mail, :passwd, 0)");
		$req->execute(array(':login' => $login, ':mail' => $mail, ':passwd' => $passwd));
		mail($mail, "Registration to Camagru" , "Hello ".$login.",\r\nYou successfully registered to Camagru. To complete, click the link bellow :\r\nhttp://localhost:8080/confirm.php?login=$login");
		return (TRUE);
	}

	if ($_POST["submit"] == "Register")
	{
		try {
			if ($_POST["passwd"] != $_POST["passwd2"])
			 	$_SESSION["lognew"] = "Error: the passwords aren't the sames.";
			else if (add_user($_POST["login"], $_POST["mail"], $_POST["passwd"], $conn))
				$_SESSION["lognew"] = "Account created. Please confirm your email.";
		}
		catch(PDOException $e) {
			echo "Error: ".$e->getMessage()."<br>";
			die();
		}
	}

?>
