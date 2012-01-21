/*
    Javascript Google Goggles(TM) XML Parser
   
    This file is part of PHPgoggles.

    PHPgoggles is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    PHPgoggles is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with PHPgoggles.  If not, see <http://www.gnu.org/licenses/>.
    
	The author is in no way affiliated with Google Inc.
*/

function fileUpload() {
	// Upload a file to the server.
	
    var photo = document.getElementById("picField");
    var file = photo.files[0];
	var fileName = file.name;
	var fileSize = file.size;
	if (fileSize > 512000){
	 	document.getElementById('canvas').innerHTML = "<h2>Large File</h2><p>The maximum file size for upload is 500KB</p>";
	  	button.disabled = false;
	  	}
	else {
        document.getElementById('upload_results').innerHTML = '<h1>Sending...</h1>';
	    var uri = "test.php";
	    var xhr = new XMLHttpRequest();
	    xhr.open("POST", uri, true);
        xhr.setRequestHeader("Content-Type", "application/octet-stream");
	    xhr.setRequestHeader("Content-Length", fileSize);
        xhr.setRequestHeader("Connection", "close");
	   
	    xhr.onreadystatechange = function() {
	    	if (xhr.readyState == 4) {
	      		if ((xhr.status >= 200 && xhr.status <= 200) || xhr.status == 304) {
	         
	        	if (xhr.responseText != "") {
                	my_completion_handler(xhr.responseText);
	        	}
	      	}
	    }
	}

	var reader = new FileReader();   
	reader.onload = (function(aImg) { return function(e) {
    	xhr.send(e.target.result); 
        }; })(xhr);
    reader.readAsDataURL(file);
	}
}

function get_image() {
	// Print an image of the uploaded file to the canvas frame.
	
    var photo = document.getElementById("picField");
    var file = photo.files[0];

    var canvas = document.getElementById("canvas");
    canvas.innerHTML = "";
    
    var img = document.createElement('img');
    img.file = file;
    img.style.maxHeight = "240px";
    canvas.appendChild(img);
    
    var reader = new FileReader();
    reader.onload = (function(aImg) { return function(e) { aImg.src = e.target.result; }; })(img);
    reader.readAsDataURL(file);
}

function myunescape (str){
	// Simple HTML formatter.
	
	str = "" + str;
	while (true){
		var i = str . indexOf ('+');
		if (i < 0)
			break;
		str = str . substring (0, i) + '%20' +
			str . substring (i + 1, str . length);
		}
	return unescape (str);
	}

function take_snapshot() {
	// Upload snapshot to server.
	
	document.getElementById('upload_results').innerHTML = '<h1>Sending...</h1>';
	webcam.upload();
	}
	
function go_goggle(){
	// Send either the uploaded image or picture taken from the webcam to the server.
	
	document.getElementById('goggle').src = './images/google_down.png';
	if (document.getElementById('webcam_movie') != null){
		take_snapshot();
		}
	else {
		fileUpload();
		}
	}

function refresh_cam(){
	// Reload the webcam flash object to handle problems.
	
	document.getElementById('upload_results').innerHTML = "";
	d = document.getElementById("upload_results");
	d.style.height = "100%";
	if (document.getElementById('webcam_movie') != null){
		webcam.reset();
		}
	else {
		document.getElementById("canvas").innerHTML = webcam.get_html(320, 240);
		}
	document.getElementById('goggle').src = './images/google_up.png';
	document.getElementById('snap').src = './images/camera_up.png';
	}
	
function init(){
	// JPEGcam function
	
	webcam.set_api_url( 'test.php' );
	webcam.set_quality( 90 ); // JPEG quality (1 - 100)
	webcam.set_shutter_sound( false ); // turn off shutter click sound
	webcam.set_hook( 'onComplete', 'my_completion_handler' );
	var Browser = {
		Version: function() {
			var version = 999; // assume browser iplements FileAPI
			if (navigator.appVersion.indexOf("MSIE") != -1)
				version = parseFloat(navigator.appVersion.split("MSIE")[1]);
			return version;
			}
		}
	if (Browser.Version() < 10) {
		// if client is using IE9 or lower, run this code
		var uploadform = document.getElementById('uploadform');
		var fileupload = document.getElementById('fileupload');
		uploadform.removeChild(fileupload);
		}
	refresh_cam();
	}

function paint_box(res_num, type, x, width, y, height){
	// Add an image overlay to show the location of the results, optimized for Chrome.
	
	var canvas = document.getElementById('canvas');
	var webcam = document.getElementById('webcam_movie');
	var painted = document.createElement('div');
	painted.className = "overlay"; 
	painted.id = "res"+res_num;  
	painted.style.top = (270+y)+"px";  
	painted.style.left = (240+x)+"px";
	painted.style.width = width+"px";  
	painted.style.height = height+"px";
	switch(type){
		case 'object':
			painted.style.borderColor = "red";
			break;
		case 'similar_image':
			painted.style.borderColor = "green";
			break;
		case 'Similar+Image':
			painted.style.borderColor = "green";
			break;
		default:
			painted.style.borderColor = "blue";
		}
	canvas.appendChild(painted);
	}

