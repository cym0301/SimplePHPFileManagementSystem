<?php
    SESSION_START();

    unset($_SESSION['userID']);
    unset($_SESSION['isLoggedIn']);
    
    echo json_encode(array("status"=>"success", "message"=>"You are logged out"));
?>