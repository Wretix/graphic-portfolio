<?php
    session_start();

    require_once "db.php";

    function insertMessage($fromAdminID, $fromUserID, $fromUsername, $toRoom, $toRoomName, $toCompany, $message, $toAdminID, $toUserID, $sendTime) {
        $connection = dbConnect();
        $sql = "INSERT INTO messages (fromAdminID, fromUserID, fromUsername, toRoom, toRoomName, toCompany, message, toAdminID, toUserID, sendTime) VALUES (:fromAdminID, :fromUserID, :fromUsername, :toRoom, :toRoomName, :toCompany, :message, :toAdminID, :toUserID, now())";
        $statement = $connection->prepare($sql);
        $statement->execute([
            ":fromAdminID" => $fromAdminID,
            ":fromUserID" => $fromUserID,
            ":fromUsername" => $fromUsername,
            ":toRoom" => $toRoom,
            ":toRoomName" => $toRoomName,
            ":toCompany" => $toCompany,
            ":message" => $message,
            ":toAdminID" => $toAdminID,
            ":toUserID" => $toUserID
        ]);
        return $statement;
    }

    /*function getLastMessage() {
        $message = $_POST["message"];
        $connection = dbConnect();
        $sql = "SELECT * FROM messages ORDER BY id desc limit 1";
        $statement = $connection->prepare($sql);
        $statement->execute($message);
        return $statement;
    }*/

    function isUserInRoom($userID, $roomID) {
        $connection = dbConnect();
        $sql = "SELECT * FROM roomPermission WHERE userID = :userID AND roomID = :roomID";
        $statement = $connection->prepare($sql);
        $statement->execute(array($userID, $roomID));
        return $statement;
    }

    function isAdminInRoom($allowedByAdmin, $roomID) {
        $connection = dbConnect();
        $sql = "SELECT * FROM roomPermission WHERE allowedByAdmin = :allowedByAdmin AND roomID = :roomID";
        $statement = $connection->prepare($sql);
        $statement->execute(array($allowedByAdmin, $roomID));
        return $statement;
    }

    $messageStrip = preg_replace('#<[^>]+>#', '', $_POST["message"]);
    if (!empty($messageStrip)) {
        if (strlen($messageStrip) <= 255) {
            $resultAdminInRoom = isAdminInRoom($messageStrip, $_POST["toRoom"]);
            $resultUserInRoom = isUserInRoom($messageStrip, $_POST["toRoom"]);
            if (isAdminAuthorized()) {
                $resultAdminName = showAdminName();
                $rowAdminName = $resultAdminName->fetch();
                if ($resultUserInRoom->rowCount() > 0) {
                    $rowUserInRoom = $resultUserInRoom->fetch();

                    $prefix1 = "@uzivatel";
                    $user = $rowUserInRoom["userID"];
                    $secretMessage1 = $user . $prefix1;

                    $prefix2 = "@UZIVATEL";
                    $user = $rowUserInRoom["userID"];
                    $secretMessage2 = $user . $prefix2;

                    $prefix3 = "@Uzivatel";
                    $user = $rowUserInRoom["userID"];
                    $secretMessage3 = $user . $prefix3;

                    if ((strpos($messageStrip, $secretMessage1) === 0) OR (strpos($messageStrip, $secretMessage2) === 0) OR (strpos($messageStrip, $secretMessage3) === 0)) {
                        insertMessage($_SESSION["id"], 0, $rowAdminName["firstname"] . " " . $rowAdminName["surname"], $_POST["toRoom"], $_POST["toRoomName"], $_POST["toCompany"], $messageStrip, 0, $rowUserInRoom["userID"], $sendTime);
                    } else {
                        insertMessage($_SESSION["id"], 0, $rowAdminName["firstname"] . " " . $rowAdminName["surname"], $_POST["toRoom"], $_POST["toRoomName"], $_POST["toCompany"], $messageStrip, 0, 0, $sendTime);
                    }
                } else {
                    insertMessage($_SESSION["id"], 0, $rowAdminName["firstname"] . " " . $rowAdminName["surname"], $_POST["toRoom"], $_POST["toRoomName"], $_POST["toCompany"], $messageStrip, 0, 0, $sendTime);
                }
            } elseif (isUserAuthorized()) {
                $resultUserName = showUserName();
                $rowUserName = $resultUserName->fetch();
                if ($resultUserInRoom->rowCount() > 0) {
                    $rowUserInRoom = $resultUserInRoom ->fetch();

                    $prefix1 = "@uzivatel";
                    $user = $rowUserInRoom["userID"];
                    $secretMessage1 = $user . $prefix1;

                    $prefix2 = "@UZIVATEL";
                    $user = $rowUserInRoom["userID"];
                    $secretMessage2 = $user . $prefix2;

                    $prefix3 = "@Uzivatel";
                    $user = $rowUserInRoom["userID"];
                    $secretMessage3 = $user . $prefix3;

                    if ((strpos($messageStrip, $secretMessage1) === 0) OR (strpos($messageStrip, $secretMessage2) === 0) OR (strpos($messageStrip, $secretMessage3) === 0)) {
                        insertMessage(0, $_SESSION["id"], $rowUserName["firstnameUser"] . " " . $rowUserName["surnameUser"], $_POST["toRoom"], $_POST["toRoomName"], $_POST["toCompany"], $messageStrip, 0, $rowUserInRoom["userID"], $sendTime);
                    } else {
                        insertMessage(0, $_SESSION["id"], $rowUserName["firstnameUser"] . " " . $rowUserName["surnameUser"], $_POST["toRoom"], $_POST["toRoomName"], $_POST["toCompany"], $messageStrip, 0, 0, $sendTime);
                    }
                } elseif ($resultAdminInRoom->rowCount() > 0) {
                    $rowAdminInRoom = $resultAdminInRoom->fetch();

                    $prefix1 = "@admin";
                    $user = $rowAdminInRoom["allowedByAdmin"];
                    $secretMessage1 = $user . $prefix1;

                    $prefix2 = "@ADMIN";
                    $user = $rowAdminInRoom["allowedByAdmin"];
                    $secretMessage2 = $user . $prefix2;

                    $prefix3 = "@Admin";
                    $user = $rowAdminInRoom["allowedByAdmin"];
                    $secretMessage3 = $user . $prefix3;

                    if ((strpos($messageStrip, $secretMessage1) === 0) OR (strpos($messageStrip, $secretMessage2) === 0) OR (strpos($messageStrip, $secretMessage3) === 0)) {
                        insertMessage(0, $_SESSION["id"], $rowUserName["firstnameUser"] . " " . $rowUserName["surnameUser"], $_POST["toRoom"], $_POST["toRoomName"], $_POST["toCompany"], $messageStrip, $rowAdminInRoom["allowedByAdmin"], 0, $sendTime);
                    } else {
                        insertMessage(0, $_SESSION["id"], $rowUserName["firstnameUser"] . " " . $rowUserName["surnameUser"], $_POST["toRoom"], $_POST["toRoomName"], $_POST["toCompany"], $messageStrip, 0, 0, $sendTime);
                    }
                } else {
                    insertMessage(0, $_SESSION["id"], $rowUserName["firstnameUser"] . " " . $rowUserName["surnameUser"], $_POST["toRoom"], $_POST["toRoomName"], $_POST["toCompany"], $messageStrip, 0, 0, $sendTime);
                }
            }
        }
    }