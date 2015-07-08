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

Stylesheet used for PubMed import module

-->

<?php 

	include("../../config/style-defaults.php"); 

	// Table style parameters
	$table_select = "4%";
	$table_date = "8%";
	$table_title = "35%";
	$table_journal = "17%";
	$table_authors = "30%";


?>


	#searchContainer {
		z-index:1000;
		position:absolute;
		top:12.5%;
		left:12.5%;
		width:75%;
		height: 75%;
		padding:15px;
		box-sizing:border-box;
		background-color:rgba(255,255,255,0.9);
		border: 5px solid #8FBCDB;
		overflow-y:scroll;
		overflow-x:hidden;
		padding-top:25px;
	}
	
	
	#resultsContainer {
		display: inline-block;
		margin-top:25px;
	}
		
	#searchFormContainer,
	#resultsContainer {
		width:100%;
		box-sizing:border-box;
	}
	
	.searchFormHeader {
		text-transform:lowercase;
		font-variant:small-caps;
		font-size:1.4em;
		margin:0px;
		padding:0px;
		margin-bottom:5px;
	}
	
	#searchFormContainer {
		width:50%;
		float:left;
	}

	#searchForm {
		padding:5px;
		border: 2px solid #447294;
		display:table;
	}	
	
	#searchFormContainer .title {
		width:50%;
		display: inline-block;
		float:left;
	}
	
	#searchFormContainer .search_row {
		display: table-row;
	}
	
		
	#searchFormContainer .fieldName {
		font-weight:bold;
		display: table-cell;
		width:150px;
		color: #447294;
		font-family: <?php echo $font_default_sans; ?>;
	}
	#searchFormContainer .searchField {
		display:table-cell;
	}
	
	.searchField input {
		width:250px;
	}

	
	
	#descriptionContainer {
		width:45%;
		float:left;
		margin:0px;
		margin-right:15px;
		padding:0px;
		box-sizing:border-box;
	}
	
#results {
	border-collapse:collapse;
	border:none;
	margin:0px;
	padding:0px;
	table-layout:fixed;
}	
	
#resultsHeader {
	width: 100%;
	overflow: auto;
	background-color: #294052;
	color: #ffffff;
	font-size: 0.9em;
	padding: 0px;
	font-family: <?php echo $font_default_sans; ?>;
}

#resultsHeader .date {
	width: <?php echo $table_date;?>;
	padding: 5px;
	text-align:left;
}

#resultsHeader .title {
	width: <?php echo $table_title;?>;
	padding: 5px;
	text-align:left;
}

#resultsHeader .authors {
	width: <?php echo $table_authors;?>;
	padding: 5px;
	text-align:left;
}

#resultsHeader .journal {
	width: <?php echo $table_journal;?>;
	padding: 5px;
	text-align:left;
}

#resultsHeader .select {
	width: <?php echo $table_select; ?>;
 	padding: 2px;
	text-align:center;
	vertical-align: top;
}

#resultsList {
	font-family: <?php echo $font_default_sans; ?>;
	font-size:0.9em;
}

#resultsList .record {
	display: table-row;
	width:100%;
	margin:0px;
	padding:0px;
	vertical-align:top;
	background-color:#fff;
}

#resultsList .date {
	width: <?php echo $table_date;?>;
	padding: 5px;
}

#resultsList .title {
	width: <?php echo $table_title;?>;
	text-align:justify;
	padding: 5px;
}

#resultsList .authors {
	width: <?php echo $table_authors;?>;
	text-align:justify;
	padding: 5px;
}


#resultsList .journal {
	width: <?php echo $table_journal;?>;
	padding: 5px;
	font-style: italic;
	text-align:left;
}

#resultsList .select {
	position:relative;
	width:<?php echo $table_select; ?>;
	height:100%;
	padding:5px 0px 0px 3px;
	text-align:center;
}
	
	#resultsList a:link, a:visited {
		color: #447294;
		text-decoration: none;
	}

	#resultsList a:hover {
		color: steelblue;
		text-decoration: underline;
	}
	
.import_submit {
	width:100%;
	display:inline-block;
	padding:0px;
	margin:0px;
}

.import_submit .import_label,
.import_submit .confirm_label {
	padding:3px;
	border-width:2px;
	border-style:solid;
	float:right;
	font-size: 1.0em;
	font-family: <?php echo $font_default_sans; ?>;
	font-weight:bold;
}

.import_selected,
.confirm_selected {
	border-color:#447294;
	background-color:#CCFFCC;
}

.import_unselected,
.confirm_unselected {
	border-color: #fff;
}

.import_selected a, .import_selected a:link, .import_selected a:visited,
.confirm_selected a, .confirm_selected a:link, .confirm_selected a:visited
 {
	color: #447294;
	text-decoration: none;
	font-weight:bold;
	cursor:pointer;
}

.import_selected:hover,
.confirm_selected:hover {
	color: steelblue;
	text-decoration: underline;
	cursor: pointer;
}

.import_unselected a, .import_unselected a:link, .import_unselected a:visited,
.confirm_unselected a, .confirm_unselected a:link, .confirm_unselected a:visited {
	color:#666666;
	text-decoration: none;
	cursor:default;
}

.import_unselected:hover,
.confirm_unselected:hover {
	cursor:default;
}