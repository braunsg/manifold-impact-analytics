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

Generates first metrics visualization: publications ranked by descending citation count

*/

<script type="text/javascript">
var vis_id = '<?php echo $vis_id; ?>';
var selection = '#' + vis_id;
var citation_data = <?php echo $data['citation_data']; ?>;

if(citation_data.length > 50) {
	trunc_citation_data = citation_data.slice(0,50);
} else {
	trunc_citation_data = citation_data;
}


var yMax = trunc_citation_data[0]["citedByCount"];
var yMin = trunc_citation_data[trunc_citation_data.length - 1]["citedByCount"];
// Define basic parameters

var	width = $(selection).width();
	height = $(selection).height();

// For each individual chart
var margin = {top: 15, left: 60, right: 30, bottom: 50};

var	barPadding = 30; //15

var xDomain = d3.range(1,trunc_citation_data.length+1);

var xScale = d3.scale.ordinal()
			.domain(xDomain)
			.rangeRoundBands([margin.left, width-margin.right],0,0.25);

var yScale = d3.scale.linear()
			.domain([0, yMax])
			.range([height-margin.bottom, margin.top]);


var distr_xAxis = d3.svg.axis()
		.orient("bottom")
		.scale(xScale);
		

var yAxis = d3.svg.axis()
		.orient("left")
		.scale(yScale);

var barWidth = xScale.rangeBand()/2;

var svg_vis_1 = d3.select(selection).append("svg")
			.attr("width",width)
			.attr("height",height);
			
var g_vis_1 = svg_vis_1.append("g")
			.attr("id","g_" + vis_id)
			.attr("width",width-margin.left-margin.right)
			.attr("height",height-margin.top-margin.bottom);

var distr_bars = g_vis_1.selectAll("rect")
			.data(trunc_citation_data)
			.enter()
			.append("rect")
			.attr("x",function(d) {
				return xScale(d.rank) + barWidth/2;
			})
			.attr("y",function(d) { 
				if(d.citedByCount > 0) {
					return yScale(d.citedByCount);
				} else if(d.citedByCount == 0) {
					return (height-margin.bottom) - 2;
				}		
			 })
			.attr("width",barWidth)
			.attr("height",function(d) { 
				if(d.citedByCount > 0) {
					return (height-margin.bottom) - yScale(d.citedByCount); 
				} else {
					return 2;
				}
			})
			.attr("fill",function(d) {
					if(d.fl == true) {
						return "orange";
					} else {
						return "#294052";
					} 
			})
			.on("mouseover", function() {
				d3.select(this).style("cursor","pointer");
			})
			.on("click",function(d) {
				if(d.eid !== "NULL") {
					window.open("http://www.scopus.com/record/display.url?eid=" + d.eid + "&origin=resultslist");
				}
			});

var xAxisLine = g_vis_1.append("g")
			.attr("class","xAxis")
			.attr("transform","translate(" + 0 + "," + (height-margin.bottom) + ")")
			.call(distr_xAxis);
			
var yAxisLine = g_vis_1.append("g")
			.attr("class","yAxis")
			.attr("transform","translate(" + margin.left + "," + 0 + ")")
			.call(yAxis);

var xAxisLabel = g_vis_1.append("text")
			.attr("class","axisLabel")
			.attr("x",margin.left + (width-margin.left-margin.right)/2)
			.attr("y",height-15)
			.attr("text-anchor","middle")
			.text("Paper Rank");

var yAxisLabel = g_vis_1.append("text")
			.attr("class","axisLabel")
			.attr("text-anchor","middle")
			.attr("transform","rotate(-90)")
			.attr("y",15)
			.attr("x",-(margin.top+(height-margin.top-margin.bottom)/2))
			.text("Citation Count");

</script>