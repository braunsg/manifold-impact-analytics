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

Stylesheet used for Manifold index page (index.php)

-->

	body,html {
		margin:0px;
		padding:0px;
		position:absolute;
	}
	
	#about_container {
		position:relative;
		width:100%;
		background-color: #496C89;
		padding-bottom:25px;
		margin-top: -50px;
	}

	#updates_container {
		position:relative;
		width:100%;
		background-color: #6789A3;
		padding-bottom:25px;
	}
	

	
	
	.section_header {
		width:100%;
		font-family: <?php echo $font_default_serif; ?>;
		color: #fff;
		font-size:2.0em;
		text-align: center;
		padding: 10px 0px 10px 0px;
		cursor:default;
	}
	
	#search_container {
		position:relative;
		width:100%;
		min-height:100%;
		background-color: #042037;
		box-sizing:border-box;
	}
	
	#header_container {
		position:absolute;
		width:100%;
		height:450px;
		top:50%;
		padding:15px
		box-sizing:border-box;
		margin-top:-225px;
		cursor: default;
	}
	
	
	#header_text {
		text-align:center;
		width:100%;
		height:129px;
		font-size:4.0em;
		color: #fff;
		font-family: <?php echo $font_default_serif; ?>;
		cursor:default;
		background: url("inc/images/manifold-logo.png") center no-repeat;
	}
	
	#subheader_text {
		text-align:center;
		width:100%;
		font-size:1.7em;
		color: #fff;
		font-family: <?php echo $font_default_serif; ?>;
		text-transform:lowercase;
		font-variant: small-caps;
		cursor:default;
		margin-top:-10px;
		margin-bottom:15px;
	}
	
	#header_subtext {
		text-align: center;
		width:100%;
		font-size:1.6em;
		color: #FDD99A;
		font-family: <?php echo $font_default_serif; ?>;
		letter-spacing:1px;
		cursor: default;
	}
	
	#omnibox_container {
		width:100%;
		margin-top:0px;
		text-align:center;
		padding:0px;
	}
	
	
	#omnibox_container input {
		width:50%;
		font-family: <?php echo $font_default_serif; ?>;
		font-size:1.4em;
		color: #333333;
		padding: 10px;
 		border: solid 6px #dcdcdc;
 		font-style:italic;
 		-webkit-box-sizing:border-box;
 		-moz-box-sizing:border-box;
 		box-sizing:border-box;
		transition: box-shadow 0.3s, border 0.3s;	
	}
	
	#omnibox_container input:focus {
		border: solid 6px #707070;
		box-shadow: 0 0 5px 1px #969696;
	}
	
	#searchbox_container_quick,
	#searchbox_container_dropdown,
	#searchbox_container_custom,
	#searchbox_container_reports,
	#searchbox_container_admin
	 {
		width:100%;
		text-align:center;
		padding:0px;
		position:relative;
	}
		
	.searchbox_hidden {
		display:none;
	}
	
	#tabs_container {
		width:50%;
		display: inline-block;
		margin-top:20px;
		margin-bottom:-7px;
		padding: 0px;
		text-align:center;
		margin-left:25%;
	}
	
	#tabs_container:after {
		clear:both;
	}
	
	#tabs_container .tab {
		float: left;
		color: #fff;
		display: inline-block;
		box-sizing:border-box;
		margin:0px;
		margin-right:5px;
		padding:3px;
		font-family: <?php echo $font_default_serif; ?>;
		text-transform: uppercase;
		font-variant:small-caps;
		font-size:0.8em;
		border: 1px solid #dcdcdc;
		border-bottom: none;
		cursor:pointer;
		font-weight:normal;
	}
	
	#tabs_container .tab_selected {
		color:#000;
		background:#fff;
	}
	
	#tabs_container .tab:hover {
		color:#000;
		background:#fff;
	}
	
	#quickstats_container {
		width: 100%;
		padding:0px;
		text-align:center;
		position:relative;
		top: 20px;
		min-height:250px;
		float:left;
	}
	
		
	#quickstats_data {
		position:relative;
		width:100%;
		height:250px;
		left:0px;
		
	}

	.data_box {
		width:125px;
		height: 125px;
		font-family: <?php echo $font_default_serif; ?>;
		margin:0px 2px 0px 2px;
		padding:4px;
		border: 2px solid #042037;
		box-sizing:border-box;
		background-color: rgba(255,255,255,0.8);
		background-color: rgba(255,207,82,0.9);
		display: inline-block;
	}
	
	.special_box {
		/*width:25%;*/
		width:125px;
		height: 125px;
		font-family: <?php echo $font_default_serif; ?>;
		margin:0px;
		padding:4px;
		border: 2px solid #042037;
		box-sizing:border-box;
		background-color: rgba(255,255,255,0.9);
		background-color: #dcdcdc;
		display: inline-block;
		cursor: pointer;
		text-align:center;
	}
	
	.full_profile {
		font-family: <?php echo $font_default_serif; ?>;
		margin:0px;
		padding:4px;
		box-sizing:border-box;
		display: block;
		cursor: pointer;
		text-align:center;
		color:#fff;
		width:300px;
		position:relative;
		left:50%;
		margin-left:-150px;
		margin-top:15px;
		border: 2px solid #fff;
		font-style:italic;
	
	}
	
	.full_profile:hover {
		background-color: steelblue;
	}
	
	.special_content {
		vertical-align:middle;
		text-align: center;
		width:100%;
		height:100%;
		font-weight:bold;
		font-size:1.2em;
	}
	
	.data_header {
		width:100%;
		height:45%;
		font-size:0.8em;
		font-weight: bold;
		text-align:center;
		margin:0px;
		padding: 2px;
		box-sizing:border-box;
	}
	
	.metric {
		width:100%;
		height:55%;
		margin:0px;
		padding: 0px;
		box-sizing:border-box;
		line-height:normal;
		font-size:1.8em;
		text-align:center;
		font-weight:bold;

	}


	#autocompleteContainer {
		width:50%;
		position:absolute;
		z-index:500;
		background: rgba(255,255,255,0.9);
		cursor: default;
		overflow-y: auto;
		overflow-x: hidden;
		display:none;
		margin:0px;
		padding:0px;
		box-sizing:border-box;
		text-align:left;
	}

	.autocomplete_result {
		font-family: <?php echo $font_default_serif; ?>;
		font-size:1.0em;
		width: 100%;
		color:#666;
		margin:0px;
		padding:8px 3px 8px 10px;
		box-sizing:border-box;
	}

	.autocomplete_result:hover {
		background-color: #294052;
		color: #fff;
		cursor:pointer;
	}

	.autocomplete_header {
		font-family: <?php echo $font_default_serif; ?>;
		font-size:1.3em;
		width: 100%;
		color:#fff;
		margin:0px;
		padding:8px 3px 8px 10px;
		box-sizing:border-box;	
		background-color:#16364D;
	}
	
