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

Defines PHP/JavaScript/jQuery functions used to generate profiles

-->

<?php


// Draws the HEADER for the profile
function drawHeader($array) {
	global $type;
	global $con;

	// Get last data update
	$sql = "SELECT date_threshold FROM events_master WHERE eventType = 'publication_data_full_update' ORDER BY eventID DESC LIMIT 1";
	$result = runQuery($con,$sql);
	$obj = mysqli_fetch_object($result);
	$data_current_date = date("Y/m/d",strtotime($obj->date_threshold));
	mysqli_free_result($result);
	
?>
	<script>
	/* Pop the data current date variable into Javascript var for use in PubMed search */
	var data_current_date = '<?php echo $data_current_date; ?>';
	</script>
	
	<div id='headerContainer'>
	<div id='headerGroup'>
		<div class='authorName'><?php echo $array['header']; ?></div>
		<div class='editProfile'>
			<span class='data_current_date'>Data current through <?php echo $data_current_date; ?></span>
	<?php
		if($type === "faculty") {
	?>
			<a target="_blank" href="#" id="pubmed_import">Import from PubMed</a><br>	
			<a target="_blank" href="http://www.scopus.com/authid/detail.url?authorId=<?php echo $array['scopusId']; ?>">View Scopus Profile</a><br>
	<?php
		}
	?>
		</div>
		<div id='subHeader'>

<?php

	if($subheader = $array['subHeader']) {
			if($subheader['tenure_status'] != null) {
?>
				<div id='apptLabel'><?php echo $subheader['tenure_status']; ?></div>
<?php
			}
?>
			<div class='deptName'><?php echo $subheader['title'] . " &#8226; "; ?></div><div class='deptName' id='deptLabel'><?php echo $subheader['dept']; ?> <a id="change_dept"  href="#">[Change Department]</a></div>
<?php
	}
?>
		</div>	<!-- end subHeader -->
	</div> <!-- end headerGroup -->
	</div>	<!-- end headerContainer -->

<?php
}

// Draw the OVERVIEW module (impact metrics/descriptive statistics)
function drawSummary($profile_data_faculty) {
	global $type;
	global $con;
	if($type === "faculty" || $type === "dept") {
		global $dept_grp_id;
	} else if($type === "custom") {
		global $internetIdList;
	}
	// Generate descriptive statistics for department

	$fields = array("Indices" => array("hIndex" => "h-index",
									   "hflIndex" => "h(fl)-index"),
					"Publication Counts" => array("pubCount" => "Total",
												  "flPubCount" => "First/Last"),
					"Citation Counts" => array("totalCitations" => "Total",
											   "totalflCitations" => "First/Last"));

	$profile_data_dept = array();

	foreach($fields as $category => $subcategories) {

		$profile_data_dept[$category] = array();
		foreach($subcategories as $metric => $metric_label) {
			$metricArray = array();
			$profile_data_dept[$category][$metric] = array();
			$dataPointer = &$profile_data_dept[$category][$metric];
			if($type === "faculty" || $type === "dept") {
				$sql = "SELECT faculty_metrics." . $metric . " AS metric FROM faculty_metrics INNER JOIN faculty_data ON faculty_metrics.internetID = faculty_data.internetID INNER JOIN faculty_affiliations ON faculty_affiliations.internetID = faculty_data.internetID WHERE faculty_data.status_faculty = 1 AND faculty_data.percentTime >= 0.67 AND faculty_data.status_current = 1 AND faculty_affiliations.affilID = '$dept_grp_id' AND faculty_affiliations.affilClass = 'DISPLAY' ORDER BY metric ASC";
			} else if($type === "custom") {
				$sql = "SELECT faculty_metrics." . $metric . " AS metric FROM faculty_metrics INNER JOIN faculty_data ON faculty_metrics.internetID = faculty_data.internetID WHERE faculty_data.status_faculty = 1 AND faculty_data.percentTime >= 0.67 AND faculty_data.status_current = 1 AND faculty_data.internetID IN ($internetIdList) ORDER BY metric ASC";
			
			}

			$result = mysqli_query($con,$sql);
			while($row = mysqli_fetch_array($result)) {
				$metricArray[] = $row['metric'];
			}
			$count = count($metricArray);
			$dataPointer["min"] = $metricArray[0];
			$dataPointer["max"] = $metricArray[$count-1];
			$dataPointer["median"] = quartile($metricArray,0.5);
			$dataPointer["lower_quartile"] = quartile($metricArray,0.25);
			$dataPointer["upper_quartile"] = quartile($metricArray,0.75);
			$dataPointer["label"] = $metric_label;

			mysqli_free_result($result);	

		}
	}	
	
	
?>
	<div id='summaryData'>
		<div class='sectionHeader'><a target='_blank' href='resources.php?p=faq#about-metrics'><img class='info_bubble' src='inc/images/info_bubble.png'></a>Overview</div>
		<?php include("inc/visualizations/summary_data.php"); ?>
	</div> <!-- end summaryData -->
<?php
}

