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

This script pulls author profiles and publications authored by faculty stored 
in the Manifold database (faculty_data)

-->

<?php

include("../config/default-config.php");
include("../functions/default-functions.php");

// Initialize database connection
$con = connectDB();

// Initialize settings
ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 0);
error_reporting(E_ERROR | E_PARSE);
date_default_timezone_set('America/Chicago');

// Initialize log file
$dateTime = date("Y-m-d") . "_" . date("hiA");
$outputFileName = "get-scopus-records_log_" . $dateTime . ".txt";
$outputFile = fopen("logs/" . $outputFileName,"a");

// Initialize error log
$errorLogName = "get-scopus-records_errorLog_" . $dateTime . ".txt";
$errorLogFile = fopen("logs/" . $errorLogName,"a");
ini_set("log_errors", 1);
ini_set("error_log", $errorLogName);

// Record start of process
$eventDescription = "Pull NEW publications for all current faculty, verify EXISTING records are valid";
$eventStart = date("Y-m-d H:i:s");

// Specify end date -- last day of previous closing quarter
$endDate = date("Y-m-d",strtotime("2015-06-30"));

$startProcess = "INSERT INTO events_master (eventType,eventDescription,eventStart,date_threshold,processLogFile,errorLogFile) VALUES('publication_data_full_update','$eventDescription','$eventStart','$endDate','$outputFileName','$errorLogName')";
if(!runQuery($con,$startProcess)) {
	printFile($outputFile,"MySQL Error: " . mysqli_error($con) . "\n");
	printFile($errorLogFile,"MySQL Error: " . mysqli_error($con) . "\n");
}
$processNumber = getLastId($con);
printFile($outputFile,"Process start: " . $eventStart . "\n");

// Create an array of faculty Scopus IDs to search, indexed by internetID
$facultyIdArray = array();
$facultysql = "SELECT internetID FROM faculty_data WHERE status_current = 1";
$result = runQuery($con,$facultysql);
while($row = mysqli_fetch_array($result)) {
	$thisInternetId = $row['internetID'];
	$facultyIdArray[$thisInternetId] = array();
	
	// Exclude NULLs for the lookup - they are present in the table to ensure faculty display correctly on the web
	$scopusid_sql = "SELECT idValue FROM faculty_identifiers WHERE idType = 'scopus_id' AND internetID = '$thisInternetId' AND idValue IS NOT NULL AND idValue <> ''";
	$subresult = runQuery($con,$scopusid_sql);
	while($subrow = mysqli_fetch_assoc($subresult)) {
		$facultyIdArray[$thisInternetId][] = $subrow['idValue'];
	}
	mysqli_free_result($subresult);
}
$facultyCount = count($facultyIdArray);

//////////////////////////////////////////////////////////////////////////////////////////
///////// NOTE ///////////////////////////////////////////////////////////////////////////
/*

2015-07-02 Steven Braun

$fieldsArray was used in previous versions of this script to specify which fields to pull for
records through the API. However, this has been deprecated in favor of specifying 
view=COMPLETE in the API parameters, which yields ALL fields, regardless of specifications

*/
$fieldsArray = array(
	'citedby-count' => true,		// Number of times paper has been cited
	'identifier' => true,			// Scopus publication ID
	'eid' => true,					// Electronic identifier -- more reliable than Scopus ID
	'title' => true,				// Title of paper
	'publicationName' => true,		// Title of journal/publication venue
	'volume' => true,				// Volume of publication
	'issueIdentifier' => true,		// Issue of publication
	'pageRange' => true,			// Page range of article
	'coverDate' => true,			// Date of publication
	'coverDisplayDate' => true,		// Date of publication, as printed in original
	'authname' => true,				// List of authors
	'surname' => true,				// Author surname
	'given-name' => true,			// Author given name
	'authid' => true,				// Associated author IDs
	'doi' => true,					// DOI of paper
	'pubmed-id' => true,			// PubMed ID of paper
	'subtype' => true,				// Document subtype (e.g., 'ip')
	'subtypeDescription' => true	// Document subtype description (e.g., 'Article in press')
);
	
