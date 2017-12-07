<?php
session_start();

//only works if the payload has an operation variable.
if(isset($_POST['operation'])) {
	$operation = $_POST['operation'];
	echo json_encode(array("results"=>process($operation)));
}


//Processes the sent input and basically
//distributes the work to the desired method accordingly.
function process($operation) {
	$result = false;
	switch ($operation) {
		case 'viewTT':
			$result = viewTimeTable();	
			break;
		
		case 'login':
			$result = doLogin();
			break;

		case 'uploadTT':
			$result = uploadTT();
			break;

		case 'logout':
			$result = doLogout();
			break;

		case 'isLoggedIn':
			$result = isLoggedIn();
			break;

		case 'doRegister':
			$result = doRegister();
			break;
		
		case 'getFullNameFromSession':
			$result = getFullNameFromSession();
			break;

		case 'fetchpending':
			$result = fetchPendingNotifications();
			break;

		case 'confirmApprovalForApplier':
			$result = confirmApprovalForApplier();
			break;

		case 'rejectApprovalForApplier':
			$result = rejectApprovalForApplier(null);
			break;

		case 'isAdmin':
			$result = isAdmin(false);
			break;

		default:
			break;
	}
	return $result;
}


/**
 * View operation when trying to view a time table
 */
function viewTimeTable() {
	$course = $_POST['course'];
	$year = "";
	if($course == "btech") {
		$year = $_POST["year_btech"];

	} else if($course == "barch") {
		$year = $_POST["year_barch"];
	} 

	if($year == "") {
		return '{"status": "none"}';
	}

	$keycode = $course . "" . $year;
	$fetchSQL = "SELECT schedule FROM timetable WHERE course='$keycode'";
	$link = getDBConnection();
	if (!$link) {
    	return '{"status": "none"}';
	}
	mysqli_select_db($link, 'timetableci');
	
	$result = mysqli_query($link, $fetchSQL);
	if(mysqli_num_rows($result) != 0) {
		$obj = (object) [];
		$i=0;
		while($row = mysqli_fetch_assoc($result)) {
			$obj->$i = $row['schedule'];
			$i = $i + 1;
		}
		closeDBConnection($link);
		$ss = "status";
		$obj->$ss = "ok";
		dumpLogs("TT view = success", $keycode);
		return json_encode($obj);
	}

	dumpLogs("TT view = fail", $keycode);
	closeDBConnection($link);
	return '{"status": "none"}';
}


/**
 * Checks if the user has appropriate permissions or not
 * by checing the data base.
 */
function doLogin() {
	$link = getDBConnection();
	$uname = $_POST["uname"];
	$pwd = $_POST["pwd"];
	$uname = mysqli_real_escape_string($link, $uname);
	$pwd = mysqli_real_escape_string($link, $pwd);
	dumpLogs("LOGIN_ATTEMPT", "ATTEMPT_START", "username=".$uname, "password=".$pwd);
	$loginSql = "SELECT * from users WHERE username='$uname' AND password='$pwd'";
	if(!$link) {
		return '{"status": "none"}';
	}		
	//select the db
	if (!mysqli_select_db($link, 'timetableci')) {
	    return '{"status": "none"}';
	}	

	$res = mysqli_query($link, $loginSql);
	$rowsresult = mysqli_num_rows($res);
	if($rowsresult==0 || $rowsresult > 1) {
		dumpLogs("LOGIN_ATTEMPT", "ATTEMPT_REJECT", "username=".$uname, "password=".$pwd);
		return '{"status": "none"}';
	}
	$row = mysqli_fetch_assoc($res);
	$firstName = $row["firstname"];
	$lastName = $row["lastname"];
	$rights = $row["permission"];
	$uid = $row["uid"];
	$status = "status";
	//Store all of it in the session.
	$_SESSION['user_session'] = $uid;
	$_SESSION['fname'] = $firstName;
	$_SESSION['lname'] = $lastName;
	$_SESSION['level'] = $rights;
	dumpLogs("LOGIN_ATTEMPT", "ATTEMPT_SUCC", "username=".$uname, "password=".$pwd);
	$fn="firstName";
	$ln="lastName";
	$u="uid";
	$obj = (object) [];
	$obj->$fn = $firstName;
	$obj->$ln = $lastName;
	$obj->$u = $uid;
	$obj->$status = "ok";
	closeDBConnection($link);
	return json_encode($obj);

}


