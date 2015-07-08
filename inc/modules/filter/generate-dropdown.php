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

Generates dropdown for results on index search page when doing DEPARTMENT search
$_POST data comes from lib/functions/index-functions.js

-->

<?php

include("../../config/default-config.php");
include("../../functions/default-functions.php");

// Establish mysql database connection
$con = connectDB();

// Get posted search terms

$affilId = isset($_POST['affilID']) ? trim(mysqli_real_escape_string($con, $_POST['affilID'])) : "";
$type = isset($_POST['type']) ? $_POST['type'] : "dropdown";

if($type === "dropdown") {
	$class = 'dropdown_result';
	$idBase = 'dropdown_result_';
	$plus = "";
} else if($type === "custom") {
	$class = 'custom_select_item';
	$idBase = 'custom_faculty_';
	$tenure_code = $_POST['filter'];
	$plus = "<div class='filter_plus'></div>";
}


if($tenure_code === "TEN" || $tenure_code === "NTK") {
	switch($tenure_code) {
		case "TEN":
			$tenure_value = "Tenured";
			break;
		case "NTK":
			$tenure_value = "Tenure Track";
			break;
	}
	$filter_query = "AND faculty_data.tenure_status = '$tenure_value' ";
} else {
	$filter_query = "";
}

$faculty_sql = "SELECT faculty_data.internetID, faculty_data.firstName, faculty_data.lastName, faculty_affiliations.affilID FROM faculty_data INNER JOIN faculty_affiliations ON faculty_data.internetID = faculty_affiliations.internetID WHERE faculty_data.status_current = 1 AND faculty_data.status_faculty = 1 AND faculty_data.percentTime >= 0.67 AND faculty_affiliations.affilID = '$affilId' AND faculty_affiliations.affilClass = 'DISPLAY' " . $filter_query . "ORDER BY faculty_data.lastName ASC";

$faculty_result = runQuery($con,$faculty_sql);

if(mysqli_num_rows($faculty_result) > 0) {
	if($type === "dropdown") {
		print "<div class='$class' id='" . $idBase  . $affilId . "'>View department</div>";
	}
	while($row = mysqli_fetch_array($faculty_result)) {
		$deptArray = explode(',',$row['affilID']);

		print "<div class='$class' id='" . $idBase . $row['internetID'] . "_" . $deptArray[0] . "'>" . $plus . $row['lastName'] . ", " . $row['firstName'] . "</div>";
	}
}

mysqli_free_result($faculty_result);

closeDB($con);

?>
