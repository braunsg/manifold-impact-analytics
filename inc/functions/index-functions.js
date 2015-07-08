/*

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

Defines JavaScript/jQuery functions used in index.php (Manifold search page)

*/

var autocomplete_id;
$(document).ready(function() {
	
	var autocompleteOffset = $("#omnibox").position();
	var autocompleteHeight = $("#omnibox").css("height");
	var padding = 18;
	var dropdown_padding = 32;
	$("#autocompleteContainer")
		.css({"top":(autocompleteOffset["top"]+autocompleteHeight+padding)+"px","left":autocompleteOffset["left"]+"px"});

	$(document).click(function(e) {
		$("#autocompleteContainer").css("display","none");
	});
		
	var defaultSearchContents = $("#omnibox").val();
	var unfocusColor = $("#omnibox").css("color");
	$("#omnibox").focus(function() {
		if($(this).val() === defaultSearchContents) {
			$(this).val("");
			$(this).css({"color":"#000","font-style":"normal"});
		}
	});

	$("#tabs_container .tab").click(function() {
		if(typeof affilID !== "undefined") {
			delete affilID;
		}
		$(".tab").attr("class","tab");
		$(this).addClass("tab_selected");
		$("#omnibox").val(defaultSearchContents);
		$("#omnibox").css({"color":unfocusColor,"font-style":"italic"});
		searchType = $(this).attr("id");
		$("#quickstats_data").find(".data_box,.full_profile").remove();
		$("[id^=searchbox_container_]").addClass("searchbox_hidden");
		$("#searchbox_container_" + searchType).removeClass("searchbox_hidden");		
	});

	$("#dropdown_select_dept").click(function(e) {
		$("#quickstats_data").find(".data_box,.full_profile").remove();
		xOffset = $(this).position().left;
		yOffset = $(this).position().top + $(this).height() + dropdown_padding;
		parentWidth = $(this).width() + dropdown_padding;
		
		$("#dept-dropdown_results_container").css({"width":parentWidth,"left":xOffset + "px","top":yOffset + "px","display":"block"});

		$(document).bind("click", function() {
			$("#dept-dropdown_results_container").css("display","none");
		});
		e.stopPropagation();
	});

	$("#dropdown_select_faculty").click(function(e) {
		xOffset = $(this).position().left;
		yOffset = $(this).position().top + $(this).height() + dropdown_padding;
		parentWidth = $(this).width() + dropdown_padding;
		
		$("#faculty-dropdown_results_container").css({"width":parentWidth,"left":xOffset + "px","top":yOffset + "px","display":"block"});
		$(document).bind("click", function() {
			$("#faculty-dropdown_results_container").css("display","none");
		});
		e.stopPropagation();
	});

	$("#dept-dropdown_results_container .dropdown_result").click(function() {
		affilName = $(this).text();
		affilID = ($(this).attr("id")).replace("dropdown_result_","");
		$("#dropdown_select_dept").html(affilName);
		$.post("inc/modules/filter/generate-dropdown.php",{affilID: affilID, type:"dropdown"}, function(data) {
			$("#faculty-dropdown_results_container").html(data);
			$("#dropdown_select_faculty").html("Select faculty");

			$("#faculty-dropdown_results_container .dropdown_result").click(function() {
				name = $(this).text();
				getId = $(this).attr("id").replace("dropdown_result_","");
				$("#dropdown_select_faculty").html(name);
				var searchType;
				
				// Check if deptID or internetID -- a deptID will start with either Z or 1
				if(getId.indexOf("Z") == 0 || getId.indexOf("1") == 0) {
					searchType = "dept";
					autocomplete_id = getId;
				} else {
					searchType = "faculty";	
					autocomplete_id = getId.substring(0,getId.indexOf("_"));
					autocomplete_dept = getId.substring(getId.indexOf("_")+1);
				}
				getStats(searchType);

			});			
		});
	});

	var tenure_filter_code;
	$("[id^=tenure_select_]").click(function() {	
		$("[id^=tenure_select_]").removeClass("filter_selected");
		$(this).addClass("filter_selected");

		tenure_filter_code = $(this).attr("id").replace("tenure_select_","");
		if(typeof affilID !== "undefined") {
			submitFilter(affilID);
		}
	});

	$("#custom_select_dept .custom_select_item").click(function() {
		affilName = $(this).text();
		affilID = ($(this).attr("id")).replace("custom_dept_","");
		$("[id^=custom_dept_]").removeClass("filter_selected");
		$(this).addClass("filter_selected");
		submitFilter(affilID);
	});

	function submitFilter(deptID) {
		$.post("inc/modules/filter/generate-dropdown.php",{affilID: affilID, type: "custom", filter: tenure_filter_code}, function(data) {
			$("#custom_select_faculty .custom_select_itemlist").html(data);

			$("#custom_select_faculty .custom_select_item").click(function() {
				var name = $(this).text();
				var getId = $(this).attr("id").replace("custom_faculty_","");
				var internetId = getId.substring(0,getId.indexOf("_"));
				if($("#custom_subset_" + internetId).length == 0) {
					$("#custom_select_subset .custom_select_itemlist").append($("<div id='custom_subset_" + internetId + "' class='custom_select_item'><div class='filter_minus'></div>" + name + "</div>")
						.click(function(e) {
						console.log($(this).attr("id"));
							$(this).remove();
							e.stopPropagation();
						})
					);
				}
			});			
		});
	}

	$("#custom_select_search").click(function() {
		var url;
		var ids = [];
		$("#custom_select_subset .custom_select_itemlist .custom_select_item").each(function() {
			var id = $(this).attr("id").replace("custom_subset_","");
			ids.push(id); 
		});	
		if(ids.length > 0) {
			url = ids.join(",");
			window.location = "profile.php?type=custom&id=" + url;
		}
	});

	$("#omnibox").on('input',function() {
		// In case the window was resized, get corrected autocompleteOffset
		autocompleteOffset = $("#omnibox").position();
		$("#autocompleteContainer")
			.css({"top":(autocompleteOffset["top"]+autocompleteHeight+padding)+"px","left":autocompleteOffset["left"]+"px"});		

		var searchTerm = $(this).val();
		if(searchTerm.length > 0) {
			$("#autocompleteContainer").css("display","block");
			$.post("inc/modules/filter/autocomplete.php",{searchTerm: searchTerm}, function(returnResults) {
				$("#autocompleteContainer").html(returnResults);
				$(".autocomplete_result").click(function(event) {
					var text = $(this).text();
					var getId = $(this).attr("id");
					var searchType;
					
					// Check if deptID or internetID -- a deptID will start with either Z or 1
					if(getId.indexOf("Z") == 0 || getId.indexOf("1") == 0) {
						searchType = "dept";
						autocomplete_id = getId;
					} else {
						searchType = "faculty";	
						autocomplete_id = getId.substring(0,getId.indexOf("_"));
						autocomplete_dept = getId.substring(getId.indexOf("_")+1);
					}
					$("#omnibox").val(text);
					getStats(searchType);
				});

			});



		} else {
			$("#quickstats_data").find(".data_box,.full_profile").remove();		
			$("#autocompleteContainer").css("display","none");
		}		

	});

	$("#omnibox").focusout(function() {
		if($(this).val() === "") {
			$(this).val(defaultSearchContents);
			$(this).css({"color":unfocusColor,"font-style":"italic"});

		}
	});
});	
	
	function getStats(searchType) {
		$.post("inc/modules/ajax/quickstats.php",{type:searchType,id:autocomplete_id},function(response) {
			$("#quickstats_data").html("");
			var data = jQuery.parseJSON(response);
			for(var metric in data) {				
				// Append boxes for quick stats
				$("#quickstats_data").append($("<div class='data_box'><div class='data_header'>" + metric + "</div><div class='metric'>" + data[metric] + "</div></div>").hide());
			}
			$("#quickstats_data").append($("<div class='full_profile' id='" + autocomplete_id + "'>Click to load full profile &rarr;</div>").click(function() { 
				if(searchType === "faculty") {
					window.location = "profile.php?type=faculty&id=" + autocomplete_id;
				} else if(searchType === "dept") {
					window.location = "profile.php?type=dept&id=" + autocomplete_id;
				}
			})
			.hide());

			// Fade in stats boxes
			$(".data_box,.full_profile").each(function(i) {
				$(this).delay(i*40).fadeIn(250);
			});

		});
	}