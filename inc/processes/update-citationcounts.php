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

This script updates total citation counts to all publications authored by faculty 
in the Manifold database; also calculates number of first/last authorships

-->

<?php

include("../config/default-config.php");
include("../functions/default-functions.php");

// Defines the type of calculation to perform
$type = "total";

// Initialize database connection
$con = connectDB();

// Initialize array of faculty to loop through
$facultyArray = array();

// Initialize query to retrieve faculty IDs
$facultysql = "SELECT internetID FROM faculty_data WHERE status_current = 1";
$result = runQuery($con, $facultysql);

// Retrieve and store faculty IDs
while($row = mysqli_fetch_array($result)) {
	$rowCounter++;
	$internetId = $row['internetID'];
	$facultyArray[] = $internetId;
}	


// Initialize log files, printed headers 
$dateTime = date("Y-m-d") . "_" . date("hiA");

if($type === "total") {

	// Initialize log file
	$outputFileName = "update-citationcounts_total_log_" . $dateTime . ".txt";
	$outputFile = fopen("logs/" . $outputFileName,"a");
	$eventType = "total_citation_counts_update";
	$eventDescription = "Update of total citation counts for all faculty";
	
	printFile($outputFile,"Calculating total publication citation counts...\n");
	sleep(2);

} else if($type === "fl") {

	// Initialize log file
	$outputFileName = "update-citationcounts_citationCounts_fl_log_" . $dateTime . ".txt";
	$outputFile = fopen("logs/" . $outputFileName,"a");
	$eventType = "fl_citation_counts_update";
	$eventDescription = "Update of first/last author publication citation counts for all faculty";

	printFile($outputFile,"Calculating first/last author publication citation counts...\n");
	sleep(2);

}

// Initialize error log

$errorLogName = "update-citationcounts_errorLog_" . $dateTime . ".txt";
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


// Loop through each faculty ID, retrieve total number of citations to first/last author papers, and update 'faculty' table
$counter = 0;
$total = count($facultyArray);
foreach($facultyArray as $internetId) {


	$counter++;
	if($type === "total") {

		$totalCitationSql = "SELECT SUM(publication_data.citedByCount) AS totalCitCount FROM publication_data INNER JOIN faculty_publications ON faculty_publications.mpid = publication_data.mpid WHERE faculty_publications.record_valid = 1 AND publication_data.source = 'scopus' AND faculty_publications.internetID = '$internetId'";
		$result = runQuery($con,$totalCitationSql);
		$obj = mysqli_fetch_object($result);
		$getCount = $obj->totalCitCount;
		if($getCount != NULL) {
			$totalCitCt = $getCount;
		} else {
			$totalCitCt = 0;
		}
		mysqli_free_result($result);
		printFile($outputFile, $internetId . "\t" . $totalCitCt . "\n");

		// Update 'faculty' table with new total first/last author paper total citation count
		$updateTotalCitCountSql = "UPDATE faculty_metrics SET totalCitations = $totalCitCt WHERE internetID = '$internetId'";
		if(!runQuery($con,$updateTotalCitCountSql)) {
			printFile($errorLogFile,$internetId . "\n\tUpdate error: " . mysqli_error($con) . "\n");
			printFile($outputFile,"\tUpdate error: " . mysqli_error($con) . "\n");
		} else {
			printFile($outputFile,"\tTotal citation count updated.\n");
		}
		

	} else if($type === "fl") {

		$totalflCitationSql = "SELECT SUM(publication_data.citedByCount) AS flCount FROM publication_data INNER JOIN faculty_publications ON faculty_publications.mpid = publication_data.mpid WHERE faculty_publications.record_valid = 1 AND publication_data.source = 'scopus' AND faculty_publications.internetID = '$internetId' AND (faculty_publications.authorPosition = 1 OR faculty_publications.authorPosition = faculty_publications.authorCount)";
		$result = runQuery($con, $totalflCitationSql);
		$obj = mysqli_fetch_object($result);
		$getCount = $obj->flCount;
		if($getCount != NULL) {
			$flCitCt = $getCount;
		} else {
			$flCitCt = 0;
		}
		mysqli_free_result($result);
		printFile($outputFile,$internetId . "\t" . $flCitCt . "\n");

		// Update 'faculty' table with new total first/last author paper total citation count
		$updateflCitCountSql = "UPDATE faculty_metrics SET totalflCitations = $flCitCt WHERE internetID = '$internetId'";
		if(!runQuery($con,$updateflCitCountSql)) {
			printFile($errorLogFile,$internetId . "\n\tUpdate error: " . mysqli_error($con) . "\n");
			printFile($outputFile,"\tUpdate error: " . mysqli_error($con) . "\n");
		} else {
			printFile($outputFile,"\tFirst/last author publication citation count updated.\n");
		}
	
	
	}
	
	printStatus($counter,$total);
}

// Record end of process

$eventEnd = date("Y-m-d H:i:s");
$endProcess = "UPDATE events_master SET eventFinish = '$eventEnd' WHERE eventID = $processNumber"; 
if(!runQuery($con,$endProcess)) {
	printFile($errorLogFile,"MySQL Error: " . mysqli_error($con) . "\n");
	printFile($outputFile,"MySQL Error: " . mysqli_error($con) . "\n");
}
closeDB($con);

printFile($outputFile,"Process complete.\n");
printFile($outputFile,"Process end: " . $eventEnd . "\n");
?>