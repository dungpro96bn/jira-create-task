<?php
require 'app/Config/apiConfig.php';
require 'vendor/autoload.php';
use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => $baseUrl,
    'headers' => [
        'Authorization' => 'Basic ' . base64_encode("$email:$apiToken"),
        'Content-Type' => 'application/json',
    ],
]);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $summary = $_POST['summary'];
    $arrayDescription = $_POST['description'];
    $assignee = $_POST['assignee'];
    $duedate = $_POST['duedate'];

    $description = json_decode($arrayDescription, true);

    try {
        $issueData = [
            'fields' => [
                'project' => [
                    'key' => $projectKey,
                ],
                'summary' => $summary,
                'description' => $description,
                'issuetype' => [
                    'name' => 'Task',
                ],
            ],
        ];

        if ($assignee != "") {
            $issueData['fields']['assignee'] = ['accountId' => $assignee];
        }

        if($duedate != ""){
            $issueData['fields']['duedate'] = $duedate;
        }

        $response = $client->post('/rest/api/3/issue', [
            'json' => $issueData,
        ]);

        $data = json_decode($response->getBody(), true);

        echo '<p class="note">Task created successfully. Key: ' . $data['key'] . '</p>';
        echo '<a target="_blank" href="https://dev-scvweb.atlassian.net/jira/core/projects/TASK/board?groupBy=status&selectedIssue='.$data['key'].'">Link Task</a>';

    } catch (GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
        $responseBodyAsString = $response->getBody()->getContents();
        echo  "Error: " . $responseBodyAsString;
    }

}

