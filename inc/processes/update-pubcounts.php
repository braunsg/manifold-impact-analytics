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

This script updates total and first/last author publication counts for all faculty in
the Manifold database

-->

<?php


include("../config/default-config.php");
include("../functions/default-functions.php");

// Define type of calculation to perform
$type = "total";

// Initialize database connection
$con = connectDB();

// Initialize array of faculty to loop through
$facultyArray = array();

// Initialize query to retrieve faculty IDs
$facultysql = "SELECT internetID FROM faculty_data WHERE status_current = 1";
$result = runQuery($con,$facultysql);

// Retrieve and store faculty IDs
while($row = mysqli_fetch_array($result)) {
	$rowCounter++;
	$internetId = $row['internetID'];
	$facultyArray[] = $internetId;
}	


// Initialize log file
$dateTime = date("Y-m-d") . "_" . date("hiA");

if($type === "total") {

	$outputFileName = "update-pubcounts_total_log_" . $dateTime . ".txt";
	$outputFile = fopen("logs/" . $outputFileName,"a");
	$eventType = "total_publication_counts_update";
	$eventDescription = "Update of all faculty total publication counts";
	
	printFile($outputFile,"Calculating total publication counts...\n");
	sleep(2);

} else if($type === "fl") {

	$outputFileName = "update-pubcounts_fl_log_" . $dateTime . ".txt";
	$outputFile = fopen("logs/" . $outputFileName,"a");
	$eventType = "fl_publication_counts_update";
	$eventDescription = "Update of all faculty first/last author publication counts";
	
	printFile($outputFile,"Calculating first/last author publication counts...\n");
	sleep(2);

}

// Initialize error log

$errorLogName = "update-pubcounts_errorLog_" . $dateTime . ".txt";
$errorLogFile = fopen("logs/" . $errorLogName,"a");
ini_set("log_errors", 1);
ini_set("error_log", $errorLogName);


// Record start of process

$eventStart = date("Y-m-d H:i:s");
$startProcess = "INSERT INTO events_master (eventType,eventDescription,eventStart,processLogFile,errorLogFile) VALUES('$eventType','$eventDescription','$eventStart','$outputFileName','$errorLogName')";
if(!runQuery($con,$startProcess)) {
	printFile($errorLogFile,"MySQL Error: " . mysqli_error($con) . "\n");
	printFile($outputFile,"MySQL Error: " . mysqli_error($con) . "\n");
}
$processNumber = mysqli_insert_id($con);

printFile($outputFile,"Process start: " . $eventStart . "\n");

// Loop through each faculty ID, retrieve total number of publications in database, and update 'faculty' table
$counter = 0;
$total = count($facultyArray);
foreach($facultyArray as $internetId) {

	$counter++;
	if($type === "total") {
		$pubcountsql = "SELECT COUNT(recordNumber) AS totalPubCount FROM faculty_publications WHERE record_valid = 1 AND internetID = '$internetId' LIMIT 1";
		$result = runQuery($con,$pubcountsql);
		$obj = mysqli_fetch_object($result);
		$totalPubCount = $obj->totalPubCount;
		printFile($outputFile,$internetId . "\t" . $totalPubCount . "\n");
		mysqli_free_result($result);
		
		// Update 'faculty' table with new total publication count
		$updatePubCountSql = "UPDATE faculty_metrics SET pubCount = $totalPubCount WHERE internetID = '$internetId'";
		if(!runQuery($con,$updatePubCountSql)) {
			printFile($errorLogFile,$internetId . "\n\tUpdate error: " . mysqli_error($con) . "\n");		
			printFile($outputFile,"\tUpdate error: " . mysqli_error($con) . "\n");
		} else {
			printFile($outputFile,"\tPublication count updated.\n");
		}
	} else if($type === "fl") {
		$flPubCountSql = "SELECT COUNT(recordNumber) AS totalflPubCount FROM faculty_publications WHERE record_valid = 1 AND internetID = '$internetId' AND (authorPosition = 1 OR authorPosition = authorCount) LIMIT 1";
		$result = runQuery($con,$flPubCountSql);
		$obj = mysqli_fetch_object($result);
		$totalflPubCount = $obj->totalflPubCount;
		printFile($outputFile,$internetId . "\t" . $totalflPubCount . "\n");
		mysqli_free_result($result);
		
		// Update 'faculty' table with new total publication count
		$updateflPubCountSql = "UPDATE faculty_metrics SET flPubCount = $totalflPubCount WHERE internetID = '$internetId'";
		if(!runQuery($con,$updateflPubCountSql)) {
			printFile($errorLogFile,$internetId . "\n\tUpdate error: " . mysqli_error($con) . "\n");
			printFile($outputFile,"\tUpdate error: " . mysqli_error($con) . "\n");
		} else {
			printFile($outputFile,"\tPublication count updated.\n");
		}		
	}
	printStatus($counter,$total);
}

// Record end of process

$eventEnd = date("Y-m-d H:i:s");
$endProcess = "UPDATE events_master SET eventFinish = '$eventEnd' WHERE eventID = $processNumber"; 
if(!runQuery($con,$endProcess)) {
	printFile($errorLogFile,"MySQL Update Error: " . mysqli_error($con) . "\n");
	printFile($outputFile,"MySQL Update Error: " . mysqli_error($con) . "\n");	
}
closeDB($con);

printFile($outputFile,"Process complete.\n");
printFile($outputFile,"Process end: " . $eventEnd . "\n");


?>