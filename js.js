/**
 * Global var for all the subjects per year. The name of each list (eg. btech1, barch1 etc) are
 * nothing but the concatenated codes of (course+year). Incase if you want to add more courses
 * and the corresponding subjects, add your vars here and they will be dynamically populated.
 * Also please keep in mind that LUNCH and HOLIDAY are mandatorily kept as part of subjects
 * to handle other cases of the time table schedule.
 */
var subjects = {
	btech1: ["BCME", "BEE", "ED", "HUM", "LUNCH", "CVIS", "EELAB", "MELAB", "CSLAB", "HOLIDAY"],
	btech2: ["ABC", "ABD", "ABCDD", "ASDS", "LUNCH", "ASD", "SDF", "WERTR", "WERRR", "HOLIDAY"],
	btech3: ["BCME", "BEE", "ED", "HUM", "LUNCH", "CVIS", "EELAB", "MELAB", "CSLAB", "HOLIDAY"],
	btech4: ["ABC", "ABD", "ABCDD", "ASDS", "LUNCH", "ASD", "SDF", "WERTR", "WERRR", "HOLIDAY"],

	barch1: ["BARBCME", "BARBEE", "BARED", "HUM", "LUNCH", "BARCVIS", "EELAB", "BARMELAB", "BARCSLAB", "HOLIDAY"],
	barch2: ["BARABC", "BARABD", "ABCDD", "ASDS", "LUNCH", "ASD", "SDF", "WERTR", "BARWERRR", "HOLIDAY"],
	barch3: ["BARBCME", "BEE", "BARED", "HUM", "LUNCH", "BARCVIS", "EELAB", "MELAB", "BARCSLAB", "HOLIDAY"],
	barch4: ["BARABC", "ABD", "BARABCDD", "ASDS", "LUNCH", "BARASD", "BARSDF", "WERTR", "WERRR", "HOLIDAY"],
	barch5: ["BARABC", "ABD", "BARABCDD", "ASDS", "LUNCH", "BARASD", "SDF", "BARWERTR", "BARWERRR", "HOLIDAY"]
};

/** The operatable weekdays */
var weekdays = ["MON", "TUE", "WED", "THU", "FRI"];
/** Constant */
var totalPeriodsPerDay = 8;
/** Global var - donot touch */
var cc = "";


/**
 * Listener when the user wants to view the time table.
 */
function onViewTT() {
	document.getElementById("view-tt-layout").style.display = "block";
	document.getElementById("update-tt-layout").style.display = "none";
}

/**
 * Listener when the user wants to update the time table. Checks if
 * the user is session logged in, and only then proceedes ahead, else
 * throws an error alert dialog.
 */
function onUpdateAttemptTT() {
	isLoggedIn(function() {
		document.getElementById("view-tt-layout").style.display = "none";
		document.getElementById("update-tt-layout").style.display = "block";
	}, function() {
		confirmToaster("Log-in to view this option");
	});
}



/**
 * Method which dynamically generates and renders the time table. Adds
 * table headers, days amd periods as part of the table. Also the time
 * -table data is generally encoded, so we need to decode it to get a 
 * structured data and then parse and render it accordingly.
 */ 
function renderTimeTable(encodedData, course, btyear, baryear) {
	var year = (course=="btech")?btyear:baryear;

	decodedData = JSON.parse(encodedData);
	//this div will be finally appended to the render div
	var div = document.createElement("div");
	var headDiv = document.createElement("div");
	headDiv.appendChild(document.createTextNode("Course: "+course+", Year: "+year));
	headDiv.appendChild(document.createElement("br"));
	//append the header to the main div
	div.appendChild(headDiv);

	//create td tr table dynamically
	var table = document.createElement('table');
	//insert a header first
	var headertr = document.createElement('tr');
	var td = document.createElement('td');
	var b = document.createElement('b');
	b.appendChild(document.createTextNode("Days"));
	td.appendChild(b);
	headertr.appendChild(td);
	//insert periods
	for(var j=1; j <= totalPeriodsPerDay; ++j) {
		var td = document.createElement('td');
		var b = document.createElement('b');
		b.appendChild(document.createTextNode("Period "+j));
		td.appendChild(b);
		headertr.appendChild(td);
	}
	table.appendChild(headertr);


	for(var key in decodedData) {
		console.log("key from object:"+key+", value is: "+decodedData[key]);
		if(key != "status") {
			//create a row for the day
			var tr = document.createElement('tr');

			//the day
			var day = document.createElement('td');
			day.appendChild(document.createTextNode(weekdays[parseInt(key)]+"                 "));
			tr.appendChild(day);


			//the periods
			console.log("key is: "+key);
			var periodsData = JSON.parse(decodedData[key]);
			for(var periods=0; periods < periodsData.length; ++periods) {
				var per = document.createElement('td');
				per.appendChild(document.createTextNode(periodsData[periods]));
				tr.appendChild(per);
			}

			//finally append this day row to the weekly table data
			table.appendChild(tr);
		}
	}

	//append this table to the create div
	div.appendChild(table);

	//finally append the main div created to the desired id location
	//to render it
	removeAllChildNodes("rendered-data");
	document.getElementById("rendered-data").appendChild(div);
}


