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

Quick start guide for using Manifold

-->

<div class="page_header">Quick Start Guide</div>
<div class="faq_container">
	<div class="faq_subheader">Getting started</div>
	<div class="faq_content">
You can use Manifold to search for <a href="#search-faculty">individual faculty</a> and <a href="#search-dept">departments</a> or create a <a href="#search-custom">custom subset of faculty</a> from different departments and tracks. The following guide provides a quick start guide to walk you through using Manifold for the first time.

	</div>
</div>

<div class="faq_header">Searching</div>
<div class="faq_container">
	<a name="search-faculty"></a><div class="faq_subheader">Searching for faculty</div>
	<div class="faq_content">
You can search for faculty profiles two different ways in Manifold.<br><br>
On the Manifold search page, clicking on the <b>Quick</b> tab (viewed by default) provides an omnibox that searches the database of available names. If you start typing a faculty member's first or last name, the box will generate a list of potential matches; when you find the faculty of interest, clicking on their name will automatically populate the search box and provide their summary metrics in the space below. Clicking the <b>Click to load full profile...</b> box loads the full profile for the faculty member.<br><br>
Alternatively, clicking on the <b>Dropdown</b> tab on the search page provides two dropdown boxes to select a faculty member. Use the first dropdown box to select the faculty member's department, and then use the second dropdown box to select the faculty member by name. This automatically populates the space below with summary metrics, and from here you can load the full profile.
	</div>
</div>

<div class="faq_container">
	<a name="search-dept"><div class="faq_subheader">Searching for departments</div>
	<div class="faq_content">
You can search for departmental profiles in the same ways you can search for individual faculty profiles.
Under the <b>Quick</b> tab on the Manifold search page, typing the name of the department of interest in the omnibox will generate a list of potential matches. Clicking on the desired departmental result will automatically populate the search box and provide their summary metrics in the space below. The <b>Click to load full profile...</b> box brings you to the full profile of the department.<br><br>
Under the <b>Dropdown</b> tab, select the department of interest using the first dropdown box and then select <b>View department</b> from the second dropdown.
	</div>
</div>

<div class="faq_container">
	<a name="search-custom"><div class="faq_subheader">Creating custom subsets of faculty</div>
	<div class="faq_content">
In some cases, it may be necessary to aggregate statistics for groups of faculty that cut across different departments or tracks. This is made possible in Manifold via the custom subset option.<br><br>
Clicking on the <b>Custom</b> tab on the Manifold main search page provides a series of progressive filters. In the first filter, you can select any (or all) faculty in the Medical School based on appointment type/track; selecting <b>no track</b> filters faculty with <b>any</b> appointment type. In the department filter, you can select different departments to generate a list of affiliated faculty in that department. Clicking on individual names under the faculty filter adds faculty to the subset window; to remove names, click the minus sign for the corresponding name in the subset. When you are done creating your subset, clicking the <b>Load Data</b> button will generate the profile for the faculty defined.
	</div>
</div>

<div class="faq_header">Viewing Profiles</div>
<div class="faq_container">
	<a name="search-faculty"></a><div class="faq_subheader">Modules</div>
	<div class="faq_content">
Each profile is built from a series of modules, primarily <b>Publications</b>, <b>Overview</b>, and <b>Metrics</b>.
	</div>
</div>

<div class="faq_container">
	<a name="search-faculty"></a><div class="faq_subheader">Overview</div>
	<div class="faq_content">
The overview module provides metrics of scholarly impact and output for the profiled entity.<br><br>
In the case of individual faculty profiles, the number next to each metric label (<i>e.g.</i>, <i>h</i>-index, total publication count) indicates the metric for the individual. The chart next to these metrics illustrated boxplot distributions of each given metric for the individual faculty's department; the blue dot indicates the faculty member's position relative to the departmental distribution. Each boxplot has markers for the minimum, lower quartile, median, upper quartile, and maximum values in the department distribution.<br><br>
In the case of department and custom subset profiles, the number next to each metric label indicates the <i>median</i> value of the metric for the defined collection of faculty. This is represented graphically with the blue dot at the median value of the metric in the department or subset distribution.
	</div>
</div>

<div class="faq_container">
	<a name="search-faculty"></a><div class="faq_subheader">Publications</div>
	<div class="faq_content">
Each profile displays publications indexed in Scopus by the profiled entity. For individual faculty profiles, these include publications on which the faculty member is listed as an author. For departments or custom subsets of faculty, these include publications on which any faculty member in the department or subset is listed as an author. More publications can be viewed by changing the year range through the dropdown boxes provided; all publication records are aggregated by calendar year and displayed alphabetically by title.
<br><br>
	Clicking on <b>Download all records</b> automatically generates a spreadsheet with all of the display publication data for further analyses. Clicking on <b>Generate citations</b> populates a window with citations for the displayed publications in the required Medical School CV format.
	</div>
</div>

<div class="faq_container">
	<a name="search-faculty"></a><div class="faq_subheader">Faculty Summary</div>
	<div class="faq_content">
For department and custom subset profiles, the <b>Faculty Summary</b> section provides a table of all faculty in the department or subset and their corresponding metrics, as labeled. Clicking <b>Download all records</b> exports the data in this table to a downloaded spreadsheet.
	</div>
</div>

<div class="faq_container">
	<a name="search-faculty"></a><div class="faq_subheader">Metrics Visualizations</div>
	<div class="faq_content">
	Each profile generates a series of visualizations that provide deeper context for the metrics in the <b>Overview</b> section. Please see the <a href="resources.php?p=faq#about-visualizations">documentation explaining how to interpret the visualizations</a>.
	</div>
</div>

<div class="faq_header">More Questions</div>
<div class="faq_container">
	<div class="faq_content">
	If you have any questions about using Manifold or about the content and data displayed, please consult the <b><a href="resources.php?p=faq">Frequently Asked Questions</a></b> page or submit your question(s) via our <a href="resources.php?p=contact">contact form</a>.
	</div>
</div>
