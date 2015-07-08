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

Calculates h- and h(fl)-indexes for each faculty member and injects values into Manifold database

-->

<?php


include("../config/default-config.php");
include("../functions/default-functions.php");

// Define type of calculation to perform
$type = "h";

// Initialize database connection
$con = connectDB();


// Create an array of faculty emplIDs

$facultyIdArray = array();

$facultysql = "SELECT internetID FROM faculty_data WHERE status_current = 1";
$result = runQuery($con, $facultysql);
while($row = mysqli_fetch_array($result)) {
	$thisInternetId = $row['internetID'];
	$facultyIdArray[] = $thisInternetId;
}	

$counter = 0;


$total = count($facultyIdArray);

$dateTime = date("Y-m-d") . "_" . date("hiA");

// Initialize error log

$errorLogName = "update-indexes_errorLog_" . $dateTime . ".txt";
$errorLogFile = fopen("logs/" . $errorLogName,"a");
ini_set("log_errors", 1);
ini_set("error_log", $errorLogName);



if($type === "hfl") {

	$outputFileName = "update-indexes_hfl_log_" . $dateTime . ".txt";
	$outputProcessDescription = "Calculating h(fl)-indices...\n";
	$eventType = "hfl_index_all_update";
	$eventDescription = "Update of h(fl)-index for all faculty";
	
} else if($type === "h") {

	$outputFileName = "update-indexes_h_log_" . $dateTime . ".txt";
	$outputProcessDescription = "Calculating h-indices...\n";
	$eventType = "h_index_all_update";
	$eventDescription = "Update of h-index for all faculty";

} else if($type === "both") {

	$outputFileName = "update-indexes_h_hfl_log_" . $dateTime . ".txt";
	$outputProcessDescription = "Calculating h- and h(fl)-indices...\n";
	$eventType = "h_hfl_index_all_update";
	$eventDescription = "Update of both h- and h(fl)-index for all faculty";
	
}

$outputFile = fopen("logs/" . $outputFileName,"a");
printFile($outputFile,"Process start: " . $dateTime . "\n");
printFile($outputFile,$outputProcessDescription);

// Record start of process

$eventStart = date("Y-m-d H:i:s");
$startProcess = "INSERT INTO events_master (eventType,eventDescription,eventStart,processLogFile,errorLogFile) VALUES('$eventType','$eventDescription','$eventStart','$outputFileName','$errorLogName')";
if(!runQuery($con,$startProcess)) {
	printFile($outputFile,"\tMySQL Error: " . mysqli_error($con) . "\n");
	printFile($errorLogFile,"\tMySQL Error: " . mysqli_error($con) . "\n");
}
$processNumber = mysqli_insert_id($con);
printFile($outputFile,"Process start: " . $eventStart . "\n");


foreach($facultyIdArray as $internetkey => $internetId) {

	$counter++;

	// Generate pubID array -- including all publications, because h and h(fl)-indices will most often be recalculated together
	$pubIdArray = array();
	$hflArray = array();

	$pubIdArray = array();
	$pubsql = "SELECT mpid, authorPosition, authorCount FROM faculty_publications WHERE record_valid = 1 AND internetID = '$internetId'";
	$result = runQuery($con,$pubsql);
	while($row = mysqli_fetch_array($result)) {
		$pubIdArray[] = $row['mpid'];
		if($row['authorPosition'] == 1 || $row['authorPosition'] == $row['authorCount']) {
			$hflArray[] = $row['mpid'];
		}
	}
	mysqli_free_result($result);

	if($type === "hfl" || $type === "both") {
		printFile($outputFile,"InternetID: " . $internetId . " (" . $counter . "/" . $total . ")\n");
		$citedCountArray = array();
		foreach($hflArray as $pubkey => $mpid) {
			// Indices are only calculated for SCOPUS publication data
			$citedsql = "SELECT citedByCount FROM publication_data WHERE mpid = '$mpid' AND source = 'scopus' LIMIT 1";
			$result = runQuery($con,$citedsql);
			if(mysqli_num_rows($result) > 0) {
				$obj = mysqli_fetch_object($result);
				$citedCountArray[$mpid] = $obj->citedByCount;
			}
			mysqli_free_result($result);
		}
		
		arsort($citedCountArray);

		$iCount = 0;
		$hflIndex = 0;
		foreach($citedCountArray as $mpid => $citedCount) {
			$iCount++;
			if($citedCount >= $iCount) {
				$hflIndex++;
			} else {
				break;
			}
		}

		printFile($outputFile,"\tH(fl)-index: " . $hflIndex . "\n");

	// Update table

		$indexsql = "UPDATE faculty_metrics SET hflIndex = $hflIndex WHERE internetID = '$internetId'";
		if(!runQuery($con,$indexsql)) {
			printFile($errorLogFile,$internetId . "\n\tUpdate error: " . mysqli_error($con) . "\n");
			printFile($outputFile,"\tUpdate error: " . mysqli_error($con) . "\n");
		} else {
			printFile($outputFile,"\tH(fl)-index updated.\n");
		}
	}
	
	if($type === "h" || $type === "both") {

		printFile($outputFile,"InternetID: " . $internetId . " (" . $counter . "/" . $total . ")\n");

		// Calculate h-index

		$citedCountArray = array();
		foreach($pubIdArray as $pubkey => $mpid) {
			// Indices are only calculated for SCOPUS publication data		
			$citedsql = "SELECT citedByCount FROM publication_data WHERE mpid = '$mpid' AND source = 'scopus' LIMIT 1";
			$result = runQuery($con,$citedsql);
			if(mysqli_num_rows($result) > 0) {
				$obj = mysqli_fetch_object($result);
				$citedCountArray[$mpid] = $obj->citedByCount;
			}
			mysqli_free_result($result);
		}

		arsort($citedCountArray);

		$iCount = 0;
		$hIndex = 0;
		foreach($citedCountArray as $mpid => $citedCount) {
			$iCount++;
			if($citedCount >= $iCount) {
				$hIndex++;
			} else {
				break;
			}
		}

		printFile($outputFile,"\tH-index: " . $hIndex . "\n");

		// Update table

		$indexsql = "UPDATE faculty_metrics SET hIndex = $hIndex WHERE internetID = '$internetId'";
		if(!runQuery($con,$indexsql)) {
			printFile($errorLogFile,$internetId . "\n\tUpdate error: " . mysqli_error($con) . "\n");
			printFile($outputFile,"\tUpdate error: " . mysqli_error($con) . "\n");
		} else {
			printFile($outputFile,"\tH-index updated.\n");
		}
	
	}
	
	printStatus($counter,$total);

}

// Record end of process

$eventEnd = date("Y-m-d H:i:s");
$endProcess = "UPDATE events_master SET eventFinish = '$eventEnd' WHERE eventID = $processNumber"; 
if(!runQuery($con,$endProcess)) {
	printFile($outputFile,"\tMySQL Error: " . mysqli_error($con) . "\n");
	printFile($errorLogFile,"\tMySQL Error: " . mysqli_error($con) . "\n");
}
closeDB($con);

printFile($outputFile,"Process complete.\n");
printFile($outputFile,"Process end: " . $eventEnd . "\n");

?>