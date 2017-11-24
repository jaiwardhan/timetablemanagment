<?php


//fetch the operation, based on the options sent,
//execute the corresponding section.


$operation = $_POST['operation'];
echo json_encode(array("results"=>process($operation)));



function process($operation) {
	$result = false;
	switch ($operation) {
		case 'viewTT':
			$result = viewTimeTable();	
			break;
		
		case 'login':
			$result = doLogin();
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
	if($course == "btech") {
		$year = $_POST["year_btech"];
		//sanitize and make sql calls
		return "True enginner";

	} else if($course == "barch") {
		$year = $_POST["year_barch"];
		//sanitize and make sql calls
		return '{"mon": ["sci", "maths"], "tue": []}';
	} else {
		return false;
	}
}


/**
 * Checks if the user has appropriate permissions or not
 * by checing the data base.
 */
function doLogin() {
	return false;
}

?>
