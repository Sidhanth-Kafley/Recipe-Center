<?php
include 'top.php';
$thisURL = DOMAIN . PHP_SELF;
print PHP_EOL . '<!-- SECTION: 1 Initialize variables -->' . PHP_EOL;
$update = false;
// define security variable to be used in SECTION 2a.
    print PHP_EOL . '<!-- SECTION: 1a. debugging setup -->' . PHP_EOL;
    if (DEBUG) {
        print '<p>Trails:</p><pre>';
        print_r($_POST);
        print '</pre>';
    }
//Initialize variables one for each form element   
print PHP_EOL . '<!-- SECTION: 1b form variables -->' . PHP_EOL;
$firstName = "";
$lastName = "";
$phoneNumber = "";
$email = "";
$Birthday ="";
$password="";
$confirmpassword="";
$gender = "Male";
// If the form is an update we need to intial the values from the table
    /*if (isset($_GET["var"])) {
        $pmkUserID = (int) htmlentities($_GET["var"], ENT_QUOTES, "UTF-8");

        $query = 'SELECT pmkUserID, fldFirstName, fldLastName, fldBirthday, fldPhoneNumber, fldEmail,fldPassword ';
        $query .= 'FROM tblUsers WHERE pmkUserID = ?';

        $data = array($pmkUserID);


        if ($thisDatabaseReader->querySecurityOk($query, 1)) {
            $query = $thisDatabaseReader->sanitizeQuery($query);
            $store = $thisDatabaseReader->select($query, $data);
        }
        $firstName = $store[0]["fldFirstName"];
        $lastName = $store[0]["fldLastName"];
        $Birthday = $store[0]["fldBirthday"];
        $phoneNumber = $store[0]["fldPhoneNumber"];
        $email = $store[0]["fldEmail"];
        $password = $store[0]["fldPassword"];

        $query = 'SELECT pfkTag ';
        $query .= 'FROM tblTrailsTags WHERE pfkTrailsId = ?';
        if ($thisDatabaseReader->querySecurityOk($query, 1)) {
            $query = $thisDatabaseReader->sanitizeQuery($query);
            $store = $thisDatabaseReader->select($query, $data);
        }

        foreach ($store as $row) {
            array_push($TagsSelected, $row['pfkTag']);
        }
    }*/
