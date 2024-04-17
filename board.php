<?php
session_start();
require 'app/Config/apiConfig.php';
require 'vendor/autoload.php';
require 'functions.php';
use GuzzleHttp\Client;

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

$client = new Client([
    'base_uri' => $baseUrl,
    'headers' => [
        'Authorization' => 'Basic ' . base64_encode("$email:$apiToken"),
        'Content-Type' => 'application/json',
    ],
]);

//get user assign
$responseUser = $client->get('/rest/api/3/user/assignable/search', [
    'query' => [
        'project' => $projectKey,
    ],
]);

$users = json_decode($responseUser->getBody(), true);

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

<div id="createTaskBox">
    <div class="mask"></div>
    <div id="createTask">
        <span class="close-createTask"><i class="fa-solid fa-xmark"></i></span>
        <div class="form-createTask">
            <form id="formCreateTask">
                <div class="createTask-list">
                    <div class="field-group">
                        <label class="title">Title<span>*</span></label>
                        <div class="field-input">
                            <input type="text" name="summary" id="summary" required>
                        </div>
                    </div>
                    <div class="field-group">
                        <label class="title">Description</label>
                        <div class="field-input">
                            <div class="input-rich"></div>
                            <textarea class="input-raw hidden"></textarea>
                            <textarea class="hidden" name="description" id="description"></textarea>
                        </div>
                    </div>
                    <div class="field-group">
                        <label class="title">Assignee</label>
                        <div class="field-input">
                            <div class="user-active">
                                <div class="user-item-active" data-id="">
                                    <div class="avt">
                                                <span class="icon">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" role="presentation"><g fill="#fff" fill-rule="evenodd"><path d="M6 14c0-1.105.902-2 2.009-2h7.982c1.11 0 2.009.894 2.009 2.006v4.44c0 3.405-12 3.405-12 0V14z"></path><circle cx="12" cy="7" r="4"></circle></g></svg>
                                                </span>
                                    </div>
                                    <span>Unassigned</span>
                                </div>
                            </div>
                            <div class="user-list">
                                <div class="user-item item01" data-id="">
                                    <div class="avt">
                                                <span class="icon">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" role="presentation"><g fill="#fff" fill-rule="evenodd"><path d="M6 14c0-1.105.902-2 2.009-2h7.982c1.11 0 2.009.894 2.009 2.006v4.44c0 3.405-12 3.405-12 0V14z"></path><circle cx="12" cy="7" r="4"></circle></g></svg>
                                                </span>
                                    </div>
                                    <span>Unassigned</span>
                                </div>
                                <?php
                                foreach ($users as $user) {
                                    echo  '<div class="user-item" data-id="'.$user['accountId'].'"><img src="'.$user['avatarUrls']["48x48"].'"><span>'.$user['displayName'] . '</span></div>';
                                }
                                ?>
                            </div>
                            <input type="hidden" id="assignee" name="assignee" value="">
                        </div>
                    </div>
                    <div class="field-group">
                        <label class="title">Due date</label>
                        <div class="field-input">
                            <input type="date" id="duedate" name="duedate" value="" placeholder="YYYY-mm-dd">
                        </div>
                    </div>
                </div>
                <input type="text" id="status" name="status" value="">
                <div class="submit-form">
                    <div class="btn-inner">
                        <input type="submit" id="submitBtn" name="create_Task" value="Create Task">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" width="210" height="210" style="shape-rendering: auto; display: block; background: rgba(255, 255, 255, 0);"><g><circle cx="50" cy="50" fill="none" stroke="#ffffff" stroke-width="10" r="40" stroke-dasharray="188.49555921538757 64.83185307179586">
                                    <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"/>
                                </circle><g/></g></svg>
                    </div>
                </div>
            </form>
        </div>

        <div class="swal-overlay" tabindex="-1">
            <div class="swal-mask"></div>
            <div class="swal-modal" role="dialog" aria-modal="true">
                <div class="boxClose"><i class="fa-regular fa-xmark"></i></div>
                <div class="icon-check">
                    <picture class="image">
                        <source srcset="/assets/images/check.png 2x">
                        <img class="sizes" src="/assets/images/check.png" alt="">
                    </picture>
                </div>
                <div id="response" class="note-task"></div>
            </div>
        </div>


        <script>
            $(document).ready(function() {
                $('#formCreateTask').submit(function(event) {
                    event.preventDefault();
                    $(".form-createTask .submit-form svg").css("opacity", "1");

                    var formData = $(this).serialize();
                    $.ajax({
                        type: 'POST',
                        url: 'create-task.php',
                        data: formData,
                        success: function(response) {
                            var avtUser = $(".form-createTask .user-item.item01").html();
                            $(".form-createTask .submit-form svg").css("opacity", "0");
                            $('#response').html(response);
                            $(".swal-overlay").addClass("swal-overlay--show-modal");
                            $('#formCreateTask')[0].reset();
                            $(".form-createTask .user-item-active").html(avtUser);
                        }
                    });
                });
            });

            $(".swal-overlay .swal-mask,.swal-modal .boxClose").click(function () {
                $(".swal-overlay").removeClass("swal-overlay--show-modal");
            });

        </script>

        <script defer type="module">
            import init, {convert} from "https://unpkg.com/htmltoadf@0.1.10/htmltoadf.js";

            let editor;

            const INITIAL_CONTENT = ``;
            const inputRaw = document.querySelector('.input-raw');
            const inputRich = document.querySelector('.input-rich');
            const adfOutput = document.querySelector('#description');
            // const btnToggle = document.querySelector('.btn-toggle');


            const jsonFormatter = {
                replacer: function(match, pIndent, pKey, pVal, pEnd) {
                    var key = '';
                    var val = '';
                    var str = '';
                    var r = pIndent || '';
                    if (pKey)
                        r = r + '"'+key + pKey.replace(/[": ]/g, '') + '": ';
                    if (pVal)
                        r = r + (pVal[0] == '"' ? str : val) + pVal + '';
                    return r + (pEnd || '');
                },
                prettyPrint: function(obj) {
                    var jsonLine = /^( *)("[\w]+": )?("[^"]*"|[\w.+-]*)?([,[{])?$/mg;
                    return JSON.stringify(obj, null, 3)
                        .replace(/&/g, '&amp;').replace(/\\"/g, '&quot;')
                        .replace(/</g, '&lt;').replace(/>/g, '&gt;')
                        .replace(jsonLine, jsonFormatter.replacer);
                }
            };

            tinymce.init({
                selector:'.input-rich',
                plugins: 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount',
                toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | image | help',
                image_title: true,
                height: 300,
                automatic_uploads: true,
                images_upload_url: 'postAcceptor.php',
                file_picker_types: 'image',
                setup: function(ed) {
                    editor = ed;
                    editor.on('keyup', () => {
                        inputRaw.value = editor.getContent()
                        output(editor.getContent())
                    })
                    editor.on('change', () => {
                        inputRaw.value = editor.getContent()
                        output(editor.getContent())
                    })
                    editor.on('init', () => {
                        editor.setContent(INITIAL_CONTENT)
                    })
                },
                file_picker_callback: function(cb, value, meta) {
                    var input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');

                    input.onchange = function() {
                        var file = this.files[0];

                        var reader = new FileReader();
                        reader.onload = function () {
                            var id = 'blobid' + (new Date()).getTime();
                            var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                            var base64 = reader.result.split(',')[1];
                            var blobInfo = blobCache.create(id, file, base64);
                            blobCache.add(blobInfo);

                            cb(blobInfo.blobUri(), { title: file.name });
                        };
                        reader.readAsDataURL(file);
                    };

                    input.click();
                }
            })

            inputRaw.addEventListener('keyup', event => {
                editor.setContent(event.target.value)
                output(event.target.value)
            })

            // btnToggle.addEventListener('click', () => {
            //     if(inputRaw.classList.contains('hidden')){
            //         inputRich.classList.add('hidden')
            //         inputRaw.classList.remove('hidden')
            //         btnToggle.innerText = "Show Rich"
            //     }else{
            //         inputRaw.classList.add('hidden')
            //         inputRich.classList.remove('hidden')
            //         btnToggle.innerText = "Show Raw"
            //     }
            // });

            function output(html){
                adfOutput.innerHTML = jsonFormatter.prettyPrint(JSON.parse(convert(html)));
                console.log(jsonFormatter);
            }

            init().then(() => {
                inputRaw.value = INITIAL_CONTENT
                output(INITIAL_CONTENT)
            })
        </script>

    </div>
</div>

<script>


    function initSortable() {
        var todo = document.getElementById('todo');
        var inProgress = document.getElementById('in-progress');
        var done = document.getElementById('done');

        new Sortable(todo, {
            group: 'task-item',
            animation: 150,
            filter: '.title-column, .createTask',
            onEnd: function(evt) {
                var taskId = evt.item.getAttribute('data-task-id'); // Lấy ID của task
                var newStatus = 10095; // Trạng thái mới của task

                // Gửi yêu cầu cập nhật trạng thái của task
                // $.ajax({
                //     url: 'https://dev-scvweb.com/rest/api/3/issue/' + taskId,
                //     type: 'PUT',
                //     contentType: 'application/json',
                //     data: JSON.stringify({
                //         "transition": {
                //             "id": "YOUR_TRANSITION_ID" // ID của transition để chuyển trạng thái (cần tìm hiểu từ Jira của bạn)
                //         },
                //         "fields": {
                //             "status": {
                //                 "id": newStatus
                //             }
                //         }
                //     }),
                //     success: function(response) {
                //         console.log('Task status updated successfully');
                //     },
                //     error: function(xhr, status, error) {
                //         console.error('Failed to update task status:', error);
                //     }
                // });
            }
        });

        new Sortable(inProgress, {
            group: 'task-item',
            animation: 150,
            filter: '.title-column, .createTask',
            onEnd: function(evt) {
                console.log('Moved task from In Progress column to position ' + evt.newIndex);
            }
        });

        new Sortable(done, {
            group: 'task-item',
            animation: 150,
            filter: '.title-column, .createTask',
            onEnd: function(evt) {
                console.log('Moved task from Done column to position ' + evt.newIndex);
            }
        });

        var boardLists = document.querySelectorAll('.boardContent-list');

        boardLists.forEach(function(boardList) {
            new Sortable(boardList, {
                group: 'task-item',
                animation: 150,
                filter: '.title-column, .createTask',
                // onChoose: function(evt) {
                //     evt.oldIndex = evt.from.children.indexOf(evt.item);
                // },
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
            url: '/board/board-list.php',
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
        }, 100);
    });
    // $(document).ready(function() {
    //     loadContent();
    //     setInterval(loadContent, 1000);
    // });
</script>

<?php include 'footer.php'; ?>
