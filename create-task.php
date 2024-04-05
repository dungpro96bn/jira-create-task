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
    $description = $_POST['description'];
    $assignee = $_POST['assignee'];
    $duedate = $_POST['duedate'];

    function convertToJiraFormat($htmlContent)
    {
        $doc = new DOMDocument();
        $doc->loadHTML('<?xml encoding="UTF-8">' . $htmlContent);
        $content = [];

        $imageUrls = [];
        foreach ($doc->getElementsByTagName('img') as $image) {
            $imageUrls[] = $image->getAttribute('src');
        }

        foreach ($doc->getElementsByTagName('p') as $node) {
            $paragraph = [
                'type' => 'paragraph',
                'content' => [],
            ];

            foreach ($node->childNodes as $childNode) {
                if ($childNode->nodeName === '#text') {
                    $paragraph['content'][] = [
                        'type' => 'text',
                        'text' => $childNode->textContent,
                    ];
                } else if ($childNode->nodeName === 'br') {
                    $paragraph['content'][] = [
                        'type' => 'text',
                        'text' => '\n',
                    ];
                }
            }

            $content[] = $paragraph;
        }

        return [
            'type' => 'doc',
            'version' => 1,
            'content' => $content,
            'imageUrls' => $imageUrls,
        ];
    }

    $imagePath = 'https://tour-tagger-travel.vccdev.vn/wp-content/uploads/hotel_pic01_pc.png';
    $imageData = base64_encode(file_get_contents($imagePath));
    $imageMimeType = 'image/jpeg';
// Chèn ảnh vào trường description
    $jiraDescription['content'][0]['content'][] = [
        'type' => 'media',
        'attrs' => [
            'id' => 'unique_media_id',
            'type' => 'file',
            'collection' => 'attachment',
            'width' => 100, // Thay đổi kích thước ảnh nếu cần
            'height' => 100,
            'url' => 'data:' . $imageMimeType . ';base64,' . $imageData,
        ],
    ];

    $htmlContent = $description;
    $jiraDescription = convertToJiraFormat($htmlContent);

//    var_dump($jiraDescription);

//    die();

    try {
        $issueData = [
            'fields' => [
                'project' => [
                    'key' => $projectKey,
                ],
                'summary' => $summary,
                'description' => $jiraDescription,
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

