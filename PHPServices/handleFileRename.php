<?php
    SESSION_START();

    define('__ROOT__', $_SERVER['DOCUMENT_ROOT']); 
    require_once(__ROOT__.'/PHPServices/handleDatabase.php'); 
    require_once(__ROOT__.'/PHPServices/utilities.php');

    $db = getDBConnection();
    $query = $db->prepare("SELECT * FROM files WHERE ownerID = :ownerID AND id = :fileID");
    $query->bindValue(":ownerID", $_SESSION['userID']);
    $query->bindValue(":fileID", $_POST['fileID']);
    $query->execute();
    
    if($query->rowCount() == 0){
        echo json_encode(array("status"=>"error", "message"=>"You have no permission to rename it"));
        return;
    }

    $query = $db->prepare("UPDATE files SET filename = :filename WHERE id = :fileID");
    $query->bindValue(":filename", $_POST['filename']);
    $query->bindValue(":fileID", $_POST['fileID']);
    $query->execute();

    echo json_encode(array("status"=>"success", "message"=>"Folder/File renamed"));
?>