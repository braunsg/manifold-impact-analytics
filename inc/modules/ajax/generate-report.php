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

Outputs exported data (publications, metrics) into Excel spreadsheet using PHPExcel 1.8.0

Could be rewritten to output in CSV format instead for better cross-browser support
(Firefox does not correctly output Excel; Chrome does)

-->

<?php

include("../../config/default-config.php");
include("../../functions/default-functions.php");

// Reinstantiate database connection
$con = connectDB();

// Grab GET parameters
$type = mysqli_real_escape_string($con, $_GET['type']);
$id = mysqli_real_escape_string($con, $_GET['id']);
$action = mysqli_real_escape_string($con, $_GET['action']);
$startDate = mysqli_real_escape_string($con, $_GET['startDate']);
$endDate = mysqli_real_escape_string($con, $_GET['endDate']);

// Get name for download file
switch($type) {
	case "faculty-download":
		$download_file_name = "Master Faculty List";
		break;
	case "dept":
		$name_sql = "SELECT affilName FROM affiliation_data WHERE umn_zdeptid = '$id' OR umn_deptid = '$id' LIMIT 1";
		$name_result = runQuery($con,$name_sql);
		$obj = mysqli_fetch_object($name_result);
		$this_name = $obj->affilName;
		if($action === "facultylist") {
			$download_file_name = "Faculty Summary ($this_name)";
		} else {
			$download_file_name = "Faculty Publication List ($this_name)";
		
		}
		break;
	case "custom":
		if($action === "facultylist") {
			$download_file_name = "Faculty Summary (Custom)";
		} else {
			$download_file_name = "Faculty Publication List (Custom)";
		}
		break;	
	default:
		$name_sql = "SELECT firstName, lastName FROM faculty_data WHERE internetID = '$id' LIMIT 1";
		$name_result = runQuery($con,$name_sql);
		$obj = mysqli_fetch_object($name_result);
		$this_first_name = $obj->firstName;
		$this_last_name = $obj->lastName;
		$download_file_name = "Faculty Publication List ($this_first_name $this_last_name)";
		break;
}

mysqli_free_result($name_result);
						
// Configure for force download

// Strip out commas in filename -- header doesn't like these
$download_file_name = str_replace(",","",$download_file_name);

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Disposition: attachment;filename=$download_file_name.xlsx"); 
header("Content-Transfer-Encoding: binary");


// Include PHP Excel

set_include_path('../../libraries/PHPExcel_1.8.0/Classes/');
include 'PHPExcel.php';
include 'PHPExcel/Writer/Excel2007.php';

