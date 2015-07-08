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

A visualization based on D3.js that helps explain how the h-index is calculated

-->

<style>

#hChart, #hflChart {
	font-family: Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif;
}

.xAxis {
	stroke:black;
	fill: none;
    shape-rendering: crispEdges;
}

.yAxis {
	stroke:black;
	fill: none;
    shape-rendering: crispEdges;
}

.xAxis text, .yAxis text {
	stroke: none;
	fill: black;
	font-size: 11px;
}

.axisLabel {
	stroke: none;
	fill: black;
	font-size: 13px;
	font-weight: bold;
}

.hLine {
	stroke:black;
	stroke-width: 2;
}

.dashLine {
	stroke:black;
	stroke-width: 2;
}

.specialMarker {
	font-family: sans-serif;
	font-weight: bold;
	font-size: 20px;
	fill: red;
	stroke:none;
}

#start_animation {
	cursor: pointer;
}

</style>

<script>

var selection = "<?php echo $selection; ?>";

// Define basic parameters

var windowPadding = {top:50, left:50, right:50, bottom:50},
	windowWidth = window.innerWidth - windowPadding.left - windowPadding.right,
	windowHeight = window.innerHeight - windowPadding.top - windowPadding.bottom;

// For each individual chart
var margin = {top:15, left:50, right:15, bottom:50};

var width = $("#" + selection).width(),
	height = 350,
	barWidth = 15,
	barPadding = 30; //15
	
var keyPress = 0;
var transitions = [];

var data = [];

