<?php
    require "../secure/autoload.php";
    $Error = "";
    $email = "";
    $first_name = "";
    $last_name = "";
    $phone = "";
    $city = "";
    $state = "";
    $zipcode = "";
    $dob = "";
    $credit = "";
    $checkbox1 = "";
    $llc = "";
    $ip="";
    $code="";
    $state_list = array("CT", "DE", "FL", "ME", "MD", "NJ", "NY", "NC", "PA", "RI", "SC", "VT", "VA", "DC");

    if($_SERVER['REQUEST_METHOD']=="POST" && isset($_SESSION['token']) && isset($_POST['token']) && $_SESSION['token'] ==$_POST['token']){
        $email = $_POST['email'];
        if(!checkdnsrr(substr($_POST['email'], strpos($_POST['email'], '@') +1), 'MX' )){
            $Error = "<font color='red';>Please enter a valid email</font>";
        }
        //$date = date("Y-m-d H:i:s");
        $url_address = get_random_string(61);

        //Another way to avoid having slashes in input, can also straight put addslashes()

        $first_name = esc($_POST['first_name']);
        $last_name = esc($_POST['last_name']);
        $phone = esc($_POST['phone']);
        $city = esc($_POST['city']);
        $state = esc($_POST['state']);
        $zipcode = esc($_POST['zipcode']);
        $dob = esc($_POST['dob']);
        $credit = esc($_POST['credit']);
        $checkbox1 = esc($_POST['checkbox1']);
        $llc = esc($_POST['llc']);
        $ip = get_client_ip();
        $code = random_int(100000, 999999);

        //Check if email exists
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
                $Error = "<font color='red';>Email is already in use</font>";
            }
            }

        //If no error, insert the SQL and send the confirmation email
        if ($Error ==""){

            $arr['url_address'] =$url_address;
            $arr['email'] =$email;
            $arr['first_name'] =$first_name;
            $arr['last_name'] =$last_name;
            $arr['phone'] =$phone;
            $arr['city'] =$city;
            $arr['state'] =$state;
            $arr['zipcode'] =$zipcode;
            $arr['dob'] =$dob;
            $arr['credit'] =$credit;
            $arr['checkbox1'] =$checkbox1;
            $arr['llc'] =$llc;
            $arr['ip'] =$ip;
            $arr['code'] = $code;

            $query = "INSERT INTO user_full(first_name, last_name, email, phone, city, state, zipcode, dob, credit, checkbox, llc, url_address, ip, code) VALUES(:first_name, :last_name, :email, :phone, :city, :state, :zipcode, :dob, :credit, :checkbox1, :llc, :url_address, :ip, :code)";
            $stm = $connection->prepare($query);
            $stm -> execute($arr);

            //Send new lead to admin
            $to = 'simonremgmt@gmail.com';
            $subj = 'NEW STARTUP LEAD';
            $msg = 'Name: ' . $first_name . '<br />' . 'Last Name: ' . $last_name . '<br />' . 'Phone: ' . $phone . '<br />' . 'Email: ' . $email;
            sendEmail($to, $first_name, $subj, $msg);


            if ($credit >= 550 && $llc == 'yes' && in_array($state, $state_list)){

                //Send verification code to user only if they qualify
                $to = $email;
                $subj = '[Fundafai] Email Verification Code';
                $msg = '<h1>Your email verification code is: ' . $code .'</h1><br><h2>Please enter this code on the verfication page.</h2>' . '<br><p>If you did not request for this code, please disregard this email or respond with STOP in subject line.';
                sendEmail($to, $first_name, $subj, $msg);

                //Set the session variables
                $_SESSION['url_address']= $url_address;
                $_SESSION['first_name']= $first_name;
                $_SESSION['email'] =$email;
                $_SESSION['code'] =$code;
                $_SESSION['login_attempts'] =0;
                $_SESSION['message'] = "<h1 style='color:#FDC93B';>Success!</h1><h2 style='color:white';>".  $first_name .", you have been pre-approved for a $25,000+ startup funding line of credit!</h2>";

                header("Location: verify_code.php");
                die;
            }else {

                $_SESSION['url_address']= $url_address;
                $_SESSION['first_name']= $first_name;
                $_SESSION['email'] =$email;
                $_SESSION['code'] =$code;
                $_SESSION['credit'] =$credit;
                $_SESSION['llc'] =$llc;
                $_SESSION['state'] =$state;
                $_SESSION['login_attempts'] =0;
                $_SESSION['message'] = "<h1 style='color:#FDC93B';>We apologize...</h1><h2 style='color:white';>"."We are unable to approve your application at this time. </h2>";

                header("Location: alternative_solutions.php");
                die;
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
    <title>Startup Capital Funding - Fast Apply</title>
    <script src="https://kit.fontawesome.com/80acfed07d.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" type="image/x-icon" href="../images/favicon.svg">
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>

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
        /*
        margin: auto;
        border: 4px solid transparent;
        border-top-color: white;
        border-radius: 50%;
        animation: button-loading-spinner 1s ease infinite;*/
    }

    @keyframes button-loading-spinner{
        from{
            transform: rotate(0turn);
        }
        to {
            transform: rotate(1turn);
        }
    }

</style>
</head>
<body style="font-family: Poppins">

<!--Navigation-->
<nav>
        <a href='../index.html'><img src="../images/fundafai.svg" alt=""></a>
        <div class="navigation">
            <ul>
                <i id="menu-close" class="fas fa-times"></i>
                <li> <a href='../index.html'>Home</a></li>
                <li> <a href='../about.html'>Business</a></li>
                <li> <a href='blog.html'>Real Estate</a></li>
                <li> <a href='course.html'>Blog</a></li>
                <li> <a class="active" href='startup_qualify.php'>Apply</a></li>
            </ul>

            <img id="menu-btn" src="../images/menu.png" alt="">

        </div>
    </nav>

    <section id="form-page2">
        <div class="wrapper">
            
            <div class="left">
                <h1 style="color:white;">Fund Your Startup Today</h1>
                <span><span style=color:#FDC93B><i class="fa-solid fa-check"></i></span> Get started with $25,000+ line of credit</span><br/>
                <span><span style=color:#FDC93B><i class="fa-solid fa-check"></i></span> Get funded within 5-7 business days</span><br/>
                <span><span style=color:#FDC93B><i class="fa-solid fa-check"></i></span> Rates starting at 6%, pay what you use</span><br/>
                <span><span style=color:#FDC93B><i class="fa-solid fa-check"></i></span> No prepayment penalties</span><br/>
                <span><span style=color:#FDC93B><i class="fa-solid fa-check"></i></span> No tax returns necessary</span><br/><br/>
                <div class="details">
                <p style="color:gray; font-size: 14px;">Application for revolving business line of credit starting at $25,000. Repayment term of 5 years. Your available credit goes back up as you pay. 
                    No prepayment penalty. Only pay what you use. Rates starting at 6% depending on credit standing. 
                    660+ Fico Score Minimum, No tax returns, No bank statements necessary.</p>
                </div>   
            </div>    

            <div class="right">

                <form method="POST">
                    <div><?php
                        if(isset($Error) && $Error !=""){
                            echo $Error;
                        }
                    ?></div>
                    <div class="mobile-heading">
                    <h3 style="font-weight:600; color: rgb(21, 21, 100)">Get Started</h3>
                    </div>


                    <div class="form-group">
                        <input type="text" id ='first_name' name="first_name" placeholder="First Name" value='<?=$first_name?>' class="form-control" required=""
                        pattern="[a-zA-Z .'-]+" minlength="1" maxlength="30" oninput="setCustomValidity('')">
                        
                        <input type="text" id = 'last_name' name="last_name" placeholder="Last Name" value='<?=$last_name?>' class="form-control" required=""
                        pattern="[a-zA-Z .'-]+" minlength="1" maxlength="30" oninput="setCustomValidity('')">
                    </div>

                    <div class="form-wrapper">
                    <input type='email' id = 'email' placeholder='Email' value='<?=$email?>' class='form-control' name='email' required
                    pattern="[a-zA-Z0-9.@-]+"  oninput="setCustomValidity('')">
                    </div>

                    <div class="form-wrapper">
                    <input type="text" id ='phone' name='phone' value='<?=$phone?>' class='form-control' placeholder="Mobile Phone Number (ex. 777-777-7777)" minlength="10" maxlength="14" 
                    pattern="[0-9()-]+" required oninput="setCustomValidity('')">
                    </div>

                    <!--Very specific DOB pattern 
                     pattern="^(0[1-9]|1[012]|[1-9])[- /.](0[1-9]|[12][0-9]|3[01]|[1-9])[- /.](19|20)\d\d$" -->
                    <div class="form-group">
                    <input type="text" id="dob" name="dob" value='<?=$dob?>' placeholder="Date of Birth (mm/dd/yyyy)" class="form-control" required="" 
                    pattern="[0-9/-]+" minlength="6" maxlength="10" oninput="setCustomValidity('')">
                    <input type="number" id="credit" name="credit" value='<?=$credit?>' placeholder="Credit Score" class="form-control" required="" min='0' max='999' oninput="setCustomValidity('')">
                    </div>

                    <div class="form-wrapper">
                    Do You Currently Have a LLC, S-Corp, or C-Corp? &nbsp; &nbsp;
                        <input type="radio" id="corp_box" name="llc" value="yes" oninput="setCustomValidity('')" required>
                        <label for="yes">Yes</label>
                        <input type="radio" id="corp_box" name="llc" value="no" oninput="setCustomValidity('')" >
                        <label for="no">No</label>
                    </div>
                    <p style="margin-top: 14px;">  </p>

                    <div class="form-wrapper">
                    <input type="text" id='city' name="city" value='<?=$city?>' placeholder="City" class="form-control" required=""
                    pattern="[a-zA-Z .'-]+" minlength="1" maxlength="30" oninput="setCustomValidity('')">
                    </div>

                    <div class="form-group">
                    <select name="state" id="state" class="form-control" required="" oninput="setCustomValidity('')">
                        <option disabled selected value>State</option>
                        <option value="AL">AL</option>
                        <option value="AK">AK</option>
                        <option value="AR">AR</option>
                        <option value="AZ">AZ</option>
                        <option value="CA">CA</option>
                        <option value="CO">CO</option>
                        <option value="CT">CT</option>
                        <option value="DC">DC</option>
                        <option value="DE">DE</option>
                        <option value="FL">FL</option>
                        <option value="GA">GA</option>
                        <option value="HI">HI</option>
                        <option value="IA">IA</option>
                        <option value="ID">ID</option>
                        <option value="IL">IL</option>
                        <option value="IN">IN</option>
                        <option value="KS">KS</option>
                        <option value="KY">KY</option>
                        <option value="LA">LA</option>
                        <option value="MA">MA</option>
                        <option value="MD">MD</option>
                        <option value="ME">ME</option>
                        <option value="MI">MI</option>
                        <option value="MN">MN</option>
                        <option value="MO">MO</option>
                        <option value="MS">MS</option>
                        <option value="MT">MT</option>
                        <option value="NC">NC</option>
                        <option value="NE">NE</option>
                        <option value="NH">NH</option>
                        <option value="NJ">NJ</option>
                        <option value="NM">NM</option>
                        <option value="NV">NV</option>
                        <option value="NY">NY</option>
                        <option value="ND">ND</option>
                        <option value="OH">OH</option>
                        <option value="OK">OK</option>
                        <option value="OR">OR</option>
                        <option value="PA">PA</option>
                        <option value="RI">RI</option>
                        <option value="SC">SC</option>
                        <option value="SD">SD</option>
                        <option value="TN">TN</option>
                        <option value="TX">TX</option>
                        <option value="UT">UT</option>
                        <option value="VT">VT</option>
                        <option value="VA">VA</option>
                        <option value="WA">WA</option>
                        <option value="WI">WI</option>
                        <option value="WV">WV</option>
                        <option value="WY">WY</option>
                    </select>
                    <input type="text" name="zipcode" id="zip" value='<?=$zipcode?>' placeholder="Zip Code" class="form-control" required="" 
                    pattern="^[0-9]{5}(?:-[0-9]{4})?$" minlength="5" maxlength="10" oninput="setCustomValidity('')">
                    </div>

                    <div class="form-wrapper">
                        <input type="checkbox" name="checkbox1" id="agree" class="checkbox" required="" oninput="setCustomValidity('')">
                        <label for="agree"><span style="font-size:14px; font-weight: 200;">&nbsp&nbspI agree to Fundafai's Registration Terms and Privacy Policy</span> </label>
                    </div>

                    <input type="hidden" name="token" value="<?=$_SESSION['token']?>">

                    <!--Original Button
                    <button type='submit' class='prequalify_button' name='send'>Apply Now</button>
                    <br>
                    -->

                    <!--New Button  onclick="this.classList.toggle('button--loading')"-->
                    <button type='submit' name='send' id='submit_button' class="button2">
                        <span style = "font-size: 1rem; color: white;" class="button__text">Apply Now</span>
                        <span class="button_new_text"><i class="fa fa-circle-o-notch fa-spin"></i>Processing</span>
                    </button>

                        <script>
                            let buttonclick = document.getElementById('submit_button');
                            buttonclick.onclick = function(){
                                buttonclick.classList.toggle('button--loading');
                            }

                            let agree_box = document.getElementById('agree');
                            agree_box.oninvalid = function(){
                                agree_box.setCustomValidity('Please review terms and check box');
                                buttonclick.classList.remove('button--loading');
                            }

                            let zip = document.getElementById('zip');
                            zip.oninvalid = function(){
                                zip.setCustomValidity('Please enter valid zipcode');
                                buttonclick.classList.remove('button--loading');
                            }

                            let first_name = document.getElementById('first_name');
                            first_name.oninvalid = function(){
                                first_name.setCustomValidity('Please enter valid name');
                                buttonclick.classList.remove('button--loading');
                            }

                            let last_name = document.getElementById('last_name');
                            last_name.oninvalid = function(){
                                last_name.setCustomValidity('Please enter valid name');
                                buttonclick.classList.remove('button--loading');
                            }

                            let email = document.getElementById('email');
                            email.oninvalid = function(){
                                email.setCustomValidity('Please enter valid email');
                                buttonclick.classList.remove('button--loading');
                            }

                            let phone = document.getElementById('phone');
                            phone.oninvalid = function(){
                                phone.setCustomValidity('Please enter valid phone number (ex. 777-777-7777)');
                                buttonclick.classList.remove('button--loading');
                            }

                            let corp_box = document.getElementById('corp_box');
                            corp_box.oninvalid = function(){
                                corp_box.setCustomValidity('Please select an option (Yes/No)');
                                buttonclick.classList.remove('button--loading');
                            }

                            let dob = document.getElementById('dob');
                            dob.oninvalid = function(){
                                dob.setCustomValidity('Please enter valid birthdate (ex. 01/20/2000)');
                                buttonclick.classList.remove('button--loading');
                            }

                            let credit = document.getElementById('credit');
                            credit.oninvalid = function(){
                                credit.setCustomValidity('Please enter valid credit score');
                                buttonclick.classList.remove('button--loading');
                            }

                            let city = document.getElementById('city');
                            city.oninvalid = function(){
                                city.setCustomValidity('Please enter valid city');
                                buttonclick.classList.remove('button--loading');
                            }

                            let state = document.getElementById('state');
                            state.oninvalid = function(){
                                state.setCustomValidity('Please enter valid state');
                                buttonclick.classList.remove('button--loading');
                            }

                        </script>

                    <!--
                    <script>
                    const btn = document.querySelector(".button2");
                    btn.classList.toggle("button--loading");
                    </script>
                    -->

                </form>
            </div>
        </div>
    
        <div class="mobile-disclaimer">
            <p style="color: #7b838a; font-size: 14px;">*Application for revolving business lines of credit for $25,000+. Repayment term of 5 years. Your available credit goes back up as you pay. 
                No prepayment penalty. Only pay what you use. Rates starting at 6% depending on credit standing. 
                660+ Fico Score Minimum, No tax returns, No bank statements necessary.</p>
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

    <script>
        $('#menu-btn').click(function(){
            $('nav .navigation ul').addClass('active')
        });

        $('#menu-close').click(function(){
            $('nav .navigation ul').removeClass('active')
        });
    </script>

</body>
</html>