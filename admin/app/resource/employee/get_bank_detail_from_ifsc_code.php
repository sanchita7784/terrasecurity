<?php 
$response = ["status" => 0, "msg" => "Unknown Error", "data" => [] ];

$ifsc = $_GET['ifsc_code'];
try
{
    $url = "https://ifsc.razorpay.com/$ifsc";
    $res = curl_get_request($url);

    if (!$res)
    {
        throw new Exception("No Api Response");
    }

    $res = (array) json_decode($res);

    if ($res == "Not Found")
    {
        throw new Exception("Invalid IFSC Code");
    }
    else
    {
        $response["data"] = $res;
    }

    $response['status'] = 1;
}
catch (Exception $ex)
{
    $response["status"] = 0;
    $response["msg"] = $ex->getMessage();
}

echo json_encode($response);
exit;