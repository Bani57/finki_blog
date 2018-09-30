<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

include_once '../config/database.php';


$database = new Database();
$db = $database->getConnection();

$author = isset($_GET['author']) ? $_GET['author'] : die();

$q = $db->prepare("SELECT * FROM Post WHERE author = :author ORDER BY `date` DESC");
$q->bindParam(":author", $author, PDO::PARAM_STR, 30);


if ($q->execute()) {
    echo json_encode($q->fetchAll(PDO::FETCH_ASSOC));
} else {
    echo json_encode(
   array('message' => 'Error while getting posts.', 'error' => $q->errorInfo() )
);
}
?>