//////////////////////////////////////////////////////////////////////////////////////////
///////// NOTE ///////////////////////////////////////////////////////////////////////////
/*

2015-07-02 Steven Braun

Start the script with a short query that resets all record_valid flags to 0 before
verifying against Scopus data; leave PubMed faculty_publication records = 1

*/				
print "Resetting all Scopus faculty_publication records record_valid flags to 0...\n";
$record_valid_reset_sql = "UPDATE faculty_publications SET record_valid = 0 WHERE scopus_eid IS NOT NULL AND scopus_eid <> ''";
if(!runQuery($con,$record_valid_reset_sql)) {
	printFile($errorLogFile,"Reset record_valid error: " . mysqli_error($con));
	printFile($outputFile,"Reset record_valid error: " . mysqli_error($con));
} else {
	printFile($outputFile,"All Scopus faculty_publications record_valid flags reset to 0.\n");
}
	
	
$fieldsString = implode(',',array_keys($fieldsArray,true));
printFile($outputFile, "Obtaining publication data for...\n");
$thisCount = 0;
$continueCt = 0;
$data_try = 0;

// Parameter defining whether or not to attempt Scopus ID search for
// faculty with no ID in database -- SEE NOTES BELOW
$attempt_scopus_search = false;

printFile($outputFile,"Preparing to download publications through " . $endDate . "...\n");

