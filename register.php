<?php

	session_start();

	require_once "tmp/db.php";

	function isNotEmpty($email, $firstname, $surname, $password, $passwordVerify, $companyName, $identificationNumber) {
		return !empty($email)
		&& !empty($firstname)
		&& !empty($surname)
		&& !empty($password)
		&& !empty($passwordVerify)
		&& !empty($companyName)
		&& !empty($identificationNumber);
	}

	function URLGenerator($length = 20) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ?!-=';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return "http://matyasvacek.cz/adminVerification.php?verification=" . $randomString;
	}

	if (isAdminAuthenticated() OR isUserAuthenticated()) {
		header("location: index.php");
	}

	if (!empty($_POST["isSubmit"])) {
		$emailStrip = htmlspecialchars(strip_tags($_POST["email"]));
		$firstnameStrip = htmlspecialchars(strip_tags($_POST["firstname"]));
		$surnameStrip = htmlspecialchars(strip_tags($_POST["surname"]));
		$companyNameStrip = htmlspecialchars(strip_tags($_POST["companyName"]));
		$identificationNumber = $_POST["identificationNumber"];
	    if (isNotEmpty($emailStrip, $firstnameStrip, $surnameStrip, $_POST["password"], $_POST["passwordVerify"], $companyNameStrip, $identificationNumber)) {
	        if (isValidEmail($emailStrip)) {
	            if (!emailExists($emailStrip)) {
	            	if (strlen($firstnameStrip) >= 3 & strlen($firstnameStrip) <= 50 && strlen($surnameStrip) >= 3 & strlen($surnameStrip) <= 50) {
		            	if ($_POST["password"] == $_POST["passwordVerify"]) {
		            		if (strlen($_POST["password"]) >= 8 & strlen($_POST["password"]) <= 32) {
	            				if (strpbrk($_POST["password"], '1234567890') !== FALSE) {
	            					if (strpbrk($_POST["password"], 'ABCČDĎEFGHIJKLMNŇOPQRŘSŠTŤUVWXYZŽ') !== FALSE) {
				            			if (!companyExists($companyNameStrip)) {
				            				if (strlen($companyNameStrip) >= 3 & strlen($companyNameStrip) <= 50) {
				            					if (!identificationNumberExists($identificationNumber)) {
				            						if (is_numeric($identificationNumber)) {
					            						if (strlen($identificationNumber) == 8) {
							            					$resultCreation = createAdmin($emailStrip, $firstnameStrip, $surnameStrip, $_POST["password"], $createdDate, $modifiedDate, $companyNameStrip, $identificationNumber);

							            					$verificationURL = URLGenerator();
							            					$resultVerification = createVerificationURL($emailStrip, $verificationURL, $createdDate);
							            					if ($resultCreation & $resultVerification) {
							            						echo "<script>alert('Byl jste úspěšně zaregistrován. Na váš email jsme Vám zaslali odkaz, pomocí kterého si prosím ověřte svůj účet.')</script>";

							            						$mailTo = $emailStrip;
																$headers = 'Content-type: text/html; charset=utf-8' . "\r\n";
																$headers .= "From: <CoMMi>";
																$txt = "<div style='width: 100%; background-color: #e9e7f1;'>
																			<h1 style='padding-top: 1rem; padding-bottom: 1rem; font-family: Roboto, sans-serif; text-align: center; color: #000000;'>
																				Ověření účtu
																			</h1>
																		</div>
																		<div style='width: 100%;'>
																			<h2 style='font-family: Roboto, sans-serif; color: black; padding-left: 2rem;'>
																				Vážený uživateli,
																			</h2>
																			<p style='font-family: Roboto, sans-serif; color: #000000; padding-left: 2rem;'>
																				na Vaši žádost Vám níže posíláme odkaz, pomocí kterého si prosím ověřte svůj účet na webové stránce <b>CoMMi</b> (http://www.matyasvacek.cz).
																			</p>
																			<hr>
																			<p style='font-family: Roboto, sans-serif; padding-left: 2rem; color: #000000;'><b>Odkaz>></b>
																				<li style='font-family: Roboto, sans-serif; color: black; padding-left: 2rem;'><b>" . $verificationURL . "</b></li>
																			</p>
																			<p style='font-family: Roboto, sans-serif; padding-left: 2rem; color: #000000;'>Tento odkaz k ověření se stane po 3 dnech od založení neplatný. Poté si musíte vyžádat o nový. <br> Pokud si účet ale neověříte, nebudete moc pozývat ostatní uživatele do Vaší společnosti.
																			</p>
																			<hr>
																			<p style='font-family: Roboto, sans-serif; padding-left: 2rem; color: red;'>*Pokud jste si účet ale nezaložil Vy, jednoduše tento email prosím ignorujte.
																			</p>
																		</div>
																		<div style='width: 100%; background-color: #e9e7f1;'>
																			<img src='http://www.matyasvacek.cz/img/logoCommi.png' style='display: block; margin-left: auto; margin-right: auto; width: 100px; padding-top: 0.4rem; padding-bottom: 0.4rem;'>
																		</div>";

																mail($mailTo, "Ověření účtu", $txt, $headers);
							                					?>
											    				<meta http-equiv="refresh" content="0;url=login.php">
											    				<?php
															} else {
										                    	writeErrorMessage("*Nepodařilo se nám vytvořit uživatele a založit firmu, <br> zkuste to prosím znovu.");
										                    }
										                } else {
										                	writeErrorMessage("*Identifikační číslo musí být mít délku 8 znaků.");
										                }
										            } else {
										            	writeErrorMessage("*Identifikační číslo není validní, <br> zkuste to prosím znovu.");
										            }
								                } else {
								                	writeErrorMessage("*Toto identifikační číslo již existuje, <br> zkuste prosím jiné.");
								                }
						                    } else {
						                    	writeErrorMessage("*Název společnosti musí být v rozmezí 3 až 50 znaků.");
						                    }
				            			} else {
				            				writeErrorMessage("*Společnost s tímto názvem už existuje, <br> zkuste prosím jiný.");
				            			}
				            		} else {
				            			writeErrorMessage("*Heslo musí obsahovat minimálně jedno velké písmeno.");
				            		}
			            		} else {
			            			writeErrorMessage("*Heslo musí obsahovat minimálně jednu číslici.");
			            		}
		                	} else {
		                		writeErrorMessage("*Heslo musí být v rozmezí 8 až 32 znaků.");
		                	}
		               	} else {
		                	writeErrorMessage("*Zadaná hesla se neshodují.");
		                }
                	} else {
                		writeErrorMessage("*Jméno a příjmení musí být v rozmezí 3 až 50 znaků.");
	                }
	            } else {
	                writeErrorMessage("*Uživatel s touto emailovou adresou již existuje, <br> zkuste prosím jinou.");
	            }
	        } else {
	            writeErrorMessage("*Emailová adresa je ve špatném formátu.");
	        }
	    } else {
	        writeErrorMessage("*Všechny hodnoty jsou povinné k vyplnění.");
	    }
	}

	require_once "common/header.php";


	echo "<div class='main'>";
		echo "<h1 class='title'>REGISTRACE</h1>";
		echo "<div class='form'>";
			echo "<p class='formTitle'>Uživatelské údaje</p>";
			echo "<form action='register.php' method='POST'>";
				echo "<input type='text' name='email' placeholder='Email' class='input' value='" . $_POST['email'] . "' required>";
				echo "<input type='text' name='firstname' placeholder='Jméno' class='input' value='" . $_POST['firstname'] . "' required>";
				echo "<input type='text' name='surname' placeholder='Příjmení' class='input' value='" . $_POST['surname'] . "' required>";
				echo "<input type='password' name='password' placeholder='Heslo' class='input' required>";
				echo "<input type='password' name='passwordVerify' placeholder='Ověření hesla' class='input' required>";
			echo "</div>";
			echo "<br>";
			echo "<div class='form'>";
				echo "<p class='formTitle'>Firemní údaje</p>";
				echo "<input type='text' name='companyName' placeholder='Název firmy' class='input' value='" . $_POST['companyName'] . "' required>";
				echo "<input type='number' name='identificationNumber' placeholder='Identifikační číslo' class='input' value='" . $_POST['identificationNumber'] . "' required>";
				echo "<input type='hidden' name='isSubmit' value='true'>";
				echo "<input type='submit' name='submit' class='inputSubmit' value='Zaregistrovat se'>";
				echo "<a href='login.php' class='redirect'>Přihlášení</a>";
			echo "</form>";
		echo "</div>";
	echo "</div>";


	require_once "common/footer.php";

?>