<?php
require_once './app/include/FileUtility.php';
require_once './app/include/config.php';

$response = ["status" => 1, "msg" => ""];

try
{
    $base64 = $_POST['base64'] ?? null;
    $filename = $_POST['filename'] ?? null;

    if (!$base64)
    {
        throw new Exception("base64 not found in request");
    }

    if (!$filename)
    {
        throw new Exception("filename not found in request");
    }

    $path = "storage/files/temp";

    $response['file'] = FileUtility::base64ToFile($base64, $path, $filename);
    $response['filename'] = pathinfo($response['file'], PATHINFO_BASENAME);
}
catch(Exception $ex)
{
    $response['status'] = 0;
    $response['msg'] = $ex->getMessage();
}

echo json_encode($response);