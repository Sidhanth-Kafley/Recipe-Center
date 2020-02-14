
<?php
include 'top.php';
$thisURL = DOMAIN . PHP_SELF;

print PHP_EOL . '<!-- SECTION: 1 Initialize variables -->' . PHP_EOL;

// define security variable to be used in SECTION 2a.
    print PHP_EOL . '<!-- SECTION: 1a. debugging setup -->' . PHP_EOL;
    if (DEBUG) {
        print '<p>Info:</p><pre>';
        print_r($_POST);
        print '</pre>';
    }
    
//Initialize variables one for each form element   
print PHP_EOL . '<!-- SECTION: 1b form variables -->' . PHP_EOL;
$email = "";
$password="";

//Initialize Error Flags one for each form element we validate   
print PHP_EOL . '<!-- SECTION: 1c form error flags -->' . PHP_EOL;
$emailERROR = false;
$passwordERROR = false;

print PHP_EOL . '<!-- SECTION: 1d misc variables -->' . PHP_EOL;
$errorMsg = array();
$dataEntered = false;
$mailed = false;

//process the submit form
    print PHP_EOL . '<!-- SECTION: 2 process for when the form is submitted -->' . PHP_EOL;
    if (isset($_POST["btnSubmit"])) {
        print PHP_EOL . '<!-- SECTION: 2a Security -->' . PHP_EOL;
        if (!securityCheck($thisURL)) {
            $msg = "<p>Sorry you cannot access this page.";
            $msg .= "Security breach detected and reported</p>";
            die($msg);
        }
        //Sanitize (clean) data
        print PHP_EOL . '<!-- SECTION: 2b Sanitize(clean) data -->' . PHP_EOL;
        
        $email = htmlentities($_POST["txtEmail"], ENT_QUOTES, "UTF-8");
        
        $password = htmlentities($_POST["txtPassword"], ENT_QUOTES, "UTF-8");

        print PHP_EOL . '<!-- SECTION: 2c Validation -->' . PHP_EOL;
        //Validation
        if ($email == "") {
        $errorMsg[] = "Please enter your email address";
        $emailERROR = true;
        }
        if ($password == "") {
            $errorMsg[] = "Please enter your passoword";
            $passwordERROR = true;
        }
         // build a message to display on the screen in section 3a and to mail
        // to the person filling out the form (section 2g).
        $records = '';
        
        if (!$errorMsg) {

        $email = $_POST["txtEmail"];
        $password = $_POST["txtPassword"];
        
        $query = "SELECT fldPassword, fldEmail FROM tblUsers WHERE fldEmail = ?";
        
        if ($thisDatabaseReader->querySecurityOk($query)) {
            $query = $thisDatabaseReader->sanitizeQuery($query);
            $records = $thisDatabaseReader->select($query, array($email));
            
}

        if($query ==""){
            $errorMsg[] = "Your Account did not exist. Please try again";
            $emailERROR = true;
        }else{
            
            if(password_verify($password,$records[0]["fldPassword"])){
                header("location: welcome.php");
            }
            else{
               $errorMsg[] = "Your password is incorrect";
               $passwordERROR = true; 
            }

        }
        $message = '<h2>Your information.</h2>';

        foreach ($_POST as $htmlName => $value) {
            $message .= "<p>";

            //breaks up the form name into words. for example
            //txtFirstName becomes First Name
            $camelCase = preg_split('/(?=[A-Z])/', substr($htmlName, 3));

            foreach ($camelCase as $oneWord) {
                $message .= $oneWord . " ";
            }

            $message .= " = " . htmlentities($value, ENT_QUOTES, "UTF-8") . "</p>";
        }
        // Process for mailing a message which contains the forms data         
        // the message was built in section 2f.    

        $to = $email;
        $cc = "";
        $bcc = "";

        $from = "Recipecenter<customer.service@recp.com";

//subject of mail should make sense to your form
        $todaysDate = strftime("%x");
        $subject = "Your information" . $todaysDate;

        $mailed = sendMail($to, $cc, $bcc, $from, $subject, $message);
    }
    }
    ?>

<article id="form2Main">
    <?php
// If its the first time coming to the form or there are errors we are going
// to display the form.
    if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) {// closing of if marked with end body submit
        print "<h2>Log in Successfully.</h2>";

        /*print"<p>For your records a copy of this data has";

        if (!$mailed) {
            print "not";
        }
        print"been sent:</p>";
        print "<p> To:" . $email . "</p>";
        print $message;*/
        
    } else {

        print '<h2>Sign In</h2>';
        // display any error messages before we print out the form

        if ($errorMsg) {
            print'<div id="errors">' . "\n";
            print"<h2>Your form has the following mistakes that need to be fixed</h2>\n";
            print"<ol>\n";

            foreach ($errorMsg as $err) {
                print"<li>" . $err . "</li>\n";
            }

            print "</ol>\n";
            print "</div>\n";
        }
	      //We used bootstrap for styling the form
        ?>
  
    <form class="form-signin" action="<?php print PHP_SELF; ?>"
              id="frmRegister"
              method="post">
              
                    <input class="form-control top"
                    <?php if ($emailERROR) print 'class="mistake"'; ?>
                        id="txtEmail"
                        maxlength="45"
                        name="txtEmail"
                        onfocus="this.select()"
                        placeholder="Enter email"
                        tabindex="120"
                        type="text"
                        value="<?php print $email; ?>"
                        >
                    <input class="form-control middle"
                    <?php if ($passwordERROR) print 'class="mistake"'; ?>
                        id="txtUserName"
                        maxlength="45"
                        name="txtPassword"
                        onfocus="this.select()"
                        placeholder="Enter password"
                        tabindex="120"
                        type="password"
                        value="<?php print $password; ?>"
                        >
              
              <button name="btnSubmit" class="btn btn-lg btn-primary btn-block" type="submit">Sign In</button>
              <p class="createAccount">Don't have an account?<a href ="https://zjin2.w3.uvm.edu/cs148/final/form1.php">Create a new account</a></p>
        
            
        <?php
    }
    ?>
    </form>
   <footer class="bottom-container">
	Developed by Sydney Hildreth, Sidhanth Kafley & Zecheng Jin.
</footer>

        
        
