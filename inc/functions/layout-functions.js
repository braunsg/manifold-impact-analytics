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

Defines JavaScript/jQuery functions used for overall page layouts

*/

$(document).ready(function() {
	
	// Function to open PubMed import box
	$("#pubmed_import").click(function() {
		$.get("inc/modules/pubmed/pubmed-search.php",function(pubmed_form) {
			var id;
			$("body").append(pubmed_form);
		});
		return false;
	});
	
	// Function allowing users to change the display department of faculty
	$("#change_dept").click(function() {
		var change_dept_select = "<select id='change_dept_select'><option>Select department...</option>";
		$.getJSON("inc/modules/filter/generate-depts.php",{return_type: 'json'}, function(data) {
			for(var dept_id in data) {
				change_dept_select += "<option value='" + dept_id + "'>" + data[dept_id] + "</option>";
			}
			change_dept_select += "</select>";
			$("#deptLabel").html(change_dept_select);
			$("#deptLabel").append($("<button id='change_dept_confirm'>Confirm</button>"));
			$("#change_dept_confirm").click(function() {
				var new_dept_id = $("#change_dept_select").val();
				$.post("inc/modules/admin/change-dept.php",{internetID: internetId, new_deptID: new_dept_id}, function(response) {
					$("#deptLabel").html(response);
					location.reload();
				});
			});
		});

	});
	
	// Function that adjusts department/custom profile Faculty Summary table display
	if($("#tableContainer_faculty").length > 0) {
	
		// Adjust the table width to account for scroll bar
		var scrollbarWidth = $("#tableContainer_faculty").width() - $("#facultyList").width();
		$("#facultyHeader").css("padding-right",scrollbarWidth + "px");	
	}
	
});