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

Stylesheet used for OVERVIEW module

-->

<?php
	// General layout parameters
	$overview = "30%";
	$header = "32%";
	$dataCell = "34%";
	$borderColor = "#8FBCDB";
?>

#summaryData {
	font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif;
	width: <?php echo $overview; ?>;
	margin:0px;
	padding: 0px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	float:right;
	cursor:default;
}

#summaryData:after {
	clear:both;
}

#summaryTable {
	width: 100%;
	border: 1px solid <?php echo $borderColor; ?>;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	display: table;
	table-layout:fixed;
	border-collapse: collapse;
}


#summaryTable .row {
	width: 100%;
	padding: 0px;
	display: table-row;
	margin-bottom: 5px;
	height: 100px;
}


#summaryTable .rowHeader {
	width: <?php echo $header; ?>;
	display: table-cell;
	padding:5px;
	text-align:center;
	font-size: 16px;
	font-weight: bold;
	border:1px solid #8FBCDB;

	
}

#summaryTable .subCell {
	width: <?php echo $data; ?>;
	display: table-cell;
	padding: 0px;
	height: 75px;
	border: 1px solid <?php echo $borderColor; ?>;
}

.subTable {
	width: 100%;
	height: 100%;
	margin: 0px;
	padding: 0px;
	display: table;
	table-layout:fixed;
	border:none;
	border-collapse:collapse;
}

.subTable tr {
	margin: 0px;
	padding: 0px;
}

.subTable .subTable_header {
	width: 100%;
	background-color: #8FBCDB;
	color: #ffffff;
	font-weight: bold;
	letter-spacing: 1px;
	border: none;
	display: table-cell;
	font-size:11px;
	margin: 0px;
	text-align:center;
	height:25px;
}


.subTable .subTable_data_1 {
	display: table-cell;
	padding:0px;
	text-align:center;
	font-size: 25px;
	color: #91DB84;
	border: none;

}

.subTable .subTable_data_2 {
	display: table-cell;
	padding:0px;
	text-align:center;
	font-size: 25px;
	color: #EBC807;
	border: none;

}


