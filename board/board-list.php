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
//$columns = [10054, 10055, 10060, 10061, 10062, 10080, 10056, 10081, 10076];

foreach ($columns as $column):
    $columnId = $column;
    $response = $client->get('/rest/api/3/search', [
        'query' => [
            'jql' => "status=$columnId AND parent is EMPTY",
            'fields' => 'summary,status,parent,description,assignee,duedate,priority'
        ],
    ]);

    $columnData = json_decode($response->getBody(), true);
    $issues = $columnData['issues'];

    if($columnId == 10095){
        $idBoard = "todo";
        $title = "To Do";
        $color = "#44546f";
        $bg = "#DFE1E6";
    } elseif ($columnId == 10096){
        $idBoard = "in-progress";
        $title = "In Progress";
        $color = "#05c";
        $bg = "#DEEBFF";
    } elseif ($columnId == 10097){
        $idBoard = "done";
        $title = "Done";
        $color = "#216e4e";
        $bg = "#E3FCEF";
    }

    ?>


    <?php
    if ($response->getStatusCode() == 200):
    $issues = $columnData['issues'];
    $taskCount = count($issues);
    ?>
        <div data-column="<?php echo $columnId; ?>" id="<?php if($idBoard){echo $idBoard;} ?>" class="board-item board-<?php if($idBoard){echo $idBoard;} ?>">
        <p class="title-column"><span style="background: <?php echo $bg; ?> ;color: <?php echo $color; ?>"><?php if($title){echo $title;} ?></span><span class="task-count"><?php echo $taskCount; ?></span></p>
            <div class="boardContent-list">
                <?php foreach ($issues as $issue):?>
