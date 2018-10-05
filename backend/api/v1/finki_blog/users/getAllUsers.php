<?php
header("Access-Control-Allow-Origin: https://localhost");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

include_once '../config/database.php';


$database = new Database();
$db = $database->getConnection();

$q = $db->prepare("SELECT * FROM User ORDER BY username");

if ($q->execute()) {
    echo json_encode($q->fetchAll(PDO::FETCH_ASSOC));
} else {
    http_response_code(500);
    echo json_encode(
   array('message' => 'Error while getting users.', 'error' => $q->errorInfo() )
);
}
?>
