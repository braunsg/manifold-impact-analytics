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

Generates DEPARTMENT analytics profile

-->

<?php

// Define start and end dates for displaying publications

$startDate = intval($startYear) . "-01-01";
$endDate = intval($endYear) . "-01-01";

$deptId = mysqli_real_escape_string($con, $deptId);
// Define a global variable with department ID for overview descriptive statistics
$dept_grp_id = $deptId;


$thisDeptArray = array('faculty' => array(), 'pubs' => array());
$pubsArray = array();
$orgasql = "SELECT affilName FROM affiliation_data WHERE (umn_zdeptid = '$deptId' OR umn_deptid = '$deptId')";
$result = runQuery($con,$orgasql);
$row = mysqli_fetch_array($result);
$thisDeptArray['name'] = $row['affilName'];
$distributions = array("h"=>array(),"hfl"=>array());

$deptsql = "SELECT faculty_data.internetID, faculty_data.firstName, faculty_data.lastName, faculty_metrics.hIndex, faculty_metrics.hflIndex, faculty_metrics.pubCount, faculty_metrics.flPubCount, faculty_metrics.totalCitations, faculty_metrics.totalflCitations FROM faculty_data INNER JOIN faculty_metrics ON faculty_metrics.internetID = faculty_data.internetID INNER JOIN faculty_affiliations ON faculty_affiliations.internetID = faculty_data.internetID WHERE faculty_affiliations.affilID = '$deptId' AND faculty_affiliations.affilClass = 'DISPLAY' AND faculty_data.status_faculty = 1 AND faculty_data.percentTime >= 0.67 AND faculty_data.status_current = 1 ORDER BY faculty_data.lastName ASC";
$result = runQuery($con,$deptsql);
while($row = mysqli_fetch_array($result)) {
	$distributions["h"][] = $row['hIndex'];
	$distributions["hfl"][] = $row['hflIndex'];
	
	$thisDeptArray['faculty'][$row['internetID']] = array("firstName" => htmlspecialchars($row['firstName']),
														  "lastName" => htmlspecialchars($row['lastName']),
														  "hIndex" => $row['hIndex'],
														  "hflIndex" => $row['hflIndex'],
														  "pubCount" => $row['pubCount'],
														  "flPubCount" => $row['flPubCount'],
														  "totalCitations" => $row['totalCitations'],
														  "totalflCitations" => $row['totalflCitations'],
														  "thisPeriodPubCount" => 0
														  );
}


$internetIdList = "'" . implode('\',\'',array_keys($thisDeptArray['faculty'])) . "'";


?>

<div id='profile'>

<?php

	$headerArray = array("header" => $thisDeptArray['name']);
	
	// Create page header	
	drawHeader($headerArray);							
?>

	<div id='top_publications'></div>

<?php

	// Create overview module
	drawSummary();
	
	
?>

	<div id='publications'></div>

<?php
	
	// List top-cited publications for department
	drawPublicationList($type,null,$deptId,null,null,"top_publications");
	
	// List all publications for department
	drawPublicationList($type,null,$deptId,$startYear,$endYear,"initialize");

	// List faculty/create faculty summary
	drawFaculty($thisDeptArray['faculty']);

	$metricsData = array("h_distribution" => json_encode($distributions["h"],true),
						"hfl_distribution" => json_encode($distributions["hfl"],true),
						"hMatrix" => array("type"=>"dept","id"=>$deptId)
						);
						
	// Create visualizations
	drawMetrics($metricsData);

?>

</div>
