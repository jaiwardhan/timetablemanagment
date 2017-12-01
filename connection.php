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

		case 'getFullNameFromSession':
			$result = getFullNameFromSession();
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
		return json_encode($obj);
	}

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
		return '{"status": "none"}';
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
	return json_encode($obj);

}


function doLogout() {
	if(session_destroy()) {
		return '{"status": "ok"}';
	}
	return '{"status": "none"}';
}


function isLoggedIn() {
	if(isset($_SESSION['user_session'])) {
		return '{"status": "ok"}';
	}
	return '{"status": "none"}';
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


	$sql = "SELECT schedule FROM timetable WHERE course='$course' and day='$day'";
	$res = mysqli_query($link, $sql);
	$result = (!$res || (mysqli_num_rows($res))==0)?0:(mysqli_num_rows($res));

	// return $result."-";

	//check if a valid result was obtained
	//if not, then insert, else overwrite
	if($result==0) {
		$inssql = "INSERT into timetable (course, day, 	schedule) VALUES ('$course', '$day', '$schedule')";

		$result = mysqli_query($link, $inssql);
		if(!$result) {
			closeDBConnection($link);
			return '{"connect": -1}';
		} else {
			closeDBConnection($link);
			return '{"connect": 1}';
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
			closeDBConnection($link);
			return '{"connect": 2}';
		}
	}


	closeDBConnection($link);
	return '{"connect": 3}';
}

function getDBConnection() {
	$link = mysqli_connect('localhost', 'test', 'test');
	return $link;
}

function closeDBConnection($link) {
	if($link) {
		mysqli_close($link);
	}
}


function getFullNameFromSession() {
	if(isset($_SESSION['user_session'])) {
		$fname = $_SESSION['fname'];
		$lname = $_SESSION['lname'];
		return $fname." ".$lname;
	}
}


?>
