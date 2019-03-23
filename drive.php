<!DOCTYPE html>
<?php 
    SESSION_START();
?>
<?php if ($_SESSION['isLoggedIn']): ?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=0.8, maximum-scale=1.0, user-scalable=0">
        <title>File Management System</title>
        
        <!-- jQuery and Popper library required by Bootstrap -->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script src="https://unpkg.com/popper.js/dist/umd/popper.min.js"></script>

        <!-- Bootstrap Libraries -->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        
        <!-- Font Awesome -->
        <script defer src="https://use.fontawesome.com/releases/v5.8.1/js/all.js" integrity="sha384-g5uSoOSBd7KkhAMlnQILrecXvzst9TdC09/VM+pjDTCM+1il8RHz5fKANTFFb+gQ" crossorigin="anonymous"></script>
        
        <!-- dropzone.js library -->
        <script src="./libraries/dropzone/dropzone.min.js"></script>
        <link rel="stylesheet" href="./libraries/dropzone/dropzone.min.css">
        <link rel="stylesheet" href="./libraries/dropzone/basic.min.css">
        
        <!-- Local CSS stylesheet -->
        <link rel="stylesheet" href="fms.css">
        
        
    </head>

    <body>
        <div class="container-fluid">
            <h4>File Management System</h4>
            <span class="signOutDiv" onClick="signOut();"><i class="fas fa-sign-out-alt"></i>Sign out</span>
            <input type="hidden" value="456" id="currentDirectory">
            <form class="dropzone" id="fileUploadZone" enctype="multipart/form-data">
                <div class="dz-message">Drop files here or click to select files to upload to current directory</div>
            </form>
            <hr/>
            <div id="fileNavigationPanel">
                <span onClick="getFileList()"><i class="fas fa-folder fa-2x"></i><span class="navFolderName"> Home</span></span>
                <span id="navFolder"></span>
            </div>
            
            <div id="buttonControlPanel"><span data-toggle="tooltip" data-placement="bottom" title="Create a folder"><i class="fas fa-folder-plus fa-2x" data-toggle="modal" data-target="#createFolderModal"></i></span></div>
            <table>
                <thead>
                    <th style="width: 65%;">Filename</th>
                    <th style="width: 15%;">Size</th>
                    <th style="width: 20%;">Operation</th>
                </thead>
                <tbody id="fileList">
                </tbody>
            </table>
        </div>
        
        <!-- modal for creating folder -->
        <div class="modal fade" id="createFolderModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create folder</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Folder Name: </label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" id="newFolderName">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onClick="createFolder();">Create</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- modal for renaming file/folder -->
        <div class="modal fade" id="renameFileModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Rename file/folder</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="renameFileID">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Old File Name: </label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" id="renameFileNameOld" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">New File Name: </label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" id="renameFileNameNew">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onClick="renameFile();">Rename</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
    
    <script>
        
        function addFileToList(filename, size, fileID){
            var row = "";
            row += "<tr>";
            row += "<td><span onClick=\"downloadFile('" + fileID + "')\"><i class=\"fas fa-file\"></i> " + filename + "</span></td>";
            row += "<td>" + size + "</td>";
            row += "<td><span onClick=\"deleteFile(this, '" + fileID + "');\"<i class=\"fas fa-file-excel\"></i></span> <span onClick=\"toggleRenameFileModal('" + filename + "','" + fileID + "')\"><i class=\"fas fa-cog\"></i></span></td>";
            row += "</tr>";
            $("#fileList").append(row);
        }
        
        function addFolderToList(filename, size, fileID){
            var row = "";
            row += "<tr>";
            row += "<td><span onClick=\"getFileList('" + fileID + "')\" ><i class=\"fas fa-folder\"></i> " + filename + "</span></td>";
            row += "<td>" + size + "</td>";
            row += "<td><span onClick=\"deleteFile(this, '" + fileID + "');\"<i class=\"fas fa-folder-minus\"></i></span> <i class=\"fas fa-cog\"></i></td>";
            row += "</tr>";
            $("#fileList").append(row);
        }
        
        function deleteFile(elem, fileID){
            $.ajax({
                type: "POST",
                url: "./PHPServices/handleFileDelete.php",
                data: {
                    fileID: fileID
                },
                success: function(data){
                    if(data['status'] == "success"){
                        $(elem).parent().parent().remove()
                    }else{
                        alert(data['message']);
                    }
                },
                dataType: "json"
            });
            console.log(fileID);
        }
        
        function toggleRenameFileModal(filename, fileID){
            $("#renameFileNameOld").val(filename);
            $("#renameFileID").val(fileID);
            $("#renameFileNameNew").val("");
            $("#renameFileModal").modal("show");
        }
        
        function renameFile(){
            if($("#renameFileNameNew").val() == ""){
                alert("Please enter a folder name");
            }
            $.ajax({
                type: "POST",
                url: "./PHPServices/handleFileRename.php",
                data: {
                    fileID: $("#renameFileID").val(),
                    filename: $("#renameFileNameNew").val()
                },
                success: function(data){
                    if(data['status'] == "success"){
                        getFileList($("#currentDirectory").val());
                        $("#renameFileModal").modal('hide');
                    }else{
                        alert(data['message']);
                    }
                },
                dataType: "json"
            });
        }
        
        function downloadFile(fileID){
            var win = window.open("./PHPServices/handleFileDownload.php?fileID="+fileID);
            win.focus();
        }
        
        function signOut(){
            $.get("./PHPServices/handleLogout.php", function(data){
                if(data['status'] == "success"){
                    window.location.href = "index.php";
                }
            }, "json");    
        }
        
        function createFolder(){
            if($("#newFolderName").val() == ""){
                alert("Please enter a folder name");
            }
            $.ajax({
                type: "POST",
                url: "./PHPServices/handleFolderCreate.php",
                data: {
                    currentDirectory: $("#currentDirectory").val(),
                    folderName: $("#newFolderName").val()
                },
                success: function(data){
                    if(data['status'] == "success"){
                        getFileList($("#currentDirectory").val());
                        $("#newFolderName").val("");
                        $("#createFolderModal").modal('hide');
                    }else{
                        alert(data['message']);
                    }
                },
                dataType: "json"
            });
        }
        
        function getFileList(dirID = ""){
            $.get("./PHPServices/handleFileList.php?directory=" + dirID, function(data){
                if(data['status'] == "success"){
                    $("#fileList").html("");
                    for(let file of data['files']){
                        if(file['isDir'] == "1"){
                            addFolderToList(file['filename'], file['size'], file['id']);
                        }else{
                            addFileToList(file['filename'], file['size'], file['id']);
                        }
                    }
                    $("#currentDirectory").val(dirID);
                    Dropzone.options.fileUploadZone.params.currentDirectory = dirID;
                    $("#navFolder").html("");
                    if(data['isRoot'] == "0"){
                        $("#navFolder").html("<i class=\"fas fa-chevron-right\"></i> <span class=\"navFolderName\">... </span><i class=\"fas fa-chevron-right\"></i> <span><i class=\"fas fa-folder fa-2x\"></i><span class=\"navFolderName\"> " + data['folderName'] + "</span></span>");
                    }
                }else{
                    alert(data['message']);
                }
            }, "json");
        }
        
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
            getFileList();
        });
        
        //Set up File Upload Zone
        Dropzone.autoDiscover = false;
        Dropzone.options.fileUploadZone = {
            init: function(){
                this.on("success", function(file){
                    var result = JSON.parse(file.xhr.responseText);
                    if(result['status'] == "success"){
                        console.log(result['file']);
                        addFileToList(result['file']['filename'], result['file']['size'], result['file']['id']);
                    }else{
                        alert(result['message']);
                    }
                });
            },
            url: "./PHPServices/handleFileUpload.php",
            params: {
                currentDirectory: "2e7e3d99f5838baa588b01fcbec2e45d"
            }
        }
        $("#fileUploadZone").dropzone();
        
    </script>
</html>
<?php else: ?>
    <?php header('Location: ./index.php'); ?>
<?php endif; ?>