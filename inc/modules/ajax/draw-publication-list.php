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

Generates publication lists, based on request type; also regenerates publication lists 
based on changes to the year range parameters on a profile

-->

<script>
	$(document).ready(function() {
		initializeProfile();
		$('.yearSelectBox').change(function() {
			var startYear = $('#startYearSelectBox').val();
			var endYear = $('#endYearSelectBox').val();
			var loading = "<div class='pubLoading'><span class='text'>Loading publication data...please be patient.</span><div class='loadingBar'></div></div>";
			$("#publicationLoadContent").html(loading);
			if(session_type === 'faculty') {
				$.post("inc/modules/ajax/draw-publication-list.php",{postType: session_type, postInternetId: session_internetId, postDept: session_dept, startYear: startYear, endYear: endYear, action: "update"}, function(data) {
					$("#publicationLoadContent").html(data);

				}); 	

			} else if(session_type === 'dept') {
				$.post("inc/modules/ajax/draw-publication-list.php",{postType: session_type, postInternetId: session_internetId, postDept: session_dept, startYear: startYear, endYear: endYear, action: "update"}, function(data) {
					$("#publicationLoadContent").html(data);
				});
			} else if(session_type === 'custom') {
				$.post("inc/modules/ajax/draw-publication-list.php",{postType: session_type, postInternetId: session_internetId, postDept: session_dept, startYear: startYear, endYear: endYear, action: "update"}, function(data) {
					$("#publicationLoadContent").html(data);
				});
			
			}
		});
		
		// Adjust table widths to account for scroll bar
		var all_publications_scrollbarWidth = $("#tableContainer_publications").width() - $("#tableContainer_publications").find(".publicationsList").width();
		$("#publications").find(".publicationsHeader").css("padding-right",all_publications_scrollbarWidth + "px");

		var top_publications_scrollbarWidth = $("#tableContainer_top-publications").width() - $("#tableContainer_top-publications").find(".publicationsList").width();
		$("#top_publications").find(".publicationsHeader").css("padding-right",top_publications_scrollbarWidth + "px");


	});
</script>

<?php


include("../../config/default-config.php");
include("../../functions/default-functions.php");

// Reinstantiate database connection
$con = connectDB();


$type = $_POST['postType'];

// Ids are sent already quoted, so unquote them, escape, and requote them.
$internetId = mysqli_real_escape_string($con, str_replace("'", '', $_POST['postInternetId']));
$internetId = "'" . implode("','", explode(',', $internetId)) . "'";

if($type !== "custom") {
	$deptId = mysqli_real_escape_string($con, $_POST['postDept']);

	if($type === "dept") {
		// Generate list of internetIDs for faculty in this department -- to be used for later queries too	
		$facultyArray = array();
		$deptsql = "SELECT faculty_data.internetID FROM faculty_data INNER JOIN faculty_affiliations ON faculty_affiliations.internetID = faculty_data.internetID WHERE faculty_affiliations.affilID = '$deptId' AND faculty_affiliations.affilClass = 'DISPLAY' AND faculty_data.status_faculty = 1 AND faculty_data.status_current = 1";
		$result = runQuery($con,$deptsql);
		while($row = mysqli_fetch_array($result)) {
			$facultyArray[] = $row['internetID'];
		}

		$internetIdList = "'" . implode('\',\'',$facultyArray) . "'";
	}	

} else {
	$deptId = null;
	$internetIdList = $internetId;
}

$action = $_POST['action'];

