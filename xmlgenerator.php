<?
// Borno says hi

include_once("/home/mediadeserts/secure/connection.php");

  function setColor($percent, $invert = false)
{
    
    $R = min((2.0 * (1.0-$percent)), 1.0) * 255.0;
    $G = min((2.0 * $percent), 1.0) * 255.0;
    $B = 0.0;
    
    return (($invert) ? 
sprintf("%02X%02X%02X",$R,$G,$B) 
: sprintf("%02X%02X%02X",$R,$G,$B)); 
} //colorMeter

  		header('Content-Type: text/xml');
		echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
		echo "<response>\n";

// SQL SELECT COMMAND
$circulationAreaResults = mysqli_query($con,"SELECT circulationAreas.zipcode, circulationAreas.occupiedHomes, sundayCirculation, combinedSundayCirculation, geometry FROM circulationAreas INNER JOIN zipcodes ON circulationAreas.zipcode = zipcodes.zipcode WHERE STATE='NC' GROUP BY zipcode ORDER BY circulationAreas.zipcode ASC;");

while($area = mysqli_fetch_array($circulationAreaResults)) {

//insert here
		$zipcode = $area['zipcode'];
		$geometry = $area['geometry'];
		
		echo "<area>\n";
			echo "<zipcode>$zipcode</zipcode>\n";
			echo "<geometry>\n";
echo $geometry;
			echo "</geometry>\n";
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

?>