// Generates publication lists for profiles
function drawPublicationList($type,$internetId,$deptId,$startYear,$endYear,$action) {
	if($action === "top_publications") {
	?>

		<script>
		var type = <?php echo json_encode($type); ?>;
		var internetId = <?php echo json_encode($internetId); ?>;
		var dept = <?php echo json_encode($deptId); ?>;
		var actionMode = <?php echo json_encode($action); ?>;
		$.post("inc/modules/ajax/draw-publication-list.php",{postType: type, postInternetId: internetId, postDept: dept, action: actionMode}, function(data) {
			$("#top_publications").html(data);
		}); 	

		</script>

	
	<?php
	} else {
	?>
	
		<script>
		var loading = "<div class='pubLoading'><span class='text'>Loading publication data...please be patient.</span><div class='loadingBar'></div></div>";
		var type = <?php echo json_encode($type); ?>;
		var internetId = <?php echo json_encode($internetId); ?>;
		var dept = <?php echo json_encode($deptId); ?>;
		var startYear = <?php echo json_encode(intval($startYear)); ?>;
		var endYear = <?php echo json_encode(intval($endYear)); ?>;
		var actionMode = <?php echo json_encode($action); ?>;
		$("#publications").html(loading);
		$.post("inc/modules/ajax/draw-publication-list.php",{postType: type, postInternetId: internetId, postDept: dept, startYear: startYear, endYear: endYear, action: actionMode}, function(data) {
			$("#publications").html(data);
			$("#generate_cv_link").click(function() {
				var startDate = $('#startYearSelectBox').val() + "-01-01";
				var endDate = $('#endYearSelectBox').val() + "-12-31";
		$.post("inc/modules/cv/generate-cv.php",{id:session_internetId,startDate:startDate,endDate:endDate},function(cv) {
	// console.log(cv);
					$("body").append(cv);
	// console.log(cv);
				});
				return false;
			});
		
		}); 	

		</script>
	<?php
	}
}

// Generates FACULTY SUMMARY tables for department/custom profiles
function drawFaculty($facultyData) {
	global $type;
	if($type === "dept") {
		global $deptId;
	} else if($type === "custom") {
		global $internetIdList;
		$sendIdList = str_replace('\'','',$internetIdList);
	}
	global $startDate;
	global $endDate;
	$downloadBase = "inc/modules/ajax/generate-report.php";
	if($type === "dept") {
		$downloadQuery = "?id=" . $deptId . "&type=dept&action=facultylist&startDate=" . $startDate . "&endDate=" . $endDate;	
	} else if($type === "custom") {
		$downloadQuery = "?id=" . $sendIdList . "&type=custom&action=facultylist&startDate=" . $startDate . "&endDate=" . $endDate;
	}
?>
	<div id='faculty'>
		<div class='sectionHeader'>
		Faculty Summary<span id='download'><a href='<?php echo $downloadBase . $downloadQuery; ?>'>Download all records</a></span>
		</div>

		<div class='tableContainer' id='tableContainer_faculty'>
			<table class='contentTable'>
				<thead id='facultyHeader'>
					<tr>
						<th style='background-color:#ffffff;border-bottom:1px solid #ffffff;' class='name'></th>
						<th colspan=2 class='indices'>Indices</th>
						<th colspan=2 class='publications'>Publications</th>
						<th colspan=2 class='citations'>Citation Counts</th>
					</tr>
					<tr>
						<th class='name'>Name</th>
						<th class='h_index'><i>h</i></th>
						<th class='hfl_index'><i>h</i>(<i>fl</i>)</th>
						<th class='pub_count'>Total</th>
						<th class='fl_pub_count'>First/Last</th>
						<th class='citation_count'>Total</th>
						<th class='fl_citation_count'>First/Last</th>
					</tr>	
				</thead>

				<tbody id='facultyList'>

<?php
				$rowCounter = 0;
				foreach($facultyData as $internetId => $facultyInfo) {
					$rowCounter++;
					if($rowCounter%2) {
						$rowCSS = 'row1';
					} else {
						$rowCSS = 'row2';
					}
					$name = $facultyInfo['firstName'] . " " . $facultyInfo['lastName'];
					$hIndex = $facultyInfo['hIndex'];
					$hflIndex = $facultyInfo['hflIndex'];
					$totalCitations = $facultyInfo['totalCitations'];
					$totalflCitations = $facultyInfo['totalflCitations'];
					$pubCount = $facultyInfo['pubCount'];
					$flPubCount = $facultyInfo['flPubCount'];
// 					$thisPeriodPubCount = $facultyInfo['thisPeriodPubCount'];
?>
					<tr class='<?php echo $rowCSS; ?>' id='record_<?php echo $rowCounter; ?>'>
<?php
						if($type === "custom") {
							$deptId = $facultyInfo['deptId'];
						}
?>
						<td class='name'><a href='profile.php?type=faculty&id=<?php echo $internetId; ?>'><?php echo $name; ?></a></td>
						<td class='h_index'><?php echo $hIndex; ?></td>
						<td class='hfl_index'><?php echo $hflIndex; ?></td>
<!-- 								<td class='this_period_pub_count'><?php echo $thisPeriodPubCount; ?></td> -->
						<td class='pub_count'><?php echo $pubCount; ?></td>
						<td class='fl_pub_count'><?php echo $flPubCount; ?></td>
						<td class='citation_count'><?php echo $totalCitations; ?></td>
						<td class='fl_citation_count'><?php echo $totalflCitations; ?></td>
 								<!-- <td class='action'><?php echo $action; ?></td> -->

					</tr>
<?php
				}

?>
				</tbody><!-- end facultyList -->
			</table>
		</div>
	</div><!-- end faculty -->

<?php
}
?>