/**
* Method that would take care of hiding/unhiding the year selector based
* on the year selected by the user.
*/
function onCourseSelect(data) {
	if($('#'+data[0]).val() == "btech") {
		//make the btech year selector visible and all others false
		document.getElementById(data[2]).style.display = "none";
		document.getElementById(data[1]).style.display = "block";
		$("#dataSubmit").prop('disabled', false);
	
	} else if($('#'+data[0]).val() == "barch") {
		document.getElementById(data[1]).style.display = "none";
		document.getElementById(data[2]).style.display = "block";
		$("#dataSubmit").prop('disabled', false);
	
	} else {
		document.getElementById(data[1]).style.display = "none";
		document.getElementById(data[2]).style.display = "none";
		$("#dataSubmit").prop('disabled', true);
	}
}



/**
* Method that would take care of hiding/unhiding the year selector based
* on the year selected by the user.
*/
function onCourseInsSelect(data) {
	if($('#'+data[0]).val() == "btech") {
		//make the btech year selector visible and all others false
		document.getElementById(data[2]).style.display = "none";
		document.getElementById(data[1]).style.display = "block";
		$("#updateData").prop('disabled', false);
	
	} else if($('#'+data[0]).val() == "barch") {
		document.getElementById(data[1]).style.display = "none";
		document.getElementById(data[2]).style.display = "block";
		$("#updateData").prop('disabled', false);
	
	} else {
		document.getElementById(data[1]).style.display = "none";
		document.getElementById(data[2]).style.display = "none";
		$("#updateData").prop('disabled', true);
	}
}

function onSelectInsYear(data) {
	//based on the year and the course, fetch and dynamically
	//inject the course drop downs into the page
	var course = $('#'+data[0]).val();
	var year = $('#'+data[1]).val();
	var courseCode = course + year;
	console.log(courseCode);	//TODO:remove this comment
	if(year.indexOf("--") >= 0) {
		confirmToaster("Select the proper year");
	} else {
		//get the dom div to be appended to
		var div = document.getElementById("course-upload_div");
		//first flush all child nodes
		removeAllChildNodes("course-upload_div");

		var i=0;
		//else fetch the data from the array and render to the div
		subj = subjects[courseCode];
		console.log(subj);	//TODO:remove this comment
	
		if(div.getAttribute("id")==undefined)div.setAttribute("id", courseCode);
		cc = courseCode;

		//add day option
		div.appendChild(document.createTextNode("Day:"));
		var daySel = document.createElement("select");
		daySel.name=courseCode+"day";
		daySel.setAttribute("id", courseCode+"day");
		for(i=0; i<weekdays.length; ++i) {
			var opt = document.createElement('option');
			opt.text = weekdays[i];
			opt.value = weekdays[i];
			daySel.appendChild(opt);
		}
		div.appendChild(daySel);
		div.appendChild(document.createElement('br'));


		//append all the subject options for 8 periods and 1 lunch
		for(i=0; i < 9; ++i) {
			//for every period add an option
			var periodSlot = createPeriodOption(subj);
			//set its id attr, name etc
			periodSlot.name = courseCode+"period"+i;
			periodSlot.setAttribute("id", courseCode+"period"+i);
			div.appendChild(document.createTextNode("Per-"+(i+1)));
			div.appendChild(periodSlot);
		}
		div.appendChild(document.createElement('br'));


		//add the upload button with pickupcode as id of the main div
		var b = document.createElement('button');
		var btext = document.createTextNode('Upload');
		b.appendChild(btext);
		b.setAttribute("id", courseCode);
		console.log("setting attr as: "+courseCode);
		b.setAttribute("onClick", "uploadData()");


	}
}