<!--                --><?php //var_dump($issue); ?>
                    <div class="task-item" data-task-id="<?php echo $issue['id']; ?>">
                        <div class="taskItem-inner">
                            <a href="#<?php echo $issue['id']; ?>">
                                <h4 class="board-summary"><?php echo $issue['fields']['summary']; ?></h4>
                                <?php if (isset($issue['fields']['duedate'])): ?>
                                    <?php
                                    $currentDate = date('Y-m-d');
                                    $dateString = $issue['fields']['duedate'];
                                    $date = date_create_from_format('Y-m-d', $dateString);
                                    $formattedDate = date_format($date, 'd M');
                                    ?>
                                    <div class="dueDate <?php if ($currentDate >= $dateString) {
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
                                    <p class="icon-priority" title="<?php echo $issue['fields']['priority']['name']; ?>"><img src="<?php echo $issue['fields']['priority']['iconUrl']; ?>" alt=""></p>
                                    <?php if(isset($issue['fields']['assignee']) && $issue['fields']['assignee'] !== null):?>
                                    <p class="assignee"><img title="<?php echo $issue['fields']['assignee']["displayName"]; ?>" src="<?php echo $issue['fields']['assignee']["avatarUrls"]["48x48"]; ?>" alt=""></p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>


                        <div id="<?php echo $issue['id']; ?>" class="taskContent-popup">
                            <div class="popup-inner">
                                <div class="taskBox">
                                    <span class="close-popup"><i class="fa-solid fa-xmark"></i></span>
                                    <div class="left-taskInfo">
                                        <p class="key"><img src="https://dev-scvweb.atlassian.net/rest/api/2/universal_avatar/view/type/issuetype/avatar/10318?size=medium" alt=""><?php echo $issue["key"]; ?></p>
                                        <h4 class="board-summary"><?php echo $issue['fields']['summary']; ?></h4>
                                        <div class="taskInfo-item description">
                                            <p class="info-title">Description</p>
                                            <?php
                                            $descriptionArray = $issue['fields']['description'];
                                            var_dump($descriptionArray);
                                            ?>
                                        </div>
                                        <div class="taskInfo-item child-issues">
                                            <p class="info-title">Child issues</p>

                                            <?php
                                            $parentId = $issue['id'];

                                            $response = $client->get('/rest/api/3/search', [
                                                'query' => [
                                                    'jql' => "parent=$parentId",
                                                    'fields' => 'summary,status,parent,description,assignee,duedate,priority'
                                                ],
                                            ]);

                                            $parentData = json_decode($response->getBody(), true);
                                            $childIssues = $parentData['issues'];

                                            $countToDo = 0;
                                            $countInProgress = 0;
                                            $countDone = 0;
                                            $totalItems = count($childIssues);
                                            ?>
                                            <?php foreach ($childIssues as $childIssue){
                                                if ($childIssue['fields']['status']['name'] == 'Done'){
                                                    $countDone++;
                                                } elseif ($childIssue['fields']['status']['name'] == 'In Progress'){
                                                    $countInProgress++;
                                                } elseif ($childIssue['fields']['status']['name'] == 'To Do'){
                                                    $countToDo++;
                                                }

                                            }?>

                                            <div class="progressbar-border">
                                                <?php if ($countDone > 0): ?>
                                                    <div class="progressbar" aria-valuemax="<?php echo $totalItems; ?>" aria-valuenow="<?php echo $countDone?>">
                                                        <div role="presentation">
                                                            <div class="_4t3ii2wt _bfhku8mo" aria-describedby="14074val-tooltip"></div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if ($countInProgress > 0): ?>
                                                    <div class="progressbar" aria-valuemax="<?php echo $totalItems; ?>" aria-valuenow="<?php echo $countInProgress?>">
                                                        <div role="presentation">
                                                            <div class="_4t3ii2wt _bfhk9cbf" aria-describedby="14075val-tooltip"></div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if ($countToDo > 0): ?>
                                                    <div class="progressbar" aria-valuemax="<?php echo $totalItems; ?>" aria-valuenow="<?php echo $countToDo?>">
                                                        <div role="presentation">
                                                            <div class="_4t3ii2wt _bfhkhloo" aria-describedby="14074val-tooltip"></div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <?php
                                            $parentId = $issue['id'];

                                            $response = $client->get('/rest/api/3/search', [
                                                'query' => [
                                                    'jql' => "parent=$parentId",
                                                    'fields' => 'summary,status,parent,description,assignee,duedate,priority'
                                                ],
                                            ]);

                                            $parentData = json_decode($response->getBody(), true);
                                            $childIssues = $parentData['issues'];
                                            $childCount = count($childIssues);
                                            $subtaskCount = 0;

                                            if($childCount > 0):?>
                                                <div class="taskChild">
                                                    <div class="taskChild-list">
                                                        <?php foreach ($childIssues as $childIssue):?>
                                                            <?php
                                                            $status = $childIssue['fields']['status']['name'];
                                                            $statusColors = array(
                                                                'To Do' => '#44546f',
                                                                'In Progress' => '#05c',
                                                                'Done' => '#216e4e'
                                                            );
                                                            $statusBgs = array(
                                                                'To Do' => '#DFE1E6',
                                                                'In Progress' => '#DEEBFF',
                                                                'Done' => '#E3FCEF'
                                                            );
                                                            $statusColor = isset($statusColors[$status]) ? $statusColors[$status] : '#DFE1E6';
                                                            $statusBg = isset($statusBgs[$status]) ? $statusBgs[$status] : '#DFE1E6';

                                                            ?>
                                                            <div class="taskChild-item">
                                                                <p class="key"><img src="https://dev-scvweb.atlassian.net/rest/api/2/universal_avatar/view/type/issuetype/avatar/10318?size=medium" alt=""><?php echo $childIssue["key"]; ?></p>
                                                                <h3 class="title-taskChild"><?php echo $childIssue['fields']['summary']; ?></h3>
                                                                <div class="status-assignee">
                                                                    <p class="icon-priority" title="<?php echo $childIssue['fields']['priority']['name']; ?>"><img src="<?php echo $childIssue['fields']['priority']['iconUrl']; ?>" alt=""></p>
                                                                    <?php if(isset($childIssue['fields']['assignee']) && $childIssue['fields']['assignee'] !== null):?>
                                                                    <p class="assignee"><img title="<?php echo $childIssue['fields']['assignee']["displayName"]; ?>" src="<?php echo $childIssue['fields']['assignee']["avatarUrls"]["48x48"]; ?>" alt=""></p>
                                                                    <?php endif; ?>
                                                                    <span class="status" style="background: <?php echo $statusBg; ?> ;color: <?php echo $statusColor; ?>"><?php echo $childIssue['fields']["status"]["name"]; ?></span>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                        </div>
                                    </div>
                                    <div class="right-taskInfo">
                                        <p class="status" style="background: <?php echo $bg; ?> ;color: <?php echo $color; ?>"><?php echo $issue['fields']['status']['name'];?></p>
                                        <div class="details">
                                            <h4 class="d-title">details</h4>
                                            <div class="details-item">
                                                <label class="title">assignee</label>
                                                <div class="item-info assignee-item">
                                                    <?php if(isset($issue['fields']['assignee']) && $issue['fields']['assignee'] !== null):?>
                                                    <span class="icon"><img src="<?php echo $issue['fields']['assignee']["avatarUrls"]["48x48"]; ?>" alt=""></span>
                                                    <span class="name"><?php echo $issue['fields']['assignee']["displayName"]; ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="details-item">
                                                <label class="title">priority</label>
                                                <div class="priority-item item-info">
                                                    <span class="icon"><img src="<?php echo $issue['fields']['priority']['iconUrl']; ?>" alt=""></span>
                                                    <span class="priority-name"><?php echo $issue['fields']['priority']['name']; ?></span>
                                                </div>
                                            </div>
                                            <div class="due-date details-item">
                                                <label class="title">due date</label>
                                                <?php if (isset($issue['fields']['duedate'])): ?>
                                                    <?php
                                                    $currentDate = date('Y-m-d');
                                                    $dateString = $issue['fields']['duedate'];
                                                    $date = date_create_from_format('Y-m-d', $dateString);
                                                    $formattedDate = date_format($date, 'd M');
                                                    ?>
                                                    <div class="dueDate dueDate-item item-info <?php if ($currentDate >= $dateString) {
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
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        $parentId = $issue['id'];

                        $response = $client->get('/rest/api/3/search', [
                            'query' => [
                                'jql' => "parent=$parentId",
                                'fields' => 'summary,status,parent,description,assignee,duedate'
                            ],
                        ]);

                        $parentData = json_decode($response->getBody(), true);
                        $childIssues = $parentData['issues'];
                        $childCount = count($childIssues);

