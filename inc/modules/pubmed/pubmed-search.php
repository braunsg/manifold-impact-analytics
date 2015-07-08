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

Generates popout to search PubMed for publication records to import

-->

<div id="popout_screen_container">


<script>
	$(document).ready(function() {
		function retrieveResults(type,parameters,dateThreshold) {
			var loading = $("<div class='pubLoading'><span class='text'>Loading search results from PubMed...please be patient.</span><div class='loadingBar'></div></div>");
			$("#resultsContainer").html(loading);
			var moveTo = $("#resultsContainer").position();
			$("#searchContainer").scrollTop(moveTo.top);
			$.post("inc/modules/pubmed/search-response.php",{searchType:type, parameters: parameters, dateThreshold:dateThreshold},function(response) {
				$("#resultsContainer").html(response);


				$("[id^=pmimport_]").click(function() {
					var pmid = $(this).prop("id").replace("pmimport_","");
					if(this.checked == true) {
						$("#pmrecord_" + pmid).css("background-color","#CCFFCC");
					} else {
						$("#pmrecord_" + pmid).css("background-color","#FFFFFF");
					}						
					if($("[id^=pmimport_]:checked").length > 0) {
						$(".import_label").removeClass("import_unselected").addClass("import_selected");					
					} else {
						$(".import_label").removeClass("import_selected").addClass("import_unselected");					
					}

				});
				
				$(".import_label").click(function() {
					if($(this).hasClass("import_selected") == true) {
						var import_data = {};
						var import_order = [];
						$("[id^=pmimport_]:checked").each(function() {
							var id = $(this).prop("id").replace("pmimport_","");
							import_data[id] = aggregate_data[id];
							import_order.push(id);
						});
						$.post("inc/modules/pubmed/import-confirm.php",{import_data: import_data, import_order: import_order}, function(confirm_import) {
							$("#searchContainer").scrollTop(0).html(confirm_import);
							$("img#close").click(function() {
								$("#pubmed_screen_container").remove();
							});
					
							$("[id^=pmimport_]").click(function() {
								var pmid = $(this).prop("id").replace("pmimport_","");
								if(this.checked == true) {
									$("#pmrecord_" + pmid).css("background-color","#CCFFCC");
								} else {
									$("#pmrecord_" + pmid).css("background-color","#FFFFFF");
								}						
								if($("[id^=pmimport_]:checked").length > 0) {
									$(".confirm_label").removeClass("confirm_unselected").addClass("confirm_selected");					
								} else {
									$(".confirm_label").removeClass("confirm_selected").addClass("confirm_unselected");
									$(".confirm_label").find("a").attr("href","return false;");
				
								}

							});		
					
							$("#confirm_submission_button").click(function() {
								var import_error = 0;
								var getPosition;
								if($(this).hasClass("confirm_selected") == true) {
									var submit_data = {};
									$("[id^=pmimport_]:checked").each(function() {
										var id = $(this).prop("id").replace("pmimport_","");
										// Get author position
										if($("input[name=authorPosition_" + id + "]").length > 0) {
											if($("input[name=authorPosition_" + id + "]:checked").length == 1) {
												getPosition = $("input[name=authorPosition_" + id + "]:checked").val();
											} else {
												if(import_error == 0) {
													import_error = 1;
													alert("You must select your name in the author list of all publications selected for import.");
												}
											}
										} else {
											getPosition = 0;
										}										
										submit_data[id] = import_data[id];
										submit_data[id]["authorPosition"] = getPosition;
									});
									if(import_error == 0) {
										$(this).removeClass("confirm_selected").addClass("confirm_unselected");
										$(this).prepend("<img src='inc/images/loading_circle.gif' style='vertical-align:middle;margin-right:3px;'>");									
										$.post("inc/modules/pubmed/import-records.php",{submit_data: submit_data, internetID: user}, function(submission) {
											$("img#close").click(function() {
												$("#pubmed_screen_container").remove();
											});
								
											$("#searchContainer").scrollTop(0).html(submission);
										});
									}
								}
							});				
						});
					}
				});
								
					

			});
		}
						
		$("button#search").click(function() {
			var parameters = {};
			var pmid = $("#search_pmid").val();
			if(pmid !== "") {
				var type = "pmid";
				parameters["pmid"] = $("#search_pmid").val();
			} else {
				var type = "title";
				parameters["title"] = $("#search_title").val();
			}
			
			retrieveResults(type,parameters,data_current_date);
			
		});
		
		close_popout();
			
	
	});
</script>
<style><?php include("../../style/style_pubmed.php"); ?></style>

<?php
	global $data_current_date;
	$internetId = $_POST['internetID'];
	
// Search form

?>
<div id="searchContainer">
	<div id="close_window"><img id="close" src="inc/images/close.png"></div>
	<div class="sectionHeader">Import from PubMed</div>
	<div id="descriptionContainer">
	<div class="genericContent">In some instances, your entire history of publications may not be captured by Scopus. This may occur when you publish in a journal that Scopus does not yet index, for example, as is true with some open access journals in particular. In these cases, you may be able to find missing publications in PubMed.<br><br>
	If one or more of your publications are missing from Scopus and thus not captured in Manifold, you can use the following form to search PubMed for those missing publications and import them directly into your profile. <b>Note</b>: Publication data from PubMed cannot be manually edited upon import. Also note that calculations of impact metrics, including <i>h</i>-index and citation counts, are based only on Scopus publications as Scopus records provide citation counts for all papers whereas PubMed does not provide these counts.<br><br>
	Search PubMed below by searching for your paper(s) by title. If you know the <a target="_blank" href="http://www.ncbi.nlm.nih.gov/pubmed">PubMed ID</a> for the paper of interest, you may import directly by supplying that ID in the search field provided.<br><br>
	For more information on using this module, please consult the guide on <b><a target="_blank" href="resources.php?p=pubmed-import">Importing publications from PubMed</a></b>.
	</div>
	</div>
	<div id="searchFormContainer">
		<div class="sectionHeader searchFormHeader">Search</div>
		<div id="searchForm">
			<div class="search_row">
				<div class="fieldName">Title</div>
				<div class="searchField"><input type="text" id="search_title"></div>
			</div>
			<div class="search_row">
				<div class="fieldName">PubMed ID</div>
				<div class="searchField"><input type="text" id="search_pmid"></div>
			</div>		
			<button id="search">Search</button>
		</div>
	</div>
	<div id="resultsContainer">
	</div>


</div>


</div>
