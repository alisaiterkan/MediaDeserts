<? 


include_once("http://mediadeserts.bodkin.me/secure/connection.php");


 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Media Deserts</title>
    <link href='http://fonts.googleapis.com/css?family=Bevan' rel='stylesheet' type='text/css'>
    <link href="style.css" rel="stylesheet">
	<link href="scripts/ui-theme/jquery-ui-1.10.0.custom.css" rel="stylesheet">
	<link href="scripts/jquery-multipleselect.css" rel="stylesheet">
	<link href="scripts/shadowbox/shadowbox.css" rel="stylesheet">
  </head>
  <body>
  <div id="page-wrapper" class="body row scroll-y">
	  
	  <header>
		  
		  <h1>Media Deserts</h1>
		  <h2>Finding communities that lack access to fresh local news and information</h2>
		  
		  <div id="filters">
			  <form>
			  	<div class="col col8">
			  	</div>
			  	<div class="col col8">
			  	</div>
			  	<div class="col col8">
			  	<label for="publications">Publications</label>
				  	<select name="publications" id="publications-selector" multiple>
					  	<?
					  	$newspaperTypes = mysqli_query($con,"SELECT type FROM newspapers GROUP BY type;");
					  	while($row = mysqli_fetch_array($newspaperTypes))
						  {
							  echo "<optgroup label='". $row['type'] ."'>";
					  	$newspapers = mysqli_query($con,"SELECT * FROM newspapers;");

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
			  	<div class="col col8">
			  	</div>
			  	<div class="col col8">
			  	</div>
			  	<div class="col col8">
			  	</div>
			  </form>
		  </div>
		  
	  </header>
	  
	  <section id="content">
		  		<div id="loadingtext">Loading...</div>
		  		
		  	  <div id="map"></div>
		  	  <div id="sidebar" class="hidden">
			  	  
			  	  <ul>
			  	  	<li>
				  	  	<h2>Newspaper Name #1</h2>
				  	  	<ul>
					  	  	<li><span class="stats-label">Circulation Level:</span> 45%</li>
					  	  	<li><span class="stats-label">Monday Circulation:</span> 45%</li>
				  	  	</ul>
			  	  	</li>
			  	  	<li>
				  	  	<h2>Newspaper Name #2</h2>
				  	  	<ul>
					  	  	<li><span class="stats-label">Circulation Level:</span> 45%</li>
					  	  	<li><span class="stats-label">Monday Circulation:</span> 45%</li>
				  	  	</ul>
			  	  	</li>			  	  
			  	  </ul>
			  	  
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

    <script src="scripts/jquery.js"></script>
    <script src="scripts/jqueryui.js"></script>
    <script src="scripts/jquery-multipleselect.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBqhj4sutTn567eZrFQs1hVJUMXsF7gWLI&sensor=true"></script>
    <script src="scripts/shadowbox/shadowbox.js"></script>
    <script src="scripts/script.js"></script>
    
  </body>
</html>
