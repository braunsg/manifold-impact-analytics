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

Generates CUSTOM SEARCH analytics profile

-->

<?php

// Define start and end date parameters for displaying publications
$startDate = intval($startYear) . "-01-01";
$endDate = intval($endYear) . "-01-01";

// Generate custom internet ID list

// Escape all ids before imploding string
foreach($idArray as $idkey => $idvalue) {
	$idArray[$idkey] = mysqli_real_escape_string($con, $idvalue);
}


$internetIdList = "'" . implode('\',\'',$idArray) . "'";

$thisCustomArray = array('faculty' => array(), 'pubs' => array(), 'name' => "Custom Filter");
$pubsArray = array();

$distributions = array("h"=>array(),"hfl"=>array());

$customsql = "SELECT faculty_data.internetID, faculty_data.firstName, faculty_data.lastName, faculty_metrics.hIndex, faculty_metrics.hflIndex, faculty_metrics.pubCount, faculty_metrics.flPubCount, faculty_metrics.totalCitations, faculty_metrics.totalflCitations, faculty_affiliations.affilID FROM faculty_data INNER JOIN faculty_metrics ON faculty_metrics.internetID = faculty_data.internetID INNER JOIN faculty_affiliations ON faculty_affiliations.internetID = faculty_data.internetID WHERE faculty_affiliations.affilClass = 'DISPLAY' AND faculty_data.internetID IN ($internetIdList) ORDER BY faculty_data.lastName ASC";
$result = runQuery($con,$customsql);
while($row = mysqli_fetch_array($result)) {
	$distributions["h"][] = $row['hIndex'];
	$distributions["hfl"][] = $row['hflIndex'];
	
	$thisCustomArray['faculty'][$row['internetID']] = array("firstName" => htmlspecialchars($row['firstName']),
														  "lastName" => htmlspecialchars($row['lastName']),
														  "hIndex" => $row['hIndex'],
														  "hflIndex" => $row['hflIndex'],
														  "pubCount" => $row['pubCount'],
														  "flPubCount" => $row['flPubCount'],
														  "totalCitations" => $row['totalCitations'],
														  "totalflCitations" => $row['totalflCitations'],
														  "deptId" => $row['affilID']
														  );
}

?>

<div id='profile'>

<?php

	$headerArray = array("header" => $thisCustomArray['name']);	
	
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
		
	// List most-cited publications
	drawPublicationList($type,$internetIdList,null,null,null,"top_publications");

	// List all publications
	drawPublicationList($type,$internetIdList,null,$startYear,$endYear,"initialize");

	// List faculty/create faculty summary
	drawFaculty($thisCustomArray['faculty']);

	$metricsData = array("h_distribution" => json_encode($distributions["h"],true),
						"hfl_distribution" => json_encode($distributions["hfl"],true),
						"hMatrix" => array("type"=>"custom","id"=>implode(',',$idArray))
						);
						
	// Create visualizations
	drawMetrics($metricsData);

?>

</div>