getData = $.get("inc/resources/resources-content/data.txt",function(getData) {
	var lines = getData.split("\n");
	for(var i = 0; i < lines.length; i++) {
		data.push(Number(lines[i].trim()));
	}
	


	var min = Math.min.apply(null,data);
	var max = Math.max.apply(null,data);



	// Define transitions //
	// transitions = [transition_0, transition_1, transition_2, transition_3, transition_4, transition_5, transition_6, transition_7, transition_8];
	transitions = [transition_0, transition_1, transition_2, transition_3];

	function transition_0() {
		h_bars.transition()
			.duration(250)
			.ease("quad")
			.delay(function(d,i) {
				return i*100;
			})
			.attr("x", function(d,i) { return xScale(sortedIndexArray[i]+1) + (barWidth/2); })
			.each("end", function(d,i) {
				if(i == data.length-1) {
				
					h_xAxisLine.selectAll("text")
						.transition()
						.duration(1000)
						.style("fill","black");

					h_xAxisLabel.text("Published Papers -- Rank Order");
				}
			});
	}

	function transition_1() {
		hLine.transition()
			.duration(1000)
			.attr("x2",xScale(data.length)+barWidth)
			.attr("y2",yScale(data.length));
	}

	function transition_2() {		
		h_bars.transition()
			.duration(100)
			.delay(function(d,i) {
				var k = sortedIndexArray[i];
				return k*100;
			})
			.style("fill",function(d,i) {
				var k = sortedIndexArray[i];
				if((k+1) <= hIndex) {
					return "blue";
				} else {
					return "gray";
				}
			});
		
	}

	function transition_3() {

		var h_dashLine = hChart.append("line")
			.attr("class","dashLine")
			.style("stroke-dasharray",("3, 3"))
			.attr("x1",xScale(hIndex) + barWidth)
			.attr("y1", yScale(hIndex))
			.attr("x2",xScale(hIndex) + barWidth)
			.attr("y2",yScale(hIndex));
		
		h_dashLine.transition()
			.duration(1000)
			.attr("x2",margin.left)
			.attr("y2",yScale(hIndex))
			.each("end",function() {
				hChart.append("text")
						.attr("class","specialMarker")
						.attr("x",25)
						.attr("y",yScale(hIndex)+7)
						.attr("text-anchor","middle")
						.text(hIndex);
				start_button.text("Restart");

			});

	}



	// Define chart parameters


	var xLabels = d3.range(1,data.length+1);

	var xScale = d3.scale.ordinal()
				.domain(xLabels)
				.rangeRoundBands([margin.left, width-margin.right],0,0.25);
	// 			.range(data);			
	// 			.range([margin.left+barPadding,width-margin.right]);
			
	var yScale = d3.scale.linear()
				.domain([min, max])
				.range([height-margin.bottom, margin.top]);


	var xAxis = d3.svg.axis()
			.orient("bottom")
			.ticks(10)
	// 		.tickFormat(function(d,i) {
	// 			return xLabels[i];
	// 		})
			.scale(xScale);
			

	var yAxis = d3.svg.axis()
			.orient("left")
			.ticks(max/20)
			.scale(yScale);

	var barWidth = xScale.rangeBand()/2;

	function vis_reset() {
	
		if(typeof svg !== "undefined") {
			svg.remove();
		}
		svg = d3.select("#" + selection).append("svg")
					.attr("width",width)
					.attr("height",height);
			
		hChart = svg.append("g")
					.attr("id","hChart")
					.attr("width",width)
					.attr("height",height);
						
		h_bars = hChart.selectAll("rect")
					.data(data)
					.enter()
					.append("rect")
					.attr("class","dataBar")
					.attr("x",function(d,i) {
						return xScale(i+1) + barWidth/2;
					})
					.attr("y",function(d) { return yScale(d); })
					.attr("width",barWidth)
					.attr("height",function(d) { return (height-margin.bottom) - yScale(d); });

		h_xAxisLine = hChart.append("g")
					.attr("class","xAxis")
					.attr("transform","translate(" + 0 + "," + (height-margin.bottom) + ")")
					.call(xAxis);
			
		h_xAxisLine.selectAll("text")
			.style("fill","#fff");
		
		h_yAxisLine = hChart.append("g")
					.attr("class","yAxis")
					.attr("transform","translate(" + margin.left + "," + 0 + ")")
					.call(yAxis);

		h_xAxisLabel = hChart.append("text")
					.attr("class","axisLabel")
					.attr("x",margin.left + (width-margin.left-margin.right)/2)
					.attr("y",height-15)
					.attr("text-anchor","middle")
					.text("Published Papers -- By Time");

		h_yAxisLabel = hChart.append("text")
					.attr("class","axisLabel")
					.attr("text-anchor","middle")
					.attr("transform","rotate(-90)")
					.attr("y",15)
					.attr("x",-(margin.top+(height-margin.top-margin.bottom)/2))
					.text("Citation Count");

		start_button = hChart.append("text")
					.attr("id","start_animation")
					.attr("text-anchor","end")
					.attr("y",10)
					.attr("x",width-margin.right)
					.text("Start");
			
		hLine = hChart.append("line")
					.attr("class","hLine")
					.attr("x1",margin.left)
					.attr("y1",height-margin.bottom)
					.attr("x2",margin.left)
					.attr("y2",height-margin.bottom);

		start_button.on("click", function() {
			if(keyPress == 0) {
				start_button.text("Next");
			} 
			if (keyPress > 3) {
				vis_reset();
				keyPress = 0;
			} else if (keyPress <= 3) {
				svg.call(transitions[keyPress]);
				keyPress++;
			}
		});
			
				
	}			
				
	vis_reset();	


	// Now transition to rank-ordering papers

	var sortedData = data.slice(0);

	sortedData.sort(function(a,b) {
						return b-a;
					 });
					 
	
	var hIndex = 0;		
	var counter = 0;		 
	for(var j = 0; j < sortedData.length; j++) {
		counter++;
		if(sortedData[j] >= counter) {
			hIndex++;
		} else {
			break;
		}
	
	}				 
					 
					 
	var sortedIndexArray = [];

	for(var i = 0; i < data.length; i++) {
		var k = sortedData.indexOf(data[i]);
		sortedIndexArray.push(k);	
		sortedData[k] = null;
	}						 
						 

});







</script>