foreach($facultyIdArray as $internetId => $scopusIdArray) {
	$data_try = 0;
	$thisCount++;
	

	printFile($outputFile, "InternetID: " . $internetId . " (" . $thisCount . "/" . $facultyCount . ")\n");
	
	if(count($scopusIdArray) == 0) {
		printFile($outputFile, "\tScopus ID: None on record. Skipping.\n");
		
		//////////////////////////////////////////////////////////////////////////////////////////
		///////// NOTE ///////////////////////////////////////////////////////////////////////////
		/*
	
		2015-07-02 Steven Braun

		The following section attempts to search Scopus for a Scopus ID if the current faculty
		doesn't have any registered in the database. HOWEVER, this is often unreliable and has been
		replaced by manual search processes. Functionality is DEPRECATED, but code left
		for legacy purposes.
	
		$attempt_scopus_search parameter added above to control whether or not this process runs
	
		*/				
		
		if($attempt_scopus_search == true) {
			while($data_try == 0) {
				printFile($outputFile, "\tScopus ID: None on record.\n");
				printFile($outputFile, "\tAttempting to retrieve ID from Scopus...\n");
				$nameQuery = "SELECT firstName, lastName FROM faculty_data WHERE internetID = '$internetId' LIMIT 1";
				$result = runQuery($con,$nameQuery);
				$obj = mysqli_fetch_object($result);
				$firstName = $obj->firstName;
				$lastName = $obj->lastName;
	
				// Change affil() parameter here to relevant institution
				$queryString = urlencode("affil(minnesota) AND authfirst(" . $firstName . ") AND authlastname(" . $lastName . ")");
				$url = 'http://api.elsevier.com/content/search/author?query=' . $queryString . '&field=identifier&count=1';
				$openCurl = curl_init();
				curl_setopt_array($openCurl, array(
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_HEADER => 0,
					CURLOPT_URL => $url,
					CURLOPT_HTTPHEADER => array(
							'X-ELS-APIKey: ' . $apiKey,
							'Accept: application/json'
						)
				));
				$result = curl_exec($openCurl);
				$httpCode = curl_getinfo($openCurl, CURLINFO_HTTP_CODE); // Retrieve HTTP Response
				if($result === false) {
					printFile($outputFile, "\tCurl error: " . curl_error($openCurl) . "\n");
					printFile($errorLogFile, $internetId . " : " . $authId . "\n");
					printFile($errorLogFile, "\tCurl error: " . curl_error($openCurl) . "\n");
					$data_try = 0;
					printFile($outputFile, "\tRetrying...\n");
				} else if($httpCode !== 200) {
					if($httpCode === 404) {
						// If get 404, it's because the request somehow lost the API key
						$data_try = 0;
						printFile($outputFile, "\tRetrying...\n");			
					} else {
						$httpErrorMsg = "HTTP Response Error - Code: " . $httpCode . "\n";
						printFile($errorLogFile, $internetId . " : " . $authId . "\n");
						printFile($errorLogFile, "\tHTTP Error: " . $httpErrorMsg . "\n\n");
						printFile($outputFile, "\tHTTP Error: " . $httpErrorMsg);
						$data_try = 0;
						printFile($outputFile, "\tRetrying...\n");
					}
				} else {
					$data_try = 1;
					$json = json_decode($result,true);	
					if($json['search-results']['opensearch:totalResults'] == 0) {
						printFile($outputFile, "\tNo data found in Scopus; defaulting publication count to 0.\n");
					} else {
						$scopusId = filter_var($json['search-results']['entry'][0]['dc:identifier'], FILTER_SANITIZE_NUMBER_INT);
						$scopusIdArray = array($scopusId);
						printFile($outputFile, "\tScopus ID retrieved: " . $scopusId . "\n");
						$insertscopusid_sql = "INSERT INTO faculty_identifiers (internetID,idType,idValue) VALUES ('$internetId','scopus_id','$scopusId')";
						if(!runQuery($con,$insertscopusid_sql)) {
							printFile($errorLogFile,"\tError: " . mysqli_error($con));
							printFile($outputFile,"\tError: " . mysqli_error($con));
						} else {
							printFile($outputFile, "\tScopus ID added to faculty table. Now continuing with publication data retrieval...\n");
						}
					}
				}	
			}
		} // END attempt_scopus_search 
		
	}
	
	// If the faculty member has at least 1 Scopus author ID registered in the database,
	// continue with publication data pull
	if(count($scopusIdArray) > 0) {	
		foreach($scopusIdArray as $key => $authId) {
			$data_try = 0;				
			while($data_try == 0) {	
	
				printFile($outputFile, "\tScopus ID: " . $authId . "...\n");
				$addedPubCount = 0;
				$offset = 0;
				$countTotal = 0;
				$countIncrement = 50;
				$loopThrough = 1;
				$totalResults = null;
				$pubCtr = 0;
				$searchPeriod = "unrestricted";
				if($searchPeriod === "restricted") {
					$queryString = urlencode("au-id(" . $authId . ") AND PUBYEAR=" . $startYear);
				} else if($searchPeriod === "unrestricted") {
					$queryString = urlencode("au-id(" . $authId . ")");
				}
				while($loopThrough == 1) {

					// Here, we are opting to use an API call that retrieves the COMPLETE view for each returned record; this enables us to also capture document type and document description (e.g., article, article in press), since we cannot capture these via the specified fields above
					$url = 'http://api.elsevier.com/content/search/scopus?query=' . $queryString .'&view=COMPLETE&count=' . $countIncrement . '&start=' . $offset;
					printFile($outputFile, "\t" . $url . "\n");
					$openCurl = curl_init();
					curl_setopt_array($openCurl, array(
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_HEADER => 0,
						CURLOPT_URL => $url,
						CURLOPT_HTTPHEADER => array(
								'X-ELS-APIKey: ' . $apiKey,
								'Accept: application/json'
							)
					));
					$result = curl_exec($openCurl);
					$httpCode = curl_getinfo($openCurl, CURLINFO_HTTP_CODE); // Retrieve HTTP Response
					if($result === false) {
						printFile($outputFile, "Curl error: " . curl_error($openCurl) . "\n");
						fwrite($errorLogFile, $internetId . " : " . $authId . "\n");
						fwrite($errorLogFile, "Curl error: " . curl_error($openCurl) . "\n");
					} else if($httpCode !== 200) {
						if($httpCode === 500) {
							// Generic error -- can't do anything about this
							$httpErrorMsg = "HTTP Response Error - Code: " . $httpCode . "\n";
							printFile($errorLogFile, $internetId . " : " . $authId . "\n");
							printFile($errorLogFile, "\tHTTP Error: " . $httpErrorMsg . "\n\n");
							printFile($outputFile, "\tHTTP Error: " . $httpErrorMsg);
							$data_try = 1;
							$loopThrough = 0;
							printFile($outputFile, "\tFACULTY SKIPPED\n");
							
						} else if($httpCode === 404) {
							// If get 404, it's because the request somehow lost the API key
							$data_try = 0;
							printFile($outputFile, "\tRetrying...\n");			
						} else {
							$httpErrorMsg = "HTTP Response Error - Code: " . $httpCode . "\n";
							printFile($errorLogFile, $internetId . " : " . $authId . "\n");
							printFile($errorLogFile, "\tHTTP Error: " . $httpErrorMsg . "\n\n");
							printFile($outputFile, "\tHTTP Error: " . $httpErrorMsg);
							$data_try = 0;
							printFile($outputFile, "\tRetrying...\n");
						}
					} else {
						$data_try = 1;
						$json = json_decode($result,true);
						$pubs = $json['search-results']['entry'];
						$pubsCount = count($pubs);
						$countTotal += count($pubs);
						if(is_null($totalResults)) {
							$totalResults = $json['search-results']['opensearch:totalResults'];
							if($totalResults == 0) {
								printFile($outputFile, "\tNo publications recorded with this ID.\n");
								$loopThrough  = 0;
								continue;
							} else {
								printFile($outputFile, "\tTotal results: " . $totalResults . "\n");
							}
						}
			
						foreach($pubs as $key => $pubInfo) {
							$pubCtr++;
							printFile($outputFile, "\tPublication " . $pubCtr . "/" . $totalResults . "\n");
							if($pubInfo['error']) {
								$thisError = $pubInfo['error'];
								if($thisError !== "Result set was empty") {
									printFile($errorLogFile, "\tError message: " . $pubInfo['error'] . "\n");
									printFile($outputFile, "\tError message: " . $pubInfo['error'] . "\n");
								}
							} else {
								$pubDate = $pubInfo['prism:coverDate'];
								$checkDate = date("Y-m-d",strtotime($pubDate));
								if($checkDate <= $endDate) { 
									$scopus_pubId = filter_var($pubInfo['dc:identifier'], FILTER_SANITIZE_NUMBER_INT);
									$scopus_eid = $pubInfo['eid'];
									printFile($outputFile, "\t\tPublication Scopus ID: " . $scopus_pubId . "\n");
									printFile($outputFile, "\t\tPublication eID: " . $scopus_eid . "\n");
									printFile($outputFile, "\t\tPublication date: " . $checkDate . "\n");
									$checkPubsDupes = "SELECT recordNumber FROM publication_data WHERE scopus_eid = '$scopus_eid'";
									$result = runQuery($con,$checkPubsDupes);
									$pub_duplicate_count = mysqli_num_rows($result);
									$authorArray = array();
									$totalAuthorCount = count($pubInfo['author']);
									$authorPosition = null;
									foreach($pubInfo['author'] as $authNo => $authInfo) {
										if($authInfo['authid'] == $authId) {
											$authorPosition = $authNo+1; // Adding 1 to offset zero-index
										}
										if(!$authInfo['surname'] || !$authInfo['given-name'] || $authInfo['surname'] === "" || $authInfo['given-name'] === "") {
											$authorArray[] = $authInfo['authname'];
										} else {
											$authorArray[] = $authInfo['surname'] . ", " . $authInfo['given-name'];
										}
									}
									if($authorPosition == null) { // If author position is undefined, probably due to Scopus ID aliasing -- continue to next iteration
										continue;
									} 
									$authorList = implode('|',$authorArray);
									$title = $pubInfo['dc:title'];
									$pubName = $pubInfo['prism:publicationName'];
									$vol = $pubInfo['prism:volume'];
									$issue = $pubInfo['prism:issueIdentifier'];
									$pages = $pubInfo['prism:pageRange'];
									$pubDate = $pubInfo['prism:coverDate'];
									$pubDisplayDate = $pubInfo['prism:coverDisplayDate'];
									$doi = $pubInfo['prism:doi'];
									$pmid = $pubInfo['pubmed-id'];
									$citedby_count = $pubInfo['citedby-count'];
									$docType = $pubInfo['subtype'];
									$docTypeDescr = $pubInfo['subtypeDescription'];
									$valuesArray = array("'mpid_pending'",
														 "'" . $scopus_pubId . "'",
														 "'" . escapeString($con,$scopus_eid) . "'",
														 "'" . $pmid . "'",
														 "'" . escapeString($con,$doi) . "'",
														 "'" . escapeString($con,$title) . "'",
														 "'" . escapeString($con,$pubName) . "'",
														 "'" . $pubDate . "'",
														 "'" . escapeString($con,$pubDisplayDate) . "'",
														 "'" . escapeString($con,$authorList) . "'",
														 "'" . escapeString($con,$pages) . "'",
														 "'" . escapeString($con,$vol) . "'",
														 "'" . escapeString($con,$issue) . "'",
														 $citedby_count,
														 "'" . escapeString($con,$docType) . "'",
														 "'" . escapeString($con,$docTypeDescr) . "'",
														 "'scopus'",
														 "'" . date("Y-m-d H:i:s") . "'"
														 );
									$valuesString = implode(',',$valuesArray);
									
									if($pub_duplicate_count == 0) {
										$pubsql = "INSERT INTO publication_data (mpid,scopus_pubid, scopus_eid, pmid, doi, pubTitle, pubName, pubDate, displayDate, authors, pageRange, volume, issue, citedByCount, docType, docTypeDescription, source, lastUpdate) VALUES ($valuesString)";
										printFile($outputFile, "\t\t" . $pubsql . "\n");
										if(!runQuery($con,$pubsql)) {
											printFile($outputFile, "\tInsert query error: " . mysqli_error($con) . "\n");
											printFile($errorLogFile, "\tInsert query error: " . mysqli_error($con) . "\n");
										}
								
										$record_no = mysqli_insert_id($con);
										$mpid = "mpid_" . $record_no;
								
										// Insert newly minted mpid
								
										$insert_mpid = "UPDATE publication_data SET mpid = '$mpid' WHERE recordNumber = $record_no";
										printFile($outputFile, "\t\t" . $insert_mpid . "\n");
										if(!runQuery($con,$insert_mpid)) {
											printFile($outputFile, "\tInsert MPID query error: " . mysqli_error($con) . "\n");
											printFile($errorLogFile, "\tInsert MPID query error: " . mysqli_error($con) . "\n");
										}
									} else {
										$get_mpid = "SELECT mpid FROM publication_data WHERE scopus_eid = '$scopus_eid'";
										$mpid_query = runQuery($con,$get_mpid);
										$obj_mpid = mysqli_fetch_object($mpid_query);
										$mpid = $obj_mpid->mpid;
									}
									mysqli_free_result($result);
							
									// Now inject into facultyPubs
									$checkFacPubDupes = "SELECT recordNumber FROM faculty_publications WHERE internetID = '$internetId' AND mpid = '$mpid'";
									$result = runQuery($con,$checkFacPubDupes);
									$fac_duplicate_count = mysqli_num_rows($result);
									if($fac_duplicate_count == 0) {
										$addedPubCount++;
										
										//////////////////////////////////////////////////
										///// NOTE ///////////////////////////////////////
										
										/*
										
										2015-07-02 Steven Braun
										
										When insert new record into faculty_publications, initialize record_valid = 1;
										this means that the record has been reconciled with Scopus and the
										faculty publication linkage is correct (based on Scopus data)
										
										*/
										
										$facpubsql = "INSERT INTO faculty_publications (fpid,internetID, scopusID, mpid, scopus_eid, authorPosition, authorCount, record_valid) VALUES ('fpid_pending','$internetId','$authId','$mpid','$scopus_eid','$authorPosition','$totalAuthorCount',1)";
										printFile($outputFile, "\t\t" . $facpubsql . "\n");
										if(!runQuery($con,$facpubsql)) {
											printFile($outputFile, "\tInsert query error: " . mysqli_error($con) . "\n");
											printFile($errorLogFile, "\tInsert query error: " . mysqli_error($con) . "\n");
										}
								
										$record_no = mysqli_insert_id($con);
										$fpid = "fpid_" . $record_no;
								
										// Insert newly minted fpid
										$insert_fpid = "UPDATE faculty_publications SET fpid = '$fpid' WHERE recordNumber = $record_no";
										printFile($outputFile, "\t\t" . $insert_fpid . "\n");
										
										if(!runQuery($con,$insert_fpid)) {
											printFile($outputFile, "\tInsert FPID query error: " . mysqli_error($con) . "\n");
											printFile($errorLogFile, "\tInsert FPID query error: " . mysqli_error($con) . "\n");
										}
								
									} else {
									
										//////////////////////////////////////////////////
										///// NOTE ///////////////////////////////////////
										
										/*
										
										2015-07-02 Steven Braun
										
										Adding this else statement -- if the faculty publication record already
										exists in faculty_publications, still update record_valid = 1
										
										*/
									
										$fp_record_valid_sql = "UPDATE faculty_publications SET record_valid = 1 WHERE internetID = '$internetId' AND mpid = '$mpid'";
										if(!runQuery($con,$fp_record_valid_sql)) {
											printFile($outputFile, "\tUpdate query error: " . mysqli_error($con) . "\n");
											printFile($errorLogFile, "\tUpdate query error: " . mysqli_error($con) . "\n");

										}
									
									}
									mysqli_free_result($result);
							
								} // End loop checking if publication falls within quarter
							} // End PUBERROR structure
						} // End PUBS structure
						
						// MJB PLACED THIS INSIDE CURLRESPONSE!
						// Record paging happens inside the successful curl request block
						// Outside, an unhandled error can cause faculty with > $countIncrement pubs
						// to spin into an infinite loop requesting more and more
						if($totalResults - $countTotal > 0) {
							$offset += $countIncrement;
						} else {
							$loopThrough = 0;
						}
					} // End CURLRESPONSE structure
				} // End LOOPTHROUGH control structure
				curl_close($openCurl);
			} // End data_try while loop
		} // End SCOPUSID loop
		printFile($outputFile, "\tADDED " . $addedPubCount . " faculty publication record(s)\n");
	}	// end if(count(scopusId)>0)
	printStatus($thisCount,$facultyCount);

} // End FACULTYIDS loop
// Record end of process
$eventEnd = date("Y-m-d H:i:s");
$endProcess = "UPDATE events_master SET eventFinish = '$eventEnd' WHERE eventID = $processNumber"; 
runQuery($con,$endProcess);
closeDB($con);
printFile($outputFile, "All processes complete.\n");
printFile($outputFile,"Process end: " . $eventEnd . "\n");
?>
