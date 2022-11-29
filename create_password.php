<?php
    require "../secure/autoload.php";
    $Error = "";
    $user_data = check_login($connection);

    $url_address = "";
    $first_name = "";
    $email = "";
    $password = "";
    $confirm_password = "";
    $hash = "";

    if(isset($_SESSION['url_address']))
    {
        $url_address = $_SESSION['url_address'];
    }

    if(isset($_SESSION['first_name']))
    {
        $first_name = $_SESSION['first_name'];
    }

    if(isset($_SESSION['email']))
    {
        $email = $_SESSION['email'];
    }


    if($_SERVER['REQUEST_METHOD']=="POST" && isset($_SESSION['token']) && isset($_POST['token']) && $_SESSION['token'] ==$_POST['token']){

        $password = esc($_POST['password']);
        $confirm_password = esc($_POST['confirm_password']);
        $hash = password_hash($password, PASSWORD_DEFAULT);


        if($password !== $confirm_password) {
            $Error = "Please make sure passwords match.";
        }

            if ($Error ==""){

                //Assign the variables for SQL search
                $arr['password'] = $hash;
                $arr['email'] = $email;

                $query = "UPDATE user_full SET password=:password WHERE email=:email";
                $stm = $connection->prepare($query);
                $stm -> execute($arr);

                $_SESSION['message'] = "Thank you for validating your account. Please log in to continue.";

                header("Location: login.php");
                die;
    
            }
        //$Error = "Wrong code. Please try again";
    }

    $_SESSION['token'] = get_random_string(61);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre-Approved for Startup Funding</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/x-icon" href="../images/favicon.svg">
</head>
<body>

    <section id="loader_page">

    <center>
            
            <div>Hello <?=$_SESSION['first_name']?></div>
            <div>Your email is <?=$_SESSION['email']?></div>
                <br/>

    <form method="POST">

    <div><?php
            if(isset($Error) && $Error !=""){
                echo $Error;
            }
        ?></div>

        <div id='title'><h6>Please Create a New Password: </h6>
        <p>Password must be at least 8 characters long with minimum one capital letter, one lowercase character, and one special character.</p>

        <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
        </div>

        <input id='textbox' type='password' placeholder="New Password" name='password' 
        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[.,_=+^():|;~#@$!%*?&-])[A-Za-z\d.,_=+^():|;~#@$!%*?&-]{8,}$" required minlength="8" maxlength="25" oninvalid="this.setCustomValidity('Please Enter Valid Password (8+ characters with minimum 1 uppercase character, 1 lowercase character, 1 number, 1 special character [~#@$!%^*?&_=+.,:;|()-]')" oninput="setCustomValidity('')"><br>
        <input id='textbox' type='password' placeholder="Confirm Password" name='confirm_password' required
        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[.,_=+^():|;~#@$!%*?&-])[A-Za-z\d.,_=+^():|;~#@$!%*?&-]{8,}$" required minlength="8" maxlength="25" oninvalid="this.setCustomValidity('Please Enter Valid Password')" oninput="setCustomValidity('')"><br>

        <button type='submit' name='send'>Create Password</button>

    </form>

    </section>


<!--
    <script>
        var loader = document.getElementById("loader_page");

        window.addEventListener("load", function(){
            loader.style.display= "none";

        })
    </script>
-->


</body>
</html>