<!--
xmlgenerator.php
MediaDeserts
Last Updated Monday, August 12th 2013
 -Comments
-->
<?
// Borno says hi
// Maria says hi!


//Set up connection to database so that users can't abuse!
include_once("/home/mediadeserts/secure/connection.php");

//Maria - What?
if(isset($_GET['type'])) {
	$type = $_GET['type'];
} else {
	$type = "xml";
}
//If user has selected a state, use it. 
if(isset($_POST['state'])) {
	$state = $_POST['state'];
} else {
	//Otherwise, use North Carolina
	$state = "NC";
}
  //Set color based on percent, don't invert it
  function setColor($percent, $invert = false)
{
    //Amount under 100% determines red tint
    $R = min((2.0 * (1.0-$percent)), 1.0) * 255.0;
    //Green by default, 100%
    $G = min((2.0 * $percent), 1.0) * 255.0;
    //No blue here
    $B = 0.0;
    //Return the hexadecimal conversion of the RGB
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
		//IMPLEMENT
		//Return the stuff between the multiple geometry tags so that the caller knows to expect more than one
		$search = array("<MultiGeometry>","</MultiGeometry>");
		return "multiple";
} else {
		//Get the coordinates between the outerBoundary tags inside LinearRing
		$outer = get_string($geometry, "<outerBoundaryIs><LinearRing><coordinates>","</coordinates></LinearRing></outerBoundaryIs>");
		//Split the string by spaces to create a list of the coordinate pairs
		$outer = explode(" ", $outer);
		//This gets added to the JSON information
		array_push($json, $outer); 

		//Get the coordinates between the innerBoundary tags inside LinearRing
		$inner = get_string($geometry, "<innerBoundaryIs><LinearRing><coordinates>","</coordinates></LinearRing></innerBoundaryIs>");
		//If it isn't empty,
		if($inner !== null || $inner !== "") {
			//Split the coordinate pairs into a list
			$inner = explode(" ", $inner);
			//Add the coordinates to the JSON information
			array_push($json, $inner); 
		}
		//Return the JSON information
		return $json;
}
}

