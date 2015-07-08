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

Updates the data file used to generate the h-index/h-citations correlation visualization
on profiles in the IMPACT ANALYTICS section

-->

<?php


include("../config/default-config.php");
include("../functions/default-functions.php");

$con = connectDB();

$date = date("Y-m-d");
$outputFileName = "hIndex_hCitations_data_" . $date . ".txt";
$outputFile = fopen("../visualizations/h_matrix/" . $outputFileName,"a");
$logFileName = "update-hmatrix_log_" . $date . "_" . date("hiA") . ".txt";
$logFile = fopen("logs/" . $logFileName, "a");

// Record start of process

$eventDescription = "Update data for h-matrix visualizations";
$eventStart = date("Y-m-d H:i:s");
$startProcess = "INSERT INTO events_master (eventType,eventDescription,eventStart,processLogFile,errorLogFile) VALUES('hmatrix_data_update','$eventDescription','$eventStart','$logFileName','$logFileName')";
if(!runQuery($con,$startProcess)) {
	printFile($outputFile,"Error: " . mysqli_error($con) . "\n");
}
$processNumber = mysqli_insert_id($con);

printFile($logFile, "Preparing to update data for h-matrix visualization...\n");


// Begin calculations

$facultyArray = array();

$select_metrics_sql = "SELECT faculty_data.internetID, faculty_data.firstName, faculty_data.lastName, faculty_metrics.hIndex, faculty_metrics.pubCount, faculty_affiliations.affilID FROM faculty_data INNER JOIN faculty_metrics ON faculty_metrics.internetID = faculty_data.internetID INNER JOIN faculty_affiliations ON faculty_affiliations.internetID = faculty_data.internetID WHERE faculty_affiliations.affilClass = 'DISPLAY' AND faculty_data.status_faculty = 1 AND faculty_data.percentTime >= 0.67 AND faculty_data.status_current = 1";
$result = runQuery($con,$select_metrics_sql);
while($row = mysqli_fetch_array($result)) {
	$internetId = $row['internetID'];
	$firstName = $row['firstName'];
	$lastName = $row['lastName'];
	$hIndex = $row['hIndex'];
	$pubCount = $row['pubCount'];
	$deptId = $row['affilID'];
	$facultyArray[$internetId] = array("hIndex" => $hIndex, "totalCount" => 0, "firstName" => $firstName, "lastName" => $lastName, "pubCount" => $pubCount, "dept" => $deptId);
}	

foreach($facultyArray as $internetId => $data) {
	$hIndex = $data['hIndex'];
	if($hIndex > 0) {
		$getCitationCount = "SELECT SUM(subtable.citedByCount) as hCitations FROM (SELECT publication_data.citedByCount FROM publication_data INNER JOIN faculty_publications ON faculty_publications.mpid = publication_data.mpid WHERE faculty_publications.record_valid = 1 AND faculty_publications.internetID = '$internetId' ORDER BY publication_data.citedByCount DESC LIMIT $hIndex) AS subtable";
		if(!mysqli_query($con,$getCitationCount)) {
			die(mysqli_error($con));
		} else {
			$result = mysqli_query($con,$getCitationCount);
			$obj = mysqli_fetch_object($result);
			$hCitations = $obj->hCitations;
			if(is_null($hCitations)) {
				$hCitations = 0;
			}
			$facultyArray[$internetId]['totalCount'] = $obj->hCitations;
		}
	}
	$hCitationCount = $facultyArray[$internetId]['totalCount'];
	$output = $internetId . "\t" . $hIndex . "\t" . $hCitationCount . "\t" . $data["pubCount"] . "\t" . $data["firstName"] . "\t" . $data["lastName"] . "\t" . $data["dept"] . "\n";
	printFile($outputFile,$output);
}

// Now update data source in visualizations table

$update_vis_data_sql = "UPDATE visualizations SET vis_dataURL = '$outputFileName' WHERE visID = 'V0005'";
runQuery($con,$update_vis_data_sql);

// Record end of process

$eventEnd = date("Y-m-d H:i:s");
printFile($logFile,"Process end: " . $eventEnd . "\n");

$endProcess = "UPDATE events_master SET eventFinish = '$eventEnd' WHERE eventID = $processNumber"; 
runQuery($con,$endProcess);
closeDB($con);

printFile($logFile,"H-matrix data update complete. Data stored in $outputFileName.\n");
?>
