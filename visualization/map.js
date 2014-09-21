var map;
var geo = new Object();
var markerArray = new Array();
var windowArray = new Array();



if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
        geo.lat = position.coords.latitude;
        geo.lng = position.coords.longitude;
        //alert(geo.lat);
        initialize();
        
    }, function(positionError) {
           console.log("Denied");
    geo.lat = 0;
    geo.lng = 0;
     initialize();
    });
}
 



var data = {
    "result": 200,
    "data": [
        {
            "site_name": "Plum Creek",
            "site_id": "12",
            "lat_degrees": "43",
            "lat_minutes": "5",
            "lat_seconds": "32.5458",
            "lng_degrees": "-83",
            "lng_minutes": "20",
            "lng_seconds": "15.7662",
            "site_directions": "Go to the water",
            "assessments": [
                {
                    "site_name": "12. Plum Creek",
                    "assessment_date": "2014-09-18",
                    "stream_quality_score": "34",
                    "stream_quality_grade": "Good (34 - 48)"
                },
                {
                    "site_name": "12. Plum Creek",
                    "assessment_date": "2013-05-05",
                    "stream_quality_score": "10",
                    "stream_quality_grade": "Poor (< 19)"
                }
            ]
        },
        {
            "site_name": "FlintRiverSouth",
            "site_id": "7",
            "lat_degrees": "42",
            "lat_minutes": "57",
            "lat_seconds": "43.3332",
            "lng_degrees": "-83",
            "lng_minutes": "14",
            "lng_seconds": "7.5402",
            "site_directions": "Go to the water's edge",
            "assessments": [
                {
                    "site_name": "7. FlintRiverSouth",
                    "assessment_date": "2014-09-18",
                    "stream_quality_score": "37",
                    "stream_quality_grade": "Good (34 - 48)"
                },
                {
                    "site_name": "7. FlintRiverSouth",
                    "assessment_date": "2014-09-18",
                    "stream_quality_score": "8",
                    "stream_quality_grade": "Poor (< 19)"
                }
            ]
        }
    ]
}



function initialize() {

    var mapOptions = {
        zoom: 9,
        center: new google.maps.LatLng(43.07, -83.56)
    };
    map = new google.maps.Map(document.getElementById('map-canvas'),
        mapOptions);

    $.each(data.data, function(index, marker) {
        addListItem(marker,index);
        marker.lat = ConvertDMSToDD(marker.lat_degrees,marker.lat_minutes,marker.lat_seconds);
        marker.long = ConvertDMSToDD(marker.lng_degrees,marker.lng_minutes,marker.lng_seconds);
        console.log("95: "+marker.lat);
        console.log("96: "+marker.long);
        marker.gLatLng = new google.maps.LatLng(marker.lat, marker.long);
        var newMarker = new google.maps.Marker({
            position: new google.maps.LatLng(marker.lat, marker.long),
            map: map,
            icon: 'https://dl.dropboxusercontent.com/u/1991578/icon.png',
            title: marker.name
        });
        markerArray[index] = newMarker;
        var infowindow = new google.maps.InfoWindow({
            content: getWindowHTML(marker)
        });
        windowArray[index] = infowindow;
        google.maps.event.addListener(newMarker, 'click', function() {
            infowindow.open(map, newMarker);

        });
    });

    $(".list-group-item").click(function(){
       var index = $(this).attr("data-index");
       closeAllWindows();
       map.setCenter(data.data[index].gLatLng);
       windowArray[index].open(map, markerArray[index]);

    });
}

function closeAllWindows(){
    $.each(windowArray,function(index,infoWindow){
       infoWindow.close();
    });
}

function addListItem(marker,index){
     var html = "";
    html += '<a href="#" class="list-group-item" data-index="'+index+'">';
    html += "<h3>" + marker.site_name + "</h3>";
    html += "<br>";
    html += "<label>Last Assessment</label>";
    html += "<table class='table table-striped table-bordered'>";
    html +=  "<thead>";
    html += "<tr>";
    html += "   <th>Date</th>";
    html += "   <th>Score</th>";
    html += "    <th>Grade</th>";
    html += "   </tr>";
    html += "</thead>";
    html += "<tbody>";
    html += "<tr>";
    html += "   <th>"+marker.assessments[0].assessment_date+"</th>";
    html += "   <th>"+marker.assessments[0].stream_quality_score+"</th>";
    html += "    <th>"+marker.assessments[0].stream_quality_grade+"</th>";
    html += "   </tr>";
    html += "</tbody>";
    html += "</table>";
    html += "</a>";
    
    $(".list-group").append(html);
    
}
//google.maps.event.addDomListener(window, 'load', initialize);

//see http://stackoverflow.com/questions/14560999/using-the-haversine-formula-in-javascript
function getDistance(lat, lng) {
    if(!lat || !lng){
         return "Unkonwn";
    }
    var R = 6371; // km 
   ///console.log(parseFloat(lng));
    //has a problem with the .toRad() method below.
    var x1 = parseFloat(geo.lat) - parseFloat(lat);
    
    var dLat = x1.toRad();
    var x2 = parseFloat(geo.lng) - parseFloat(lng);
    var dLon = x2.toRad();
    var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(parseFloat(lat).toRad()) * Math.cos(parseFloat(geo.lat).toRad()) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    var dist = R * c;
    ///console.log(parseFloat(geo.lng));
    return (Math.ceil(dist));
}


Number.prototype.toRad = function() {
    return this * Math.PI / 180;
}

function ConvertDMSToDD(degrees, minutes, seconds) {
    degrees = parseInt(degrees);
    minutes = parseInt(minutes);
    seconds = parseInt(seconds);

    console.log(degrees+":"+minutes+":"+seconds);
    var dd = degrees + (minutes / 60) + (seconds / 3600);

    return dd;
}

function getWindowHTML(marker){
    var ret = "";
    ret += "<div class='infoWindow'>";
    ret += "<h4>" + marker.site_name + "</h4>";
    ret += "<br>";
    ret += "<label>Assessments</label>";
    ret += "<table class='table table-striped table-bordered'>";
    ret +=  "<thead>";
    ret += "<tr>";
    ret += "   <th>Date</th>";
    ret += "   <th>Score</th>";
    ret += "    <th>Grade</th>";
    ret += "   </tr>";
    ret += "</thead>";
    ret += "<tbody>";
    $.each(marker.assessments,function(index,assesment){
    ret += "<tr>";
    ret += "   <th>"+assesment.assessment_date+"</th>";
    ret += "   <th>"+assesment.stream_quality_score+"</th>";
    ret += "    <th>"+assesment.stream_quality_grade+"</th>";
    ret += "   </tr>";
    });
    ret += "</tbody>";
    ret += "</table>";
    ret += "<hr>";

    ret += "<p><b>Directions:</b><br>"+marker.site_directions+"</p>";

    if(geo.lat && geo.lng){
        ret += "<p><b>Distance:</b><br>"+getDistance(parseFloat(marker.lat), parseFloat(marker.long)) + " miles away</p>";
  }
    
    ret += "</div>";

    return ret;
}