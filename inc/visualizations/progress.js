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

Generates progress bar chart for departmental publication totals report

*/

var	width = $(selection).width(),
	height = 36;


var barHeight = 15;
// For each individual chart
var margin = {top:2, left:70, right: 2, bottom: 2};

var positions = {total: margin.top, fl: (margin.top+margin.top+barHeight)};

var	barPadding = 30; //15

var xScale = d3.scale.linear()
			.domain([0,100])
			.range([1, width-margin.left-margin.right]);
			
d3.select(selection).append("svg")
			.attr("id","progress_" + viz_id)
			.attr("width",width)
			.attr("height",height);
			
d3.select("#progress_" + viz_id).append("rect")
			.attr("class","total_progress")
			.attr("x",margin.left)
			.attr("y",positions.total)
			.attr("width",xScale(total_progress))
			.attr("height",barHeight)
			.attr("fill","#003366");

d3.select("#progress_" + viz_id).append("rect")
			.attr("class","fl_progress")
			.attr("x",margin.left)
			.attr("y",positions.fl)
			.attr("width",xScale(fl_progress))
			.attr("height",barHeight)
			.attr("fill","#FFCC66");

d3.select("#progress_" + viz_id).append("text")
			.attr("class","progress_text")
			.attr("x",0)
			.attr("y",positions.total+(barHeight/2))
			.text("Total");

d3.select("#progress_" + viz_id).append("text")
			.attr("class","progress_text")
			.attr("x",0)
			.attr("y",positions.fl+(barHeight/2))
			.text("F/L");
			
d3.select("#progress_" + viz_id).append("text")
			.attr("class","progress_text")
			.attr("x",35)
			.attr("y",positions.total+(barHeight/2))
			.text(total_progress + "%");

d3.select("#progress_" + viz_id).append("text")
			.attr("class","progress_text")
			.attr("x",35)
			.attr("y",positions.fl+(barHeight/2))
			.text(fl_progress + "%");
