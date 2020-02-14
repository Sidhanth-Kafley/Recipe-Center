<!DOCTYPE html>
<html lang="en">
    <head>
        <title>CS148 Final Project</title>
        <meta charset="utf-8">
        <meta name="author" content="Sydney Hildreth，Sidhanth Kafley，Zecheng Jin">
        <meta name="description" content="This is a website for viewing and adding new recipes. 
              You can create an account and add new recipes.">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!--[if lt IE 9]>
        <script src="//html5shim.googlecode.com/sin/trunk/html5.js"></script>
        <![endif]-->

        <link rel="stylesheet" href="css/final.css" type="text/css" media="screen">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <?php
        // %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
        //
        // inlcude all libraries. 
        // 
        // %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
        print '<!-- begin including libraries -->';
        
        include 'lib/constants.php';
        
        include LIB_PATH . '/Connect-With-Database.php';
        
        include 'lib/security.php';
        include 'lib/validation-functions.php';
        include 'lib/mail-message.php';
    
        $user = htmlentities($_SERVER["REMOTE_USER"], ENT_QUOTES, "UTF-8");
        $IS_ADMIN = in_array($user, $ADMIN);

        

        
        print '<!-- libraries complete-->';
        ?>	

    </head>

    <!-- **********************     Body section      ********************** -->
    <?php
    print '<body id="' . $PATH_PARTS['filename'] . '">';
    include 'header.php';
    include 'nav.php';
    ?>
