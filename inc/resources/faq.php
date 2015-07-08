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

Generates FAQ page for Manifold

-->

<div class="page_header">Frequently Asked Questions</div>

<div class="faq_header">Table of Contents</div>
<ul class="table_of_contents">
	<li><a href="#what-is-manifold">What is Manifold?</a></li>
	<li><a href="#why-built">Why was Manifold built?</a></li>
	<li><a href="#how-different">How is Manifold different from other systems?</a></li>
	<li><a href="#who-include">Who does Manifold include?</a></li>	
	<li><a href="#what-include">What does Manifold include?</a></li>
	<li><a href="#what-not-include">What does Manifold <b>not</b> include?</a></li>
	<li><a href="#where-data-from">Where do the data come from?</a></li>
	<li><a href="#why-publication-missing">Why is my publication missing?</a></li>
	<li><a href="#add-missing-publication">Can I add missing publications?</a></li>
	<li><a href="#where-start">Where do I start?</a></li>
	<li><a href="#what-can-do">What can I do with Manifold?</a></li>
	<li><a href="#manifold-profiles">What is in the profiles?</a></li>
	<li><a href="#about-metrics">About the metrics</a></li>
	<li><a href="#about-visualizations">About the visualizations</a></li>
</ul>

<div class="faq_header">About Manifold</div>
<div class="faq_container">
	<a name="what-is-manifold"></a><div class="faq_subheader">What is Manifold?</div>
	<div class="faq_content">
	Manifold is a web-accessible interface that generates profiles and reports of research impact and scholarly output for faculty and departments in the University of Minnesota Medical School.
	</div>
</div>

<div class="faq_container">
	<a name="why-built"><div class="faq_subheader">Why was Manifold built?</div>
	<div class="faq_content">
	Manifold has been developed to support the Medical School Dean’s Scholarship Metrics Initiative. The system provides a central clearinghouse for administrators and faculty alike for reporting faculty impact measures and evidence of scholarly output.
	</div>
</div>

<div class="faq_container">
	<a name="how-different"></a><div class="faq_subheader">How is Manifold different from other systems?</div>
	<div class="faq_content">
	Like other systems available on campus, including Experts@MN, Manifold provides aggregated lists of publication data for faculty. Unlike other systems, however, Manifold focuses particularly on measures of research impact and scholarly output and the contextualization of these measures.
	</div>
</div>

<div class="faq_container">
	<a name="who-include"></a><div class="faq_subheader">Who does Manifold include?</div>
	<div class="faq_content">
	Manifold generates metrics, data, and profiles for all faculty who are paid, work full time, and have a primary appointment in the Medical School according to payroll records. The system also aggregates faculty based on primary departmental affiliation and provides the same data for each Medical School department.
	</div>
</div>

<div class="faq_container">
	<a name="what-include"></a><div class="faq_subheader">What does Manifold include?</div>
	<div class="faq_content">
	The Scholarship Metrics Initiative covers scholarship specifically in the form of peer-reviewed journal articles. In line with this requirement, Manifold includes bibliometrics on such journal articles authored by Medical School faculty.
	</div>
</div>

<div class="faq_container">
	<a name="what-not-include"></a><div class="faq_subheader">What does Manifold <b>not</b> include?</div>
	<div class="faq_content">
	Other forms of scholarship beyond peer-reviewed journal articles, including but not limited to book chapters and educational materials, are not included in the data.
	</div>
</div>

<a name="about-data"></a><div class="faq_header">About the data</div>

<div class="faq_container">
	<a name="where-data-from"></a><div class="faq_subheader">Where do the data come from?</div>
	<div class="faq_content">
	Data presented in Manifold come principally from Scopus and are automatically downloaded on a regular (quarterly) basis. Faculty also have the option of importing peer-reviewed journal articles from PubMed <b>when those articles are not indexed already by Scopus</b>.
	</div>
</div>

<div class="faq_container">
	<a name="why-publication-missing"></a><div class="faq_subheader">Why is my publication missing?</div>
	<div class="faq_content">
