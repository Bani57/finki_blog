<?php
header("Access-Control-Allow-Origin: https://localhost");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$user1 = isset($_GET['user1']) ? $_GET['user1'] : die();
$user2 = isset($_GET['user2']) ? $_GET['user2'] : die();

$q = $db->prepare(
    "INSERT INTO Following
  (
    user1,
    user2,
    sinceDate
  )
   VALUES (
     :user1,
     :user2,
     SYSDATE()
   )"
 );

$q->bindParam(":user1", $user1, PDO::PARAM_STR, 30);
$q->bindParam(":user2", $user2, PDO::PARAM_STR, 30);


if ($q->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(
 array('message' => 'Error while following user.', 'error' => $q->errorInfo() )
);
}