function clear_box(res_num){
	// Remove the image overlay to show the location of the results.
	
	var canvas = document.getElementById('canvas');
	var box = document.getElementById('res'+res_num);
	canvas.removeChild(box);
	}
		
function my_completion_handler(msg) {
	// Handle the returned response.

	document.getElementById('goggle').src = './images/google_up.png';
	if (msg == 'error'){
		document.getElementById('upload_results').innerHTML =
			'<h1>Server Error</h1>' +
			'<p>Unfortunately the server encountered an error, please try again.</p>';	
		return 0;
		}
	if (window.DOMParser){
		parser=new DOMParser();
		xmlDoc=parser.parseFromString(msg,"text/xml");
		}
	else {
		//IE
		xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
		xmlDoc.async="false";
		xmlDoc.loadXML(msg); 
		}
	d = document.getElementById("upload_results");
	d.innerHTML = "";
	x = xmlDoc.getElementsByTagName("success_item");
	if (x.length>0){
		d.innerHTML="<h1>Results</h1><p>We gathered "+x.length+" results for the image, for more information on the results "+
			'<a href="output.php">download the XML results file</a>.</p>';
		var i = 0;
		while (i<x.length){
			var mydiv = document.createElement('div');
			node = d.appendChild(mydiv);
			var type = x.item(i).getElementsByTagName("type").item(0).childNodes[0].nodeValue;
			if (type == "similar+image"){
				type = "similar_image";
				}
			var coords = x.item(i).getElementsByTagName("coordinates").item(0);
			var x_val = coords.childNodes[0].childNodes[0].nodeValue;
			var x_width = coords.childNodes[1].childNodes[0].nodeValue;
			var y_val = coords.childNodes[2].childNodes[0].nodeValue;
			var y_height = coords.childNodes[3].childNodes[0].nodeValue;
			var res_desc = myunescape(x.item(i).getElementsByTagName("result_string").item(0).childNodes[0].nodeValue);
			var img_static = unescape(x.item(i).getElementsByTagName("static_image").item(0).childNodes[0].nodeValue);
			var img_site = unescape(x.item(i).getElementsByTagName("request_query").item(0).childNodes[0].childNodes[0].nodeValue);
			paint_box(i, type, parseInt(x_val), parseInt(x_width), parseInt(y_val), parseInt(y_height));
			node.innerHTML = 
				'<div class="left"><img src="'+img_static+'" alt="Cached Image"/></div>'+
				'<div class="right"><h2>'+res_desc+'</h2>'+
				'<p><a href="'+img_site+'" target="_blank">Search on Google</a></p>'+
				'<p><a onMouseOver="paint_box('+i+", '"+type+"', "+x_val+', '+x_width+', '+y_val+', '+y_height+')"'+
				' onMouseOut="clear_box('+i+')">'+
				//'x = '+x_val+', width = '+x_width+', y = '+y_val+', height = '+y_height+
				'</a></p></div>';
			node.className = type;
			if (i%2 == 0){
				node.style.cssFloat = "left";
				}
			else {
				node.style.cssFloat = "right";
				}
			i++;
			}
		d.style.height = ((Math.round(i/2)*100)+110)+"px";
		}
	else {
		d.innerHTML="<h1>No Results Found</h1><p>We were unable to get any results for the image"+
		" please browse the similar images below or "+
		'<a href="output.php">download the XML results file</a>.</p>';
		
		x = xmlDoc.getElementsByTagName("alternative_info");
		var i = 0;
		while (i<x.length){
			var mydiv = document.createElement('div');
			node = d.appendChild(mydiv);
			var type = "similar_image";
			var res_desc = myunescape(x.item(i).getElementsByTagName("result_description").item(0).childNodes[0].nodeValue);
			var image = unescape(x.item(i).getElementsByTagName("image").item(0).childNodes[0].nodeValue);
			var img_static = unescape(x.item(i).getElementsByTagName("static_image").item(0).childNodes[0].nodeValue);
			var img_site = unescape(x.item(i).getElementsByTagName("image_site").item(0).childNodes[0].nodeValue);
			if (img_site.length > 60){
				img_site = img_site.substring(0, 27)+'..... '+img_site.substring(img_site.length-30);
				}
			node.innerHTML = 
				'<div class="left"><img src="'+img_static+'" alt="Cached Image" onClick="window.open('+"'"+image+"','_blank');"+'"/></div>'+
				'<div class="right"><h2>'+res_desc+'</h2>'+
				'<p><a href="'+img_site+'" target="_blank">'+img_site+'</a></p></div>';
			node.className = type;
			if (i%2 == 0){
				node.style.cssFloat = "left";
				}
			else {
				node.style.cssFloat = "right";
				}
			i++;
			}
		d.style.height = ((Math.round(i/2)*100)+110)+"px";
		}
	var req_info = "<br><br>"+myunescape(xmlDoc.getElementsByTagName("request_time").item(0).childNodes[0].nodeValue)+'<div style="clear: both">&nbsp;</div>';
	d.innerHTML += req_info;
}
