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

Generates autocomplete results for NAME/FREE TEXT search on index.php
$_POST data comes from lib/functions/index-functions.js

-->

<?php


include("../../config/default-config.php");
include("../../functions/default-functions.php");

// Establish mysql database connection
$con = connectDB();

// Get posted search terms

$query = isset($_POST['searchTerm']) ? trim(mysqli_real_escape_string($con, $_POST['searchTerm'])) : '';

$faculty_sql = "SELECT faculty_data.internetID, faculty_data.firstName, faculty_data.lastName, faculty_affiliations.affilID FROM faculty_data INNER JOIN faculty_affiliations ON faculty_data.internetID = faculty_affiliations.internetID WHERE (faculty_data.firstName LIKE '%$query%' OR faculty_data.lastName LIKE '%$query%') AND faculty_data.status_faculty = 1 AND faculty_data.percentTime >= 0.67 AND faculty_data.status_current = 1 AND faculty_affiliations.affilClass = 'DISPLAY' ORDER BY faculty_data.lastName ASC";

$dept_sql = "SELECT affilName, umn_zdeptid, umn_deptid FROM affiliation_data WHERE display = 1 AND affilName LIKE '%$query%' ORDER BY affilName ASC";

if (strlen($query) > 0) {
	$faculty_result = runQuery($con,$faculty_sql);
	$dept_result = runQuery($con,$dept_sql);

	print "<div class='autocomplete_header'>Faculty</div>";
	if(mysqli_num_rows($faculty_result) > 0) {
		while($row = mysqli_fetch_array($faculty_result)) {
			$deptArray = explode(',',$row['affilID']);
			print "<div class='autocomplete_result' id='" . $row['internetID'] . "_" . htmlspecialchars($deptArray[0], ENT_QUOTES) . "'>" . htmlspecialchars($row['lastName']) . ", " . htmlspecialchars($row['firstName']) . "</div>";
		}
	} else {
		print "<div class='autocomplete_result' id='noresult'>No faculty results found.</div>";
	}

	print "<div class='autocomplete_header'>Departments</div>";
	if(mysqli_num_rows($dept_result) > 0) {
		while($row = mysqli_fetch_array($dept_result)) {
			$umn_deptid = $row['umn_deptid'];
			$umn_zdeptid = $row['umn_zdeptid'];
		
			print "<div class='autocomplete_result' id='" . $umn_zdeptid . "'>" . htmlspecialchars($row['affilName']) . "</div>";
		}
	} else {
		print "<div class='autocomplete_result' id='noresult'>No department results found.</div>";
	}

	mysqli_free_result($faculty_result);
	mysqli_free_result($dept_result);
}

closeDB($con);

?>
