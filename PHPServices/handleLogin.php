<?php
    SESSION_START();
    
    define('__ROOT__', $_SERVER['DOCUMENT_ROOT']); 
    require_once(__ROOT__.'/PHPServices/handleDatabase.php'); 
    
    if(empty($_POST['username']) || empty($_POST['password'])){
        echo json_encode(array("status"=>"error", "message"=>"Please fill in all information"));
        return;
    }

    $db = getDBConnection();

    // Check if user exist
    $query = $db->prepare("SELECT * FROM users WHERE username = :username");
    $query->bindValue(":username", $_POST['username']);
    $query->execute();
    if ($query->rowCount() == 0){
        echo json_encode(array("status"=>"error", "message"=>"User does not exist"));
        return;
    }
    // Check user password
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $userID = $result['id'];
    if( password_verify($_POST['password'], $result['password']) ){
        
        // Check if root directory created in database
        $query = $db->prepare("SELECT * FROM files WHERE ownerID = :id AND parent = \"root\"");
        $query->bindValue(":id", $userID);
        $query->execute();
        
        if($query->rowCount() == 0){
            // Create root node if not exist.
            $isFileIDExist = false;
            do{
                $fileID = bin2hex(openssl_random_pseudo_bytes(16));
                $query = $db->prepare("SELECT * FROM files WHERE id = :id");
                $query->bindValue(":id", $fileID);
                $query->execute();
                if($query->rowCount() == 0){
                    $query = $db->prepare("INSERT INTO files(id, filename, lastModified, isDir, parent, ownerID, size) VALUES(:id, \"Home\", NOW(), 1, \"root\", :userid, 0)");
                    $query->bindValue(":id", $fileID);
                    $query->bindValue(":userid", $userID);
                    $query->execute();
                    $isFileIDExist = false;
                }else{
                    $isFileIDExist = true;
                }
            }while($isFileIDExist);
        }
        
        // Write to Session variable
        $_SESSION['isLoggedIn'] = true;
        $_SESSION['userID'] = $userID;
        echo json_encode(array("status"=>"success", "message"=>"Login successful"));
        
    }else{
        echo json_encode(array("status"=>"error", "message"=>"Unauthorized Access"));
    }
?>