function doLogout() {
	if(session_destroy()) {
		dumpLogs("LOGOUT_ATTEMPT", "ATTEMPT_SUCC", "uid=".$_SESSION['user_session'], "rights=".$_SESSION['level']);
		return '{"status": "ok"}';
	}

	dumpLogs("LOGOUT_ATTEMPT", "ATTEMPT_REJECT", "uid=".$_SESSION['user_session'], "rights=".$_SESSION['level']);
	return '{"status": "none"}';
}


function isLoggedIn() {
	if(isset($_SESSION['user_session'])) {
		return '{"status": "ok"}';
	}
	return '{"status": "none"}';
}


function isAdmin($returnBool) {
	$result = '{"status": "none"}';
	if(isset($_SESSION['user_session']) && isset($_SESSION["level"]) && $_SESSION["level"]=="root") {
		dumpLogs("Admin check", "uid=".$_SESSION['user_session'], "rights=".$_SESSION['level']);
		if($returnBool) {
			$result = true;
		} else {
			$result = '{"status": "ok"}';
		}
	} else {
		if($returnBool == true) {
			$result = false;
		}
	}
	return $result;
}


/**
 * Tries to update the DB table with the new time table.
 * If the entry is not there already, then creates a new entry 
 * and does and INSERT to the table. If it exists, then does an
 * UPDATE to the table and overrwirtes it (the day row).
 */
function uploadTT() {
	$link = getDBConnection();
	if (!$link) {
    	return '{"connect": 0}';
	}

	if (!mysqli_select_db($link, 'timetableci')) {
	    return '{"connect": 0}';
	}	
	$baseData = json_decode($_POST["data"]);
	$course = $baseData->{"course"};
	$day = $baseData->{"day"};
	$schedule = $baseData->{"periodsData"};
	dumpLogs("Insert-Overwrite Time table attempt", "couse=".$course, "day=".$day, "schedule=".$schedule);

	$sql = "SELECT schedule FROM timetable WHERE course='$course' and day='$day'";
	$res = mysqli_query($link, $sql);
	$result = (!$res || (mysqli_num_rows($res))==0)?0:(mysqli_num_rows($res));

	// return $result."-";

	//check if a valid result was obtained
	//if not, then insert, else overwrite
	if($result==0) {
		//also insert permissions are only set to 
		//the admins, check if this guy is an admin.
		//If yes then proceed with insert, else with revoke.
		if(isAdmin(true)==true) {
			$inssql = "INSERT into timetable (course, day, 	schedule) VALUES ('$course', '$day', '$schedule')";

			$result = mysqli_query($link, $inssql);
			if(!$result) {
				closeDBConnection($link);
				return '{"connect": -1}';
			} else {
				dumpLogs("Upload Time table success", "level=".$_SESSION["level"], "couse=".$course, "day=".$day, "schedule=".$schedule);
				closeDBConnection($link);
				return '{"connect": 1}';
			}
		} else {
			dumpLogs("Upload Time table failed", "level=".$_SESSION["level"], "couse=".$course, "day=".$day, "schedule=".$schedule);
			closeDBConnection($link);
			return '{"connect": 3}';
		}
	} 
	else {
		//update the table
		$updsql = "UPDATE timetable SET schedule = '$schedule' WHERE course = '$course' and day = '$day'";
		$result = mysqli_query($link, $updsql);
		if(!$result) {
			closeDBConnection($link);
			return '{"connect": -2}';
		} else {
			dumpLogs("overwrite Time table success", "uid=".$_SESSION["user_session"], "level=".$_SESSION["level"], "couse=".$course, "day=".$day, "schedule=".$schedule);
			closeDBConnection($link);
			return '{"connect": 2}';
		}
	}


	closeDBConnection($link);
	return '{"connect": 3}';
}

