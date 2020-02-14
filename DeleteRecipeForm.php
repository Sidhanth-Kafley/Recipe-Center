<?php
include 'top.php';
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// 

$thisURL = DOMAIN . PHP_SELF;

print PHP_EOL . '<!-- SECTION: 1 Intitialize variables -->' . PHP_EOL;
// These variables are used in both sections 2 and 3, otherwise we would
// declare them in the section we needed them

$update = false;

print PHP_EOL . '<!-- SECTION 1a. debugging step -->' . PHP_EOL;
// We print out the post array so that we can see out form is working. 
// Normally I wrap this in a debug statent but for now I want to always 
// display it. When you first come to the form it is empty. When you submit the 
// form it displays the content of the array. 

if (DEBUG) {
    print '<p>Post Array:</p><pre>';
    print_r($_POST);
    print '</pre>';
}

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// 
print PHP_EOL . '<!-- SECTION: 1b form variables -->' . PHP_EOL;
//
// Initialize variables one for each form element 
// in the order they appear on the form
$pmkRecipe = -1;

$firstName = "";
$lastName = "";
$email = "";
$recipeName = "";
$region = "North America";
$prepTime = "";
$cookTime = "";
$flavor = "";
$comments = "";
$recipe = "";
$Materials = array();
$MaterialSelected = array();


if (isset($_GET["id"])) {
    $pmkRecipe = (int) htmlentities($_GET["id"], ENT_QUOTES, "UTF-8");


    $query = 'SELECT fldFirstName, fldLastName, fldRecipeName,fldEmail, fldRegion, fldPrepTime, fldCookTime, fldFlavor, fldComments, fldRecipe ';
    $query .= 'FROM tblRecipe ';
    $query .= 'WHERE pmkRecipeId = ?';
    

    $dataRecord = array();

    $dataRecord[] = $pmkRecipe;

    if ($thisDatabaseReader->querySecurityOk($query, 1)) {
        $query = $thisDatabaseReader->sanitizeQuery($query);
        $store = $thisDatabaseReader->select($query, $dataRecord);
    }
 
    $firstName = $store[0]["fldFirstName"];
    $lastName = $store[0]["fldLastName"];
    $recipeName = $store[0]["fldRecipeName"];
    $email = $store[0]["fldEmail"];
    $region = $store[0]["fldRegion"];
    $prepTime = $store[0]["fldPrepTime"];
    $cookTime = $store[0]["fldCookTime"];
    $flavor = $store[0]["fldFlavor"];
    $comments = $store[0]["fldComments"];
    $recipe = $store[0]["fldRecipe"];
    $query = 'SELECT pfkMaterial ';
    $query .= 'FROM tblRecipeMaterials WHERE pfkRecipeId = ?';
    if ($thisDatabaseReader->querySecurityOk($query, 1)) {
            $query = $thisDatabaseReader->sanitizeQuery($query);
            $store = $thisDatabaseReader->select($query, $dataRecord);
        }
       foreach ($store as $row) {
            array_push($MaterialSelected, $row['pfkMaterial']);
        }
}

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
print PHP_EOL . '<!-- SECTION: 1c form error flags -->' . PHP_EOL;
// 
// Initialize Error Flags one for each form element we validate
// in the order they appear on the form
$firstNameERROR = false;
$lastNameERROR = false;
$emailERROR = false;
$recipeNameERROR = false;
$prepTimeERROR = false;
$cookTimeERROR = false;
$flavorERROR = false;
$commentsERROR = "";
$recipeERROR = "";
$materialsERROR = false;
$regionERROR = false;





//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
print PHP_EOL . '<!-- SECTION: 1d misc variables -->' . PHP_EOL;
//
// create array to hoold error messaged filled (if any) in 2d displayed in 3c
$errorMsg = array();
$dataEntered = false;

// have we mailed the info to the user, flag variable?
$mailed = false;

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
print PHP_EOL . '<!-- SECTION: 2 Process for when the form is submitted -->' . PHP_EOL;
//


