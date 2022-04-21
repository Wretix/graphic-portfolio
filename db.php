<?php

    //PŘIPOJENÍ K DATABÁZI
    function dbConnect() {
        $server = "127.0.0.1";
        $name = "vacekdatabase";
        $user = "root";
        $pass = "";
        //$port = "3306";

        $charset = "utf8";
        $dsn = "mysql:host=$server;dbname=$name;charset=$charset";

        $opt = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        );
        try {
            return new PDO($dsn, $user, $pass, $opt);
        } catch (PDOException $exception) {
            echo "Připojení selhalo: " . $exception->getMessage();
            exit;
        }
    }

    //INSERTY
    function createAdmin($email, $firstname, $surname, $password, $createdDate, $modifiedDate, $companyName, $identificationNumber) {
        $connection = dbConnect();
        $sql = "INSERT INTO admin (email, firstname, surname, password, createdDate, modifiedDate, companyName, identificationNumber) VALUES (:email, :firstname, :surname, :hash, now(), now(), :companyName, :identificationNumber)";
        $statement = $connection->prepare($sql);
        $hash = createPasswordHash($password);
        $statement->execute(array($email, $firstname, $surname, $hash, $companyName, $identificationNumber));
        return $statement;
    }

    function createUser($emailUser, $firstnameUser, $surnameUser, $password, $createdByAdmin, $inCompany, $createdDate, $modifiedDate) {
        $connection = dbConnect();
        $sql = "INSERT INTO user (emailUser, firstnameUser, surnameUser, password, createdByAdmin, inCompany, createdDate, modifiedDate) VALUES (:email, :firstname, :surname, :hash, :createdByAdmin, :inCompany, now(), now())";
        $statement = $connection->prepare($sql);
        $hash = createPasswordHash($password);
        $statement->execute(array($emailUser, $firstnameUser, $surnameUser, $hash, $createdByAdmin, $inCompany));
        return $statement;
    }

    function createRoom($adminID, $companyName, $roomName, $createdDate) {
        $connection = dbConnect();
        $sql = "INSERT INTO room (adminID, companyName, roomName, createdDate) VALUES (:adminID, :companyName, :roomName, now())";
        $statement = $connection->prepare($sql);
        $statement->execute(array($adminID, $companyName, $roomName));
        return $statement;
    }

    function createUserInvitation($emailInvitation, $codedURL, $invitedByAdmin, $toCompany, $createdDate) {
        $connection = dbConnect();
        $sql = "INSERT INTO user_invitation (emailUser, codedURL, invitedByAdmin, toCompany, createdDate) VALUES (:emailInvitation, :codedURL, :invitedByAdmin, :toCompany, now())";
        $statement = $connection->prepare($sql);
        $statement->execute(array($emailInvitation, $codedURL, $invitedByAdmin, $toCompany));
        return $statement;
    }

    function createTemporaryCode($personEmail, $resetCode, $createdDate) {
        $connection = dbConnect();
        $sql = "INSERT INTO password_reset (personEmail, resetCode, createdDate) VALUES (:personEmail, :resetCode, now())";
        $statement = $connection->prepare($sql);
        $statement->execute(array($personEmail, $resetCode));
        return $statement;
    }

    function accessToRoom($userID, $userName, $roomID, $roomName, $allowedByAdmin) {
        $connection = dbConnect();
        $sql = "INSERT INTO room_permission (userID, userName, roomID, roomName, allowedByAdmin) VALUES (:userID, :userName, :roomID, :roomName, :allowedByAdmin)";
        $statement = $connection->prepare($sql);
        $statement->execute(array($userID, $userName, $roomID, $roomName, $allowedByAdmin));
        return $statement;
    }

    /*function uploadImage($name, $image) {
        $connection = dbConnect();
        $sql = "INSERT INTO emojis (name, image) VALUES (:name, :image)";
        $statement = $connection->prepare($sql);
        $statement->execute(array($name, $image));
        return $statement;
    }*/

    function uploadProfilePicture($name, $image, $adminID, $userID) {
        $connection = dbConnect();
        $sql = "INSERT INTO profile_picture (name, image, adminID, userID) VALUES (:name, :image, :adminID, :userID)";
        $statement = $connection->prepare($sql);
        $statement->execute(array($name, $image, $adminID, $userID));
        return $statement;
    }

    function createVerificationURL($emailAdmin, $codedURL, $createdDate) {
        $connection = dbConnect();
        $sql = "INSERT INTO admin_verification (emailAdmin, codedURL, createdDate) VALUES (:emailAdmin, :codedURL, now())";
        $statement = $connection->prepare($sql);
        $statement->execute(array($emailAdmin, $codedURL));
        return $statement;
    }

    //SELECTY
    function findAdminByEmail($email) {
        $connection = dbConnect();
        $sql = "SELECT * FROM admin WHERE email = :email";
        $statement = $connection->prepare($sql);
        $statement->execute(array($email));
        return $statement;
    }

    function findCompanyByName($companyName) {
        $connection = dbConnect();
        $sql = "SELECT * FROM admin WHERE companyName = :companyName";
        $statement = $connection->prepare($sql);
        $statement->execute(array($companyName));
        return $statement;
    }

    function findIdentificationNumber($identificationNumber) {
        $connection = dbConnect();
        $sql = "SELECT * FROM admin WHERE identificationNumber = :identificationNumber";
        $statement = $connection->prepare($sql);
        $statement->execute(array($identificationNumber));
        return $statement;
    }

    function findUserByEmail($emailUser) {
        $connection = dbConnect();
        $sql = "SELECT * FROM user WHERE emailUser = :email";
        $statement = $connection->prepare($sql);
        $statement->execute(array($emailUser));
        return $statement;
    }

    function findUserByID($createdByAdmin) {
        $connection = dbConnect();
        $sql = "SELECT * FROM user WHERE createdByAdmin = :createdByAdmin";
        $statement = $connection->prepare($sql);
        $statement->execute(array($createdByAdmin));
        return $statement;
    }

    function findRoomByName($roomName, $companyName) {
        $connection = dbConnect();
        $sql = "SELECT * FROM room WHERE roomName = :roomName AND companyName = :companyName";
        $statement = $connection->prepare($sql);
        $statement->execute(array($roomName, $companyName));
        return $statement;
    }

    function showRoom() {
        $sessionID = $_SESSION["id"];
        $connection = dbConnect();
        $sql = "SELECT * FROM room WHERE adminID = :adminID";
        $statement = $connection->prepare($sql);
        $statement->execute(array($sessionID));
        return $statement;
    }

    function selectAllAdmin() {
        $connection = dbConnect();
        $sql = "SELECT * FROM admin";
        $statement = $connection->prepare($sql);
        $statement->execute(array());
        return $statement;
    }

    function selectAllUser() {
        $connection = dbConnect();
        $sql = "SELECT * FROM user";
        $statement = $connection->prepare($sql);
        $statement->execute(array());
        return $statement;
    }

    function showRoomID() {
        $sessionID = $_SESSION["id"];
        $roomID = $_GET["toRoom"];
        $connection = dbConnect();
        $sql = "SELECT * FROM room WHERE adminID = :adminID AND id = :id";
        $statement = $connection->prepare($sql);
        $statement->execute(array($sessionID, $roomID));
        return $statement;
    }

    function showRoomID2() {
        $connection = dbConnect();
        $sessionID = $_SESSION["id"];
        $roomID = $_GET["toRoom"];
        $sql = "SELECT * FROM room_permission WHERE userID = :userID AND roomID = :roomID";
        $statement = $connection->prepare($sql);
        $statement->execute(array($sessionID, $roomID));
        return $statement;
    }

    function showAccessToRoom() {
        $sessionID = $_SESSION["id"];
        $connection = dbConnect();
        $sql = "SELECT * FROM room_permission WHERE userID = :userID";
        $statement = $connection->prepare($sql);
        $statement->execute(array($sessionID));
        return $statement;
    }

    function findUserByPermission($userID, $roomID) {
        $connection = dbConnect();
        $sql = "SELECT * FROM room_permission WHERE userID = :userID AND roomID = :roomID";
        $statement = $connection->prepare($sql);
        $statement->execute(array($userID, $roomID));
        return $statement;
    }

    function findUserByEmailInvitation($email) {
        $connection = dbConnect();
        $sql = "SELECT * FROM user_invitation WHERE emailUser = :email";
        $statement = $connection->prepare($sql);
        $statement->execute(array($email));
        return $statement;
    }

    function showAdminName() {
        $sessionID = $_SESSION["id"];
        $connection = dbConnect();
        $sql = "SELECT * FROM admin WHERE id = :id";
        $statement = $connection->prepare($sql);
        $statement->execute(array($sessionID));
        return $statement;
    }

    function showUserName() {
        $sessionID = $_SESSION["id"];
        $connection = dbConnect();
        $sql = "SELECT * FROM user WHERE id = :id";
        $statement = $connection->prepare($sql);
        $statement->execute(array($sessionID));
        return $statement;
    }

    function showCreatedUsers() {
        $sessionID = $_SESSION["id"];
        $connection = dbConnect();
        $sql = "SELECT * FROM user WHERE createdByAdmin = :createdByAdmin";
        $statement = $connection->prepare($sql);
        $statement->execute(array($sessionID));
        return $statement;
    }

    function showAllowedUsers() {
        $sessionID = $_SESSION["id"];
        $connection = dbConnect();
        $sql = "SELECT * FROM room_permission WHERE allowedByAdmin = :allowedByAdmin";
        $statement = $connection->prepare($sql);
        $statement->execute(array($sessionID));
        return $statement;
    }

    function showPeopleInRoom() {
        $roomID = $_GET["toRoom"];
        $connection = dbConnect();
        $sql = "SELECT * FROM room_permission WHERE roomID = :roomID";
        $statement = $connection->prepare($sql);
        $statement->execute(array($roomID));
        return $statement;
    }

    function showCodedURL() {
        $connection = dbConnect();
        $codedURL = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $sql = "SELECT * FROM user_invitation WHERE codedURL = :codedURL AND createdDate >= NOW() - INTERVAL 1 DAY";
        $statement = $connection->prepare($sql);
        $statement->execute(array($codedURL));
        return $statement; 
    }

    function showInvalidCodedURL() {
        $connection = dbConnect();
        $codedURL = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $sql = "SELECT * FROM user_invitation WHERE codedURL = :codedURL AND createdDate <= NOW() - INTERVAL 1 DAY";
        $statement = $connection->prepare($sql);
        $statement->execute(array($codedURL));
        return $statement;
    }

    function showVerificationURL() {
        $connection = dbConnect();
        $codedURL = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $sql = "SELECT * FROM admin_verification WHERE codedURL = :codedURL AND createdDate >= NOW() - INTERVAL 3 DAY";
        $statement = $connection->prepare($sql);
        $statement->execute(array($codedURL));
        return $statement;
    }

    function showInvalidVerificationURL() {
        $connection = dbConnect();
        $codedURL = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $sql = "SELECT * FROM admin_verification WHERE codedURL = :codedURL AND createdDate <= NOW() - INTERVAL 3 DAY";
        $statement = $connection->prepare($sql);
        $statement->execute(array($codedURL));
        return $statement;
    }

    function findPersonByCode($resetCode) {
        $connection = dbConnect();
        $sql = "SELECT * FROM password_reset WHERE resetCode = :resetCode AND createdDate >= NOW() - INTERVAL 15 MINUTE";
        $statement = $connection->prepare($sql);
        $statement->execute(array($resetCode));
        return $statement;
    }

    function displayAdminProfilePicture($adminID) {
        $connection = dbConnect();
        $sql = "SELECT * FROM profile_picture WHERE adminID = :adminID";
        $statement = $connection->prepare($sql);
        $statement->execute(array($adminID));
        return $statement;
    }

    function displayUserProfilePicture($userID) {
        $connection = dbConnect();
        $sql = "SELECT * FROM profile_picture WHERE userID = :userID";
        $statement = $connection->prepare($sql);
        $statement->execute(array($userID));
        return $statement;
    }

    function displayOtherProfilePicture($adminID, $userID) {
        $connection = dbConnect();
        $sql = "SELECT * FROM profile_picture WHERE adminID = :adminID AND userID = :userID";
        $statement = $connection->prepare($sql);
        $statement->execute(array($adminID, $userID));
        return $statement;
    }

    function displayDefaultProfilePicture($id) {
        $connection = dbConnect();
        $sql = "SELECT * FROM profile_picture WHERE id = :id";
        $statement = $connection->prepare($sql);
        $statement->execute(array($id));
        return $statement;
    }

    //STATISTIKA
    function roomWithTheMostMessages($toCompany) {
        $connection = dbConnect();
        $sql = "SELECT toRoomName, COUNT(toRoomName) AS pocet_zprav FROM messages WHERE toCompany = :toCompany GROUP BY toRoomName ORDER BY pocet_zprav DESC LIMIT 3";
        $statement = $connection->prepare($sql);
        $statement->execute(array($toCompany));
        return $statement;
    }

    function whoWroteTheMostMessages($toCompany) {
        $connection = dbConnect();
        $sql = "SELECT fromUsername, COUNT(fromUsername) AS pocet_zprav FROM messages WHERE toCompany = :toCompany GROUP BY fromUsername ORDER BY pocet_zprav DESC LIMIT 3";
        $statement = $connection->prepare($sql);
        $statement->execute(array($toCompany));
        return $statement;
    }

    function newUsers($inCompany) {
        $connection = dbConnect();
        $sql = "SELECT * FROM user WHERE createdDate >= CURDATE() - INTERVAL 3 DAY AND inCompany = :inCompany";
        $statement = $connection->prepare($sql);
        $statement->execute(array($inCompany));
        return $statement;
    }

    function newRooms($companyName) {
        $connection = dbConnect();
        $sql = "SELECT * FROM room WHERE createdDate >= CURDATE() - INTERVAL 3 DAY AND companyName = :companyName";
        $statement = $connection->prepare($sql);
        $statement->execute(array($companyName));
        return $statement;
    }

    function theNewestUser($inCompany) {
        $connection = dbConnect();
        $sql = "SELECT * FROM user WHERE inCompany = :inCompany ORDER BY id DESC LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->execute(array($inCompany));
        return $statement;
    }

    function theOldestUser($inCompany) {
        $connection = dbConnect();
        $sql = "SELECT * FROM user WHERE inCompany = :inCompany ORDER BY id ASC LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->execute(array($inCompany));
        return $statement;
    }

    function whenAreMessagesSent() {
        $connection = dbConnect();
        $sql = "SELECT * FROM messages ORDER BY sendTime ASC";
        $statement = $connection->prepare($sql);
        $statement->execute(array());
        return $statement;
    }

    function theOldestRoom($companyName) {
        $connection = dbConnect();
        $sql = "SELECT * FROM room WHERE companyName = :companyName ORDER BY id ASC LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->execute(array($companyName));
        return $statement;
    }

    function theNewestRoom($companyName) {
        $connection = dbConnect();
        $sql = "SELECT * FROM room WHERE companyName = :companyName ORDER BY id DESC LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->execute(array($companyName));
        return $statement;
    }

    //FUNKCE K SELECTŮM
    function emailExists($email) {
        $resultAdmin = findAdminByEmail($email);
        $resultUser = findUserByEmail($email);
        return $resultAdmin->rowCount() > 0 OR $resultUser->rowCount() > 0;
    }

    function companyExists($companyName) {
        $result = findCompanyByName($companyName);
        return $result->rowCount() > 0;
    }

    function identificationNumberExists($findIdentificationNumber) {
        $result = findIdentificationNumber($findIdentificationNumber);
        return $result->rowCount() >0;
    }

    function roomExists($roomName, $companyName) {
        $result = findRoomByName($roomName, $companyName);
        return $result->rowCount() > 0;
    }

    function emailInvitationExists($email) {
        $result = findUserByEmailInvitation($email);
        return $result->rowCount() > 0;
    }

    function accessToRoomExists($userID, $roomID) {
        $result = findUserByPermission($userID, $roomID);
        return $result->rowCount() > 0;
    }

    function userDisplayExists($createdByAdmin) {
        $result = showCreatedUsers($createdByAdmin);
        return $result->rowCount() > 0;
    }

    function roomDisplayExists($adminID) {
        $result = showRoom($adminID);
        return $result->rowCount() > 0;
    }

    function profileAdminPictureExists($adminID) {
        $result = displayAdminProfilePicture($adminID);
        return $result->rowCount() > 0;
    }

    function profileUserPictureExists($userID) {
        $result = displayUserProfilePicture($userID);
        return $result->rowCount() > 0;
    }

    function isValidEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return true;
    }


    //FUNKCE K HESLŮM
    function createPasswordHash($strPassword, $numAlgo = 1, $arrOptions = array()) {
        $hash = password_hash($strPassword, $numAlgo, $arrOptions);
        return $hash;
    }

    function verifyPasswordHash($strPassword, $strHash) {
        $boolReturn = password_verify($strPassword, $strHash);
        return $boolReturn;
    }

    //FUNKCE K MESSAGE INFU
    function writeErrorMessage($errorMessage) {
        require_once "common/header.php";
        echo "<div class='errorMessageWindow'><p class='errorMessageText'>" . $errorMessage . "</p></div>";
    }

    function writeSuccessfulMessage($succesfulMessage) {
        require_once "common/header.php";
        echo "<div class='successfulMessageWindow'><p class='successfulMessageText'>" . $succesfulMessage . "</p></div>";
    }

    //FUNKCE K AUTENTIZACI DO HEADERU
    function isAdminAuthenticated() {
        return isset($_SESSION["email"])
            && isset($_SESSION["firstname"])
            && isset($_SESSION["surname"]);
    }

    function isUserAuthenticated() {
        return isset($_SESSION["emailUser"])
            && isset($_SESSION["firstnameUser"])
            && isset($_SESSION["surnameUser"]);
    }

    //FUNKCE K AUTENTIZACI NA VŠECH STRÁNKÁCH
    function isAdminAuthorized() {
        return isset($_SESSION["email"])
            && isset($_SESSION["id"])
            && isset($_SESSION["firstname"])
            && isset($_SESSION["surname"]);
    }

    function isUserAuthorized() {
        return isset($_SESSION["emailUser"])
            && isset($_SESSION["id"])
            && isset($_SESSION["firstnameUser"])
            && isset($_SESSION["surnameUser"]);
    }


    //UPDATY
    function rewriteAdminDataDetails($firstname, $surname, $modifiedDate) {
        $connection = dbConnect();
        $sessionID = $_SESSION["id"];
        $sql = "UPDATE admin SET firstname = :firstname, surname = :surname, modifiedDate = now() WHERE id = " . $sessionID;
        $statement = $connection->prepare($sql);
        $statement->execute(array($firstname, $surname));
        return $statement;
    }

    function rewriteUserDataDetails($firstnameUser, $surnameUser, $modifiedDate) {
        $connection = dbConnect();
        $sessionID = $_SESSION["id"];
        $sql = "UPDATE user SET firstnameUser = :firstnameUser, surnameUser = :surnameUser, modifiedDate = now() WHERE id = " . $sessionID;
        $statement = $connection->prepare($sql);
        $statement->execute(array($firstnameUser, $surnameUser));
        return $statement;
    }

    function rewriteAdminDataPassword($password, $modifiedDate) {
        $connection = dbConnect();
        $sessionID = $_SESSION["id"];
        $sql = "UPDATE admin SET password = :hash, modifiedDate = now() WHERE id = " . $sessionID;
        $statement = $connection->prepare($sql);
        $hash = createPasswordHash($password);
        $statement->execute(array($hash));
        return $statement;
    }

    function rewriteNotAuthenticatedAdminDataPassword($password, $modifiedDate, $emailAgain) {
        $connection = dbConnect();
        $sql = "UPDATE admin SET password = :password, modifiedDate = now() WHERE email = :emailAgain";
        $statement = $connection->prepare($sql);
        $hash = createPasswordHash($password);
        $statement->execute(array($hash, $emailAgain));
        return $statement;
    }

    function rewriteUserDataPassword($password, $modifiedDate) {
        $connection = dbConnect();
        $sessionID = $_SESSION["id"];
        $sql = "UPDATE user SET password = :hash, modifiedDate = now() WHERE id = " . $sessionID;
        $statement = $connection->prepare($sql);
        $hash = createPasswordHash($password);
        $statement->execute(array($hash));
        return $statement;
    }

    function rewriteNotAuthenticatedUserDataPassword($password, $modifiedDate, $emailAgain) {
        $connection = dbConnect();
        $sql = "UPDATE user SET password = :password, modifiedDate = now() WHERE emailUser = :emailAgain";
        $statement = $connection->prepare($sql);
        $hash = createPasswordHash($password);
        $statement->execute(array($hash, $emailAgain));
        return $statement;
    }

    function updateProfilePicture($name, $image, $adminID, $userID) {
        $connection = dbConnect();
        $sql = "UPDATE profile_picture SET name = :name, image = :image WHERE adminID = :adminID AND userID = :userID";
        $statement = $connection->prepare($sql);
        $statement->execute(array($name, $image, $adminID, $userID));
        return $statement;
    }

    function updateVerification($verified, $email) {
        $connection = dbConnect();
        $sql = "UPDATE admin SET verified = :verified WHERE email = :email";
        $statement = $connection->prepare($sql);
        $statement->execute(array($verified, $email));
        return $statement;
    }


    //DELETY
    function deleteMessages() {
        $connection = dbConnect();
        $roomID = $_GET["deleteRoom"];
        $sql = "DELETE FROM `messages` WHERE toRoom = :toRoom";
        $statement = $connection->prepare($sql);
        $statement->execute(array($roomID));
        return $statement;
    }

    function deleteRoom() {
        $connection = dbConnect();
        $roomID = $_GET["deleteRoom"];
        $sessionID = $_SESSION["id"];
        $sql = "DELETE FROM `room` WHERE id = :id AND adminID = :adminID";
        $statement = $connection->prepare($sql);
        $statement->execute(array($roomID, $sessionID));
        return $statement;
    }

    function deleteUsersInRoom() {
        $connection = dbConnect();
        $roomID = $_GET["deleteRoom"];
        $sql = "DELETE FROM `room_permission` WHERE roomID = :roomID";
        $statement = $connection->prepare($sql);
        $statement->execute(array($roomID));
        return $statement;
    }

    function leaveRoom() {
        $connection = dbConnect();
        $userID = $_SESSION["id"];
        $roomID = $_GET["leaveRoom"];
        $sql = "DELETE FROM `room_permission` WHERE userID = :userID AND roomID = :roomID";
        $statement = $connection->prepare($sql);
        $statement->execute(array($userID, $roomID));
        return $statement;
    }

    function deleteUser() {
        $connection = dbConnect();
        $userID = $_GET["deleteUser"];
        $sessionID = $_SESSION["id"];
        $sql = "DELETE FROM `user` WHERE id = :id AND createdByAdmin = :createdByAdmin";
        $statement = $connection->prepare($sql);
        $statement->execute(array($userID, $sessionID));
        return $statement;
    }

    function removeUserFromAllRooms() {
        $connection = dbConnect();
        $userID = $_GET["deleteUser"];
        $sessionID = $_SESSION["id"];
        $sql = "DELETE FROM `room_permission` WHERE userID = :userID AND allowedByAdmin = :allowedByAdmin";
        $statement = $connection->prepare($sql);
        $statement->execute(array($userID, $sessionID));
        return $statement;
    }

    function deleteCode($personEmail) {
        $connection = dbConnect();
        $resetCode = $_POST["code"];
        $sql = "DELETE FROM `password_reset` WHERE personEmail = :personEmail";
        $statement = $connection->prepare($sql);
        $statement->execute(array($personEmail));
        return $statement;
    }

    function removeUserFromRoom() {
        $connection = dbConnect();
        $id = $_GET["removeUserFromRoom"];
        $sessionID = $_SESSION["id"];
        $sql = "DELETE FROM `room_permission` WHERE id = :id AND allowedByAdmin = :allowedByAdmin";
        $statement = $connection->prepare($sql);
        $statement->execute(array($id, $sessionID));
        return $statement;
    }

    function deleteInvitation($emailUser, $codedURL) {
        $connection = dbConnect();
        $sql = "DELETE FROM user_invitation WHERE emailUser = :emailUser AND codedURL = :codedURL";
        $statement = $connection->prepare($sql);
        $statement->execute(array($emailUser, $codedURL));
        return $statement;
    }

    function deleteVerificationCode($emailAdmin) {
        $connection = dbConnect();
        $sql = "DELETE FROM admin_verification WHERE emailAdmin = :emailAdmin";
        $statement = $connection->prepare($sql);
        $statement->execute(array($emailAdmin));
        return $statement;
    }
?>