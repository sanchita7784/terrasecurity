<?php

require_once './app/include/FileUtility.php';
require_once './app/include/config.php';

$fileUtility = new FileUtility(1024 * 1024 * 1024);

$response = [
    "msg" => ""
];

header('Content-Type: application/json');

if ($fileUtility->uploadFile($_FILES['file'], "storage/files/temp"))
{
    $response["msg"] = "File uploaded successfully";
    $response["file"] = [
        "path" => $fileUtility->path,
        "filename" => $fileUtility->filename . "." . $fileUtility->extension,
        "url" => BASE_URL . $fileUtility->path . $fileUtility->filename . "." . $fileUtility->extension
    ];

    http_response_code(200);
    echo json_encode($response);
}
else
{
    $response["msg"] = implode(", ", $fileUtility->errors);

    http_response_code(400);
    echo json_encode($response);
}

