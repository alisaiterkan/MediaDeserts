<!-- 
index.php
MediaDeserts Project
Last Updated Monday, August 12th 2013
 -Commented
 -Commented out the county filter
-->


<!--Secure the connection to the database so others can't use it -->
<? 
include_once("/home/mediadeserts/secure/connection.php");
 ?>
<!--Start html-->
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Media Deserts</title>
    <!--Pretty font-->
    <link href='http://fonts.googleapis.com/css?family=Bevan' rel='stylesheet' type='text/css'>
    <!--Page Style-->
    <link href="styles/main.css" rel="stylesheet">
    <!--JQuery style-->
    <link href="scripts/ui-theme/jquery-ui-1.10.0.custom.css" rel="stylesheet">
    <!--Multiple Select Module style-->
    <link href="scripts/jquery-multipleselect.css" rel="stylesheet">
    <!--Shadowbox style-->
    <link href="scripts/shadowbox/shadowbox.css" rel="stylesheet">
  </head>
  <body>
  <div id="page-wrapper" class="body row scroll-y">
	  <header class="cf">
		  	<div id="logo">
			  	<img src="logo.png" height="140" alt="Media Deserts: Communities that lack access to fresh local news and information" title="Media Deserts: Communities that lack access to fresh local news and information" style="max-width:100%;">
		  	</div>
		  	<div id="forms">
		  		<!--MAP OPTIONS-->
		  		<div id="forms-left">
			  		<form id="forms-options">
				  		<div class="map-row">
							<!--User can create a new map here --IMPLEMENT-->
							<input type="button" value="New Map (Clear Filters)" class="rounded-left">
							<!--User can save a map here --IMPLEMENT -->
							<input type="button" value="Save Map (For Sharing)" class="rounded-right">
						</div>
						<!--GEOGRAPHIC OPTION BUTTONS-->
				  		<div class="geographic-row">
							<!--MAP BY STATE-->
							<input type="button" value="State View (Fastest)" class="rounded-left">
							<!--COUNTY VIEW, FOR LATER
							<input type="button" value="County View (Medium)" class="rounded-left rounded-right">-->
							<!--MAP BY ZIP CODE-->
							<input type="button" value="Zip Code View (Slowest)" class="rounded-right">
						</div>
						<!--METRIC OPTIONS FOR MAP-->
				  		<div class="metric-row color-metrics">
							<!--METRICS FROM AAM-->
				  			<div class="select-container">
					  			<option value="None">None</option>
				  			<select name="outlet-metrics" id="outlet-metrics" multiple>
							<!--EXTRAPOLATED METRICS-->
				  			<optgroup label="By Extrapolated Data">
					  			<option value="distance">Distance to Closest Newspaper</option>
					  			<option value="numberOfOutlets">Number Of Outlets</option>
				  			</optgroup>
							<!--DAILY METRICS-->
				  			<optgroup label="By Day">
					  			<option value="sundayCirculation" selected>Sunday Circulation</option>
					  			<option value="combinedDaily">Average Daily Circulation</option>
					  			<option value="mondayCirculation">Monday Circulation</option>
					  			<option value="tuesdayCirculation">Tuesday Circulation</option>
					  			<option value="wednesdayCirculation">Wednesday Circulation</option>
					  			<option value="thursdayCirculation">Thursday Circulation</option>
					  			<option value="fridayCirculation">Friday Circulation</option>
					  			<option value="saturdayCirculation">Saturday Circulation</option>
				  			</optgroup>
				  			</select>
				  			</div>
							<!--METRICS FROM CENSUS, DEMOGRAPHIC-->
				  			<div class="select-container">
				  			<select name="demographics-metrics" id="demographics-metrics" multiple>
							<!--POPULATION METRICS-->
				  			<optgroup label="Population">
					  			<option value="Population">Total Population</option>
								<option value="pctProjectedGrowth">Projected Population Change</option>
								<option value="households">Total Households</option>
								<option value="pctHouseholdGrowth">Projected Households Change</option>
								<option value="householdSize">Household Size</option>
								<option value="medianAge">Median Age</option>
				  			</optgroup>
							<!--SOCIOECONOMIC METRICS-->
				  			<optgroup label="Economics & Education">
								<option value="income">Average Income</option>
								<option value="pctIncomeGrowth">Projected Average Income Growth</option>
								<option value="pctBachelors">% with Bachelors Degree</option>
								<option value="pctUnemployed">Unemployment Rate</option>
								<option value="medianHomeValue">Average Home Value</option>
								<option value="pctHousingOwned">% of Housing Owned</option>
								<option value="pctHousingRented">% of Housing Rented</option>
								<option value="pctHousingVacant">% of Housing Vacant</option>
				  			</optgroup>
				  			</select>
				  			</div>
				  			<div class="cf"></div>
				  		</div>
			  		</form>
		  		</div>
		  		<!--FILTERING OPTIONS-->
		  		<div id="forms-right">
			  		<form id="forms-filters">
			  		<div class="filter-header cf">
				  		<h2>Filters</h2>
						<!--ADDING AND REMOVING FILTERS-->
				  		<div class="filters-add-remove">
							<!--ADDING-->
							<input type="button" value="Add Filter" id="add" style="margin-right:5px;">
							<!--REMOVING-->							
							<input type="button" value="Remove Filter" id="remove" >
					  	</div>

			  		</div>
					<!--LISTING FILTERS-->
			  		<div class="filters-row-containter">
				  		<div class="filter-row cf geographicView" id="filter-1">
							<!--GEOGRAPHIC OR PUBLICATIONS-->
					  		<div class="views-selector cf">
						  		<select name="views-1" id="views-1">
							  		<option value="geographicView" selected>Geographic Filter</option>
							  		<option value="publicationsView">Publication Filter</option>
						  		</select>
					  		</div>
							<!--FILTER BY STATE-->
					  		<div class="states-selector cf">
					  		<select name="states-1" id="states-1" multiple="" style="display: none;">
									<option value="AL">Alabama</option><option value="AK">Alaska</option><option value="AZ">Arizona</option><option value="AR">Arkansas</option><option value="CA">California</option><option value="CO">Colorado</option><option value="CT">Connecticut</option><option value="DE">Delaware</option><option value="DC">District of Columbia</option><option value="FL">Florida</option><option value="GA">Georgia</option><option value="HI">Hawaii</option><option value="ID">Idaho</option><option value="IL">Illinois</option><option value="IN">Indiana</option><option value="IA">Iowa</option><option value="KS">Kansas</option><option value="KY">Kentucky</option><option value="LA">Louisiana</option><option value="ME">Maine</option><option value="MD">Maryland</option><option value="MA">Massachusetts</option><option value="MI">Michigan</option><option value="MN">Minnesota</option><option value="MS">Mississippi</option><option value="MO">Missouri</option><option value="MT">Montana</option><option value="NE">Nebraska</option><option value="NV">Nevada</option><option value="NH">New Hampshire</option><option value="NJ">New Jersey</option><option value="NM">New Mexico</option><option value="NY">New York</option><option value="NC">North Carolina</option><option value="ND">North Dakota</option><option value="OH">Ohio</option><option value="OK">Oklahoma</option><option value="OR">Oregon</option><option value="PA">Pennsylvania</option><option value="RI">Rhode Island</option><option value="SC">South Carolina</option><option value="SD">South Dakota</option><option value="TN">Tennessee</option><option value="TX">Texas</option><option value="UT">Utah</option><option value="VT">Vermont</option><option value="VA">Virginia</option><option value="WA">Washington</option><option value="WV">West Virginia</option><option value="WI">Wisconsin</option><option value="WY">Wyoming</option></select>
					  		</div>
							<!--FILTER BY YEAR-->
					  		<div class="year-selector cf">
						  		<select name="years-1" id="years-1" multiple>
					  	<?
					  	
						//QUERY FOR YEAR FILTRATION
					  	$newspapers = mysqli_query($con,"SELECT fromReport FROM circulationAreas GROUP BY fromReport;");
						//PUT EACH POSSIBLE YEAR INTO FILTER OPTIONS
					  	while($row = mysqli_fetch_array($newspapers))
						  {
						  		
						  			echo "<option value='" . $row['fromReport'] . "'>". $row['fromReport'] ."</option>";	
						  
						  		}
					  	
					  	?>
					</select>
					  		</div>
							<!--FILTER BY PUBLICATION-->
					  		<div class="publication-selector cf">
						  		<select name="publications-1" id="publications-1" multiple>
					  	<?
						//QUERY FOR STATES
					  	$newspaperTypes = mysqli_query($con,"SELECT type, state FROM newspapers GROUP BY state;");
					  	while($row = mysqli_fetch_array($newspaperTypes))
						  {
							  echo "<optgroup label='". $row['state'] ."'>";
						//QUERY FOR PAPERS IN THE STATE
					  	$newspapers = mysqli_query($con,"SELECT * FROM newspapers WHERE state ='" . $row['state'] . "';");

					  	while($row = mysqli_fetch_array($newspapers))
						  {
									//SET EACH POSSIBLE PAPER AS AN OPTION FOR FILTRATION
						  			echo "<option value='" . $row['id'] . "'>". $row['name'] ."</option>";	
						  
						  		}
						  		echo "</optgroup>";
					  	}
					  	?>
					</select>
					  		</div>
					  		</div>
				  		</div>
			  		</form>
		  		</div>
		  		
		  	</div>
	  </header>
	<!--ABOUT US AND REFRESH MAP-->
	<nav class="cf">
		<!--INFORMATION LINKS-->
	  	<ul class="left-nav">
		  	<li>Learn More</li>
		  	<li>Blog</li>
		  	<li>Contact</li>
	  	</ul>
		<!--REFRESH MAP OPTION-->
	  	<div class="right-nav">
		  	<input type="button" name="refreshmap" id="refreshmap" value="Update Map">
	  	</div>
	</nav>
	<!--PAGE CONTENT-->
	<section id="content" class="cf">
				<!--LOADING ACKNOWLEDGEMENT-->
		  		<div id="loadingtext">Loading...</div>
		  	  <!--MAP HOLDER-->
		  	  <div id="map"></div>
			  <!--SIDEBAR HOLDER-->
		  	  <div id="sidebar" class="hidden"></div>
			  <!--JUXTAPOSITION TABLE-->
		  	  <div id="footerInfo">
			  <!--COLOR SCALE-->
		  	  <div id="scale" class="cf">
				<!--FIX SCALE SLIDER-->
		  	  	 <div id="mover"></div><div id="scalefiller"></div>
				<!--SCALE COLORS-->
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
		  	  <!--INFORMATION-->
		  	  <div id="infobar" class="cf">
				
				<div id="infotable">Click on a circulation area for more information.</div></div>

		  	  </div>
	  </section>
	  
	  
	  

  </div>
<!--WHILE LOADING-->
<div id="loading-modal" title="Loading">
  <p>Loading...</p>
</div>
    <!--LINK JQUERY-->
    <script src="scripts/jquery.js"></script>
    <!--LINK JQUERY UI-->
    <script src="scripts/jquery-ui.js"></script>
    <!--MULTIPLE-SELECT MODULE-->
    <script src="scripts/jquery-multipleselect.js"></script>
    <!--GOOGLE MAPS MAP-->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBqhj4sutTn567eZrFQs1hVJUMXsF7gWLI&sensor=true"></script>
    <!--SHADOWBOX MODULE-->
    <script src="scripts/shadowbox/shadowbox.js"></script>
    <!--GOOGLE MAPS API-->
    <script src="//www.google.com/jsapi"  language="javascript"></script>
    <!--LINK JAVASCRIPT-->
    <script src="scripts/script.js"></script> 
	
  </body>
</html>
