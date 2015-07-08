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

Generates fourth metrics visualization: departmental distributions (histograms) of h- and
h(fl)-index values

*/

<script type="text/javascript">
var vis_id = '<?php echo $vis_id; ?>';
var selection = '#' + vis_id;
var h_distribution = <?php echo $data['h_distribution']; ?>;
var hfl_distribution = <?php echo $data['hfl_distribution']; ?>;

// Define basic parameters
var	width = $(selection).width();
	height = $(selection).height();

// For each individual chart
var margin = {top: 15, left: 50, right: 30, bottom: 50};

var	barPadding = 30; //15
	
// Distribution of h-indices -- initialize values
var h_counts = {};
var hfl_counts = {};

var yMin = 0, yMax = 0;
var yTickCount;
for(var i = 0; i < h_distribution.length; i++) {
	var value = h_distribution[i];
	h_counts[value] = h_counts[value] ? h_counts[value]+1 : 1;
	yMin = yMin ? (h_counts[value] < yMin ? h_counts[value] : yMin) : h_counts[value];
	yMax = yMax ? (h_counts[value] > yMax ? h_counts[value] : yMax) : h_counts[value];
	
}

for(var i = 0; i < hfl_distribution.length; i++) {
	var value = hfl_distribution[i];
	hfl_counts[value] = hfl_counts[value] ? hfl_counts[value]+1 : 1;
	yMin = yMin ? (hfl_counts[value] < yMin ? hfl_counts[value] : yMin) : hfl_counts[value];
	yMax = yMax ? (hfl_counts[value] > yMax ? hfl_counts[value] : yMax) : hfl_counts[value];
	
}

var xMin = Math.min.apply(null,h_distribution);
if(Math.min.apply(null,hfl_distribution) < xMin) {
	xMin = Math.min.apply(null,hfl_distribution);
}
var xMax = Math.max.apply(null,h_distribution);

// Define chart parameters
var xDomain = d3.range(xMin,xMax+1);

var xStep;
if(xMax > 30) {
	xStep = 5;
} else {
	xStep = 1;
}

var xScale = d3.scale.ordinal()
			.domain(xDomain)
			.rangeRoundBands([margin.left, width-margin.right],0.2,0);
			
var yScale = d3.scale.linear()
			.domain([0, yMax+1])
			.range([height-margin.bottom, margin.top]);

var distr_xAxis = d3.svg.axis()
		.orient("bottom")
		.tickValues(d3.range(xMin,xMax+1,xStep))
		.scale(xScale);
		

var yAxis = d3.svg.axis()
		.orient("left")
		.tickFormat(d3.format("0f"))
		.scale(yScale);

var barWidth = xScale.rangeBand()/2;

var svg_vis_4 = d3.select(selection).append("svg")
			.attr("width",width)
			.attr("height",height);
			
var g_vis_4 = svg_vis_4.append("g")
			.attr("id","g_" + vis_id)
			.attr("width",width-margin.left-margin.right)
			.attr("height",height-margin.top-margin.bottom);


var h_distr_bars = g_vis_4.selectAll("rect.h")
			.data(d3.entries(h_counts))
			.enter()
			.append("rect")
			.attr("class","dataBar")
			.attr("x",function(d) {
				return xScale(d.key);
			})
			.attr("y",function(d) { return yScale(d.value); })
			.attr("width",barWidth)
			.attr("height",function(d) { return (height-margin.bottom) - yScale(d.value); });
var hfl_distr_bars = g_vis_4.selectAll("rect.hfl")
			.data(d3.entries(hfl_counts))
			.enter()
			.append("rect")
			.attr("class","dataBar")
			.attr("x",function(d) {
				return xScale(d.key) + barWidth;
			})
			.attr("y",function(d) { return yScale(d.value); })
			.attr("width",barWidth)
			.attr("height",function(d) { return (height-margin.bottom) - yScale(d.value); })
			.style("fill","orange");

var xAxisLine = g_vis_4.append("g")
			.attr("class","xAxis")
			.attr("transform","translate(" + 0 + "," + (height-margin.bottom) + ")")
			.call(distr_xAxis);
			
var yAxisLine = g_vis_4.append("g")
			.attr("class","yAxis")
			.attr("transform","translate(" + margin.left + "," + 0 + ")")
			.call(yAxis);

var xAxisLabel = g_vis_4.append("text")
			.attr("class","axisLabel")
			.attr("x",margin.left + (width-margin.left-margin.right)/2)
			.attr("y",height-15)
			.attr("text-anchor","middle")
			.text("h-index/h(fl)-index");

var yAxisLabel = g_vis_4.append("text")
			.attr("class","axisLabel")
			.attr("text-anchor","middle")
			.attr("transform","rotate(-90)")
			.attr("y",15)
			.attr("x",-(margin.top+(height-margin.top-margin.bottom)/2))
			.text("Faculty Count");

if(typeof faculty_hIndex !== "undefined") {
	var faculty_h_line = g_vis_4.append("line")
			.attr("x1",xScale(faculty_hIndex))
			.attr("y1",height-margin.bottom)
			.attr("x2",xScale(faculty_hIndex))
			.attr("y2",margin.top)
			.style("stroke-width",2)
			.style("stroke","steelblue");

	var faculty_h_line_label = g_vis_4.append("text")
		.attr("text-anchor","middle")
		.attr("class","chartLabel")
		.attr("transform","rotate(-90)")
		.attr("y",xScale(faculty_hIndex)-2)
		.attr("x",-(margin.top+(height-margin.top-margin.bottom)/2))
		.text("h-index " + faculty_hIndex);
		
	faculty_hIndex = null;
}

if(typeof faculty_hflIndex !== "undefined") {
	var faculty_hfl_line = g_vis_4.append("line")
			.attr("x1",xScale(faculty_hflIndex))
			.attr("y1",height-margin.bottom)
			.attr("x2",xScale(faculty_hflIndex))
			.attr("y2",margin.top)
			.style("stroke-width",2)
			.style("stroke","orange");

	var faculty_hfl_line_label = g_vis_4.append("text")
		.attr("text-anchor","middle")
		.attr("class","chartLabel")
		.attr("transform","rotate(-90)")
		.attr("y",xScale(faculty_hflIndex)-2)
		.attr("x",-(margin.top+(height-margin.top-margin.bottom)/2))
		.text("h(f)-index " + faculty_hflIndex);
			
	faculty_hflIndex = null;
}

</script>