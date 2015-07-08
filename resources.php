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

Handler for loading help resources (About, FAQ, contact form, etc.)

-->

<!DOCTYPE html>
<?php
$current_page = preg_replace('/\.php$/','',basename(__FILE__));

include("inc/templates/components-header.php");
include("inc/templates/page-template-header.php");

// Generate page content

$type = isset($_GET['p']) ? $_GET['p'] : "";

if (preg_match('/^[a-z0-9_-]+$/i', $type) && file_exists(__DIR__ . '/inc/resources/' . $type . '.php')) {
	include("inc/resources/" . $type . ".php");
}
else {
	echo "Unknown page";
}

include("inc/templates/footer.php");

?>