/**
 * Helper which removes all the child nodes of an
 * id by its name.
 */
function removeAllChildNodes(idName) {
	var elem = document.getElementById(idName);
	if(elem) {
		//first flush all child nodes
		while (elem.hasChildNodes()) {
			elem.removeChild(elem.lastChild);
		}
	}
}


/**
 * Creates a select drop down with options from the subjects.
 */
function createPeriodOption(subs) {
	var select = document.createElement("select");

	// select.setAttribute("id", "week1-day1-option-"+(i+1));
	// select.name="week1-day1-option-"+(i+1);
	for(j=0; j < subs.length; ++j) {
		var option = document.createElement("option");
		option.text = subs[j];
		option.value = subs[j];
		select.appendChild(option); 
	}

	return select;
}



/**
 * The Method!! Uploads the data to the server by injecting
 * the data params as a serialized json payload, and a post call
 * via AJAX to the server.
 */
function uploadData() {
	//the payload. Contains the course being
	//tried to update/overwritten for, the day
	//and the periods data selected out of the drop
	//downs.
	var upd = {
		"course": cc,
		"day": $("#"+cc+"day").val(),
		"periodsData": ""
	}
	var str = [];
	//push all the selected subjects on that day
	//into an array using jquery.
	for(var i = 0; i < 8; ++i) {
		str.push($("#"+cc+"period"+i).val());
	}
	//serialize and add subjects to the payload.
	upd["periodsData"] = JSON.stringify(str);
	console.log(JSON.stringify(upd));
	//POST to server
	$.ajax({
		url:"connection.php", //the page containing php script
		type: "post", //request type,
		dataType: 'json',
		data: {
			operation: "uploadTT",	//operation to be looking for 
			data: JSON.stringify(upd)	//serialize the data again to a string
		}
	}).done(function(result){
		// console.log("Succ");
		// console.log(result.results);

		//since there is a response from server, deserialize
		//and check if status is ok.
		decodedData = JSON.parse(result.results);
		if(decodedData["connect"] == 0)  {
			confirmToaster("Failed to write data.");
		} else if(decodedData["connect"] > 0) {
			if(decodedData["connect"] == 1) {
				confirmToaster("Data insertion succesfull");
			} else if(decodedData["connect"] == 2) {
				confirmToaster("Data overwrite succesfull");
			} else if(decodedData["connect"] == 3) {
				confirmToaster("You dont have the necessary rights<br/>to insert a new data record.");
			}
		} else if(decodedData["connect"] < 0) {
			confirmToaster("No success with resp: "+decodedData["connect"]);
		} else {
			confirmToaster("Unknown response from server.");
		}
		//alert("connection val: "+ decodedData["connect"]);
		// alert("Upload success");
	}).fail(function(result){
		console.log("Fail");
		confirmToaster("Upload failed. Possible backend error.");
	});
}

/**
 * The Method which fetches the timetable form the
 * server when asked by any user. Takes a combination of 
 * the course and the year.
 */
function submit() {
	var course = $("#course-selector").val();
	var barchyear = $("#year-barch").val();
	var btechyear = $("#year-btech").val();
	$.ajax({
		url:"connection.php", //the page containing php script
		type: "post", //request type,
		dataType: 'json',
		data: {
			operation: "viewTT",
			course: $("#course-selector").val(),
			year_barch: $("#year-barch").val(),
			year_btech: $("#year-btech").val()
		}
	}).done(function(result){
		var res = JSON.parse(result.results)["status"];
		if(res == "ok") {
			// alert("View success");
			renderTimeTable(result.results, course, barchyear, btechyear);
		} else {
			confirmToaster("No data found with this course combination");
			removeAllChildNodes("rendered-data");
		}
		console.log(result.results);
		// console.log("Value replaced");
	}).fail(function(result){
		alert("view failed");
		console.log(result.results);
		$("#nodata").text("Invalid query selected");
	});

}



