<?php
header("Access-Control-Allow-Origin: https://localhost");
header("Content-Type: multipart/form-data");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


    $target_dir = "/var/www/html/finki_blog/img/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        http_response_code(415);
        echo json_encode("File is not an image.");
        $uploadOk = 0;
    }
    // Check if file already exists
    if (file_exists($target_file)) {
        http_response_code(406);
        echo json_encode("Sorry, file already exists.");
        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        http_response_code(413);
        echo json_encode("Sorry, your file is too large.");
        $uploadOk = 0;
    }
    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif") {
        http_response_code(415);
        echo json_encode("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo json_encode("Sorry, your file was not uploaded.");
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo json_encode("The file ". basename($_FILES["fileToUpload"]["name"]). " has been uploaded.");
        } else {
            http_response_code(500);
            echo json_encode("Sorry, there was an error uploading your file.");
        }
    }
?>
