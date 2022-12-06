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
    $message="";

    if(isset($_SESSION['message']))
    {
        $message = $_SESSION['message'];
    }

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
            $Error = "<font color='red';>Please make sure passwords match.</font>";
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
    }

    $_SESSION['token'] = get_random_string(61);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Password - Fundafai</title>
    <script src="https://kit.fontawesome.com/80acfed07d.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/x-icon" href="../images/favicon.svg">
    <style>
        .button2{
            position: relative;
        }

        .fa {
            margin-left: 0px;
            margin-right: 8px;
        }

        .button__text{
            color:white;
            transition: all .2s;
        }

        .button_new_text{
            display: none; 
        }

        .button--loading .button__text{
            display: none;
        }

        .button--loading .button_new_text{
            display:contents;
            opacity: 100;
            color:white;
            transition: all .2s;
        }

        .button--loading::after{
            content: "";
            position: absolute;
            width: 16px;
            height: 16px;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }
    </style>

</head>
<body>

<section id="subform_page">

    <div class="flexbox-container">

        <div class="flexbox-item left">

            <div class = "subflex-container">

                <div class="logo_subflex-item"> 
                <a href='../index.html'><h2>Fund<font color='#FDC93B';>a</font>fai</h2></a>
                </div>

                <div class="message_subflex-item">

                    <?php
                        if(isset($message) && $message !=""){
                            echo $message;
                        }

                        if(!isset($message) || $message ==""){
                            $message = "<h1 style='color:#FDC93B';>Thank you for verifying your email. </h1><h2 style='color:white';>"."Please create a new password. </h2>";
                            echo $message;
                        }
                    ?>
                </div>
            </div>

        </div>
        <br/>

        <div class='flexbox-item right'>
            <center>    

            <form method="POST">



                <h3 style="font-weight:600; color: rgb(21, 21, 100)">Please Create a New Password: </h3>
                <p style="font-size:14px;">(Password must be at least 8 characters long with minimum one capital letter, one lowercase letter, one number, and one special character)</p>

                <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
                <div>
                <?php
                    if(isset($Error) && $Error !=""){
                        echo $Error;
                    }
                ?>
                </div>

                <input id='textbox' type='password' class='input_box_email' placeholder="New Password" name='password' 
                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[.,_=+^():|;~#@$!%*?&-])[A-Za-z\d.,_=+^():|;~#@$!%*?&-]{8,}$" required minlength="8" maxlength="25" oninput="setCustomValidity('')"><br>
                <input id='textbox' type='password' class='input_box_email' placeholder="Confirm Password" name='confirm_password' required
                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[.,_=+^():|;~#@$!%*?&-])[A-Za-z\d.,_=+^():|;~#@$!%*?&-]{8,}$" required minlength="8" maxlength="25" oninput="setCustomValidity('')"><br>

                <!--Original submit
                <button type='submit' name='send'>Create Password</button> -->

                <button type='submit' name='send' id='submit_button' class="button2">
                        <span style = "font-size: 1rem; color: white;" class="button__text">Create Password</span>
                        <span class="button_new_text"><i class="fa fa-circle-o-notch fa-spin"></i>Updating</span>
                </button>

                <script>
                            let buttonclick = document.getElementById('submit_button');
                            buttonclick.onclick = function(){
                                buttonclick.classList.toggle('button--loading');
                            }

                            let password = document.getElementById('textbox');
                            password.oninvalid = function(){
                                password.setCustomValidity('Please Enter Valid Password (8+ characters with minimum 1 uppercase character, 1 lowercase character, 1 number, and 1 special character [~#@$!%^*?&_=+.,:;|()-]');
                                buttonclick.classList.remove('button--loading');
                            }
                            </script>

            </form>

        </div>
    </div>

        <footer>
        
            <div class="copyright">
                <p>© 2021 Fundafai. All Rights Reserved.</p>
                <div class="pro-links">
                    <i class="fab fa-facebook"></i>
                    <i class="fab fa-instagram"></i>
                    <i class="fab fa-linkedin"></i>
                </div>
            </div>

        </footer>

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