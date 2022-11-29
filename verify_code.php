<?php
    require "../secure/autoload.php";
    $Error = "";
    $user_data = check_login($connection);

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
                        $_SESSION['url_address'] = $data-> url_address;
                        $_SESSION['first_name']= $data->first_name;
                        $_SESSION['email'] = $data-> email;
                        header("Location: create_password.php");
                        unset($_SESSION['login_attempts']);
                        die;
                    }
                    } 
            }
        $_SESSION['login_attempts'] +=1;
        $Error = "Wrong code. Please try again";
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
                
    
    <?php 
    //Sets the PHP to close verification page if too many failed attempts and redirect to reset code
            if($_SESSION['login_attempts'] > 5) {
                //$_SESSION['locked'] = time();
                echo "Please reset verification code to log in.";
            } else{
        ?>
            
            <h1>Congratulations!<h1>
            <div>Hello <?=$_SESSION['first_name']?></div>
            <div>Your email is <?=$_SESSION['email']?></div>
                <br/>
            <h2>You Have Been Pre-Approved For $25,000+ Startup Funding!<h2>
                <br/>

    <form method="POST">

    <div><?php
            if(isset($Error) && $Error !=""){
                echo $Error;
            }
        ?></div>

        <div id='title'><h6>Please Verify The 6-Digit Code Sent to: <?=star_email($email)?> </h6>
        <p>(Please allow up to 5 minutes to receive code)</p>

        <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
    </div>

        <?php
            if($_SESSION['login_attempts']  == 5) {
                    echo "You have 1 login attempt left.<br>";
                }
            ?>

        <input id='textbox' type='text' placeholder="6-Digit Code" name='code_check' required
        pattern="[0-9]+" maxlength='6' oninvalid="this.setCustomValidity('Please Enter Valid Code')" oninput="setCustomValidity('')"><br><br>

        <button type='submit' name='send'>Verify</button>
            <?php } ;
            ?>

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