//Initialize Error Flags one for each form element we validate   
print PHP_EOL . '<!-- SECTION: 1c form error flags -->' . PHP_EOL;
$firstNameERROR = false;
$lastNameERROR = false;
$phoneNumberERROR = false;
$emailERROR = false;
$BirthdayERROR = false;
$passwordERROR = false;
$confirmpasswordERROR = false;
$genderERROR = false;
print PHP_EOL . '<!-- SECTION: 1d misc variables -->' . PHP_EOL;
$errorMsg = array();
$dataEntered = false;
//$mailed = false;
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
        /*$pmkTrailsId = (int) htmlentities($_POST["hidTrailsId"], ENT_QUOTES, "UTF-8");
        if ($pmkTrailsId > 0) {
            $update = true;
        }*/
        $firstName = htmlentities($_POST["txtFirstName"], ENT_QUOTES, "UTF-8");

        $lastName = htmlentities($_POST["txtLastName"], ENT_QUOTES, "UTF-8");

        $phoneNumber = htmlentities($_POST["txtPhoneNumber"], ENT_QUOTES, "UTF-8");

        $email = htmlentities($_POST["txtEmail"], ENT_QUOTES, "UTF-8");

        $Birthday = htmlentities($_POST["txtBirthday"], ENT_QUOTES, "UTF-8");
        
        
        $password = htmlentities($_POST["txtPassword"], ENT_QUOTES, "UTF-8");
        
        $confirmpassword = htmlentities($_POST["txtConfirmpassword"], ENT_QUOTES, "UTF-8");
        
        $gender =htmlentities($_POST["radGender"], ENT_QUOTES, "UTF-8");

        print PHP_EOL . '<!-- SECTION: 2c Validation -->' . PHP_EOL;
        //Validation
        if ($firstName == "") {
            $errorMsg[] = "Please enter your First Name";
            $firstNameERROR = true;
        } elseif (!verifyAlphaNum($firstName)) {
            $errorMsg[] = "Your first name appears to have extra characters.";
            $firstNameERROR = true;
        }
        if ($lastName == "") {
            $errorMsg[] = "Please enter your Last Name";
            $lastNameERROR = true;
        } elseif (!verifyAlphaNum($lastName)) {
            $errorMsg[] = "Your last name appears to have extra characters.";
            $lastNameERROR = true;
        }
        if ($phoneNumber == "") {
            $errorMsg[] = "Please enter your phone Number";
            $phoneNumberERROR = true;
        } elseif (!verifyNumeric($phoneNumber)) {
            $errorMsg[] = "Invalid phone Number.";
            $phoneNumberERROR = true;
        }
        if ($Birthday == "") {
        $errorMsg[] = "Please enter your Birthday";
        $BirthdayERRORERROR = true;
        } elseif (!validateDate($Birthday)) {
        $errorMsg[] = "Your Birthday is invalid, Please enter it in the format: yyyy-mm-dd.";
        $BirthdayERROR = true;
        }
        if ($email == "") {
        $errorMsg[] = "Please enter your email address";
        $emailERROR = true;
        } elseif (!verifyEmail($email)) {
        $errorMsg[] = " Your email address appears to be incorrect.";
        $emailERROR = true;
        }
        if ($password == "") {
            $errorMsg[] = "Please set up your password";
            $passwordERROR = true;
        } elseif (!verifyPassword($password)) {
            $errorMsg[] = "Password must include at least one capital letter, one number, one lowercase letter, a special letter and length greater that 8.";
            $passwordERROR = true;
        }
        if ($confirmpassword == "") {
            $errorMsg[] = "Please reenter your password";
            $confirmpasswordERROR = true;
        } elseif ($confirmpassword != $password) {
            $errorMsg[] = "Confirm password does not match with your password.";
            $confirmpasswordERROR = true;
        }
        if ($gender != "Male" AND $gender != "Female" AND $gender != "other") {
        $errorMsg[] = "Please choose a gender";
        $genderERROR = true;
        }
         // build a message to display on the screen in section 3a and to mail
        // to the person filling out the form (section 2g).
        if (!$errorMsg) {
        //if ($debug){
        //print "<p>Form is valid</p>";
        print PHP_EOL . '<!-- SECTION: 2e Save data-->' . PHP_EOL;

        $hash_password = password_hash($password,PASSWORD_DEFAULT);
            $dataEntered = false;
            $data = array();
            $data[] = $firstName;
            $data[] = $lastName;
            $data[] = $Birthday;
            $data[] = $phoneNumber;
            $data[] = $email;
            $data[] = $hash_password;
            try {
                $thisDatabaseWriter->db->beginTransaction();
                if ($update) {
                    $query = 'UPDATE tblUsers SET ';
                } else {
                    $query = 'INSERT INTO tblUsers SET ';
                }

                $query .= 'fldFirstName = ?, ';
                $query .= 'fldLastName = ?, ';
                $query .= 'fldBirthday = ?, ';
                $query .= 'fldPhoneNumber = ?, ';
                $query .= 'fldEmail = ?, ';
                $query .= 'fldPassword = ? ';
                
                if ($update) {
                    $query .= 'WHERE pmkUserID = ?';
                    $data[] = $pmkUserID;
                    if (DEBUG) {
                        $thisDatabaseWriter->TestSecurityQuery($query, 1);
                        print_r($data);
                    }
                    if ($thisDatabaseReader->querySecurityOk($query, 1)) {
                        $query = $thisDatabaseWriter->sanitizeQuery($query);
                        $results = $thisDatabaseWriter->update($query, $data);
                    }
                } else {
                    if ($thisDatabaseWriter->querySecurityOk($query, 0)) {
                        $query = $thisDatabaseWriter->sanitizeQuery($query);


                        $results = $thisDatabaseWriter->insert($query, $data);

                        $pmkUserID = $thisDatabaseWriter->lastInsert();
                    }
                }
                // all sql statements are done so lets commit to our changes
                $dataEntered = $thisDatabaseWriter->db->commit();

                if (DEBUG) {
                    print "<p>transaction complete ";
                }
        }catch (PDOExecption $e) {
                $thisDatabaseWriter->db->rollback();
                if (DEBUG) {
                    print "Error!: " . $e->getMessage() . "</br>";
                }
                $errorMsg[] = "There was a problem with accepting your data please contact us directly.";
            }
        }
    }
       
    
    ?>
<article id="main">
    <?php
