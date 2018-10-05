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

$comment = json_decode(file_get_contents("php://input"));

$q = $db->prepare(
    "INSERT INTO Comment
      (
        user,
        post,
        `date`,
        content,
        likes
      )
       VALUES (
         :user,
         :post,
         SYSDATE(),
         :content,
         0
       )"
     );

$q->bindParam(":user", $comment->user, PDO::PARAM_STR, 30);
$q->bindParam(":post", $comment->post, PDO::PARAM_INT, 10);
$q->bindParam(":content", $comment->content, PDO::PARAM_STR, 200);


if ($q->execute()) {
    echo json_encode(true);
} else {
  http_response_code(500);
    echo json_encode(
 array('message' => 'Error while adding comment.', 'error' => $q->errorInfo() )
);
}
?>
