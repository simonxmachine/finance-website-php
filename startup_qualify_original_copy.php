<?php
    require "../secure/autoload.php";
    $Error = "";
    $email = "";
    $username = "";
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

    if($_SERVER['REQUEST_METHOD']=="POST" && isset($_SESSION['token']) && isset($_POST['token']) && $_SESSION['token'] ==$_POST['token']){
        $email = $_POST['email'];
        if(!preg_match("/^[\w\-]+@[\w\-]+.[\w\-]+$/", $email) || !checkdnsrr(substr($_POST['email'], strpos($_POST['email'], '@') +1), 'MX' )  ){
            $Error = "Please enter a valid email";
        }
        //$date = date("Y-m-d H:i:s");
        $url_address = get_random_string(61);

        //One way to avoid having slashes in input
        $username = trim($_POST['username']);

        if(!preg_match("/^[a-zA-Z0-9]+$/", $username)){
            $Error = "Please use only letters and numbers for username";
        }

        $username = esc($username);
        $password = esc($_POST['password']);
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

        //Check if email exists
            $arr = false;
            $arr['email'] = $email;
            $query = "SELECT * FROM users WHERE email=:email LIMIT 1";
            $stm = $connection->prepare($query);
            $check = $stm -> execute($arr);


            //mysqli_query($connection, $query);
            if($check){
                //Can FETCH_OBJ or FETCH_ASSOC for array
                $data = $stm-> fetchall(PDO::FETCH_OBJ);
                if(is_array($data) && count($data) > 0){
                    $Error = "Email is already in use";
                }
                }

        if ($Error ==""){

            $arr['url_address'] =$url_address;
            //$arr['date'] =$date;
            $arr['username'] =$username;
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

            $query = "INSERT INTO users(url_address, username, password, email) VALUES(:url_address, :username, :password, :email)";
            $stm = $connection->prepare($query);
            $stm -> execute($arr);

            //mysqli_query($connection, $query);
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
    <title>Signup</title>
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
        ?></div>
        <div id='title'>Signup</div>
        <input id='textbox' type='text' placeholder='Username' value='<?=$username?>' name='username' required><br>
        <input id='textbox' type='password' placeholder='Password' name='password' required><br><br>

        <input type="text" name="first_name" placeholder="First Name" class="form-control" required=""
        pattern="[a-zA-Z .'-]+" minlength="1" maxlength="30" oninvalid="this.setCustomValidity('Please Enter Valid Name')" oninput="setCustomValidity('')">
        
        <input type="text" name="last_name" placeholder="Last Name" class="form-control" required=""
        pattern="[a-zA-Z .'-]+" minlength="1" maxlength="30" oninvalid="this.setCustomValidity('Please Enter Valid Name')" oninput="setCustomValidity('')">
       
        <input type='email' id='textbox' placeholder='Email' value='<?=$email?>' name='email' required
        oninvalid="this.setCustomValidity('Please Enter Valid Email')" oninput="setCustomValidity('')"><br>

        <input type="text" name='phone' placeholder="Mobile Phone Number (ex. 777-777-7777)" class="form-control" minlength="10" maxlength="12" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required=""
        oninvalid="this.setCustomValidity('Please Enter Valid Phone Number (ex. 777-777-7777)')" oninput="setCustomValidity('')">
       
        <input type="text" name="city" placeholder="City" class="form-control" required=""
        pattern="[a-zA-Z .'-]+" minlength="1" maxlength="30" oninvalid="this.setCustomValidity('Please Enter Valid City')" oninput="setCustomValidity('')">

        <label for="state">State: </label>
        <select name="state" id="state" placeholder="State" class="form-control" required="" oninvalid="this.setCustomValidity('Please Select State')" oninput="setCustomValidity('')">
            <option disabled selected value> -- </option>
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

        <input type="text" name="zipcode" placeholder="Postal Zip Code" class="form-control" required="" 
        pattern="^[0-9]{5}(?:-[0-9]{4})?$" minlength="5" maxlength="10" oninvalid="this.setCustomValidity('Please Enter Valid Zip Code')" oninput="setCustomValidity('')">
       
        <legend>Do You Currently Have a LLC, S-Corp, or C-Corp?</legend>
            <input type="radio" id="yes" name="llc" value="yes" required>
            <label for="yes">Yes</label>
            <input type="radio" id="no" name="llc" value="no">
            <label for="no">No</label>
       
        <input type="text" name="dob" placeholder="Birthdate (mm/dd/yyyy)" class="form-control" required="" 
        pattern="^(0[1-9]|1[012]|[1-9])[- /.](0[1-9]|[12][0-9]|3[01]|[1-9])[- /.](19|20)\d\d$" minlength="6" maxlength="10" oninvalid="this.setCustomValidity('Please Enter Valid Birthdate (ex. 01/20/2000)')" oninput="setCustomValidity('')">

        <input type="number" name="credit" placeholder="Credit Score" class="form-control" required="" min='0' max='999' oninvalid="this.setCustomValidity('Please Enter Valid Credit Score')" oninput="setCustomValidity('')">

        <label for="agree"><span style="font-size:14px; font-weight: 200;">&nbsp&nbspI agree to Fundafai's Registration Terms and Privacy Policy</span> </label>
        <input type="checkbox" name="checkbox1" id="agree" class="checkbox" required=""
        oninvalid="this.setCustomValidity('Please Check Box')" oninput="setCustomValidity('')">
       
        <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
        <input type='submit' value='Apply Now'>


    </form>
    
</body>
</html>