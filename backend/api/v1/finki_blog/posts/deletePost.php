<?php
header("Access-Control-Allow-Origin: https://localhost");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

include_once '../config/database.php';


$database = new Database();
$db = $database->getConnection();

$id = isset($_GET['id']) ? $_GET['id'] : die();

$q = $db->prepare("DELETE FROM Post WHERE id = :id");
$q->bindParam(":id", $id, PDO::PARAM_STR, 30);


if ($q->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(
   array('message' => 'Error while deleting post.', 'error' => $q->errorInfo() )
);
}
?>
