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

General layout style parameters

-->

body {
	margin:0px;
	padding: 0px;
	overflow-x:hidden;
	width:100%;
	height:100%;	
}

html {
	width: 100%;
	height:100%;
	margin: 0px;
	overflow-x: hidden;
	overflow-y:auto;
}

#topNavBar {
	position:relative;
	width:100%;
	height:50px;
	font-family: <?php echo $font_default_sans; ?>;
	font-size: 18px;
	text-align: left;
	color: #ffffff;
	background-color: #E6F4F7;
	background-color: #042037;
	border-bottom: 5px solid #8FBCDB;
	margin:0px;
	padding:0px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;

}

#splashContainer {
	font-family: <?php echo $font_default; ?>;
	width: 600px;
	height: 400px;
	position:absolute;
	left: 50%;
	margin-left:-300px;
	padding: 0px;
}

#splashContainer .contentContainer {
	width: 100%;
	margin-top:10px;
	margin-bottom:10px;
	padding:10px;
	border: 1px solid #8FBCDB;
	box-sizing:border-box;
}

.contentContainer p {
	text-align:justify;
	line-height:150%;
	text-indent:0px;
	color: #666;
	font-size:14px;


}

#splashContainer .sectionLabel {
	width: 100%;
	font-size:25px;
	letter-spacing: 2px;
	text-align:center;
	font-weight:bold;
	margin-top:20px;
}

.update .head {
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
	font-size:12px;
	box-sizing:border-box;
}

#splashContainer #headerContainer {
	width: 100%;
	height:300px;
	margin-top:5px;
	margin-bottom: 5px;
	box-sizing:border-box;
	
}
		
#loading {
	width: 100%;
	height: 100%;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	position: relative;
	overflow:hidden;
}

.pubLoading {
	width: 100%;
	border: 2px solid #660000;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	padding-top: 15px;
	padding-bottom: 15px;
	background-color: #FFFFCC;
}

.pubLoading .text {
	text-align: center;
	font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif;
	display: block;

}

.loadingBar {
	position: relative;
	left: 50%;
	margin-left: -110px;
	width:220px;
	height: 19px;
	background-image: url(<?php echo "inc/images/bar_loader.gif"; ?>);
	display: block;
}


#deptHeader {
	font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif;
	font-size: 18px;
	text-align: left;
	padding-top: 4px;
	padding-bottom: 4px;
	margin-bottom: 0px;
	padding-left: 5px;
	color: #ffffff;
	background-color: #294052;
}
					


#linkDesc {
	padding-left: 20px;
	font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif;
	font-size: 12px;
	color: gray;
	margin-bottom: 24px;
	text-align: justify;
}
		
#container {
	width: 100%;
	height: 100%;
	margin: 0px;
	padding: 0px;
}


					
#contentPanel {
	width: 100%;
	margin: 0px;
	padding:10px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;

}



#profile {
	font-family: <?php echo $font_default_sans; ?>;
	font-size: 14px;
	margin: 0px; 
	padding: 0px;
	width:100%;
	
}

#headerContainer {
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}


#headerGroup {
	width: 100%;
	margin: 0px;
	padding: 0px;
	cursor: default;
	background-color:#fff;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}

.fixed {
	position: fixed;
	top: 0px;
	overflow: auto;
	z-index:100;
	border-bottom: 2px solid #8FBCDB;
	margin:0px;
	
}


.authorName {
	font-family: <?php echo $font_default_serif; ?>;
	font-size: 50px;
	line-height: 100%;
	color: #447294;
	margin: 0px;
	margin-bottom:10px;
	display: inline-block;
	word-wrap: break-wrap;
}



.editProfile {
	float:right;
	font-weight:bold;
	text-align:right;
	padding-right:5px;
}

.deptName a, .deptName a:link, .deptName a:visited,
.editProfile a, .editProfile a:link, .editProfile a:visited {
	position: relative;
	bottom: 0px;
	right: 0px;
	color: #447294;
	text-decoration: none;
	font-size: 14px;
}

.deptName a:hover, .editProfile a:hover {
	color: steelblue;
	text-decoration: underline;
	cursor: pointer;
}

.data_current_date {
	font-style:italic;
	font-weight: normal;
	display:block;
}

#subHeader {
	width: 100%;
	padding: 0px;
	margin: 0px;
	height: 30px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}


#subHeader .deptName {
	font-size: 14px;
	color: #8FBCDB;
	color: #999999;
	text-indent: 5px;
	float: left;
}

.manifold-head {
	float:right;
	height:45px;
	width:135px;
	color:#000;
	font-family: <?php echo $font_default_serif; ?>;
	font-size:16px;
	letter-spacing:3px;
	margin:0px;
	margin-right:5px;
	padding:0px;
	box-sizing:border-box;
	background: url("inc/images/manifold-logo-small.png") center no-repeat;

	font-size: 2.0em;
	color: #ffffff;
	letter-spacing:0px;
<!-- 	margin-left:10px; -->
display:inline-block;
}

.returnMain {
	float: right;
	padding-right: 2px;
	cursor: pointer;
}

#returnMainButton {
	background-color: #a8ddb5;
	padding: 0px 8px 0px 8px;
	margin:4px 2px 4px 2px;
	font-size: 12px;
	text-align: left;
	color: #000;
	letter-spacing:1px;
	font-weight:bold;
	border: 1px solid #fff;
	vertical-align: middle;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
	height:32px;
	cursor: pointer;
}


.sectionHeader {
	font-family: <?php echo $font_default_sans; ?>;
	font-size: 25px;
	font-weight: bold;
	text-align: left;
	padding: 5px 0px 5px 0px;
	margin: 0px;
	margin-top: 10px;
	color:#153450;
	text-indent: 0px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	clear: both;
	cursor: default;

}