if($action === "top_publications") {

	// Retrieve top publication data
	$pubData = array();

	if($type === "faculty") {
		$internetId = trim($internetId, "'");
		$pubsql = "SELECT faculty_publications.authorPosition, publication_data.citedByCount, publication_data.pubTitle, publication_data.pubDate, publication_data.displayDate, publication_data.authors, publication_data.pubName, publication_data.scopus_eid, publication_data.pmid FROM publication_data INNER JOIN faculty_publications ON faculty_publications.mpid = publication_data.mpid WHERE faculty_publications.record_valid = 1 AND faculty_publications.internetID = '$internetId' AND publication_data.source = 'scopus' ORDER BY publication_data.citedByCount DESC LIMIT 10";
		$result = runQuery($con,$pubsql);
		if(mysqli_num_rows($result) != 0) {
			$display = "normal";
			while($row = mysqli_fetch_array($result)) {
				$id_label = 'scopus_eid';
				$id = $row['scopus_eid'];
				$citedByCount = (int)$row['citedByCount'];
				$pubData[] = array($id_label => $id, 'title' => $row['pubTitle'], 'pubName' => $row['pubName'], 'authors' => $row['authors'], 'authorPosition' => $row['authorPosition'],'pubDate' => $row['displayDate'], 'citedByCount' => $citedByCount);		
			
			}	
		} else {
			$display = "none";
		}
		mysqli_free_result($result);

	} else if($type === "dept" || $type === "custom") {
		$pubsql = "SELECT faculty_publications.authorPosition, publication_data.citedByCount, publication_data.pubTitle, publication_data.pubDate, publication_data.displayDate, publication_data.authors, publication_data.pubName, publication_data.scopus_eid, publication_data.pmid, publication_data.source FROM publication_data INNER JOIN faculty_publications ON faculty_publications.mpid = publication_data.mpid WHERE faculty_publications.record_valid = 1 AND publication_data.source = 'scopus' AND faculty_publications.internetID IN ($internetIdList) ORDER BY publication_data.citedByCount DESC LIMIT 10";
		$result = runQuery($con,$pubsql);
		if(mysqli_num_rows($result) == 0) {
			$display = 'none';
		} else {
			$display = 'normal';
			$pubIds = array();	// Dummy array to check for duplicates where UMN faculty are coauthors
			while($row = mysqli_fetch_array($result)) {
				$authorPosition = $row['authorPosition'];
				$id_label = 'scopus_eid';
				$id = $row['scopus_eid'];
				$citedByCount = (int)$row['citedByCount'];
				if(!in_array($id, $pubIds)) {
					$pubIds[] = $id;
					$pubData[] = array($id_label => $id, 'title' => $row['pubTitle'], 'pubName' => $row['pubName'], 'authorPositions' => array($authorPosition), 'authors' => $row['authors'], 'pubDate' => $row['displayDate'], 'citedByCount' => $citedByCount);		
				} else {
					$pubData[count($pubIds)-1]['authorPositions'][] = $authorPosition;
				}
			}	
		}
		mysqli_free_result($result);	
	}


?>

	<div class='sectionHeader'>
		<a target='_blank' href='resources.php?p=faq#where-data-from'><img class='info_bubble' src='inc/images/info_bubble.png'></a>Most-cited publications (top 10)
	</div>
	
	<?php
	if(count($pubData) == 0) {
	?>
		<div class='genericContent'>There are no top-cited publications to display.</div>
	<?php
	} else {
	?>
	<table class='publicationsHeader'>
		<tr>
			<th class='title'>Title</th>
			<th class='date'>Cover Date</th>
			<th class='journal'>Journal</th>
			<th class='authors'>Authors</th>
			<th class='citedByCount'>Scopus Citations</th>
		</tr>
	</table>

	<div class='tableContainer' id='tableContainer_top-publications'>
		<table class='contentTable'>
		<tbody class='publicationsList'>
	
	<?php
		$rowCounter = 0;
		foreach($pubData as $key => $pubInfo) {
			// Escape all publication info for HTML output
			// But only string values. There are no nested array values to escape
			foreach ($pubInfo as $pubkey => $pubvalue) {
				if (is_string($pubvalue)) {
					$pubInfo[$pubkey] = htmlspecialchars($pubvalue);
				}
			}

			$rowCounter++;
			if($rowCounter%2) {
				$rowCSS = 'row1';
			} else {
				$rowCSS = 'row2';
			}
			$date = date("M d Y",strtotime($pubInfo['pubDate']));
			$date = $pubInfo['pubDate'];
			$authorsArray = explode('|',trim($pubInfo['authors']));
			switch($type) {
				case "faculty":
					$authorPosition = $pubInfo['authorPosition'];
					$authorsArray[$authorPosition-1] = "<b>" . $authorsArray[$authorPosition-1] . "</b>";
				break;
			
				case "dept":
				case "custom":
					foreach($pubInfo['authorPositions'] as $position) {
						$authorsArray[$position-1] = "<b>" . $authorsArray[$position-1] . "</b>";
					}
			
				break;
			}
			$authors = implode('; ',$authorsArray);
			$citedByCount = $pubInfo['citedByCount'];
			$title = $pubInfo['title'];
			$journalName = $pubInfo['pubName'];
			$article_url = "http://www.scopus.com/record/display.url?eid=" . $pubInfo['scopus_eid'] . "&origin=resultslist";
			
	?>

			<tr class="<?php echo $rowCSS; ?>" id="record_<?php echo $rowCounter; ?>">
				<td class='title'>
					<a target='_blank' href='<?php echo $article_url; ?>'><?php echo truncate($title); ?></a>
				</td>
				<td class='date'><?php echo $date; ?></td>
				<td class='journal'><?php echo $journalName; ?></td>
				<td class='authors'><?php echo truncate($authors); ?></b></td>
				<td class='citedByCount'><?php echo $citedByCount; ?></td>
			</tr>
	<?php
		}
	?>
		</tbody>
		</table>	
	</div> <!-- // end tableContainer -->
	
<?php
	}
?>


<?php
} else {
	$getStartYear = mysqli_real_escape_string($con, $_POST['startYear']);
	$getEndYear = mysqli_real_escape_string($con, $_POST['endYear']);
	$downloadBase = "inc/modules/ajax/generate-report.php";

	?>

	<script>
		var session_type = "<?php echo $type; ?>";
		var session_internetId = "<?php echo $internetId; ?>";
		var session_dept = "<?php echo $deptId; ?>";
	</script>

	<?php

	if($action === "initialize") {

		// Generate select menus for publication period, by quarters
		$currentYear = date("Y");
		// If only in 1st quarter of new year, set start date to previous year
		if(date("m") < 4) {
			$startYear = $currentYear - 1;
			$endYear = $startYear;
		} else {
			$startYear = $getStartYear;
			$endYear = $getEndYear;
		}

		$startDate = $startYear . "-01-01";
		$endDate = $endYear . "-12-31";

		// Generate list of quarters -- determine earliest year of publication by author
		if($type === "faculty") {
			$internetId = trim($internetId, "'");
			$earliestPubSql = "SELECT publication_data.pubDate FROM publication_data INNER JOIN faculty_publications ON faculty_publications.mpid = publication_data.mpid WHERE faculty_publications.record_valid = 1 AND faculty_publications.internetID = '$internetId' ORDER BY publication_data.pubDate ASC LIMIT 1";
			$result = runQuery($con,$earliestPubSql);
			if(mysqli_num_rows($result) > 0) {
				$obj = mysqli_fetch_object($result);
				$yearMin = date("Y",strtotime($obj->pubDate));	// Earliest year of publication by faculty
				if($yearMin > $startYear) {
					$yearMin = $startYear;
				}			
			} else {
				$yearMin = $startYear;
			}
		} else if($type === "dept" || $type === "custom") {
		
			$earliestPubSql = "SELECT publication_data.pubDate FROM publication_data INNER JOIN faculty_publications ON faculty_publications.mpid = publication_data.mpid WHERE faculty_publications.record_valid = 1 AND faculty_publications.internetID IN ($internetIdList) ORDER BY publication_data.pubDate ASC LIMIT 1";
			$result = runQuery($con,$earliestPubSql);
			$obj = mysqli_fetch_object($result);
			$yearMin = date("Y",strtotime($obj->pubDate));	// Earliest year of publication faculty in department
	
		}

		// Now generate the date range selection
		$yearSelectList = array();
		for($i = $currentYear; $i >= $yearMin; $i--) {
			$yearSelectList[] = $i;
		}

		// Generate the period start select box
		$startHeaderSelect = "<select id='startYearSelectBox' class='yearSelectBox'>";
		foreach($yearSelectList as $year) {
			if($year === $startYear) {
				$startHeaderSelect .= "<option value='" . $year . "' selected>" . $year . "</option>";				
			} else {
				$startHeaderSelect .= "<option value='" . $year . "'>" . $year . "</option>";
			}
		}
		$startHeaderSelect .= "</select>";

		// Generate the period end select box
		$endHeaderSelect = "<select id='endYearSelectBox' class='yearSelectBox'>";
		foreach($yearSelectList as $year) {
			if($year === $endYear) {
				$endHeaderSelect .= "<option value='" . $year . "' selected>" . $year . "</option>";				
			} else {
				$endHeaderSelect .= "<option value='" . $year . "'>" . $year . "</option>";
			}
		}
		$endHeaderSelect .= "</select>";

		$pubsHeaderMsg = "<a target='_blank' href='resources.php?p=faq#where-data-from'><img class='info_bubble' src='inc/images/info_bubble.png'></a>Journal articles, " . $startHeaderSelect . " through " . $endHeaderSelect;

	} else if($action === "update") {
	
		$startDate = intval($_POST['startYear']) . "-01-01";
		$endDate = intval($_POST['endYear']) . "-12-31";
	
	}
	// Retrieve publication data
	$pubData = array();

	if($type === "faculty") {
		$internetId = trim($internetId, "'");	
		$pubsql = "SELECT faculty_publications.authorPosition, publication_data.citedByCount, publication_data.pubTitle, publication_data.pubDate, publication_data.displayDate, publication_data.authors, publication_data.pubName, publication_data.scopus_eid, publication_data.pmid, publication_data.source FROM publication_data INNER JOIN faculty_publications ON faculty_publications.mpid = publication_data.mpid WHERE faculty_publications.record_valid = 1 AND publication_data.pubDate >= '$startDate' AND publication_data.pubDate <= '$endDate' AND faculty_publications.internetID = '$internetId' ORDER BY publication_data.pubDate DESC";
		$result = runQuery($con,$pubsql);
		if(mysqli_num_rows($result) != 0) {
			$display = "normal";
			while($row = mysqli_fetch_array($result)) {
				if($row['source'] === 'scopus') {
					$id_label = 'scopus_eid';
					$id = $row['scopus_eid'];
					$citedByCount = (int)$row['citedByCount'];
				} else if($row['source'] === 'pubmed') {
					$id_label = 'pmid';
					$id = $row['pmid'];
					$citedByCount = 'N/A';
				}
				$pubData[] = array('source'=>$row['source'],$id_label => $id, 'title' => $row['pubTitle'], 'pubName' => $row['pubName'], 'authors' => $row['authors'], 'authorPosition' => $row['authorPosition'],'pubDate' => $row['displayDate'], 'citedByCount' => $citedByCount);		
			
			}	
		} else {
			$display = "none";
		}
		mysqli_free_result($result);

	} else if($type === "dept" || $type === "custom") {
		$pubsql = "SELECT faculty_publications.authorPosition, publication_data.citedByCount, publication_data.pubTitle, publication_data.pubDate, publication_data.displayDate, publication_data.authors, publication_data.pubName, publication_data.scopus_eid, publication_data.pmid, publication_data.source FROM publication_data INNER JOIN faculty_publications ON faculty_publications.mpid = publication_data.mpid WHERE faculty_publications.record_valid = 1 AND publication_data.pubDate >= '$startDate' AND publication_data.pubDate <= '$endDate' AND faculty_publications.internetID IN ($internetIdList) ORDER BY publication_data.pubDate DESC";
		$result = runQuery($con,$pubsql);
		if(mysqli_num_rows($result) == 0) {
			$display = 'none';
		} else {
			$display = 'normal';
			$pubIds = array();	// Dummy array to check for duplicates where UMN faculty are coauthors
			while($row = mysqli_fetch_array($result)) {
				$authorPosition = $row['authorPosition'];
				if($row['source'] === 'scopus') {
					$eid = $row['scopus_eid'];
					$citedByCount = $row['citedByCount'];
					if(!in_array($eid, $pubIds)) {
						$pubIds[] = $eid;
						$pubData[] = array('source'=>'scopus','scopus_eid' => $eid, 'title' => $row['pubTitle'], 'pubName' => $row['pubName'], 'authorPositions' => array($authorPosition), 'authors' => $row['authors'], 'pubDate' => $row['displayDate'], 'citedByCount' => (int)$citedByCount);		
					} else {
						$pubData[count($pubIds)-1]['authorPositions'][] = $authorPosition;
					}
			
				} else if($row['source'] === 'pubmed') {
					$pmid = $row['pmid'];
					if(!in_array($pmid,$pubIds)) {
						$pubIds[] = $pmid;
						$pubData[] = array('source' => 'pubmed', 'pmid' => $pmid, 'title' => $row['pubTitle'], 'pubName' => $row['pubName'], 'authorPositions' => array($authorPosition), 'authors' => $row['authors'], 'pubDate' => $row['displayDate'], 'citedByCount' => 'N/A');
					} else {
						$pubData[count($pubIds)-1]['authorPositions'][] = $authorPosition;
					}
			
				}

			}	
		}
		mysqli_free_result($result);	
	}



	if($action === "initialize") {
		if($type === "faculty") {
			$downloadQuery = "?id=" . $internetId . "&type=faculty&action=publist&startDate=" . $startDate . "&endDate=" . $endDate;
		} elseif($type === "dept") {
			$downloadQuery = "?id=" . $deptId . "&type=dept&action=publist&startDate=" . $startDate . "&endDate=" . $endDate;
		} elseif($type === "custom") {
			$downloadQuery = "?id=" . str_replace('\'','',$internetIdList) . "&type=custom&action=publist&startDate=" . $startDate . "&endDate=" . $endDate;
	
		}

		?>

		<div class='sectionHeader'>
			<?php echo $pubsHeaderMsg; ?>
			<span id='download'>
				<a id='downloadLink' name='download' href='<?php echo $downloadBase . $downloadQuery; ?>'>Download all records</a>
				<?php if($type === "faculty") { ?>
			
				<a id="generate_cv_link">Generate citations</a>
			
				<?php } ?>
			</span>
		</div>

	
		<?php

		if($display === "none") {
		?>
			<div id='publicationLoadContent'>
				<div class='genericContent'>
		<?php
				if($type === "faculty") {
					print "This author has no publications listed in Scopus or imported from PubMed within the given time period.";
				} elseif($type === "dept") {
					print "This department has no faculty publications listed in Scopus or imported from PubMed within the given time period.";
				} else if($type === "custom") {
					print "This subset of faculty has no publications listed in Scopus or imported from PubMed within the given time period.";
				}
		?>
				</div>
			</div>
				
		<?php
		} else if($display === "normal") {
		?>

			<div id='publicationLoadContent'>

				<table class='publicationsHeader'>
					<tr>
						<th class='source'></th>
						<th class='title'>Title</th>
						<th class='date'>Cover Date</th>
						<th class='journal'>Journal</th>
						<th class='authors'>Authors</th>
						<th class='citedByCount'>Scopus Citations</th>
					</tr>
				</table>
			
				<div class='tableContainer' id='tableContainer_publications'>

					<table class='contentTable'>
					<tbody class='publicationsList'>
			<?php
					$rowCounter = 0;
					foreach($pubData as $key => $pubInfo) {
						// Escape all publication info for HTML output
						foreach ($pubInfo as $pubkey => $pubvalue) {
							if (is_string($pubvalue)) {
								$pubInfo[$pubkey] = htmlspecialchars($pubvalue);
							}
						}

						$rowCounter++;
						if($rowCounter%2) {
							$rowCSS = 'row1';
						} else {
							$rowCSS = 'row2';
						}
						$date = date("M d Y",strtotime($pubInfo['pubDate']));
						$date = $pubInfo['pubDate'];
						$authorsArray = explode('|',trim($pubInfo['authors']));
						switch($type) {
							case "faculty":
								$authorPosition = $pubInfo['authorPosition'];
								$authorsArray[$authorPosition-1] = "<b>" . $authorsArray[$authorPosition-1] . "</b>";
							break;
						
							case "dept":
							case "custom":
								foreach($pubInfo['authorPositions'] as $position) {
									$authorsArray[$position-1] = "<b>" . $authorsArray[$position-1] . "</b>";
								}
						
							break;
						}
						$authors = implode('; ',$authorsArray);
						$citedByCount = $pubInfo['citedByCount'];
						$title = $pubInfo['title'];
						$journalName = $pubInfo['pubName'];
					
						if($pubInfo['source'] === 'scopus') {
							$article_url = "http://www.scopus.com/record/display.url?eid=" . $pubInfo['scopus_eid'] . "&origin=resultslist";
						} else if($pubInfo['source'] === 'pubmed') {
							$article_url = "http://www.ncbi.nlm.nih.gov/pubmed/" . $pubInfo['pmid'];
						}
			?>
						<tr class="<?php echo $rowCSS; ?>" id="record_<?php echo $rowCounter; ?>">
							<td class='source'>
								<?php
								if($pubInfo['source'] === 'scopus') {
								?>
									<img src="inc/images/scopus_flag.png">
								<?php
								} else if($pubInfo['source'] === 'pubmed') {
								?>
									<img src="inc/images/pubmed_flag.png">
								<?php
								}
								?>
							</td>
							<td class='title'>
						
								<a target='_blank' href="<?php echo $article_url; ?>"><?php echo truncate($title); ?></a>
							</td>
							<td class='date'><?php echo $date; ?></td>
							<td class='journal'><?php echo $journalName; ?></td>
							<td class='authors'><?php echo truncate($authors); ?></td>
							<td class='citedByCount'><?php echo $citedByCount; ?></td>
						</tr>
			<?php
					}

			?>
					</tbody>
					</table>
				</div> <!-- end tableContainer -->
			</div> <!-- end publicationLoadContent -->

			<?php
	
		}

	} else if($action === "update") {
		if($type === "faculty") {
			$downloadQuery = "?id=" . $internetId . "&type=faculty&action=publist&startDate=" . $startDate . "&endDate=" . $endDate;
		} elseif($type === "dept") {
			$downloadQuery = "?id=" . $deptId . "&type=dept&action=publist&startDate=" . $startDate . "&endDate=" . $endDate;
		} elseif($type === "custom") {
			$downloadQuery = "?id=" . str_replace('\'','',$internetIdList) . "&type=custom&action=publist&startDate=" . $startDate . "&endDate=" . $endDate;	
		}
		?>
		<script>
	
			$("#downloadLink").attr("href",<?php echo "\"" . $downloadBase . $downloadQuery . "\""; ?>);
		</script>
	
		<?php

		if($display === "none") {
		
		?>
	
			<div class='genericContent'>

		<?php
	
				if($type === "faculty") {
					print "This author has no publications listed in Scopus or imported from PubMed within the given time period.";
				} elseif($type === "dept") {
					print "This department has no faculty publications listed in Scopus or imported from PubMed within the given time period.";
				} elseif($type === "custom") {
					print "This subset of faculty has no publications listed in Scopus or imported from PubMed within the given time period.";
				}
	
		?>

		</div>
	
		<?php
		
		} else if($display === "normal") {

		?>
		<table class='publicationsHeader'>
			<tr>
				<th class='source'></th>
				<th class='title'>Title</th>
				<th class='date'>Cover Date</th>
				<th class='journal'>Journal</th>
				<th class='authors'>Authors</th>
				<th class='citedByCount'>Scopus Citations</th>
			</tr>
		</table>
	
		<div class='tableContainer'>
			<table class='contentTable'>
			<tbody class='publicationsList'>
		
		<?php
					$rowCounter = 0;
					foreach($pubData as $key => $pubInfo) {
						// Escape all publication info for HTML output
						foreach ($pubInfo as $pubkey => $pubvalue) {
							if (is_string($pubvalue)) {
								$pubInfo[$pubkey] = htmlspecialchars($pubvalue);
							}
						}

						$rowCounter++;
						if($rowCounter%2) {
							$rowCSS = 'row1';
						} else {
							$rowCSS = 'row2';
						}
						$date = date("M d Y",strtotime($pubInfo['pubDate']));
						$date = $pubInfo['pubDate'];
						$authorsArray = explode('|',trim($pubInfo['authors']));
						switch($type) {
							case "faculty":
								$authorPosition = $pubInfo['authorPosition'];
								$authorsArray[$authorPosition-1] = "<b>" . $authorsArray[$authorPosition-1] . "</b>";
							break;
						
							case "dept":
							case "custom":
								foreach($pubInfo['authorPositions'] as $position) {
									$authorsArray[$position-1] = "<b>" . $authorsArray[$position-1] . "</b>";
								}
						
							break;
						}
						$authors = implode('; ',$authorsArray);
						$citedByCount = $pubInfo['citedByCount'];
						$title = $pubInfo['title'];
						$journalName = $pubInfo['pubName'];
						if($pubInfo['source'] === 'scopus') {
							$article_url = "http://www.scopus.com/record/display.url?eid=" . $pubInfo['scopus_eid'] . "&origin=resultslist";
						} else if($pubInfo['source'] === 'pubmed') {
							$article_url = "http://www.ncbi.nlm.nih.gov/pubmed/" . $pubInfo['pmid'];
						}
					
		?>
	
				<tr class="<?php echo $rowCSS; ?>" id="record_<?php echo $rowCounter; ?>">
					<td class='source'>
						<?php
						if($pubInfo['source'] === 'scopus') {
						?>
							<img src="inc/images/scopus_flag.png">
						<?php
						} else if($pubInfo['source'] === 'pubmed') {
						?>
							<img src="inc/images/pubmed_flag.png">
						<?php
						}
						?>
					</td>
					<td class='title'>
						<a target='_blank' href='<?php echo $article_url; ?>'><?php echo truncate($title); ?></a>
					</td>
					<td class='date'><?php echo $date; ?></td>
					<td class='journal'><?php echo $journalName; ?></td>
					<td class='authors'><?php echo truncate($authors); ?></b></td>
					<td class='citedByCount'><?php echo $citedByCount; ?></td>
				</tr>
		<?php
				}
		?>
			</tbody>
			</table>
		</div> <!-- // end tableContainer -->
		<?php
		}
	}
}
// Close database connection
closeDB($con);

?>
