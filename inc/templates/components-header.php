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

Loads default configuration files needed in profile page headers

Serves as the HTML head for each page
-->

<head>
	<?php include("inc/config/components-controller.php"); ?>
	<?php include("inc/config/default-config.php"); ?>
	<?php 
		if(!$page_controller) {
			$page_controller = $current_page;
		}
	?>
		
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo $site_def_titles["global"]; ?></title>

	<!-- Load libraries -->
	<?php 
	foreach(combine_arrays(array($site_def_libraries["global"],(($var = $site_def_libraries[$page_controller]) != false ? $var : null))) as $filename) { 
		$path = $site_def_libraries["path"];
	?>
		<script src="<?php echo $path . $filename; ?>"></script>	
	<?php } //endforeach ?>

	<!-- Load functions -->
	<?php foreach(combine_arrays(array($site_def_functions["global"],(($var = $site_def_functions[$page_controller]) != false ? $var : null))) as $filename) { 
		$path = $site_def_functions["path"];

		if(stristr($filename,".php")) {
			require_once($path . $filename);
		} else if(stristr($filename,".js")) {
	?>
		<script src="<?php echo $path . $filename; ?>"></script>	
	<?php 
		} // endif
	} // endforeach
	?>

	<!-- Load stylesheets -->
	<?php foreach(combine_arrays(array($site_def_styles["global"],(($var = $site_def_styles[$page_controller]) != false ? $var : null))) as $filename) {
		$path = $site_def_styles["path"];
		$style_id = basename($filename);
	?>
		<style id="<?php echo $style_id; ?>"><?php include($path . $filename); ?></style>
	<?php
	} ?>
</head>