.genericContent {
	font-family: <?php echo $font_default_sans; ?>;
	width: 100%;
	margin: 0px;
	padding: 5px 0px 0px 0px;
	text-indent: 0px;
	font-size: 14px;
	text-align: justify;
}

.genericContent p {
	padding: 0px 15px 0px 15px;
	text-align: justify;
	text-indent: 25px;
}

.genericContent ul li {
	text-indent: 0px;
	text-align: justify;
	padding: 0px 5px 0px 5px;
}

#download {
	height: 100%;
	float: right;
	margin-right: 5px;
}

#download a, a:link, a:visited {
	position: relative;
	bottom: 0px;
	right: 0px;
	color: #447294;
	text-decoration: none;
	font-size: 14px;
}

#download a:hover {
	color: steelblue;
	text-decoration: underline;
	cursor: pointer;
}


.downSort {
	margin-right: 4px;
}

.downSort img {
	width: 16px;
	height: 16px;
	border: 1px solid #fff;
	cursor: pointer;
}

.fig {
	width: 50%;
	border: none;
	margin-left: auto;
	margin-right: auto;
	display: block;
	margin-top: 15px;
	margin-bottom: 15px;
	
}

.yearSelectBox {
	padding: 2px;
	font-size: 0.6em;
/*	font-family: <?php echo $font_default_sans; ?>;*/
	text-align: left;
	color: #000;
/*	border: 0px;*/
	vertical-align: middle;
	margin-left:0px;
	margin-right: 5px;
	cursor:pointer;
}


.profileSelectBox {
	background-color: #294052;
	padding: 5px 1px 5px 1px;
	font-size: 14px;
	text-align: left;
	color: #ffffff;
	border: 0px;
	vertical-align: middle;
	margin-left:0px;
	margin-right: 5px;
	height:30px;
}

.getProfileButton {
	background-color: #294052;
	padding: 0px 8px 0px 8px;
	font-size: 12px;
	text-align: left;
	color: #ffffff;
	border: 1px solid #fff;
	vertical-align: middle;
	margin-left:0px;
	margin-right: 0px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
	height:32px;
	cursor: pointer;
}

#payLabel {
	background-color: #294052;
	padding: 2px;
	font-size: 14px;
	font-weight: bold;
	line-height:100%;
	text-align: left;
	color: #ffffff;
	border: 0px;
	vertical-align: middle;
	margin-left:2px;
	margin-right: 0px;
	display: inline;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	float:left;
	cursor:default;
}	

#apptLabel {
	background-color: #843606;
	padding: 2px;
	font-size: 14px;
	font-weight: bold;
	line-height:100%;
	text-align: left;
	color: #ffffff;
	border: 0px;
	vertical-align: middle;
	margin-left:2px;
	margin-right: 0px;
	display: inline;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	float:left;
	cursor:default;
}	

/* Tab navigation box styles */

	#tabbox_container {
		position:absolute;
		z-index:1000;
		width:200px;
		top:0px;
		right:0px;
		/*margin:0px 20px 0px 0px;*/
		padding:0px;
		-webkit-box-sizing:border-box;
		-moz-box-sizing:border-box;
		box-sizing:border-box;
		
	}
	
	#tabbox_header {
		width:100%;
		height:45px;
		margin:0px;
		padding:0px;
	}
	#tabbox_container #tab_image {
		position:relative;
		right:0px;
		top:7px;
		cursor:pointer;
		display:inline-block;
		margin:0px;
		padding:0px;
		float:right;
	}
	
	#tab_image:after {
		clear:both;
	}
	
	#tabbox_list {
		display:inline-block;
		-webkit-box-sizing:border-box;
		-moz-box-sizing:border-box;
		box-sizing:border-box;
		width:75%;
		float:right;
		margin-top:5px;
		visibility:hidden;
		background-color: rgba(255,255,255,0.95);
		border:none;	
/*		border-right: 5px solid #cecece;	*/
border: 1px solid #cecece;
border-right:none;
border-top:none;
		text-align:right;
	}

	
	
	#tabbox_list ul {
		text-indent:0px;
		width:100%;
		list-style-type:none;
		margin:0px;
		padding:0px;
		-moz-box-sizing:border-box;
		-webkit-box-sizing:border-box;
		box-sizing:border-box;
	}
	
	#tabbox_list li {
		padding:4px;
		margin:4px;
		/*background: rgba(255,255,255,0.9);*/
		color: #666;
		font-family: <?php echo $font_default_serif; ?>;
		font-size:1.2em;
		box-sizing:border-box;
		-webkit-box-sizing:border-box;
		-moz-box-sizing:border-box;
	}
	
	#tabbox_list li:hover {
		background-color:#666;
	}

	#tabbox_list li:hover a {
		color: #fff;
	}
			
	#tabbox_list a:link, #tabbox_list a:visited {
		text-decoration:none;
		color: #666;
	}


/* Miscellaneous style declarations */


#generate_cv_link {
	margin-left:8px;
}

#close_window {
	width:100%;
	height:32px;
	background-color: #fff;
	position:absolute;
	top:0px;
	left:0px;
	margin:0px;
	padding:0px;
}

img#close {
	cursor:pointer;
	margin:0px;
	padding:0px;
}

#popout_screen_container {
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


img.info_bubble {
	display:inline-block;
	width:20px;
	height:20px;
	margin:0px 5px 0px 0px;
	padding:0px;
	vertical-align:center;
	border:none;
}

.vis_no_display {
	width:100%;
	box-sizing:border-box;
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	font-family: <?php echo $font_default_sans; ?>;
	font-size:1.3em;
	text-align:center;
	vertical-align:middle;
	font-style:italic;
	position:relative;
	top:50%;
}