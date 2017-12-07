<?php
//include("connection.php");
?>
<html>
<head>
	<!-- link the style sheets, google fonts and jq here -->
	<link rel="stylesheet" type="text/css" href="css/styles.css">
	<link rel="stylesheet" type="text/css" href="css/commons.css">
	<link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
	<script language="JavaScript" type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="js.js"></script>
	<script language="JavaScript" type="text/javascript">

	//Do the following when the page has finished loading
	$(document).ready(function(){
		
		//ensure that the submit button is disabled at page load.
		$("#dataSubmit").prop('disabled', true);
		$("#updateData").prop('disabled', true);
		
		//---login stuff----
		$('#login-trigger').click(function() {
		    $(this).next('#login-content').slideToggle();
		    $(this).toggleClass('active');          
	    
	    	if ($(this).hasClass('active')) $(this).find('span').html('&#x25B2;')
	      	else $(this).find('span').html('&#x25BC;')
	    });
	    //check if the session was already live and set the
	    //status of login button accordingly
	    setLoginButtonStatus(function() {
	    	document.getElementById("register-now").style.display = "none";
	    	checkAdminStatus();
	    }, function() {
	    	document.getElementById("incorrect-login").style.display = "none";
	    	document.getElementById("register-now").style.display = "block";
	    	document.getElementById("approval-bt").style.display = "none";
	    });

	    //make sure that the home tab is clicked by def 
	    //on page load complete.
	    document.getElementById("defaultOpen").click();
	});
	</script>
</head>

