<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;">
        <title>File Management System</title>
        
        <!-- jQuery and Popper library required by Bootstrap -->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script src="https://unpkg.com/popper.js/dist/umd/popper.min.js"></script>

        <!-- Bootstrap Libraries -->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        
        <!-- Local CSS stylesheet -->
        <link rel="stylesheet" href="fms.css">
    </head>

    <body>
        <div class="container-fluid">
            <h4>File Management System</h4>
            <div class="col-md-3 bg-primary text-white loginDiv">
                <span class="loginWord">Login</span><br>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Username: </label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" id="username">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Password: </label>
                    <div class="col-sm-9">
                        <input class="form-control" type="password" id="password">
                    </div>
                </div>
                <button type="submit" class="btn btn-success" onClick="login();">Submit</button>
            </div>
        </div>
    </body>
    <script>
        function login(){
            $.post("./PHPServices/handleLogin.php", 
                   {username: $("#username").val(), password: $("#password").val()},
                    function(data){
                        if(data['status'] == "success"){
                            window.location.href = "drive.php";
                        }else{
                            alert(data['message']);
                        }
                    },
                    "json");
        }
    </script>

</html>