.update .head {
	font-family: <?php echo $font_default_serif; ?>;
	width: 100%;
	font-size:20px;
	font-weight:bold;
	letter-spacing:3px;
	text-align:center;
	text-transform: uppercase;
	margin-bottom:5px;
}

.update .description p {
	width: 100%;
	padding:5px;
	text-align:justify;
	line-height:150%;
	text-indent:0px;
	color: #666;
	font-size:1.0em;
	box-sizing:border-box;
	font-family: <?php echo $font_default_serif; ?>;
}

.contentContainer {
	top: 0px;
	left:50%;
	width: 50%;
	margin-top:0px;
	margin-bottom:0px;
	margin-left:25%;
	padding:20px;
	border: 4px solid #8FBCDB;
	box-sizing:border-box;
	background: rgba(255,255,255,0.95);
}

.contentContainer p {
	text-align:justify;
	line-height:150%;
	text-indent:0px;
	color: #666;
	color: #000;
	font-size:1.0em;
	font-family: <?php echo $font_default_serif; ?>;


}

.contentContainer a:link, .contentContainer a:visited {
	text-decoration:none;
	color: steelblue;
	font-weight:bold;
}

.contentContainer a:hover {
	text-decoration: underline;
}

#dropdown_container {
	width:50%;
	margin-left:25%;
	margin-top:0px;
	text-align:center;
	padding:0px;
}


#dropdown_select_faculty {
	width:38%;
	display: inline-block;
	float:right;
}

#dropdown_select_dept,
#dropdown_select_faculty {
		font-family: <?php echo $font_default_serif; ?>;
		font-size:1.2em;
		color: #333333;
		padding: 10px;
 		border: solid 6px #dcdcdc;
 		font-style:italic;
		text-align:left;
		background:#fff url("inc/images/down_select.png") no-repeat right center;
		margin:0px;
		box-sizing:border-box;
		-moz-box-sizing:border-box;
		-webkit-box-sizing:border-box;
		cursor:pointer;
}

