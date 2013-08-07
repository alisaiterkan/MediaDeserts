<?
// Borno says hi

include_once("/home/mediadeserts/secure/connection.php");

if(isset($_GET['type'])) {
	$type = $_GET['type'];
} else {
	$type = "xml";
}
if(isset($_POST['state'])) {
	$state = $_POST['state'];
} else {
	$state = "NC";
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
		array_push($json, $outer); 

		$inner = get_string($geometry, "<innerBoundaryIs><LinearRing><coordinates>","</coordinates></LinearRing></innerBoundaryIs>");
		if($inner !== null || $inner !== "") {
			$inner = explode(" ", $inner);
			array_push($json, $inner); 
		}
		return $json;
}
}

if($type == "json") {
header('Content-Type: application/json');

	$circulationAreaResults = mysqli_query($con,"SELECT circulationAreas.zipcode, circulationAreas.occupiedHomes, sundayCirculation, combinedSundayCirculation, geometry FROM circulationAreas INNER JOIN zipcodes ON circulationAreas.zipcode = zipcodes.zipcode WHERE STATE='$state' GROUP BY zipcode ORDER BY circulationAreas.zipcode ASC;");

$json = array();


while($area = mysqli_fetch_array($circulationAreaResults)) {
	  $areaJSON = array();
	  $areaJSON['zipcode'] = $area['zipcode'];
	  $areaJSON['geometry'] = processGeometry($area['geometry']);
		$zipcode = $area['zipcode'];
						$newspaperResults = mysqli_query($con,"SELECT fromReport, reportDate, newspapers.name name, newspapers.id newspaperID, newspapers.headquarters headquarters, newspapers.state hqState, frequency, additionalDescription, occupiedHomes, combinedDaily, combinedAverage, mondayCirculation, tuesdayCirculation, wednesdayCirculation, thursdayCirculation, fridayCirculation, saturdayCirculation, sundayCirculation, combinedSundayCirculation FROM circulationAreas INNER JOIN newspapers ON circulationAreas.newspaperID = newspapers.id WHERE circulationAreas.zipcode = $zipcode");

			while($newspapers = mysqli_fetch_array($newspaperResults)) {
	  	  	$newspaperJSON = array();

			  $newspaperJSON["paperName"] = htmlspecialchars($newspapers['name']);
			  $newspaperJSON["paperID"] = intval($newspapers['newspaperID']);
			  $$newspaperJSON["hq"] = htmlspecialchars($newspapers['headquarters']) . ", " . htmlspecialchars($newspapers['hqState']);
			  array_push($areaJSON, $newspaperJSON);
	  }
			  array_push($json, $areaJSON);
}
 echo json_encode($json);

} else {

  		header('Content-Type: text/xml');
		echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
		echo "<response>\n";

// SQL SELECT COMMAND
$circulationAreaResults = mysqli_query($con,"SELECT circulationAreas.zipcode, circulationAreas.occupiedHomes, sundayCirculation, combinedSundayCirculation, geometry, demographics.name, demographics.population, demographics.medianAge, demographics.pctBachelors, demographics.pctUnemployed, demographics.income, demographics.households, demographics.householdSize, demographics.pctHousingOwned, demographics.pctHousingRented, demographics.pctHousingVacant, demographics.medianHomeValue, demographics.pctProjectedGrowth, demographics.pctHouseholdGrowth, demographics.pctIncomeGrowth FROM circulationAreas INNER JOIN zipcodes ON circulationAreas.zipcode = zipcodes.zipcode INNER JOIN demographics ON circulationAreas.zipcode = demographics.zipcode WHERE STATE='$state' GROUP BY zipcode ORDER BY circulationAreas.zipcode ASC;");

while($area = mysqli_fetch_array($circulationAreaResults)) {

//insert here
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
			
			echo "<zipname>$zipname</zipname>\n"
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

			
						$newspaperResults = mysqli_query($con,"SELECT fromReport, reportDate, newspapers.name name, newspapers.id newspaperID, newspapers.headquarters headquarters, newspapers.state hqState, frequency, additionalDescription, occupiedHomes, combinedDaily, combinedAverage, mondayCirculation, tuesdayCirculation, wednesdayCirculation, thursdayCirculation, fridayCirculation, saturdayCirculation, sundayCirculation, combinedSundayCirculation FROM circulationAreas INNER JOIN newspapers ON circulationAreas.newspaperID = newspapers.id WHERE circulationAreas.zipcode = $zipcode");
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

if(intval($newspapers['occupiedHomes']) != 0) {
	if(intval($newspapers['sundayCirculation']) != 0) {
		$sundayCirculationPercent[$i] = intval($newspapers['sundayCirculation']) / intval($newspapers['occupiedHomes']);
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
			echo "<stats>";
				echo "<pct>" . array_sum($sundayCirculationPercent) / count($sundayCirculationPercent) . "</pct>";
				echo "<color>" . setColor(((array_sum($sundayCirculationPercent) / count($sundayCirculationPercent))*2), false) . "</color>";
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
