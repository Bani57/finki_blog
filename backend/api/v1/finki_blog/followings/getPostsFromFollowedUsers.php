<?php
header("Access-Control-Allow-Origin: https://localhost");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

include_once '../config/database.php';


$database = new Database();
$db = $database->getConnection();

$user = isset($_GET['user']) ? $_GET['user'] : die();

$q = $db->prepare("SELECT P.* FROM Post P, Following F WHERE author = F.user2 AND F.user1 = :user ORDER BY P.date DESC");
$q->bindParam(":user", $user, PDO::PARAM_STR, 30);


if ($q->execute()) {
    echo json_encode($q->fetchAll(PDO::FETCH_ASSOC));
} else {
  http_response_code(500);
    echo json_encode(
   array('message' => 'Error while getting posts from followed users.', 'error' => $q->errorInfo() )
);
}
?>
