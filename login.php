<?php
    require "../secure/autoload.php";
    $Error = "";
    $message = "";
    $user_data = check_login($connection);
    

    if(isset($_SESSION['message']))
    {
        $message = $_SESSION['message'];
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
                        header("Location: index.php");
                        die;
                    }
                    $Error = "Wrong Email/Password. Please try again.";
                    $message = "";
                }

                } 
        }
        $Error = "Wrong Email/Password. Please try again.";
        $message = "";
    }

    $_SESSION['token'] = get_random_string(61);
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
</head>
<body style="font-family: Poppins">

    <style type="text/css">
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap');

        form{
            margin: auto;
            border: solid thin #aaa;
            padding: 6px;
            max-width: 200px;
        }

        #title{
            background-color: blue;
            color: white;
            padding: .5em;
            text-align: center;
        }

        #textbox{
                border: solid thin #aaa;
                margin-top: 6px;
                width: 98%;
        }

    </style>

    <form method="POST">
        <div><?php
            if(isset($Error) && $Error !=""){
                echo $Error;
            }
            if(isset($message) && $message !=""){
                echo $message;
            }

        ?></div>
        <div id='title'>Login</div>
        <input type='email' placeholder='Email' value='<?=$email?>' class='form-control' name='email' required
        pattern="[a-zA-Z0-9.@-]+" oninvalid="this.setCustomValidity('Please Enter Valid Email')" oninput="setCustomValidity('')"><br>

        <input id='textbox' type='password' placeholder="Password" name='password' 
        pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[.,_=+^():|;~#@$!%*?&-])[A-Za-z\d.,_=+^():|;~#@$!%*?&-]{8,}$" 
        required minlength="8" maxlength="25" oninvalid="this.setCustomValidity('Please Enter Valid Password')" oninput="setCustomValidity('')"><br>


        <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
        <input type='submit' value='Login'>


    </form>
    
</body>
</html>