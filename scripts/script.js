	var userState = "RI";
	
	var chosenState = null;
	
	var lat_lng = new google.maps.LatLng(35.6006, -79.4508);
	
	var map = null;
	
	var geocoder;

	var currentpolygon = null;
	
	var zippolygon = [];
	// var zipcoords defines the array to hold all of the coordinates data   
	var zipcoords = [];

	var zipHTML = [];

$(document).ready(function() {
// Defines the UI filters
$("#circulation-selector").multiselect({
	selectedText: "# of # selected",
	minWidth: 'auto'
}).multiselectfilter();
$("#years-selector").multiselect({
	selectedText: "# of # selected",
	minWidth: 'auto'
}).multiselectfilter();
$("#publications-selector").multiselect({
	selectedText: "# of # selected",
	minWidth: 'auto'
}).multiselectfilter();
$("#states-selector").multiselect({
	selectedText: "# of # selected",
	minWidth: 'auto',
	multiple: false,
	selectedList: 4
}).multiselectfilter();
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

if(google.loader.ClientLocation)
	{
	    visitor_lat = google.loader.ClientLocation.latitude;
	    visitor_lon = google.loader.ClientLocation.longitude;
	    visitor_city = google.loader.ClientLocation.address.city;
	    visitor_region = google.loader.ClientLocation.address.region;
	    visitor_country = google.loader.ClientLocation.address.country;
	    visitor_countrycode = google.loader.ClientLocation.address.country_code;
	    console.log(visitor_country + " " + visitor_region);
	    if(visitor_country == "USA") {
	    	userState = visitor_region;
	    	lat_lng = new google.maps.LatLng(visitor_lat, visitor_lon);
		    $('#states-selector option[value="' + visitor_region + '"]').attr("selected", "selected");
	    }
	}

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
function clickPolygon(zipCode, reportHTML){
	if(currentpolygon !== null) {
	currentpolygon.setOptions({
				strokeOpacity: 0.1,
				fillOpacity: .70,
				strokeWeight: 1
			});
}
	currentpolygon = zipCode;
	zipCode.setOptions({
				strokeOpacity: 1,
				strokeWeight: 2,
				fillOpacity: 1
			});

$('#sidebar').html(reportHTML);
$(".open-link").click(function() {
   	$(".open-link-status").text('+');
   	$(".newspaper-content").addClass('hidden');
	$(this).siblings(".newspaper-content").removeClass('hidden');
	$(this).children(".open-link-status").text('-');
});

$('#infotable').html(infoHTML);


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
				var i = 0;
				$(thisRow).children('reports').children('report').each(function() {
				var reportData = {};
				var reportHTML = "";
				var infoHTML = "";

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

								infoHTML = infoHTML + "<table><td class='zipcode'>"+ reportData['zipcode'] +"</td>";
								infoHTML = infoHTML + "<td class='OccupiedHomes'>" + reportData['occupiedhomes'] +"</td>";
								infoHTML = infoHTML + "<td>IncomeLevel</td>";
								infoHTML = infoHTML + "<td>EducationLevel</td>"
 								infoHTML = infoHTML + "<td>Race</td>"
								infoHTML = infoHTML + "<td>Age</td>"
								infoHTML = infoHTML + "<td>FamilyTypes</td>"
								infoHTML = infoHTML + "</table>";

				});
				//ADD STATS into htmlGroup HERE

				zipHTML[zipcode] = htmlGroup;				

				google.maps.event.addDomListener(zippolygon[zipcode], "mouseover", function(){
					mouseoverPolygon(zippolygon[zipcode])});
				google.maps.event.addDomListener(zippolygon[zipcode], "mouseout", function(){
					mouseoutPolygon(zippolygon[zipcode])});
				google.maps.event.addDomListener(zippolygon[zipcode], "click", function(){
					clickPolygon(zippolygon[zipcode], zipHTML[zipcode])});
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
			jQuery('#infobar').html('<div id="infobarplaceholder">No publications cover the selected area</div>');
		}
	});
	map.mapTypes.set('map_style', styledMap);
}
google.maps.event.addDomListener(window, "load", initialize);



Shadowbox.init();
