	var userState = "RI";
	
	var chosenState = null;
	
	var lat_lng = new google.maps.LatLng(35.6006, -79.4508);
	
	var map = null;
	
	var geocoder;

	var currentpolygon = null;
	
	var zippolygon = [];
	// var zipcoords defines the array to hold all of the coordinates data   
	var zipcoords = [];

	var zipReportsHTML = [];

	var zipDemographicsHTML = [];

    function filterHTML(filterID, viewsOptions, statesOptions, yearsOptions, publicationsOptions) {
	    return '<div class="filter-row cf" id="filter-'+filterID+'"><div class="views-selector cf"><select name="views-'+filterID+'" id="views-'+filterID+'">'+viewsOptions+'</select></div><div class="states-selector cf"><select name="states-'+filterID+'" id="states-'+filterID+'" multiple="" style="display: none;">'+statesOptions+'</select></div><div class="year-selector cf"><select name="years-'+filterID+'" id="years-'+filterID+'" multiple>'+yearsOptions+'</select></div><div class="publication-selector cf"><select name="publications-'+filterID+'" id="publications-'+filterID+'" multiple>"'+publicationsOptions+'"</select></div></div>';
    }
    
    function declareMultiSelect() {
$(".views-selector select").multiselect({
	selectedText: "Filter By",
	noneSelectedText: "Filter By",
	minWidth: 'auto',
	multiple: false
}).multiselectfilter();

$(".states-selector select").multiselect({
	selectedList: 1,
	noneSelectedText: "States",
	minWidth: 'auto',
	multiple: false
}).multiselectfilter();

$(".year-selector select").multiselect({
	selectedText: "Change over # years",
	noneSelectedText: "Year",
	minWidth: 'auto',
	selectedList: 1,
}).multiselectfilter();

$(".publication-selector select").multiselect({
	selectedText: "Publications (#)",
	noneSelectedText: "Publications",
	minWidth: 'auto'
}).multiselectfilter();

    }



$(document).ready(function() {
	$("#demographics-1").change(function(){

		var scootaloo = $(this).val();
		if(scootaloo != "00"){
		       $('#infotable td[data-val="'+scootaloo+'"]').addClass("selected").removeClass("selected");
			var tdHTML = $('#infotable td[data-val="'+scootaloo+'"]').parent().html();
			$("#infotable tr:nth-child(2n)").add(tdHTML)
		    }
		    else {

		    }





});
	    if(google.loader.ClientLocation != null)
	{
	    visitor_lat = google.loader.ClientLocation.latitude;
	    visitor_lon = google.loader.ClientLocation.longitude;
	    visitor_city = google.loader.ClientLocation.address.city;
	    visitor_region = google.loader.ClientLocation.address.region;
	    visitor_country = google.loader.ClientLocation.address.country;
	    visitor_countrycode = google.loader.ClientLocation.address.country_code;
	    console.log(visitor_city + " " + visitor_region + " " + visitor_country);
	    $( "#loading-modal" ).html("<p>Welcome Visitor.</p><p>Loading: " + visitor_region + "</p>");
	    if(visitor_country == "USA") {
	    	userState = visitor_region;
	    	lat_lng = new google.maps.LatLng(visitor_lat, visitor_lon);
		    $('#states-1 option[value="' + visitor_region + '"]').attr("selected", "selected");
		    // $(".states-selector select").multiselect("refresh");
	    }
	} else {
		console.log("Location Finding Failed")
	}


	    filterID = 2;
		
		viewsOptions = $(".views-selector select").html();
	    statesOptions = $(".states-selector select").html();
	    yearsOptions = $(".year-selector select").html();
	    publicationsOptions = $(".publication-selector select").html();
	    
	    $('#add').click(function () {
	    	$('.filters-row-containter').append(filterHTML(filterID, viewsOptions, statesOptions, yearsOptions, publicationsOptions));
	    	declareMultiSelect();

	    	filterID++; 
		});
		
	    $('#remove').click(function () {
	    	$('.filters-row-containter .filter-row:last-child ').remove();
		});
$("#demographics-metrics").multiselect({
	selectedText: "Demographics Data (#)",
	noneSelectedText: "Demographics Data",
	minWidth: 'auto',
	multiple: false
}).multiselectfilter();	 
   
$("#outlet-metrics").multiselect({
	selectedText: "Outlet Data (#)",
	noneSelectedText: "Outlet Data",
	minWidth: 'auto',
	multiple: false
}).multiselectfilter();	    

declareMultiSelect();

$(".views-selector select").change(function() {
console.log('changed');
 if($(this).val() == "geographicView" ) {
	 $(this).parent().hide().removeClass("publicationView").addClass("geographicView");
 }
 if($(this).val() == "publicationView" ) {
	 $(this).parent().hide().removeClass("geographicView").addClass("publicationView");
 }
});



$(function() {

	$( "#loading-modal" ).dialog({
      autoOpen: false,
      height:140,
      modal:true,
      show: {
        effect: "blind",
        duration: 1000
      },
      hide: {
        effect: "explode",
        duration: 1000
      }

    });

});
});

