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

Generate quick statistics (metrics) for display on index.php

-->

<?php 

include("../../config/default-config.php");
include("../../functions/default-functions.php");

// Establish connection

$con = connectDB();

// Define variables

$id = $_POST["id"];
$type = $_POST["type"];

switch($type) {
	case "faculty":
		$sql = "SELECT hIndex, hflIndex, pubCount,flPubCount,totalCitations,totalflCitations FROM faculty_metrics WHERE internetID = '$id'";
		$result = runQuery($con,$sql);
		$obj = mysqli_fetch_object($result);
		$dataArray = array("<i>h</i>-index" => $obj->hIndex,
						   "<i>h</i>(<i>fl</i>)-index" => $obj->hflIndex,
						   "Total Publications" => $obj->pubCount,
						   "First/Last Author Publications" => $obj->flPubCount,
						   "Total Citations" => $obj->totalCitations,
						   "First/Last Author Citations" => $obj->totalflCitations);
		break;
	case "dept":
		$fields = array("hIndex" => "Median <i>h</i>-index",
						"hflIndex" => "Median <i>h</i>(<i>fl</i>)-index",
						"pubCount" => "Median Total Publications",
						"flPubCount" => "Median First/Last Author Publications",
						"totalCitations" => "Median Total Citations",
						"totalflCitations" => "Median First/Last Author Citations");

		$dataArray = array();
		
		foreach($fields as $metric => $metric_label) {
			$metricArray = array();

			$sql = "SELECT faculty_metrics." . $metric . " AS metric FROM faculty_metrics INNER JOIN faculty_data ON faculty_data.internetID = faculty_metrics.internetID INNER JOIN faculty_affiliations ON faculty_affiliations.internetID = faculty_data.internetID WHERE faculty_data.status_faculty = 1 AND faculty_data.percentTime >= 0.67 AND faculty_affiliations.affilID = '$id' AND faculty_affiliations.affilClass = 'DISPLAY' AND faculty_data.status_current = 1 ORDER BY metric ASC";
			$result = mysqli_query($con,$sql);
			while($row = mysqli_fetch_array($result)) {
				$metricArray[] = $row['metric'];
			}
			
			$count = count($metricArray);

			$dataArray[$metric_label] = quartile($metricArray,0.5);

			mysqli_free_result($result);	

		}

		break;
}

echo json_encode($dataArray,true);

?>