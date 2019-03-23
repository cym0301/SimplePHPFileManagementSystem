<?php
    SESSION_START();

    define('__ROOT__', $_SERVER['DOCUMENT_ROOT']); 
    require_once(__ROOT__.'/PHPServices/handleDatabase.php'); 
    require_once(__ROOT__.'/PHPServices/utilities.php');

    $dirID = $_POST['currentDirectory'];

    $db = getDBConnection();

    // Handle upload to root
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
    
    $uploadDir = "../files/";    
    $actualFileName = $_FILES['file']['name'];
    $pathInfo = pathinfo($actualFileName);
    $actualFileNameExist = false;
    $i = 0;
    do{
        $query = $db->prepare("SELECT * FROM files WHERE ownerID = :ownerID AND filename = :filename");
        $query->bindValue(":ownerID", $_SESSION['userID']);
        $query->bindValue(":filename", $actualFileName);
        $query->execute();
        if($query->rowCount() == 0){
            $actualFileNameExist = false;
        }else{
            $i = $i + 1;
            $actualFileName = $pathInfo['filename']."(".$i.").".$pathInfo['extension'];
            $actualFileNameExist = true;
        }
    }while($actualFileNameExist);

    $fileName = "";
    do{
        $fileName = bin2hex(openssl_random_pseudo_bytes(32));
    }while(file_exists($uploadDir.$fileName));
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

    // Move file to the designated place
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir.$fileName)){
        $query = $db->prepare("INSERT INTO files(id, ownerID, filename, lastModified, isDir, identifier, parent, size) VALUES(:id, :ownerID, :filename, NOW(), 0, :identifier, :parent, :size)");
        $query->bindValue(":id", $fileID);
        $query->bindValue(":ownerID", $_SESSION['userID']);
        $query->bindValue(":filename", $actualFileName);
        $query->bindValue(":identifier", $fileName);
        $query->bindValue(":parent", $dirID);
        $query->bindValue(":size", $_FILES['file']['size']);
        $query->execute();
        
        $fileInfo = array("id"=>$fileID, "filename"=>$actualFileName, "size"=>formatSizeUnits($_FILES['file']['size']));
        echo json_encode(array("status"=>"success", "message"=>"Successfully uploaded", "file"=>$fileInfo));
    }else{
        echo json_encode(array("status"=>"error", "message"=>"Failed to move the document."));
    }
?>