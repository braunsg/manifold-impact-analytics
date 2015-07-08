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

Handler for changing faculty DISPLAY department on profile

-->

<?php

// Generates an array of department names and IDs for use on various pages

include("../../config/default-config.php");
include("../../functions/default-functions.php");

// Establish mysql database connection
$con = connectDB();

// Pull department data and put into array for easy manipulation
$internetId = $_POST["internetID"];
$new_deptId = $_POST["new_deptID"];
$sql = "SELECT affilName FROM affiliation_data WHERE affilID = '$new_deptId'";
$result = runQuery($con,$sql);
$obj = mysqli_fetch_object($result);
$new_deptName = $obj->affilName;
mysqli_free_result($result);

$sql = "UPDATE faculty_affiliations SET affilID = '$new_deptId' WHERE internetID = '$internetId' AND affilClass = 'DISPLAY'";
if(!runQuery($con,$sql)) {
	die("Error: " . mysqli_error($con) . "\n");
} else {
	echo $new_deptName . "<a id='change_dept'  href='#'> [Change Department]</a>";
}

?>