<?php

    session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("config.php");
$ip = $_SERVER['REMOTE_ADDR'];
if (empty($_SESSION)) {
htmlGetBack("Anouthorized users cannot view this page","index.php" ,"Go back" );
logError("$ip tried to access this page without authorizing");
exit;
}
$username = $_SESSION['username'];
$sesid = $_SESSION['id'];
        if(isset($_POST['submit']))
        {
	if (empty($_POST['newname']) or empty($_POST['password'])) {
	$prob = "You did not fill all areas";
	$link = "username_ch.php";
	$message = "Go back";
	htmlGetBack($prob, $link, $message);
	logError("$username ($sesid) bypassed HTML and submitted empty input");
	}
	else {
    $password = $_POST['password'];
    $newname = $_POST['newname'];
        $conn;

        $result = mysqli_query($conn, "SELECT * FROM users WHERE pswd = '$password' AND username = '$username'");
        $row = mysqli_fetch_assoc($result);
	$result2 = mysqli_query($conn, "SELECT * FROM users WHERE username = '$newname'");
        $row2 = mysqli_fetch_assoc($result2);
        if ($row['id'] != $_SESSION['id']) {
		
        	$prob = "incorrect credentials";
        	$link = "username_ch.php";
        	$message = "try again";
        	htmlGetBack($prob, $link, $message);
		logError("$username ($sesid) submitted incorrect credentials while changing username");
        }
	else if ($row2['id'] != 0) {
		$prob = "new username exists";
        	$link = "username_ch.php";
        	$message = "try again";
        	htmlGetBack($prob, $link, $message);
		logError("$username ($sesid) tried to change his username to an existing one: $newname");
	}
	else {
        mysqli_query($conn, "UPDATE users SET username = '$newname' where username = '$username'");         
        print( "Changed successfully");
	$_SESSION['username'] = $newname;
	}
	}
    } else { 

       

  
       print("

        <html>
        <title> Location of Pollution </title>
        <meta charset='utf-8'>
               <center>
        <div>
	<h4> Username Changing </h4>
        <form action='' method='POST'>
        <table border='1px' cellpadding='5' >
        <tr><td>Password :</td><td> <input type='password' name='password' required></td> </tr>
        <tr><td> New Name:</td><td> <input type='text' name='newname' required> </td></tr>
        <tr><td colspan='2' align='center'> <input type='submit' name='submit' size = '40' value='change'> </td></tr>
	</table>
	</center>
	</div>
        </html>
       
        ");

      
	

      }

  htmlGetBack("", "userOffice.php", "Return");



?>