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

var weekdays = ["MON", "TUE", "WED", "THU", "FRI"];

var totalPeriodsPerDay = 8;

var cc = "";

function onViewTT() {
	document.getElementById("view-tt-layout").style.display = "block";
	document.getElementById("update-tt-layout").style.display = "none";
}

function onUpdateAttemptTT() {
	isLoggedIn(function() {
		document.getElementById("view-tt-layout").style.display = "none";
		document.getElementById("update-tt-layout").style.display = "block";
	}, function() {
		alert("Log-in to view this option");
	});
}





function renderTimeTable(encodedData, course, btyear, baryear) {
	var year = (course=="btech")?btyear:baryear;

	decodedData = JSON.parse(encodedData);
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
		alert("Select the proper year");
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

function removeAllChildNodes(idName) {
	var elem = document.getElementById(idName);
	if(elem) {
		//first flush all child nodes
		while (elem.hasChildNodes()) {
			elem.removeChild(elem.lastChild);
		}
	}
}

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


function uploadData() {
	
	var upd = {
		"course": cc,
		"day": $("#"+cc+"day").val(),
		"periodsData": ""
	}
	var str = [];
	for(var i = 0; i < 8; ++i) {
		str.push($("#"+cc+"period"+i).val());
	}
	upd["periodsData"] = JSON.stringify(str);
	//POST to server
	console.log(JSON.stringify(upd));
	$.ajax({
		url:"connection.php", //the page containing php script
		type: "post", //request type,
		dataType: 'json',
		data: {
			operation: "uploadTT",
			data: JSON.stringify(upd)
		}
	}).done(function(result){
		console.log("Succ");
		console.log(result.results);
		decodedData = JSON.parse(result.results);
		if(decodedData["connect"] == 0)  {
			alert("Failed to write data.");
		} else if(decodedData["connect"] > 0) {
			if(decodedData["connect"] == 1) {
				alert("Data insertion succesfull");
			} else if(decodedData["connect"] == 2) {
				alert("Data overwrite succesfull");
			}
		} else if(decodedData["connect"] < 0) {
			alert("No success with resp: "+decodedData["connect"]);
		} else {
			alert("Unknown response from server.");
		}
		//alert("connection val: "+ decodedData["connect"]);
		// alert("Upload success");
	}).fail(function(result){
		console.log("Fail");
		alert("Upload failed. Possible backend error.");
	});
}

/**
* Submit this data over to the server from here
* using ajax.
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
			alert("No data found");
		}
		console.log(result.results);
		// console.log("Value replaced");
	}).fail(function(result){
		alert("view failed");
		console.log(result.results);
		$("#nodata").text("Invalid query selected");
	});

}


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
			$('#login-content').slideToggle();
		    $(this).toggleClass('active');          
	    
	    	if ($(this).hasClass('active')) $(this).find('span').html('&#x25B2;')
	      	else $(this).find('span').html('&#x25BC;')
			document.getElementById("incorrect-login").style.display = "none";
			document.getElementById("submitlogin").value = "Log Out";
			document.getElementById("login-trigger").innerHTML="Welcome, "+res['firstName']+" "+res['lastName'];
			document.getElementById("inputs").style.display = "none";
			document.getElementById("submitlogin").onclick = doLogout;

		} else {
			document.getElementById("incorrect-login").style.display = "block";	
			document.getElementById("inputs").style.display = "block";
		}
	}).fail(function(result){
		alert("failed call for login");
		document.getElementById("incorrect-login").style.display = "block";
		document.getElementById("inputs").style.display = "block";
	});
}


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
		console.log("isLoggedIn: done");
		if(res["status"]=="ok") {
			console.log("isLoggedIn: loginok");
			//alert("Is logged in");
			if(callbackSucc) {
				console.log("isLoggedIn: execing callback"); 
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
function openCity(evt, cityName) {
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
}