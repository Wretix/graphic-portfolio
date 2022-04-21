<?php
    
    session_start();

	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	
	require_once "db.php";

	function getMessages() {
        $connection = dbConnect();
        $sql = "SELECT * FROM messages WHERE toRoom = :toRoom";
        $statement = $connection->prepare($sql);
        $statement->execute([":toRoom" => $_GET["toRoom"]]);
        $data = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }

    function getAllIDs() {
        $connection = dbConnect();
        $sql = "SELECT * FROM profilePicture";
        $statement = $connection->prepare($sql);
        $statement->execute(array());
        return $statement;
    }

    /*foreach (getMessages() as $message) {
        if ($message["fromUserID"] == $_SESSION["id"]) {
            echo "<div class='messageBubble'><div class='message'>" . $message["fromUsername"] . ": " . $message["message"] . "</div></div><p class='timeHover'>" . $message["sendTime"] . "</p> <br>";
        } else {
            echo "<div class='messageBubble2'><p class='message'>" . $message["fromUsername"] . ": " . $message["message"] . "</p></div><p class='timeHover2'>" . $message["sendTime"] . "</p> <br>";
        }
    }*/

    foreach (getMessages() as $message) {
        if (isAdminAuthorized()) {
            //ZPRÁVA SAMOTNÉHO ADMINA
            if ($message["fromAdminID"] == $_SESSION["id"]) {
                $resultOwnProfilePicture = displayAdminProfilePicture($_SESSION["id"]);
                $resultOwnProfilePictureDEF = displayDefaultProfilePicture(0);
                //S PROFILOVKOU
                if (profileAdminPictureExists($_SESSION["id"])) {
                    $rowOwnProfilePicture = $resultOwnProfilePicture->fetch();
                    echo "<div class='wholeMessageBubble'><p class='fromUsername'>" . $message["fromUsername"] . "</p><img class='profilePictureBubble1' title='" . $rowOwnProfilePicture["id"] . "' src='data:image;base64," . $rowOwnProfilePicture["image"] . "'><div class='messageBubble'><div class='message'>" . $message["message"] . "</div></div><p class='timeHover'>" . $message["sendTime"] . "</p></div> <br>";
                //BEZ PROFILOVKY
                } else {
                    $rowOwnProfilePictureDEF = $resultOwnProfilePictureDEF->fetch();
                    echo "<div class='wholeMessageBubble'><p class='fromUsername'>" . $message["fromUsername"] . "</p><img class='profilePictureBubble1' title='" . $rowOwnProfilePictureDEF["id"] . "' src='data:image;base64," . $rowOwnProfilePictureDEF["image"] . "'><div class='messageBubble'><div class='message'>" . $message["message"] . "</div></div><p class='timeHover'>" . $message["sendTime"] . "</p></div> <br>";
                }
            //ZPRÁVA OD UŽIVATELE
            } elseif ($message["toUserID"] == 0 && $message["toAdminID"] == 0) {
                foreach (getAllIDs() as $key) {
                    //S PROFILOVKOU
                    if ($message["fromUserID"] == $key["userID"]) {
                        if (profileUserPictureExists($key["userID"])) {
                            $resultOtherProfilePicture = displayUserProfilePicture($message["fromUserID"]);
                            $rowOtherProfilePicture = $resultOtherProfilePicture->fetch();
                            echo "<div class='wholeMessageBubble'><p class='fromUsername2'>" . $message["fromUsername"] . "</p><img class='profilePictureBubble2' title='" . $rowOtherProfilePicture["id"] . "' src='data:image;base64," . $rowOtherProfilePicture["image"] . "'><div class='messageBubble2'><div class='message'>" . $message["message"] . "</div></div><p class='timeHover2'>" . $message["sendTime"] . "</p></div> <br>";
                        }
                    //BEZ PROFILOVKY
                    } elseif ($message["fromUserID"] !== $key["userID"]) {
                        if (!profileUserPictureExists($message["fromUserID"]) & $key["userID"] == 0 & $key["adminID"] == 0) {
                            $resultOtherProfilePictureDEF = displayDefaultProfilePicture(0);
                            $rowOtherProfilePictureDEF = $resultOtherProfilePictureDEF->fetch();
                            echo "<div class='wholeMessageBubble'><p class='fromUsername2'>" . $message["fromUsername"] . "</p><img class='profilePictureBubble2' title='" . $rowOtherProfilePictureDEF["id"] . "' src='data:image;base64," . $rowOtherProfilePictureDEF["image"] . "'><div class='messageBubble2'><div class='message'>" . $message["message"] . "</div></div><p class='timeHover2'>" . $message["sendTime"] . "</p></div> <br>";
                        }
                    }
                }
            //SOUKROMÁ ZPRÁVA OD UŽIVATELE ADMINOVI
            } elseif ($message["toAdminID"] == $_SESSION["id"]) {
                $secretMessage1 = false;
                foreach (getAllIDs() as $key) {
                    //S PROFILOVKOU
                    if ($message["fromUserID"] == $key["userID"] & $message["fromAdminID"] == 0) {
                        if (profileUserPictureExists($key["userID"])) {
                            $resultOtherProfilePicture = displayUserProfilePicture($message["fromUserID"]);
                            $rowOtherProfilePicture = $resultOtherProfilePicture->fetch();
                            echo "<div class='wholeMessageBubble'><p class='fromUsername2'>" . $message["fromUsername"] . "</p><img class='profilePictureBubble2' title='" . $rowOtherProfilePicture["id"] . "' src='data:image;base64," . $rowOtherProfilePicture["image"] . "'><div class='messageBubble3' title='Tuto zprávu vidíte pouze vy a její odesílatel.'><div class='message'>" . $message["message"] . "</div></div><p class='timeHover2'>" . $message["sendTime"] . "</p></div> <br>";
                        }
                    //BEZ PROFILOVKY
                    } elseif ($message["fromUserID"] !== $key["userID"] & $message["fromAdminID"] == 0) {
                        if (!profileUserPictureExists($message["fromUserID"])) {
                            $resultOtherProfilePictureDEF = displayDefaultProfilePicture(0);
                            $rowOtherProfilePictureDEF = $resultOtherProfilePictureDEF->fetch();
                            $secretMessage1 = true;
                        }
                    }
                } if ($secretMessage1) {
                    echo "<div class='wholeMessageBubble'><p class='fromUsername2'>" . $message["fromUsername"] . "</p><img class='profilePictureBubble2' title='" . $rowOtherProfilePictureDEF["id"] . "' src='data:image;base64," . $rowOtherProfilePictureDEF["image"] . "'><div class='messageBubble3' title='Tuto zprávu vidíte pouze vy a její odesílatel.'><div class='message'>" . $message["message"] . "</div></div><p class='timeHover2'>" . $message["sendTime"] . "</p></div> <br>";
                }
            }
        //ZPRÁVA SAMOTNÉHO UŽIVATELE
        } elseif (isUserAuthorized()) {
            if ($message["fromUserID"] == $_SESSION["id"]) {
                //S PROFILOVKOU
                if (profileUserPictureExists($_SESSION["id"])) {
                    $resultOwnProfilePicture = displayUserProfilePicture($_SESSION["id"]);
                    $rowOwnProfilePicture = $resultOwnProfilePicture->fetch();
                    echo "<div class='wholeMessageBubble'><p class='fromUsername'>" . $message["fromUsername"] . "</p><img class='profilePictureBubble1' title='" . $rowOwnProfilePicture["id"] . "' src='data:image;base64," . $rowOwnProfilePicture["image"] . "'><div class='messageBubble'><div class='message'>" . $message["message"] . "</div></div><p class='timeHover'>" . $message["sendTime"] . "</p></div> <br>";
                //BEZ PROFILOVKY
                } else {
                    $resultOwnProfilePictureDEF = displayDefaultProfilePicture(0);
                    $rowOwnProfilePictureDEF = $resultOwnProfilePictureDEF->fetch();
                    echo "<div class='wholeMessageBubble'><p class='fromUsername'>" . $message["fromUsername"] . "</p><img class='profilePictureBubble1' title='" . $rowOwnProfilePictureDEF["id"] . "' src='data:image;base64," . $rowOwnProfilePictureDEF["image"] . "'><div class='messageBubble'><div class='message'>" . $message["message"] . "</div></div><p class='timeHover'>" . $message["sendTime"] . "</p></div> <br>";
                }
            //ZPRÁVA OD ADMINA CO VIDÍ UŽIVATEL
            } elseif ($message["toUserID"] == 0 & $message["toAdminID"] == 0) {
                foreach (getAllIDs() as $key) {
                    //S PROFILOVKOU
                    if ($message["fromAdminID"] == $key["adminID"] & $key["userID"] == 0) {
                        if (profileAdminPictureExists($key["adminID"])) {
                            $resultOtherProfilePicture = displayAdminProfilePicture($message["fromAdminID"]);
                            $rowOtherProfilePicture = $resultOtherProfilePicture->fetch();
                            $jedna = true;
                        }
                    //S PROFILOVKOU
                    } elseif ($message["fromUserID"] == $key["userID"] & $key["adminID"] == 0) {
                        if (profileUserPictureExists($key["userID"])) {
                            $resultOtherProfilePicture = displayUserProfilePicture($message["fromUserID"]);
                            $rowOtherProfilePicture = $resultOtherProfilePicture->fetch();
                            $dva = true;
                        }
                    //BEZ PROFILOVKY
                    } elseif ($message["fromAdminID"] !== $key["adminID"]) {
                        if (!profileAdminPictureExists($key["adminID"])) {
                            $resultOtherProfilePictureDEF = displayDefaultProfilePicture(0);
                            $rowOtherProfilePictureDEF = $resultOtherProfilePictureDEF->fetch();
                            $tri = true;
                        }
                    //BEZ PROFILOVKY
                    } elseif ($message["fromUserID"] !== $key["userID"]) {
                        if (!profileUserPictureExists($key["userID"])) {
                            $resultOwnProfilePictureDEF = displayDefaultProfilePicture(0);
                            $rowOwnProfilePictureDEF = $resultOwnProfilePictureDEF->fetch();
                            $ctyri = true;
                        }
                    }
                } if ($jedna) {
                    echo "<div class='wholeMessageBubble'><p class='fromUsername2'>" . $message["fromUsername"] . "</p><img class='profilePictureBubble2' title='" . $rowOtherProfilePicture["id"] . "' src='data:image;base64," . $rowOtherProfilePicture["image"] . "'><div class='messageBubble2'><div class='message'>" . $message["message"] . "</div></div><p class='timeHover2'>" . $message["sendTime"] . "</p></div> <br>";
                } elseif ($dva) {
                    echo "<div class='wholeMessageBubble'><p class='fromUsername2'>" . $message["fromUsername"] . "</p><img class='profilePictureBubble2' title='" . $rowOtherProfilePicture["id"] . "' src='data:image;base64," . $rowOtherProfilePicture["image"] . "'><div class='messageBubble2'><div class='message'>" . $message["message"] . "</div></div><p class='timeHover2'>" . $message["sendTime"] . "</p></div> <br>";
                } elseif ($tri) {
                    echo "<div class='wholeMessageBubble'><p class='fromUsername2'>" . $message["fromUsername"] . "</p><img class='profilePictureBubble2' title='" . $rowOtherProfilePictureDEF["id"] . "' src='data:image;base64," . $rowOtherProfilePictureDEF["image"] . "'><div class='messageBubble2'><div class='message'>" . $message["message"] . "</div></div><p class='timeHover2'>" . $message["sendTime"] . "</p></div> <br>";
                } elseif ($ctyri) {
                    echo "<div class='wholeMessageBubble'><p class='fromUsername2'>" . $message["fromUsername"] . "</p><img class='profilePictureBubble2' title='" . $rowOtherProfilePictureDEF["id"] . "' src='data:image;base64," . $rowOtherProfilePictureDEF["image"] . "'><div class='messageBubble2'><div class='message'>" . $message["message"] . "</div></div><p class='timeHover2'>" . $message["sendTime"] . "</p></div> <br>";
                }
            //SOUKROMÁ ZPRÁVA OD ADMINA UŽIVATELOVI A OD UŽIVATELE UŽIVATELOVI
            } elseif ($message["toUserID"] == $_SESSION["id"]) {
                $secretMessage2 = false;
                $secretMessage3 = false;
                foreach (getAllIDs() as $key) {
                    //S PROFILOVKOU
                    if ($message["fromAdminID"] == $key["adminID"] & $message["fromUserID"] == 0) {
                        if (profileAdminPictureExists($key["adminID"])) {
                            $resultOtherProfilePicture = displayAdminProfilePicture($message["fromAdminID"]);
                            $rowOtherProfilePicture = $resultOtherProfilePicture->fetch();
                            echo "<div class='wholeMessageBubble'><p class='fromUsername2'>" . $message["fromUsername"] . "</p><img class='profilePictureBubble2' title='" . $rowOtherProfilePicture["id"] . "' src='data:image;base64," . $rowOtherProfilePicture["image"] . "'><div class='messageBubble3' title='Tuto zprávu vidíte pouze vy a její odesílatel.'><div class='message'>" . $message["message"] . "</div></div><p class='timeHover2'>" . $message["sendTime"] . "</p></div> <br>";
                        }
                    //S PROFILOVKOU
                    } elseif ($message["fromUserID"] == $key["userID"] & $message["fromAdminID"] == 0) {
                        if (profileUserPictureExists($key["userID"])) {
                            $resultOtherProfilePicture = displayUserProfilePicture($message["fromUserID"]);
                            $rowOtherProfilePicture = $resultOtherProfilePicture->fetch();
                            echo "<div class='wholeMessageBubble'><p class='fromUsername2'>" . $message["fromUsername"] . "</p><img class='profilePictureBubble2' title='" . $rowOtherProfilePicture["id"] . "' src='data:image;base64," . $rowOtherProfilePicture["image"] . "'><div class='messageBubble3' title='Tuto zprávu vidíte pouze vy a její odesílatel.'><div class='message'>" . $message["message"] . "</div></div><p class='timeHover2'>" . $message["sendTime"] . "</p></div> <br>";
                        }
                    //BEZ PROFILOVKY
                    } elseif ($message["fromAdminID"] !== $key["adminID"] & $message["fromUserID"] == 0) {
                        if (!profileAdminPictureExists($message["fromAdminID"])) {
                            $resultOtherProfilePictureDEF = displayDefaultProfilePicture(0);
                            $rowOtherProfilePictureDEF = $resultOtherProfilePictureDEF->fetch();
                            $secretMessage2 = true;
                        }
                    //BEZ PROFILOVKY
                    } elseif ($message["fromUserID"] !== $key["userID"] & $message["fromAdminID"] == 0) {
                        if (!profileUserPictureExists($message["fromUserID"])) {
                            $resultOtherProfilePictureDEF = displayDefaultProfilePicture(0);
                            $rowOtherProfilePictureDEF = $resultOtherProfilePictureDEF->fetch();
                            $secretMessage3 = true;
                        }
                    }
                } if ($secretMessage2) {
                    echo "<div class='wholeMessageBubble'><p class='fromUsername2'>" . $message["fromUsername"] . "</p><img class='profilePictureBubble2' title='" . $rowOtherProfilePictureDEF["id"] . "' src='data:image;base64," . $rowOtherProfilePictureDEF["image"] . "'><div class='messageBubble3' title='Tuto zprávu vidíte pouze vy a její odesílatel.'><div class='message'>" . $message["message"] . "</div></div><p class='timeHover2'>" . $message["sendTime"] . "</p></div> <br>";
                } if ($secretMessage3) {
                    echo "<div class='wholeMessageBubble'><p class='fromUsername2'>" . $message["fromUsername"] . "</p><img class='profilePictureBubble2' title='" . $rowOtherProfilePictureDEF["id"] . "' src='data:image;base64," . $rowOtherProfilePictureDEF["image"] . "'><div class='messageBubble3' title='Tuto zprávu vidíte pouze vy a její odesílatel.'><div class='message'>" . $message["message"] . "</div></div><p class='timeHover2'>" . $message["sendTime"] . "</p></div> <br>";
                }
            }
        }
    }


?>