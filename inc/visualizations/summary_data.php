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

Generates boxplots in OVERVIEW module on pages

-->

<style>

.xAxisLine path,
.xAxisLine line {
	fill: none;
	stroke: black;
	shape-rendering: crispEdges;
	width:1;
}

.xAxisLine text {
	font-family: Calibri, Candara, Segoe, 'Segoe UI', Optima, Arial, sans-serif;
	font-size: 0.9em;
	font-weight: bold;
}


.metricLabel {
	font-family: Baskerville, 'Baskerville Old Face', 'Goudy Old Style', Garamond, 'Times New Roman', serif;
	font-family: Calibri, Candara, Segoe, 'Segoe UI', Optima, Arial, sans-serif;
	font-size: 15px;
	font-weight: bold;
	text-transform:lowercase;
	font-variant:small-caps;
	stroke: none;
	fill: #333;
	text-anchor:start;
}

.category_header {
	font-family: Baskerville, 'Baskerville Old Face', 'Goudy Old Style', Garamond, 'Times New Roman', serif;
	font-family: Calibri, Candara, Segoe, 'Segoe UI', Optima, Arial, sans-serif;

	font-size: 1.2em;
	margin-bottom:5px;
	cursor:default;
	font-weight:bold;
}	

.boxplot_container {
	margin-bottom:10px;
	cursor:default;
}

.metric_marker {
	fill: steelblue;
}

</style>



<?php
	foreach($profile_data_dept as $category => $subcategories) {
		if($type !== "faculty") {
			$category .= " (Median)";
		}
		?>
		<div class="category_header"><?php echo $category; ?></div>
		<?php
		foreach($subcategories as $metric => $metric_label) {
		?>
			<div class="boxplot_container" id="vis_<?php echo $metric; ?>"></div>
	<?php
		}
	}

?>

<script>

var type = "<?php echo $type; ?>";
var dept_data = <?php echo json_encode($profile_data_dept,true); ?>;

if(type === "faculty") {
	var faculty_data = <?php echo json_encode($profile_data_faculty,true); ?>;
}

var margin = {top: 5, left: 120, right: 15, bottom: 30};

var width = $("#summaryData").width(),
	height = 50+margin.top + margin.bottom,
	box_height = 10,
	box_padding = box_height,
	labelAdjustment = "0.25em",
	metric_marker_radius = 4;


var positions = {1: {y1: margin.top, y2:margin.top + box_height}, 2: {y1:margin.top + box_height + box_padding, y2:(margin.top + box_height + box_padding) + box_height}};

for(var category in dept_data) {
	category_data = dept_data[category];
	for(var metric in category_data) {
// 		$("body").append("<div id='vis_" + metric + "'></div>")
		var selection = "#vis_" + metric;

		if(metric === "hIndex" || metric === "pubCount" || metric === "totalCitations") {
			var box_counter = 0;

// 			var domain_min = category_data[metric]["min"];
// 			var domain_max = category_data[metric]["max"];

			switch(metric) {
				case "hIndex":
					var domain_min = category_data["hflIndex"]["min"];		
					break;
				case "pubCount":
					var domain_min = category_data["flPubCount"]["min"];						
					break;
				case "totalCitations":
					var domain_min = category_data["totalflCitations"]["min"];						
					break;
			}

			var domain_max = category_data[metric]["max"];

			var svg = d3.select(selection)
				.append("svg")
				.attr("width",width)
				.attr("height",height);


			var plot_scale = d3.scale.linear()
				.domain([domain_min,domain_max])	
				.range([margin.left, width-margin.right]);


			var axis = d3.svg.axis()
				.tickFormat(d3.format("s"))
				.scale(plot_scale);

			var xAxisLine = svg.append("g")
				.attr("class","xAxisLine")
				.attr("transform","translate(0,"+(height-margin.bottom)+")")
				.call(axis)
				.selectAll("text")
					.attr("y",0)
					.attr("x",6)
					.attr("dy",".85em")
					.attr("transform","rotate(45)")
					.style("text-anchor","start");
		} 

		box_counter++;
		box_height = positions[box_counter]["y2"] - positions[box_counter]["y1"];
		box_midpoint = positions[box_counter]["y1"] + (box_height/2);
		
		var metric_label = svg.append("text")
			.attr("class","metricLabel")
			.attr("x",5)
			.attr("y",box_midpoint)
			.attr("dy",labelAdjustment)
			.text(category_data[metric]["label"]);
	
		var plot_line = svg.append("line")
			.attr("x1",function() { return plot_scale(category_data[metric]["min"]); })
			.attr("y1",box_midpoint)
			.attr("x2",function() { return plot_scale(category_data[metric]["max"]); })
			.attr("y2",box_midpoint)
			.attr("stroke","black")
			.attr("stroke-width",1);

		var box = svg.append("rect")
			.attr("x",function() { return plot_scale(category_data[metric]["lower_quartile"]); })
			.attr("y",positions[box_counter]["y1"])
			.attr("width",function() {
				return plot_scale(category_data[metric]["upper_quartile"]) - plot_scale(category_data[metric]["lower_quartile"]);
			})
			.attr("height",box_height)
			.attr("stroke","black")
			.attr("fill","white");

		var line_min = svg.append("line")
			.attr("x1",function() { return plot_scale(category_data[metric]["min"]); })
			.attr("y1",positions[box_counter]["y1"])
			.attr("x2",function() { return plot_scale(category_data[metric]["min"]); })
			.attr("y2",positions[box_counter]["y2"])
			.attr("stroke","black")
			.attr("stroke-width",1);

		var line_min = svg.append("line")
			.attr("x1",function() { return plot_scale(category_data[metric]["max"]); })
			.attr("y1",positions[box_counter]["y1"])
			.attr("x2",function() { return plot_scale(category_data[metric]["max"]); })
			.attr("y2",positions[box_counter]["y2"])
			.attr("stroke","black")
			.attr("stroke-width",1);

		var line_median = svg.append("line")
			.attr("x1",function() { return plot_scale(category_data[metric]["median"]); })
			.attr("y1",positions[box_counter]["y1"])
			.attr("x2",function() { return plot_scale(category_data[metric]["median"]); })
			.attr("y2",positions[box_counter]["y2"])
			.attr("stroke","black")
			.attr("stroke-width",1);

		if(type === "faculty") {
			svg.append("circle")
				.attr("class","metric_marker")
				.attr("r",metric_marker_radius)
				.attr("cx", plot_scale(faculty_data[metric]))
				.attr("cy",box_midpoint);
				
			svg.append("text")
				.attr("class","metricLabel")
				.attr("x", margin.left - 50)
				.attr("y",box_midpoint)
				.attr("dy",labelAdjustment)
				.text(faculty_data[metric]);
				
		} else if(type === "dept" || type === "custom") {
			svg.append("circle")	
				.attr("r",metric_marker_radius)
				.attr("class","metric_marker")
				.attr("cx", plot_scale(category_data[metric]["median"]))
				.attr("cy",box_midpoint);


			svg.append("text")
				.attr("class","metricLabel")
				.attr("x", margin.left - 50)
				.attr("y",box_midpoint)
				.attr("dy",labelAdjustment)
				.text(category_data[metric]["median"]);
			
		}

	}
}
</script>