<!-- ensuring that all the text in this class has the same font -->
<body class="yodafont">

	<!-- the nav for login -->
	<div id="loginNav">
		<nav>
		  <ul>
			<li id="login">
			  <a id="login-trigger" href="#">
				Member Login <span></span>
			  </a>
			  <div id="login-content">
			  	<!-- we are going to use the form element to get
			  		the user data, but submit takes place by using
			  		javascript and making the call to the server by
			  		dynamically picking up the form values. Follow the
			  		id: submitlogin in JS to know more. -->
				<form>
					<fieldset id="inputs">
						<input id="username" type="email" name="Email" placeholder="Your email address" required>   
						<input id="password" type="password" name="Password" placeholder="Password" required>
					</fieldset>
					<fieldset id="actions">
						<input type="button" id="submitlogin" value="Log in" onclick="doLogin(this.form)">
					</fieldset>
				</form>
				<div id="incorrect-login" style="display:none">Login Incorrect</div>
				<div id="register-now" style="display:block"><a href="register.php">Register Now</a></div>
			  </div>                     
			</li>
		  </ul>
		</nav>
	</div>



	
	<!-- the main container that will have the header and controls for the
		website navigation -->
	<div id="maincontainer">
		<div id="maincontainerdiv">
			<h1 id="maincontainerheader">Time Table Explorer</h1>
		</div>

		<!-- we will navigate between tabs for different content on the
			same page, including sessions without redirects. To add your tab
			put another button element in the "#tab" div and add its corresponding
			div with the appropriate id identifier. -->
		<div class="tab">
			<button class="tablinks" id="defaultOpen" onclick="openTabOps(event, 'home')">Home</button>
			<button class="tablinks" onclick="openTabOps(event, 'contactus')">Contact Us</button>
			<button class="tablinks" onclick="openTabOps(event, 'timetableview')">Schedule</button>
			<button class="tablinks" id="approval-bt" onclick="openTabOps(event, 'approvalview')">Approvals</button>
		</div>

		<!-- the elements of the tab navigation, explained -->
		<div id="home" class="tabcontent">
		  <h3>XYZ College of Engineering</h3>
		  <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
		  <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p>
		</div>
		<div id="contactus" class="tabcontent">
		  <h3>We are All-ears</h3>
		  <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p> 
		  <p>
		  	Mail us: <a href="mailto:abc@xyz.com">abc@xyz.com</a><br/>
		  	Phone number: +91-89890-876-123

		  </p>
		</div>
		<div id="timetableview" class="tabcontent">
			<!-- this section and its controls are responsible for displaying and
				showing or moderating the contents of the student time table -->
		  	<h3>Schedule</h3>
		  	<p>Here you can view the various the daily student college schedule for all available courses by the universtiy. Only admins will have moderation access (obviously).</p>
		  	<div class="table-form">

		  		<!-- appropriate permission handling is automatically taken care
		  			of by the js elements -->
				<button type="button" id="view-tt-button" class="maincontrol" onClick="onViewTT()">
					View Timetable
				</button>&nbsp;
				<button type="button" id="update-tt-button" class="maincontrol" onClick="onUpdateAttemptTT()">
					Update Timetable
				</button><br/><br/><br/>


				<!-- If displaying the time table, then this div goes into visible
					mode and based on the options selected, the timetable data is fetched -->
				<div id="view-tt-layout" style="display:none">
					<!-- Course selector -->
					<div class="course"> 
						Select Course:&nbsp;&nbsp;
						<select name="course" id="course-selector" onchange="onCourseSelect(['course-selector', 'yearbtechlist', 'yearbarchlist'])">
							<option value="--">--</option>
							<option value="btech">B.tech</option>
							<option value="barch">B.Arch</option>
						</select>
					</div>
					<!-- Year selector - add the drop down for year based 
						on the course selected -->
					<div class="year">
						<div id="yearbtechlist" style="display:none">
							Select Year:&nbsp;&nbsp;
							<select name="year-btech" id="year-btech">
								<option value="1">1st</option>
								<option value="2">2nd</option>
								<option value="3">3rd</option>
								<option value="4">4th</option>
							</select>
						</div>

						<div id="yearbarchlist" style="display:none">
							Select Year:&nbsp;&nbsp;
							<select name="year-barch" id="year-barch">
								<option value="1">1st</option>
								<option value="2">2nd</option>
								<option value="3">3rd</option>
								<option value="4">4th</option>
								<option value="5">5th</option>
							</select>						
						</div>

						
					</div>
					<button type="button" id="dataSubmit" onClick="submit()">Find Schedule</button>
					<!-- if no data is found then this div is dynmically populated -->
					<div id="nodata"></div>
					<!-- if data is found from the server, then this div is populated
						with the data and a table is dynamically rendered here -->
					<div id="rendered-data"></div>
				</div>


				<!--- If we are in Update/overwriting mode then this section is
					dynamically made visible. The appropriate permissions for the visibility 
					of this div are automatically handled in js -->
				<div id="update-tt-layout" style="display:none">
					<div>
						Total days: <b>5</b><br/>
						Periods per day: <b>8</b> + 1 (lunch)<br/>
					</div>
					Select Course:
					<select name="course" id="course-selector-ins" onchange="onCourseInsSelect(['course-selector-ins', 'yearbtechlist-ins', 'yearbarchlist-ins'])">
							<option value="--">--</option>
							<option value="btech">B.tech</option>
							<option value="barch">B.Arch</option>
					</select> <br/>
					<!-- add the drop down for year based on the course selected -->
					<div class="year">
						<div id="yearbtechlist-ins" style="display:none">
							Select the year:
							<select name="year-btech" id="year-btech-ins" onchange="onSelectInsYear(['course-selector-ins', 'year-btech-ins', 'course-upload_div'])">
								<option value="--">--</option>
								<option value="1">1st</option>
								<option value="2">2nd</option>
								<option value="3">3rd</option>
								<option value="4">4th</option>
							</select><br/>
						</div>

						<div id="yearbarchlist-ins" style="display:none" onchange="onSelectInsYear(['course-selector-ins', 'year-barch-ins', 'course-upload_div'])">
							Select the year:
							<select name="year-barch" id="year-barch-ins">
								<option value="--">--</option>
								<option value="1">1st</option>
								<option value="2">2nd</option>
								<option value="3">3rd</option>
								<option value="4">4th</option>
								<option value="5">5th</option>
							</select><br/>						
						</div>
					</div>
					
					<!-- subjects for every year and course are defined in global vars in the
						js. They are dynamically put and rendered here in this div based on the
						course and year the user selects above -->
					<div id="course-upload_div"></div>
					<!-- upload this data to the server -->
					<button type="button" id="updateData" onClick="uploadData()">Upload/Overwrite</button>

				</div>

			</div>
		</div>

		<div id="approvalview" class="tabcontent">
			<div id="approvalview-pending-no">
				<div class="alert alert-block">
			  		<h4 class="alert-heading">Awesome!</h4>
			  		No pending approvals!
				</div>	
			</div>

			<div id="approvalview-pending-yes">
				<!-- to be dynamically populated -->

			</div>
			<!-- 
			<div class="alert alert-success">
			  <strong>Well done!</strong> You successfully read this important alert message.<button class="yes">No</button><button class="yes">Submit</button>
			</div>

			<div class="alert alert-error">
			  <strong>Oh Snap!</strong> Change a few things up and try submitting again.
			</div>

			<div class="alert alert-info">
			  <strong>Heads Up!</strong> This alert needs your attention, but it's not super important.
			</div>
			 -->
		</div>
	</div>


	<!-- The actual snackbar -->
	<div id="cnf-toaster">Thank you for registering <br/>The Webmaster will confirm shortly</div>

	

</body>

</html>
