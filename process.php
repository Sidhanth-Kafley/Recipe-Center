<?php
//Get values pass from the login form in form2.php file
$email = $_POST['txtEmail'];
$password = $_POST['txtPassword'];
//to prevent mysql injection
$email = stripcslashes($email);
$password = stripcslashes($email);
$email = mysql_real_escape_string($email);
$password = mysql_real_escape_string($password);

//connect to the server and select database
mysql_select_db("login");

?>