function getDBConnection() {
	dumpLogs("DB-ACCESS_ATTEMPT");
	$link = mysqli_connect('localhost', 'test', 'test');
	return $link;
}

function closeDBConnection($link) {
	if($link) {
		dumpLogs("DB-ACCESS_CLOSE");
		mysqli_close($link);
	}
}


/**
 * Attempts to get the full name from session data
 */
function getFullNameFromSession() {
	if(isset($_SESSION['user_session'])) {
		$fname = $_SESSION['fname'];
		$lname = $_SESSION['lname'];
		return $fname." ".$lname;
	}
}


/**
 * Attempts to log a register attempt to the registration db
 */
function doRegister() {

	$link = getDBConnection();
	if (!$link) {
    	return '{"connect": 0}';
	}

	if (!mysqli_select_db($link, 'timetableci')) {
	    return '{"connect": 0}';
	}	

	$username = $_POST["uname"];
	$password = $_POST["pwd"];
	$firstname = $_POST["fname"];
	$lastname = $_POST["lname"];

	dumpLogs("USER_REGISTER_ATTEMP", "START_ATTEMPT", "username=".$username, "password=".$password, 'firstname='.$firstname, "lastName=".$lastname);

	//we can only register if the user is not there in
	//the users table.
	$checkuserSQL = "SELECT * FROM users WHERE username='$username'";
	$res = mysqli_query($link, $checkuserSQL);
	$result = (!$res || (mysqli_num_rows($res))==0)?0:(mysqli_num_rows($res));
	
	//if there waas no existing user, then we can go ahead and update or overwrite
	//in the registrations database.
	if($result==0) {
		dumpLogs("USER_REGISTER_ATTEMP", "START_NOT_IN_USERS", "username=".$username, "password=".$password, 'firstname='.$firstname, "lastName=".$lastname);
		//check if this user already has an entry in the registrations table
		$chkRedg = "SELECT * FROM registrations WHERE username='$username' and password='$password'";
		$res = mysqli_query($link, $chkRedg);
		$result = (!$res || (mysqli_num_rows($res))==0)?0:(mysqli_num_rows($res));
		//if not existing then insert
		if($result==0) {
			dumpLogs("USER_REGISTER_ATTEMP", "START_NOT_IN_REGISTERPDN", "username=".$username, "password=".$password, 'firstname='.$firstname, "lastName=".$lastname);
			$insSQL = "INSERT into registrations (username, password, firstname, lastname) 
						VALUES ('$username', '$password', '$firstname', '$lastname')";
			$result = mysqli_query($link, $insSQL);
			if(!$result) {
				dumpLogs("USER_REGISTER_ATTEMP", "ATTEMPT_REJECT", "username=".$username, "password=".$password, 'firstname='.$firstname, "lastName=".$lastname);
				closeDBConnection($link);
				return '{"status": "rejected"}';
			} else {
				dumpLogs("USER_REGISTER_ATTEMP", "ATTEMPT_SUCC", "username=".$username, "password=".$password, 'firstname='.$firstname, "lastName=".$lastname);
				closeDBConnection($link);
				return '{"status": "success"}';
			}
		} else {
			dumpLogs("USER_REGISTER_ATTEMP", "ATTEMPT_IN_PENDING", "username=".$username, "password=".$password, 'firstname='.$firstname, "lastName=".$lastname);
			closeDBConnection($link);
			return '{"status": "confirmpending"}';
		}

	} 
	else {
		dumpLogs("USER_REGISTER_ATTEMP", "USER_ALREADY_REGISTERED", "username=".$username, "password=".$password, 'firstname='.$firstname, "lastName=".$lastname);
		//return status as existing user
		closeDBConnection($link);
		return '{"status": "existing"}';
	}


	closeDBConnection($link);
	return '{"connect": 3}';




	return '{"status": "ok"}';
}



