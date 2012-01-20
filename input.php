<?php
/*
	PHP Google Goggles(TM) Request Creator
	
	This cose is based on deetch's python project hosted at:
   	https://github.com/deetch/goggles-experiment
   
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

session_start();

include('new_parser.php');
//User-agent header is v.important otherwise you'll get a 500 error
ini_set('user_agent', "Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_1_3 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7E18 Safari/528.16 GoogleMobileApp/0.7.3.5675 GoogleGoggles-iPhone/1.0; gzip");
//Set user-agent twice for good measure
$headers = '{"Content-Type": "application/x-protobuffer", "Pragma": "no-cache", "User-Agent": "Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_1_3 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7E18 Safari/528.16 GoogleMobileApp/0.7.3.5675 GoogleGoggles-iPhone/1.0; gzip"}';
$url = "http://www.google.com/goggles/container_proto?cssid=";
// the following string contains some magic ints and "iPhone OS 4.1 iPhone3GS" as "user-agent";
$activation_magic = pack("C*", 34, 0, 98, 60, 10, 19, 34, 2, 101, 110, 186, 211, 240, 59, 10, 8, 1, 16, 1, 40, 1, 48, 0, 56, 1, 18, 29, 10, 9, 105, 80, 104, 111, 110, 101, 32, 79, 83, 18, 3, 52, 46, 49, 26, 0, 34, 9, 105, 80, 104, 111, 110, 101, 51, 71, 83, 26, 2, 8, 2, 34, 2, 8, 1);
$cssid = '';
$response_headers = array();

function to_varint($value){
    $index = 0;
    $varint = "";
    while ((0x7F & $value) != 0){
        $i = (0x7F & $value);
        if ((0x7F & ($value >> 7)) != 0){
            $i += 128;
        }
        $varint .= pack ("C*", $i);
        $value = $value >> 7;
        $index++;
    }
    return $varint;
}

function gen_cssid(){
    $random_array = array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f');
    array_flip($random_array);
    shuffle($random_array);
    return implode($random_array);
    }
    
function init(){
    global $activation_magic;
    global $headers;
    global $url;
    global $response_headers;
    global $cssid;
    $cssid = gen_cssid();
    $req =  html_request($url.$cssid, $activation_magic, $headers);
    while ($response_headers[0] != "HTTP/1.1 200 OK"){
    	$cssid = gen_cssid();
    	$req =  html_request($url.$cssid, $activation_magic, $headers);
    }
    return $cssid;
}

function encode_image($image){
    $size = strlen($image);
    $a = to_varint($size + 32);
    $b = to_varint($size + 14);
    $c = to_varint($size + 10);
    $size = to_varint($size);
	//trailing bytes contain some data about the request -> 3 varint variables with the image and another lot of variables under the field "15705794".
    $trailing_bytes = pack("C*", 24, 75, 32, 1, 48, 0, 146, 236, 244, 59, 9, 24, 0, 56, 198, 151, 220, 223, 247, 37, 34, 0);
    return pack("C*", 10).$a.pack("C*", 10).$b.pack("C*", 10).$c.pack("C*", 10).$size.$image.$trailing_bytes;
    }
    
function send_image($image){
    global $headers;
    global $url;
    global $response_headers;
    global $cssid;
    $data = "";
    $req = html_request($url.init(), encode_image($image), $headers);
    if ($response_headers[0] == "HTTP/1.1 200 OK"){    	
    	parse($req);
    	}
    else {
    	print 'error';
    	}
}

function html_request($url, $data, $optional_headers = null) { 
    global $response_headers;
    $params = array('http' => array( 'method' => 'post', 'content' => $data ));    
    if ($optional_headers!== null) { 
        $params['http']['header'] = $optional_headers; 
    } 
    $ctx = stream_context_create($params); 
    $fp = @fopen($url, 'rb', false, $ctx); 
    if (!$fp) { 
        throw new Exception("Problem with $url, $php_errormsg"); 
    } 
    $response = @stream_get_contents($fp);  
    if ($response === false) { 
        throw new Exception("Problem reading data from $url, $php_errormsg"); 
     }
    $response_headers = $http_response_header;
    return $response; 
}

$photo = file_get_contents('php://input');
send_image($photo);    
?>