<?php
	include "config/conn.php";

	if ($_POST["submit"] == "Reset" && $_POST["login"] && $_POST["passwd"] == $_POST["passwd2"] && strlen($_POST["passwd"]) > 5 && preg_match_all("/[0-9]/", $_POST["passwd"]) > 1)
	{

		try {
			$req = $conn->prepare("SELECT login, mail FROM account WHERE login = :login");
		    $req->execute(array(':login' => $_POST["login"]));
			$result = $req->fetch(PDO::FETCH_ASSOC);
			if ($req->rowCount() > 0 && $_POST["mail"] == hash("whirlpool", $result["mail"]))
			{
				$passwd = hash("whirlpool", $_POST["passwd"]);
				$req = $conn->prepare("UPDATE account SET passwd = :passwd WHERE login = :login");
				$req->execute(array(':login' => $_POST["login"], ':passwd' => $passwd));
				print("Reset success.");
			}
			else
				print("Reset fail.");
		}
		catch(PDOException $e) {
	    	echo "Error: ".$e->getMessage()."<br>";
			die();
		}
	}
	else if ($_GET["reset"])
	{
		?>
			<CENTER><table>
				<form name="reset.php" action="reset.php" method="post">
					Reset form
					<tr><td align="right">Login: </td><td><input type="text" name="login" /></td></tr>
					<tr><td align="right">Password: </td><td><input type="password" name="passwd" /></td></tr>
					<tr><td align="right">Confirm: </td><td><input type="password" name="passwd2" /></td></tr>
					<input type="hidden" name="mail" value="<?php print($_GET['reset']); ?>" />
					<tr><td colspan="2" align="center"><input type="submit" name="submit" value="Reset" /></td></tr>
				</form>
			</table></CENTER>
		<?php
	}
?>
