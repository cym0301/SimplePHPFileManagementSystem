<?php
    SESSION_START();

    define('__ROOT__', $_SERVER['DOCUMENT_ROOT']); 
    require_once(__ROOT__.'/PHPServices/handleDatabase.php'); 
    require_once(__ROOT__.'/PHPServices/utilities.php');

    $db = getDBConnection();
    $dirID = $_GET['directory'];
    if($dirID == ""){
        $query = $db->prepare("SELECT * FROM files WHERE ownerID = :ownerID AND parent = \"root\"");
        $query->bindValue(":ownerID", $_SESSION['userID']);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $dirID = $result['id'];
    }

    // Check if it is a folder
    $query = $db->prepare("SELECT isDir FROM files WHERE id = :id");
    $query->bindValue(":id", $dirID);
    $query->execute();
    $result = $query->fetch();
    if($result[0] == "0"){
        echo json_encode(array("status"=>"error", "message"=>"This is not a folder"));
        return;
    }

    // Check if user has access to write to this directory
    $query = $db->prepare("SELECT id, filename, isDir, size, parent FROM files WHERE id = :dirID AND ownerID = :ownerID");
    $query->bindValue(":dirID", $dirID);
    $query->bindValue(":ownerID", $_SESSION['userID']);
    $query->execute();
    if($query->rowCount() == 0){
        echo json_encode(array("status"=>"error", "message"=>"You have no access to this directory"));
        return;
    }
    
    $isRoot = "0";
    $result = $query->fetch();
    if($result['parent'] == "root"){
        $isRoot = "1";
    }
    $folderName = $result['filename'];

    // Get list of directories and files under this directory
    $fileList = array();
    $query = $db->prepare("SELECT id, filename, isDir, size FROM files WHERE parent = :dirID AND ownerID = :ownerID ORDER BY isDir DESC, filename ASC");
    $query->bindValue(":dirID", $dirID);
    $query->bindValue(":ownerID", $_SESSION['userID']);
    $query->execute();
    foreach($query->fetchAll() as $file){
        $fileList[] = array("id"=>$file[0], "filename"=>$file[1], "isDir"=>$file[2], "size"=>formatSizeUnits($file[3]));
    }

    // Construct return array
    $ret = array("status"=>"success", "message"=>"List of files retrieved", "files"=>$fileList, "isRoot"=>$isRoot, "folderName"=>$folderName);
    echo json_encode($ret);
?>