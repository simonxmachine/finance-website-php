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
    <title>Client Portal - Fundafai</title>
    <script src="https://kit.fontawesome.com/80acfed07d.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/x-icon" href="../images/favicon.svg">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap');

        .nav2{
            position:relative;
            background-color: rgb(21, 21, 100);
            padding: 3vh 4vh 3vh 4vh;
        }
        .nav2 .navigation ul li a{
            color: white; 
        }

        .nav2 a{
            text-decoration: none;
        }

        .portal_logo a{
            text-decoration: none;
            color: white;
        }

        #progress_notes{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }


        #progress_notes p{
            padding: 3vh 2vh 3vh 2vh;
            text-align: center;
        }

        #progress_notes2{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: #1943dc;
        }

        #progress_notes2 h2{
            padding: 3vh 3vh 3vh 3vh;
            text-align: center;
        }

        .client_portal_footer{
        padding: 2vh 4vh 2vh 4vh;

        }

        /*Status Bar*/

        .main{
            width: 100%;
            height: 100%;
            display:flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 5vh 5vh 5vh 5vh;
            text-align: center;
        }

        .main .top_p{
            margin-top:10px;
        }

        .head{
            text-align: center;
        }
        .head_1{
            font-size: 30px;
            font-weight: 600;
            color: #333;
        }
        .head_1 span{
            color: rgb(21, 21, 100);
        }
        .head_2{
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-top: 3px;
        }
        .main ul{
            display: flex;
            margin-top: 2vh;
        }
        .main ul li{
            list-style: none;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .main ul li .icon{
            font-size: 35px;
            color: rgb(21, 21, 100);
            margin: 0 60px;
        }
        .main ul li .text{
            font-size: 14px;
            font-weight: 600;
            color: rgb(21, 21, 100);
        }

        /* Progress Div Css  */

        .main ul li .progress{
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: rgb(21, 21, 100);
            margin: 14px 0;
            display: grid;
            place-items: center;
            color: #fff;
            position: relative;
            cursor: pointer;
        }
        .progress::after{
            content: " ";
            position: absolute;
            width: 125px;
            height: 5px;
            background-color: rgb(21, 21, 100);
            left: 30px;
        }
        .five::after{
            width: 0;
            height: 0;
        }

        .main ul li .progress .uil{
            display: none;
        }
        .main  ul li .progress p{
            font-size: 13px;
        }

        /* Active Css  */

        .main ul li .active{
            background-color: green;
            display: grid;
            place-items: center;
        }
        .main li .active::after{
            background-color: green;
        }
        .main ul li .active p{
            display: none;
        }
        .main  ul li .active .uil{
            font-size: 20px;
            display: flex;
        }

        /* Responsive Css  */

        @media (max-width: 820px) {

            .main ul{
                flex-direction: column;
            }
            .main ul li{
                flex-direction: row;
            }
            .main ul li .progress{
                margin: 0 30px;
            }
            .progress::after{
                width: 5px;
                height: 55px;
                top: 30px;
                left: 50%;
                transform: translateX(-50%);
                z-index: -1;
            }
            .five::after{
                height: 0;
            }
            .main ul li .icon{
                margin: 15px 0;
            }

        }

        @media (max-width:600px) {
            .head .head_1{
                font-size: 24px;
            }
            .head .head_2{
                font-size: 16px;
            }
        }

    </style>


<nav class="nav2">
            <a href='../index.html'><h2>Fund<font color='#FDC93B';>a</font>fai</h2></a>
            <div class="navigation">
                <ul>
                    <i id="menu-close" class="fas fa-times"></i>
                    <li> <a class="active" href='#'>My Account</a></li>
                    <li> <a href='logout.php'>Logout</a></li>
                </ul>

                <img id="menu-btn" src="../images/white_menu.png" alt=""></img>

            </div>
    </nav>
</head>
<body>

    <section id="progress_bar">

        <div class="main">

            <h1 style='color:rgb(21, 21, 100); line-height: 3.0rem;';>Welcome back <?=$first_name?>!</span> </h1>


            <p class='top_p' style="font-size: 1.0rem; font-weight:500;">Program: <span style="color:green;font-size: 1.0rem; font-weight:500;">Startup Funding</span> </h2>
            
            <p class='top_p' style="font-size: 1.0rem; font-weight:500;">Funding Status: <span style="color:green;font-size: 1.0rem; font-weight:500;">Step 2 - Application</span> </h2>
            
            <ul>
                <li>
                    <i class="icon uil uil-capture"></i>
                    <div class="progress one">
                        <p>1</p>
                        <i class="uil uil-check"></i>
                    </div>
                    <p class="text">Pre-Qualify</p>
                </li>
                <li>
                    <i class="icon uil uil-pen"></i>
                    <div class="progress two">
                        <p>2</p>
                        <i class="uil uil-check"></i>
                    </div>
                    <p class="text">Application</p>
                </li>
                <li>
                    <i class="icon uil uil-file-search-alt"></i>
                    <div class="progress three">
                        <p>3</p>
                        <i class="uil uil-check"></i>
                    </div>
                    <p class="text">Review</p>
                </li>
                <li>
                    <i class="icon uil uil-thumbs-up"></i>
                    <div class="progress four">
                        <p>4</p>
                        <i class="uil uil-check"></i>
                    </div>
                    <p class="text">Approval</p>
                </li>
                <li>
                    <i class="icon uil uil-money-bill"></i>
                    <div class="progress five">
                        <p>5</p>
                        <i class="uil uil-check"></i>
                    </div>
                    <p class="text">Funding</p>
                </li>
            </ul>
        </div>
    </section>

    <section id = "progress_notes">

        <p><strong><font color='#151564';>Step 2: Application</font></strong> - Please fill out all application fields to submit file for review. Estimated funding time upon application completion: <font color='green';>< 3 weeks </font></p>

    </section>

    <section id="portal_page">
        <div class="portal-container">
            <div class="portal-item portal-item-1">

            <h2 style='color:#FDC93B';>Missing Items: </h2>
            <br>
            <p style="color:white;">Address</p>
            <br>
            <p style="color:white;">SSN #</p>
            <br>
            <p style="color:white;">Business Name</p>
            <br>
            <p style="color:white;">Business EIN Number</p>
            <br>
            <p style="color:white;">Business Description</p>
            <br>
            <p style="color:white;">Corporation Documents</p>
            </div>


            <div class="portal-item portal-item-2">
            <center>    

            <form method="POST">

                <h3 style="font-weight:600; color: rgb(21, 21, 100)">Please update the following: </h3>

                <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
                <div>
                <?php
                    if(isset($Error) && $Error !=""){
                        echo $Error;
                    }
                ?>
                </div>

                <input id='textbox' type='password' class='input_box_email' placeholder="SSN #" name='password' 
                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[.,_=+^():|;~#@$!%*?&-])[A-Za-z\d.,_=+^():|;~#@$!%*?&-]{8,}$" required minlength="8" maxlength="25" oninvalid="this.setCustomValidity('Please Enter Valid Password (8+ characters with minimum 1 uppercase character, 1 lowercase character, 1 number, 1 special character [~#@$!%^*?&_=+.,:;|()-]')" oninput="setCustomValidity('')"><br>
                <input id='textbox' type='text' class='input_box_email' placeholder="Company Name" name='company_name' 
                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[.,_=+^():|;~#@$!%*?&-])[A-Za-z\d.,_=+^():|;~#@$!%*?&-]{8,}$" required minlength="8" maxlength="25" oninvalid="this.setCustomValidity('Please Enter Valid Password (8+ characters with minimum 1 uppercase character, 1 lowercase character, 1 number, 1 special character [~#@$!%^*?&_=+.,:;|()-]')" oninput="setCustomValidity('')"><br>
                <input id='textbox' type='password' class='input_box_email' placeholder="EIN #" name='confirm_password' required
                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[.,_=+^():|;~#@$!%*?&-])[A-Za-z\d.,_=+^():|;~#@$!%*?&-]{8,}$" required minlength="8" maxlength="25" oninvalid="this.setCustomValidity('Please Enter Valid Password')" oninput="setCustomValidity('')"><br>
                <br>
                <br>
                <h3 style="font-weight:600; color: rgb(21, 21, 100)">Please attach corporation documents: </h3>
                <br>
                <iframe src="https://script.google.com/macros/s/AKfycbzlgGLYdmsecsJW7YgxFb6bsItPtC2dJHvAVtbPWW_cppK4KItaXSttyo92sEi4qNd-/exec" frameBorder="0" height="100" scrolling="no"></iframe>


                <button type='submit' name='send'>Update</button>

            </form>
            </div>
     </section>

     <section id = "progress_notes2">

        <center>
            <h2 style="font-size: 20px; font-weight: 500;"><font color="#FFFFFF">Have questions or need help? Call us 888-888-8888 (M-F 9AM-5PM EST)</font></h2>
        </center>

    </section>

     <section id="portal_footer">
        <footer class="client_portal_footer">
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


    <?php 
    $number = 5;
    ?>


<script type="text/javascript">
    var php_var = "<?php echo $number;?>";

    $('#menu-btn').click(function(){
        $('nav .navigation ul').addClass('active')
    });

    $('#menu-close').click(function(){
        $('nav .navigation ul').removeClass('active')
    });

    console.log(php_var);

    const one = document.querySelector(".one");
    const two = document.querySelector(".two");
    const three = document.querySelector(".three");
    const four = document.querySelector(".four");
    const five = document.querySelector(".five");

    if (php_var==5){
        one.classList.add("active");
    }

    one.onclick = function() {
        one.classList.add("active");
        two.classList.remove("active");
        three.classList.remove("active");
        four.classList.remove("active");
        five.classList.remove("active");
    }

    two.onclick = function() {
        one.classList.add("active");
        two.classList.add("active");
        three.classList.remove("active");
        four.classList.remove("active");
        five.classList.remove("active");
    }
    three.onclick = function() {
        one.classList.add("active");
        two.classList.add("active");
        three.classList.add("active");
        four.classList.remove("active");
        five.classList.remove("active");
    }
    four.onclick = function() {
        one.classList.add("active");
        two.classList.add("active");
        three.classList.add("active");
        four.classList.add("active");
        five.classList.remove("active");
    }
    five.onclick = function() {
        one.classList.add("active");
        two.classList.add("active");
        three.classList.add("active");
        four.classList.add("active");
        five.classList.add("active");
    }

</script>


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