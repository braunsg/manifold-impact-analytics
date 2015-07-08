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

Generates navigation header/links at the top of each page

-->

<style>
.nav_link {
	font-family: <?php echo $font_default_serif; ?>;
	margin: 0px 6px 0px 6px;
	padding: 10px 0px 0px 0px;
	height:100%;
	color: #fff;
	float:right;
	font-size:1.0em;
}

.nav_link a:link, .nav_link a:visited {
	color: #fff;
	text-decoration:none;
}

.nav_link a:hover {
	text-decoration:underline;
}

#navigation_container {
	height:45px;
	margin:0px;
	padding:0px;
	position:absolute;
	top:0px;
	right: 0px;
	z-index:1000;
}
</style>
<div id="navigation_container">
<?php if($page_controller !== "index") { ?>
<div class="manifold-head"></div>
<?php } ?>
<div class="nav_link"><a href="resources.php?p=contact">Contact</a></div>
<div class="nav_link"><a href="resources.php?p=faq">FAQ</a></div>
<div class="nav_link"><a href="resources.php?p=quickstart">Quick Start</a></div>
<div class="nav_link"><a href="resources.php?p=about">About</a></div>
<div class="nav_link"><a href="index.php">Search</a></div>
</div>
