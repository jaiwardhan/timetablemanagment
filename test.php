<?php

	function getDBConnection() {
		$link = mysqli_connect('127.0.0.1', 'test', 'test');
		return $link;
	}

	function closeDBConnection($link) {
		if($link) {
			mysqli_close($link);
		}
	}

	// $uname = 'jai@g.com';
	// $pwd = 'root';
	// $loginSql = "SELECT * from users WHERE username='$uname' AND password='$pwd'";
	// $link = getDBConnection();
	// if(!link) {
	// 	echo("not connected".PHP_EOL);
	// }		
	// //select the db
	// if (!mysql_select_db('timetableci', $link)) {
	//     echo("not able to select db".PHP_EOL);
	// }	

	// $res = mysql_query($loginSql, $link);
	// $rowsresult = mysql_num_rows($res);
	// if($rowsresult==0 || $rowsresult > 1) {
	// 	echo("not found".PHP_EOL);
	// } else {
	// 	echo("YES found".PHP_EOL);
	// }
	// $row = mysql_fetch_assoc($res);
	// echo($row["firstname"].PHP_EOL);
	// echo($row["lastname"].PHP_EOL);
	// echo($row["uid"].PHP_EOL);



	// $link = getDBConnection();
	// $uname = "jai@g.com";
	// $pwd = "root";
	// $loginSql = "SELECT * from users WHERE username='$uname' AND password='$pwd'";
	// if(!$link) {
	// 	return '{"status": "none"}';
	// }		
	// //select the db
	// if (!mysql_select_db('timetableci', $link)) {
	//     return '{"status": "none"}';
	// }	

	// $res = mysql_query($loginSql, $link);
	// $rowsresult = mysql_num_rows($res);
	// if($rowsresult==0 || $rowsresult > 1) {
	// 	return '{"status": "none"}';
	// }
	// $row = mysql_fetch_assoc($res);
	// $firstName = $row["firstname"];
	// $lastName = $row["lastname"];
	// $uid = $row["uid"];
	// $status = "status";
	// $_SESSION['user_session'] = $row['uid'];
	// session_start();
	// $fn="firstName";
	// $ln="lastName";
	// $u="uid";
	// $obj = (object) [];
	// $obj->$fn = $firstName;
	// $obj->$ln = $lastName;
	// $obj->$u = $uid;
	// $obj->$status = "ok";
	// echo(json_encode($obj));

	// return json_encode($obj);

	/**
 * Checks if the user has appropriate permissions or not
 * by checing the data base.
 */
function doLogin() {
	$link = getDBConnection();
	$uname = 'jai@g.com';
	$pwd = 'root';
	$uname = mysqli_real_escape_string($link, $uname);
	$pwd = mysqli_real_escape_string($link, $pwd);
	$loginSql = "SELECT * from users WHERE username='$uname' AND password='$pwd'";
	if(!$link) {
		 tell('{"status": "none"}');
	}		
	//select the db
	if (!mysqli_select_db($link, 'timetableci')) {
	    tell('{"status": "none"}');
	}	

	$res = mysqli_query($link, $loginSql);
	$rowsresult = mysqli_num_rows($res);
	if($rowsresult==0 || $rowsresult > 1) {
		tell( '{"status": "none"}');
	}
	$row = mysqli_fetch_assoc($res);
	$firstName = $row["firstname"];
	$lastName = $row["lastname"];
	$uid = $row["uid"];
	$status = "status";
	//Store all of it in the session.
	$_SESSION['user_session'] = $uid;
	$_SESSION['fname'] = $firstName;
	$_SESSION['lname'] = $lastName;
	$fn="firstName";
	$ln="lastName";
	$u="uid";
	$obj = (object) [];
	$obj->$fn = $firstName;
	$obj->$ln = $lastName;
	$obj->$u = $uid;
	$obj->$status = "ok";
	closeDBConnection($link);
	tell(json_encode($obj));

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
    	tell( '{"connect": 0}');
	}

	if (!mysqli_select_db($link, 'timetableci')) {
	    tell( '{"connect": 0}');
	}	
	$baseData = json_decode('{"course":"btech4","day":"MON","periodsData":"[\"ABC\",\"ABC\",\"ABC\",\"ABC\",\"ABC\",\"ABC\",\"ABC\",\"ABC\"]"}');
	$course = $baseData->{"course"};
	$day = $baseData->{"day"};
	$schedule = $baseData->{"periodsData"};
	tell('day is: '.$day);
	tell('course is: '.$course);
	tell('sch is: '.$schedule);

	//$sql = "SELECT count(*) as c FROM timetable WHERE course='$course' AND day='$day'";

	$sql = "SELECT schedule FROM timetable WHERE course='$course' and day='$day'";
	$res = mysqli_query($link, $sql);
	$result = (!$res || (mysqli_num_rows($res))==0)?0:(mysqli_num_rows($res));
	tell("rows found: ".$result);

	// return $result."-";

	//check if a valid result was obtained
	//if not, then insert, else overwrite
	if($result==0) {
		$inssql = "INSERT into timetable (course, day, 	schedule) VALUES ('$course', '$day', '$schedule')";

		$result = mysqli_query($link, $inssql);
		if(!$result) {
			closeDBConnection($link);
			tell( '{"connect": -1}');
		} else {
			closeDBConnection($link);
			tell( '{"connect": 1}');
		}
	} 
	else {
		//update the table
		$updsql = "UPDATE timetable SET schedule = '$schedule' WHERE course = '$course' and day = '$day'";
		$result = mysqli_query($link, $updsql);
		if(!$result) {
			closeDBConnection($link);
			tell( '{"connect": -2}');
		} else {
			closeDBConnection($link);
			tell( '{"connect": 2}');
		}
	}


	// closeDBConnection($link);
	// tell( '{"connect": 3}');
}