/**
 * Fethces the pending users who have not been approved by the
 * admin for an access.
 */
function fetchPendingNotifications() {
	$link = getDBConnection();
	if (!$link) {
    	return '{"connect": 0}';
	}

	if (!mysqli_select_db($link, 'timetableci')) {
	    return '{"connect": 0}';
	}	

	dumpLogs("PENDING_REQ_FETCH", "uid=".$_SESSION["user_session"], "rights=".$_SESSION["level"]);

	//we can only register if the user is not there in
	//the users table.
	$checkuserSQL = "SELECT * FROM registrations";
	$res = mysqli_query($link, $checkuserSQL);
	$result = (!$res || (mysqli_num_rows($res))==0)?0:(mysqli_num_rows($res));
	$obj = (object) [];
	$ss = "status";
	if($result==0) {
		$obj->$ss = "none";
	} 
	else {
		$usname = "username";
		$firstname = "firstname";
		$lastname = "lastname";
		
		$i=0;
		while($row = mysqli_fetch_assoc($res)) {
			$obj->$i = new \stdClass(); //create empty container and start pushing data in this
			$obj->$i->$usname = $row[$usname];
			$obj->$i->$firstname = $row[$firstname];
			$obj->$i->$lastname = $row[$lastname];
			$i = $i + 1;
		}
		$obj->$ss = "ok";
		$len = "length";
		$obj->$len = $i;
	}
	closeDBConnection($link);
	return json_encode($obj);
}



/**
 * Attempts to confirm a user who was in the pending section of
 * the registrations database.
 */
function confirmApprovalForApplier() {
	$link = getDBConnection();
	if (!$link) {
    	return '{"connect": 0}';
	}

	if (!mysqli_select_db($link, 'timetableci')) {
	    return '{"connect": 0}';
	}	


	dumpLogs("REDG_CONF_ATTEMPT", "START_ATTEMPT", "uid=".$_SESSION["user_session"], "rights=".$_SESSION["level"]);

	//first check if this user is in the registration list or not
	//if yes, then transfer over to the users list with mod permission
	//else skip quietly on the page.
	$username = $_POST["uname"];
	$checkuserSQL = "SELECT * from registrations WHERE username='$username'";
	$res = mysqli_query($link, $checkuserSQL);
	$result = (!$res || (mysqli_num_rows($res))==0)?0:(mysqli_num_rows($res));

	$obj = (object) [];
	$status = "status";
	//transfer the content data to the users table
	$usname = "username";
	$firstname = "firstname";
	$lastname = "lastname";
	$password = "password";
	$perm = "moderator";

	if($result == 0 || $result > 1) {
		dumpLogs("REDG_CONF_ATTEMPT", "ATTEMPT_FAIL_NOT_IN_REDGLIST", "uid=".$_SESSION["user_session"], "rights=".$_SESSION["level"]);
		//simply ignore
		$obj->$status = "ok";
	} else {
		$row = mysqli_fetch_assoc($res);
		//also check if this mail id is already present in the registered user
		$csql = "SELECT * from users WHERE username='$row[$usname]'";
		$cres = mysqli_query($link, $csql);
		$cresult = (!$cres || (mysqli_num_rows($cres))==0)?0:(mysqli_num_rows($cres));

		if($cresult == 0) {
			$uniqueUID = generateUniqueUserId($link);
			$insertUserSQL = "INSERT into users (uid, username, password, firstname, lastname, permission) VALUES ('$uniqueUID', '$row[$usname]', '$row[$password]', '$row[$firstname]', '$row[$lastname]', '$perm')";
			$insres = mysqli_query($link, $insertUserSQL);

			if(!$insres) {
				$obj->$status = "noins";
				dumpLogs("REDG_CONF_ATTEMPT", "DB_WRITE_FAILED", "uid=".$_SESSION["user_session"], "rights=".$_SESSION["level"]);
			} else {
				dumpLogs("REDG_CONF_ATTEMPT", "DB_WRITE_PASS", "confirmed_uid=".$uniqueUID, "uid=".$_SESSION["user_session"], "rights=".$_SESSION["level"]);
				//remove from the registrations table
				$delsql = "DELETE FROM registrations WHERE username='$username'";
				$delres = mysqli_query($link, $delsql);
				$obj->$status = "ok";
			}

		} else {
			dumpLogs("REDG_CONF_ATTEMPT", "ATTEMPT_FAIL_ALREADY_USER", "uid=".$_SESSION["user_session"], "rights=".$_SESSION["level"]);
			//simply ignore
			$obj->$status = "ok";
		}


	}
	closeDBConnection($link);
	return json_encode($obj);
}



