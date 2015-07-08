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

Stylesheet used for resources pages (About, FAQ, Contact, etc)

-->

.page_header {
	font-family: <?php echo $font_default_serif; ?>;
	font-size:2.0em;
	width: 100%;
	padding:0px;
	margin: 0px 15px 25px 5px;
	box-sizing: border-box;
	-moz-box-sizing:border-box;
	-webkit-box-sizing:border-box;
}

.faq_header {
	font-family: <?php echo $font_default_serif; ?>;
	font-size:1.4em;
	width: 100%;
	padding:0px;
	margin: 15px 15px 15px 5px;
	border-bottom: 2px dashed #CCC;
	box-sizing: border-box;
	-moz-box-sizing:border-box;
	-webkit-box-sizing:border-box;
	
}

.faq_container {
	width: 100%;
	margin:0px;
	padding:0px;
	box-sizing: border-box;
	-moz-box-sizing:border-box;
	-webkit-box-sizing:border-box;
	display: inline-block;
}	

.faq_subheader {
	font-family: <?php echo $font_default_serif; ?>;
	font-size:1.2em;
	font-style:italic;
	margin: 15px 15px 15px 5px;
	padding: 0px;
	box-sizing: border-box;
	-moz-box-sizing:border-box;
	-webkit-box-sizing:border-box;

}

.faq_content {
	font-family: <?php echo $font_default_sans; ?>;
	font-size:0.9em;
	margin: 5px 5px 5px 35px;
	text-align:justify;
	width:55%;
	padding: 0px;
	box-sizing: border-box;
	-moz-box-sizing:border-box;
	-webkit-box-sizing:border-box;
	float: left;
	display: inline-block;
}

.faq_feature {
	width: 40%;
	margin:5px 5px 5px 15px;
	padding:0px;
	float:left;
	display: inline-block;
}	

.faq_content a:link, .faq_content a:visited {
	font-weight:bold;
}

.faq_content a:hover {
	text-decoration:underline;
}

/* Styles for the contact form */

#contact_form_container {
	width: 30%;
	margin: 5px 5px 5px 35px;
	font-family: <?php echo $font_default_serif; ?>;
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;
}

.contact_form_row {
	width: 100%;
	margin: 0px;
	padding: 0px;
	display: block;
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;

}

.contact_form_fieldlabel {
	width: 100%;
	margin: 0px;
	padding: 5px 15px 5px 0px;
	font-weight:bold;
	text-align:justify;
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;

}

.contact_form_field {
	width: 100%;
	margin: 0px;
	padding: 5px 0px 5px 5px;
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;

}

.contact_submit {
	width:100%;
	margin:0px;
	padding: 5px;
	text-align:center;
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;

}

.textfield {
	width: 100%;
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;

}

.textarea {
	width: 100%;
	height:100px;
	resize:none;
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;

}

.field_required {
	color: red;
	font-size:1.2em;
	margin-left:2px;
}

#message_response {
	width:100%;
	padding:5px;
	margin:15px 0px 15px 0px;
	
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing: border-box;
	border: 2px solid #8FBCDB;
	text-align: justify;
}

.table_of_contents {
	list-style:none;
	font-family: <?php echo $font_default_sans; ?>;
}

.table_of_contents a:link, .table_of_contents a:visited {
	text-decoration:none;
}

.table_of_contents a:hover {
	text-decoration:underline;
}

.resources_slide {
	width:100%;
	margin: 15px 0px 15px 0px;
	padding:0px;
}

.resources_slide img {
	border: 1px solid #000;
}