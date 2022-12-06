<?php
    require "../secure/autoload.php";
    $Error = "";
    $message = "";
    $email = "";

    if(!isset($_SESSION['password_attempts']))
    {
        $_SESSION['password_attempts'] = 0;
    }

    if(isset($_SESSION['message']))
    {
        $message = $_SESSION['message'];
    }

    if(isset($_SESSION['url_address']))
    {
        unset($_SESSION['url_address']);
    }

    if(isset($_SESSION['first_name']))
    {
        unset($_SESSION['first_name']);
    }

    if(isset($_SESSION['email']))
    {
        unset($_SESSION['email']);
    }

    if(isset($_SESSION['password_attempts']) && $_SESSION['password_attempts'] > 5){
        $Error = "<font color='red';>You have exceeded the number of allowable login attempts.</font> ";
        $_SESSION['error']= "<font color='red';>Please try again later or reset password.</font>";
    }

    if($_SERVER['REQUEST_METHOD']=="POST" && isset($_SESSION['token']) && isset($_POST['token']) && $_SESSION['token'] ==$_POST['token']){
        
        $email = esc($_POST['email']);
        $password = esc($_POST['password']);

        if ($Error ==""){

            $arr['email'] =$email;
            //$arr['password'] =$password;

            $query = "SELECT * FROM user_full WHERE email=:email LIMIT 1";
            $stm = $connection->prepare($query);
            $check = $stm -> execute($arr);

            //Checks to make sure there is a valid query for the email/password then sets session parameters
            if($check){
                //Can FETCH_OBJ or FETCH_ASSOC for array
                $data = $stm-> fetchall(PDO::FETCH_OBJ);
                if(is_array($data) && count($data) > 0){

                    $data = $data[0];
                    $existingHashFromDb = $data-> password;
                    $isPasswordCorrect = password_verify($password, $existingHashFromDb);

                    if($isPasswordCorrect){
                        $_SESSION['first_name']= $data->first_name;
                        $_SESSION['url_address'] = $data-> url_address;
                        $_SESSION['email'] = $data-> email;
                        unset($_SESSION['password_attempts']);
                        unset($_SESSION['error']);
                        unset($_SESSION['message']);
                        header("Location: client_portal.php");
                        die;
                    }
                    $_SESSION['password_attempts'] +=1;
                    $Error = "<font color='red';>Wrong Email/Password. Please try again.</font>";
                    $message = "";
                }
                } 
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
    <title>Client Login - Fundafai</title>
    <script src="https://kit.fontawesome.com/80acfed07d.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/x-icon" href="../images/favicon.svg">
    <style>

        .right h3{
            margin-top: 10px;
        }

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

                    <h1 style='color:#FDC93B';>Client Portal</h1><h2 style='color:white';>Please login to continue.</h2>

                    </div>
                </div>
            </div>


            <div class='flexbox-item right'>
                <center>    

                <form method="POST">
                    <div><?php
                        if(isset($Error) && $Error !=""){
                            echo $Error;
                        }

                        if(isset($_SESSION['error']) && $_SESSION['error'] != "" ){
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                        }
                        ?>
                    </div>

                    <h3 style="font-weight:600; color: rgb(21, 21, 100)">Please Enter Login Email/Password: </h3>

                    <div><?php

                        if(isset($_SESSION['password_attempts']) && $_SESSION['password_attempts']  == 5) {
                                echo "<font color='red';>You have 1 login attempt left before being locked out.</font><br>";
                            }
                        ?>
                    </div>

                        <input id='email' type='email' placeholder='Email' value='<?=$email?>' class='input_box_email' name='email' required
                        pattern="[a-zA-Z0-9.@-]+" oninput="setCustomValidity('')"><br>

                        <input id='password' type='password' placeholder="Password" class='input_box_email' name='password' 
                        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[.,_=+^():|;~#@$!%*?&-])[A-Za-z\d.,_=+^():|;~#@$!%*?&-]{8,}$" 
                        required minlength="8" maxlength="25" oninput="setCustomValidity('')"><br>

                        <input type="hidden" name="token" value="<?=$_SESSION['token']?>">


                        <button type='submit' name='send' id='submit_button' class="button2">
                        <span style = "font-size: 1rem; color: white;" class="button__text">Login</span>
                        <span class="button_new_text"><i class="fa fa-circle-o-notch fa-spin"></i>Verifying</span>
                         </button>

                    <div> <a href='reset_password.php'><p style="font-size:14px;">Forgot Password</p></a> </div>
                    <div> <a href='startup_qualify.php'><p style="font-size:14px;">Create New Account</p></a> </div>


                <script>
                            let buttonclick = document.getElementById('submit_button');
                            buttonclick.onclick = function(){
                                buttonclick.classList.toggle('button--loading');
                            }

                            let email = document.getElementById('email');
                            email.oninvalid = function(){
                                email.setCustomValidity('Please enter valid email');
                                buttonclick.classList.remove('button--loading');
                            }

                            let password = document.getElementById('password');
                            password.oninvalid = function(){
                                password.setCustomValidity('Please enter valid password. Passwords contain at least one capital letter, one lowercase letter, one number, and one special character');
                                buttonclick.classList.remove('button--loading');
                            }
                            </script>

                </form>
                </center>
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

</body>
</html>