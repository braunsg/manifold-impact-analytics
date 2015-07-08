<!--

manifold-impact-analytics
https://github.com/braunsg/manifold-impact-analytics

Open source code for Manifold, an automated impact analytics and visualization platform developed by
Steven Braun.

COPYRIGHT (C) 2015 Steven Braun

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  

A full copy of the license is included in LICENSE.md.

//////////////////////////////////////////////////////////////////////////////////////////
/////// About this file

Injects confirmed PubMed publications for import into the temp_submissions table;
sends user an e-mail to verify the submission

-->

<?php

	include("../../config/default-config.php");
	include("../../functions/default-functions.php");

	$con = connectDB();
	$internetId = mysqli_real_escape_string($con, $_POST['internetID']);

	// Dummy variable to control for duplicate temp_submissions entries
	$duplicate_entries = array();
	
	// Get first/last name of faculty member
	
	$name_data_sql = "SELECT faculty_data.firstName, faculty_data.lastName, affiliation_data.affilName FROM faculty_data INNER JOIN faculty_affiliations ON faculty_data.internetID = faculty_affiliations.internetID INNER JOIN affiliation_data ON faculty_affiliations.affilID = affiliation_data.umn_zdeptid WHERE faculty_affiliations.affilClass = 'DISPLAY' AND faculty_data.internetID = '$internetId' LIMIT 1";
	$name_data_result = runQuery($con,$name_data_sql);
	$name_obj = mysqli_fetch_object($name_data_result);
	$faculty_firstName = $name_obj->firstName;
	$faculty_lastName = $name_obj->lastName;
	$faculty_affilName = $name_obj->affilName;
	mysqli_free_result($name_data_result);
	
	$email_address = $internetId . "@example.edu";
	
	$submit_data = $_POST["submit_data"];
	$email_data = array();
	
	foreach($submit_data as $pmid => $record_data) {
		$pmid = mysqli_real_escape_string($con, $pmid);
		$totalAuthorCount = count($record_data["authorList"]);

		$check_duplicates = "SELECT mpid FROM publication_data WHERE pmid = '$pmid'";
		$result = runQuery($con,$check_duplicates);
		$duplicate_count = mysqli_num_rows($result);
		mysqli_free_result($result);

		$authorArray = array();
		foreach($record_data["authorList"] as $authorNo => $authorInfo) {
			$authorArray[] = $authorInfo["LastName"] . ", " . $authorInfo["ForeName"];
		}
		$authorList = implode('|',$authorArray);
		$authorCount = count($record_data["authorList"]);
		$authorPosition = $record_data["authorPosition"];
		$title = $record_data["pubTitle"];
		$pubName = $record_data["pubName"];
		$vol = $record_data["volume"];
		$issue = $record_data["issue"];
		$pages = $record_data["pageRange"];	
				
		// Generate publication and display dates
		// For PubMed records, "publication date" is based on DateCreated,
		// and "display date" is based on PubDate from original XML response
		
		$date_identifiers = array("Year","Month","Day");
		$array_creationDate = array();
		$array_pubDate = array();	// reorder publication display date if missing elements
		foreach($date_identifiers as $identifier) {
			if(array_key_exists($identifier,$record_data["creationDate"])) {
				$date_entity = $record_data["creationDate"][$identifier];
				switch($identifier) {
					case "Year":
						$array_creationDate["Year"] = $date_entity;
						break;
					case "Month":
						$array_creationDate["Month"] = $date_entity;
						break;
					case "Day":
						$array_creationDate["Day"] = $date_entity;
						break;
				}
			} else {
				switch($identifier) {
					case "Year":
						$array_creationDate["Year"] = 	"0000";
						break;
					case "Month":
						$array_creationDate["Month"] = "00";
						break;
					case "Day":
						$array_creationDate["Day"] = "00";
						break;
				}
		
			}
			if(array_key_exists($identifier,$record_data["pubDate"])) {
				$array_pubDate[$identifier] = $record_data["pubDate"][$identifier];
			}
			
			
		}
		
		
		$creationDate = implode("-",$array_creationDate);	// this is pubDate for PubMed articles
		$pubDate = implode(" ",$array_pubDate);				// this is displayDate for PubMed articles
		$doi = $record_data["idList"]["doi"];
		$verification_code = substr(md5(uniqid(rand(), true)), 16, 16);
		if($duplicate_count == 0) {
		
			$valuesArray = array("'" . escapeString($con,$internetId) . "'",
								 "'" . $pmid . "'",
								 "'" . escapeString($con,$doi) . "'",
								 "'" . escapeString($con,$title) . "'",
								 "'" . escapeString($con,$pubName) . "'",
								 "'" . $creationDate . "'",
								 "'" . escapeString($con,$pubDate) . "'",
								 "'" . escapeString($con,$authorList) . "'",
								 $authorPosition,
								 $authorCount,
								 "'" . escapeString($con,$pages) . "'",
								 "'" . escapeString($con,$vol) . "'",
								 "'" . escapeString($con,$issue) . "'",
								 "'pubmed'",
								 "'PENDING'",
								 "'" . escapeString($con,$verification_code) . "'");

			$valuesString = implode(',',$valuesArray);

			// Check if already imported
			$temp_submission_sql = "SELECT recordNumber FROM temp_submissions WHERE internetID = '$internetId' AND pmid = '$pmid'";
			$temp_submission_result = runQuery($con,$temp_submission_sql);
			if(mysqli_num_rows($temp_submission_result) == 0) {			
				$insert_pubmed_record = "INSERT INTO temp_submissions (internetID, pmid, doi, pubTitle, pubName, pubDate, displayDate, authors, authorPosition, authorCount, pageRange, volume, issue, source,status,verification_code) VALUES ($valuesString)";
				if(!runQuery($con,$insert_pubmed_record)) {
					print "\tInsert query error: " . mysqli_error($con) . "\n";
					$display = "error";
				} else {
					$email_data[$pmid] = array('title' => $title,
											   'date' => $pubDate,
											   'journal' => $pubName,
											   'verification_code' => $verification_code);									 	   
				}
			} else {
				if(!array_key_exists($pmid,$duplicate_entries)) {
					$duplicate_entries[$pmid] = $title;
				}
			}
		} else {	// If PubMed record already in database, still grab data for facultyPubs entry
			$get_mpid = "SELECT mpid, source FROM publication_data WHERE pmid = '$pmid'";
			$mpid_result = runQuery($con,$get_mpid);
			$obj = mysqli_fetch_object($mpid_result);
			$mpid = $obj->mpid;
			$source = $obj->source;

			// Check if already imported
			$temp_submission_sql = "SELECT recordNumber FROM temp_submissions WHERE internetID = '$internetId' AND pmid = '$pmid'";
			$temp_submission_result = runQuery($con,$temp_submission_sql);
			if(mysqli_num_rows($temp_submission_result) == 0) {
			
				$valuesArray = array("'" . escapeString($con,$internetId) . "'",
									 "'" . $mpid . "'",
									 $authorPosition,
									 $authorCount,
									 $pmid,
									 "'" . $source . "'",
									 "'PENDING'",
									 "'" . escapeString($con,$verification_code) . "'");
									 
				$valuesString = implode(',',$valuesArray);
				$insert_pubmed_record = "INSERT INTO temp_submissions (internetID, mpid, authorPosition, authorCount, pmid, source, status, verification_code) VALUES($valuesString)";
				if(!runQuery($con,$insert_pubmed_record)) {
					print "\tInsert query error: " . mysqli_error($con) . "\n";
					$display = "error";
				} else {
					$email_data[$pmid] = array('title' => $title,
											   'date' => $pubDate,
											   'journal' => $pubName,
											   'verification_code' => $verification_code);									 	   
				}
			} else {
				if(!array_key_exists($pmid,$duplicate_entries)) {
					$duplicate_entries[$pmid] = $title;
				}
			}
		}// End check for record duplicates
	
	} // End records loop through
	
	closeDB($con);
	
	// Only send confirmation e-mail if attempted import isn't already in system
	if(count($duplicate_entries) != count(array_keys($submit_data)) && $display !== "error") {
	
		// Send confirmation e-mail to faculty via x500 -- verifies identity
		// See PHPMailer documentation for more details
		
		require '../../libraries/PHPMailer/PHPMailerAutoload.php';

		// Send confirmation message to provided e-mail

		$confirmation_mail = new PHPMailer;

		$confirmation_mail ->isSMTP();                                      // Set mailer to use SMTP
		$confirmation_mail ->Host = $smtp['host'];  // Specify main and backup SMTP servers
		$confirmation_mail ->SMTPAuth = True;                               // Enable SMTP authentication
		$confirmation_mail ->Username = $smtp['user'];                 // SMTP username
		$confirmation_mail ->Password = $smtp['pw'];                           // SMTP password
		$confirmation_mail ->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted

		$confirmation_mail ->From = 'manifold_alias@example.edu';
		$confirmation_mail ->FromName = 'Manifold Submission';
		$confirmation_mail ->addAddress($email_address);     // Add a recipient
		
		$confirmation_mail ->isHTML(true);                                  // Set email format to HTML

		$confirmation_mail ->Subject = 'Manifold Submission: Verify your identity';
		$confirmation_mail ->Body    = "Dear $faculty_firstName $faculty_lastName,<br><br>" .
			"This message is to confirm your recent submission to your Manifold profile via the PubMed import module. <b>In order for your submission to be displayed on your profile</b>, you must verify your identity.<br><br>Review the summary of your submission below. If all data are correct, click the <b>Verification Code</b> link provided for each record.<br><br>" .
			"If you have any questions about the import process or if you believe you are receiving this message in error, please submit an inquiry through the <a href='" . $site_def_external_baseurl . "resources.php?p=contact'>contact form</a>.<br><br>";
		
		foreach($email_data as $pmid => $publication_data) {
			$verification_link = $site_def_external_baseurl . "verify-submission.php?verify=" . $publication_data['verification_code'];
			$pub_info = "<b>Title</b>: <a target='_blank' href='http://www.ncbi.nlm.nih.gov/pubmed/$pmid'>" . $publication_data['title'] . "</a><br>" .
						"<b>Journal</b>: <i>" . $publication_data['journal'] . "</i><br>" . 
						"<b>Publication Date</b>: " . $publication_data['date'] . "<br>" .
						"<b>Verification Code</b>: <a target='_blank' href='$verification_link'>" . $publication_data['verification_code'] . "</a><br><br>";
			$confirmation_mail->Body .= $pub_info;
		}


		if(!$confirmation_mail->send()) {
		echo 'Mailer Error: ' . $confirmation_mail->ErrorInfo;
			$display = "error";
		} else {
			$display = "success";
		}
	} else {
		$display = "error";
	}	
	