While <a target="_blank" href="http://www.elsevier.com/online-tools/scopus/content-overview">Scopus is quite comprehensive for the physical, health, and life sciences</a>, there are still some journals that it does not index. As a result, articles published in such journals will not appear in Scopus and consequently will not show up in Manifold.
<br><br>
In other cases, an article missing in Manifold may show up in Scopus. When this happens, it is likely due to incorrect author attribution in Scopus itself. There are several possible explanations for this:
<ul>
	<li>There may be multiple author profiles for a faculty member in Scopus that have not yet been merged into a single one,</li>
	<li>An article may be attributed to another author with a shared (usually common) last name, or</li>
	<li>An author’s Scopus profile may correctly capture all of their publications, but the displayed affiliation is incorrect (i.e., not the University of Minnesota).</li>
</ul>
All of these conditions may result in Manifold capturing an incorrect author identifier for a given faculty member, making it likely to miss relevant publications.
<br><br>
In order to correct such errors as they occur, please consult the guide on <a href="resources.php?p=scopus-merge">merging Scopus author profiles</a>. Also, consider <a target="_blank" href="http://orcid.org/">registering for an ORCID</a> to improve author disambiguation; linking your ORCID to your Scopus author profile(s) can make it easier to capture your publication history more completely.
	</div>
</div>

<div class="faq_container">
	<a name="add-missing-publication"></a><div class="faq_subheader">Can I add missing publications?</div>
	<div class="faq_content">
Publication data in Scopus is pulled automatically by scheduled processes and cannot be manually added from that source. However, each individual faculty profile has the ability to import missing publication data from PubMed when that data is not available from Scopus. Please consult the <a href="resources.php?p=pubmed-import">Importing from PubMed guide</a> for more information.
<br><br>
Note that publication data from any other source cannot be manually imported at this time.
	</div>
</div>


<div class="faq_header">Using Manifold</div>

<div class="faq_container">
	<a name="where-start"></a><div class="faq_subheader">Where do I start?</div>
	<div class="faq_content">
	Please consult the <b><a href="resources.php?p=quickstart">Quick Start guide</a></b> for instructions on using Manifold for the first time.
	</div>
</div>

<div class="faq_container">
	<a name="what-can-do"></a><div class="faq_subheader">What can I do with Manifold?</div>
	<div class="faq_content">
	Manifold enables users to search for and view metrics profiles for individual faculty, departments, and custom subsets of faculty filtered by track (e.g., tenured, clinical scholar) and department. Searches load profiles at each of these levels.

	The system also automatically generates spreadsheets of publication data displayed. Faculty can use the system to download citations for their publications in the standardized Medical School CV format.
	</div>
</div>

<div class="faq_container">
	<a name="manifold-profiles"></a><div class="faq_subheader">What is in the profiles?</div>
	<div class="faq_content">
	Each profile type (faculty, department, custom subset) provides three basic modules: a list of publications by the profiled individual or set of faculty (sorted by calendar year of publication), an overview of the distribution of impact metrics for faculty and their affiliate department (displayed as a boxplot), and a series of visualizations that contextualize these metrics. Additionally, the department and custom subset profiles generate a downloadable summary table that provides metrics for all faculty in the department or subset for further analyses.
	</div>
</div>

<a name="about-metrics"></a><div class="faq_header">About the metrics</div>

<div class="faq_container">
	<div class="faq_subheader">Overview</div>
	<div class="faq_content">
	Each profile provides an Overview section that includes the following metrics. These metrics are displayed as 1) a boxplot illustrating the distribution of each metric in the affiliated department and 2) as a blue dot indicating a faculty member’s metric as it falls within the departmental affiliation, where applicable.	<br><br>
	Since Manifold generates content based on Scopus data, <b>all impact metrics, including publication counts, citation counts, and <i>h</i>/<i>h</i>(<i>fl</i>)-index, are calculated using Scopus data</b>, except where noted below.
	</div>
