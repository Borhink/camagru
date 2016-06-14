<?php
include "header.php";
?>
<div id="center">
<?php

if (!$_SESSION["user"])
{
	?>

		<form method="post" action="index.php">
			<input type="submit" name="submit" value="Login">
			<input type="submit" name="submit" value="Register">
			<input type="submit" name="submit" value="Reset password">
		</form>
		<table>
			<?php
			if ($_POST["submit"] == "Register" || $_SESSION["lognew"])
			{
				?>
				<h3>REGISTRATION</h3>
				<?php if ($_SESSION["lognew"]) { echo("<font color='red'>".$_SESSION["lognew"]."</font>"); unset($_SESSION["lognew"]); } ?>
				<form name="create.php" action="create.php" method="post">
					<tr><td align="right">Login: </td><td><input type="text" name="login" /></td></tr>
					<tr><td align="right">Password: </td><td><input type="password" name="passwd" /></td></tr>
					<tr><td align="right">Confirm: </td><td><input type="password" name="passwd2" /></td></tr>
					<tr><td align="right">Mail: </td><td><input type="email" name="mail" /></td></tr>
					<tr><td colspan="2" align="right"><input type="submit" name="submit" value="Register" /></td></tr>
				</form>
				<?php
			}
			else if ($_POST["submit"] == "Reset password" || $_SESSION["logres"])
			{
				?>
				<h3>RESET PASSWORD</h3>
				<?php if ($_SESSION["logres"]) { echo("<font color='red'>".$_SESSION["logres"]."</font>"); unset($_SESSION["logres"]); } ?>
				<form name="login.php" action="login.php" method="post">
					<tr><td align="right">Login: </td><td><input type="text" name="login" /></td></tr>
					<tr><td colspan="2" align="right"><input type="submit" name="submit" value="Reset" /></td></tr>
				</form>
				<?php
			}
			else
			{
				?>
				<h3>LOGIN IN</h3>
				<?php if ($_SESSION["loglog"]) { echo("<font color='red'>".$_SESSION["loglog"]."</font>"); unset($_SESSION["loglog"]); } ?>
				<form name="login.php" action="login.php" method="post">
					<tr><td align="right">Login: </td><td><input type="text" name="login" /></td></tr>
					<tr><td align="right">Password: </td><td><input type="password" name="passwd" /></td></tr>
					<tr><td colspan="2" align="right"><input type="submit" name="submit" value="Login" /></td></tr>
				</form>
				<?php
			}
			?>
		</table>

	<?php
}
?>
</div>
<?php
include "footer.php";
?>
