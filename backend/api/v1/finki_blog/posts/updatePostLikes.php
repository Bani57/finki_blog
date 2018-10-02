<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PATCH");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

include_once '../config/database.php';


$database = new Database();
$db = $database->getConnection();

$id = isset($_GET['id']) ? $_GET['id'] : die();
$amount = isset($_GET['amount']) ? $_GET['amount'] : die();

$q = $db->prepare("UPDATE Post SET likes = :amount WHERE id = :id");
$q->bindParam(":id", $id, PDO::PARAM_STR, 30);
$q->bindParam(":amount", $amount, PDO::PARAM_STR, 30);


if ($q->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(
   array('message' => 'Error while updating likes of post.', 'error' => $q->errorInfo() )
);
}
?>