/**
 * Generates a unique UID for a user when entering
 * into the main user table. Uses Mersenne Twister algorithm
 * to generate fast numbers- but not cryptographically secure.
 */
function generateUniqueUserId($link) {
	
	$uidSQL = "select uid from users";
	$res = mysqli_query($link, $uidSQL);
	$uids = array();
	while($row = mysqli_fetch_assoc($res)) {
		array_push($uids, $row['uid']);
	}

	$valueToUse = -1;
	while(1) {
		$newrand = mt_rand(1, 74969);
		if(!in_array($newrand, $uids, true)) {
			$valueToUse = $newrand;
			break;
		}
	}

	return $valueToUse;

}


/**
 * When a user is rejected the permission to be a user
 * (ideally by the admin).
 */
function rejectApprovalForApplier($link) {
	$sent = 0;
	if(!$link) {
		//then get the connection
		$link = getDBConnection();
		if (!$link) {
	    	return '{"connect": 0}';
		}

		if (!mysqli_select_db($link, 'timetableci')) {
		    return '{"connect": 0}';
		}	
	} else {
		$sent = 1;
	}

	dumpLogs("REDG_REJ_ATTEMPT", "START_ATTEMPT", "uid=".$_SESSION["user_session"], "rights=".$_SESSION["level"]);


	$obj = (object) [];
	$status = "status";
	//transfer the content data to the users table
	$usname = "username";
	$firstname = "firstname";
	$lastname = "lastname";
	$password = "password";
	$perm = "moderator";


	//remove from the registrations table
	$uname = $_POST["uname"];
	$delsql = "DELETE FROM registrations WHERE username='$uname'";
	$delres = mysqli_query($link, $delsql);
	$statadata = "";
	if(!$delres) {		
		$statadata = "nodel";
		dumpLogs("REDG_REJ_ATTEMPT", "DB_WRITE_FAILED", "uid=".$_SESSION["user_session"], "rights=".$_SESSION["level"]);
	} else {
		$statadata = "ok";
		dumpLogs("REDG_REJ_ATTEMPT", "DB_WRITE_PASS", "uid=".$_SESSION["user_session"], "rights=".$_SESSION["level"]);
	}


	$obj->$status = $statadata;
	if($sent == 0) {
		closeDBConnection($link);
	}
	return json_encode($obj);
}



function dumpLogs(...$params) {
	$eachLogParam = 0;
	$write = false;
	//first log the user's ip addr and the time stamp
	$log = "User: ".$_SERVER['REMOTE_ADDR'].' - '.date("F j, Y, g:i a").PHP_EOL;
	
	//then start appending
	while($eachLogParam < sizeof($params)) {
		$log = $log . (string)$params[$eachLogParam] . PHP_EOL;
		$eachLogParam++;
		$write = true;
	}

	//if we did write to the log variable then dump (we dont want unnecessary dumps)
	if($write == true) {
		//marks end of the log
		$log = $log . "------------------------------------------".PHP_EOL.PHP_EOL; 
		//log is put in a file which is in date format named, giving sectional data insights. Hope this helps for the time table scheduler. :)
		file_put_contents('./logs/log_'.date("j.n.Y").'.txt', $log, FILE_APPEND);
	}
}

?>
