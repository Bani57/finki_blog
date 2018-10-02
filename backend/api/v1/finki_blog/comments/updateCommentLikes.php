<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PATCH");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

include_once '../config/database.php';


$database = new Database();
$db = $database->getConnection();

$user = isset($_GET['user']) ? $_GET['user'] : die();
$post = isset($_GET['post']) ? $_GET['post'] : die();
$date = isset($_GET['date']) ? $_GET['date'] : die();
$amount = isset($_GET['amount']) ? $_GET['amount'] : die();

$q = $db->prepare("UPDATE Comment SET likes = :amount WHERE user = :user AND post = :post AND date = :date");
$q->bindParam(":user", $user, PDO::PARAM_STR, 30);
$q->bindParam(":post", $post, PDO::PARAM_INT, 10);
$q->bindParam(":date", $date, PDO::PARAM_STR, 50);
$q->bindParam(":amount", $amount, PDO::PARAM_STR, 30);


if ($q->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(
   array('message' => 'Error while updating likes of post.', 'error' => $q->errorInfo() )
);
}
?>
