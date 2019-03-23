<?php
    SESSION_START();

    define('__ROOT__', $_SERVER['DOCUMENT_ROOT']); 
    require_once(__ROOT__.'/PHPServices/handleDatabase.php'); 
    require_once(__ROOT__.'/PHPServices/utilities.php');

    $dirID = $_POST['currentDirectory'];

    $db = getDBConnection();

    // Handle create to root
    if($dirID == ""){
        $query = $db->prepare("SELECT * FROM files WHERE ownerID = :ownerID AND parent = \"root\"");
        $query->bindValue(":ownerID", $_SESSION['userID']);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $dirID = $result['id'];
    }

    // Check if user has access to write to this directory
    $query = $db->prepare("SELECT * FROM files WHERE id = :dirID AND ownerID = :ownerID");
    $query->bindValue(":dirID", $dirID);
    $query->bindValue(":ownerID", $_SESSION['userID']);
    $query->execute();
    if($query->rowCount() == 0){
        echo json_encode(array("status"=>"error", "message"=>"You have no access writing to this directory"));
        return;
    }
    
    // Check if folder exists / file of the same name exist
    $query = $db->prepare("SELECT * FROM files WHERE filename = :filename AND parent = :parent AND ownerID = :ownerID");
    $query->bindValue(":filename", $_POST['folderName']);
    $query->bindValue(":parent", $dirID);
    $query->bindValue(":ownerID", $_SESSION['userID']);
    $query->execute();
    if($query->rowCount() != 0){
        echo json_encode(array("status"=>"error", "message"=>"There exists a folder or a file with the same name"));
        return;
    }

    $fileID = "";
    $isFileIDExist = false;
    do{
        $fileID = bin2hex(openssl_random_pseudo_bytes(16));
        $query = $db->prepare("SELECT * FROM files WHERE id = :id");
        $query->bindValue(":id", $fileID);
        $query->execute();
        if($query->rowCount() != 0){
            $isFileIDExist = true;
        }else{
            $isFileIDExist = false;
        }
    }while($isFileIDExist);

    $query = $db->prepare("INSERT INTO files(id, filename, lastModified, isDir, parent, ownerID, size) VALUES(:id, :folderName, NOW(), 1, :parent, :userid, 0)");
    $query->bindValue(":id", $fileID);
    $query->bindValue(":folderName", $_POST['folderName']);
    $query->bindValue(":parent", $dirID);
    $query->bindValue(":userid", $_SESSION['userID']);
    $query->execute();
    $file = array("id"=>$fileID, "size"=>formatSizeUnits(0), "filename"=>$_POST['folderName']);
    echo json_encode(array("status"=>"success", "message"=>"Successfully created a folder", "file"=>$file));
?>