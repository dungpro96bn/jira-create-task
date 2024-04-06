<?php
session_start();
require 'app/Config/apiConfig.php';
require 'vendor/autoload.php';
require 'functions.php';
use GuzzleHttp\Client;

// Check if the cookie exists
checkLogin();

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

    <main class="main">
        <div class="inner">
            <div id="createTask">
                <h2 class="heading-main">Create Tasks On Jira</h2>
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
                                    // setTimeout(() => {
                                    //     $(".swal-overlay").removeClass("swal-overlay--show-modal");
                                    // }, 5000);
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
    </main>

<?php include 'footer.php'; ?>

