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

Completes the PubMed record import process, following user verification

-->

<!DOCTYPE html>
<?php

// Completes PubMed submission process after user verifies identity

$current_page = preg_replace('/\.php$/','',basename(__FILE__));

include("inc/templates/components-header.php");
include("inc/templates/page-template-header.php");

?>

<div class="page_header">Verifying Submission</div>

<?php

// GET verification code

$verification_code = isset($_GET['verify']) ? $_GET['verify'] : "";

if(!$verification_code || $verification_code == "") {

?>


<div class="faq_header">Verification error</div>
<div class="faq_container">
	<div class="faq_content">
	Your verification code cannot be recognized. Please try again or submit an inquiry through the <a href="resources.php?p=contact">contact form</a>.
	</div>
</div>

<?php

} else {	// Complete the submission process if a verification code is provided

	// Establish database connection
	
	$con = connectDB();
	$verification_code = mysqli_real_escape_string($con, $verification_code);
	
	// Pull publication data from database
	
	$get_data_sql = "SELECT * FROM temp_submissions WHERE verification_code = '$verification_code'";
	$result = runQuery($con,$get_data_sql);
	if(mysqli_num_rows($result) == 0) {
		$expired = 1;
	?>

	<div class="faq_header">Verification code expired</div>
	<div class="faq_container">
		<div class="faq_content">
		The verification code you provided has expired. If you believe this is in error, please submit an inquiry through the <a href="resources.php?p=contact">contact form</a>.
		</div>
	</div>
	
	<?php
	
	} else {
	
		while($row = mysqli_fetch_array($result)) {
			$internetId = $row['internetID'];
			$this_record_num = $row['recordNumber'];
			if($row['mpid'] == NULL) {	// If no MPID, need to add new record to publications table
				$valuesArray = array("'mpid_pending'",
									 "'" . $row['pmid'] . "'",
									 "'" . escapeString($con,$row['doi']) . "'",
									 "'" . escapeString($con,$row['pubTitle']) . "'",
									 "'" . escapeString($con,$row['pubName']) . "'",
									 "'" . $row['pubDate'] . "'",
									 "'" . escapeString($con,$row['displayDate']) . "'",
									 "'" . escapeString($con,$row['authors']) . "'",
									 "'" . escapeString($con,$row['pageRange']) . "'",
									 "'" . escapeString($con,$row['volume']) . "'",
									 "'" . escapeString($con,$row['issue']) . "'",
									 "'" . escapeString($con,$row['source']) . "'",
									 "'" . date("Y-m-d H:i:s") . "'");
								 
								 
				$valuesString = implode(',',$valuesArray);
				$insert_pubmed_record = "INSERT IGNORE INTO publication_data (mpid, pmid, doi, pubTitle, pubName, pubDate, displayDate, authors, pageRange, volume, issue, source, lastUpdate) VALUES ($valuesString)";
				if(!runQuery($con,$insert_pubmed_record)) {
					print "\tInsert query error: " . mysqli_error($con) . "\n";
					$error = 1;
				} else {
					$query_record_id = mysqli_insert_id($con);
					$mpid = "mpid_" . $query_record_id;
					$update_mpid = "UPDATE publication_data SET mpid = '$mpid' WHERE recordNumber = $query_record_id";
					if(!runQuery($con,$update_mpid)) {
						print "\tMPID update error: " . mysqli_error($con) . "\n";
					}
				}
			} else {
				$mpid = $row['mpid'];
			}

			// Add record to facultyPubs	
			$facpubsql = "SELECT recordNumber FROM faculty_publications WHERE internetID = '$internetId' AND mpid = '$mpid'";
			$facpubsql_result = runQuery($con,$facpubsql);
			$facpubsql_duplicate = mysqli_num_rows($facpubsql_result);

			if($facpubsql_duplicate == 0) {				
				$authorPosition = $row['authorPosition'];
				$authorCount = $row['authorCount'];
				
				// Note: when adding new record to faculty_publications, record_valid should always default to 1
				// for PubMed imports
				$insert_faculty_pub = "INSERT INTO faculty_publications (internetID, mpid, authorPosition, authorCount, fpid, record_valid) VALUES ('$internetId','$mpid',$authorPosition,$authorCount,'fpid_pending',1)";
				if(!runQuery($con,$insert_faculty_pub)) {
					print "\tInsert query error: " . mysqli_error($con) . "\n";
					$error = 1;
				} else {
					$query_record_id = mysqli_insert_id($con);
					$fpid = "fpid_" . $query_record_id;
					$update_fpid = "UPDATE faculty_publications SET fpid = '$fpid' WHERE recordNumber = $query_record_id";
					if(!runQuery($con,$update_fpid)) {
						print "\tFPID update error: " . mysqli_error($con) . "\n";
					}
				}
			}
						
			// Now expire the verification code
			
			$expire_code_sql = "UPDATE temp_submissions SET mpid = '$mpid', verification_code = 'EXPIRED', status = 'VERIFIED', date_verified = '" . date("Y-m-d H:i:s") . "' WHERE recordNumber = $this_record_num";
			if(!runQuery($con,$expire_code_sql)) {
				print "\tExpiration error: " . mysqli_error($con) . "\n";
			}
			
			// Finally, increment faculty member's (first/last) publication count by 1 for each imported record
			
			if($authorPosition == 1 || $authorPosition == $authorCount) {
				$update_metrics_sql = "UPDATE faculty_metrics SET flPubCount = flPubCount + 1, pubCount = pubCount + 1 WHERE internetID = '$internetId'";
			} else {
				$update_metrics_sql = "UPDATE faculty_metrics SET pubCount = pubCount + 1 WHERE internetID = '$internetId'";
			}
			
			if(!runQuery($con,$update_metrics_sql)) {
				print "\tMetrics update error: " . mysqli_error($con) . "\n";
			}
			
		} // End looping through records
	}	
	if($error == 1) {
	
?>

	<div class="faq_header">Submission not completed</div>
	<div class="faq_container">
		<div class="faq_content">
		A problem occurred in finalizing the publications confirmation process. Please try again or submit an inquiry through the <a href="resources.php?p=contact">contact form</a>.
		</div>
	</div>


<?php
	
	} else {
		if($expired != 1) {
			if($facpubsql_duplicate == 0) {
?>

				<div class="faq_header">Submission completed</div>
				<div class="faq_container">
					<div class="faq_content">
					Your submission has been verified and completed. The submitted record will show up on your profile immediately.
					</div>
				</div>


<?php	
			} else {
?>

				<div class="faq_header">Submission duplication</div>
				<div class="faq_container">
					<div class="faq_content">
					The record you have attempted to verify already exists in the database; no new records have been added. If you have any questions about this, please submit an inquiry through the <a href="resources.php?p=contact">contact form</a>.
					</div>
				</div>

			
<?php			
			}
		}
	}
	
	
}

include("inc/templates/footer.php");
