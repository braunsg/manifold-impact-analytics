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

Generates an array of department names and IDs for use on various pages

-->

<?php

include("../../config/default-config.php");
include("../../functions/default-functions.php");

// Establish mysql database connection
$con = connectDB();

// Pull department data and put into array for easy manipulation
$depts_array = array(); 
$con = connectDB();
$dept_sql = "SELECT umn_zdeptid, umn_deptid, affilName FROM affiliation_data WHERE display = 1 ORDER BY affilName ASC";
$result = runQuery($con,$dept_sql);

while($row = mysqli_fetch_array($result)) {
	$this_id = $row['umn_zdeptid'];
	
	$depts_array[$this_id] = $row['affilName'];
}
mysqli_free_result($result);

if($_GET['return_type'] === 'json') {
	echo json_encode($depts_array);
}
?>