?>	
	
	<script>
		close_popout();
	</script>
	
	<div id="close_window"><img id="close" src="inc/images/close.png"></div>
	<div class="sectionHeader">
	<?php 
	if($display === "success") {
		echo "Verify your identity";
	} else if($display === "error") {
		echo "Import error";
	}
	?>
	</div>
	<div id="descriptionContainer">
		<div class="genericContent">
		<?php 
		if($display === "success") {
			if(count($duplicate_entries) == 0) {
		?>
				The selected publications have been temporarily imported but require authorization.<br>
				<br>
				<b>Before these publications are displayed on your profile</b>, you must verify your identity via your University of Minnesota x500. Please follow the directions in the e-mail sent to your University e-mail account to finalize the import confirmation process.
		<?php
			} else if(count($duplicate_entries) < count(array_keys($submit_data))) {
		?>
				Some of the selected publications have been temporarily imported but others have not because they have already been imported before. Please use the verification link provided in the e-mail sent to you to verify the following submissions:<br><br>
		<?php
				foreach($duplicate_entries as $pmid => $title) {
					print "<a href='http://www.ncbi.nlm.nih.gov/pubmed/" . $pmid . "' target='_blank'>" . $title . "</a><br><br>";			
				}
		?>
				For the remaining submissions, <b>before the publications are displayed on your profile</b>, you must verify your identity via your University of Minnesota x500. Please follow the directions in the e-mail sent to your University e-mail account to finalize the import confirmation process.
		<?php
			} 

		} else if($display === "error") {
			if(count($duplicate_entries) == count(array_keys($submit_data))) {
		?>
				All of the selected records you have specified for import have already been submitted. Please use the verification link provided in the e-mail sent to you to verify the data for your profile.<br><br>
			
		<?php
			} else {
		?>
			There was an error in the import process. Please try again.
		
		<?php
			}
		}
		?>

		<br><br>
		If you have any questions about this process or experience any difficulty with verifying publications for display, please submit an inquiry through the <a target="_blank" href="resources.php?p=contact">contact form</a>.		
		</div>
	</div>
