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

Generates departmental summary data for quarterly reports

-->

<?php


include("../config/default-config.php");
include("../functions/default-functions.php");

$con = connectDB();

// Determine quarter, year, and previous year
$currentQuarter = getQuarter();
$quarterName = $currentQuarter["quarterName"];
$startDate = $currentQuarter["startDate"];
$endDate = $currentQuarter["endDate"];
$currentYear = date("Y",strtotime($startDate));
$previousYear = $currentYear - 1;

// Define log files
$logFileName = "update-quarterly-dept-report_log_" . date("Y-m-d") . "_" . date("hiA") . ".txt";
$logFile = fopen("logs/" . $logFileName, "a");
$outputFileName = $currentYear . "_q" . $quarterName . ".json";

// Record start of process

$eventDescription = "Update data for quarterly department report";
$eventStart = date("Y-m-d H:i:s");
$startProcess = "INSERT INTO events_master (eventType,eventDescription,eventStart,processLogFile,errorLogFile) VALUES('data-update_report-1','$eventDescription','$eventStart','$logFileName','$logFileName')";
if(!runQuery($con,$startProcess)) {
	printFile($outputFile,"Error: " . mysqli_error($con) . "\n");
}
$processNumber = mysqli_insert_id($con);

printFile($logFile, "Preparing to update data for quarterly department reports...\n");

// Get departments

$affils = array();
$affils[] = array("currentQuarter" => array(
												"totalPublications" => null,		// Total number of publications for current quarter
												"flPublications" => null			// Sum first/last author publications for current quarter
												),
						"previousYear" => array(
												"totalPublications" => null,		// Total number of publications from previous year
												"flPublications" => null			// Sum first/last author publications from previous year
												),
						"cumulativeQuarter" => array(
												"totalPublications" => null,		// Total number of publications, cumulative, through current quarter
												"flPublications" => null			// Sum first/last author publications, cumulative, through current quarter
												),
						"deptName" => "Total",
						"umn_deptid" => "total"
						);


$deptsql = "SELECT affilName, umn_deptid, umn_zdeptid FROM affiliation_data WHERE display = 1 AND affilType = 'department' ORDER BY affilName ASC";
$result = runQuery($con,$deptsql);
while($row = mysqli_fetch_array($result)) {
	$umn_deptid = $row['umn_deptid'];
	$umn_zdeptid = $row['umn_zdeptid'];
	$deptName = $row['affilName'];
	$affils[] = array("currentQuarter" => array(
													"totalPublications" => null,		// Total number of publications for current quarter
													"flPublications" => null			// Sum first/last author publications for current quarter
													),
							"previousYear" => array(
													"totalPublications" => null,		// Total number of publications from previous year
													"flPublications" => null			// Sum first/last author publications from previous year
													),
							"cumulativeQuarter" => array(
													"totalPublications" => null,		// Total number of publications, cumulative, through current quarter
													"flPublications" => null			// Sum first/last author publications, cumulative, through current quarter
													),
							"deptName" => $deptName,
							"umn_deptid" => $umn_deptid,
							"umn_zdeptid" => $umn_zdeptid
							);
}
mysqli_free_result($result);

