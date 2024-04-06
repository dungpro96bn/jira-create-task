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

$columns = [10095, 10096, 10097];

//$response = $client->get('/rest/api/3/search', [
//    'query' => [
//        'jql' => "status=$todo_column_id AND parent is EMPTY",
//        'fields' => 'summary,status,parent,description,assignee,duedate'
//    ],
//]);

//$data = json_decode($response->getBody(), true);

//var_dump($columns);

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

        <div id="<?php if($idBoard){echo $idBoard;} ?>" class="board-item board-<?php if($idBoard){echo $idBoard;} ?>">
            <p class="title-column"><?php if($title){echo $title;} ?></p>
            <div class="boardContent-list">
                <?php
                if ($response->getStatusCode() == 200):
                    $issues = $columnData['issues'];
                    foreach ($issues as $issue):?>
                        <div class="task-item" data-task-id="<?php echo $issue['id']; ?>">
                            <div class="taskItem-inner">
                                <h4 class="board-summary"><?php echo $issue['fields']['summary']; ?></h4>
                                <?php if (isset($issue['fields']['duedate'])): ?>
                                    <?php
                                    $currentDate = date('Y-m-d');
                                    $dateString = $issue['fields']['duedate'];
                                    $date = date_create_from_format('Y-m-d', $dateString);
                                    $formattedDate = date_format($date, 'd M');
                                    ?>
                                    <div class="dueDate <?php if ($currentDate == $dateString) {
                                        echo "deadline";
                                    } ?>" title="<?php echo "Due Date: " . $issue['fields']['duedate']; ?>">
                                        <p>
                                            <svg width="16" height="16" viewBox="0 0 24 24" role="presentation">
                                                <path d="M4.995 5h14.01C20.107 5 21 5.895 21 6.994v12.012A1.994 1.994 0 0119.005 21H4.995A1.995 1.995 0 013 19.006V6.994C3 5.893 3.892 5 4.995 5zM5 9v9a1 1 0 001 1h12a1 1 0 001-1V9H5zm1-5a1 1 0 012 0v1H6V4zm10 0a1 1 0 012 0v1h-2V4zm-9 9v-2.001h2V13H7zm8 0v-2.001h2V13h-2zm-4 0v-2.001h2.001V13H11zm-4 4v-2h2v2H7zm4 0v-2h2.001v2H11zm4 0v-2h2v2h-2z" fill="currentColor" fill-rule="evenodd"></path>
                                            </svg>
                                            <span><?php echo $formattedDate; ?></span>
                                        </p>
                                    </div>
                                <?php endif; ?>
                                <div class="key-assignee">
                                    <p class="key"><img src="https://dev-scvweb.atlassian.net/rest/api/2/universal_avatar/view/type/issuetype/avatar/10318?size=medium" alt=""><?php echo $issue["key"]; ?></p>
                                    <p class="assignee"><img src="<?php echo $issue['fields']['assignee']["avatarUrls"]["48x48"]; ?>" alt=""></p>
                                </div>
                                <div class="description">
                                    <?php
                                    //var_dump($issue['fields']['description']) ;
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach;
                endif; ?>
            </div>

        </div>

    <?php
endforeach;
?>