/**
 * Method which tries doing the login and ensures that the page
 * is rendered according to the login status. All updates are done on
 * the same page and no redirects are performed.
 */
function doLogin(formData) {
	$.ajax({
		url:"connection.php", //the page containing php script
		type: "post", //request type,
		dataType: 'json',
		data: {
			operation: "login",
			uname: formData.Email.value,
			pwd: formData.Password.value
		}
	}).done(function(result){
		var res = JSON.parse(result.results);
		console.log(res);
		if(res['status']=="ok") {
			//we would want the uname/pwd box to close on
			//its own.
			$('#login-content').slideToggle();
		    $(this).toggleClass('active');          
	    
	    	if ($(this).hasClass('active')) $(this).find('span').html('&#x25B2;')
	      	else $(this).find('span').html('&#x25BC;')
			document.getElementById("incorrect-login").style.display = "none";
			
			//also that when the box is opened, the button should now tell the user
			//to log out with the same button action.
			document.getElementById("submitlogin").value = "Log Out";
			
			//the box should now welcome the user by fetching his name from the
			//recieved payload
			document.getElementById("login-trigger").innerHTML="Welcome, "+res['firstName']+" "+res['lastName'];
			document.getElementById("inputs").style.display = "none";
			//and the button call should now do a logout action when clicked.
			document.getElementById("submitlogin").onclick = doLogout;
			document.getElementById("register-now").style.display = "none";

		} else {
			//tell the user its an incorrect login.
			document.getElementById("incorrect-login").style.display = "block";	
			document.getElementById("inputs").style.display = "block";
			document.getElementById("approval-bt").style.display = "none";
		}
		checkAdminStatus();
	}).fail(function(result){
		alert("failed call for login");
		document.getElementById("incorrect-login").style.display = "block";
		document.getElementById("inputs").style.display = "block";
	});
}



/**
 * Does a logout when requested for. Essentially destroys the user 
 * session and reloads the page if success.
 */
function doLogout() {
	$.ajax({
		url:"connection.php", //the page containing php script
		type: "post", //request type,
		dataType: 'json',
		data: {
			operation: "logout"
		}
	}).done(function(result){
		location.reload(true);
	}).fail(function(result){
		alert("failed to log out");
		location.reload();
	});
}


/**
 * Helper to check if the user is logged in. Since this is a post call
 * hence we may want further processing after the post has been processed.
 * Hence supports two callbacks in either case if the user is logged in or not.
 */
function isLoggedIn(callbackSucc, callbackFail) {
	$.ajax({
		url:"connection.php", //the page containing php script
		type: "post", //request type,
		dataType: 'json',
		data: {
			operation: "isLoggedIn"
		}
	}).done(function(result) {
		var res = JSON.parse(result.results);
		// console.log("isLoggedIn: done");
		if(res["status"]=="ok") {
			console.log("isLoggedIn: loginok");
			//alert("Is logged in");
			if(callbackSucc) {
				// console.log("isLoggedIn: execing callback"); 
				callbackSucc();
			}
			
		} else {
			console.log("isLoggedIn: login--notok");
			if(callbackFail) {
				console.log("isLoggedIn: execing callbackfail");
				callbackFail();
			}
		}
	}).fail(function(result){
		alert("failed to check");
		location.reload();
	});
}


/**
 * Sets the status of the login button based on if the user is already
 * session logged in or not. Since that is a POST, hence we go ahead and
 * support two callbacks which process when the post is complete. This method
 * is called when the page is loaded again and we wanna see if the user was still
 * in session active mode.
 */
function setLoginButtonStatus(callbackOnSuccess, callbackOnFailure) {
	isLoggedIn(function () {
		console.log("setLoginButtonStatus: true");
		// $('#login-content').slideToggle();
	    // $(this).toggleClass('active');          
    
    	if ($(this).hasClass('active')) $(this).find('span').html('&#x25B2;')
      	else $(this).find('span').html('&#x25BC;')
		document.getElementById("incorrect-login").style.display = "none";
		document.getElementById("submitlogin").value = "Log Out";
		setFullNameFromSession("login-trigger");
		document.getElementById("inputs").style.display = "none";
		document.getElementById("submitlogin").onclick = doLogout;

		if(callbackOnSuccess) {
			callbackOnSuccess();
		}

	}, function () {
		console.log("setLoginButtonStatus: false");
		document.getElementById("incorrect-login").style.display = "block";	
		document.getElementById("inputs").style.display = "block";
		if(callbackOnFailure) {
			callbackOnFailure();
		}
	}); 
}



