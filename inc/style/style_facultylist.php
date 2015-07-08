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

Stylesheet used for faculty summary tables

-->

<?php
	// Table style parameters
	$faculty = "68%";
	$name = "60px";
	$h_index = "35px";
	$hfl_index = "55px";
	$citation_count = "80px";
	$pub_count = "80px";
	$fl_pub_count = "80px";
	$fl_citation_count = "95px";
// 	$this_period_pub_count = "80px";
	$action = "20%";

?>

#faculty {
	font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif;
	width: <?php echo $faculty; ?>;
	margin:0px;
	padding: 0px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	float:left;
	border: 0px;

}

#faculty .tableContainer {
	width: 100%;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	margin: 0px;
	padding: 0px;
	overflow-y: auto;
	overflow-x: none;
	max-height: 500px;
	border-bottom: 4px solid #153450;

}

#faculty .contentTable {
	width: 100%;
	margin: 0px;
	padding: 0px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	border-collapse: collapse;
}


#facultyHeader {

	width: 100%;
	overflow: hidden;
	background-color: #294052;
	color: #ffffff;
	font-size: 13px;
	padding: 0px;	
	line-height: 100%;
	border-collapse: collapse;
}

#facultyHeader .name {
	width: <?php echo $name;?>;
	padding: 5px;
	vertical-align:bottom;
	text-align: left;
}

#facultyHeader .name a:link, a:visited {
	text-decoration:none;
	font-weight:bold;

}

#facultyHeader .indices {
	width: <?php echo (str_replace("px","",$h_index) + str_replace("px","",$hfl_index)) . "px"; ?>;
	text-align: center;
	vertical-align:bottom;
	font-weight: bold;
	letter-spacing: 1px;
	border-bottom: 1px solid #ffffff;
	border-left:0px solid #ffffff;
	border-right:1px solid #ffffff;
	padding-top:3px;
	padding-bottom:3px;
}
#facultyHeader .publications {
	width: <?php echo (str_replace("px","",$pub_count) + str_replace("px","",$fl_pub_count)) . "px"; ?>;
	text-align: center;
	vertical-align:bottom;
	font-weight: bold;
	letter-spacing: 1px;
	border-bottom: 1px solid #ffffff;
	border-left:1px solid #ffffff;
	border-right:1px solid #ffffff;
	padding-top:3px;
	padding-bottom:3px;

}
#facultyHeader .citations {
	width: <?php echo (str_replace("px","",$citation_count) + str_replace("px","",$fl_citation_count)) . "px"; ?>;
	text-align: center;
	vertical-align:bottom;
	font-weight: bold;
	letter-spacing: 1px;
	border-bottom: 1px solid #ffffff;
	border-left:1px solid #ffffff;
	padding-top:3px;
	padding-bottom:3px;
	border-right:1px solid #ffffff;
}

#facultyHeader .tableBlock {
	width: 100%;
	margin: 0px;
	padding: 0px;
	border: none;
}

.tableBlock .tableHeaderText {
	font-style: italic;
	text-align:center;

}

#facultyHeader .h_index {
	width: <?php echo $h_index;?>;
	padding: 5px;
	font-style: italic;
	border-left:1px solid #ffffff;
	text-align:center;

}

#facultyHeader .hfl_index {
	width: <?php echo $hfl_index;?>;
	padding: 5px;
	font-style: italic;
	text-align: center;
	border-right: 1px solid #ffffff;
}

#facultyHeader .citation_count {
	width: <?php echo $citation_count;?>;
	padding: 5px;
	text-align: center;

}

#facultyHeader .pub_count {
	width: <?php echo $pub_count;?>;
	padding: 5px;
	text-align: center;

}

#facultyHeader .fl_pub_count {
	width: <?php echo $fl_pub_count;?>;
	padding: 5px;
	text-align: center;
	border-right:1px solid #ffffff
}

#facultyHeader .fl_citation_count {
	width: <?php echo $fl_citation_count;?>;
	padding: 5px;
	text-align: center;
	border-right:1px solid #ffffff;
}

#facultyHeader .this_period_pub_count {
	width: <?php echo $this_period_pub_count;?>;
	padding: 5px;
	text-align: center;

}

#facultyHeader .action {
	width: <?php echo $action;?>;
	padding: 5px;
	vertical-align:bottom;
	text-align:left;
}

#facultyList {
	width: 100%;
	font-size: 16px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	border-spacing: 0px;
	border-collapse: collapse;

}

#facultyList .row1 {
	width: 100%;
	background-color: #F9F6F4;
	padding: 5px;
	margin: 0px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;

}


#facultyList .row2 {
	width: 100%;
	background-color: #E9E0DB;
	padding: 5px;
	margin: 0px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;

}


#facultyList .name {
	width: <?php echo $name;?>;
	padding: 5px;

}

#facultyList .h_index {
	width: <?php echo $h_index;?>;
	text-align: center;
	padding: 5px;

}

#facultyList .hfl_index {
	width: <?php echo $hfl_index;?>;
	padding: 5px;
	text-align: center;

}

#facultyList .citation_count {
	width: <?php echo $citation_count;?>;
	padding: 5px;
	text-align: center;

}

#facultyList .pub_count {
	width: <?php echo $pub_count;?>;
	padding: 5px;
	text-align: center;

}

#facultyList .fl_citation_count {
	width: <?php echo $fl_citation_count;?>;
	padding: 5px;
	text-align: center;

}

#facultyList .fl_pub_count {
	width: <?php echo $fl_pub_count;?>;
	padding: 5px;
	text-align: center;

}

#facultyList .this_period_pub_count {
	width: <?php echo $this_period_pub_count;?>;
	padding: 5px;
	text-align: center;

}

#facultyList .action {
	width: <?php echo $action;?>;	
	padding: 5px;

}