if (isset($_POST["btnDelete"])) {
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2a Security
//
    if (!securityCheck($yourURL)) {
        $msg = "<p>Sorry you cannot access this page. ";
        $msg.= "Security breach detected and reported</p>";
        die($msg);
    }
    
    $pmkRecipe = (int) htmlentities($_POST["hidRecipeId"], ENT_QUOTES, "UTF-8");
    $firstName = htmlentities($_POST["txtFirstName"], ENT_QUOTES, "UTF-8");
    $lastName = htmlentities($_POST["txtLastName"], ENT_QUOTES, "UTF-8");
    $email = htmlentities($_POST["txtEmail"], ENT_QUOTES, "UTF-8");
    $recipeName = htmlentities($_POST["txtRecipeName"], ENT_QUOTES, "UTF-8");
    //  area the food originates from
    $region = htmlentities($_POST["lstRegion"], ENT_QUOTES, "UTF-8");
    $prepTime = htmlentities($_POST["txtPrepTime"], ENT_QUOTES, "UTF-8");
    $cookTime = htmlentities($_POST["txtCookTime"], ENT_QUOTES, "UTF-8");
    $flavor = htmlentities($_POST["radFlavor"], ENT_QUOTES, "UTF-8");
    $comments = htmlentities($_POST["txtComments"], ENT_QUOTES, "UTF-8");
    $recipe = htmlentities($_POST["txtRecipe"], ENT_QUOTES, "UTF-8");
    
    $dataDeleteted = false;
    
    
    try {
        $thisDatabaseWriter->db->beginTransaction();

        $query = "DELETE ";
        $query .= "FROM tblRecipe ";
        $query .= "WHERE pmkRecipeId = " . $pmkRecipe;

        $dataRecord[] = $pmkRecipe;
        

        if ($thisDatabaseReader->querySecurityOk($query, 1)) {
            $query = $thisDatabaseWriter->sanitizeQuery($query);
            $results = $thisDatabaseWriter->delete($query, $dataRecord);
        }

        // all sql statements are done so lets commit to our changes
       
        $dataDeleteted = $thisDatabaseWriter->db->commit();

       
        if (DEBUG)
            print "<p>transaction complete ";
    } catch (PDOExecption $e) {
        $thisDatabaseWriter->db->rollback();
        if (DEBUG)
            print "Error!: " . $e->getMessage() . "</br>";
        $errorMsg[] = "There was a problem with accepting your data please contact us directly.";
    }

    if ($dataDeleteted) {
        header("location: index.php");
    } else {
        print '<p>Recipe was not Deleted</p>';
    }
}