/**
 *	Checks admin status and does what we need to do.
 */
function checkAdminStatus(passCallback, failCallback) {
	$.ajax({
		url:"connection.php", //the page containing php script
		type: "post", //request type,
		dataType: 'json',
		data: {
			operation: "isAdmin"
		}
	}).done(function(result) {
		var res = JSON.parse(result.results);
		// console.log("isLoggedIn: done");
		if(res["status"]=="ok") {
			document.getElementById("approval-bt").style.display = "block";
			if(passCallback!=null) {
				passCallback();
			}
		} else {
			document.getElementById("approval-bt").style.display = "none";
			if(failCallback !=  null) {
				failCallback();
			}
		}
	}).fail(function(result){
		document.getElementById("approval-bt").style.display = "none";	
	});
}


/**
 * Gets the full name of the logged in user from the
 * session data. Since this is a post call check, hence we pass
 * the divname whose text value has to be updated with the user's name.
 */
function setFullNameFromSession(divName) {
	isLoggedIn(function() {
		$.ajax({
			url:"connection.php", //the page containing php script
			type: "post", //request type,
			dataType: 'json',
			data: {
				operation: "getFullNameFromSession"
			}
		}).done(function(result) {
			console.log("getFullNameFromSession: "+result.results);
			document.getElementById(divName).innerHTML = "Welcome, "+result.results;
		})
	}, function() {
		document.getElementById(divName).innerHTML = "Welcome user!";
	}) 
}






//----------------- tabs below -----------------------------
function openTabOps(evt, cityName) {
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the button that opened the tab
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";

    if(cityName=="approvalview") {
    	populatePendingRequests();
    }
}




//---------------------- register js below ----------------------
/**
 * Method which tries doing the login and ensures that the page
 * is rendered according to the login status. All updates are done on
 * the same page and no redirects are performed.
 */
function doRegister(formData) {
	console.log("registering");
	console.log(formData.mailid.value);
	console.log(formData.password.value);
	console.log(formData.fname.value);
	console.log(formData.lname.value);
	$.ajax({
		url:"connection.php", //the page containing php script
		type: "post", //request type,
		dataType: 'json',
		data: {
			operation: "doRegister",
			uname: formData.mailid.value,
			pwd: formData.password.value,
			fname: formData.fname.value,
			lname: formData.lname.value
		}
	}).done(function(result){
		var res = JSON.parse(result.results);
		console.log(res);
		if(res['status']=="success") {
			//confirm with a toaster.
			//add callback to toaster so that it takes you back to the
			//home page
			confirmToaster(null, function() {
				setTimeout(function(){
				 	window.location.replace('index.php');
				}, 3200);
			});

		} else if(res['status']=="confirmpending") {
			//tell the user its pending
			confirmToaster("Your input is awaiting pending", function() {
				setTimeout(function(){
				 	window.location.replace('index.php');
				}, 3200);
			});
		} else {
			//tell the user its an incorrect login.
			confirmToaster("Your input was rejected by the server");
		}
	}).fail(function(result){
		alert("failed call for login");
	});
	
}

function confirmToaster(text, callback) {
	// Get the snackbar DIV
    var x = document.getElementById("cnf-toaster")

    // Add the "show" class to DIV
    x.className = "show";
    if(text!=null) {
    	x.innerHTML = text;
    }

    // After 3 seconds, remove the show class from DIV
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 2900);
    if(callback!=null) {
    	callback();
    }
}



/** ------------------- approval js below -----------------------

/**
 * method which dynamically injects a user's approval data
 * into the respective div of the page as another child.
 */ 
