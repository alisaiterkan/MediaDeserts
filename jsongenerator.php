<?
// Borno says hi

include_once("/home/mediadeserts/secure/connection.php");

if(isset($_POST['state'])) {
	$state = $_POST['state'];
} else {
	$state = "RI";
}

if(isset($_POST['outletmetric'])) {
	$outletmetric = $_POST['outletmetric'];
} else {
	$outletmetric = "RI";
}

if(isset($_POST['geoview'])) {
	$geoview = $_POST['geoview'];
} else {
	$geoview = "state";
}


  function setColor($percent, $invert = false)
{
    
    $R = min((2.0 * (1.0-$percent)), 1.0) * 255.0;
    $G = min((2.0 * $percent), 1.0) * 255.0;
    $B = 0.0;
    
    return (($invert) ? 
sprintf("%02X%02X%02X",$R,$G,$B) 
: sprintf("%02X%02X%02X",$R,$G,$B)); 
} //colorMeter
function get_string($string, $start, $end){
 $string = " ".$string;
 $pos = strpos($string,$start);
 if ($pos == 0) return "";
 $pos += strlen($start);
 $len = strpos($string,$end,$pos) - $pos;
 return substr($string,$pos,$len);
}
function processGeometry($geometry) {
		$json = array();
		$jsonOuter = array();
		$jsonInner = array();

if (strpos($geometry,'<MultiGeometry>') == true) {
		$search = array("<MultiGeometry>","</MultiGeometry>");
		return "multiple";
} else {
		
		$outer = get_string($geometry, "<outerBoundaryIs><LinearRing><coordinates>","</coordinates></LinearRing></outerBoundaryIs>");
		$outer = explode(" ", $outer);
		$json['outer'] = $outer;
		
if(strpos($geometry,'<innerBoundaryIs><LinearRing><coordinates>') == true) {
		$inner = get_string($geometry, "<innerBoundaryIs><LinearRing><coordinates>","</coordinates></LinearRing></innerBoundaryIs>");
		if($inner !== null || $inner !== "") {
			$inner = explode(" ", $inner);
		$json['inner'] = $inner;
		}
		
		}
		return $json;
}
}

header('Content-Type: application/json');

if($geoview == "zipcode") {

	$circulationAreaResults = mysqli_query($con,"SELECT circulationAreas.zipcode, circulationAreas.occupiedHomes, sundayCirculation, combinedSundayCirculation, geometry FROM circulationAreas INNER JOIN zipcodes ON circulationAreas.zipcode = zipcodes.zipcode INNER JOIN demographics ON circulationAreas.zipcode = demographics.zipcode  WHERE circulationAreas.state='$state' GROUP BY zipcode ORDER BY circulationAreas.zipcode ASC;");

$json = array();


while($area = mysqli_fetch_array($circulationAreaResults)) {
	  $areaJSON = array();
	  $areaJSON['zipcode'] = $area['zipcode'];
	  $areaJSON['geometry'] = processGeometry($area['geometry']);
	  $zipcode = $area['zipcode'];
	  $newspapers = array();
	  $newspaperResults = mysqli_query($con,"SELECT circulationAreas.newspaperID, newspapers.name, newspapers.headquarters, newspapers.state, newspapers.type FROM circulationAreas INNER JOIN newspapers ON circulationAreas.newspaperID = newspapers.id WHERE circulationAreas.zipcode = $zipcode GROUP BY circulationAreas.newspaperID");
			while($newspaper = mysqli_fetch_array($newspaperResults)) {
	  	  	  $newspaperJSON = array();
	  	  	  $newspaperID = intval($newspaper['newspaperID']);
			  $newspaperJSON["paperName"] = htmlspecialchars($newspaper['name']);
			  $newspaperJSON["paperID"] = intval($newspaper['newspaperID']);
			  $newspaperJSON["hq"] = htmlspecialchars($newspaper['headquarters']) . ", " . htmlspecialchars($newspaper['state']);
			  
			 $reportsResults = mysqli_query($con,"SELECT circulationAreas.id reportID, fromReport, reportDate, frequency, additionalDescription, occupiedHomes, combinedDaily, combinedAverage, mondayCirculation, tuesdayCirculation, wednesdayCirculation, thursdayCirculation, fridayCirculation, saturdayCirculation, sundayCirculation, combinedSundayCirculation FROM circulationAreas WHERE circulationAreas.zipcode = $zipcode AND newspaperID = $newspaperID");
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

			 
			 $newspapers[$newspaperID] = $newspaperJSON;
	  }
	  
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
	  
	  // demographics
	  $demographics = array();
	  
	  $demographics['']
	  
	  $areaJSON['demographics'] = $demographics;
	  $json[$area['zipcode']] = $areaJSON;
}
 echo json_encode($json);
} elseif($geoview == "state") {

	$states = mysqli_query($con,"SELECT * FROM states;");


}
 ?>
