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

This script updates bibliometrics for ALL EXISTING PUBLICATION RECORDS in the Manifold
database (publication_data)

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
$outputFileName = "update-pub-records_log_" . $dateTime . ".txt";
$outputFile = fopen("logs/" . $outputFileName,"a");


// Initialize error log

$errorLogName = "update-pub-records_errorLog_" . $dateTime . ".txt";
$errorLogFile = fopen("logs/" . $errorLogName,"a");
ini_set("log_errors", 1);
ini_set("error_log", $errorLogName);


// Record start of process

$eventDescription = "Update all records in the publications table, including citation counts and missing or incomplete bibliographic data for verified faculty_publications records";
$eventStart = date("Y-m-d H:i:s");
$startProcess = "INSERT INTO events_master (eventType,eventDescription,eventStart,processLogFile,errorLogFile) VALUES('all_records_full_update','$eventDescription','$eventStart','$outputFileName','$errorLogName')";
if(!runQuery($con,$startProcess)) {
	printFile($outputFile,"Error: " . mysqli_error($con) . "\n");
}
$processNumber = mysqli_insert_id($con);


// Retrieve total number of rows in publications table

$date_threshold = $quarter["endDate"];

// Specify date threshold -- last day of previous quarter
// This script will only update records that have a current lastUpdate value <= this date
// $date_threshold = date("Y-m-d",strtotime("2015-06-30"));

// Only update records for CURRENT faculty
// UPDATE 2015-07-02: Only update publication records where the faculty_publication linkage has
// been verified (record_valid = 1)
$rowCount = "SELECT COUNT(DISTINCT(publication_data.recordNumber)) as totalRows FROM publication_data INNER JOIN faculty_publications ON faculty_publications.mpid = publication_data.mpid INNER JOIN faculty_data ON faculty_data.internetID = faculty_publications.internetID WHERE faculty_data.status_current = 1 AND publication_data.lastUpdate <= '$date_threshold' AND publication_data.source = 'scopus' AND faculty_publications.record_valid = 1";


$result = runQuery($con,$rowCount);
$obj = mysqli_fetch_object($result);
$totalRows = $obj->totalRows;
printFile($outputFile,"Preparing to update " . $totalRows . " records...\n");
printFile($outputFile,"Process start: " . $eventStart . "\n");

// Initialize some fields

$resultsFieldsArray = array("pubDate" => "prism:coverDate",
		  "scopus_pubid" => "dc:identifier",
		  "scopus_eid" => "eid",
		  "pubTitle" => "dc:title",
		  "pubName" => "prism:publicationName",
		  "volume" => "prism:volume",
		  "issue" => "prism:issueIdentifier",
		  "pageRange"=>"prism:pageRange",
		  "pubDate"=>"prism:coverDate",
		  "displayDate"=>"prism:coverDisplayDate",
		  "doi"=>"prism:doi",
		  "pmid"=>"pubmed-id",
		  "citedByCount"=>"citedby-count",
		  "docType" => "subtype",
		  "docTypeDescription"=>"subtypeDescription"
		  );

