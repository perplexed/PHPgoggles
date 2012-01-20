<?php
/* 
	PHP Google Goggles(TM) Response Dictionary

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
   
$Coords = array(
    1 => array("label" => "x", "isValue" => true),
    2 => array("label" => "width", "isValue" => true),
    3 => array("label" => "y", "isValue" => true),
    4 => array("label" => "height", "isValue" => true)
);

$URLlist = array(
    1 => array("label" => "URL1", "isValue" => true),
    2 => array("label" => "URL2", "isValue" => true),
    3 => array("label" => "URL3", "isValue" => true),
    4 => array("label" => "URL4", "isValue" => true),
    5 => array("label" => "URL5", "isValue" => true),
    6 => array("label" => "URL6", "isValue" => true)
);

$Info = array(
    1 => array("label" => "label", "isValue" => true),
    
    2 => array("label" => "type", "isValue" => true),
        
    15690847 => array(
        "label" => "response_data",
        "isValue" => false,
        "contents" => array(
            1 => array(
                "label" => "coordinates",
                "isValue" => false,
                "contents" => $Coords
            ),
            2 => array(
                "label" => "unknown_int",
                "isValue" => true
            ),
            3 => array(
                "label" => "image_result",
                "isValue" => false,
                "contents" => array(
                    1 => array("label" => "image", "isValue" => true),
                    2 => array("label" => "static_image", "isValue" => true, "contents" => $URLlist),
                    3 => array("label" => "image_site", "isValue" => true)
                )
            ),
            4 => array(
                "label" => "unknown1",
                "isValue" => true
            ),
            5 => array(
                "label" => "geographical_data",
                "isValue" => false,
                "contents" => array(
                    1 => array("label" => "lat", "isValue" => true),
                    2 => array("label" => "long", "isValue" => true)
                )
            ),
            6 => array(
                "label" => "lang",
                "isValue" => true
            ),
            7 => array(
                "label" => "result_string",
                "isValue" => true
            ),
            8 => array(
                "label" => "location",
                "isValue" => true
            ),
            9 => array(
                "label" => "result_type",
                "isValue" => true
            ),
            11 => array(
                "label" => "result_link",
                "isValue" => true
            ),
            12 => array(
                "label" => "unknown3",
                "isValue" => true
            ),
            13 => array(
                "label" => "unknown4",
                "isValue" => true
            ),
            14 => array(
                "label" => "relevant_links",
                "isValue" => false,
                "contents" => $URLlist
            ),
            15 => array(
                "label" => "result_data",
                false,
                "contents" => array(
                    1 => array("label" => "line_1", "isValue" => true),
                    2 => array("label" => "line_2", "isValue" => true),
                    3 => array("label" => "line_3", "isValue" => true),
                    4 => array("label" => "line_4", "isValue" => true),
                    5 => array("label" => "line_5", "isValue" => true),
                    6 => array("label" => "line_6", "isValue" => true)
                )
            ),
            16 => array(
                "label" => "unknown6",
                "isValue" => true
            ),
            17 => array(
                "label" => "unknown7",
                "isValue" => true
            ),
            13 => array(
                "label" => "unknown8",
                "isValue" => true
            ),
            19 => array(
                "label" => "std_qur",
                "isValue" => true
            ),
        )
    ),
    15693652 => array(
        "label" => "request_query",
        "isValue" => false,
        "contents" => array(
            2 => array("label" => "url", "isValue" => true)
        )
    ),
    16045192 => array(
        "label" => "direct_result",
        "isValue" => false,
        "contents" => array(
            1 => array("label" => "result_string", "isValue" => true),
            2 => array("label" => "result_info", "isValue" => true),
            3 => array("label" => "result_description", "isValue" => true)
        )
    )
);

$ReplyItem = array(
    1 => array(
        "label" => "info",
        "isValue" => false,
        "contents" => $Info
    ) 
);

$AlternativeInfo = array(
    1 => array(
        "label" => "alternative_info",
        "isValue" => false,
        "contents" => $Info
    ),
    2 => array(
        "label" => "unknown_value",
        "isValue" => true,
    ),
    3 => array(
        "label" => "response_time",
        "isValue" => true,
    ),
    6 => array(
        "label" => "cssid",
        "isValue" => true,
    )
);

$parse_dict = array(
    1 => array(
        "label" => "reply",
        "isValue" => false,
        "contents" => array(
            1 => array(
                "label" => "success_item",
                "isValue" => false,
                "contents" => $Info
            ),
            15705729 => array(
                "label" => "fail_item",
                "isValue" => false,
                "contents" => $AlternativeInfo
            )
        )
    ),
    
    4 => array("label" => "trailing_bytes", "isValue" => true)
);
?>