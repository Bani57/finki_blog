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

$post = json_decode(file_get_contents("php://input"));
$username = isset($_GET['username']) ? $_GET['username'] : die();
$post->content = shell_exec('bash /home/Bani57/root/ca/GEZOPO/signPost.sh ' . $username . ' ' . $post->userPass . ' "' . addslashes($post->content) . '"');

$q = $db->prepare(
    "INSERT INTO Post
      (
        title,
        content,
        `date`,
        likes,
        author
      )
       VALUES (
         :title,
         :content,
         SYSDATE(),
         0,
         :author
       )"
     );

$q->bindParam(":title", $post->title, PDO::PARAM_STR, 50);
$q->bindParam(":content", $post->content, PDO::PARAM_STR, 2000);
$q->bindParam(":author", $username, PDO::PARAM_STR, 30);

if ($q->execute()) {
    echo json_encode($db->lastInsertId());
} else {
  http_response_code(500);
    echo json_encode(
 array('message' => 'Error while adding post.', 'error' => $q->errorInfo() )
);
}
?>
