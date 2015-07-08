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

Defines general site-wide PHP functions used in profiles and processes

-->

<?php 

// Defines generic site-wide functions
function connectDB() {
	global $dbhost, $dbuser, $dbpw, $dbname;
	$con = mysqli_connect($dbhost,$dbuser,$dbpw,$dbname) or die(mysqli_connect_error());
	return $con;
}

function closeDB($con) {
	mysqli_close($con);
}

function runQuery($con,$sql) {
	$response = mysqli_query($con,$sql);
	return $response;
}

// Truncates long author lists to a window that centers around the faculty being profiled;
// faculty name is bolded
function truncate($string,$length=250) {
	$string = trim($string); 	 
	if(strlen($string) > $length) {
		$authors_array = explode("; ",$string);
		$adjusted_length = 10;
		foreach($authors_array as $ind => $author) {
			if(stripos($author,"<b>") !== false) {
				$position = $ind;
				break;
			}
		}
		if($position < 5) {
			$offset = $position;
		} else {
			$offset = 5;
			if($position > 5) {
				$prepend = "&hellip;";
			} else {
				$prepend = "";
			}
		}
		if((($position+1) + $adjusted_length) < count($authors_array)) {
			$append = "&hellip;";
		} else {
			$append = "";
		}
		$adjusted_array = array_slice($authors_array,($position - $offset),$adjusted_length);
		$string = implode("; ",$adjusted_array);
		$string = wordwrap($string, $length);
		$string = explode("\n",$string);
		$string = $prepend . array_shift($string) . $append;
	}

	return $string;
}

// A function that determines calendar quarter start and end dates
function getQuarter($selectedQuarter) {

	$quarters = array("1" => array("start" => "01-01", "end" => "03-31"), "2" => array("start" => "04-01", "end" => "06-30"), "3" => array("start" => "07-01", "end" => "09-30"), "4" => array("start" => "10-01", "end" => "12-31"));
	if($selectedQuarter) {
		$quarterSplit = explode('.',$selectedQuarter);
		return array("quarterName" => $quarterSplit[1], "startDate" => date("Y-m-d", strtotime($quarters[$quarterSplit[1]]["start"] . " " . $quarterSplit[0])), "endDate" => date("Y-m-d",strtotime($quarters[$quarterSplit[1]]["end"] . " " . $quarterSplit[0])));

	} else {
	
		$month = (int) date("m",strtotime("now")); // Convert current month to integer for comparison
		if($month >= 4) {
			$year = date("Y");
			if($month >= 7) {
				if($month >= 10) {
					return array("quarterName" => "3", "startDate" => date("Y-m-d", strtotime($year . "-" . $quarters["3"]["start"])), "endDate" => date("Y-m-d",strtotime($year . "-" . $quarters["3"]["end"])));
				} else {
					return array("quarterName" => "2", "startDate" => date("Y-m-d", strtotime($year . "-" . $quarters["2"]["start"])), "endDate" => date("Y-m-d",strtotime($year . "-" . $quarters["2"]["end"])));
				}	
			} else {
				return array("quarterName" => "1", "startDate" => date("Y-m-d", strtotime($year . "-" . $quarters["1"]["start"])), "endDate" => date("Y-m-d",strtotime($year . "-" . $quarters["1"]["end"])));
			}
		} else {
			$year = date("Y") - 1;
			return array("quarterName" => "4", "startDate" => date("Y-m-d", strtotime($year . "-" . $quarters["4"]["start"])), "endDate" => date("Y-m-d",strtotime($year . "-" . $quarters["4"]["end"])));
		} 
	}
}

function printFile($outputFile, $data) {
	print $data;
	fwrite($outputFile, $data);
}

function printStatus($progress,$objective) {
	$statusLog = fopen("../logs/statusLog.txt","w");
	fwrite($statusLog,$progress . "\t" . $objective);
	fclose($statusLog);

}

function getLastId($con) {
	return mysqli_insert_id($con);
}


function escapeString($con,$data) {
	return mysqli_real_escape_string($con,$data);
}

// Calculates quartiles for descriptive department statistics
function quartile($array, $quartile) {
	$count = count($array);
	if($count % 2) {	
		// if count of items is odd,
		// retrieved value is distinct	
		return $array[floor($count * $quartile)];
	} else {
		// if count of items is even,
		// retrieved value is mean of middle values	
		return ($array[(($count * $quartile)-1)] + $array[($count * $quartile)])/2;
	}

}

?>