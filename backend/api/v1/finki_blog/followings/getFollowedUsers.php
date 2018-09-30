<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

include_once '../config/database.php';


$database = new Database();
$db = $database->getConnection();

$user = isset($_GET['user']) ? $_GET['user'] : die();

$q = $db->prepare("SELECT F.user2 AS user, F.sinceDate AS sinceDate FROM Following F WHERE F.user1 = :user ORDER BY F.sinceDate DESC");
$q->bindParam(":user", $user, PDO::PARAM_STR, 30);


if ($q->execute()) {
    echo json_encode($q->fetchAll(PDO::FETCH_ASSOC));
} else {
    echo json_encode(
   array('message' => 'Error while getting followed users.', 'error' => $q->errorInfo() )
);
}
