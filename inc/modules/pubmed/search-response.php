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

Queries PubMed API and returns results based on PubMed import search terms

-->

<?php
include("../../config/default-config.php");
$searchType = $_POST["searchType"];
$date_threshold = $_POST["dateThreshold"];

if($searchType === "title") {

	$urlBase = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=pubmed&term=";
	$searchTerm = $_POST["parameters"]["title"];
	$encoded_query = urlencode($searchTerm . "[title] AND Journal Article[filter]") . "&mindate=1900&maxdate=" . $date_threshold;
	$url = $urlBase . $encoded_query;
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $url
	));

	$response = curl_exec($curl);
	// Construct new XMLReader
	$reader = new XMLReader();

	// Tell the reader to open the bulk export file
	$reader->xml($response);

	// Miscellaneous variables for my own tracking purposes
	$counter = 0;
	$idArray = array();

	// Iterate through the nodes of the XML file
	while($reader->read()) {
	
		if($reader->nodeType == XMLREADER::ELEMENT && $reader->name === 'Id') {
			$pubmedId = $reader->readString();
			$idArray[] = $pubmedId;		
		}

	}

} else {
	$idArray = array($_POST["parameters"]["pmid"]);
}


$fieldsArray = array("MedlineCitation" => array("PMID",
												"Article",
												"AuthorList"),
					 "Article" => array("Journal",
										"ArticleTitle",
										"Pagination"),
					 "Journal" => array("JournalIssue",
										"Title",
										"ISOAbbreviation"),
					 "JournalIssue" => array("Volume",
											 "Issue",
											 "PubDate"),
					 "PubDate" => array("Year",
										"Month",
										"Day"),
					 "DateCreated" => array("Year",
					 						"Month",
					 						"Day"),
					 "Pagination" => array("MedlinePgn"),
					 "AuthorList" => array("Author"),
					 "Author" => array("LastName",
									   "ForeName",
									   "Initials",
									   "AffiliationInfo"),
					 "AffiliationInfo" => array("Affiliation"),
					 "PubmedData" => array("ArticleIdList"),
					 "ArticleIdList" => array("ArticleId"));
			 
$recordFields = array("PMID" => "pmid",
					  "ArticleTitle" => "pubTitle",
					  "Title" => "pubName",
					  "MedlinePgn" => "pageRange",
					  "Volume" => "volume",
					  "Issue" => "issue");
					  
$field_keys = array_keys($fieldsArray);
$readThrough = array();

$aggregate_data = array();

if(count($idArray) > 0) {

?>
	<div class="import_submit"><div class="import_label import_unselected"><a href="#">Import selected publication(s) &rarr;</a></div></div>
	<table id="results">
		<thead id="resultsHeader">
			<th class='select'></th>
			<th class='title'>Title</th>
			<th class='date'>Cover Date</th>
			<th class='journal'>Journal</th>
			<th class='authors'>Authors</th>
		</thead>
		<tbody id='resultsList'>
<?php

	foreach($idArray as $id) {
		$record_data = array();
		$authorIndex = 0;
		$urlBase = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?db=pubmed&id=";
		$searchTerm = urlencode($id);

		$url = $urlBase . $searchTerm . "&retmode=xml";
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url
		));
		$response = curl_exec($curl);

		// Construct new XMLReader
		$reader = new XMLReader();
		$reader->xml($response);
		// Iterate through the nodes of the XML file
		while($reader->read()) {
			if($reader->nodeType == XMLREADER::ELEMENT && array_search($reader->name,$field_keys)) {
				$nodeName = $reader->name;
				$readThrough[] = $nodeName;
				if($nodeName === "Author") {
					$authorIndex++;
				}
			} 

			if($reader->nodeType == XMLREADER::END_ELEMENT && array_search($reader->name,$field_keys)) {
				array_pop($readThrough);
				$nodeName = $readThrough[count($readThrough)-1];
			}

			if($reader->nodeType == XMLREADER::ELEMENT && count($readThrough) > 0) {
				if(in_array($reader->name,$fieldsArray[$nodeName])) {
					if($nodeName === "PubDate") {
						$record_data["pubDate"][$reader->name] = $reader->readString();
					}
					
					if($nodeName === "DateCreated") {
						$record_data["creationDate"][$reader->name] = $reader->readString();
					}
					
					if($nodeName === "Author") {
						$record_data["authorList"][$authorIndex][$reader->name] = $reader->readString();
					}
					
					if($nodeName === "ArticleIdList") {
						if($reader->name === "ArticleId") {
							$record_data["idList"][$reader->getAttribute("IdType")] = $reader->readString();
						}
					}
					if(in_array($reader->name,array_keys($recordFields))) {
						$record_data[$recordFields[$reader->name]] = $reader->readString();
					}
				}
			}

		}
		if(empty($record_data["pubDate"]["Month"]) && empty($record_data["pubDate"]["Day"]) && empty($record_data["pubDate"]["Year"])) {
			if($record_data["creationDate"]["Month"]) {
				$dateObj   = DateTime::createFromFormat('!m', $record_data["creationDate"]["Month"]);
				$monthName = $dateObj->format('F');
				$record_data["pubDate"]["Month"] = $monthName;		
			}
			if($record_data["creationDate"]["Day"]) {
				$record_data["pubDate"]["Day"] = $record_data["creationDate"]["Day"];			
			}
			if($record_data["creationDate"]["Year"]) {
				$record_data["pubDate"]["Year"] = $record_data["creationDate"]["Year"];			
			}
			
		}
		$pmid = $record_data['idList']['pubmed'];
		$aggregate_data[$pmid] = $record_data;
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
							$authorsArray[] = $authorInfo["LastName"] . ", " . $authorInfo["ForeName"];
						}
						echo implode("; ", $authorsArray);
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
	
	<!-- Passing data to JavaScript object for import -->
	<script>
		var aggregate_data = <?php echo json_encode($aggregate_data,true); ?>;
	</script>
<?php
} else {

?>
	<tr><td>No results found.</td></tr>
<?php

}
?>
