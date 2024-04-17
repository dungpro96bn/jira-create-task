<?php
session_start();
require 'app/Config/apiConfig.php';
require 'vendor/autoload.php';
require 'functions.php';
use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => $baseUrl,
    'headers' => [
        'Authorization' => 'Basic ' . base64_encode("$email:$apiToken"),
        'Content-Type' => 'application/json',
    ],
]);


$response = $client->get('/rest/api/3/search', [
    'query' => [
        'jql' => 'project = ' . $projectKey,
        'maxResults' => 5,
        'startAt' => 0,
        'fields' => 'summary,status,assignee',
    ],
]);

$data = json_decode($response->getBody(), true);

var_dump($data);

//die();

echo $data['total'];
$num = 0;

foreach ($data['issues'] as $issue) {
    echo '<li>'.$num++.$issue['key'] . ': ' . $issue['fields']['summary'].'</li>';
}