<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;

$baseUrl = 'https://dev-scvweb.atlassian.net';
$apiToken = 'ATATT3xFfGF0f6dtQu5xBqd7p53gZQlLY4AoXa6NoInD0wDxvgTSEfwj8uUiNp_np101PEOqG4TtQMwMAqifbu4J_-At2p0oq14BgvHvRXLk_aKmvm5CD-lyqDjfBSVy6fYp2-M3nV0DByyihokYx_bC7u1AuTIQ9yhJHTBr-HxGUgYWxcit3V4=0EC3314D';
$email = 'b1dung@sougo-career-vietnam.com';
$projectKey = 'TASK';

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
        'maxResults' => 150,
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