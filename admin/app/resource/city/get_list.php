<?php
require_once './app/model/City.php';

$state_id = isset($_GET['state_id']) ? $_GET['state_id'] : 0;

$city = new App\Model\City();
$city_records = $city->find(["id", "city"], ["state_id" => $state_id]);

$res = [
    "status" => 1,
    "data" => []
];

foreach($city_records as $city_record)
{
    $res['data'][] = [
        "id" => $city_record['id'],
        "name" => $city_record['city']
    ];
}

header('Content-Type: application/json');

echo json_encode($res); exit;
