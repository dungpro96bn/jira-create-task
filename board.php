<?php
session_start();
//require 'app/Config/apiConfig.php';
//require 'vendor/autoload.php';
require 'functions.php';
//use GuzzleHttp\Client;

//$client = new Client([
//    'base_uri' => $baseUrl,
//    'headers' => [
//        'Authorization' => 'Basic ' . base64_encode("$email:$apiToken"),
//        'Content-Type' => 'application/json',
//    ],
//]);
//
//$todo_column_id = 10095;
//
//$response = $client->get('/rest/api/3/search', [
//    'query' => [
//        'jql' => "status=$todo_column_id AND parent is EMPTY",
//        'fields' => 'summary,status,parent,description,assignee,duedate'
//    ],
//]);
//
//$data = json_decode($response->getBody(), true);

//var_dump($data);
//die();

?>

<?php include 'header.php'; ?>

<main id="board">
    <div class="board-inner">
        <div class="boardContent">
            <div class="checkLoad"></div>
            <div id="board-list" class="board-list">

            </div>
        </div>
    </div>
</main>

<script>
    function initSortable() {
        var todo = document.getElementById('todo');
        var inProgress = document.getElementById('in-progress');
        var done = document.getElementById('done');

        new Sortable(todo, {
            group: 'task-item',
            animation: 150,
            onEnd: function(evt) {
                var taskId = evt.item.getAttribute('data-task-id'); // Lấy ID của task
                var newStatus = 10095; // Trạng thái mới của task

                // Gửi yêu cầu cập nhật trạng thái của task
                $.ajax({
                    url: 'https://dev-scvweb.com/rest/api/3/issue/' + taskId,
                    type: 'PUT',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        "transition": {
                            "id": "YOUR_TRANSITION_ID" // ID của transition để chuyển trạng thái (cần tìm hiểu từ Jira của bạn)
                        },
                        "fields": {
                            "status": {
                                "id": newStatus
                            }
                        }
                    }),
                    success: function(response) {
                        console.log('Task status updated successfully');
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to update task status:', error);
                    }
                });
            }
        });

        new Sortable(inProgress, {
            group: 'task-item',
            animation: 150,
            onEnd: function(evt) {
                console.log('Moved task from In Progress column to position ' + evt.newIndex);
            }
        });

        new Sortable(done, {
            group: 'task-item',
            animation: 150,
            onEnd: function(evt) {
                console.log('Moved task from Done column to position ' + evt.newIndex);
            }
        });

        var boardLists = document.querySelectorAll('.boardContent-list');

        boardLists.forEach(function(boardList) {
            new Sortable(boardList, {
                group: 'task-item',
                animation: 150,
                onChoose: function(evt) {
                    evt.oldIndex = evt.from.children.indexOf(evt.item);
                },
                onEnd: function(evt) {
                    var item = evt.item;
                    var newIndex = evt.newIndex;
                    var columnIndex = Array.from(item.parentNode.children).indexOf(item);

                    console.log('Moved task from column ' + columnIndex + ' to position ' + newIndex);
                }
            });
        });
    }

    var previousData = null;
    var dataChanged = false;
    function loadContent() {
        $.ajax({
            url: 'board/board-list.php',
            type: 'GET',
            success: function(data) {
                // $('#board-list').html(data);
                if (JSON.stringify(data) !== JSON.stringify(previousData)) {
                    previousData = data;
                    $('#board-list').html(data);
                    $(".checkLoad").remove();
                    initSortable();
                    dataChanged = true;
                }
            }
        });
    }
    $(document).ready(function() {
        loadContent();
        setInterval(function() {
            if (dataChanged = true) {
                loadContent();
            }
        }, 1000);
    });
    // $(document).ready(function() {
    //     loadContent();
    //     setInterval(loadContent, 1000);
    // });
</script>

<?php include 'footer.php'; ?>
