<? 
include_once("/home/mediadeserts/secure/connection.php");
 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Media Deserts</title>
    <link href='http://fonts.googleapis.com/css?family=Bevan' rel='stylesheet' type='text/css'>
    <link href="styles/main.css" rel="stylesheet">
	<link href="scripts/ui-theme/jquery-ui-1.10.0.custom.css" rel="stylesheet">
	<link href="scripts/jquery-multipleselect.css" rel="stylesheet">
	<link href="scripts/shadowbox/shadowbox.css" rel="stylesheet">
  </head>
  <body>
  <div id="page-wrapper" class="body row scroll-y">
	  
	  <header class="cf">
		  	<div id="logo">
			  	<img src="logo.png" height="140" alt="Media Deserts: Communities that lack access to fresh local news and information" title="Media Deserts: Communities that lack access to fresh local news and information" style="max-width:100%;">
		  	</div>
			<form id="filters">
			  	<div class="col col8">
			  	<div class="checkbox-ui-group">
			  		<input type="checkbox" name="stateView" value="stateView"><label for="stateView">State View</label>
			  	</div>
			  	<div class="checkbox-ui-group">
			  		<input type="checkbox" name="publicationView" value="publicationView"><label for="publicationView">Publication View</label>
			  	</div>
			  	</div>
			  	<div class="col col8">
			  	<label for="states">States</label>
				  	<select name="states" id="states-selector" multiple>
					  	<?
$states = array(
	'AL'=>'Alabama',
	'AK'=>'Alaska',
	'AZ'=>'Arizona',
	'AR'=>'Arkansas',
	'CA'=>'California',
	'CO'=>'Colorado',
	'CT'=>'Connecticut',
	'DE'=>'Delaware',
	'DC'=>'District of Columbia',
	'FL'=>'Florida',
	'GA'=>'Georgia',
	'HI'=>'Hawaii',
	'ID'=>'Idaho',
	'IL'=>'Illinois',
	'IN'=>'Indiana',
	'IA'=>'Iowa',
	'KS'=>'Kansas',
	'KY'=>'Kentucky',
	'LA'=>'Louisiana',
	'ME'=>'Maine',
	'MD'=>'Maryland',
	'MA'=>'Massachusetts',
	'MI'=>'Michigan',
	'MN'=>'Minnesota',
	'MS'=>'Mississippi',
	'MO'=>'Missouri',
	'MT'=>'Montana',
	'NE'=>'Nebraska',
	'NV'=>'Nevada',
	'NH'=>'New Hampshire',
	'NJ'=>'New Jersey',
	'NM'=>'New Mexico',
	'NY'=>'New York',
	'NC'=>'North Carolina',
	'ND'=>'North Dakota',
	'OH'=>'Ohio',
	'OK'=>'Oklahoma',
	'OR'=>'Oregon',
	'PA'=>'Pennsylvania',
	'RI'=>'Rhode Island',
	'SC'=>'South Carolina',
	'SD'=>'South Dakota',
	'TN'=>'Tennessee',
	'TX'=>'Texas',
	'UT'=>'Utah',
	'VT'=>'Vermont',
	'VA'=>'Virginia',
	'WA'=>'Washington',
	'WV'=>'West Virginia',
	'WI'=>'Wisconsin',
	'WY'=>'Wyoming',
);
foreach ($states as $abbrv => &$fullname) {
	echo "<option value='" . $abbrv . "'>". $fullname ."</option>";	

}					  	?>
					</select>
				</div>
			  	<div class="col col8">
			  	<label for="publications">Publications</label>
				  	<select name="publications" id="publications-selector" multiple>
					  	<?
					  	$newspaperTypes = mysqli_query($con,"SELECT type, state FROM newspapers GROUP BY state;");
					  	while($row = mysqli_fetch_array($newspaperTypes))
						  {
							  echo "<optgroup label='". $row['state'] ."'>";
					  	$newspapers = mysqli_query($con,"SELECT * FROM newspapers WHERE state ='" . $row['state'] . "';");

					  	while($row = mysqli_fetch_array($newspapers))
						  {

						  			echo "<option value='" . $row['id'] . "' selected>". $row['name'] ."</option>";	
						  
						  		}
						  		echo "</optgroup>";
					  	}
					  	?>
					</select>
				</div>
			  	<div class="col col8">
			  	<label for="years">Years</label>
				  	<select name="years" id="years-selector" multiple>
					  	<?
					  	

					  	$newspapers = mysqli_query($con,"SELECT dataYear FROM circulationAreas GROUP BY dataYear;");

					  	while($row = mysqli_fetch_array($newspapers))
						  {
						  		
						  			echo "<option value='" . $row['dataYear'] . "' selected>". $row['dataYear'] ."</option>";	
						  
						  		}
					  	
					  	?>
					</select>
			  	</div>
			  	<div class="col col8">
			  	<label for="circulation">Circulation</label>
				  	<select name="circulation" id="circulation-selector" multiple>
						<option value="1">Daily Circulation</option>
						<option value="2" selected>Sunday Circulation</option>
						<option value="3">Combined Average</option>
					</select>
			  	</div>
			  </form>
	  </header>
	  
	  <section id="content" class="cf">
		  		<div id="loadingtext">Loading...</div>
		  		
		  	  <div id="map"></div>
		  	  <div id="sidebar" class="hidden">
				</div>
		  	  <div id="footerInfo">
		  	  <div id="scale" class="cf">
		  	  		  	  <div id="mover"></div><div id="scalefiller"></div>

		  	  <div id="scale_labels">
			  	  
			  	  <ul>
			  	  		<li>0%</li>
			  	  		<li>10%</li>
			  	  		<li>20%</li>
			  	  		<li>30%</li>
			  	  		<li>40%</li>
			  	  		<li>50%</li>
			  	  		<li>60%</li>
			  	  		<li>70%</li>
			  	  		<li>80%</li>
			  	  		<li>90%</li>
			  	  		<li>100%</li>
			  	  
			  	  </ul>
			  	  
		  	  </div>
		  	  
		  	  
		  	  </div>
		  	  
		  	  <div id="infobar" class="cf"><div id="infobarplaceholder">Click on a circulation area for more information.</div></div>
		  	  </div>
	  </section>
	  
	  
	  

  </div>
<div id="loading-modal" title="Loading">
  <p>Loading...</p>
</div>

    <script src="scripts/jquery.js"></script>
    <script src="scripts/jquery-ui.js"></script>
    <script src="scripts/jquery-multipleselect.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBqhj4sutTn567eZrFQs1hVJUMXsF7gWLI&sensor=true"></script>
    <script src="scripts/shadowbox/shadowbox.js"></script>
    <script src="http://www.google.com/jsapi"></script>
    <script src="scripts/script.js"></script>
  </body>
</html>
