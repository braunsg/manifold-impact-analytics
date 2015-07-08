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

Stylesheet used for CV generation page

-->

<?php 

include("../../config/style-defaults.php"); 

?>

#cv_screen_container {
	z-index:1000;
	width:100%;
	height:100%;
	position:absolute;
	top:0px;
	left:0px;
	background-color: rgba(0,0,0,0.8);
	margin:0px;
	padding:0px;
}


#cv_generator_container {
	width: 100%;
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;
	z-index:1000;
	position:absolute;
	top:12.5%;
	left:12.5%;
	width:75%;
	height: 75%;
	margin: 0px;
	padding:25px 15px 15px 15px;
	box-sizing:border-box;
	background-color:rgba(255,255,255,0.9);
	border: 5px solid #8FBCDB;
	overflow-y:scroll;
	overflow-x:hidden;
	padding-top:25px;

}

.cv_section_header {
	font-family: <?php echo $font_default_serif; ?>;
	width:100%;
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;
	font-size:1.4em;
	margin: 15px 0px 15px 0px;
	padding:0px;
	padding-left:25px;
	border-bottom: 2px dashed #CCC;

}

.cv_section_container {
	width: 100%;
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;
	padding-left:25px;
}

.cv_entry_container {
	width:100%;
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;
	padding: 0px 0px 25px 0px;
	margin: 0px;
	display: inline-block;
}

.cv_entry_row {
	font-family: <?php echo $font_default_sans; ?>;
	width: 100%;
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;
	margin: 0px;
	padding:0px;
}

.index, .citation, .citation_count {
	padding: 5px;
	margin: 0px;
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;	
	display: inline;
	float:left;
	display:table-cell;
}

.index {
	width: 5%;
}

.citation {
	width: 80%;	
	text-align:justify
}


.citation_count {
	width: 15%;
	text-align:center;
}

.cv_table_header {
	font-family: <?php echo $font_default_sans; ?>;
	font-size:1.0em;
	text-transform: lowercase;
	font-variant: small-caps;
	text-align: justify;
	font-weight: bold;
	color: steelblue;
}
