<?php

    require "../secure/autoload.php";
    $user_data = check_login($connection);

    if($user_data)
    {
        header("Location: client_portal.php");
    }else
    {
        header("Location: login.php");
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <div id="header">

        <?php if($username !=""): ?>
        <div>Hi <?=$_SESSION['username']?>,nice to have you back</div>
        <?php endif;?>

        <div style="float:right">
            <a href="logout.php">Logout</a>
    
        </div>


    </div>


    This is the home page
    
    <!--Must Use htmlspecialchars to prevent Javascript/Code injection-->

    <?=htmlspecialchars($user_data->location)?>
    
</body>
</html>