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

Generates fifth metrics visualization: correlation plot between h-index and h-citations

*/

<script type="text/javascript">

var vis_id = '<?php echo $vis_id; ?>';
var selection = '#' + vis_id;
var type = '<?php echo $data["hMatrix"]["type"]; ?>';
var id = '<?php echo $data["hMatrix"]["id"]; ?>';

// Move to front
d3.selection.prototype.moveToFront = function() {
  return this.each(function(){
    this.parentNode.appendChild(this);
  });
};

// Move to back

d3.selection.prototype.moveToBack = function() { 
    return this.each(function() { 
        var firstChild = this.parentNode.firstChild; 
        if (firstChild) { 
            this.parentNode.insertBefore(this, firstChild); 
        } 
    }); 
};


// Define basic parameters
var	width = $(selection).width();
	height = $(selection).height();

if(type === "custom") {
	var idSet = id.split(",");
}

// For each individual chart
var margin = {top: 15, left: 75, right: 30, bottom: 50};
	

getData = $.get("inc/visualizations/h_matrix/" + data_source, function(getData) {
	var data = [];
	var hMin = 0,
		hMax = 0,
		pubMin = 0,
		pubMax = 0,
		citationMin = 0,
		citationMax = 0;

	var lines = getData.split("\n");
	for(var i = 0; i < lines.length; i++) {		//lines.length
		lineData = lines[i].split("\t");
		var internetId = lineData[0];
		var this_hIndex = Number(lineData[1]);
		var hCitations = Number(lineData[2]);
		var pubCount = Number(lineData[3]);
		var firstName = lineData[4];
		var lastName = lineData[5];
		var dept = lineData[6];
		if(this_hIndex > hMax) {
			hMax = this_hIndex;
		}
		if(pubCount > pubMax) {
			pubMax = pubCount;
		}
		if(hCitations > citationMax) {
			citationMax = hCitations;
		}
		
		data.push({internetId: internetId, hIndex: this_hIndex, hCitations: hCitations, pubCount: pubCount, firstName: firstName, lastName: lastName, dept: dept});
	}
	
// 	// Define chart parameters

		
	var xData = d3.range(1,hMax);
	var lineData = [];
	for(var i = 0; i < xData.length; i++) {
		var y = (xData[i])*(xData[i]);
		lineData.push({x: xData[i], y: y});
	}

	var colorScale = d3.scale.linear()
				.domain([0,pubMax])
				.range(["#fff5f0","#67000d"]);
				
	var sizeScale = d3.scale.linear()
				.domain([0,pubMax])
				.range([2,20]);

	var xScale = d3.scale.linear()
				.domain([0,hMax])
				.range([margin.left, width-margin.right]);
			
	var yScale = d3.scale.linear()
				.domain([0, citationMax])
				.range([height-margin.bottom, margin.top]);

	var lineFunction = d3.svg.line()
		.x(function(d) { return xScale(d.x); })
		.y(function(d) { return yScale(d.y); })
		.interpolate("basis");


	var xAxis = d3.svg.axis()
			.orient("bottom")
			.scale(xScale);
			

	var yAxis = d3.svg.axis()
			.orient("left")
			.ticks(20)
			.scale(yScale);
			
			
	var hMatrix_chart = d3.select(selection).append("svg")
				.attr("id","hMatrix_chart")
				.attr("width",width)
				.attr("height",height);
						
	var hMatrix_dots = hMatrix_chart.selectAll("circle")
				.data(data)
				.enter()
				.append("circle")
				.attr("r",function(d) {
					return sizeScale(d.pubCount);
				})
				.attr("cx",function(d) {
					return xScale(d.hIndex);
				})
				.attr("cy",function(d) { 
					return yScale(d.hCitations); 
				})
				.attr("fill", "#000000")
				.attr("stroke","none")
				.on("mouseover", function(d) {
					hMatrix_dots.attr("opacity",0.05);
					d3.select(this)
						.moveToFront()
						.attr("opacity",0.05)
						.attr("fill","orange")
						.style("cursor","pointer");
					d3.selectAll("circle").filter(function(k) {
						if(typeof k !== "undefined") {
							return (Math.abs(k.hCitations - d.hCitations) <= d.hIndex || k.hIndex == d.hIndex);
						}
					})
					.attr("opacity",1);

					textLabels.style("visibility","hidden");
					var thisLabel = textLabels.filter(function(k) {
						return k.internetId === d.internetId;
					});
					
					bbox = thisLabel.node().getBBox();

					textLabels_background.attr("x",bbox.x - 2)
						.attr("y",bbox.y - 2)
						.attr("width",bbox.width + 4)
						.attr("height",bbox.height + 4)
						.moveToFront()
						.style("visibility","visible");
											
					thisLabel.moveToFront()
						.style("visibility","visible");
					
					

				})
				.on("mouseout", function() {
					textLabels.style("visibility","hidden");
					textLabels_background.style("visibility","hidden");
					hMatrix_dots.attr("fill","black");
					resetChart();

				})
				.on("click", function(d) {
					window.open("profile.php?type=faculty&id=" + d.internetId);
				});

	var textLabels = hMatrix_chart.selectAll("text")
				.data(data)
				.enter()
				.append("text")
                 .attr("x",function(d) { 
                 	if(d.hIndex > 70) {
                 		return xScale(d.hIndex) - sizeScale(d.pubCount) - 140; 
	
					} else {	
                 		return xScale(d.hIndex) + sizeScale(d.pubCount) + 15; 
					}
				})
                 .attr("y",function(d) { return yScale(d.hCitations) + 5; })
                 .text(function(d) {
                 	return d.firstName + " " + d.lastName;
                 })
                 .attr("font-family", "sans-serif")
                 .attr("font-size", "13px")
                 .attr("fill", "#F54B02")
                 .style("font-weight","bold")
                 .style("visibility","hidden")
                 .style("cursor","default");
 
 	var textLabels_background = hMatrix_chart.append("rect")
 				.attr("x",0)
 				.attr("y",0)
 				.attr("width",10)
 				.attr("height",10)
 				.attr("fill","rgba(255,255,255,0.8)")
 				.style("visibility","hidden")
 				.style("cursor","pointer");
 
 	var hMatrix_lineGraph = hMatrix_chart.append("path")
				.attr("d", lineFunction(lineData))
				.attr("stroke", "black")
				.attr("stroke-width", 1)
				.attr("fill", "none");
 
	var xAxisLine = hMatrix_chart.append("g")
				.attr("class","xAxis")
				.attr("transform","translate(" + 0 + "," + (height-margin.bottom) + ")")
				.call(xAxis);

	var yAxisLine = hMatrix_chart.append("g")
				.attr("class","yAxis")
				.attr("transform","translate(" + margin.left + "," + 0 + ")")
				.call(yAxis);

	var xAxisLabel = hMatrix_chart.append("text")
				.attr("class","axisLabel")
				.attr("x",margin.left + (width-margin.left-margin.right)/2)
				.attr("y",height-10)
				.attr("text-anchor","middle")
				.text("h-index");

	var yAxisLabel = hMatrix_chart.append("text")
				.attr("class","axisLabel")
				.attr("text-anchor","middle")
				.attr("transform","rotate(-90)")
				.attr("y",15)
				.attr("x",-(margin.top+(height-margin.top-margin.bottom)/2))
				.text("h-Citation count");
 
	function resetChart() {
		hMatrix_dots.attr("opacity",0.2);
		if(type === "faculty") {
			selectedEntity = hMatrix_dots.filter(function(d) {
				return d.internetId === id;
			});

			var hCitations = selectedEntity.data()[0]["hCitations"];
			var hIndex = selectedEntity.data()[0]["hIndex"];
		
			hMatrix_dots.filter(function(k) {
				if(typeof k !== "undefined") {
					return (Math.abs(k.hCitations - hCitations) <= hIndex || k.hIndex == hIndex);
				}		
			})
			.moveToFront()
			.attr("opacity",1);

			selectedEntity.moveToFront();
			selectedEntity.attr("fill","orange").attr("opacity",1);

			var thisFacultyLabel = textLabels.filter(function(k) {
				return k.internetId === id;
			})

			bbox = thisFacultyLabel.node().getBBox();

			textLabels_background.attr("x",bbox.x - 2)
				.attr("y",bbox.y - 2)
				.attr("width",bbox.width + 4)
				.attr("height",bbox.height + 4)
				.moveToFront()
				.style("visibility","visible");

		
			thisFacultyLabel.style("visibility","visible")
				.moveToFront();

		} else if(type === "dept") {
			var selectedEntity = hMatrix_dots.filter(function(d) {
				return d.dept === id;
			});
			selectedEntity.moveToFront();
			selectedEntity.attr("fill","orange").attr("opacity",1);
	
	
		} else if(type === "custom") {
			var selectedEntity = hMatrix_dots.filter(function(d) {
				return idSet.indexOf(d.internetId) != -1;
			});
			selectedEntity.moveToFront();
			selectedEntity.attr("fill","orange").attr("opacity",1);
	
		}
	 }
 
 	resetChart();
 


});
</script>