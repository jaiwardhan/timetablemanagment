<?php

	function getDBConnection() {
		$link = mysql_connect('127.0.0.1', 'test', 'test');
		return $link;
	}

	function closeDBConnection($link) {
		if($link) {
			mysql_close($link);
		}
	}

	// $link = getDBConnection();
	// echo("link acquired".PHP_EOL);
	// mysql_select_db('timetableci', $link);
	// $uname = 'jai@g.com';
	// $pwd = 'root';
	// $loginSql = "SELECT count(*) as c from users WHERE username='$uname' AND password='$pwd'";
	// $link = getDBConnection();
	// if(!link) {
	// 	return '{"status": "none"}';
	// }		
	// //select the db
	// if (!mysql_select_db('timetableci', $link)) {
	//     return '{"status": "none"}';
	// }	
	// echo("starting query");
	// $res = mysql_query($loginSql, $link);
	// $result = mysql_result($res, 0);
	// if($result==0 || $result > 1) {
	// 	echo("found");
	// 	return '{"status": "none"}';
	// }
	// echo("none found babe");
	// return '{"status": "ok"}';



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
	$uname = "jai@g.com";
	$pwd = "root";
	$loginSql = "SELECT * from users WHERE username='$uname' AND password='$pwd'";
	if(!$link) {
		return '{"status": "none"}';
	}		
	//select the db
	if (!mysql_select_db('timetableci', $link)) {
	    return '{"status": "none"}';
	}	

	$res = mysql_query($loginSql, $link);
	$rowsresult = mysql_num_rows($res);
	if($rowsresult==0 || $rowsresult > 1) {
		return '{"status": "none"}';
	}
	$row = mysql_fetch_assoc($res);
	$firstName = $row["firstname"];
	$lastName = $row["lastname"];
	$uid = $row["uid"];
	$status = "status";
	$_SESSION['user_session'] = $uid;
	$fn="firstName";
	$ln="lastName";
	$u="uid";
	$obj = (object) [];
	$obj->$fn = $firstName;
	$obj->$ln = $lastName;
	$obj->$u = $uid;
	$obj->$status = "ok";
	closeDBConnection($link);
	echo(json_encode($obj).PHP_EOL);

}


function isLoggedIn() {
	if(isset($_SESSION['user_session'])) {
		echo('{"status": "ok"}'.PHP_EOL);
	} else {
		echo('{"status"; "none"}'.PHP_EOL);
	}
}

doLogin();
isLoggedIn();


?>