// function initialize is loaded towards the bottom on window load with this line:



//google.maps.event.addDomListener(window, "load", initialize);   


function mouseoverPolygon(zipCode){
	if(currentpolygon !== zipCode) {
	zipCode.setOptions({
				strokeOpacity: 0.5,
				fillOpacity: .90,
				strokeWeight: 1
			});
			}
}
function mouseoutPolygon(zipCode){
	if(currentpolygon !== zipCode) {
	zipCode.setOptions({
				strokeOpacity: 0.1,
				fillOpacity: .70,
				strokeWeight: 1
			});
			}
}
function clickPolygon(zipCode,  zipReportsHTML, zipDemographicsHTML){
	if(currentpolygon !== null) {
	currentpolygon.setOptions({
				strokeOpacity: 0.1,
				fillOpacity: .70,
				strokeWeight: 1

			});

	$('#infotable').html("Click on a zipcode to see dempographics information");
}
	currentpolygon = zipCode;
	zipCode.setOptions({
				strokeOpacity: 1,
				strokeWeight: 2,
				fillOpacity: 1
			});

$('#sidebar').html(zipReportsHTML);
$(".open-link").click(function() {
   	$(".open-link-status").text('+');
   	$(".newspaper-content").addClass('hidden');
	$(this).siblings(".newspaper-content").removeClass('hidden');
	$(this).children(".open-link-status").text('-');
});

$('#infotable').html(zipDemographicsHTML);
$('#infotable select').show();

}

function initialize() {
geocoder = new google.maps.Geocoder();

$("#states-selector").change( function() {
  	$('body').addClass("loading"); $( "#loading-modal" ).dialog( "open" );

  	chosenState = this.value;
console.log(chosenState);

$.ajax({
  type: "POST",
  url: "xmlgenerator.php",
  data: { state: chosenState
  }
}).done(function( data ) {

geocoder.geocode( {'address' : chosenState}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
    console.log(results[0].geometry.location);
        map.setCenter(results[0].geometry.location);
        map.setZoom(8);
    }
});


    for(var key in zippolygon)
    {
	    zippolygon[key].setMap(null);
    }



ajaxCall(data);
});


});
$("#publications-selector").change( function() {
  
$.ajax({
  type: "POST",
  url: "xmlgenerator.php",
  data: { publication: this.value
  }
}).done(function( data ) {


    for(var key in zippolygon)
    {
	    zippolygon[key].setMap(null);
    }



ajaxCall(data);
});


});
	$('body').addClass("loading"); $( "#loading-modal" ).dialog( "open" );