// If its the first time coming to the form or there are errors we are going
// to display the form.
    if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) {// closing of if marked with end body submit
        print "<h2>Thank you for Signing up to become a memeber of our website.</h2>";

        print"<p>We send a confirm email to you</p>";
        print'<a href ="https://zjin2.w3.uvm.edu/cs148/final/form2.php" class ="ready">Ready to Sign in ?</a>';
        header('Location: form2.php');
    } else {
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
    
    <form action="<?php print PHP_SELF; ?>"
               class = "form-signin" id="frmRegister"
              method="post">

            <fieldset class="contact">
                
                    
                    <input class="form-control top" class="required text-field" for="txtFirstName"
                    <?php if ($firstNameERROR) print 'class="mistake"'; ?>
                           id="txtFirstName"
                           maxlength="45"
                           name="txtFirstName"
                           onfocus="this.select()"
                           placeholder="Enter your first name"
                           tabindex="100"
                           type="text"
                           value="<?php print $firstName; ?>"
                           >
                    
                
                    
                    <input class="form-control middle" class="required text-field" for="txtLastName"
                    <?php if ($lastNameERROR) print 'class="mistake"'; ?>
                        id="txtLastName"
                        maxlength="45"
                        name="txtLastName"
                        onfocus="this.select()"
                        placeholder="Enter your last name"
                        tabindex="100"
                        type="text"
                        value="<?php print $lastName; ?>"
                        >
                   
                
                    
                    <input class="form-control middle" class="required text-field" for="txtBirthday"
                    <?php if ($BirthdayERROR) print 'class="mistake"'; ?>
                        id="txtBirthday"
                        maxlength="45"
                        name="txtBirthday"
                        onfocus="this.select()"
                        placeholder="Enter Birthday, format: yyyy-mm-dd"
                        tabindex="100"
                        type="text"
                        value="<?php print $Birthday; ?>"
                        >
                    
                
                    <input class="form-control middle" class="required text-field" for="txtPhoneNumber"
                    <?php if ($phoneNumberERROR) print 'class="mistake"'; ?>
                        id="txtPhoneNumber"
                        maxlength="45"
                        name="txtPhoneNumber"
                        onfocus="this.select()"
                        placeholder="Phone Number"
                        tabindex="100"
                        type="text"
                        value="<?php print $phoneNumber; ?>"
                        >
                    
                
                    
                    <input class="form-control middle" class="required text-field" for="txtEmail"
                    <?php if ($emailERROR) print 'class="mistake"'; ?>
                        id="txtEmail"
                        maxlength="45"
                        name="txtEmail"
                        onfocus="this.select()"
                        placeholder="Enter your email"
                        tabindex="120"
                        type="text"
                        value="<?php print $email; ?>"
                        >
                    
                
                    
                    <input class="form-control middle" class="required text-field" for="txtPassword"
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
                    
                
                   
                    <input class="form-control middle" class="required text-field" for="txtConfirmpassword"
                    <?php if ($confirmpasswordERROR) print 'class="mistake"'; ?>
                        id="txtConfirmpassword"
                        maxlength="45"
                        name="txtConfirmpassword"
                        onfocus="this.select()"
                        placeholder="Confirm password"
                        tabindex="120"
                        type="password"
                        value="<?php print $confirmpassword; ?>"
                        >
                    
                
            </fieldset>
                <fieldset class="radio <?php if ($genderERROR) print ' mistake'; ?>">
                <legend>Gender</legend>
                <p>
                    <label class="radio-field">
                        <input type="radio" 
                               id="radGenderMale" 
                               name="radGender" 
                               value="Male" 
                               tabindex="572"
                               <?php if ($gender == "Male") echo ' checked="checked" '; ?>>
                        Male</label>
                </p>

                <p>    
                    <label class="radio-field">
                        <input type="radio" 
                               id="radGenderFemale" 
                               name="radGender" 
                               value="Female" 
                               tabindex="582"
                               <?php if ($gender == "Female") echo ' checked="checked" '; ?>>
                        Female</label>
                </p>
                <p>    
                    <label class="radio-field">
                        <input type="radio" 
                               id="radGenderOther" 
                               name="radGender" 
                               value="Other" 
                               tabindex="582"
                               <?php if ($gender == "Other") echo ' checked="checked" '; ?>>
                        other</label>
                </p>
            </fieldset>
        <fieldset>
                <input class="btn btn-lg btn-primary btn-block" id="btnSubmit" name="btnSubmit" tabindex="900" type="submit" value="Submit" >
            </fieldset>
        <p class ='ready'>Already got an account?<a href ="https://zjin2.w3.uvm.edu/cs148/final/form2.php">Sign in</a>
        <?php
    }
    ?>
    </form>
    
    <?php
    include "footer.php";?>
        
        