// Loop through departments and obtain statistics
foreach($affils as $ind => $deptData) {
	// Must obtain faculty internetIDs to do calculations
	$facultyIds = array();
	$umn_deptid = $deptData["umn_deptid"];
	$umn_zdeptid = $deptData["umn_zdeptid"];
	
	if($umn_deptid === "total") {
		$facsql = "SELECT internetID FROM faculty_data WHERE percentTime >= 0.67 AND status_faculty = 1 AND status_current = 1 ORDER BY internetID ASC";
	} else {
		$facsql = "SELECT faculty_data.internetID FROM faculty_data INNER JOIN faculty_affiliations ON faculty_affiliations.internetID = faculty_data.internetID WHERE (faculty_affiliations.affilID = '$umn_deptid' OR faculty_affiliations.affilID = '$umn_zdeptid') AND faculty_affiliations.affilClass = 'DISPLAY' AND faculty_data.percentTime >= 0.67 AND faculty_data.status_faculty = 1 AND faculty_data.status_current = 1 ORDER BY faculty_data.internetID ASC";
	}
	$result = runQuery($con,$facsql);
	if(mysqli_num_rows($result) == 0) {
		unset($affils[$ind]);
	} else {
		while($row = mysqli_fetch_array($result)) {
			$internetId = $row['internetID'];
			$facultyIds[] = $internetId;
		}
		mysqli_free_result($result);
		$facultyIdList = "'" . implode('\',\'',$facultyIds) . "'";
		
		// Calculate total publications for current quarter	
		$totalPubs_quarter = "SELECT COUNT(DISTINCT publication_data.mpid) AS quarterTotal FROM faculty_publications INNER JOIN publication_data ON faculty_publications.mpid = publication_data.mpid WHERE faculty_publications.record_valid = 1 AND faculty_publications.internetID IN ($facultyIdList) AND publication_data.pubDate >= '$startDate' AND publication_data.pubDate <= '$endDate'";
		print $totalPubs_quarter . "\n";
		$result = runQuery($con,$totalPubs_quarter);
		$obj = mysqli_fetch_object($result);
		$affils[$ind]["currentQuarter"]["totalPublications"] = $obj->quarterTotal;
		mysqli_free_result($result);

		// Calculate sum of first/last author publications for current quarter	
		$flPubs_quarter = "SELECT COUNT(DISTINCT publication_data.mpid) AS quarterFlTotal FROM faculty_publications INNER JOIN publication_data ON faculty_publications.mpid = publication_data.mpid WHERE faculty_publications.record_valid = 1 AND faculty_publications.internetID IN ($facultyIdList) AND publication_data.pubDate >= '$startDate' AND publication_data.pubDate <= '$endDate' AND (faculty_publications.authorPosition = faculty_publications.authorCount OR faculty_publications.authorPosition = 1)";
		print $flPubs_quarter . "\n";
		$result = runQuery($con,$flPubs_quarter);
		$obj = mysqli_fetch_object($result);
		$affils[$ind]["currentQuarter"]["flPublications"] = $obj->quarterFlTotal;
		mysqli_free_result($result);

		// Calculate total publications for previous year

		$previousYearStart = $previousYear . "-01-01";
		$previousYearEnd = $previousYear . "-12-31";
	
	
		$totalPubs_previous = "SELECT COUNT(DISTINCT publication_data.mpid) AS previousYearTotal FROM faculty_publications INNER JOIN publication_data ON faculty_publications.mpid = publication_data.mpid WHERE faculty_publications.record_valid = 1 AND faculty_publications.internetID IN ($facultyIdList) AND publication_data.pubDate >= '$previousYearStart' AND publication_data.pubDate <= '$previousYearEnd'";
		print $totalPubs_previous . "\n";
	
		$result = runQuery($con,$totalPubs_previous);
		$obj = mysqli_fetch_object($result);
		$affils[$ind]["previousYear"]["totalPublications"] = $obj->previousYearTotal;
		mysqli_free_result($result);

		// Calculate first/last author publications for previous year
	
		$flPubs_previous = "SELECT COUNT(DISTINCT publication_data.mpid) AS previousYearFlTotal FROM faculty_publications INNER JOIN publication_data ON faculty_publications.mpid = publication_data.mpid WHERE faculty_publications.record_valid = 1 AND faculty_publications.internetID IN ($facultyIdList) AND publication_data.pubDate >= '$previousYearStart' AND publication_data.pubDate <= '$previousYearEnd' AND (faculty_publications.authorPosition = faculty_publications.authorCount OR faculty_publications.authorPosition = 1)";
		print $flPubs_previous . "\n";
		$result = runQuery($con,$flPubs_previous);
		$obj = mysqli_fetch_object($result);
		$affils[$ind]["previousYear"]["flPublications"] = $obj->previousYearFlTotal;
		mysqli_free_result($result);

		// Calculate total publications for current year cumulatively through current quarter
		$currentYearStart = $currentYear . "-01-01";
	
		$totalPubs_cumulative = "SELECT COUNT(DISTINCT publication_data.mpid) AS quarterCumulativeTotal FROM faculty_publications INNER JOIN publication_data ON faculty_publications.mpid = publication_data.mpid WHERE faculty_publications.record_valid = 1 AND faculty_publications.internetID IN ($facultyIdList) AND publication_data.pubDate >= '$currentYearStart' AND publication_data.pubDate <= '$endDate'";
		print $totalPubs_cumulative . "\n";

		$result = runQuery($con,$totalPubs_cumulative);
		$obj = mysqli_fetch_object($result);
		$affils[$ind]["cumulativeQuarter"]["totalPublications"] = $obj->quarterCumulativeTotal;
		mysqli_free_result($result);

		// Calculate first/last author publications for current year cumulatively through current quarter
	
		$flPubs_cumulative = "SELECT COUNT(DISTINCT publication_data.mpid) AS quarterCumulativeFlTotal FROM faculty_publications INNER JOIN publication_data ON faculty_publications.mpid = publication_data.mpid WHERE faculty_publications.record_valid = 1 AND faculty_publications.internetID IN ($facultyIdList) AND publication_data.pubDate >= '$currentYearStart' AND publication_data.pubDate <= '$endDate' AND (faculty_publications.authorPosition = faculty_publications.authorCount OR faculty_publications.authorPosition = 1)";
		print $flPubs_cumulative . "\n";
		$result = runQuery($con,$flPubs_cumulative);
		$obj = mysqli_fetch_object($result);
		$affils[$ind]["cumulativeQuarter"]["flPublications"] = $obj->quarterCumulativeFlTotal;
		mysqli_free_result($result);
	}

}

file_put_contents("../reports/deptReports/" . $outputFileName,json_encode($affils,true));
printFile($logFile,"Data recorded; stored in reports/deptReports/" . $outputFileName . "\n");

// Update data source in table
$update_data_source_sql = "UPDATE reports SET report_dataURL = '$outputFileName' WHERE reportID = 'R0001'";
runQuery($con,$update_data_source_sql);

// Record end of process

$eventEnd = date("Y-m-d H:i:s");
printFile($logFile,"Process end: " . $eventEnd . "\n");

$endProcess = "UPDATE events_master SET eventFinish = '$eventEnd' WHERE eventID = $processNumber"; 
runQuery($con,$endProcess);
closeDB($con);

printFile($logFile, "Data update for quarterly department reports complete.\n");
printFile($logFile,"Process end: " . $eventEnd . "\n");

?>
