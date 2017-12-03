<?php 
?>



<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/register.css">
	<link rel="stylesheet" type="text/css" href="css/styes.css">
	<script language="JavaScript" type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="js.js"></script>
	<title>Register User</title>
</head>
<body>

<p class="texto">Register User</p>
<div class="Registro">
<form method="post">
	<span class="fontawesome-envelope-alt"></span>
	<input type="text" name="fname" id="fname" required placeholder="First Name" autocomplete="off"> 
	<input type="text" name="lname" id="lname" required placeholder="Last Name" autocomplete="off">
	<input type="text" name="mailid" id="regemail" required placeholder="Institute Mail Id" autocomplete="off">
	<span class="fontawesome-lock"></span>
	<input type="password" name="password" id="password" required placeholder="password" autocomplete="off">  
	<input type="button" value="Submit for validation" id="registersubmit" onclick="doRegister(this.form)">
</form>

<!-- The actual snackbar -->
<div id="cnf-toaster">Thank you for registering <br/>The Webmaster will confirm shortly</div>

</body>
</html>