if($type === "faculty-download") {

		// Create a summary spreadsheet of all Medical School faculty, biodata only (no metrics)
		
		$output = new PHPExcel();
		$output->getDefaultStyle()->getFont()->setSize(12);								$output->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$output->getProperties()->setCreator($site_def_creator);
		$output->getProperties()->setLastModifiedBy($site_def_creator);
		$output->getProperties()->setDescription("Master faculty list");

		$output->setActiveSheetIndex(0);
		$output->getActiveSheet()->setTitle("All faculty");
			
		$columnLabelArray = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		$headerArray = array("internetID" => "Internet ID",
							 "firstName" => "First Name",
							 "lastName" => "Last Name",
							 "title" => "Title",
							 "deptName" => "Department",
							 "class_description" => "Class Description",
							 "tenure_status" => "Tenure Status");
		
		$column_counter = 0;
		foreach($headerArray as $field => $label) {
			$output->getActiveSheet()->SetCellValue($columnLabelArray[$column_counter++] . '1', $label);
		}
		$output->getActiveSheet()->getStyle('A1:' . $columnLabelArray[count($headerArray)-1] . '1')->getFont()->setBold(true);

		$pointer = 1;

		$get_all_faculty = "SELECT faculty_data.internetID, faculty_data.firstName, faculty_data.lastName, faculty_data.title, affiliation_data.affilName, faculty_data.class_description, faculty_data.tenure_status FROM faculty_data INNER JOIN faculty_affiliations ON faculty_affiliations.internetID = faculty_data.internetID INNER JOIN affiliation_data ON affiliation_data.affilID = faculty_affiliations.affilID WHERE faculty_data.status_current = 1 AND faculty_data.percentTime >= 0.67 AND faculty_data.status_faculty = 1 AND faculty_affiliations.affilClass = 'DISPLAY' ORDER BY faculty_data.lastName ASC";
		$result = runQuery($con,$get_all_faculty);
		while($row = mysqli_fetch_array($result)) {
			$pointer++;
			$column_counter = 0;
			foreach($headerArray as $field => $label) {
				$output->getActiveSheet()->SetCellValue($columnLabelArray[$column_counter++] . $pointer, $row[$field]);
			}
		}

		foreach($columnLabelArray as $label) {
			$output->getActiveSheet()->getColumnDimension($label)->setWidth(18);
		}

} else if($type === "dept" || $type === "custom") {

	// Get faculty data
	$facultyIds = array();

	if($type === "dept") {
		$getFacultyIds = "SELECT faculty_data.internetID FROM faculty_data INNER JOIN faculty_affiliations ON faculty_affiliations.internetID = faculty_data.internetID WHERE faculty_affiliations.affilID = '$id' AND faculty_affiliations.affilClass = 'DISPLAY' AND faculty_data.status_faculty = 1 AND faculty_data.percentTime >= 0.67 AND faculty_data.status_current = 1 ORDER BY faculty_data.lastName ASC";
		$result = runQuery($con,$getFacultyIds);
		while($row = mysqli_fetch_array($result)) {
			$facultyIds[] = $row['internetID'];
		}
	} else if($type === "custom") {
		$facultyIds = explode(',',$id);
	}	
	foreach($facultyIds as $internetId) {
		$getFacultyInfo = "SELECT faculty_data.internetID, faculty_data.firstName, faculty_data.lastName, faculty_data.title, faculty_data.class_description, faculty_data.tenure_status, faculty_data.lastUpdated, faculty_metrics.hIndex, faculty_metrics.hflIndex, faculty_metrics.pubCount, faculty_metrics.flPubCount, faculty_metrics.totalCitations, faculty_metrics.totalflCitations, faculty_affiliations.affilID FROM faculty_data INNER JOIN faculty_metrics ON faculty_metrics.internetID = faculty_data.internetID INNER JOIN faculty_affiliations ON faculty_affiliations.internetID = faculty_data.internetID WHERE faculty_data.internetID = '$internetId' AND faculty_affiliations.affilClass = 'DISPLAY' LIMIT 1";
		$result = runQuery($con,$getFacultyInfo);
		while($row = mysqli_fetch_array($result)) {
			$internetId = $row['internetID'];
			$firstName = $row['firstName'];
			$lastName = $row['lastName'];
			$title = $row['title'];
			$hIndex = $row['hIndex'];
			$hflIndex = $row['hflIndex'];
			$totalPubs = $row['pubCount'];
			$flPubs = $row['flPubCount'];
			$totalCitations = $row['totalCitations'];
			$flCitations = $row['totalflCitations'];
			$class_description = $row['class_description'];
			$tenure_status = $row['tenure_status'];
			$lastUpdate = $row['lastUpdated'];
			$dept = $row['affilID'];
			
			if(empty($deptName)) {
				$getDept = "SELECT affilName FROM affiliation_data WHERE umn_deptid = '$dept' OR umn_zdeptid = '$dept'";
				$dept_query = runQuery($con,$getDept);
				$obj = mysqli_fetch_object($dept_query);
				$deptName = $obj->affilName;		
			}
			$facultyIdsArray[] = $internetId;
			$facultySummaryArray[$internetId] = array('internetId' => $internetId,
													  'firstName' => $firstName,
													  'lastName' => $lastName,
													  'title' => $title,
													  'hIndex' => $hIndex,
													  'hflIndex' => $hflIndex,
													  'totalPubCount' => $totalPubs,
													  'flPubCount' => $flPubs,
													  'totalCitations' => $totalCitations,
													  'flCitations' => $flCitations,
													  'class_description' => $class_description,
													  'tenure_status' => $tenure_status
													  );
		}
	}
	

	$facultyIdList = "'" . implode("','",$facultyIdsArray) . "'";

	// Generate descriptive statistics for department

	$metric_fields = array("Indices" => array("hIndex" => "h-index",
									   "hflIndex" => "h(fl)-index"),
					"Publication Counts" => array("pubCount" => "Total",
												  "flPubCount" => "First/Last"),
					"Citation Counts" => array("totalCitations" => "Total",
											   "totalflCitations" => "First/Last"));

	$profile_data_dept = array();
	$batchSummaryArray = array();
	foreach($metric_fields as $category => $subcategories) {

		$profile_data_dept[$category] = array();
		foreach($subcategories as $metric => $metric_label) {
			$metricArray = array();
			$metric_sql = "SELECT " . $metric . " AS metric FROM faculty_metrics WHERE internetID IN ($facultyIdList) ORDER BY metric ASC";
			$metric_result = runQuery($con,$metric_sql);
			while($row = mysqli_fetch_array($metric_result)) {
				$metricArray[] = $row['metric'];
			}
			$batchSummaryArray["median_" . $metric] = quartile($metricArray,0.5);

			mysqli_free_result($metric_result);	

		}
	}	
	
	$batchSummaryArray['facultyCount'] = count($facultySummaryArray);
	
	if($action === "publist") {
	
		
		$output = new PHPExcel();
		$output->getDefaultStyle()->getFont()->setSize(12);								$output->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$output->getProperties()->setCreator($site_def_creator);
		$output->getProperties()->setLastModifiedBy($site_def_creator);
		$output->getProperties()->setDescription("List of publications");

		$output->setActiveSheetIndex(0);
		$output->getActiveSheet()->setTitle("Publications");
			
		$pubsArray = array();
		$columnLabelArray = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		$fieldsArray = array(
							'publication_data.scopus_eid' => 'Scopus Link',
							'publication_data.pmid' => 'PubMed ID',
							'publication_data.pubTitle' => 'Title',
							'publication_data.pubName' => 'Publication',
							'publication_data.pubDate' => 'Pub Date',
							'publication_data.authors' => 'Authors',
							'publication_data.pageRange' => 'Pages',
							'publication_data.volume' => 'Volume',
							'publication_data.issue' => 'Issue',
							'publication_data.citedByCount' => 'Citation Count'
							);

		$headerArray = array("Faculty Count","Median H-index","Median H(fl)-index","Median Total Pubs","Median First/Last Author Pubs","Median Total Citations (Scopus)","Median Total First/Last Author Citations");
// 		$periodPubCountCell = "B4";
		if($type === "dept") {
			$output->getActiveSheet()->setCellValue('A1', $deptName . ": Departmental Summary");	
		} else if($type === "custom") {
			$output->getActiveSheet()->setCellValue('A1',"Custom Filter: Faculty Summary");			
		}
		$output->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(14);
		$output->getActiveSheet()->mergeCells('A1:' . $columnLabelArray[(count($headerArray)-1)] . '1');


		foreach($headerArray as $ind => $label) {
			$output->getActiveSheet()->SetCellValue($columnLabelArray[$ind] . '3', $label);
		}
		$output->getActiveSheet()->getStyle('A3:' . $columnLabelArray[count($headerArray)-1] . '3')->getFont()->setBold(true)->setSize(14);

		$basicInfoArray = array($batchSummaryArray['facultyCount'],
							  $batchSummaryArray['median_hIndex'],
							  $batchSummaryArray['median_hflIndex'],
							  $batchSummaryArray['median_pubCount'],
							  $batchSummaryArray['median_flPubCount'],
							  $batchSummaryArray['median_totalCitations'],
							  $batchSummaryArray['median_totalflCitations']
							  );

		foreach($basicInfoArray as $ind => $data) {
			$output->getActiveSheet()->SetCellValue($columnLabelArray[$ind] . '4', $data);
		}
		
		$output->getActiveSheet()->SetCellValue('A6',"Publications for period " . $startDate . " through " . $endDate);
		$output->getActiveSheet()->getStyle('A6')->getFont()->setBold(true)->setSize(14);
		$output->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$output->getActiveSheet()->mergeCells('A6:' . $columnLabelArray[(count($fieldsArray)-1)] . '6');
	
	
		$pubdatasql = "SELECT " . implode(',',array_keys($fieldsArray)) . " FROM publication_data INNER JOIN faculty_publications ON faculty_publications.mpid = publication_data.mpid WHERE faculty_publications.record_valid = 1 AND faculty_publications.internetID IN (" . $facultyIdList . ") AND publication_data.pubDate <= '$endDate' AND publication_data.pubDate >= '$startDate' ORDER BY publication_data.pubDate DESC";
		
		$result = runQuery($con,$pubdatasql);
// 		$output->getActiveSheet()->SetCellValue($periodPubCountCell, mysqli_num_rows($result));

		while($row = mysqli_fetch_array($result)) {

			$thisPubArray = array();
			$pubDate = strtotime($row['pubDate']);
			$scopus_eid = $row['scopus_eid'];

			foreach($fieldsArray as $field => $name) {

				if($field === 'publication_data.authors') {
					$thisPubArray[] = str_replace('|','; ',$row[str_replace("publication_data.","",$field)]);
				} else if($field === 'publication_data.scopus_eid') {
					$thisPubArray[] = "http://www.scopus.com/record/display.url?eid=" . $scopus_eid . "&origin=resultslist";
				} else {
					$thisPubArray[] = $row[str_replace("publication_data.","",$field)];
				}
			}		
			$pubsArray[$scopus_eid] = $thisPubArray;
		}
			
		
		$outputCount = 0;
		$pointer = 8;
		foreach(array_values($fieldsArray) as $ind => $fieldLabel) {
			$output->getActiveSheet()->SetCellValue($columnLabelArray[$ind] . $pointer, $fieldLabel);
		}
		$output->getActiveSheet()->getStyle('A' . $pointer . ':' . $columnLabelArray[count($fieldsArray)-1] . $pointer)->getFont()->setBold(true);

		if(count($pubsArray) == 0) {
			$output->getActiveSheet()->SetCellValue('A' . ($pointer += 2), "These faculty do not have any new publications within the specified time period.");
		} else {
			foreach($pubsArray as $scopus_eid => $pubData) {
					$pointer += 1;
					foreach($pubData as $col => $data) {
						$output->getActiveSheet()->SetCellValue($columnLabelArray[$col] . $pointer,$data);					
						if($col == 0) {
							$output->getActiveSheet()->getCell($columnLabelArray[$col] . $pointer)->getHyperlink()->setUrl($data);
						}

					}
					$output->getActiveSheet()->getStyle('A' . $pointer . ':B' . $pointer)->getAlignment()->setWrapText(true);
					$output->getActiveSheet()->getStyle('E' . $pointer)->getAlignment()->setWrapText(true);

			}
		}
	
		// Some final formatting, and then write file
		
		$output->getActiveSheet()->getStyle('A3:H3')->getAlignment()->setWrapText(true);	
		$output->getActiveSheet()->getColumnDimension('A')->setWidth(25);
		foreach(array_slice($columnLabelArray,1) as $label) {
			$output->getActiveSheet()->getColumnDimension($label)->setWidth(25);
		}
	
			
			
	
	} else if($action === "facultylist") {
	
		// Now create a summary file for all faculty

		$output = new PHPExcel();
		$output->getDefaultStyle()->getFont()->setSize(12);								$output->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$output->getProperties()->setCreator($site_def_creator);
		$output->getProperties()->setLastModifiedBy($site_def_creator);
		$output->getProperties()->setDescription("Faculty summary statistics");

		$output->setActiveSheetIndex(0);
		$output->getActiveSheet()->setTitle("Publications");
			
		$pubsArray = array();
		$columnLabelArray = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		$fieldsArray = array(
							'publication_data.scopus_eid' => 'Scopus eID',
							'publication_data.pmid' => 'PubMed ID',
							'publication_data.pubTitle' => 'Title',
							'publication_data.pubName' => 'Publication',
							'publication_data.pubDate' => 'Pub Date',
							'publication_data.authors' => 'Authors',
							'publication_data.pageRange' => 'Pages',
							'publication_data.volume' => 'Volume',
							'publication_data.issue' => 'Issue',
							'publication_data.citedByCount' => 'Citation Count'
							);

		$headerArray = array("Faculty Count","Median H-index","Median H(fl)-index","Median Total Pubs","Median First/Last Author Pubs","Median Total Citations (Scopus)","Median Total First/Last Author Citations");
// 		$periodPubCountCell = "B4";
		if($type === "dept") {
			$output->getActiveSheet()->setCellValue('A1', $deptName . ": Departmental Summary");		
		} else if($type === "custom") {
			$output->getActiveSheet()->setCellValue('A1', "Custom Filter: Faculty Summary");	
		}
		$output->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(14);
		$output->getActiveSheet()->mergeCells('A1:' . $columnLabelArray[(count($headerArray)-1)] . '1');


		foreach($headerArray as $ind => $label) {
			$output->getActiveSheet()->SetCellValue($columnLabelArray[$ind] . '3', $label);
		}
		$output->getActiveSheet()->getStyle('A3:' . $columnLabelArray[count($headerArray)-1] . '3')->getFont()->setBold(true)->setSize(14);

		$basicInfoArray = array($batchSummaryArray['facultyCount'],
							  $batchSummaryArray['median_hIndex'],
							  $batchSummaryArray['median_hflIndex'],
							  $batchSummaryArray['median_pubCount'],
							  $batchSummaryArray['median_flPubCount'],
							  $batchSummaryArray['median_totalCitations'],
							  $batchSummaryArray['median_totalflCitations']
							  );

		foreach($basicInfoArray as $ind => $data) {
			$output->getActiveSheet()->SetCellValue($columnLabelArray[$ind] . '4', $data);
		}
	
		$headerArray = array("Internet ID","First Name","Last Name","Title","Class Description","Tenure Status","h-index","h(fl)-index","Total Pubs","First/Last Author Pubs","Total Citations","First/Last Author Citations");
		foreach($headerArray as $ind => $label) {
			$output->getActiveSheet()->SetCellValue($columnLabelArray[$ind] . '6', $label);
		}
		$output->getActiveSheet()->getStyle('A6:' . $columnLabelArray[count($headerArray)-1] . '6')->getFont()->setBold(true);

		$pointer = 6;

		foreach($facultySummaryArray as $internetId => $facultyInfo) {
			$pointer++;

			$basicInfo = array($internetId,
							   $facultyInfo['firstName'],
							   $facultyInfo['lastName'],
							   $facultyInfo['title'],
							   $facultyInfo['class_description'],
							   $facultyInfo['tenure_status'],
							   $facultyInfo['hIndex'],
							   $facultyInfo['hflIndex'],
							   $facultyInfo['totalPubCount'],
							   $facultyInfo['flPubCount'],
							   $facultyInfo['totalCitations'],
							   $facultyInfo['flCitations']
							   );		
						   
			foreach($basicInfo as $ind => $data) {
				$output->getActiveSheet()->SetCellValue($columnLabelArray[$ind] . $pointer, $data);
			}
	
		}

		foreach($columnLabelArray as $label) {
			$output->getActiveSheet()->getColumnDimension($label)->setWidth(18);
		}
	
	
	
	}
	
	
} else if($type === "faculty") {
	$internetId = $id;
	$output = new PHPExcel();
	$output->getDefaultStyle()->getFont()->setSize(12);								$output->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$output->getProperties()->setCreator($site_def_creator);
	$output->getProperties()->setLastModifiedBy($site_def_creator);
	$output->getProperties()->setDescription("List of publications");

	$output->setActiveSheetIndex(0);
	$output->getActiveSheet()->setTitle("Publications");

	$getFacultyInfo = "SELECT faculty_data.firstName, faculty_data.lastName, faculty_data.title, faculty_data.class_description, faculty_data.tenure_status, faculty_data.lastUpdated, faculty_metrics.hIndex, faculty_metrics.hflIndex, faculty_metrics.pubCount, faculty_metrics.flPubCount, faculty_metrics.totalCitations, faculty_metrics.totalflCitations, faculty_affiliations.affilID FROM faculty_data INNER JOIN faculty_metrics ON faculty_metrics.internetID = faculty_data.internetID INNER JOIN faculty_affiliations ON faculty_affiliations.internetID = faculty_data.internetID WHERE faculty_data.internetID = '$internetId' AND faculty_affiliations.affilClass = 'DISPLAY' LIMIT 1";
	$result = runQuery($con,$getFacultyInfo);
	while($row = mysqli_fetch_array($result)) {
		$firstName = $row['firstName'];
		$lastName = $row['lastName'];
		$title = $row['title'];
		$hIndex = $row['hIndex'];
		$hflIndex = $row['hflIndex'];
		$totalPubs = $row['pubCount'];
		$flPubs = $row['flPubCount'];
		$totalCitations = $row['totalCitations'];
		$flCitations = $row['totalflCitations'];
		$class_description = $row['class_description'];
		$tenure_status = $row['tenure_status'];
		$lastUpdate = $row['lastUpdated'];
		$aggregateDept = $row['affilID'];
		
		$getDept = "SELECT affilName FROM affiliation_data WHERE affilID = '$aggregateDept'";
		$dept = runQuery($con,$getDept);
		$obj = mysqli_fetch_object($dept);
		$dept = $obj->affilName;		
	}
	$facultySummaryArray = array('internetId' => $internetId,
											  'firstName' => $firstName,
											  'lastName' => $lastName,
											  'title' => $title,
											  'hIndex' => $hIndex,
											  'hflIndex' => $hflIndex,
											  'totalPubCount' => $totalPubs,
											  'flPubCount' => $flPubs,
											  'totalCitations' => $totalCitations,
											  'flCitations' => $flCitations,
											  'class_description' => $class_description,
											  'tenure_status' => $tenure_status,
											  'dept' => $dept
											  );

	if($action === "publist") {

		$pubsArray = array();
		$columnLabelArray = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		$fieldsArray = array(
							'publication_data.scopus_eid' => 'Scopus Link',
							'publication_data.pmid' => 'PubMed ID',
							'publication_data.pubTitle' => 'Title',
							'publication_data.pubName' => 'Publication',
							'publication_data.pubDate' => 'Pub Date',
							'publication_data.authors' => 'Authors',
							'publication_data.pageRange' => 'Pages',
							'publication_data.volume' => 'Volume',
							'publication_data.issue' => 'Issue',
							'publication_data.citedByCount' => 'Citation Count'
							);



		$headerArray = array("First Name","Last Name","Title","Class Description","Tenure Status","H-index","H(fl)-index","Total Pubs","First/Last Author Pubs","Total Citations (Scopus)","Total First/Last Author Citations");
		foreach($headerArray as $ind => $label) {
			$output->getActiveSheet()->SetCellValue($columnLabelArray[$ind] . '1', $label);
		}
		$output->getActiveSheet()->getStyle('A1:' . $columnLabelArray[count($headerArray)-1] . '1')->getFont()->setBold(true)->setSize(14);

		$basicInfoArray = array($facultySummaryArray['firstName'],
							  $facultySummaryArray['lastName'],
							  $facultySummaryArray['title'],
							  $facultySummaryArray['class_description'],
							  $facultySummaryArray['tenure_status'],
							  $facultySummaryArray['hIndex'],
							  $facultySummaryArray['hflIndex'],
							  $facultySummaryArray['totalPubCount'],
							  $facultySummaryArray['flPubCount'],
							  $facultySummaryArray['totalCitations'],
							  $facultySummaryArray['flCitations']
							  );

		foreach($basicInfoArray as $ind => $data) {
			$output->getActiveSheet()->SetCellValue($columnLabelArray[$ind] . '2', $data);
		}

		$output->getActiveSheet()->SetCellValue('A4',"Publications for period " . $startDate . " through " . $endDate);
		$output->getActiveSheet()->getStyle('A4')->getFont()->setBold(true)->setSize(14);
		$output->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$output->getActiveSheet()->mergeCells('A4:' . $columnLabelArray[(count($fieldsArray)-1)] . '4');
	
	
		$pubdatasql = "SELECT " . implode(',',array_keys($fieldsArray)) . " FROM publication_data INNER JOIN faculty_publications ON faculty_publications.mpid = publication_data.mpid WHERE faculty_publications.record_valid = 1 AND faculty_publications.internetID = '" . $facultySummaryArray['internetId'] . "' AND publication_data.pubDate <= '$endDate' AND publication_data.pubDate >= '$startDate' ORDER BY publication_data.pubDate DESC";
		$result = runQuery($con,$pubdatasql);
		while($row = mysqli_fetch_array($result)) {

			$thisPubArray = array();
			$pubDate = strtotime($row['pubDate']);
			$scopus_eid = $row['scopus_eid'];

			foreach($fieldsArray as $field => $name) {

				if($field === 'publication_data.authors') {
					$thisPubArray[] = str_replace('|','; ',$row[str_replace("publication_data.","",$field)]);
				} else if($field === 'publication_data.scopus_eid') {
					$thisPubArray[] = "http://www.scopus.com/record/display.url?eid=" . $scopus_eid . "&origin=resultslist";
				} else {
					$thisPubArray[] = $row[str_replace("publication_data.","",$field)];
				}
			}		
			$pubsArray[$scopus_eid] = $thisPubArray;
		}
			
		$outputCount = 0;
		$pointer = 6;
		foreach(array_values($fieldsArray) as $ind => $fieldLabel) {
			$output->getActiveSheet()->SetCellValue($columnLabelArray[$ind] . $pointer, $fieldLabel);
		}
		$output->getActiveSheet()->getStyle('A' . $pointer . ':' . $columnLabelArray[count($fieldsArray)-1] . $pointer)->getFont()->setBold(true);

		if(count($pubsArray) == 0) {
			$output->getActiveSheet()->SetCellValue('A' . ($pointer += 2), "This faculty member does not have any new publications within the specified time period.");
		} else {
			foreach($pubsArray as $scopus_eid => $pubData) {
					$pointer += 1;
					foreach($pubData as $col => $data) {
						$output->getActiveSheet()->SetCellValue($columnLabelArray[$col] . $pointer,$data);
						if($col == 0) {
							$output->getActiveSheet()->getCell($columnLabelArray[$col] . $pointer)->getHyperlink()->setUrl($data);
						} 

					}
					$output->getActiveSheet()->getStyle('A' . $pointer . ':B' . $pointer)->getAlignment()->setWrapText(true);
					$output->getActiveSheet()->getStyle('E' . $pointer)->getAlignment()->setWrapText(true);

			}
		}
	
		// Some final formatting, and then write file
	
		$output->getActiveSheet()->getColumnDimension('A')->setWidth(25);
		foreach(array_slice($columnLabelArray,1) as $label) {
			$output->getActiveSheet()->getColumnDimension($label)->setWidth(25);
		}
	
	
	}
}


// Now write to file and force download through browser
$outputContent = new PHPExcel_Writer_Excel2007($output);
$outputContent->save('php://output');


?>
