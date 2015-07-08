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

Generates third metrics visualization: h- and h(fl)-index over time

*/

<script type="text/javascript">
var hIndex = <?php echo $data['hIndex']; ?>;
var vis_id = '<?php echo $vis_id; ?>';
var selection = '#' + vis_id;
var citation_data = <?php echo $data['citation_data']; ?>;

// Get max citation count before sorting by date

var yMax = citation_data[0]["citedByCount"];

citation_data.sort(function(a,b) {
	return new Date(a.pubDate) - new Date(b.pubDate);
});

// Define basic parameters
var	width = $(selection).width();
	height = $(selection).height();

// For each individual chart
var margin = {top: 15, left: 50, right: 30, bottom: 50};

var	barPadding = 30; //15

// Distribution of h-indices -- initialize values


function calculateIndex(data) {
	data.sort(function(a,b) {
		return b-a;
	});
	var index = 0;
	for(var i = 0; i < data.length; i++) {
		if(data[i] >= (i+1)) {
			index++;
		}
	}
	return index;
}


var xMin = citation_data[0]["pubDate"];
var xMax = citation_data[citation_data.length-1]["pubDate"];

// Define chart parameters


var hArray = [];
var hflArray = [];



var getCounts = {"h":[], "hfl":[]};
for(var i in citation_data) {

	getYear = citation_data[i]["pubDate"].substr(0,4);
	if(typeof hArray[getYear] === "undefined") {
		getCounts["h"] = getCounts["h"].concat(
						citation_data.map(function(d) { 
							if(d.pubDate.substr(0,4) === getYear) {
								return d.citedByCount; 
							}
						})
					);
		getCounts["hfl"] = getCounts["hfl"].concat(
						citation_data.map(function(d) { 
							if(d.pubDate.substr(0,4) === getYear && d.fl == true) {
								return d.citedByCount; 
							}
						})
					);

		var h = calculateIndex(getCounts["h"]);
		var hfl = calculateIndex(getCounts["hfl"]);
		hArray[getYear] = h;
		hflArray[getYear] = hfl;
	}
}

hArray = d3.entries(hArray);
hflArray = d3.entries(hflArray);

var x = [new Date(hArray[0].key), new Date(hArray[hArray.length-1].key)];

var xScale = d3.time.scale()
			.domain(x)
			.range([margin.left, width-margin.right]);
			
var yScale = d3.scale.linear()
			.domain([0, hIndex])
			.rangeRound([height-margin.bottom, margin.top]);

var index_line = d3.svg.line()
	.x(function(d) { return xScale(new Date(d.key)); })
    .y(function(d) { return yScale(d.value); });

x_domain_length = xScale.domain()[1].getFullYear() - xScale.domain()[0].getFullYear() + 1;

var ticks = x_domain_length > 25 ? x_domain_length/5 : x_domain_length;
var xAxis = d3.svg.axis()
		.orient("bottom")
		.tickFormat(d3.time.format('%Y'))
		.ticks(ticks)
// 		.ticks(xDomain.length/8)
// 		.tickSubdivide(2)
		.scale(xScale);

var yAxis = d3.svg.axis()
		.orient("left")
		.scale(yScale);

// var barWidth = xScale.rangeBand()/2;
var barWidth = 15;

var svg_vis_3 = d3.select(selection).append("svg")
			.attr("width",width)
			.attr("height",height);
			
var g_vis_3 = svg_vis_3.append("g")
			.attr("id","g_" + vis_id)
			.attr("width",width-margin.left-margin.right)
			.attr("height",height-margin.top-margin.bottom);

var hLine = g_vis_3.append("path")
			.datum(hArray)
			.attr("class", "line")
			.attr("d", index_line);

var hflLine = g_vis_3.append("path")
			.datum(hflArray)
			.attr("class", "line")
			.attr("d", index_line)
			.style("stroke","orange");

var xAxisLine = g_vis_3.append("g")
			.attr("class","xAxis")
			.attr("transform","translate(" + 0 + "," + (height-margin.bottom) + ")")
			.call(xAxis)
			.selectAll("text")
				.attr("dy",0)
				.attr("dx",20)
				.attr("transform","rotate(60)");
// 				.attr("text-anchor","middle");
			
var yAxisLine = g_vis_3.append("g")
			.attr("class","yAxis")
			.attr("transform","translate(" + margin.left + "," + 0 + ")")
			.call(yAxis);

var xAxisLabel = g_vis_3.append("text")
			.attr("class","axisLabel")
			.attr("x",margin.left + (width-margin.left-margin.right)/2)
			.attr("y",height-5)
			.attr("text-anchor","middle")
			.text("Year");

var yAxisLabel = g_vis_3.append("text")
			.attr("class","axisLabel")
			.attr("text-anchor","middle")
			.attr("transform","rotate(-90)")
			.attr("y",15)
			.attr("x",-(margin.top+(height-margin.top-margin.bottom)/2))
			.text("Cumulative h-index/h(fl)-index");

</script>