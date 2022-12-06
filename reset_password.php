<?php
    require "../secure/autoload.php";
    $Error = "";

    $url_address = "";
    $email = "";

    //Add this later: If there are more than 3 code resets, lock out account and tell to call in

    if($_SERVER['REQUEST_METHOD']=="POST" && isset($_SESSION['token']) && isset($_POST['token']) && $_SESSION['token'] ==$_POST['token']){

        $email = esc($_POST['email']);
        $code = random_int(100000, 999999);

        if ($Error ==""){

            $arr = false;
            $arr['email'] = $email;
            $query = "SELECT password_resets FROM user_full WHERE email=:email LIMIT 1";
            $stm = $connection->prepare($query);
            $check = $stm -> execute($arr);
    
            if($check){
                //Can FETCH_OBJ or FETCH_ASSOC for array
                $data = $stm-> fetchall(PDO::FETCH_OBJ);
                if(is_array($data) && count($data) > 0){
                    $data = $data[0];
                    $password_resets = $data-> password_resets;
                    if($password_resets == ""){
                        $password_resets = 0;
                    } elseif ($password_resets >3) {
                        $Error = "<font color='red';>You have exceeded the number of allowable password resets. Please call (888)888-8888 or email info@fundafai.com to resolve issue. </font>";
                        $_SESSION['error']= $Error;
                        header("Location: reset_password.php");
                        die;
                    }
                    else{
                        $password_resets += 1;
                    }
                }
                }

            //Assign the variables for SQL search
            $arr = false;
            $arr['email'] = $email;
            $arr['code'] = $code;
            $arr['password_resets'] = $password_resets;

            $query = "UPDATE user_full SET code=:code, password_resets=:password_resets WHERE email=:email LIMIT 1";
            $stm = $connection->prepare($query);
            $stm -> execute($arr);

            //Send verification code to user
            $to = $email;
            $subj = '[Fundafai] Password Verification Code';
            $msg = '<h1>Your password verification code is: ' . $code .'</h1><br><h2>Please enter this code on the verfication page.</h2>' . '<br><p>If you did not request for this code, please disregard this email or respond with STOP in subject line.';
            sendEmail($to, $email, $subj, $msg);

            $arr = false;
            $arr['email'] = $email;
            $query = "SELECT * FROM user_full WHERE email=:email LIMIT 1";
            $stm = $connection->prepare($query);
            $check = $stm -> execute($arr);
    
            //If email exists, will return error
            if($check){
                //Can FETCH_OBJ or FETCH_ASSOC for array
                $data = $stm-> fetchall(PDO::FETCH_OBJ);
                if(is_array($data) && count($data) > 0){
                    $data = $data[0];
                    $_SESSION['url_address'] = $data-> url_address;
                    $_SESSION['code']= $data->code;
                    $_SESSION['email'] = $data-> email;
                    $_SESSION['login_attempts'] =0;

                    if(isset($_SESSION['password_attempts'])){
                        $_SESSION['password_attempts'] = 0;
                    }

                    $_SESSION['message'] = "<h1 style='color:#FDC93B';>Password Has Been Reset</h1><h2 style='color:white';>"."Please enter the verification code sent to your email. </h2>";
                    header("Location: verify_code.php");
                    die;
                }
                }

        }
        $Error = "<font color='red';>Email does not exist. Please try again or <a href='startup_qualify.php'>register for new account</a> </font>";
    }

    $_SESSION['token'] = get_random_string(61);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Fundafai</title>
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
                            $message = "<h1 style='color:#FDC93B';>Reset Password</h1><h2 style='color:white';>"."Please enter email to receive new verfication code. </h2>";
                            echo $message;
                        }
                    ?>
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

                <h3 style="font-weight:600; color: rgb(21, 21, 100)">Please Enter Email Connected to Account:</h3>

                <input type="hidden" name="token" value="<?=$_SESSION['token']?>">

                <input id='textbox' type='email' placeholder='Email' class='input_box_email' name='email' required
                pattern="[a-zA-Z0-9.@-]+" oninput="setCustomValidity('')"><br>

                <!--Original submit
                <button type='submit' name='send'>Reset Password</button>-->

                <button type='submit' name='send' id='submit_button' class="button2">
                        <span style = "font-size: 1rem; color: white;" class="button__text">Reset Password</span>
                        <span class="button_new_text"><i class="fa fa-circle-o-notch fa-spin"></i>Updating</span>
                </button>

                <script>
                            let buttonclick = document.getElementById('submit_button');
                            buttonclick.onclick = function(){
                                buttonclick.classList.toggle('button--loading');
                            }

                            let password = document.getElementById('textbox');
                            password.oninvalid = function(){
                                password.setCustomValidity('Please enter valid email');
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