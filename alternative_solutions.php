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
    $credit = "";
    $llc = "";
    $state = "";
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

    if(isset($_SESSION['credit']))
    {
        $credit = $_SESSION['credit'];
    }

    if(isset($_SESSION['llc']))
    {
        $llc = $_SESSION['llc'];
    }

    if(isset($_SESSION['state']))
    {
        $state = $_SESSION['state'];
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We apologize...</title>
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

                            if(!isset($message) || $message == ""){
                                $message = "<h1 style='color:#FDC93B';>We apologize...</h1><h2 style='color:white';>"."We are unable to approve your application at this time. </h2>";
                                echo $message;
                            }
                        ?>
                    </div>
                </div>

            </div>
            <br/>

            <div class='flexbox-item right'>
                <h3 style="font-weight:600; color: rgb(21, 21, 100)">Factors affecting application approval: </h3><br>

                <?php
                    if(isset($credit) && $credit < 550){
                        echo "<span style='color:red; font-size:16px;'>- Your credit score is under 550. </span><br>
                        <span style='color:black; font-size:16px;'>We recommend a credit repair service such as <a href='https://www.creditrepair.com' target='_blank'>CreditRepair.com</a>. We welcome you to apply again once your credit improves to 550 or higher. </span><br><br>";

                    }

                    if(isset($llc) && $llc == "no"){
                        echo "<span style='color:red; font-size:16px;'>- You do not currently have a registered LLC. </span><br>
                        <span style='color:black; font-size:16px;'>The startup funding program is available only for business owners with registered LLCs, S-Corps, or C-Corps. 
                        You can register your company with a service such as <a href='https://www.legalzoom.com' target='_blank'>LegalZoom.com</a>. Once you have a registered company, we can get you started on startup funding! </span><br><br>";
                    }

                    if(isset($state) && $state != ""){
                        echo "<span style='color:red; font-size:16px;'>- You do not currently reside in a qualifying state.  </span><br>
                        <span style='color:black; font-size:16px;'>Our startup funding program is currently only available in the following states: CT, DE, FL, ME, MD, MA, NJ, NY, NC, PA, RI ,SC, VT, VA, DC. Please check with your local banks and financial instituations to see what startup funding programs are available. </span><br>";
                    }

                ?>

                <br>
                <?php
                    if(isset($Error) && $Error !=""){
                        echo $Error;
                    }
                    ?>
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