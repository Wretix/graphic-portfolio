<?php

	session_start();

	require_once "tmp/db.php";

	function isNotEmpty($email) {
		return !empty($email);
	}

	function isNotEmptyCode($code) {
		return !empty($code);
	}

	function isNotEmptyPassword($password, $passwordVerify) {
		return !empty($password)
			&& !empty($passwordVerify);
	}

	function codeGenerator($length = 8) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

	function passwordRecoveryBeggining() {
		echo "<script>alert('Na Vámi zadaný email jsme zaslali kód. Ten prosím použijte v následujím kroku. Na vložení kódu máte 15 minut, poté se kód stane neplatným.')</script>";
		echo "<div class='mainPasswordRecovery'>";
			echo "<h1 class='title'>ZAPOMENUTÉ HESLO</h1>";
			echo "<p class='part'>2. krok</p>";
			echo "<form action='passwordReset.php' method='POST'>";
				echo "<p class='titleInput'>*Zadejte prosím kód, který jsme Vám zaslali na email.</p>";
				echo "<input type='text' name='code' placeholder='Kód' class='input' required>";
				echo "<br>";
				echo "<input type='hidden' name='isSubmitCode' value='true'>";
				echo "<input type='submit' name='submitCode' class='inputSubmit' value='Odeslat kód'>";
			echo "</form>";
		echo "</div>";
	}

	if (isAdminAuthenticated() OR isUserAuthenticated()) {
		header("location: index.php");
	}

	
	if (!empty($_POST["isSubmit"])) {
		$emailStrip = htmlspecialchars(strip_tags($_POST["email"]));
		if (isNotEmpty($emailStrip)) {
			if (isValidEmail($emailStrip)) {
				$resultAdmin = findAdminByEmail($emailStrip);
				$resultUser = findUserByEmail($emailStrip);
				if ($resultAdmin->rowCount() > 0) {
					$rowAdmin = $resultAdmin->fetch();
					if ($rowAdmin) {
						$code = codeGenerator();
						$result1 = createTemporaryCode($emailStrip, $code, $createdDate);
						if ($result1) {
							$mailTo = $emailStrip;
							$headers = 'Content-type: text/html; charset=utf-8' . "\r\n";
							$headers .= "From: <CoMMi>";
							$txt = "<div style='width: 100%; background-color: #e9e7f1;'>
										<h1 style='padding-top: 1rem; padding-bottom: 1rem; font-family: Roboto, sans-serif; text-align: center; color: #000000;'>
											Obnovení hesla
										</h1>
									</div>
									<div style='width: 100%;'>
										<h2 style='font-family: Roboto, sans-serif; color: black; padding-left: 2rem;'>
											Vážený uživateli,
										</h2>
										<p style='font-family: Roboto, sans-serif; color: #000000; padding-left: 2rem;'>
											na Vaší žádost Vám zasíláme kód ke změně hesla, který prosím použijte v dalším kroku.
										</p>
										<hr>
										<p style='font-family: Roboto, sans-serif; padding-left: 2rem; color: #000000;'><b>Váš ověřovací kód>></b>
											<li style='font-family: Roboto, sans-serif; color: black; padding-left: 2rem;'>Email: <b>" . $code . "</b></li>
										</p>
										<hr>
										<p style='font-family: Roboto, sans-serif; padding-left: 2rem; color: red;'>*Pokud jste si o žádost změny hesla ale nežádali, jednoduše tuto zprávu prosím ignorujte. Žádost se po 1 dni sama vymaže.
										</p>
									</div>
									<div style='width: 100%; background-color: #e9e7f1;'>
										<img src='http://www.matyasvacek.cz/img/logoCommi.png' style='display: block; margin-left: auto; margin-right: auto; width: 100px; padding-top: 0.4rem; padding-bottom: 0.4rem;'>
									</div>";

							mail($mailTo, "Obnovení hesla", $txt, $headers);
						} else {
	            			writeErrorMessage("*Něco se nepodařilo, <br> zkuste to prosím znovu.");
	            		}
					} else {
	        			writeErrorMessage("*Něco se nepodařilo, <br> zkuste to prosím znovu.");
	        		}
				} elseif ($resultUser->rowCount() > 0) {
					$rowUser = $resultUser->fetch();
					if ($rowUser) {
						$code = codeGenerator();
						$result1 = createTemporaryCode($emailStrip, $code, $createdDate);
						if ($result1) {
							$mailTo = $emailStrip;
							$headers = 'Content-type: text/html; charset=utf-8' . "\r\n";
							$headers .= "From: <CoMMi>";
							$txt = "<div style='width: 100%; background-color: #e9e7f1;'>
										<h1 style='padding-top: 1rem; padding-bottom: 1rem; font-family: Roboto, sans-serif; text-align: center; color: #000000;'>
											Obnovení hesla
										</h1>
									</div>
									<div style='width: 100%;'>
										<h2 style='font-family: Roboto, sans-serif; color: black; padding-left: 2rem;'>
											Vážený uživateli,
										</h2>
										<p style='font-family: Roboto, sans-serif; color: #000000; padding-left: 2rem;'>
											na Vaší žádost Vám zasíláme kód ke změně hesla, který prosím použijte v dalším kroku.
										</p>
										<hr>
										<p style='font-family: Roboto, sans-serif; padding-left: 2rem; color: #000000;'><b>Váš ověřovací kód>></b>
											<li style='font-family: Roboto, sans-serif; color: black; padding-left: 2rem;'>Email: <b>" . $code . "</b></li>
										</p>
										<hr>
										<p style='font-family: Roboto, sans-serif; padding-left: 2rem; color: red;'>*Pokud jste si o žádost změny hesla ale nežádali, jednoduše tuto zprávu prosím ignorujte. Žádost se po 1 dni sama vymaže.
										</p>
									</div>
									<div style='width: 100%; background-color: #e9e7f1;'>
										<img src='http://www.matyasvacek.cz/img/logoCommi.png' style='display: block; margin-left: auto; margin-right: auto; width: 100px; padding-top: 0.4rem; padding-bottom: 0.4rem;'>
									</div>";

							mail($mailTo, "Obnovení hesla", $txt, $headers);
						} else {
	            			writeErrorMessage("*Něco se nepodařilo, <br> zkuste to prosím znovu.");
	            		}
					} else {
            			writeErrorMessage("*Něco se nepodařilo, <br> zkuste to prosím znovu.");
            		}
        		} else {
					writeErrorMessage("*Uživatel s touto emailovou adresou nebyl nalezen. <br> zkuste to prosím znovu.");
				}
			} else {
				writeErrorMessage("*Emailová adresa je ve špatném formátu.");
			}
		} else {
	        writeErrorMessage("*Všechny hodnoty jsou povinné k vyplnění.");
	    }
	}

	require_once "common/header.php";

	if (!empty($_POST["isSubmitCode"])) {
		$codeStrip = htmlspecialchars(strip_tags($_POST["code"]));
		if (isNotEmpty($codeStrip)) {
			$resultCode = findPersonByCode($codeStrip);
			$rowCode = $resultCode->fetch();
			if ($rowCode) {
				echo "<script>alert('Kód úspěšně ověřen.')</script>";
				echo "<div class='mainPasswordRecovery'>";
					echo "<h1 class='title'>ZAPOMENUTÉ HESLO</h1>";
					echo "<p class='part'>3. krok</p>";
					echo "<form action='passwordReset.php' method='POST'>";
						echo "<p class='titleInput'>*Zadejte prosím nové heslo a poté ho potvrďte opakovaným opsáním.</p>";
						echo "<input type='password' name='password' placeholder='Nové heslo' class='input' required>";
						echo "<input type='password' name='passwordVerify' placeholder='Znovu nové heslo' class='input' required>";
						echo "<br>";
						echo "<input type='hidden' name='emailAgain' value='" . $rowCode["personEmail"] . "'>";
						echo "<input type='hidden' name='isSubmitPassword' value='true'>";
						echo "<input type='submit' name='submitPassword' class='inputSubmit' value='Změnit heslo'>";
					echo "</form>";
				echo "</div>";
				deleteCode($rowCode["personEmail"]);
			} else {
				writeErrorMessage("*Požadovaný kód nebyl nalezen, nebo je již neplatný.");
			}
		} else {
	        writeErrorMessage("*Všechny hodnoty jsou povinné k vyplnění.");
	    }
	}

	if (!empty($_POST["isSubmitPassword"])) {
		$emailAgain = $_POST["emailAgain"];
		if (isNotEmptyPassword($_POST["password"], $_POST["passwordVerify"])) {
			if ($_POST["password"] == $_POST["passwordVerify"]) {
				if (strlen($_POST["password"]) >= 8 & strlen($_POST["password"]) <= 32) {
					if (strpbrk($_POST["password"], '1234567890') !== FALSE) {
				    	if (strpbrk($_POST["password"], 'ABCČDĎEFGHIJKLMNŇOPQRŘSŠTŤUVWXYZŽ') !== FALSE) {
					    	$adminEmail = findAdminByEmail($emailAgain);
					    	$userEmail = findUserByEmail($emailAgain);
					    	if ($adminEmail->rowCount() > 0) {
					    		$rowAdminEmail = $adminEmail->fetch();
					    		if ($rowAdminEmail) {
					    			$result3 = rewriteNotAuthenticatedAdminDataPassword($_POST["password"], $modifiedDate, $emailAgain);
					    			if ($result3) {
					    				echo "<script>alert('Heslo úspěšně změněno.')</script>";
					    				?>
					    				<meta http-equiv="refresh" content="0;url=login.php">
					    				<?php
					    			} else {
					    				$fail = true;
					        			writeErrorMessage("*Něco se nepodařilo, <br> zkuste to prosím znovu.");
					        		}
					    		} else {
					    			$fail = true;
				        			writeErrorMessage("*Něco se nepodařilo, <br> zkuste to prosím znovu.");
				        		}
					    	} elseif ($userEmail->rowCount() > 0) {
					    		$rowUserEmail = $userEmail->fetch();
					    		if ($rowUserEmail) {
					    			$result4 = rewriteNotAuthenticatedUserDataPassword($_POST["password"], $modifiedDate, $emailAgain);
					    			if ($result4) {
					    				echo "<script>alert('Heslo úspěšně změněno.')</script>";
					    				?>
					    				<meta http-equiv="refresh" content="0;url=login.php">
					    				<?php
						    		} else {
						    			$fail = true;
					        			writeErrorMessage("*Něco se nepodařilo, <br> zkuste to prosím znovu.");
					        		}
					    		} else {
					    			$fail = true;
				        			writeErrorMessage("*Něco se nepodařilo, <br> zkuste to prosím znovu.");
				        		}
					    	} else {
					    		$fail = true;
					    	}
					    } else {
					    	writeErrorMessage("*Heslo musí obsahovat minimálně jedno velké písmeno.");
							$fail = true;
					    }
				    } else {
				    	$fail = true;
				    	writeErrorMessage("*Heslo musí obsahovat minimálně jednu číslici.");
				    }
				} else {
					$fail = true;
			    	writeErrorMessage("*Heslo musí být v rozmezí 8 až 32 znaků.");
			    }
			} else {
				$fail = true;
				writeErrorMessage("*Zadaná hesla se neshodují.");
			}
		} else {
			$fail = true;
			writeErrorMessage("*Všechny hodnoty jsou povinné k vyplnění.");
		}
	}

	if ($result1) {
		passwordRecoveryBeggining();
	} elseif ($rowCode) {

	} elseif ($result3) {

	} elseif ($result4) {

	} elseif ($fail) { 
		echo "<div class='mainPasswordRecovery'>";
			echo "<h1 class='title'>ZAPOMENUTÉ HESLO</h1>";
			echo "<p class='part'>3. krok</p>";
			echo "<form action='passwordReset.php' method='POST'>";
				echo "<p class='titleInput'>*Zadejte prosím nové heslo a poté ho potvrďte opakovaným opsáním.</p>";
				echo "<input type='password' name='password' placeholder='Nové heslo' class='input' required>";
				echo "<input type='password' name='passwordVerify' placeholder='Znovu nové heslo' class='input' required>";
				echo "<br>";
				echo "<input type='hidden' name='emailAgain' value='" . $emailAgain . "'>";
				echo "<input type='hidden' name='isSubmitPassword' value='true'>";
				echo "<input type='submit' name='submitPassword' class='inputSubmit' value='Změnit heslo'>";
			echo "</form>";
		echo "</div>";
		deleteCode($emailAgain);
	} else {
		echo "<div class='mainPasswordRecovery'>";
			echo "<h1 class='title'>ZAPOMENUTÉ HESLO</h1>";
			echo "<p class='part'>1. krok</p>";
			echo "<form action='passwordReset.php' method='POST'>";
				echo "<p class='titleInput'>*Zadejte prosím Vaší emailovou adresu.</p>";
				echo "<input type='email' name='email' placeholder='Email' class='input' value='" . $_POST['email'] . "' required>";
				echo "<br>";
				echo "<input type='hidden' name='isSubmit' value='true'>";
				echo "<input type='submit' name='submit' class='inputSubmit' value='Odeslat email'>";
			echo "</form>";
		echo "</div>";
	}


	require_once "common/footer.php";

?>