</div>

<div class="faq_container">
	<div class="faq_subheader">h-index</div>
	<div class="faq_content">
		The <b><i>h</i>-index</b>, defined in a 2005 paper by Jorge E. Hirsch, is a number intended to provide a point metric capturing a researcher's productivity and impact as a scholar. Accordingly, a researcher has <i>h</i>-index <i>h</i> if they have published at least <i>h</i> papers each with at least <i>h</i> citations to them. Practically, the <i>h</i>-index is calculated by 
		<ol>
			<li>ordering a researcher's publications in descending order by citation count,</li>
			<li>giving each ordered publication an index value (1, 2, ..., <i>n</i>, where <i>n</i> is the researcher's total number of publications), and</li> 
			<li>determining the highest rank index where the number of citations to the corresponding paper is equal to or greater than the rank index.</li>
		</ol>
	The animation to the right provides a graphical description of this calculation. Click <b>Start</b> to view.<br><br>
	Since it is derived from citation counts to publications, the <i>h</i>-index is highly dependent on the data source from which it is calculated. As a result, a researcher's <i>h</i>-index may vary significantly between major citation indices, most notably Scopus, Google Scholar, and Web of Science.
	</div>
	<div class="faq_feature" id="h_index_demo">
		<?php
		$selection = "h_index_demo";
		include("inc/resources/resources-content/h_index_demo.php"); 
		?>
	</div>


</div>

<div class="faq_container">
	<div class="faq_subheader">h(fl)-index</div>
	<div class="faq_content">
		Similar to the <i>h</i>-index, the <b><i>h</i>(<i>fl</i>)-index</b> is another measure of productivity and impact. It is calculated in the same way as the <i>h</i>-index, except only publications on which a researcher is listed as first or last author are considered in citation counts. 
	</div>
</div>

<div class="faq_container">
	<div class="faq_subheader">Publication count, total</div>
	<div class="faq_content">
		This is the total number of <b>peer-reviewed journal articles</b> published by a researcher. <i>Manifold</i> only considers these publications indexed in Scopus as well as any records that are <b>manually imported from PubMed</b>; publication counts displayed on any given profile reflect data in these sources only, where applicable. <b>Any scholarly works that are <i>not</i> peer-reviewed journal articles are not included in Manifold at this time</b>.
	</div>
</div>

<div class="faq_container">
	<div class="faq_subheader">Publication count, first/last author</div>
	<div class="faq_content">
		This is the total number of peer-reviewed journal articles published by a researcher on which the researcher is listed as first or last author. This count only reflects publications indexed in Scopus and records manually imported from PubMed.
	</div>
</div>

<div class="faq_container">
	<div class="faq_subheader">Citation count, total</div>
	<div class="faq_content">
		The total sum of citations to papers authored by the profiled individual or department. Calculations are based on <b>Scopus</b> citation counts only.
	</div>
</div>

<div class="faq_container">
	<div class="faq_subheader">Citation count, first/last author</div>
	<div class="faq_content">
		The total sum of citations to papers where a faculty member is listed as first or last author only. Calculations are based on <b>Scopus</b> citation counts only.

	</div>
</div>

<a name="about-visualizations"></a><div class="faq_header">About the visualizations</div>

<div class="faq_container">
	<div class="faq_subheader">What is the purpose of these visualizations?</div>
	<div class="faq_content">
While broadly used, impact metrics continue to be incompletely understood across many communities of researchers. While it is tempting to interpret these metrics as comprehensive indicators of a researcher’s scholarly impact and output, there are many caveats in their calculations that should be understood before making comparisons between faculty and departments. In an effort to provide good context for interpretation each profile in Manifold provides a series of visualizations to facilitate more informed analyses of the given metrics.<br><br>
The sections below provide more information about interpreting the visualizations.
	</div>
</div>

<div class="faq_container">
	<div class="faq_subheader">Publications ranked by citation count</div>
	<div class="faq_content">
	An individual's <i>h</i>-index is calculated by ranking their publications in descending order by citation count. This visualization provides a graphical representation of this, showing the top fifty publications based on citation count. Each bar represents a single publication, and the height of the bar corresponds to the number of citations to that publication. Gold bars indicate publications on which the faculty is listed as first or last author. Clicking on a given bar opens a new window or tab linked to the original Scopus record for the respective publication.
	</div>
</div>

<div class="faq_container">
	<div class="faq_subheader">Relative impact of publications over time</div>
	<div class="faq_content">
	One traditional measure of the impact of individual publications is the number of citations to them. In this chart, the relative share of citations for a given publication is represented by size. Each circle represents a single publication by the faculty member and is plotted on the horizontal axis by year of publication and vertical access by citation count. Circles that are larger indicate higher citation counts; clusters of many large circles indicate series of high-impact publications.<br><br>
	Similar to the chart of publications ranked by citation count, circles that are colored gold indicate publications on which the faculty member is listed as first or last author. Clicking on a circle opens a new window or tab linked to the original Scopus record for the respective publication.
	</div>
</div>

<div class="faq_container">
	<div class="faq_subheader">Cumulative h/h(fl)-index over time</div>
	<div class="faq_content">
	The <i>h</i>-index and <i>h</i>(<i>fl</i>)-index can vary significantly between researchers based on length of career and publication history. As a result, it is sometimes useful to assess how a researcher's <i>h</i>-index and <i>h</i>(<i>fl</i>)-index has changed over time. This chart illustrates these changes over the course of a faculty member's career. At each year along the horizontal access, the faculty's <i>h</i>-index (shown in blue) and <i>h</i>(<i>fl</i>)-index (shown in gold) is recalculated based only on publications authored through that year. Note that index calculations at each year are based on <b>current citation counts</b> for respective publications and thus may be slightly skewed if a faculty member's citation distribution is temporally skewed as well.
	</div>
</div>

<div class="faq_container">
	<div class="faq_subheader">Departmental distribution of h/h(fl)-index values</div>
	<div class="faq_content">
	Because publishing cultures tend to vary across different disciplines, it is often useful to see how individual faculty compare to other faculty in their discipline or department. This chart provides a histogram showing the distribution of <i>h</i>-index (shown in blue) and <i>h</i>(<i>fl</i>)-index (shown in gold) values for all faculty in the given department. For individual faculty profiles, the gold vertical line indicates that faculty member's relative <i>h</i>(<i>fl</i>)-index position in the overall distribution; the blue vertical line indicates relative position based on <i>h</i>-index.
	</div>
</div>

<div class="faq_container">
	<div class="faq_subheader">Correlation between h-indices and h-citations</div>
	<div class="faq_content">
	In addition to career length, the <i>h</i>-index of researchers can vary significantly based on the number of publications and total sum of citations attributed to them. In this chart, these differences are highlighted to illustrate that there is not always necessarily a strong correlation between <i>h</i>-index and the impact of a scholar's work. Each circle represents a faculty member in the Medical School and is plotted on the horizontal axis by <i>h</i>-index. On the vertical axis, faculty are plotted according to their <i>h</i>-citation count; this is the total sum of citations to those papers that are counted in the calculation of the <i>h</i>-index (that is, the sum of citations to papers whose citation rank is less than or equal to their <i>h</i>-index, also called the <i>h</i>-core). The relative size of each circle corresponds proportionally to the relative number of publications by a faculty member. As is shown, faculty can have the same <i>h</i>-index but staggeringly different <i>h</i>-citation counts and numbers of publications; for example, two faculty may have the same <i>h</i>-index but one may have a higher <i>h</i>-citation count and fewer number of publications, suggesting they have published fewer, higher-impact journal articles. Likewise, faculty can have nearly the same <i>h</i>-citation count but significantly different <i>h</i>-indices. These correlations help illustrate some of the limitations in interpreting the <i>h</i> index as a comprehensive measure of scholarly output and impact.
	</div>
</div>