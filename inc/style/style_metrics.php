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

Stylesheet used for metrics visualizations

-->

<?php
	// General layout parameters
	$metrics = "100%";
?>

#metrics {
	font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif;
	width: <?php echo $metrics; ?>;
	margin:0px;
	padding: 0px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

#metrics:after {
	content: '';
	width:100%;
	clear: both;
}

#metrics .feature {
	width: 100%;
	height: 410px;
	padding: 0px;
	margin-bottom: 10px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

.feature .featureBox {
	width: 75%;
	height: 100%;
	font-size: 13px;
	padding: 0px;	// 5px
	margin-left: 15px;
	border: 1px solid #8FBCDB;
	display: inline-block;
	float:right;
}

.feature .featureDescription {
	width: 23%;
	height: 100%;
	border: 1px solid #8FBCDB;
	padding: 0px;	// 5px
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	display: inline-block;
	float:left;
}

.featureDescription .content {
	width: 100%;
	height: 100%;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	background-color: #E6F4F7;
	padding:10px;
}

.content .title {
	font-size:20px;
	font-weight: bold;
	text-align: left;
	margin-bottom: 5px;
}

.content .text {
	font-size: 14px;
	font-weight: normal;
	text-align: justify;
}