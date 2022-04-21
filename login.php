<?php

	session_start();

	require_once "tmp/db.php";

	if (isUserAuthenticated() OR isAdminAuthenticated()) {
		header("location: index.php");
	}

	if (!empty($_POST["isSubmit"])) {
		if (!empty($_POST["email"] & $_POST["password"])) {
			$resultAdmin = findAdminByEmail($_POST["email"]);
			$resultUser = findUserByEmail($_POST["email"]);
			if ($resultAdmin->rowCount() > 0) {
				$row = $resultAdmin->fetch();
				$verified = verifyPasswordHash($_POST["password"], $row['password']);
				if ($verified) {
               		$_SESSION["firstname"] = $row['firstname'];
	                $_SESSION["surname"] = $row['surname'];
	                $_SESSION["email"] = $row['email'];
	                $_SESSION["id"] = $row['id'];

	                $resultVerification = showAdminName();
					$rowVerification = $resultVerification->fetch();
	                if ($rowVerification["verified"] == 0) {
	                	echo "<script>alert('Váš účet stále nebyl ověřen. Pokud je Váš odkaz k ověření zastaralý, zajděte do uživatelského nastavení a vyžádejte si nový.')</script>";
	                	?>
				        <meta http-equiv="refresh" content="0;url=index.php">
				        <?php
	                } else {
	                	header("location: index.php");
	                }
				} else {
					writeErrorMessage("*Vaše emailová adresa nebo heslo se neshoduje. Zkuste to prosím znovu.");
				}
			} elseif ($resultUser->rowCount() > 0) {
				$row = $resultUser->fetch();
				$verified = verifyPasswordHash($_POST["password"], $row['password']);
				if ($verified) {
					$_SESSION["firstnameUser"] = $row['firstnameUser'];
	                $_SESSION["surnameUser"] = $row['surnameUser'];
	                $_SESSION["emailUser"] = $row['emailUser'];
	                $_SESSION["id"] = $row['id'];
            		header("location: index.php");
            	} else {
            		writeErrorMessage("*Vaše emailová adresa nebo heslo se neshoduje. Zkuste to prosím znovu.");
            	}
			} else {
				writeErrorMessage("*Uživatel s touto emailovou adresou nebyl nalezen. <br> Chcete se <a href='register.php'>zaregistrovat?</a>");
			}
		} else {
	        writeErrorMessage("*Všechny hodnoty jsou povinné k vyplnění.");
	    }
	}

	require_once "common/header.php";


if (!isUserAuthenticated()) {
	echo "<div class='main'>";
		echo "<h1 class='title'>PŘIHLÁŠENÍ</h1>";
		echo "<div class='form'>";
			echo "<form action='login.php' method='POST'>";
				echo "<input type='email' name='email' placeholder='Email' class='input' value='" . $_POST['email'] . "' required>";
				echo "<input type='password' name='password' placeholder='Heslo' class='input' required>";
				echo "<input type='hidden' name='isSubmit' value='true'>";
				echo "<input type='submit' name='submit' class='inputSubmit' value='Přihlásit se'>";
			echo "</form>";
			echo "<a href='passwordReset.php' class='inputComment'>Zapomenuté heslo?</a>";
			echo "<a href='register.php' class='redirect'>Registrace firmy</a>";
		echo "</div>";
	echo "</div>";
}


	require_once "common/footer.php";

?>