function insertPendingNotifier(index, data) {
	var insertionDiv = document.createElement('div');
	insertionDiv.setAttribute('identifier', index);
	insertionDiv.setAttribute('id', 'pending'+index);
	insertionDiv.className += ' alert alert-info';
	
	var name = data["firstname"] + " " + data["lastname"];
	var nameElement = document.createElement('strong');
	nameElement.appendChild(document.createTextNode(name));
	insertionDiv.appendChild(nameElement);
	insertionDiv.appendChild(document.createElement('br'));

	var email = data["username"];
	var emailelement = document.createElement('i');
	emailelement.appendChild(document.createTextNode(email + " needs your approval to join as a moderator"));
	insertionDiv.appendChild(emailelement);
	
	var buttonNo = document.createElement('button');
	buttonNo.className += ' option approval-buttons-no';
	//append the username to this button uname property
	buttonNo.setAttribute('uname', email);
	buttonNo.setAttribute('pendingidx', index);
	buttonNo.appendChild(document.createTextNode("Dissapprove"));
	buttonNo.onclick = sendRejectionToServer;
	var buttonYes = document.createElement('button');
	buttonYes.className += ' option approval-buttons-yes';
	//append the username to this button uname property
	buttonYes.setAttribute('uname', email);
	buttonYes.setAttribute('pendingidx', index);
	buttonYes.appendChild(document.createTextNode("Approve"));
	buttonYes.onclick = sendApprovalToServer;
	insertionDiv.appendChild(buttonNo);
	insertionDiv.appendChild(buttonYes);

	//insert into the main div in the page
	var maindiv = document.getElementById('approvalview-pending-yes');
	maindiv.appendChild(insertionDiv);
}



/**
 * Method which populates all the pending registration
 * requests into the respective div of the main page bar section
 */
function populatePendingRequests() {
	console.log("checking");
	$.ajax({
		url:"connection.php", //the page containing php script
		type: "post", //request type,
		dataType: 'json',
		data: {
			operation: "fetchpending"
		}
	}).done(function(result){
		var res = JSON.parse(result.results);
		console.log(res);
		if(res['status']=="ok") {
			var idx = 0;
			removeAllChildNodes("approvalview-pending-yes");
			for(;idx < res['length']; ++idx) {
				console.log('idx '+idx);
				insertPendingNotifier(idx, res[""+idx]);
			}
			document.getElementById('approvalview-pending-no').style.display = "none";
			document.getElementById('approvalview-pending-yes').style.display = "block";
		} else {
			document.getElementById('approvalview-pending-no').style.display = "block";
			document.getElementById('approvalview-pending-yes').style.display = "none";
		}
	}).fail(function(result){
		alert("failed call for notifications");
	});
}


/**
 * Method called when the admin wants to approve a request
 * in the registration.
 */ 
function sendApprovalToServer() {
	var idx = this.getAttribute('pendingidx');
	$.ajax({
		url:"connection.php", //the page containing php script
		type: "post", //request type,
		dataType: 'json',
		data: {
			operation: "confirmApprovalForApplier",
			uname: this.getAttribute('uname')
		}
	}).done(function(result){
		var res = JSON.parse(result.results);
		var userDiv = document.getElementById('pending'+idx);
		if(res['status']=="ok") {
			userDiv.className = 'alert alert-success';
			confirmToaster("User Membership Confirmed");
		} else {
			userDiv.className = 'alert alert-block';
		}
		disableColumnById('pending'+idx);
	}).fail(function(result){
		alert("Failed call for approval");
	});
}


/**
 * Method called when the admin wants to reject a request
 * in the registration.
 */ 
function sendRejectionToServer() {
	var idx = this.getAttribute('pendingidx');
	$.ajax({
		url:"connection.php", //the page containing php script
		type: "post", //request type,
		dataType: 'json',
		data: {
			operation: "rejectApprovalForApplier",
			uname: this.getAttribute('uname')
		}
	}).done(function(result){
		var res = JSON.parse(result.results);
		var userDiv = document.getElementById('pending'+idx);
		if(res['status']=="ok") {
			userDiv.className = 'alert alert-error';
			confirmToaster("User Membership Rejected");
		} else {
			userDiv.className = 'alert alert-block';
		}
		disableColumnById('pending'+idx);
	}).fail(function(result){
		alert("failed call for rejection");
	});
}



/**
 * This method could virtually disable any div passed by
 * id and all its children as well.
 */
function disableColumnById(idName) {
	var el = document.getElementById(idName);
	var allChildNodes = el.getElementsByTagName('*');
	el.disabled = true;
	for(var i = 0; i < allChildNodes.length; i++) {
	   allChildNodes[i].disabled = true;
	}
	el.className += ' approve-button-disabled';
}

