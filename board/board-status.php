<?php
session_start();
require '../app/Config/apiConfig.php';
require '../vendor/autoload.php';
require '../functions.php';

use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => $baseUrl,
    'headers' => [
        'Authorization' => 'Basic ' . base64_encode("$email:$apiToken"),
        'Content-Type' => 'application/json',
    ],
]);

foreach ($columns as $column):
    $columnId = $column;
    $response = $client->get('/rest/api/3/search', [
        'query' => [
            'jql' => "status=$columnId AND parent is EMPTY",
            'fields' => 'summary,status,parent,description,assignee,duedate'
        ],
    ]);

    $columnData = json_decode($response->getBody(), true);
    $issues = $columnData['issues'];

    if($columnId == 10095){
        $idBoard = "todo";
        $title = "To Do";
    } elseif ($columnId == 10096){
        $idBoard = "in-progress";
        $title = "In Progress";
    } elseif ($columnId == 10097){
        $idBoard = "done";
        $title = "Done";
    } ?>

<?php
endforeach;
?>