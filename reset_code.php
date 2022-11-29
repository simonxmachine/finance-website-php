<?php
    require "../secure/autoload.php";
    $Error = "";

    $url_address = "";
    $email = "";
    $code = "";

    //Add this later: If there are more than 3 code resets, lock out account and tell to call in

    if($_SERVER['REQUEST_METHOD']=="POST" && isset($_SESSION['token']) && isset($_POST['token']) && $_SESSION['token'] ==$_POST['token']){

        $email = esc($_POST['email']);
        $code = random_int(100000, 999999);

        if ($Error ==""){

            //Assign the variables for SQL search
            $arr['email'] = $email;
            $arr['code'] = $code;

            $query = "UPDATE user_full SET code=:code WHERE email=:email LIMIT 1";
            $stm = $connection->prepare($query);
            $stm -> execute($arr);

            //Send verification code to user
            $to = $email;
            $subj = '[Fundafai] Email Verification Code';
            $msg = '<h1>Your email verification code is: ' . $code .'</h1><br><h2>Please enter this code on the verfication page.</h2>' . '<br><p>If you did not request for this code, please disregard this email or respond with STOP in subject line.';
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
                    header("Location: verify_code.php");
                    die;
                }
                }

        }
        $Error = "Email does not exist. Please try again or register for new account. ";
    }

    $_SESSION['token'] = get_random_string(61);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Verification Code</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/x-icon" href="../images/favicon.svg">
</head>
<body>

    <section id="loader_page">

    <center>           
            
            <h2>Reset Email Verification Code<h2>
                <br/>

    <form method="POST">

    <div><?php
            if(isset($Error) && $Error !=""){
                echo $Error;
            }
        ?></div>

        <div id='title'><h6>Please Enter Email Connected to Account:</h6>

        <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
    </div>

        <input type='email' placeholder='Email' class='form-control' name='email' required
        pattern="[a-zA-Z0-9.@-]+" oninvalid="this.setCustomValidity('Please Enter Valid Email')" oninput="setCustomValidity('')"><br>

        <button type='submit' name='send'>Reset Code</button>

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