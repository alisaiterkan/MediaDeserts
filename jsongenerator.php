<!--
jsongenerator.php
MediaDeserts
Last Modified Monday, August 12th 2013
 -Comments
-->

<?
// Borno says hi

//Secure connection to the db that users can't abuse
include_once("/home/mediadeserts/secure/connection.php");

//If user has selected a state, use that one
if(isset($_POST['state'])) {
	$state = $_POST['state'];
} else {
	//If not, use Rhode Island
	$state = "RI";
}

//If user has selected a metric, use it
if(isset($_POST['outletmetric'])) {
	$outletmetric = $_POST['outletmetric'];
} else {
	$outletmetric = "RI";
}

//If user has selected a geographical view, use it
if(isset($_POST['geoview'])) {
	$geoview = $_POST['geoview'];
} else {
	//Otherwise, default to state view
	$geoview = "state";
}

//Set the color of a polygon, don't invert it
  function setColor($percent, $invert = false)
{
    //Amount of red based on not low the percent is
    $R = min((2.0 * (1.0-$percent)), 1.0) * 255.0;
    //Green is default (100%) color
    $G = min((2.0 * $percent), 1.0) * 255.0;
    //No blue here
    $B = 0.0;
    //Return the hexadecimal version of this RGB color
    return (($invert) ? 
sprintf("%02X%02X%02X",$R,$G,$B) 
: sprintf("%02X%02X%02X",$R,$G,$B)); 
} //colorMeter

/*Purpose: Get a string of a variable length
 *Parameters: 
 *	$string, what we're cutting a string out of
 *	$start, the beginning of our string
 *	$end, the end of our string
 *Return:
 *	substring, the letters between $start and $end
 */
function get_string($string, $start, $end){
 $string = " ".$string;
 $pos = strpos($string,$start);
//If there's nothing between the two phrases, return an empty string
 if ($pos == 0) return "";
//start from the end of the 'start' phrase
 $pos += strlen($start);
//string length is num chars until the 'end' phrase
 $len = strpos($string,$end,$pos) - $pos;
//return the string between starting position and 'end' phrase
 return substr($string,$pos,$len);
}

/*Purpose: To process the coordinates in the geometry
 *Parameters: $geometry, the variable that holds geometry information for this object
 *Return: the JSON version of the geometry
 */
function processGeometry($geometry) {
		$json = array();
		$jsonOuter = array();
		$jsonInner = array();

//If the object has multiple geometries
if (strpos($geometry,'<MultiGeometry>') == true) {
		//Make sure there's something inside those tags
		$search = array("<MultiGeometry>","</MultiGeometry>");
		return "multiple";
} else {
		//Get the coordinates between the LinearRing tags
		$outer = get_string($geometry, "<outerBoundaryIs><LinearRing><coordinates>","</coordinates></LinearRing></outerBoundaryIs>");
		//Separate these into coordinate pairs
		$outer = explode(" ", $outer);
		//Put them into the 'outer' JSON tag
		$json['outer'] = $outer;
		
//If there is also an inner boundary
if(strpos($geometry,'<innerBoundaryIs><LinearRing><coordinates>') == true) {
		//Get the string between the tags
		$inner = get_string($geometry, "<innerBoundaryIs><LinearRing><coordinates>","</coordinates></LinearRing></innerBoundaryIs>");
		//Assuming they're not empty
		if($inner !== null || $inner !== "") {
			//Separate them into coordinate pairs
			$inner = explode(" ", $inner);
			//Add them to the 'inner' JSON tags
			$json['inner'] = $inner;
		}//otherwise, don't do anything
		
		}
		return $json;
}
}
//Give the HTML page the JSON information
header('Content-Type: application/json');


