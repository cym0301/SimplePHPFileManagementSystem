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
        echo json_encode(array("status"=>"error", "message"=>"You have no permission to delete it"));
        return;
    }
    
    $result = $query->fetch();
    if($result['isDir'] == 0){
        // Handle the case where it is not a directory
        
        // Remove physical file from server
        unlink("../files/".$result['identifier']);
        
        // Remove the node from database
        $query = $db->prepare("DELETE FROM files WHERE id = :fileID");
        $query->bindValue(":fileID", $_POST['fileID']);
        $query->execute();  
    }else{
        // Perform recursive deletion
        $dirIDs = array();
        $dirIDs[] = $_POST['fileID'];
        $i = 0;
        $fileIDs = array();
        while($i < count($dirIDs)){
            $dirID = $dirIDs[$i];
            // Delete Parent
            $query = $db->prepare("DELETE FROM files WHERE id = :dirID");
            $query->bindValue(":dirID", $dirID);
            $query->execute();
            
            // Get all children
            $query = $db->prepare("SELECT * FROM files WHERE parent = :dirID");
            $query->bindValue(":dirID", $dirID);
            $query->execute();
            foreach($query->fetchAll() as $file){
                if($file['isDir'] == 0){
                    $fileIDs[$file['id']] = $file['identifier'];
                }else{
                    $dirIDs[] = $file['id'];
                }
            }
            $i++;
        }
        
        // Delete all children
        foreach($fileIDs as $id=>$identifier){
            unlink("../files/".$identifier);
            $query = $db->prepare("DELETE FROM files WHERE id = :fileID");
            $query->bindValue(":fileID", $id);
            $query->execute();
        }
    }

    echo json_encode(array("status"=>"success", "message"=>"File has been deleted"));
        
?>