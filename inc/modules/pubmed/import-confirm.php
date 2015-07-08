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

Asks the user to confirm the PubMed records they have selected to import into their profile

-->

<script>
	close_popout();
</script>

<?php

	$import_data = $_POST["import_data"];
	$import_order = $_POST["import_order"];
?>	
	
	
	<div id="close_window"><img id="close" src="inc/images/close.png"></div>
	<div class="sectionHeader">Confirm Imported Records</div>
	<div id="descriptionContainer">
		<div class="genericContent">Are you sure you want to import the following records? Attributions to these records will be permanently attached to your profile and cannot be removed automatically. Please select which records you wish to attach to your profile, indicate your author position, and click <b>Confirm Submission</b>.<br><br>
		<b>Note:</b> You will be asked to verify your identity via your University of Minnesota e-mail account before confirmed publications are displayed.<br>
		</div>
	</div>
	<div id="resultsContainer">	
		<div class="import_submit"><div id="confirm_submission_button" class="confirm_label confirm_unselected"><a href="#">Confirm Submission &#10003;</a></div></div>
		<table id="results">
			<thead id="resultsHeader">
				<th class='select'></th>
				<th class='title'>Title</th>
				<th class='date'>Cover Date</th>
				<th class='journal'>Journal</th>
				<th class='authors'>Authors (select position)</th>
			</thead>
			<tbody id='resultsList'>
			
<?php
	foreach($import_order as $pmid) {
		$record_data = $import_data[$pmid];
?>

		<tr class="record" id="pmrecord_<?php echo $pmid; ?>">
			<td class="select"><input type="checkbox" id="pmimport_<?php echo $pmid; ?>"></td>
			<td class="title"><?php echo "<a target='_blank' href='http://www.ncbi.nlm.nih.gov/pubmed/" . $pmid . "'>" . $record_data["pubTitle"] . "</a>"; ?></td>
			<td class="date"><?php echo $record_data["pubDate"]["Month"] . " " . $record_data["pubDate"]["Year"]; ?></td>
			<td class="journal"><?php echo $record_data["pubName"]; ?></td>
			<td class="authors">
				<?php 
					$authorsArray = array();
					if(count($record_data["authorList"]) > 0) {
						foreach($record_data["authorList"] as $authorIndex => $authorInfo) {
							?>
							<input type="radio" name="authorPosition_<?php echo $pmid; ?>" value=<?php echo $authorIndex; ?>><?php echo $authorInfo["LastName"] . ", " . $authorInfo["ForeName"]; ?><br>
							<?php
						}
					} else {
						echo "<i>N/A</i>";
					}
				?>
			</td>
		</tr>

		<?php



	}
	
?>
	
	
		</tbody>
	</table>			
	
	</div>

