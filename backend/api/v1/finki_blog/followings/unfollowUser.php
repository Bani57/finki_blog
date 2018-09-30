<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

include_once '../config/database.php';


$database = new Database();
$db = $database->getConnection();

$user1 = isset($_GET['user1']) ? $_GET['user1'] : die();
$user2 = isset($_GET['user2']) ? $_GET['user2'] : die();

$q = $db->prepare("DELETE FROM Following WHERE user1 = :user1 AND user2 = :user2");
$q->bindParam(":user1", $user1, PDO::PARAM_STR, 30);
$q->bindParam(":user2", $user2, PDO::PARAM_STR, 30);


if ($q->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(
   array('message' => 'Error while unfollowing user.', 'error' => $q->errorInfo() )
);
}
?>
