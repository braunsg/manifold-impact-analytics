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

Handler for generating profile views (faculty, department, or custom)

-->

<!DOCTYPE html>
<?php
$current_page = preg_replace('/\.php$/','',basename(__FILE__));
$page_controller = $current_page; 

include("inc/templates/components-header.php");
include("inc/templates/page-template-header.php");

// Define start and end periods for publications
$type = $_GET['type'];
if($_GET['startYear']) {
	$startYear = $_GET['startYear'];
} else {
	$startYear = date("Y");	// default to current year
}

if($_GET['endYear']) {
	$endYear = $_GET['endYear'];
} else {
	$endYear = date("Y");	// default to current year
}


// Initialize database connection
$con = connectDB();

// Generate profile content
if($type === "faculty") {
	$internetId = $_GET['id'];
	include("inc/pages/faculty-profile.php");
} else if($type === "dept") {
	$deptId = $_GET['id'];
	include("inc/pages/dept-profile.php");	
} else if($type === "custom") {
	$idArray = explode(",",$_GET["id"]);
	include("inc/pages/custom-profile.php");
}

// Close database connection
closeDB($con);

include("inc/templates/footer.php");

?>