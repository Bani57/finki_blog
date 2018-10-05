<?php
header("Access-Control-Allow-Origin: https://localhost");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

include_once '../config/database.php';


$database = new Database();
$db = $database->getConnection();

$post = isset($_GET['post']) ? $_GET['post'] : die();

$q = $db->prepare("SELECT * FROM Comment WHERE post = :post ORDER BY `date` DESC");
$q->bindParam(":post", $post, PDO::PARAM_INT, 10);


if ($q->execute()) {
    echo json_encode($q->fetchAll(PDO::FETCH_ASSOC));
} else {
  http_response_code(500);
    echo json_encode(
   array('message' => 'Error while getting comments.', 'error' => $q->errorInfo() )
);
}
?>