$queryFieldsArray = array(
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


$queryFieldsString = implode(',',array_keys($queryFieldsArray,true));


// Loop through each article record in the publications table

$totalOffset = 0; // Starting here to try and catch through all the missing data, with the API key reset
$increment = 100;
$thisCount = 0;

$retry = 0;
while($totalOffset < $totalRows || $retry > 0){
	print "\tRETRY " . $retry . "\n";

	if($retry > 0) {
		$facultysql = "SELECT * FROM publication_data WHERE mpid = '$retry_id'";
	} else {

		// Only update records for CURRENT faculty

		$facultysql = "SELECT DISTINCT(publication_data.mpid), publication_data.* FROM publication_data INNER JOIN faculty_publications ON faculty_publications.mpid = publication_data.mpid INNER JOIN faculty_data ON faculty_data.internetID = faculty_publications.internetID WHERE faculty_data.status_current = 1 AND publication_data.lastUpdate <= '$date_threshold' AND publication_data.update_error = 0 AND publication_record.source = 'scopus' AND faculty_publications.record_valid = 1 ORDER BY (publication_data.recordNumber+0) ASC LIMIT $increment";

	}

	$result = runQuery($con,$facultysql);
	while($row = mysqli_fetch_array($result)) {
		if($retry == 0) {
			$thisCount++;	
		}
		$source = $row['source'];
		$mpid = $row['mpid'];
		$scopus_pubid = $row['scopus_pubid'];
		$scopus_eid = $row['scopus_eid'];
		$pmid = $row['pmid'];
		$doi = $row['doi'];
		$docType = $row['docType'];
		$docTypeDescr = $row['docTypeDescription'];
		$pubDate = $row['pubDate'];
		$displayDate = $row['displayDate'];
		$pageRange = $row['pageRange'];
		$volume = $row['volume'];
		$issue = $row['issue'];
		$pubTitle = $row['pubTitle'];
		$pubName = $row['pubName'];
		$citedByCount = $row['citedByCount'];
		
		$publicationData = array("scopus_pubid" => $scopus_pubid,
							 "scopus_eid" => $scopus_eid,
							 "pmid" => $pmid,
							 "doi" => $doi,
							 "pubTitle" => $pubTitle,
							 "pubName" => $pubName,
							 "pubDate" => $pubDate,
							 "displayDate" => $displayDate,
							 "pageRange" => $pageRange,
							 "volume" => $volume,
							 "issue" => $issue,
							 "citedByCount" => $citedByCount,
							 "docType" => $docType,
							 "docTypeDescription" => $docTypeDescr,
							 "source" => $source
							 );

		printFile($outputFile,"Manifold MPID: " . $mpid . " (" . $thisCount . "/" . $totalRows . ")\n");
		
		if($retry == 1) {

			// Try PMID
			if(!empty($pmid) && $pmid !== "") {
				$queryString = urlencode("pmid(" . $pmid . ")");
			} else {
				$retry = 2;
				$retry_id = $mpid;
				printFile($outputFile,"\tNo PMID on record. Trying with DOI...\n");
				break;				
			}
		} else if($retry == 2) {
			// Try DOI
			$queryString = urlencode("doi('" . $doi . "')");
		} else if($retry == 0) {
			if(!empty($scopus_eid) && $scopus_eid !== "") {
				$queryString = urlencode("eid(" . $scopus_eid . ")");
			} else if(!empty($pmid) && $pmid !== "") {
				$queryString = urlencode("pmid(" . $pmid . ")");
			} else {
				if(!empty($doi) && $doi !== "") {
					$queryString = urlencode("doi(" . $doi . ")");
				} else {
					$queryString = urlencode("eid(2-s2.0-" . $scopus_pubid . ")");
				}
			}
			print "\t" . $queryString . "\n"; 
		}		
		$url = 'http://api.elsevier.com/content/search/scopus?query=' . $queryString .'&fields=' . $queryFieldsString . '&count=1';
		printFile($outputFile,"\t" . $url . "\n");
		
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

		$curlResult = curl_exec($openCurl);
		$httpCode = curl_getinfo($openCurl, CURLINFO_HTTP_CODE); // Retrieve HTTP Response

		if($curlResult === false) {
			printFile($outputFile,"\tCurl error: " . curl_error($openCurl) . "\n");
			printFile($errorLogFile,"\t" . $mpid . "\tCurl error: " . curl_error($openCurl) . "\n");
		
		} else if($httpCode !== 200) {
			$httpErrorMsg = "HTTP Response Error - Code: " . $httpCode . "\n";
			printFile($outputFile,$httpErrorMsg);
			printFile($errorLogFile,"\t" . $mpid . "\tHTTP error: " . $httpErrorMsg . "\n");
		} else {
			$json = json_decode($curlResult,true);
			$pubs = $json['search-results']['entry'];
			$totalResults = $json['search-results']['opensearch:totalResults'];
			if($totalResults == 0) {
				if($retry == 0) {
					$retry = 1;
					$retry_id = $mpid;
					printFile($outputFile,"\tNo publications recorded with this query. Retrying with PMID...\n");
					printFile($errorLogFile,"\t" . $mpid . "\tNo results. Retrying with PMID...\n");
				
				} else if($retry == 1) {
					$retry = 2;
					$retry_id = $mpid;
					printFile($outputFile,"\tNo publications recorded with this query. Retrying with DOI...\n");
					printFile($errorLogFile,"\t" . $mpid . "\tNo results. Retrying with DOI...\n");

				} else if($retry == 2) {
					$retry = 0;
					unset($retry_id);
					$update_error_sql = "UPDATE publication_data SET update_error = 1 WHERE mpid = '$mpid'";
					if(runQuery($con,$update_error_sql)) {
						printFile($outputFile,"\tNo publications found after retry attempts.\n");
						printFile($errorLogFile,"\t" . $mpid . "\tNo results after retry attemps.\n");					
					} else {
						printFile($outputFile, "\t" . $mpid . "\tError flag problem: " . mysqli_error($con) . "\n");
						printFile($outputFile, "\t" . $mpid . "\tError flag problem: " . mysqli_error($con) . "\n");
					}
					unset($update_error_sql);
				}
				break;
			} else {
				$retry = 0;
				unset($retry_id);
				
				$updateStamp = date("Y-m-d H:i:s");
				$updateArray = array();
				$pubInfo = $pubs[0];
				if($pubInfo['error']) {
					$thisError = $pubInfo['error'];
					printFile($outputFile,"\tError: " . $thisError . "\n");
					printFile($errorLogFile,$scopus_eid . "\t" . $thisError . "\n");
				} else {
								
					foreach($resultsFieldsArray as $fieldName => $key) {
					
						switch($fieldName) {
							case "citedByCount":
								$updateArray["citedByCount"] = $pubInfo["citedby-count"];
								break;
							case "scopus_pubid":
								$checkPubId = filter_var($pubInfo['dc:identifier'], FILTER_SANITIZE_NUMBER_INT); // use eid instead?
								if($publicationData["scopus_pubid"] !== $checkPubId) {
									$updateArray["scopus_pubid"] = $checkPubId;
								}
								break;
							default:
								if($publicationData[$fieldName] === "" || empty($publicationData[$fieldName]) || is_null($publicationData[$fieldName])) {
									if($pubInfo[$key] !== "" && !empty($pubInfo[$key])) {
										$updateArray[$fieldName] = $pubInfo[$key];
									}
								}
								break;
						}
					}
					
					$updateArray["lastUpdate"] = $updateStamp;	// Make sure record lastUpdate timestamp is updated, regardless of whether or not anything was changed
					$updateQuery = "UPDATE publication_data SET ";
					$updateFieldsArray = array();
					printFile($outputFile,"\tFIELDS TO UPDATE:\n");
					foreach($updateArray as $updateField => $updateValue) {
						printFile($outputFile,"\t\t" . $updateField . ": " . $updateValue . "\n");
						if($updateField !== "citedByCount") {
							$updateFieldsArray[] = $updateField . "='" . escapeString($con,$updateValue) . "'";
						} else if($updateField === "citedByCount") {
							$updateFieldsArray[] = $updateField . "=" . $updateValue;
						}
					}


					$updateQuery .= implode(',',$updateFieldsArray) . ", update_error = 0 WHERE mpid = '$mpid'";
					if(!runQuery($con,$updateQuery)) {
						printFile($outputFile,"\tUpdate error: " . mysqli_error($con) . "\n");
						printFile($errorLogFile,"\tUpdate error: " . mysqli_error($con) . "\n\t" . $updateQuery . "\n");
					} else {
						printFile($outputFile,"\tRecord updated.\n");
					}
				} // End PUBERROR structure
			} // End results count structure
		} // End CURLRESPONSE structure
		curl_close($openCurl);
		if($thisCount < $totalRows) {
			printStatus($thisCount,$totalRows);
		}
	} // End publications.table rows retrieval
	if($retry == 0) {
		$totalOffset += $increment;
	}
} // End incremental looping


// After all records are updated, look for PubMed/Scopus duplicate records and migrate where appropriate
printFile($outputFile,"Finished updating existing records. Now checking for PubMed/Scopus duplicates...\n");

$pubmed_records_array = array();
$select_pubmed_sql = "SELECT mpid, pmid FROM publication_data WHERE source = 'pubmed'";
$select_pubmed_result = runQuery($con,$select_pubmed_sql);
while($row = mysqli_fetch_array($select_pubmed_result)) {
	$pubmed_records_array[$row['mpid']] = $row['pmid'];
}
mysqli_free_result($select_pubmed_result);

if(count($pubmed_records_array) > 0) {
	foreach($pubmed_records_array as $mpid => $pmid) {
	// Look for Scopus duplicates; if any exist, remove PubMed record from publication_data, faculty_publications
		$select_pubmed_dup_sql = "SELECT mpid FROM publication_data WHERE source = 'scopus' AND pmid = '$pmid'";
		$select_pubmed_dup_result = runQuery($con,$select_pubmed_dup_sql);
		if(mysqli_num_rows($select_pubmed_dup_result) > 0) {
			printFile($outputFile,"PubMed record duplicate: MPID " . $mpid . "\n");
			$data = mysqli_fetch_array($select_pubmed_dup_result);
			$migrate_mpid = $data["mpid"];
			$remove_pubmed_record_sql = "DELETE FROM publication_data WHERE mpid = '$mpid' AND pmid = '$pmid'";
			if(!runQuery($con,$remove_pubmed_record_sql)) {
				printFile($outputFile,"\tPubMed record error: " . mysqli_error($con) . "\n");
				printFile($errorLogFile,"\tPubMed record error: " . mysqli_error($con) . "\n\t" . $remove_pubmed_record_sql . "\n");
		
			} else {
				printFile($outputFile,"\tPubMed record removed.\n");
			}
		
			// Now remove faculty_publications record
			$remove_faculty_pubmed_sql = "DELETE FROM faculty_publications WHERE mpid = '$mpid'";
			if(!runQuery($con,$remove_faculty_pubmed_sql)) {
				printFile($outputFile,"\tPubMed record error: " . mysqli_error($con) . "\n");
				printFile($errorLogFile,"\tPubMed record error: " . mysqli_error($con) . "\n\t" . $remove_faculty_pubmed_sql . "\n");
		
			} else {
				printFile($outputFile,"\tFaculty PubMed record removed.\n");
			}
		}

	}
}
// Record end of process

$eventEnd = date("Y-m-d H:i:s");
printFile($outputFile,"Process end: " . $eventEnd . "\n");

$endProcess = "UPDATE events_master SET eventFinish = '$eventEnd' WHERE eventID = $processNumber"; 
runQuery($con,$endProcess);
closeDB($con);

printFile($outputFile, "All processes complete.\n");
printFile($outputFile,"Process end: " . $eventEnd . "\n");
printStatus(1,1);

?>