$.ajax({
  type: "POST",
  url: "xmlgenerator.php",
  data: { state: userState
  }
}).done(function( data ) {
	ajaxCall(data);
});
function ajaxCall(data) {
	$('body').addClass("loading"); $( "#loading-modal" ).dialog( "open" );

if(zippolygon.length > 1) {
}
	// var zippolygon defines the array to hold all of the polygons   

			// LOOP for each of the circulation areas (children of the response)  
			$(data).children('response').children('area').each(function() {
				// var thisRow stores the circulation area data from 'this' 
				var thisRow = this;
				// var zipcode stores the number of the zipcode from the circulation area
				var zipcode = $(thisRow).children('zipcode').text();
				// if there is only one polygon for the zipcode...
				if ($(thisRow).children('geometry').children('Polygon')) {
						// var polygonOuterBoundary sets up the array to be used in the following loop
						var polygonOuterBoundary = [];
						// var polygonInnerBoundary sets up the array to be used in the following loop
						var polygonInnerBoundary = [];
					// then find out the contents of the polygon's OUTER boundary element
					$(thisRow).children('geometry').children('Polygon').children('outerBoundaryIs').children('LinearRing').each(function() {
						// var outerBoundaryIs stores the contents of the linear ring, which includes a series of longitude and latitude points 
						var outerBoundaryIs = this;
						// retrieves the text of the the linear ring's coordinates 
						outerBoundaryIs = $(outerBoundaryIs).children('coordinates').text();
						// splits the coordinates into a array, given that a space seperates each long/lat pair
						outerBoundaryIs = outerBoundaryIs.split(' ');
						// LOOP for each long/lat pair, uses i as a counter
						for (var i = 0; i < outerBoundaryIs.length; i++) {
							// sets var lngLat as the long/lat pair, splitting the pair, given that a comma seperates long and lat from each other
							var lngLat = outerBoundaryIs[i].split(",");
							// reverses the long and lat values and stores them into a Google Maps readable object LatLng within the zipcoords variable
							polygonOuterBoundary.push(new google.maps.LatLng(lngLat[1], lngLat[0]));							
						};
					});
					// if the polygon has an INNER boundary, then...
					if($(thisRow).children('geometry').children('Polygon').children('innerBoundaryIs').children('LinearRing').size() > 0) {
					// then find out the contents of the polygon's INNER boundary element
					$(thisRow).children('geometry').children('Polygon').children('innerBoundaryIs').children('LinearRing').each(function() {
						// var innerBoundaryIs stores the contents of the linear ring, which includes a series of longitude and latitude points 
						var innerBoundaryIs = this;
						// retrieves the text of the the linear ring's coordinates 
						innerBoundaryIs = $(innerBoundaryIs).children('coordinates').text();
						// splits the coordinates into a array, given that a space seperates each long/lat pair
						innerBoundaryIs = innerBoundaryIs.split(' ');
						// LOOP for each long/lat pair, uses i as a counter
						for (var i = 0; i < innerBoundaryIs.length; i++) {
							// sets var lngLat as the long/lat pair, splitting the pair, given that a comma seperates long and lat from each other
							var lngLat = innerBoundaryIs[i].split(",");
							// reverses the long and lat values and stores them into a Google Maps readable object LatLng within the zipcoords variable
							polygonInnerBoundary.push(new google.maps.LatLng(lngLat[1], lngLat[0]));							
						};
					});
					// store polygon's outer and inner boundaries
					var polygonCoords = [polygonOuterBoundary, polygonInnerBoundary];
				} else {
					// store polygon's outer boundary only because there is no inner boundary
					var polygonCoords = [polygonOuterBoundary];
				}
				}
				
				// if there are multiple polygons for the zipcode (I've only found that each zipcode will have a maximum of two polygons)...
				if ($(thisRow).children('MultiGeometry').size() > 0) {
					// var polygonExtraInnerBoundary sets up the array to be used in the following loop
					var polygonExtraOuterBoundary = [];
					// var polygonExtraOuterBoundary sets up the array to be used in the following loop
					var polygonExtraInnerBoundary = [];
					// LOOP for each of the circulation areas (children of the response)  
					$(thisRow).children('geometry').children('MultiGeometry').children('Polygon').each(function() {
						// then find out the contents of the polygon's OUTER boundary element
						$(this).children('outerBoundaryIs').children('LinearRing').each(function() {
							// var outerBoundaryIs stores the contents of the linear ring, which includes a series of longitude and latitude points 
							var outerBoundaryIs = this;
							// retrieves the text of the the linear ring's coordinates 
							outerBoundaryIs = $(outerBoundaryIs).children('coordinates').text();
							// splits the coordinates into a array, given that a space seperates each long/lat pair
							outerBoundaryIs = outerBoundaryIs.split(' ');
							// LOOP for each long/lat pair, uses i as a counter
							for (var i = 0; i < outerBoundaryIs.length; i++) {
								// sets var lngLat as the long/lat pair, splitting the pair, given that a comma seperates long and lat from each other
								var lngLat = outerBoundaryIs[i].split(",");
								// reverses the long and lat values and stores them into a Google Maps readable object LatLng within the zipcoords variable
								polygonExtraOuterBoundary.push(new google.maps.LatLng(lngLat[1], lngLat[0]));							
							};
						});
					// if the polygon has an INNER boundary, then...
					if($(this).children('innerBoundaryIs').children('LinearRing').size() > 0) {
						$(this).children('innerBoundaryIs').children('LinearRing').each(function() {
							// var innerBoundaryIs stores the contents of the linear ring, which includes a series of longitude and latitude points 
							var innerBoundaryIs = this;
							// retrieves the text of the the linear ring's coordinates 
							innerBoundaryIs = $(innerBoundaryIs).children('coordinates').text();
							// splits the coordinates into a array, given that a space seperates each long/lat pair
							innerBoundaryIs = innerBoundaryIs.split(' ');
							// LOOP for each long/lat pair, uses i as a counter
							for (var i = 0; i < innerBoundaryIs.length; i++) {
								// sets var lngLat as the long/lat pair, splitting the pair, given that a comma seperates long and lat from each other
								var lngLat = innerBoundaryIs[i].split(",");
								// reverses the long and lat values and stores them into a Google Maps readable object LatLng within the zipcoords variable
								polygonExtraInnerBoundary.push(new google.maps.LatLng(lngLat[1], lngLat[0]));							
							};
						});
					// store polygon's outer and inner boundaries
					var polygonExtraCoords = [polygonExtraOuterBoundary, polygonExtraInnerBoundary];
						} else {
					// store polygon's outer boundary only because there is no inner boundary
					var polygonExtraCoords = [polygonExtraOuterBoundary];
						}
					});
				}
				var color = "#" + $(this).children("stats").children("color").text();
				
				// create the polygon
				zippolygon[zipcode] = new google.maps.Polygon({
					paths: polygonCoords,
					strokeColor: '#FFFFFF',
					strokeOpacity: 0.1,
					strokeWeight: 1,
					fillColor: color,
					fillOpacity: 0.70
				});
				zippolygon[zipcode].setMap(map);
				
				var reportGroup = [];
				var htmlGroup = [];
				var infobarGroup = [];				
				var i = 0;
				var infoHTML = "";
				var homes = [];

				$(thisRow).children('reports').children('report').each(function() {
				var reportData = {};
				var reportHTML = "";
				
				

						var from = parseInt($(this).attr("from"));
						// reportData.push($(this).text());
								

							 	reportData['zipcode'] = parseInt(zipcode, 10);
							 	reportData['reportPeriod'] = parseInt($(this).attr("from"), 10);
							 	reportData['reportDate'] = $(this).attr("date");
							 	reportData['name'] = $(this).children("name").text();
							 	reportData['additionaldescription'] = $(this).children("additionaldescription").text();
							 	reportData['combinedaverage'] = parseInt($(this).children("combinedaverage").text(), 10);
							 	reportData['combinedsundaycirculation'] = parseInt($(this).children("combinedsundaycirculation").text(), 10);
							 	reportData['frequency'] = $(this).children("frequency").text();
							 	reportData['fridaycirculation'] = parseInt($(this).children("fridaycirculation").text(), 10);
							 	reportData['mondaycirculation'] = parseInt($(this).children("mondaycirculation").text(), 10);
							 	reportData['paperid'] = parseInt($(this).children("paperid").text(), 10);
							 	reportData['saturdaycirculation'] = parseInt($(this).children("saturdaycirculation").text(), 10);
							 	reportData['sundaycirculation'] = parseInt($(this).children("sundaycirculation").text(), 10) + parseInt($(this).children("combinedsundaycirculation").text(), 10);
							 	reportData['thursdaycirculation'] = parseInt($(this).children("thursdaycirculation").text(), 10);
							 	reportData['tuesdaycirculation'] = parseInt($(this).children("tuesdaycirculation").text(), 10);
							 	reportData['tuesdaycirculation'] = parseInt($(this).children("tuesdaycirculation").text(), 10);
							 	reportData['wednesdaycirculation'] = parseInt($(this).children("wednesdaycirculation").text(), 10);
							 	reportData['occupiedhomes'] = parseFloat($(this).children("occupiedhomes").text());

								reportGroup.push(reportData);

								
								reportHTML = reportHTML + "<div class='newspaper-group newspaper-" + reportData['paperid'] + "'>";
								reportHTML = reportHTML + "<p class='open-link'><span class='open-link-newspaper-name'>" + reportData['name'] + "</span> <span class='open-link-report-date'>(" + reportData['reportPeriod'] + ")</span><span class='open-link-status'>+</span></p>";
								reportHTML = reportHTML + "<div class='newspaper-content hidden'> ";
								reportHTML = reportHTML + "<h3>Report Taken: " + reportData['reportDate'] + "</h3>";
								reportHTML = reportHTML + "<table><thead><td>Avg. Daily</td><td>Mon</td><td>Tue</td><td>Wed</td><td>Thu</td><td>Fri</td><td>Sat</td><td>Sun</td></thead>";
								reportHTML = reportHTML + "<tr><td>" + reportData['combinedaverage'] + "</td><td>" + reportData['mondaycirculation'] + "</td><td>" + reportData['tuesdaycirculation'] + "</td><td>" + reportData['wednesdaycirculation'] + "</td><td>" + reportData['thursdaycirculation'] + "</td><td>" + reportData['fridaycirculation'] + "</td><td>" + reportData['saturdaycirculation'] + "</td><td>" + reportData['sundaycirculation'] + "</td></tr></table>";
								reportHTML = reportHTML + "";
								reportHTML = reportHTML + "<h3>" + "Occupied Homes: " + reportData['occupiedhomes'] + "<h3>";
								reportHTML = reportHTML + "";								
								reportHTML = reportHTML + "</div>";
								reportHTML = reportHTML + "</div>";
								htmlGroup.push(reportHTML);


								homes.push([
								reportData['occupiedhomes'], reportData['reportPeriod']
								]);
								

				});
				//ADD STATS into htmlGroup HERE
				zipReportsHTML[zipcode] = htmlGroup;	


				infobarGroup['zipname'] = $(this).children("zipname").text();
				infobarGroup['population'] = $(this).children("population").text();
				infobarGroup['medianAge'] = $(this).children("medianAge").text();
				infobarGroup['pctBachelors'] = $(this).children("pctBachelors").text();
				infobarGroup['pctUnemployed'] = $(this).children("pctUnemployed").text();
				infobarGroup['income'] = $(this).children("income").text();
				infobarGroup['households'] = $(this).children("households").text();
				infobarGroup['householdSize'] = $(this).children("householdSize").text();
				infobarGroup['pctHousingOwned'] = $(this).children("pctHousingOwned").text();
				infobarGroup['pctHousingRented'] = $(this).children("pctHousingRented").text();
				infobarGroup['pctHousingVacant'] = $(this).children("pctHousingVacant").text();
				infobarGroup['medianHomeValue'] = $(this).children("medianHomeValue").text();
				infobarGroup['pctProjectedGrowth'] = $(this).children("pctProjectedGrowth").text();
				infobarGroup['pctHouseholdGrowth'] = $(this).children("pctHouseholdGrowth").text();
				infobarGroup['pctIncomeGrowth'] = $(this).children("pctIncomeGrowth").text();


				infoHTML = infoHTML + "<table><tr class='labels'><th>Zipcode</th>";
				infoHTML = infoHTML + "<th>Name</th>";
				infoHTML = infoHTML + "<th><select name='demographics-1' id='demographics-1'><option value='population' selected>Population</option><option value='households'>households</option><option value='medianAge'>medianAge</option><option value='pctBachelors'>pctBachelors</option><option value='pctUnemployed'>pctUnemployed</option><option value='income'>income</option><option value='householdSize'>householdSize</option><option value='pctHousingOwned'>pctHousingOwned</option><option value='pctHousingRented'>pctHousingRented</option><option value='pctHousingVacant'>pctHousingVacant</option><option value='medianHomeValue'>medianHomeValue</option><option value='pctProjectedGrowth'>pctProjectedGrowth</option><option value='pctHouseholdGrowth'>pctHouseholdGrowth</option><option value='pctIncomeGrowth'>pctIncomeGrowth</option></select></th>";

				infoHTML = infoHTML + "<th><select name='demographics-2' id='demographics-2'><option value='population'>Population</option><option value='households' selected>households</option><option value='medianAge'>medianAge</option><option value='pctBachelors'>pctBachelors</option><option value='pctUnemployed'>pctUnemployed</option><option value='income'>income</option><option value='householdSize'>householdSize</option><option value='pctHousingOwned'>pctHousingOwned</option><option value='pctHousingRented'>pctHousingRented</option><option value='pctHousingVacant'>pctHousingVacant</option><option value='medianHomeValue'>medianHomeValue</option><option value='pctProjectedGrowth'>pctProjectedGrowth</option><option value='pctHouseholdGrowth'>pctHouseholdGrowth</option><option value='pctIncomeGrowth'>pctIncomeGrowth</option></select></th>";

				infoHTML = infoHTML + "<th><select name='demographics-3' id='demographics-3'><option value='population'>Population</option><option value='households'>households</option><option value='medianAge' selected>medianAge</option><option value='pctBachelors'>pctBachelors</option><option value='pctUnemployed'>pctUnemployed</option><option value='income'>income</option><option value='householdSize'>householdSize</option><option value='pctHousingOwned'>pctHousingOwned</option><option value='pctHousingRented'>pctHousingRented</option><option value='pctHousingVacant'>pctHousingVacant</option><option value='medianHomeValue'>medianHomeValue</option><option value='pctProjectedGrowth'>pctProjectedGrowth</option><option value='pctHouseholdGrowth'>pctHouseholdGrowth</option><option value='pctIncomeGrowth'>pctIncomeGrowth</option></select></th>";

				infoHTML = infoHTML + "<th><select name='demographics-4' id='demographics-4'><option value='population'>Population</option><option value='households'>households</option><option value='medianAge'>medianAge</option><option value='pctBachelors' selected>pctBachelors</option><option value='pctUnemployed'>pctUnemployed</option><option value='income'>income</option><option value='householdSize'>householdSize</option><option value='pctHousingOwned'>pctHousingOwned</option><option value='pctHousingRented'>pctHousingRented</option><option value='pctHousingVacant'>pctHousingVacant</option><option value='medianHomeValue'>medianHomeValue</option><option value='pctProjectedGrowth'>pctProjectedGrowth</option><option value='pctHouseholdGrowth'>pctHouseholdGrowth</option><option value='pctIncomeGrowth'>pctIncomeGrowth</option></select></th>";

				infoHTML = infoHTML + "<th><select name='demographics-5' id='demographics-5'><option value='population'>Population</option><option value='households'>households</option><option value='medianAge'>medianAge</option><option value='pctBachelors'>pctBachelors</option><option value='pctUnemployed' selected>pctUnemployed</option><option value='income'>income</option><option value='householdSize'>householdSize</option><option value='pctHousingOwned'>pctHousingOwned</option><option value='pctHousingRented'>pctHousingRented</option><option value='pctHousingVacant'>pctHousingVacant</option><option value='medianHomeValue'>medianHomeValue</option><option value='pctProjectedGrowth'>pctProjectedGrowth</option><option value='pctHouseholdGrowth'>pctHouseholdGrowth</option><option value='pctIncomeGrowth'>pctIncomeGrowth</option></select></th>";
				infoHTML = infoHTML + "</tr>";


/*
				infoHTML = infoHTML + "<table><tr class='labels'><th>Zipcode</th><th>Name</th><th>Population</th><th>Households</th><th>MedianAge</th>";
				infoHTML = infoHTML + "<th>PercentBachelors</th><th>PercentUnemployed</th><th>Income</th><th>HouseholdSize</th>";
				infoHTML = infoHTML + "<th>Housing Owned</th><th>Housing Rented</th><th>Housing Vacant</th><th>Median Home Value</th>";
				infoHTML = infoHTML + "<th>Projected Growth</th><th>Household Growth</th><th>Income Growth</th></tr>";
				
				infoHTML = infoHTML + "<tr class='national'><td>National Average</td><td></td>";
				infoHTML = infoHTML + "<td>311114482</td><td>116723555</td><td>37.43</td><td>27.72%</td><td>11.14%</td><td>$26737.35<td>2.65</td><td>58.67%</td><td>30.52%</td><td>10.81%</td><td>$201407.61</td><td>0.73%</td><td>0.75%</td><td>2.28%</td>"
*/
				infoHTML = infoHTML + "<tr><td>" + zipcode + "</td>";
				infoHTML = infoHTML + "<td>" + infobarGroup['zipname'] + "</td>";
				infoHTML = infoHTML + "<td class='selected' data-val='population'>" + infobarGroup['population'] + "</td>";
				infoHTML = infoHTML + "<td class='selected' data-val='households'>" + infobarGroup['households'] + "</td>";
				infoHTML = infoHTML + "<td class='selected' data-val='medianAge'>" + infobarGroup['medianAge'] + "</td>";
				infoHTML = infoHTML + "<td class='selected' data-val='pctBachelors'>" + infobarGroup['pctBachelors'] + "%</td>";
				infoHTML = infoHTML + "<td class='un-selected' data-val='pctUnemployed'>" + infobarGroup['pctUnemployed'] + "%</td>";
				infoHTML = infoHTML + "<td class='un-selected' data-val='income'>$" + infobarGroup['income'] + "</td>";
				infoHTML = infoHTML + "<td class='un-selected' data-val='householdSize'>" + infobarGroup['householdSize'] + "</td>";
				infoHTML = infoHTML + "<td class='un-selected' data-val='pctHousingOwned'>" + infobarGroup['pctHousingOwned'] + "%</td>";
				infoHTML = infoHTML + "<td class='un-selected' data-val='pctHousingRented'>" + infobarGroup['pctHousingRented'] + "%</td>";
				infoHTML = infoHTML + "<td class='un-selected' data-val='pctHousingVacant'>" + infobarGroup['pctHousingVacant'] + "%</td>";
				infoHTML = infoHTML + "<td class='un-selected' data-val='medianHomeValue'>$" + infobarGroup['medianHomeValue'] + "</td>";
				infoHTML = infoHTML + "<td class='un-selected' data-val='pctProjectedGrowth'>" + infobarGroup['pctProjectedGrowth'] + "%</td>";
				infoHTML = infoHTML + "<td class='un-selected' data-val='pctHouseholdGrowth'>" + infobarGroup['pctHouseholdGrowth'] + "%</td>";
				infoHTML = infoHTML + "<td class='un-selected' data-val='pctIncomeGrowth'>" + infobarGroup['pctIncomeGrowth'] + "%</td></tr>";

				infoHTML = infoHTML + "</table>";
				zipDemographicsHTML[zipcode] = infoHTML;






								

				google.maps.event.addDomListener(zippolygon[zipcode], "mouseover", function(){
					mouseoverPolygon(zippolygon[zipcode])})
				google.maps.event.addDomListener(zippolygon[zipcode], "mouseout", function(){
					mouseoutPolygon(zippolygon[zipcode])});
				google.maps.event.addDomListener(zippolygon[zipcode], "click", function(){
					clickPolygon(zippolygon[zipcode], zipReportsHTML[zipcode], zipDemographicsHTML[zipcode])});
			});
		$('body').addClass("loading"); $( "#loading-modal" ).dialog( "close" );

}
	// var oldZip is used for keeping tracking of the activated polygon 
	var oldZip = null;
	// var style defines the dark color scheme used by the map 
	var style = [{
		"featureType": "road",
		"stylers": [{
			"visibility": "off"
		}]
	}, {
		"featureType": "administrative.province",
		"stylers": [{
			"weight": 2.2
		}, {
			"lightness": -50
		}]
	}, {
		"featureType": "poi.park",
		"elementType": "labels",
		"stylers": [{
			"visibility": "off"
		}]
	}, {
		"featureType": "landscape.natural.terrain",
		"elementType": "labels",
		"stylers": [{
			"visibility": "off"
		}]
	}, {
		"featureType": "landscape",
		"elementType": "geometry",
		"stylers": [{
			"color": "#000000"
		}]
	}, {
		"featureType": "administrative",
		"elementType": "labels",
		"stylers": [{
			"invert_lightness": true
		}, {
			"lightness": -52
		}]
	}, {
		"featureType": "poi.park",
		"elementType": "geometry",
		"stylers": [{
			"lightness": -65
		}, {
			"saturation": -65
		}]
	}, {
		"featureType": "water",
		"stylers": [{
			"saturation": -57
		}, {
			"lightness": -83
		}]
	}, {
		"featureType": "poi.government",
		"stylers": [{
			"visibility": "off"
		}]
	}, {
		"featureType": "administrative",
		"elementType": "geometry.fill",
		"stylers": [{
			"visibility": "off"
		}]
	}, {
		"featureType": "poi",
		"stylers": [{
			"lightness": -57
		}]
	}, {
		"featureType": "landscape",
		"elementType": "labels",
		"stylers": [{
			"visibility": "off"
		}]
	}];
	// var styledMaps defines the Google Maps object for styled maps 
	var styledMap = new google.maps.StyledMapType(style, {
		name: "Styled Map"
	});
	// var center defines the central point of map on load, right now it's focused on NC
	// var mapOptions sets the map settings   
	var mapOptions = {
		zoom: 7,
		center: lat_lng,
		mapTypeId: 'map_style',
		mapTypeControl: false,
		mapTypeControlOptions: {
			mapTypeIds: ['map_style']
		}
	}
	// var map finds the HTML element with the ID #map and loads the map into it   
	map = new google.maps.Map(document.getElementById('map'), mapOptions);

	// ajaxCall();
	// the $.get function is used to make an AJAX call using jQuery to the xmlgenerator.php file that contains the XML  
	google.maps.event.addListener(map, 'click', function() {
		if (currentpolygon !== null) {
			jQuery('#mover').animate({
				top: '-30px'
			});
			currentpolygon.setOptions({
				strokeOpacity: 0.1,
				fillOpacity: .70
			});
			jQuery('#infotable').html('No publications cover the selected area');
		}
	});
	map.mapTypes.set('map_style', styledMap);
}
google.maps.event.addDomListener(window, "load", initialize);



Shadowbox.init();