//Set the header to expect JSON information
if($type == "json") {
header('Content-Type: application/json');
	//Get the circulation info (zipcode, occupied homes, sundayCirculation, combinedCirculation, and geometry) for each zipcode in a state, ordered by ascending zip numbers
	$circulationAreaResults = mysqli_query($con,"SELECT circulationAreas.zipcode, circulationAreas.occupiedHomes, sundayCirculation, combinedSundayCirculation, geometry FROM circulationAreas INNER JOIN zipcodes ON circulationAreas.zipcode = zipcodes.zipcode WHERE STATE='$state' GROUP BY zipcode ORDER BY circulationAreas.zipcode ASC;");

//Make JSON array
$json = array();

//For each area object
while($area = mysqli_fetch_array($circulationAreaResults)) {
	  $areaJSON = array();
	//Zip code array within areaJSON information
	  $areaJSON['zipcode'] = $area['zipcode'];
	//Geomtry array within areaJSON information
	  $areaJSON['geometry'] = processGeometry($area['geometry']);
		//For each zip code in the area['zipcode'] array
		$zipcode = $area['zipcode'];
						//Get newspaper information for each newspaper in the zipcode
						$newspaperResults = mysqli_query($con,"SELECT fromReport, reportDate, newspapers.name name, newspapers.id newspaperID, newspapers.headquarters headquarters, newspapers.state hqState, frequency, additionalDescription, occupiedHomes, combinedDaily, combinedAverage, mondayCirculation, tuesdayCirculation, wednesdayCirculation, thursdayCirculation, fridayCirculation, saturdayCirculation, sundayCirculation, combinedSundayCirculation FROM circulationAreas INNER JOIN newspapers ON circulationAreas.newspaperID = newspapers.id WHERE circulationAreas.zipcode = $zipcode");
			//For eaxh newspaper
			while($newspapers = mysqli_fetch_array($newspaperResults)) {
	  	  	$newspaperJSON = array();
			//Get paper name
			  $newspaperJSON["paperName"] = htmlspecialchars($newspapers['name']);
			//Get the row number as an ID
			  $newspaperJSON["paperID"] = intval($newspapers['newspaperID']);
			//Get the headquarters of the newspaper
			  $$newspaperJSON["hq"] = htmlspecialchars($newspapers['headquarters']) . ", " . htmlspecialchars($newspapers['hqState']);
			//put newspaper dara into the areaJSON data
			  array_push($areaJSON, $newspaperJSON);
	  }
			//put the areaJSON data into the JSON info for the page
			  array_push($json, $areaJSON);
}
//Put JSON info into the html page
 echo json_encode($json);

} else {
		//If not working with JSON, xml instead
  		header('Content-Type: text/xml');
		echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
		echo "<response>\n";

// SQL SELECT COMMAND
//Newspaper information for each zip code in a state, in ascending zip code order
$circulationAreaResults = mysqli_query($con,"SELECT circulationAreas.zipcode, circulationAreas.occupiedHomes, sundayCirculation, combinedSundayCirculation, zipcodes.geometry, demographics.name, demographics.population, demographics.medianAge, demographics.pctBachelors, demographics.pctUnemployed, demographics.income, demographics.households, demographics.householdSize, demographics.pctHousingOwned, demographics.pctHousingRented, demographics.pctHousingVacant, demographics.medianHomeValue, demographics.pctProjectedGrowth, demographics.pctHouseholdGrowth, demographics.pctIncomeGrowth FROM circulationAreas INNER JOIN zipcodes ON circulationAreas.zipcode = zipcodes.zipcode INNER JOIN demographics ON circulationAreas.zipcode = demographics.zipcode WHERE STATE='$state' GROUP BY zipcode ORDER BY circulationAreas.zipcode ASC;");

//For each circulation area:
while($area = mysqli_fetch_array($circulationAreaResults)) {

//insert here
		//Each variable gets an array in the area information
		$zipcode = $area['zipcode'];
		$zipname = $area['name'];
		$population = $area['population'];
		$medianAge = $area['medianAge'];
		$pctBachelors = $area['pctBachelors'];
		$pctUnemployed = $area['pctUnemployed'];
		$income = $area['income'];
		$households = $area['households'];
		$householdSize = $area['householdSize'];
		$pctHousingOwned = $area['pctHousingOwned'];
		$pctHousingRented = $area['pctHousingRented'];
		$pctHousingVacant = $area['pctHousingVacant'];
		$medianHomeValue = $area['medianHomeValue'];
		$pctProjectedGrowth = $area['pctProjectedGrowth'];
		$pctHouseholdGrowth = $area['pctHouseholdGrowth'];
		$pctIncomeGrowth = $area['pctIncomeGrowth'];
		$geometry = $area['geometry'];
		
		echo "<area>\n";
			echo "<zipcode>$zipcode</zipcode>\n";
			echo "<geometry>\n";
			echo $geometry;
			echo "</geometry>\n";
			echo "<zipname>$zipname</zipname>\n";
			echo "<population>$population</population>\n";
			echo "<medianAge>$medianAge</medianAge>\n";
			echo "<pctBachelors>$pctBachelors</pctBachelors>\n";
			echo "<pctUnemployed>$pctUnemployed</pctUnemployed>\n";			
			echo "<income>$income</income>\n";
			echo "<households>$households</households>\n";
			echo "<householdSize>$householdSize</householdSize>\n";
			echo "<pctHousingOwned>$pctHousingOwned</pctHousingOwned>\n";
			echo "<pctHousingRented>$pctHousingRented</pctHousingRented>\n";
			echo "<pctHousingVacant>$pctHousingVacant</pctHousingVacant>\n";
			echo "<medianHomeValue>$medianHomeValue</medianHomeValue>\n";
			echo "<pctProjectedGrowth>$pctProjectedGrowth</pctProjectedGrowth>\n";
			echo "<pctHouseholdGrowth>$pctHouseholdGrowth</pctHouseholdGrowth>\n";
			echo "<pctIncomeGrowth>$pctIncomeGrowth</pctIncomeGrowth>\n";

			
						//Get each newspaper's information by zip code
						$newspaperResults = mysqli_query($con,"SELECT fromReport, reportDate, newspapers.name name, newspapers.id newspaperID, newspapers.headquarters headquarters, newspapers.state hqState, frequency, additionalDescription, occupiedHomes, combinedDaily, combinedAverage, mondayCirculation, tuesdayCirculation, wednesdayCirculation, thursdayCirculation, fridayCirculation, saturdayCirculation, sundayCirculation, combinedSundayCirculation FROM circulationAreas INNER JOIN newspapers ON circulationAreas.newspaperID = newspapers.id WHERE circulationAreas.zipcode = $zipcode");
								//Add reports to the XML
								echo "<reports>";
								$i = 0;
						while($newspapers = mysqli_fetch_array($newspaperResults)) {
														echo "<report from='". htmlspecialchars($newspapers['fromReport']) ."' date='". htmlspecialchars($newspapers['reportDate']) ."'>";

echo "<name>". htmlspecialchars($newspapers['name']) ."</name>";					
echo "<paperID>". htmlspecialchars($newspapers['newspaperID']) ."</paperID>";					
echo "<hq>". htmlspecialchars($newspapers['headquarters']) . ", " . htmlspecialchars($newspapers['hqState']) ."</hq>";					
echo "<frequency>". htmlspecialchars($newspapers['frequency']) ."</frequency>";					
echo "<additionalDescription>". htmlspecialchars($newspapers['additionalDescription']) ."</additionalDescription>";					
echo "<occupiedHomes>". intval($newspapers['occupiedHomes']) ."</occupiedHomes>";
echo "<combinedDaily>". intval($newspapers['combinedDaily']) ."</combinedDaily>";
echo "<combinedAverage>". intval($newspapers['combinedAverage']) ."</combinedAverage>";					
echo "<mondayCirculation>". intval($newspapers['mondayCirculation']) ."</mondayCirculation>";					
echo "<tuesdayCirculation>". intval($newspapers['tuesdayCirculation']) ."</tuesdayCirculation>";					
echo "<wednesdayCirculation>". intval($newspapers['wednesdayCirculation']) ."</wednesdayCirculation>";					
echo "<thursdayCirculation>". intval($newspapers['thursdayCirculation']) ."</thursdayCirculation>";					
echo "<fridayCirculation>". intval($newspapers['fridayCirculation']) ."</fridayCirculation>";					
echo "<saturdayCirculation>". intval($newspapers['saturdayCirculation']) ."</saturdayCirculation>";					
echo "<sundayCirculation>". intval($newspapers['sundayCirculation']) ."</sundayCirculation>";					
echo "<combinedSundayCirculation>". intval($newspapers['combinedSundayCirculation']) ."</combinedSundayCirculation>";

//Extrapolate sundayCirculationPercent
if(intval($newspapers['occupiedHomes']) != 0) {
	if(intval($newspapers['sundayCirculation']) != 0) {
		$sundayCirculationPercent[$i] = intval($newspapers['sundayCirculation']) / intval($newspapers['occupiedHomes']);
	//Or extrapolate it from combinedSundayCirculation
	} elseif(intval($newspapers['combinedSundayCirculation']) != 0) {
		$sundayCirculationPercent[$i] = intval($newspapers['combinedSundayCirculation']) / intval($newspapers['occupiedHomes']);
	} 
	
	if(intval($newspapers['sundayCirculation']) != 0) {
		
	}
} else {
	$missingHomes[$zipcode] = 1;
}
								echo "</report>";
	$i++;
						}

			
			echo "</reports>";
			//Get the stats for a zip
			echo "<stats>";
				echo "<pct>" . array_sum($sundayCirculationPercent) / count($sundayCirculationPercent) . "</pct>";
				//Set the color based on sundayCirculationPercent
				echo "<color>" . setColor(((array_sum($sundayCirculationPercent) / count($sundayCirculationPercent))*2), false) . "</color>";
				//Missing information leads to the striped pattern
				if($missingHomes[$zipcode] == 1) {
					echo "<striped>yes</striped>";
				} else {
					echo "<striped>no</striped>";
				}
			echo "</stats>";

		echo "  </area>\n";
		
	}
		echo "</response>\n";
		exit;
}
?>
