<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$user = json_decode(file_get_contents("php://input"));

$q = $db->prepare("INSERT INTO User
  (
    username,
    password,
    firstName,
    lastName,
    email,
    picture
  )
   VALUES (
     :username,
     :password,
     :firstName,
     :lastName,
     :email,
     :picture
   )"
 );

$q->bindParam(":username", $user->$username, PDO::PARAM_STR, 30);
$q->bindParam(":password", $user->$password, PDO::PARAM_STR, 64);
$q->bindParam(":firstName", $user->$firstName, PDO::PARAM_STR, 20);
$q->bindParam(":lastName", $user->$lastName, PDO::PARAM_STR, 30);
$q->bindParam(":email", $user->$email, PDO::PARAM_STR, 50);
$q->bindParam(":picture", $user->$picture, PDO::PARAM_STR, 100);

if ($q->execute()) {
    echo json_encode($user->$username);
} else {
  echo json_encode(
 array('message' => 'Error while adding user.', 'error' => $q->errorInfo() )
);
}
?>
