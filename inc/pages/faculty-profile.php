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

Generates INDIVIDUAL FACULTY profile

-->

<?php

global $internetId;
$internetId = mysqli_real_escape_string($con, $internetId);

?>
<script>
	var user = <?php echo json_encode($internetId); ?>;
</script>
<?php

// Retrieve faculty demographic data
$facultyinfosql = "SELECT faculty_data.firstName, faculty_data.lastName, faculty_data.title, faculty_affiliations.affilID, affiliation_data.affilName, faculty_metrics.hIndex, faculty_metrics.hflIndex, faculty_metrics.pubCount, faculty_metrics.flPubCount, faculty_metrics.totalCitations, faculty_metrics.totalflCitations, faculty_identifiers.idValue, faculty_data.tenure_status, faculty_data.class_description FROM faculty_data, faculty_metrics, faculty_identifiers, faculty_affiliations, affiliation_data WHERE faculty_data.internetID = '$internetId' AND faculty_metrics.internetID = '$internetId' AND faculty_affiliations.internetID = '$internetId' AND faculty_affiliations.affilClass = 'DISPLAY' AND faculty_identifiers.internetID = '$internetId' AND faculty_identifiers.idType = 'scopus_id' AND (faculty_affiliations.affilID = affiliation_data.umn_zdeptid OR faculty_affiliations.affilID = affiliation_data.umn_deptid)";

$result = runQuery($con,$facultyinfosql);

$row = mysqli_fetch_array($result);

	$thisFacultyArray['firstName'] = htmlspecialchars($row['firstName']);
	$thisFacultyArray['lastName'] = htmlspecialchars($row['lastName']);
	$thisFacultyArray['deptName'] = htmlspecialchars($row['affilName']);
	$thisFacultyArray['deptId'] = $row['affilID'];
	$thisFacultyArray['scopusId'] = $row['idValue'];
	$thisFacultyArray['title'] = htmlspecialchars($row['title']);
	$thisFacultyArray['hindex'] = $row['hIndex'];
	$thisFacultyArray['hflindex'] = $row['hflIndex'];
	$thisFacultyArray['pubCount'] = $row['pubCount'];
	$thisFacultyArray['flPubCount'] = $row['flPubCount'];
	$thisFacultyArray['totalCitations'] = $row['totalCitations'];
	$thisFacultyArray['totalflCitations'] = $row['totalflCitations'];

// Define a global variable with department ID for overview descriptive statistics
$dept_grp_id = $thisFacultyArray['deptId'];

if($row['tenure_status'] !== '') {
	$thisFacultyArray['tenure_status'] = $row['tenure_status'];
} else {
	$thisFacultyArray['tenure_status'] = null;
}
if($row['class_description'] !== '') {
	$thisFacultyArray['class_description'] = $row['class_description'];
} else {
	$thisFacultyArray['class_description'] = null;
}

// Get citation counts for ALL papers over entire career trajectory
$citation_data = array();
$citations_sql = "SELECT publication_data.citedByCount, publication_data.pubDate, publication_data.scopus_eid, faculty_publications.authorPosition, faculty_publications.authorCount FROM publication_data INNER JOIN faculty_publications ON faculty_publications.mpid = publication_data.mpid WHERE publication_data.source = 'scopus' AND faculty_publications.record_valid = 1 AND faculty_publications.internetID = '$internetId' ORDER BY publication_data.citedByCount DESC";
$result = runQuery($con,$citations_sql);
if(mysqli_num_rows($result) != 0) {
	$itemCounter = 0;
	while($row = mysqli_fetch_array($result)) {
		$pubDate = $row['pubDate'];
		$citedByCount = $row['citedByCount'];
		$authorPosition = $row['authorPosition'];
		$authorCount = $row['authorCount'];
		if($authorPosition == 1 || $authorPosition == $authorCount) {
			$fl = true;
		} else {
			$fl = false;
		}
		$itemCounter++;
		$citation_data[] = array('rank' => $itemCounter,
								 'eid' => $row['scopus_eid'],
								 'citedByCount' => $citedByCount,
								 'pubDate' => $pubDate,
								 'fl' => $fl
								 );

	}
}

// Retrieve data to generate h/hfl index distribution from department
$distributions = array("h" => array(), "hfl" => array());
$deptsql = "SELECT faculty_metrics.hIndex, faculty_metrics.hflIndex FROM faculty_metrics INNER JOIN faculty_data ON faculty_data.internetID = faculty_metrics.internetID INNER JOIN faculty_affiliations ON faculty_affiliations.internetID = faculty_data.internetID WHERE faculty_affiliations.affilID = '" . $thisFacultyArray['deptId'] . "' AND faculty_data.status_current = 1 AND faculty_data.status_faculty = 1";

$result = runQuery($con,$deptsql);
while($row = mysqli_fetch_array($result)) {
	$distributions["h"][] = $row['hIndex'];
	$distributions["hfl"][] = $row['hflIndex'];
}
mysqli_free_result($result);

// Some basic data
$hIndex = $thisFacultyArray['hindex'];
$hflIndex = $thisFacultyArray['hflindex'];
$totalCitations = $thisFacultyArray['totalCitations'];
$flCitations = $thisFacultyArray['totalflCitations'];

?>
<div id='profile'>

<?php

	$metricsData = array("hIndex" => $hIndex,
						 "hflIndex" => $hflIndex,
						 "h_distribution" => json_encode($distributions["h"],true),
						 "hfl_distribution" => json_encode($distributions["hfl"],true),
						 "hMatrix" => array("type" => "faculty", "id" => $internetId),
						 "firstName" => $thisFacultyArray['firstName'],
						 "deptName" => $thisFacultyArray['deptName'],
						 "citation_data" => json_encode($citation_data,true),
						 "tenure_status" => $thisFacultyArray['tenure_status']
						 );
						 

	$headerArray = array("header" => $thisFacultyArray['firstName'] . " " . $thisFacultyArray['lastName'],
						 "scopusId" => $thisFacultyArray['scopusId'],
						 "subHeader" => array("tenure_status" => $thisFacultyArray['tenure_status'],
											  "class_description" => $thisFacultyArray['class_description'],
											  "title" => $thisFacultyArray['title'],
											  "dept" => $thisFacultyArray['deptName']
											  )
						);
			
			
	$summary_faculty = array("totalCitations" => $totalCitations,
							  "totalflCitations" => $flCitations,
							  "hIndex" => $hIndex,
							  "hflIndex" => $hflIndex,
							  "pubCount" => $thisFacultyArray['pubCount'],
							  "flPubCount" => $thisFacultyArray['flPubCount']);

	// Create page header
	drawHeader($headerArray);
?>

<div id='top_publications'></div>	

<?php

	// Create overview module
	drawSummary($summary_faculty);

?>						

<div id='publications'></div>

<?php
	// List top-cited publications
	drawPublicationList($type,$internetId,$thisFacultyArray['deptId'],null,null,"top_publications");

	// List all publications
	drawPublicationList($type,$internetId,$thisFacultyArray['deptId'],$startYear,$endYear,"initialize");

	// Create visualizations
	drawMetrics($metricsData);
		
	global $data_current_date;
?>
</div>
