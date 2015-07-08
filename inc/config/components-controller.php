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

Controller that defines function library, stylesheet, and other references used site-wide

-->

<?php


// Define page title(s)

$site_def_titles = array("global" => "University of Minnesota Medical School");

// Define style sheets

$site_def_styles = array("path" => "inc/style/",
				"global" => array("style_layout.php"),
				"profile" => array("style_facultylist.php",
								   "style_publications.php",
								   "style_metrics.php",
								   "style_overview.php",
								   "style_modules.css"),
				"index" => array("style_index.php"),
				"resources" => array("style_resources.php"),
				"cv" => array("style_cv.php"),
				"verify-submission" => array("style_resources.php")
				);

// Define libraries

$site_def_libraries = array("path" => "inc/libraries/",
				   "global" => array("jquery-1.10.2.min.js",
				   					 "d3.v3.min.js")
				  );
				  
// Define functions

$site_def_functions = array("path" => "inc/functions/",
					"global" => array("default-functions.php",
									  "global-functions.js"),
					"profile" => array("profile-functions.php",
									   "layout-functions.js"),
					"admin" => array("profile-functions.php",
									 "layout-functions.js",
									 "admin-functions.js"),
					"index" => array("index-functions.js")
						   );
					

// Define function to act on arrays

function combine_arrays(array $arrays) {
	$combined_array = array();
	foreach($arrays as $array) {
		if(!empty($array) && !is_null($array)) {
			foreach($array as $value) {
				$combined_array[] = $value;
			}
		}
	}
	return $combined_array;
}

?>