#dropdown_select_dept {
	width:60%;
	display: inline-block;
	float:left;
	padding-right:40px;
}

#dept-dropdown_results_container,
#faculty-dropdown_results_container {
		height:200px;
		position:absolute;
		z-index:500;
		background: rgba(255,255,255,0.9);
		cursor: default;
		overflow: auto;
		display:none;
		margin:0px;
		padding:0px;
		box-sizing:border-box;
		text-align:left;
}


.dropdown_result {
	font-family: <?php echo $font_default_serif; ?>;
	font-size:1.0em;
	width: 100%;
	color:#666;
	margin:0px;
	padding:8px 3px 8px 10px;
	box-sizing:border-box;
}

.dropdown_result:hover {
	background-color: #294052;
	color: #fff;
	cursor:pointer;
}



#custom_container {
	width:50%;
	margin-left:25%;
	margin-top:0px;
	text-align:center;
	padding:0px;
}

#custom_select_dept,
#custom_select_faculty,
#custom_select_subset,
#custom_select_filters,
#custom_select_search
 {
		font-family: <?php echo $font_default_serif; ?>;
		font-size:1.0em;
		color: #333333;
		padding: 3px;
 		border: solid 6px #dcdcdc;
		text-align:left;
		background:#fff;
		margin:0px;
		box-sizing:border-box;
		-moz-box-sizing:border-box;
		-webkit-box-sizing:border-box;
		cursor:pointer;
		height: 225px;
		overflow-y:auto;
		display:inline-block;
}

#custom_select_filters {
	width:12%;
	float:left;
}

.custom_select_itemlist {
		margin:0px;
		padding:0px;
		box-sizing:border-box;
		bottom:0px;
		position:relative;
}

#custom_select_dept,
#custom_select_faculty
 {
	width:30%;
	float:left;
}

#custom_select_subset {
	width: 28%;
	height:195px;
	float: right;
}

#custom_select_search {
	width:28%;
	height: 30px;
	float: right;
	font-weight:normal;
	border-width:4px;
	padding:0px;
	text-align:center;
	vertical-align:middle;
	background-color: #FFFF99;
	
}
#custom_select_search:hover {
	font-weight:bold;
	cursor: pointer;
}

.custom_select_header {
	font-family: <?php echo $font_default_serif; ?>;
	font-size:1.0em;
	width: 100%;
	color:#666;
	margin:0px;
	padding:3px;
	box-sizing:border-box;
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	text-align: center;
	vertical-align: middle;
	font-weight:bold;
	position:static;
	cursor:default;
	
}

.custom_select_item {
	font-family: <?php echo $font_default_serif; ?>;
	font-size:0.8em;
	width: 100%;
	color:#666;
	margin:0px;
	padding:3px;
	box-sizing:border-box;
}

.custom_select_item:hover {
	background-color: #294052;
	color: #fff;
	cursor:pointer;
}

.filter_selected {
	background-color: #294052;
	color: #fff;
	cursor:pointer;
}	

.filter_plus {
	width:15px;
	height:15px;
	background-image:url("inc/images/filter_plus.png");
	display: inline-block;
	margin-left:3px;
	float:right;
}

.filter_minus {
	width:15px;
	height:15px;
	background:url("inc/images/filter_minus.png");
	display: inline-block;
	margin-left:3px;
	float: right;
}

#reports_container {
	width:50%;
	font-family: <?php echo $font_default_serif; ?>;
	font-size:1.0em;
	color: #333333;
	padding: 3px;
	border: solid 6px #dcdcdc;
	text-align:left;
	background:#fff;
	margin:0px;
	box-sizing:border-box;
	-moz-box-sizing:border-box;
	-webkit-box-sizing:border-box;
	height: 225px;
	overflow-y:auto;
	display:inline-block;
}

.report_header,
.report_header a:link,
.report_header a:visited {
	font-size:1.1em;
	width: 100%;
	color:#666;
	margin:0px;
	padding:3px;
	box-sizing:border-box;
	display:block;
}

.report_header:hover,
.report_header a:hover {
	background-color: #294052;
	color: #fff;
	cursor:pointer;
}

.report_description {
	font-size:0.9em;
	width: 100%;
	color:#666;
	margin:0px;
	padding:3px 25px 3px 25px;
	box-sizing:border-box;
	display:block;
	text-align:justify;
}