if (isset($_POST["btnSubmit"])) {

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    print PHP_EOL . '<!--SECTION: 2a Security -->' . PHP_EOL;

    //the url for this form

    if (!securityCheck($thisURL)) {
        $msg = '<p>Sorry you cannot access this page.</p>';
        $msg .= '<p>Security breach detected and reported.</p>';
        die($msg);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //    
    print PHP_EOL . '<!-- SECTION: 2b Sanitize (clean) data -->' . PHP_EOL;
    // remove any potential JavaScript or html code from users input on the
    // form. Note it is best to follow the same order as declared in section 1c.


    $pmkRecipe = (int) htmlentities($_POST["hidRecipeId"], ENT_QUOTES, "UTF-8");
    if ($pmkRecipe > 0) {
        $update = true;
    }


    $firstName = htmlentities($_POST["txtFirstName"], ENT_QUOTES, "UTF-8");
    $lastName = htmlentities($_POST["txtLastName"], ENT_QUOTES, "UTF-8");
    $email = htmlentities($_POST["txtEmail"], ENT_QUOTES, "UTF-8");
    $recipeName = htmlentities($_POST["txtRecipeName"], ENT_QUOTES, "UTF-8");
    //  area the food originates from
    $region = htmlentities($_POST["lstRegion"], ENT_QUOTES, "UTF-8");
    $prepTime = htmlentities($_POST["txtPrepTime"], ENT_QUOTES, "UTF-8");
    $cookTime = htmlentities($_POST["txtCookTime"], ENT_QUOTES, "UTF-8");
    $flavor = htmlentities($_POST["radFlavor"], ENT_QUOTES, "UTF-8");
    $comments = htmlentities($_POST["txtComments"], ENT_QUOTES, "UTF-8");
    $recipe = htmlentities($_POST["txtRecipe"], ENT_QUOTES, "UTF-8");





    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    print PHP_EOL . '<!-- SECTION: 2c Validation -->' . PHP_EOL;
    // 
    // Validation section. Check each value for possible errors, empty or
    // not what we expect. You will need an IF block for each element you will
    // check (see above section 1c and 1d). The if blocks should also be in the 
    // order that the elements appear on your form so that the error messages
    // will be in the order they appear. errorMsg will be displayed on the form
    // see section 3b. The error flag ($emailERROR) will be used in section 3c.
    //first name
    if ($firstName == "") {
        $errorMsg[] = "Please enter your first name";
        $firstNameERROR = true;
    } elseif (!verifyAlphaNum($firstName)) {
        $errorMsg[] = "Your first name appears to have extra character.";
        $firstNameERROR = true;
    }

    //last name
    if ($lastName == "") {
        $errorMsg[] = "Please enter your last name";
        $lastNameERROR = true;
    } elseif (!verifyAlphaNum($lastName)) {
        $errorMsg[] = "Your last name appears to have extra character.";
        $lastNameERROR = true;
    }


    //Recipe Name
    if ($recipeName == "") {
        $errorMsg[] = "Please enter the name of the recipe.";
        $recipeNameERROR = true;
    } elseif (!verifyAlphaNum($recipeName)) {
        $errorMsg[] = "The name appears to have extra character.";
        $recipeNameERROR = true;
    } else {
        $recipeNameERROR = false;
    }

    //email
    if ($email == "") {
        $errorMsg[] = 'Please enter your email address.';
        $emailERROR = true;
    } elseif (!verifyEmail($email)) {
        $errorMsg[] = 'Your email address appears to be incorrect.';
        $emailERROR = true;
    }

    // area where food is from
    if ($region == "") {
        $errorMsg[] = "Please the region the recipe originates from.";
        $regionERROR = true;
    }

    //describing comments
    // Note that this if statments mean the comments are not required 
    if ($prepTime == "") {
        $errorMsg[] = "Please enter a the time it takes to prepare.";
        $prepTimeERROR = true;
    } elseif (!verifyNumeric($prepTime)) {
        $errorMsg[] = 'Your prepare time appears to be incorrect.';
        $prepTimeERROR = true;
    }

    if ($cookTime == "") {
        $errorMsg[] = "Please enter a the time it takes to cook.";
        $cookTimeERROR = true;
    } elseif (!verifyNumeric($cookTime)) {
        $errorMsg[] = 'Your cook time appears to be incorrect.';
        $cookTimeERROR = true;
    }

    //event options
    if ($flavor != "Sweet" AND $flavor != "Spicy" AND $flavor != "Sour" AND $flavor != "Salty" AND $flavor != "Other") {
        $errorMsg[] = "Please choose a flavor.";
        $flavorERROR = true;
    }

    //comments
    // Note that this if statments mean the comments are not required 
    if ($flavor == "Other") {
        if ($comments == "") {
            $errorMsg[] = "Since you selected the other option, please describe the event.";
            $commentsERROR = true;
        } elseif (!verifyAlphaNum($comments)) {
            $errorMsg[] = "Your comments appear to have extra characters that are not allowed.";
            $commentsERROR = true;
        }
    }

    if ($recipe == "") {
        $errorMsg[] = "Please fill out the how to make the food.";
        $recipeERROR = true;
    }

    if (count($Materials) < 0) {
        $errorMsg[] = "No materials chosen.";
        $materialsERROR = true;
    }


  
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    print PHP_EOL . '<!-- SECTION: 2d Process Form - Passed Validation -->' . PHP_EOL;
    // 
    // Process for when the form passes validation (the errorMsg array is empty)
    //
    if (!$errorMsg) {
        // if ($debug)
        //print '<p>Form is valid</p>';
        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        print PHP_EOL . '<!-- SECTION: 2e Save Data -->' . PHP_EOL;
        //
        // This block saves the data to a CSV file. 
        // array used to hold form values that will be saved to a CSV file
        $dataRecord = array();
        $dataEntered = false;



        $dataRecord[] = $firstName;
        $dataRecord[] = $lastName;
        $dataRecord[] = $recipeName;
        $dataRecord[] = $email;
        $dataRecord[] = $region;
        $dataRecord[] = $prepTime;
        $dataRecord[] = $cookTime;
        $dataRecord[] = $flavor;
        $dataRecord[] = $comments;
        $dataRecord[] = $recipe;

        try {
            $thisDatabaseWriter->db->beginTransaction();
            
            if ($update) {
                $query = 'UPDATE tblRecipe SET ';
            } else {
                $query = 'INSERT INTO tblRecipe SET ';
            }

            $query .= 'fldFirstName = ?, ';
            $query .= 'fldLastName = ?, ';
            $query .= 'fldRecipeName = ?, ';
            $query .= 'fldEmail = ?, ';
            $query .= 'fldRegion = ?, ';
            $query .= 'fldPrepTime = ?, ';
            $query .= 'fldCookTime = ?, ';
            $query .= 'fldFlavor = ?, ';
            $query .= 'fldComments = ?, ';
            $query .= 'fldRecipe = ? ';


            if ($update) {

                $query .= 'WHERE pmkRecipeId = ?';
                $thisDatabaseReader->testSecurityQuery($query, 1);

                $dataRecord[] = $pmkRecipe;
                
                if (DEBUG) {
                    $thisDatabaseWriter->TestSecurityQuery($query, 1);

                    print_r($dataRecord);
                }
                
                if ($thisDatabaseReader->querySecurityOk($query, 1)) {
                    $query = $thisDatabaseWriter->sanitizeQuery($query);
                    $store = $thisDatabaseWriter->update($query, $dataRecord);
                    $primaryKey = $pmkRecipe;
                }
            } else {
                
                if ($thisDatabaseWriter->querySecurityOk($query, 0)) {
                    $query = $thisDatabaseWriter->sanitizeQuery($query);


                    $store = $thisDatabaseWriter->insert($query, $dataRecord);

                    $primaryKey = $thisDatabaseWriter->lastInsert();
                }
            }

            // Delete query and execution
            $query = 'DELETE FROM tblRecipeMaterials WHERE pfkRecipeId = ?';
            $deleteMaterialsArray = array($primaryKey);


            if ($thisDatabaseReader->querySecurityOk($query, 1)) {
                $query = $thisDatabaseWriter->sanitizeQuery($query);
                $store = $thisDatabaseWriter->delete($query, $deleteMaterialsArray);
            }

            // Inserts each check into the database
            $query = 'INSERT INTO tblRecipeMaterials (pfkRecipeId, pfkMaterial) VALUES ';
            $query .= '(?, ?)';

            // $thisDatabaseReader->testSecurityQuery($query, 0);


            foreach ($_POST['chkArrays'] as $material) {
                $materialToInsert = $material;
                $materialInsertArray = array();
                array_push($materialInsertArray, $primaryKey);
                array_push($materialInsertArray, $materialToInsert);
                if ($thisDatabaseWriter->querySecurityOk($query, 0)) {

                    $query = $thisDatabaseWriter->sanitizeQuery($query);

                    $store = $thisDatabaseWriter->insert($query, $materialInsertArray);
                }
            }

            if (DEBUG) {
                $i = 0;
                print '<p>Stupid array: <pre>';
                print_r($_POST['chkArrays']);

                print '<p>Really Stupid array: should be the same as above?<pre>';
                foreach ($_POST['chkArrays'] as $checkbox) {
                    $materialInsertArray[$i] = $checkbox;
                    $i++;
                }
            }

            // all sql statements are done so lets commit to our changes
            $dataEntered = $thisDatabaseWriter->db->commit();

            if (DEBUG) {
                print "<p>pmk= " . $primaryKey;
            }

            if (DEBUG) {
                print "<p>transaction complete ";
            }
        } catch (PDOExecption $e) {
            
            $thisDatabaseWriter->db->rollback();
            
            if (DEBUG) {
                print "Error!: " . $e->getMessage() . "</br>";
            }
            
            $errorMsg[] = "There was a problem with accepting your data please contact us directly.";
        }
    }



    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
        print PHP_EOL . '<!-- SECTION: 2f Create message -->' . PHP_EOL;
    //
    // build a message to display on the screen in section 3a and to mail
    // to the person filling out the form (section 2g).

    $message = '<h2>Your information.</h2>';


    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
        print PHP_EOL . '<!-- SECTION: 2g Mail to user -->' . PHP_EOL;
    //
    // Process for mailing a message which contains the form's data
    // the message was built in section 2f.
    $to = $email; // the person who filled out the form   
    $cc = ''; //carbon copy
    $bcc = 'srhildre@uvm.edu'; //blind carbon copy

    $from = 'Sydney Hildreth <srhildre@uvm.edu>';

    //subject of mail should mkae sense to your form 
    $todaysDate = strftime("%x");
    $subject = 'Form Submitted: ' . $todaysDate;

    $mailed = sendMail($to, $cc, $bcc, $from, $subject, $message);
} //end form is valid
//#############################################################################
//
print PHP_EOL . '<!-- SECTION: 3 Display Form -->' . PHP_EOL;
//
?>
<main>
    <article>
        <?php
//##################################
//
        print PHP_EOL . '<!-- SECTION 3a -->' . PHP_EOL;
//
// If its the first timing coming to the form or there are errors we are going
// to display the form.
//if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) {
//       // closing of if marked with: end body submit))
        if ($dataEntered) {
            print '<h2 style="color:blue;font-size:30px;">Thank you for providing your recipe! Hopefully many people enjoy it! </h2>';

            print '<p>For your records, a copy the form has ';

            if (!$mailed) {
                print "not ";
            }

            print 'been sent:</p>';
            print '<p>To: ' . $email . '</p>';

            print $message;
        } else {
            print '<h2>Delete Recipe</h2>';


            //##################################
            //
            print PHP_EOL . '<!-- SECTION 3b Error Messages -->' . PHP_EOL;
            //
            // display any error messages before we print out the form

            if ($errorMsg) {
                print '<div id="errors">' . PHP_EOL;
                print '<h2>Please fix the following mistakes from the form.</h2>' . PHP_EOL;
                print '<ol>' . PHP_EOL;

                foreach ($errorMsg as $err) {
                    print '<li>' . $err . '</li>' . PHP_EOL;
                }

                print '</ol>' . PHP_EOL;
                print '</div>' . PHP_EOL;
            }



            //##################################
            //
        print PHP_EOL . '<!-- SECTION 3c html Form -->' . PHP_EOL;
            //
            /* Display the HTML form. note that the action is to this same page. $phpSelf
              is defined in top.php
              NOTE the line:
              value="<?php print $email; ?>
              this makes the form sticky by displaying either the initial default value (line ??)
              or the value they typed in (line ??)
              NOTE this line:
              <?php if($emailERROR) print 'class="mistake"'; ?>
              this prints out a css class so that we can highlight the background etc. to
              make it stand out that a mistake happened here.
             */
             //We used bootstrap for styling the form
            ?>   
       

            <form class = 'form-signin' action="<?php print PHP_SELF; ?>"
              method="post"
              id="frmDelete">
            <input type="hidden" name="hidRecipeId" value="<?php print $pmkRecipe; ?>" >
            <input type="hidden" name="txtFirstName" value="<?php print $firstName; ?>">
            <input type="hidden" name="txtLastName" value="<?php print $lastName; ?>">
            <input type="hidden" name="txtEmail" value="<?php print $email; ?>">
            <input type="hidden" name="txtRecipeName" value="<?php print $recipeName; ?>">
            <input type="hidden" name="lstRegion" value="<?php print $region; ?>">
            <input type="hidden" name="txtPrepTime" value="<?php print $prepTime; ?>">
            <input type="hidden" name="txtCookTime" value="<?php print $cookTime; ?>">
            <input type="hidden" name="radFlavor" value="<?php print $flavor; ?>">
            <input type="hidden" name="txtComments" value="<?php print $comments; ?>">
            <input type="hidden" name="txtRecipe" value="<?php print $recipe; ?>">
            <fieldset class="buttons">
                <input class="btn btn-lg btn-primary btn-block" type="submit" name="btnDelete" value="Delete" tabindex="900">
            </fieldset> <!-- ends buttons -->
        </form> 

            <form action=''<?php print PHP_SELF; ?>'
                  class = 'form-signin' id='frmRegister'
                  method='post'>
                <input type="hidden" id="hidRecipeId" name="hidRecipeId"
                       value="<?php print $pmkRecipe; ?>">


                <fieldset class="contact">
                    <legend>Contact Information</legend>


                    <input class="form-control top"  for = "txtFirstName"
                    <?php if ($firstNameERROR) print 'class="mistake"'; ?>
                           id ="txtFirstName"
                           maxlength="45"
                           name="txtFirstName"
                           onfocus="this.select()"
                           placeholder="Enter your first name"
                           tabindex="100"
                           type="text"
                           value='<?php print $firstName; ?>'
                           >    




                    <input class="form-control middle" class = "required" for = "txtLastName"
                    <?php if ($lastNameERROR) print 'class="mistake"'; ?>
                           id ="txtLastName"
                           maxlength="45"
                           name="txtLastName"
                           onfocus="this.select()"
                           placeholder="Enter your last name"
                           tabindex="100"
                           type="text"
                           value='<?php print $lastName; ?>'
                           >    


                    <input class="form-control middle" class = "required" for = "txtEmail"
                    <?php if ($emailERROR) print 'class="mistake"'; ?>
                           id ="txtEmail"
                           maxlength="45"
                           name="txtEmail"
                           onfocus="this.select()"
                           placeholder="Enter your email address"
                           tabindex="100"
                           type="text"
                           value='<?php print $email; ?>'
                           >    

                </fieldset> <!--ends contact -->
                <br>
                <fieldset>
                    <legend>Recipe Information</legend>


                    <input class="form-control middle" for = "txtRecipeName"
                    <?php if ($recipeNameERROR) print 'class="mistake"'; ?>
                           id ="txtRecipeName"
                           maxlength="45"
                           name="txtRecipeName"
                           onfocus="this.select()"
                           placeholder="Enter the name of the recipe"
                           tabindex="100"
                           type="text"
                           value='<?php print $recipeName; ?>'
                           >    

                </fieldset>
                <br>

                <!--list of cities to travel to-->
                <fieldset  class="listbox <?php if ($regionERROR) print ' mistake'; ?>">
                    <legend>Region the recipe originates from</legend>
                    <p>

                        <select id="lstRegion" 
                                name="lstRegion" 
                                tabindex="520" >
                            <option <?php if ($region == "North America") print " selected "; ?>
                                value="North America">North America</option>
                            <option <?php if ($region == "Asia") print " selected "; ?>
                                value="Asia">Asia</option>
                            <option <?php if ($region == "South America") print " selected "; ?>
                                value="South America">South America</option>
                            <option <?php if ($region == "Europe") print " selected "; ?>
                                value="Europe">Europe</option>
                            <option <?php if ($region == "Australia") print " selected "; ?>
                                value="Australia">Australia</option>
                            <option <?php if ($region == "Africa") print " selected "; ?>
                                value="Africa">Africa</option>
                            <option <?php if ($region == "Antarctica") print " selected "; ?>
                                value="Antarctica">Antarctica</option>

                        </select>
                    </p>
                </fieldset>
                <br>
                <!--begin time section-->
                <fieldset class="text">
                    <legend>How Long Will It Take?</legend>


                    <input class="form-control middle"  for="txtPrepTime"
                    <?php if ($prepTimeERROR) print 'class="mistake"'; ?>
                           id="txtPrepTime" 
                           maxlength="45"
                           name="txtPrepTime" 
                           onfocus="this.select()"
                           placeholder="Enter Prep Time"
                           tabindex="100"
                           type="text"
                           value='<?php print $prepTime; ?>'                    
                           >



                    <input class="form-control middle" for="txtCookTime"
                    <?php if ($cookTimeERROR) print 'class="mistake"'; ?>
                           id="txtCookTime" 
                           maxlength="45"
                           name="txtCookTime" 
                           onfocus="this.select()"
                           placeholder="Enter Cook Time"
                           tabindex="100"
                           type="text"
                           value='<?php print $cookTime; ?>'                    
                           >

                </fieldset>
                <!--ends time section-->

                <br>

                <!--starts event section-->
                <fieldset class="radio-field <?php if ($flavorERROR) print ' mistake'; ?>">
                    <legend>What flavor does the recipe have?</legend>
                    <p>    
                        <label class="radio-field"><input type="radio" id="radFlavorSweet" name="radFlavor" value="Sweet" tabindex="572" 
                                                          <?php if ($flavor == "Sweet") echo ' checked="checked" '; ?>>Sweet</label>
                    </p>
                    <p>
                        <label class="radio-field"><input type="radio" id="radFlavorSpicy" name="radFlavor" value="Spicy" tabindex="574" 
                                                          <?php if ($flavor == "Spicy") echo ' checked="checked" '; ?>>Spicy</label>
                    </p>
                    <p>
                        <label class="radio-field"><input type="radio" id="radFlavorSour" name="radFlavor" value="Sour" tabindex="574" 
                                                          <?php if ($flavor == "Sour") echo ' checked="checked" '; ?>>Sour</label>
                    </p>
                    <p>
                        <label class="radio-field"><input type="radio" id="radFlavorSalty" name="radFlavor" value="Salty" tabindex="574" 
                                                          <?php if ($flavor == "Salty") echo ' checked="checked" '; ?>>Salty</label>
                    </p>
                    <p>
                        <label class="radio-field"><input type="radio" id="radFlavorOther" name="radFlavor" value="Other" tabindex="574" 
                                                          <?php if ($flavor == "Other") echo ' checked="checked" '; ?>>Other (Please leave a comment in the comment box)</label>
                    </p>

                    <p>
                        <label class="required" for="txtComments">Description of the type of event if other is selected.</label>
                        <textarea <?php if ($commentsERROR) print 'class="mistake"'; ?>
                            id="txtComments" 
                            name="txtComments" 
                            onfocus="this.select()" 
                            tabindex="200"><?php print $comments; ?></textarea>
                        <!-- NOTE: no blank spaces inside the text area, be sure to close 
                                   the text area directly -->
                    </p>
                </fieldset>

                <fieldset>
                    <legend>Recipe</legend>
                    <p>
                        <label class="required" for="txtRecipe">Instructions and Measurements</label>
                        <textarea <?php if ($recipeERROR) print 'class="mistake"'; ?>
                            id="txtRecipe" 
                            name="txtRecipe" 
                            onfocus="this.select()" 
                            tabindex="200"><?php print $recipe; ?></textarea>
                        <!-- NOTE: no blank spaces inside the text area, be sure to close 
                                   the text area directly -->
                    </p>
                </fieldset>

                <fieldset>
                    <?php
                    $query = 'SELECT pmkMaterials FROM tblMaterials';

                    //$thisDatabaseReader->testSecurityQuery($query, 0);
                    if ($thisDatabaseReader->querySecurityOk($query, 0)) {
                        $query = $thisDatabaseReader->sanitizeQuery($query);
                        $Materials = $thisDatabaseReader->select($query, '');
                    }

                    print '<legend class="fieldsetter">Materials Needed:</legend>';
                    if ($materialsERROR) {
                        print ' class="mistake"';
                    }
                    print '<p>';
                    if (is_array($Materials)) {
                        foreach ($Materials as $material) {
                            print '<label for="chk' . str_replace(" ", "", $material["pmkMaterials"]) . '"';

                            print '>';

                            print '<input type="checkbox" id="chk' . str_replace(" ", "", $material["pmkMaterials"]) . '" name="chkArrays[]" ';
                            if (in_array($material["pmkMaterials"], $MaterialSelected)) {
                            print ' checked ';
                        }
                            print 'value=' . $material["pmkMaterials"];


                            print '>' . $material["pmkMaterials"];
                            print '</label>';
                            print '<br>';
                        }
                    }
                    print '</p>';
                    ?>
                </fieldset>
                <!--ends comment section-->


                

                <?php
            } // ends data entered
            ?>
        </form>
    </article>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
