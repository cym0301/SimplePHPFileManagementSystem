<?php 
    SESSION_START();

    define('__ROOT__', $_SERVER['DOCUMENT_ROOT']); 
    require_once(__ROOT__.'/PHPServices/handleDatabase.php'); 
    require_once(__ROOT__.'/PHPServices/utilities.php');

    $db = getDBConnection();
    $query = $db->prepare("SELECT * FROM files WHERE isDir = 0 AND ownerID = :ownerID AND id = :fileID");
    $query->bindValue(":ownerID", $_SESSION['userID']);
    $query->bindValue(":fileID", $_GET['fileID']);
    $query->execute();
    if($query->rowCount() == 0){
        echo "No such a file.";
    }else{
        $result = $query->fetch();
        $fileIdentifier = $result['identifier'];
        $fileName = $result['filename'];
        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate");
        header("Pragma: public");
        header("Content-Length: ".filesize("../files/$fileIdentifier"));
        readfile("../files/$fileIdentifier");
        exit();
    }
?>