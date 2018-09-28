<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

include_once '../config/database.php';


$database = new Database();
$db = $database->getConnection();

$username = isset($_GET['username']) ? $_GET['username'] : die();

$q = $db->prepare("DELETE FROM User WHERE username = :username");
$q->bindParam(":username", $username, PDO::PARAM_STR, 30);


if ($q->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(
   array('message' => 'Error while deleting user.', 'error' => $q->errorInfo() )
);
}
?>
