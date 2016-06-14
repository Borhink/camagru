<?php
	include "config/conn.php";

	if ($_GET["login"])
	{
		$login = $_GET["login"];
		try {
			$req = $conn->prepare("SELECT login, valid FROM account WHERE login = :login");
		    $req->execute(array(':login' => $login));
			$result = $req->fetch(PDO::FETCH_ASSOC);
			if ($req->rowCount() > 0)
			{
				if (!$result["valid"])
				{
					$req = $conn->prepare("UPDATE account SET valid = 1 WHERE login = :login");
				    $req->execute(array(':login' => $login));
					echo "Account confirm success for $login.<br>";
				}
				else {
					echo "Account already confirmed for $login.<br>";
				}
			}
		}
		catch(PDOException $e) {
	    	echo "Error: ".$e->getMessage()."<br>";
			die();
		}
	}
?>