<?php
// Draws visualizations in IMPACT ANALYTICS section of profiles
function drawMetrics($data) {
	global $type;
	global $con;
	global $vis_root_path;
	// Whitelist $type as SQL column component and default to faculty
	if (!in_array($type, array('faculty','dept','custom'))) {
		$type = 'faculty';
	}
	if($type === "faculty") {
		if(count(json_decode($data['citation_data'])) == 0) {
			$vis_display = 0;
		} else {
			$vis_display = 1;
		}
	?>
	<script>
		var faculty_hIndex = <?php echo $data['hIndex']; ?>;
		var faculty_hflIndex = <?php echo $data['hflIndex']; ?>;
	</script>
	<?php
	}
?>
	
	<div id='metrics'>
	
		<div class='sectionHeader'><a target='_blank' href='resources.php?p=faq#about-visualizations'><img class='info_bubble' src='inc/images/info_bubble.png'></a>Impact Analytics</div>
	<?php
			$vis_sql = "SELECT visID, visName, visDescription, visURL, vis_dataURL FROM visualizations WHERE display_" . $type . " = 1 ORDER BY visID";
			$result = runQuery($con,$vis_sql);
			while($row = mysqli_fetch_array($result)) {
				$vis_id = $row['visID'];
	?>
				<div class='feature'>
					<div class='featureDescription'>
						<div class='content'>
							<div class='title'><?php echo $row['visName']; ?></div>
							<div class='text'><?php print $row['visDescription']; ?>
								<?php if($vis_id === "V0004" && $type === "faculty") {
									print $data["firstName"] . "'s <i>h</i>-index position is highlighted in the vertical blue line and <i>h</i>(<i>fl</i>)-index in gold, illustrating how they compare to other faculty in the department.";
								}
								?>
								<?php if($vis_id === "V0005") {
									switch($type) {
										case "faculty":
											print $data["firstName"] . " is highlighted in yellow.";
											break;
										case "dept":
											print "Individuals in this department are highlighted in yellow.";
											break;
										case "custom":
											print "Individuals in this subset are highlighted in yellow.";
											break;
									}
								}
								?>
							</div>
						</div>
					</div>
					<div class='featureBox' id='<?php echo $row['visID']; ?>' style='height:400px;'>
						<?php 

							if($vis_display == 1) {
								if($vis_id === 'V0005') {
									echo "<script>var data_source = '" . $row['vis_dataURL'] . "';</script>";
									include($vis_root_path . $row['visURL']); 
								} else {
									include($vis_root_path . $row['visURL']); 
								}							
							} else if($vis_display == 0) {
								if($vis_id === 'V0001' || $vis_id === 'V0002' || $vis_id === 'V0003') {
									echo "<div class='vis_no_display'>There is not enough publication data to display this visualization.</div>";
								} else {
									if($vis_id === 'V0005') {
										echo "<script>var data_source = '" . $row['vis_dataURL'] . "';</script>";
										include($vis_root_path . $row['visURL']); 
									} else {
										include($vis_root_path . $row['visURL']); 
									}							
								
								}
							}
						?>
					</div>
				</div>
	<?php
			}
			mysqli_free_result($result);
	?>

								
	</div>			

<?php

}

?>
