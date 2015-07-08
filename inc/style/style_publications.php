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

Stylesheet used for publication lists

-->

<?php
	// Table style parameters
	$publications = "68%";

	$table_source = "16px";
	$table_date = "calc(10%-" . $table_source . ")";
	$table_title = "35%";
	$table_journal = "17%";
	$table_authors = "30%";
	$table_citedByCount = "8%";

?>

#publications, #top_publications {
	font-family: <?php echo $font_default_sans; ?>;
	width: <?php echo $publications; ?>;
	margin:0px;
	margin-bottom:25px;
	padding: 0px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	float:left;
}

#publications .tableContainer,
#top_publications .tableContainer {
	width: 100%;
	box-sizing: border-box;
	margin: 0px;
	padding: 0px;
	overflow-y: auto;
	overflow-x: none;
	border-bottom: 4px solid #153450;
}

#publications .tableContainer {
	max-height:500px;
} 

#top_publications .tableContainer {
	max-height:300px;
}

#publications .contentTable,
#top_publications.contentTable {
	width: 100%;
	border-collapse: collapse;
	margin: 0px;
	padding: 0px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}






.publicationsHeader {
	width: 100%;
	overflow: auto;
	background-color: #294052;
	color: #ffffff;
	font-size: 13px;
	padding: 0px;
}

.publicationsHeader .date {
	width: <?php echo $table_date;?>;
	padding: 5px;
	text-align:left;
}

.publicationsHeader .title {
	width: <?php echo $table_title;?>;
	padding: 5px;
	text-align:left;
}

.publicationsHeader .authors {
	width: <?php echo $table_authors;?>;
	padding: 5px;
	text-align:left;
}


.publicationsHeader .citedByCount {
	width: <?php echo $table_citedByCount;?>;
	text-align: center;
	padding: 5px;
}

.publicationsHeader .journal {
	width: <?php echo $table_journal;?>;
	padding: 5px;
	text-align:left;
}

.publicationsHeader .edit {
	width: <?php echo $table_edit; ?>;
 	padding: 2px;
	text-align:center;
	vertical-align: top;
}

.publicationsHeader .source {
	width: <?php echo $table_source; ?>;
 	padding: 2px;
	text-align:center;
	vertical-align: top;
}


.publicationsList {
	width: 100%;
	font-size: 13px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	vertical-align:top;

}

.publicationsList .row1 {
	width: 100%;
<!-- 	background-color: #F9F6F4; -->
	padding: 5px;
	margin: 0px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

.publicationsList .row2 {
	width: 100%;
<!-- 	background-color: #E9E0DB; -->
	padding: 5px;
	margin: 0px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;

}

.publicationsList .date {
	width: <?php echo $table_date;?>;
	padding: 5px;
}

.publicationsList .title {
	width: <?php echo $table_title;?>;
	text-align:justify;
	padding: 5px;
}

.publicationsList .title a {
	font-size:inherit;
}

.publicationsList .authors {
	width: <?php echo $table_authors;?>;
	text-align:justify;
	padding: 5px;
}

.publicationsList .citedByCount {
	width: <?php echo $table_citedByCount;?>;
	padding: 5px;
	vertical-align:top;
	font-size: 20px;
	text-align:center;
	font-style: bold;
}


.publicationsList .journal {
	width: <?php echo $table_journal;?>;
	padding: 5px;
	font-style: italic;
	text-align:left;
}

.publicationsList .edit {
	position:relative;
	width:<?php echo $table_edit; ?>;
	height:100%;
	padding:5px 0px 0px 3px;
	text-align:left;
	cursor: pointer;
}

.publicationsList .source {
	position:relative;
	width:<?php echo $table_source; ?>;
	height:100%;
	padding:5px 0px 0px 3px;
	text-align:left;
}

.publicationsList .editPubsButton {
	position:absolute;
	width:16px;
	height:16px;
	top:5px;
	left:3px;
	margin:0px;
	padding: 0px;
}

.publicationsList a:link, a:visited {
	color: #447294;
	text-decoration: none;
}

.publicationsList a:hover {
	color: steelblue;
	text-decoration: underline;
}

