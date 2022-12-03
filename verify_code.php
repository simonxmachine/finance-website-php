<?php
    require "../secure/autoload.php";
    $Error = "";
    $user_data = check_login($connection);
    $message = "";

    if(isset($_SESSION['message']))
    {
        $message = $_SESSION['message'];
    }

    //Logs user out of session if too many code verfication fails
    if(isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] > 5){
        unset($_SESSION['login_attempts']);
        unset($_SESSION['url_address']);
    }

    $url_address = "";
    $first_name = "";
    $email = "";
    $code = "";

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

    if(isset($_SESSION['code']))
    {
        $code = $_SESSION['code'];
    }

    if($_SERVER['REQUEST_METHOD']=="POST" && isset($_SESSION['token']) && isset($_POST['token']) && $_SESSION['token'] ==$_POST['token']){

        $code_check = esc($_POST['code_check']);

            if ($Error ==""){

                //Assign the variables for SQL search
                $arr['code_check'] = $code_check;
                $arr['email'] = $email;

                $query = "SELECT * FROM user_full WHERE code=:code_check AND email=:email LIMIT 1";
                $stm = $connection->prepare($query);
                $check = $stm -> execute($arr);

                //mysqli_query($connection, $query);
                if($check){
                    //Can FETCH_OBJ or FETCH_ASSOC for array
                    $data = $stm-> fetchall(PDO::FETCH_OBJ);
                    if(is_array($data) && count($data) > 0){

                        $data = $data[0];

                        $code_resets = $data-> code_resets;
                        $password_resets = $data-> password_resets;

                        //Reset the number of code_resets to 0 after successfully confirming email code
                        if($code_resets || $password_resets){
                            $code = random_int(100000, 999999);
                            $code_resets = 0;
                            $password_resets = 0;
                            $arr = false;
                            $arr['email'] = $email;
                            $arr['code'] = $code;
                            $arr['code_resets'] = $code_resets;
                            $arr['password_resets'] = $password_resets;
                
                            $query = "UPDATE user_full SET code=:code, code_resets =:code_resets, password_resets=:password_resets WHERE email=:email LIMIT 1";
                            $stm = $connection->prepare($query);
                            $stm -> execute($arr);
                        }

                        $_SESSION['url_address'] = $data-> url_address;
                        $_SESSION['first_name']= $data->first_name;
                        $_SESSION['email'] = $data-> email;
                        $_SESSION['message'] = "<h1 style='color:#FDC93B';>Thank you for verifying your email. </h1> <h2 style='color:white';>" . "Please create a new password. </h2>";
                        header("Location: create_password.php");
                        unset($_SESSION['login_attempts']);
                        die;
                    }
                    } 
            }
        $_SESSION['login_attempts'] +=1;
        $Error = "<font color='red';>Wrong code. Please try again.</font>";             
    }

    $_SESSION['token'] = get_random_string(61);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code - Fundafai</title>
    <script src="https://kit.fontawesome.com/80acfed07d.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/x-icon" href="../images/favicon.svg">
</head>
<body>

    <section id="subform_page">

        <div class="flexbox-container">


            <div class="flexbox-item left">

                <div class = "subflex-container">

                    <div class="logo_subflex-item"> 
                    <a href='../index.html'><h2>Fundafai</h2></a>
                    </div>

                    <div class="message_subflex-item">

                        <?php
                            if(isset($message) && $message !=""){
                                echo $message;
                            }

                            if(!isset($message) || $message == ""){
                                $message = "<h1 style='color:#FDC93B';>Verify Email</h1><h2 style='color:white';>"."Please enter the verification code sent to your email. </h2>";
                                echo $message;

                            }
                        ?>
                    </div>
                </div>

            </div>
            <br/>

            <div class='flexbox-item right'>
            <center>    
            <h3 style="font-weight:600; color: rgb(21, 21, 100)">Please verify email with the 6-digit code sent to: <span style="color:black; font-size:18px;"><?=star_email($email)?></span> </h3>

                <p style="font-size:14px;">(Please allow up to 5 minutes to receive code)</p>
                <br>
                <?php
                    if(isset($Error) && $Error !=""){
                        echo $Error;
                    }
                    ?>
                
                <form method="POST">

                <input type="hidden" name="token" value="<?=$_SESSION['token']?>">

                <?php
                    if(isset($_SESSION['login_attempts']) && $_SESSION['login_attempts']  == 5) {
                            echo "You have 1 login attempt left.<br>";
                        }
                    ?>
        <?php 
            //Sets the PHP to close verification page if too many failed attempts and redirect to reset code
                    if(isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] > 5) {
                        //$_SESSION['locked'] = time();
                        echo "Please reset code to log in. <a href='reset_code.php'>Resend Code</a>";
                    } else{
                ?>

                <input id='textbox' type='text' class='input_box' placeholder="Enter Code" name='code_check' required
                pattern="[0-9]+" maxlength='6' oninvalid="this.setCustomValidity('Please Enter Valid Code')" oninput="setCustomValidity('')"><br>

                <button type='submit' name='send'>Verify Email</button>
                <p style="font-size:14px;">Didn't receive a code? <a href='reset_code.php'>Resend code</a></p>
                    <?php } ;
                    ?>
                    <br>


            </center>
            </div>

            </form>

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