//If the selected geographical view is zip codesr
if($geoview == "zipcode") {
	//Get the zip code information in the selected state, in ascending zip code order
	$circulationAreaResults = mysqli_query($con,"SELECT circulationAreas.zipcode, circulationAreas.occupiedHomes, sundayCirculation, combinedSundayCirculation, geometry FROM circulationAreas INNER JOIN zipcodes ON circulationAreas.zipcode = zipcodes.zipcode INNER JOIN demographics ON circulationAreas.zipcode = demographics.zipcode  WHERE circulationAreas.state='$state' GROUP BY zipcode ORDER BY circulationAreas.zipcode ASC;");

$json = array();

For each area object
while($area = mysqli_fetch_array($circulationAreaResults)) {
	  $areaJSON = array();
	//Make list of zip codes
	  $areaJSON['zipcode'] = $area['zipcode'];
	//List of geometries
	  $areaJSON['geometry'] = processGeometry($area['geometry']);
	  $zipcode = $area['zipcode'];
	  $newspapers = array();
	//Get the newspapers in the current zip code
	  $newspaperResults = mysqli_query($con,"SELECT circulationAreas.newspaperID, newspapers.name, newspapers.headquarters, newspapers.state, newspapers.type FROM circulationAreas INNER JOIN newspapers ON circulationAreas.newspaperID = newspapers.id WHERE circulationAreas.zipcode = $zipcode GROUP BY circulationAreas.newspaperID");
			//Get the paper information for each paper
			while($newspaper = mysqli_fetch_array($newspaperResults)) {
	  	  	  $newspaperJSON = array();
	  	  	  $newspaperID = intval($newspaper['newspaperID']);
			  $newspaperJSON["paperName"] = htmlspecialchars($newspaper['name']);
			  $newspaperJSON["paperID"] = intval($newspaper['newspaperID']);
			  $newspaperJSON["hq"] = htmlspecialchars($newspaper['headquarters']) . ", " . htmlspecialchars($newspaper['state']);
			  //Get the reports for each newspaper
			 $reportsResults = mysqli_query($con,"SELECT circulationAreas.id reportID, fromReport, reportDate, frequency, additionalDescription, occupiedHomes, combinedDaily, combinedAverage, mondayCirculation, tuesdayCirculation, wednesdayCirculation, thursdayCirculation, fridayCirculation, saturdayCirculation, sundayCirculation, combinedSundayCirculation FROM circulationAreas WHERE circulationAreas.zipcode = $zipcode AND newspaperID = $newspaperID");
			//Put them into the JSON
			while($report = mysqli_fetch_array($reportsResults)) {
				$newspaperJSON['reports'][$report['reportID']]['fromReport'] = $report['fromReport'];
				$newspaperJSON['reports'][$report['reportID']]['reportDate'] = $report['reportDate'];
				$newspaperJSON['reports'][$report['reportID']]['frequency'] = $report['frequency'];
				$newspaperJSON['reports'][$report['reportID']]['additionalDescription'] = $report['additionalDescription'];
				$newspaperJSON['reports'][$report['reportID']]['occupiedHomes'] = $report['occupiedHomes'];
				$newspaperJSON['reports'][$report['reportID']]['combinedDaily'] = $report['combinedAverage'];
				$newspaperJSON['reports'][$report['reportID']]['mondayCirculation'] = $report['mondayCirculation'];
				$newspaperJSON['reports'][$report['reportID']]['tuesdayCirculation'] = $report['tuesdayCirculation'];
				$newspaperJSON['reports'][$report['reportID']]['wednesdayCirculation'] = $report['wednesdayCirculation'];
				$newspaperJSON['reports'][$report['reportID']]['thursdayCirculation'] = $report['thursdayCirculation'];
				$newspaperJSON['reports'][$report['reportID']]['fridayCirculation'] = $report['fridayCirculation'];
				$newspaperJSON['reports'][$report['reportID']]['saturdayCirculation'] = $report['saturdayCirculation'];
				$newspaperJSON['reports'][$report['reportID']]['sundayCirculation'] = $report['sundayCirculation'];
				$newspaperJSON['reports'][$report['reportID']]['combinedSundayCirculation'] = $report['combinedSundayCirculation'];
			}

			 //Put each newspaper's info and reports into the newspaper tag
			 $newspapers[$newspaperID] = $newspaperJSON;
	  }
	  //Put the newspapers into the JSON
	  $areaJSON['newspapers'] = $newspapers;
	  
	  // stats
	  $stats = array();

	  $stats['numberOFNewspapers'] = "color value";
	  $stats['closetNewspaper'] = "color value";
	  $stats['sundayCirculationPCT'] = "color value";
	  $stats['dailyCirculationPCT'] = "color value";
	  $stats['combinedDailyPCT'] = "color value";
	  $stats['polygoncolor'] = "color value";
	  $stats['color'] = "color value";
	  $stats['color'] = "color value";
	  $stats['color'] = "color value";
	  $stats['color'] = "color value";
	  $stats['color'] = "color value";
	  
	  $areaJSON['stats'] = $stats;
	  
	  // demographics 	IMPLEMENT
	  $demographics = array();
	  
	  $demographics['']
	  
	  $areaJSON['demographics'] = $demographics;
	  $json[$area['zipcode']] = $areaJSON;
}
//Put the JSON info into the html
 echo json_encode($json);
} elseif($geoview == "state") {

	$states = mysqli_query($con,"SELECT * FROM states;");


}
 ?>