//                        var_dump($parentData);
//                        die();
                        $subtaskCount = 0;

                        if($childCount > 0):?>
                            <div class="taskChild">
                                <div class="btn-taskChild-list">
                                    <div class="btn-box" title="Show subtasks">
                                        <svg width="24" height="24" viewBox="0 0 24 24" role="presentation"><g fill="currentColor"><path d="M19 7c1.105.003 2 .899 2 2.006v9.988A2.005 2.005 0 0118.994 21H9.006A2.005 2.005 0 017 19h11c.555 0 1-.448 1-1V7zM3 5.006C3 3.898 3.897 3 5.006 3h9.988C16.102 3 17 3.897 17 5.006v9.988A2.005 2.005 0 0114.994 17H5.006A2.005 2.005 0 013 14.994V5.006zM5 5v10h10V5H5z"></path><path d="M7.707 9.293a1 1 0 10-1.414 1.414l2 2a1 1 0 001.414 0l4-4a1 1 0 10-1.414-1.414L9 10.586 7.707 9.293z"></path></g></svg>
                                        <span class="count-task">
                                        <?php foreach ($childIssues as $childIssue) {
                                            if ($childIssue['fields']['status']['name'] == 'Done') {
                                                $subtaskCount++;
                                            }
                                        }
                                        ?>
                                        <?php echo $subtaskCount; ?>/<?php echo $childCount; ?></span>
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </div>
                                </div>
                                <div class="taskChildContent">
                                    <?php
                                    $countToDo = 0;
                                    $countInProgress = 0;
                                    $countDone = 0;
                                    $totalItems = count($childIssues);
                                    ?>
                                    <?php foreach ($childIssues as $childIssue){
                                        if ($childIssue['fields']['status']['name'] == 'Done'){
                                            $countDone++;
                                        } elseif ($childIssue['fields']['status']['name'] == 'In Progress'){
                                            $countInProgress++;
                                        } elseif ($childIssue['fields']['status']['name'] == 'To Do'){
                                            $countToDo++;
                                        }

                                    }?>

                                    <div class="progressbar-border">
                                        <?php if ($countDone > 0): ?>
                                            <div class="progressbar" aria-valuemax="<?php echo $totalItems; ?>" aria-valuenow="<?php echo $countDone?>">
                                                <div role="presentation">
                                                    <div class="_4t3ii2wt _bfhku8mo" aria-describedby="14074val-tooltip"></div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($countInProgress > 0): ?>
                                            <div class="progressbar" aria-valuemax="<?php echo $totalItems; ?>" aria-valuenow="<?php echo $countInProgress?>">
                                                <div role="presentation">
                                                    <div class="_4t3ii2wt _bfhk9cbf" aria-describedby="14075val-tooltip"></div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($countToDo > 0): ?>
                                            <div class="progressbar" aria-valuemax="<?php echo $totalItems; ?>" aria-valuenow="<?php echo $countToDo?>">
                                                <div role="presentation">
                                                    <div class="_4t3ii2wt _bfhkhloo" aria-describedby="14074val-tooltip"></div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="taskChild-list">
                                        <?php foreach ($childIssues as $childIssue):?>
                                            <?php
                                            $status = $childIssue['fields']['status']['name'];
                                            $statusColors = array(
                                                'To Do' => '#44546f',
                                                'In Progress' => '#05c',
                                                'Done' => '#216e4e'
                                            );
                                            $statusBgs = array(
                                                'To Do' => '#DFE1E6',
                                                'In Progress' => '#DEEBFF',
                                                'Done' => '#E3FCEF'
                                            );
                                            $statusColor = isset($statusColors[$status]) ? $statusColors[$status] : '#DFE1E6';
                                            $statusBg = isset($statusBgs[$status]) ? $statusBgs[$status] : '#DFE1E6';

                                            ?>
                                            <div class="taskChild-item">
                                                <h3 class="title-taskChild"><?php echo $childIssue['fields']['summary']; ?></h3>
                                                <div class="key-assignee-taskChild">
                                                    <p class="key"><img src="https://dev-scvweb.atlassian.net/rest/api/2/universal_avatar/view/type/issuetype/avatar/10318?size=medium" alt=""><?php echo $childIssue["key"]; ?></p>
                                                    <div class="status-assignee">
                                                        <span class="status" style="background: <?php echo $statusBg; ?> ;color: <?php echo $statusColor; ?>"><?php echo $childIssue['fields']["status"]["name"]; ?></span>
                                                        <p class="assignee"><img title="<?php echo $childIssue['fields']['assignee']["displayName"]; ?>" src="<?php echo $childIssue['fields']['assignee']["avatarUrls"]["48x48"]; ?>" alt=""></p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <?php if($columnId == 10095 || $columnId == 10096):?>
                <div class="createTask" data-status="<?php echo $columnId; ?>">
                    <div class="btn-create"><i class="fa-solid fa-plus"></i><span>Create</span></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>


    <?php
endforeach;
?>

<script>
    $(".progressbar").each(function () {
        var numberMax = $(this).attr("aria-valuemax"),
            numberNow = $(this).attr("aria-valuenow"),
            width = 100 / numberMax * numberNow;
        $(this).css("width", width + "%");
    })
</script>