function doRegister() {

	$link = getDBConnection();
	if (!$link) {
    	return '{"connect": 0}';
	}

	if (!mysqli_select_db($link, 'timetableci')) {
	    return '{"connect": 0}';
	}	
	// $baseData = json_decode($_POST["data"]);
	$username = "jai@g.com";
	$password = "root";
	$firstname = "jai";
	$lastname = "swar";

	//we can only register if the user is not there in
	//the users table.
	$checkuserSQL = "SELECT * FROM users WHERE username='$username'";
	$res = mysqli_query($link, $checkuserSQL);
	$result = (!$res || (mysqli_num_rows($res))==0)?0:(mysqli_num_rows($res));
	//if there waas no existing user, then we can go ahead and update or overwrite
	//in the registrations database.
	if($result==0) {
		//check if this user already has an entry in the registrations table
		$chkRedg = "SELECT * FROM registrations WHERE username='$username' and password='$password'";
		$res = mysqli_query($link, $chkRedg);
		$result = (!$res || (mysqli_num_rows($res))==0)?0:(mysqli_num_rows($res));
		//if not existing then insert
		if($result==0) {
			$insSQL = "INSERT into registrations (username, password, firstname, lastname) 
						VALUES ('$username', '$password', '$firstname', '$lastname')";
			$result = mysqli_query($link, $insSQL);
			if(!$result) {
				closeDBConnection($link);
				tell('{"status": "rejected"}');
			} else {
				closeDBConnection($link);
				tell('{"status": "success"}');
			}
		} else {
			closeDBConnection($link);
			tell('{"status": "confirmpending"}');
		}

	} 
	else {
		//return status as existing user
		closeDBConnection($link);
		tell('{"status": "existing"}');
	}


	// closeDBConnection($link);
	// tell('{"connect": 3}');




	return '{"status": "ok"}';
}


function fetchPendingNotifications() {
	$link = getDBConnection();
	if (!$link) {
    	return '{"connect": 0}';
	}

	if (!mysqli_select_db($link, 'timetableci')) {
	    return '{"connect": 0}';
	}	

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
	tell(json_encode($obj));
}


function generateUniqueUserId() {
	$link = getDBConnection();
	if (!$link) {
    	return '{"connect": 0}';
	}

	if (!mysqli_select_db($link, 'timetableci')) {
	    return '{"connect": 0}';
	}	

	
	$uidSQL = "select uid from users";
	$res = mysqli_query($link, $uidSQL);
	$uids = array();
	while($row = mysqli_fetch_assoc($res)) {
		tell("pushing:".$row['uid']);
		array_push($uids, $row['uid']);
	}

	$valueToUse = -1;
	while(1) {
		$newrand = mt_rand(1, 74969);
		tell("calculated: ".$newrand);
		if(!in_array($newrand, $uids, true)) {
			$valueToUse = $newrand;
			break;
		}
	}

	return $valueToUse;

}


// doLogin();
//doRegister();
//fetchPendingNotifications();
//generateUniqueUserId();


function tell($data) {
	echo($data.PHP_EOL);
}

function isLoggedIn() {
	if(isset($_SESSION['user_session'])) {
		echo('{"status": "ok"}'.PHP_EOL);
	} else {
		echo('{"status"; "none"}'.PHP_EOL);
	}
}

// doLogin();
//isLoggedIn();


//Comment everything but the line below to see the php version you are running in the browser.
// phpinfo();




function confirmApprovalForApplier() {
	$link = getDBConnection();
	if (!$link) {
    	return '{"connect": 0}';
	}

	if (!mysqli_select_db($link, 'timetableci')) {
	    return '{"connect": 0}';
	}	

	//first check if this user is in the registration list or not
	//if yes, then transfer over to the users list with mod permission
	//else skip quietly on the page.
	$username = 's';
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
			} else {
				//remove from the registrations table
				$delsql = "DELETE FROM registrations WHERE username='$username'";
				$delres = mysqli_query($link, $delsql);
				$obj->$status = "ok";
			}

		} else {
			//simply ignore
			$obj->$status = "ok";
		}


	}
	closeDBConnection($link);
	tell(json_encode($obj));
}


//confirmApprovalForApplier();


// function logtest(...$t) {
// 	$i=0;
// 	while($i < sizeof($t)) {
// 		tell($t[$i]);
// 		$i++;
// 	}
// }




// logtest(1,2,3,4,5,"asdfasdfasd", 2.343);


$log = "jaz, jazz";
file_put_contents('./logs/log_'.date("j.n.Y").'.txt', $log, FILE_APPEND);

?>