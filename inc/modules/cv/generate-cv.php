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

Generate citations for displayed publications in required CV format (University of Minnesota 
Medical School specific)

-->

<div id="popout_screen_container">

	<style><?php include("../../style/style_cv.php"); ?></style>
	<script>
	$(document).ready(function() {
			close_popout();
		});

	</script>
	
	<?php
	include("../../config/default-config.php");
	include("../../functions/default-functions.php");
	
	// Reinstantiate database connection
	$con = connectDB();

	$id = mysqli_real_escape_string($con, trim($_POST["id"], "'"));
	$startDate = mysqli_real_escape_string($con, $_POST["startDate"]);
	$endDate = mysqli_real_escape_string($con, $_POST["endDate"]);

	// Generate page content

	// Run query to get publication data

	$sql = "SELECT publication_data.mpid,publication_data.scopus_eid, publication_data.pmid, publication_data.pubTitle, publication_data.pubName, publication_data.pubDate, publication_data.volume, publication_data.issue, publication_data.pageRange,publication_data.authors, publication_data.citedByCount, faculty_publications.authorPosition, publication_data.source FROM publication_data INNER JOIN faculty_publications ON faculty_publications.mpid = publication_data.mpid WHERE faculty_publications.record_valid = 1 AND faculty_publications.internetID = '$id' AND publication_data.pubDate >= '$startDate' AND publication_data.pubDate <= '$endDate' ORDER BY publication_data.pubDate DESC";
	$result = runQuery($con,$sql);

	?>



	<div id="cv_generator_container">
	<div id="close_window"><img id="close" src="inc/images/close.png"></div>
	
	
		<div class="cv_section_header">Peer-Reviewed Publications</div>
		<div class="cv_section_container">

	<?php

	$index = 0;
	if(mysqli_num_rows($result) > 0) {
	
	?>
	
		<div class="cv_entry_row">
			<div class="index"><span class="cv_table_header">Index</span></div>
			<div class="citation"><span class="cv_table_header">Citation</span></div>
			<div class="citation_count"><span class="cv_table_header">Scopus Citation Count</span></div>
		</div>	

	<?php	
		while($row = mysqli_fetch_array($result)) {
			// Apply HTML escaping to all fields before output
			foreach ($row as $htmlkey => $htmlvalue) {
				$row[$htmlkey] = htmlspecialchars($htmlvalue);
			}
	?>

				<div class="cv_entry_container" id="<?php echo $row['mpid']; ?>">
					<div class="cv_entry_row">
						<div class="index"><?php echo ++$index; ?>
						</div>
						<div class="citation">
							<?php 
					
							$authors = explode("|",$row['authors']);
							$authorPosition = $row['authorPosition'];
							$name_array = array();
							foreach($authors as $ind => $author_name) {
								$this_name = "";
								$split_fullname = explode(",",$author_name);
								$lastname = $split_fullname[0];
								$this_name .= $lastname . ",";
								$firstname = Ltrim($split_fullname[1]);
								$split_firstname = explode(" ",$firstname);
								foreach($split_firstname as $name_part) {
									if(strpos(".",$name_part) >= 0) {
										$this_name .= " " . substr($name_part, 0,1) . ".";
									} else {
										$this_name .= " " . $name_part;
									}
								}
								if(($ind+1) == $authorPosition) {
									$this_name = "<b>" . $this_name . "</b>";
								}
								$name_array[] = $this_name;
							}
							$authors_list = implode(", ",$name_array);
							$title = $row['pubTitle'];
							$journal = $row['pubName'];
							$pubYear = date("Y",strtotime($row['pubDate']));
							$volume = $row['volume'];
							if($row['issue'] !== "") {
								$issue = "(" . $row['issue'] . ")";
							} else {
								$issue = "";
							}
							if($row['pageRange'] !== "") {
								$pages = ":" . $row['pageRange'];
							} else {
								$pages = "";
							}
					
							print $authors_list . " " . $title . ". <u>" . $journal . "</u>. " . $pubYear . "; " . $volume . $issue . $pages . ".";	
							if($row['source'] === 'scopus') {
								$article_url = "http://www.scopus.com/record/display.url?eid=" . $row['scopus_eid'] . "&origin=resultslist";
							} else if($row['source'] === 'pubmed') {
								$article_url = "http://www.ncbi.nlm.nih.gov/pubmed/" . $row['pmid'];
							}
							
							print "<br><br><b>URL</b>: <a target='_blank' href='$article_url'>$article_url</a>";
							?>
					
						</div>
						<div class="citation_count"><?php echo $row['citedByCount']; ?>
						</div>
					</div>
				</div>
	<?php
		}
	} else {
	?>
		<div class="cv_entry_container">
			<div class="cv_entry_row">There are no publications to list for the given time period.</div>
		</div>
	<?php
	}
	?>
		</div>

	</div>
</div>

<?php

mysqli_free_result($result);
closeDB($con);

?>
