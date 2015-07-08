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

Generates departmental publication totals report based on data in data file

-->

<?php

include("../config/default-config.php");
include("../functions/default-functions.php");

// Initialize database connection
$con = connectDB();

// Get data source

$select_data_source_sql = "SELECT report_dataURL FROM reports WHERE reportID = 'R0001' LIMIT 1";
$data_result = runQuery($con,$select_data_source_sql);
$obj = mysqli_fetch_object($data_result);
$data_source = $obj->report_dataURL;
mysqli_free_result($data_result);
closeDB($con);

$dataFile = file_get_contents("inc/reports/deptReports/" . $data_source);
$data = json_decode($dataFile,true);

// Determine quarter, year, and previous year
// This should be based on the file name, NOT the getQuarter function because the latest update may not correspond 
// with the actual calendar year
$report_date = explode("_",str_replace(array("q",".json"),"",$data_source));
$currentYear = $report_date[0];
$quarterName = $report_date[1];
$previousYear = $currentYear - 1;

// Style attributes

$deptColumn = "20%";
$standardColumn = "20%";
$halfColumn = "10%";
$font_default = "Calibri, Candara, Segoe, 'Segoe UI', Optima, Arial, sans-serif";

?>



<style>
	
	
	.department {
		width: <?php echo $deptColumn; ?>;
		padding: 2px;
		margin: 0px;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
	}
	
	.currentQuarter {
		width: <?php echo $standardColumn; ?>;
		padding: 2px;
		margin: 0px;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;

	}
	
	.previousYear {
		width: <?php echo $standardColumn; ?>;
		padding: 2px;
		margin: 0px;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
	}
	
	.cumulativeQuarter {
		width: <?php echo $standardColumn; ?>;
		padding: 2px;
		margin: 0px;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
	}	
	
	.viz {
		width: <?php echo $standardColumn; ?>;
		padding: 2px;
		margin: 0px;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
	}	
	
	.totalPublications {
		width: <?php echo $halfColumn; ?>;
		padding: 2px;
		margin: 0px;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		text-align:center;

	}
	
	.flPublications {
		width: <?php echo $halfColumn; ?>;
		padding: 2px;
		margin: 0px;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		text-align:center;

	}
		
	#headerContainer {
		width: 100%;
/* 		overflow:hidden; */
		margin:0px;
		padding:0px;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		font-family: <?php echo $font_default; ?>;
	}

	#headerTable {
		width:100%;
		margin:0px;
		padding:0px;		
		background-color: #294052;
		color: #ffffff;
		font-size:16px;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		font-weight:bold;
		border-collapse:collapse;

	}	

	#dataTable {
		width:100%;
		margin:0px;
		padding:0px;
		font-size:16px;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;

	}	
	
	.blank {
		border: none;
		background-color:#fff;
	}
	
	.superHeader {
		height:50%;
		text-align:center;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		color: #ffffff;
		border:2px solid #fff;
		border-top:none;
		border-bottom:none;
	}
	
	.subHeader {
		height: 50%;
		text-align:center;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		color: #ffffff;
						border:2px solid #fff;
				border-bottom:none;

		
	}
	
	#dataContainer {
		width:100%;
		height:85%;
		margin:0px;
		padding:0pxl
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		font-family: <?php echo $font_default; ?>;
	}
	
	.row1 {
		width: 100%;
		background-color: #F9F6F4;
		padding: 10px 5px 10px 5px;
		margin: 0px;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
	}


	.row2 {
		width: 100%;
		background-color: #E9E0DB;
		padding: 10px 5px 10px 5px;
		margin: 0px;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;

	}
	
	.total {
		width: 100%;
		background-color: #FFF67A;
		padding: 10px 5px 10px 5px;
		margin: 0px;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		font-weight:bold;		
	}
	
	#dataContainer td {
		padding: 8px;
	}

	.progress_text {
		font-family: <?php echo $font_default; ?>;
		font-size:12px;
		font-weight:bold;
		text-anchor:left;
		dominant-baseline:central;
	}

.fixed_dept {
	position: fixed;
	top: 0px;
	overflow: auto;
	z-index:100;
	margin:0px;
	width:100%;	
}

</style>
<div class='sectionHeader'>
Medical School All-Departmental Publication Summary
</div>

<div id="headerContainer">
	<table id="headerTable">
		<tr>
			<td class='blank'></td>
			<td class='currentQuarter superHeader' colspan='2'><?php echo $currentYear . " Quarter " . $quarterName; ?></td>
			<td class='previousYear superHeader' colspan='2'><?php echo $previousYear; ?></td>
			<td class='currentQuarter superHeader' colspan='2'><?php echo $currentYear . " Cumulative"; ?></td>
			<td class='viz superHeader' rowspan='2'>Goal Progress</td>
		</tr>
		<tr>
			<td class='department subHeader'>Department</td>
			<td class='totalPublications subHeader'>Total publications</td>
			<td class='flPublications subHeader'>First/last author publications</td>
			<td class='totalPublications subHeader'>Total publications</td>
			<td class='flPublications subHeader'>First/last author publications</td>
			<td class='totalPublications subHeader'>Total publications</td>
			<td class='flPublications subHeader'>First/last author publications</td>		
		</tr>
	</table>
</div>
<div id="dataContainer">
	<table id="dataTable">

<?php

$counter = 0;
$current_total = 0;
$current_fl = 0;
$previous_total = 0;
$previous_fl = 0;
$cumulative_total = 0;
$cumulative_fl = 0;

foreach($data as $ind => $deptData) {
	
		$umn_deptid = $deptData['umn_deptid'];
		$chart_id = "chart_" . $ind;
		if($umn_deptid === "total") {
			$rowClass='total';
		} else {
			if(++$counter % 2) {
				$rowClass= 'row1';
			} else {
				$rowClass = 'row2';
			}
		}
		print "<tr class='" . $rowClass . "'>";
			print "<td class='department'><b>" . $deptData['deptName'] . "</b></td>";
			print "<td class='totalPublications'>" . $deptData['currentQuarter']['totalPublications'] . "</td>";
			print "<td class='flPublications'>" . $deptData['currentQuarter']['flPublications'] . "</td>";
			print "<td class='totalPublications'>" . $deptData['previousYear']['totalPublications'] . "</td>";
			print "<td class='flPublications'>" . $deptData['previousYear']['flPublications'] . "</td>";
			print "<td class='totalPublications'>" . $deptData['cumulativeQuarter']['totalPublications'] . "</td>";
			print "<td class='flPublications'>" . $deptData['cumulativeQuarter']['flPublications'] . "</td>";	
			print "<td class='viz' id='" . $chart_id . "'>";
			if($orgaId !== "total") {
			print "<script>var viz_id = '" . $chart_id . "'; var selection = '#' + viz_id;" .
					"var total_progress = " . floor($deptData['cumulativeQuarter']['totalPublications']/$deptData['previousYear']['totalPublications']*100) . ";" .
					"var fl_progress = " . floor($deptData['cumulativeQuarter']['flPublications']/$deptData['previousYear']['flPublications']*100) . ";";
			
			
			print "</script>";
?>

<script type='text/javascript' src='inc/visualizations/progress.js'></script>			
<?php
			}

			print "</td>";	
		print "</tr>";



}

?>

	</table>

</div>
