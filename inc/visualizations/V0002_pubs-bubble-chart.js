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

Generates second metrics visualization: publications plotted over time

*/

<script type="text/javascript">
var vis_id = '<?php echo $vis_id; ?>';
var selection = '#' + vis_id;

if(typeof citation_data === 'undefined') {
	var citation_data = <?php echo $data['citation_data']; ?>;
}

// Get max citation count before sorting by date

var yMax = citation_data[0]["citedByCount"];

citation_data.sort(function(a,b) {
	return new Date(b.pubDate) - new Date(a.pubDate);
});


// Define basic parameters
var	width = $(selection).width();
	height = $(selection).height();

// For each individual chart
var margin = {top: 15, left: 65, right: 30, bottom: 50};

var	barPadding = 30; //15
	
var xMin = citation_data[0]["pubDate"];
var xMax = citation_data[citation_data.length-1]["pubDate"];


// Define chart parameters

var xDomain = citation_data.map(function(d) { return new Date(d["pubDate"]); });

var x = [xDomain[xDomain.length-1],xDomain[0]];


var xScale = d3.time.scale()
			.domain(x)
			.range([margin.left, width-margin.right]);
			
var yScale = d3.scale.linear()
			.domain([0, yMax])
			.rangeRound([height-margin.bottom, margin.top]);

var rScale = d3.scale.linear()
			.domain([0,yMax])
			.rangeRound([1,150]);

var line = d3.svg.line()
	.x(function(d) { return xScale(new Date(d.pubDate)); })
    .y(function(d) { return yScale(d.citedByCount); });

var xAxis = d3.svg.axis()
		.orient("bottom")
		.tickFormat(d3.time.format('%Y'))
		.scale(xScale);

var yAxis = d3.svg.axis()
		.orient("left")
		.scale(yScale);

// var barWidth = xScale.rangeBand()/2;
var barWidth = 15;

var svg_vis_2 = d3.select(selection).append("svg")
			.attr("width",width)
			.attr("height",height);

var g_vis_2 = svg_vis_2.append("g")
			.attr("id","g_" + vis_id)
			.attr("width",width-margin.left-margin.right)
			.attr("height",height-margin.top-margin.bottom);

var data_circles = g_vis_2.selectAll("circle")
			.data(citation_data)
			.enter()
			.append("circle")
			.attr("cx",function(d) { return xScale(new Date(d.pubDate)); })
			.attr("cy",function(d) { return yScale(d.citedByCount); })
			.attr("r",function(d) { return rScale(d.citedByCount); })
			.attr("class","circleMarker")
			.style("stroke","#fff")
			.style("stroke-width",2)
			.style("fill",function(d) {
				if(d.fl == true) {
					return "orange";
				}
			})
			.on("mouseover",function() {
				d3.select(this).style("cursor","pointer");
			})
			.on("click", function(d) {
				if(d.eid !== "NULL") {
					window.open("http://www.scopus.com/record/display.url?eid=" + d.eid + "&origin=resultslist");
				}
			});
			
var xAxisLine = g_vis_2.append("g")
			.attr("class","xAxis")
			.attr("transform","translate(" + 0 + "," + (height-margin.bottom) + ")")
			.call(xAxis)
			.selectAll("text")
				.attr("dy",0)
				.attr("dx",20)
				.attr("transform","rotate(60)");
			
var yAxisLine = g_vis_2.append("g")
			.attr("class","yAxis")
			.attr("transform","translate(" + margin.left + "," + 0 + ")")
			.call(yAxis);

var xAxisLabel = g_vis_2.append("text")
			.attr("class","axisLabel")
			.attr("x",margin.left + (width-margin.left-margin.right)/2)
			.attr("y",height-5)
			.attr("text-anchor","middle")
			.text("Date of Publication");

var yAxisLabel = g_vis_2.append("text")
			.attr("class","axisLabel")
			.attr("text-anchor","middle")
			.attr("transform","rotate(-90)")
			.attr("y",15)
			.attr("x",-(margin.top+(height-margin.top-margin.bottom)/2))
			.text("Citation Count");
</script>