<?php
/* 
    PHP Google Goggles(TM) Response Parser

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

// dictionary containing a list of field names and values
include('parse_dict.php');

// 3 least significant bits contain the data type the rest of the bits encode the field number
function parse_tag($input, $pos){
	list(,$x) = unpack('C', substr($input, $pos));
	$dtype = $x & 0x7;
	$field = 0;
	$field |= (($x & 0x7f) >> 3);
	$i = 1;
	while (($x & 0x80) != 0){
		list(,$x) = unpack('C', substr($input, $pos+$i));
		$field |= (($x & 0x7f) << (($i*7)-3));
		$i++;
		}
	return array($dtype, $field, $pos+$i);
	}

// Parse a varint encoded variable to decimal
function parse_varint($input, $pos){
	list(,$x) = unpack('C', substr($input, $pos));
	$i = 1;
	$val = 0;
	$val |= $x & 0x7f;
	while (($x & 0x80) != 0){
		list(,$x) = unpack('C', substr($input, $pos+$i));
		$val |= (($x & 0x7f) << ($i*7));
		$i++;
		}
	return array($val, $pos+$i);
	}

//Parse 32bit variable
function parse_32($input, $pos){
	$dat = substr($input, $pos, 4);
	$val = unpack('V', $dat);
	return array($val, $pos+4);
	}

//Double precision 64bit float (lats & longs)
function parse_64($input, $pos){
	$dat = substr($input, $pos, 8);
	list(,$val) = unpack('d', $dat);
	return array($val, $pos+8);
	}

//Read the data after the tag has been parsed	
function get_data($input){
    $pos = 0;
    $result = array();
    while($pos < strlen($input)){
    	list($dtype, $field, $pos) = parse_tag($input, $pos);
	switch($dtype){
		case 0:
			//varint
			list ($data, $pos) = parse_varint($input, $pos);
			break;
		case 1:
			// 64bit;
			list($data, $pos) = parse_64($input, $pos);
			break;
		case 2:
			// lengthdelim (the data may contain more key->value pairs)
			list($length, $pos) = parse_varint($input, $pos);
			$data = substr($input, $pos, $length);
			//list(,$data) = unpack('A*', $dat);
			$pos += $length;
			break;
		case 3:
			// startgroup
			$data = null;
			break;
		case 4:
			// endgroup
			$data = null;
			break;
		case 5:
			// 32bit
			list($data, $pos) = parse_32($input, $pos);
			break;
		default:
			//unknown data type
			$data = null;
		}
	$result[] = array($dtype, $field, $data);
	}
    return $result;
    }
	
function parse_xml($input, $dict){
	$output = "";
	foreach (get_data($input) as $val){
		list($dtype, $field, $data) = $val;
		if (isset($dict[$field])){
			$d = $dict[$field];
			if ($d['isValue'] == true){
				if($dtype == 2){
					if($d['label'] == "Trailing_Bytes"){
						// not sure what the trailing bytes are for
						list(,$data) = unpack('H*', $data);
						}
					else {
						// just a string
						list(,$data) = unpack('A*', $data);
						}
					$dat = "<".$d['label'].">".urlencode($data)."</".$d['label'].">";
					}
				else {
					// all other data dtypes
					$dat = "<".$d['label'].">".$data."</".$d['label'].">";
					}
				}
			else {
				// more key/value pairs
				$dat = "<".$d['label'].">".parse_xml($data, $d['contents'])."</".$d['label'].">";
				}
			$output .= $dat;
			}
		else {
			list(,$data) = unpack('A*', $data);
			$dat = '<unknown_data type="'.$dtype.'" field="'.$field.'">'.$data.'</unknown_data>';
			}
		}
	return $output;
	}

function parse($input){
	global $parse_dict;
	$output .= '<xml_api_reply version="1">';	
	$output .= parse_xml($input, $parse_dict);
	$output .= '</xml_api_reply>';
	$_SESSION['xml'] = $output;
	print $output;
	}
?>