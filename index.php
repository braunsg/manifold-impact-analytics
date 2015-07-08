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

This is the landing page for Manifold. From here, profiles can be searched for via name
of faculty or department or a custom-defined subset of faculty.

-->


<!DOCTYPE html>

	<?php 
	$current_page = preg_replace('/\.php$/','',basename(__FILE__));
	$page_controller = $current_page; 
	
	include("inc/templates/components-header.php");

	include("inc/modules/filter/generate-depts.php");
		
	// Generate tenure statuses -- hardcoded, for now
	$appts_array = array("all" => "All",
						 "TEN" => "Tenured",
						 "NTK" => "Tenure Track");

	// Generate array holding data about reports
	$reports_array = array();
	$report_sql = "SELECT reportName, reportDescription, reportURL FROM reports ORDER BY reportID ASC";
	$result = runQuery($con,$report_sql);
	while($row = mysqli_fetch_array($result)) {
		$reports_array[] = array("reportName" => $row['reportName'],
								 "reportDescription" => $row['reportDescription'],
								 "reportURL" => $row['reportURL']);
	}
	mysqli_free_result($result);

?>	
	
<body>
	<?php include("inc/templates/navigation-box.php"); ?>
	<div id="search_container">
		<div id="header_container">
			<div id="header_text">
				<!-- Manifold -->
			</div>
			<div id="subheader_text">
				Impact Analytics
			</div>
			<div id="header_subtext">
				[NAME OF INSTITUTION]
			</div>
				<div id="tabs_container">
					<div class="tab tab_selected" id="quick">Quick</div>
					<div class="tab" id="dropdown">Dropdown</div>
					<div class="tab" id="custom">Custom</div>
					<div class="tab" id="reports">Reports</div>
				</div>

			<div id="searchbox_container_quick">
				<div id="omnibox_container">
					<input type="text" id="omnibox" value="Enter faculty or department name">
				</div>
				<div id="autocompleteContainer"></div>
			</div>
			<div id="searchbox_container_dropdown" class="searchbox_hidden">
				<div id="dropdown_container">
					<div id="dropdown_select_dept">
					Select department
					</div>
					<div id="dropdown_select_faculty" class="dropdown_select">
					&hellip;
					</div>
					<div id="dept-dropdown_results_container">
						<?php

						foreach($depts_array as $affilID => $affilName) {
						?>
						
						<div class="dropdown_result" id="dropdown_result_<?php echo $affilID; ?>"><?php echo $affilName; ?></div>
						
						<?php
						}
						
						?>
					</div>
					<div id="faculty-dropdown_results_container">
					</div>
				</div>
			</div>
			<div id="searchbox_container_custom" class="searchbox_hidden">
				<div id="custom_container">
					<div id="custom_select_filters">
						<div class="custom_select_header">Tenure</div>
						<div class="custom_select_itemlist">
					<?php 
						foreach($appts_array as $code => $description) {
					?>	
					
						<div class="custom_select_item" id="tenure_select_<?php echo $code; ?>"><?php echo $description; ?></div>
					<?php
						}
					?>
						</div>
					</div>
					<div id="custom_select_dept">
						<div class="custom_select_header">Select department</div>
						<div class="custom_select_itemlist">
						<?php

						foreach($depts_array as $affilID => $affilName) {
						?>
						
						<div class="custom_select_item" id="custom_dept_<?php echo $affilID; ?>"><?php echo $affilName; ?></div>
						
						<?php
						}
						
						?>
						</div>
					</div>
					
					<div id="custom_select_faculty">
						<div class="custom_select_header">Select faculty</div>
						<div class="custom_select_itemlist"></div>
					</div>
					
					<div id="custom_select_subset">
						<div class="custom_select_header">Subset</div>
						<div class="custom_select_itemlist"></div>
					</div>
					
					<div id="custom_select_search">Load Data</div>
				</div>				
			</div>
			<div id="searchbox_container_reports" class="searchbox_hidden">
				<div id="reports_container">
				<?php
					foreach($reports_array as $index => $reportInfo) {
				?>
					<div class="report_header"><a href="reports.php?p=<?php echo preg_replace('/\.php$/','',$reportInfo['reportURL']); ?>"><?php echo $reportInfo['reportName']; ?></a></div>
					<div class="report_description"><?php echo $reportInfo['reportDescription']; ?></div>
				<?php
				}
				?>

				</div>
			</div>


			<div id="quickstats_container">
				<div id="quickstats_data">
				</div>
			</div>
		</div>
	</div>
	<div id="about_container">
		<div class="section_header">
		<a name="about-manifold"></a>
		What is Manifold?
		</div>
		<div class="contentContainer">
				<p><b>Manifold</b> is an ongoing software development project that supports University of Minnesota Medical School Dean Brooks Jackson's <b>Scholarship Metrics Initiative</b> by providing publication-driven analytics of the scholarly output and research impact of faculty. Using bibliometric data from <b>Scopus</b>, profiles are generated for Medical School faculty and departments that provide metrics of research impact as well as visualizations to help contextualize those metrics. </p>
		</div>
	</div>
</body>
</html>