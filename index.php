<?php
//include("connection.php");
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/styles.css">
	<link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
	<script language="JavaScript" type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
	<script src="js.js"></script>
	<script language="JavaScript" type="text/javascript">
	$(document).ready(function(){
		console.log("doc ready");
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
	    setLoginButtonStatus(null, function() {document.getElementById("incorrect-login").style.display = "none";});
	    document.getElementById("defaultOpen").click();
	});
	</script>
</head>
<body class="yodafont">


	<div id="loginNav">
		<nav>
		  <ul>
			<li id="login">
			  <a id="login-trigger" href="#">
				Admin Login <span></span>
			  </a>
			  <div id="login-content">
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
			  </div>                     
			</li>
		  </ul>
		</nav>
	</div>



	
		
	<div id="maincontainer">
		<div id="maincontainerdiv">
			<h1 id="maincontainerheader">Time Table Explorer</h1>
		</div>


		<div class="tab">
			<button class="tablinks" id="defaultOpen" onclick="openCity(event, 'home')">Home</button>
			<button class="tablinks" onclick="openCity(event, 'contactus')">Contact Us</button>
			<button class="tablinks" onclick="openCity(event, 'timetableview')">Schedule</button>
		</div>
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
		  <h3>Schedule</h3>
		  <p>Here you can view the various the daily student college schedule for all available courses by the universtiy. Only admins will have moderation access (obviously).</p>
		  <div class="table-form">
			<button type="button" id="view-tt-button" class="maincontrol" onClick="onViewTT()">
				View Timetable
			</button>&nbsp;
			<button type="button" id="update-tt-button" class="maincontrol" onClick="onUpdateAttemptTT()">
				Update Timetable
			</button><br/><br/><br/>

			<div id="view-tt-layout" style="display:none">
				<div class="course"> 
					Select Course:&nbsp;&nbsp;
					<select name="course" id="course-selector" onchange="onCourseSelect(['course-selector', 'yearbtechlist', 'yearbarchlist'])">
						<option value="--">--</option>
						<option value="btech">B.tech</option>
						<option value="barch">B.Arch</option>
					</select>
				</div>
				<!-- add the drop down for year based on the course selected -->
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
				<div id="nodata"></div>
				<div id="rendered-data"></div>
			</div>


			<!--- the update data form -->
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
				<!-- dynamically generate subjects drop downs based on the course -->
				
				<div id="course-upload_div">
				
				</div>

				<button type="button" id="updateData" onClick="uploadData()">Upload/Overwrite</button>

			</div>

		</div>
		</div>
	</div>

	

</body>

</html>
