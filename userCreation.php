<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/PHPServices/handleDatabase.php'); 
?>
<?php if($_GET): ?>

<?php
    if(empty($_GET['username']) || empty($_GET['password'])){
        echo "Please fill in all information";
    }else{
        $db = getDBConnection();
        $query = $db->prepare("INSERT INTO users(username, password) VALUES(:username, :password)");
        $query->bindValue(":username", $_GET['username']);
        $query->bindValue(":password", password_hash($_GET['password'], PASSWORD_BCRYPT));
        $query->execute();
        echo "OK";
    }
?>

<?php else: ?>
    <form method="GET" action="./userCreation.php">
        Username: <input type="text" name="username"><br>
        Password: <input type="password" name="password"><br>
        <button type="submit">Register</button>
    </form>
<?php endif; ?>