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

Configuration file that defines database parameters, the Scopus API key used to query the API, and other site parameters

-->

<?php


// Performance parameters

date_default_timezone_set('America/Chicago');
ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 0);
error_reporting(E_ERROR);
ini_set('auto_detect_line_endings',true);

// API key for Scopus API
// Retrieve at http://dev.elsevier.com/myapikey.html
$apiKey = '';

// Database configuration
$dbhost = "localhost";
$dbuser = "";
$dbpw = "";
$dbname = "manifold";

// Site variables

$site_def_creator = "[NAME OF INSTITUTION]";	// Label used for attributions
$site_def_external_baseurl = "http://manifold.localhost/";

// SMTP configuration

$smtp = array('host' => 'smtp.example.com',
			  'user' => 'manifold',
			  'pw' => '');

// Directory structure

$vis_root_path = "inc/visualizations/";
$reports_root_path = "inc/reports/";

// Style defaults

include("inc/config/style-defaults.php");

?>
