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



// doLogin();
uploadTT();


function tell($data) {
	echo($data.PHP_EOL);
}

// function isLoggedIn() {
// 	if(isset($_SESSION['user_session'])) {
// 		echo('{"status": "ok"}'.PHP_EOL);
// 	} else {
// 		echo('{"status"; "none"}'.PHP_EOL);
// 	}
// }

// doLogin();
// isLoggedIn();


//Comment everything but the line below to see the php version you are running in the browser.
// phpinfo();

?>