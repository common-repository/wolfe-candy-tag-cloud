<?php

//Licensed under GPLv3, full text at http://www.gnu.org/licenses/gpl-3.0.txt

//===============================================================


class WlfC_TagCloud {

	// style parameters - note - all in text format
	var $style_box_color = "LightGrey";
	var $style_border_color = "darkblue";
	var $style_border_width = "1px";
	var $style_box_width = "75%";
	var $style_box_padding = "2px";
	var $style_font_color = "#4F5AA1"; // must be in hex if it needs to be randomised or shaded
	var $style_min_font_size = "10";
	var $style_max_font_size = "35"; 		// Increasing this will make the text larger, decreasing will make it smaller
	var $style_set_font = ""; 
	var $style_random = "true";			// Randomise italics and bold
	
	var $style_shade_spread = "25"; 		// if "random shade" is true then this is the random rgb value spread of the shading - this only works if color is specified in hex format
	var $style_shade_random = "true";	 	// randomise the text shade/tints
	var $style_color_random = "true"; 	// choose random base color
	var $style_multi_random = "true"; 	// choose random base color each time
	
	var $style_tag_separator = "&nbsp; &nbsp; &nbsp;";

	var $list_random = "true";
	var $list_vertical = "true";
	var $list_use_ratings = "false";
	var $list_max="100";
	
	var $return_url = "";
	var $selected_tag="";
	
	var $tag_list=array(); // actual tag list (with rating and link data if provided)
	var $tag_list_mode = "";   // specify the taxonomy to use

//===============================================================
	
function GetHTML(){

	// font list
	$font_array=array("Arial","Arial Narrow","Times","Helvetica","Calibri","Verdana","Courier","Candara","Geneva","Optima","Cambria","Garamond","Didot","Perpetua","Copper Plate");
	$font_start_array = array("","","<i>","<b>", "");
	$font_end_array = array("","","</i>","</b>", "");

	// add return tag
	$add_char = strpos($this->return_url,"?") ? "&" : "?"; 
	$new_url = $this->return_url.$add_char."tagsel=";
	$html_out = "";

	$s1 = $this->style_box_padding;
	$s2 = $this->style_border_width;
	$s3 = $this->color_name_to_hex($this->style_border_color);
	$s4 = $this->color_name_to_hex($this->style_box_color);
	$s5 = $this->style_box_width;

	// add css styles in a block - sanitize first using strip_tags
	$html_style = "<style>".strip_tags(" 
		.tags_div
		{
		padding: $s1;
		border:  $s2 solid $s3;
		background-color: $s4;
		width: $s5;
		-moz-border-radius: 5px;
		}

		.vertical-text {
		  display: inline-block;
		  transform: rotate(-90deg); 
		  -moz-transform: rotate(-90deg);
		}

		A:link
		{
		text-decoration: none;
		}

		A:hover
		{
		text-decoration: none;
		border: 3px solid darkgray;
		}
		
	")."</style>";
	
	$html_out = "";

//===============================================================
	
	// blank means use default post_tag list that is assigned
	if ($tag_list_mode == "" && empty($this->tag_list)){
		$this->tag_list=$this->SetWPTags(); 
	}

	$tabs = $this->tag_list; 

	// do not return anything for an empty array
	if (!empty($tabs)){
	
		$max_count = $this->MaxRating($tabs); // use in case of ratings provided in array
		$html_out = $html_style;

		// randomise if required
		if (strtolower($this->list_random)=="true") {
			uksort($tabs, function() {return rand() > getrandmax()/2;}); }
		
		// change base colour
		if (strtolower($this->style_color_random)=="true"){ $this->style_font_color = $this->random_base_color(intval($this->style_shade_spread));}

		$cnt = 0; // used to see if max has been reached

		foreach($tabs as $tab => $tabval)
		{
			// get associated values
			$rating = floatval($tabval["rating"])+0;
			$hlink = $tabval["link"]."";

			// rating based font size (% of max size based on % rating occurance) or random
			$x = !(strtolower($this->list_use_ratings)=="true") ? round((rand(0,100)/100) * intval($this->style_max_font_size) ): round(($rating / $max_count) * intval($this->style_max_font_size));			$font_size = intval($this->style_min_font_size + $x).'px';
			if ($font_size =='0px'){ $font_size = intval($this->style_min_font_size).'px';} # set default if calculation not generating correct output

			//random font, random style, random color range, random text direction
			$font_sel = $this->style_set_font=="" ? $font_array[rand(0,14)] : $this->style_set_font;
			$font_sel = " font-family: ".$font_sel.", Arial; "; // defaults to Arial if font specified is not supported by the browser
			$rpos = rand(0,4);
			$font_sel_s = strtolower($this->style_random)=="true" ? $font_start_array[$rpos] : "";
			$font_sel_e = strtolower($this->style_random)=="true" ? $font_end_array[$rpos] : "";

			// modify text color
			if (strtolower($this->style_multi_random)=="true"){ $this->style_font_color = $this->random_base_color(intval($this->style_shade_spread));}
			if (strtolower($this->style_shade_random)=="true"){ $this->style_font_color = $this->random_color($this->color_name_to_hex($this->style_font_color), intval($this->style_shade_spread));}
			$font_col = "style='color: ".$this->style_font_color.";'";  # #676F9D

			$font_vert = "";
			if (($x*2.5) <= intval($this->style_max_font_size)){
				if (rand(0,2) == 0) {
					if (strtolower($this->list_vertical)=="true") {$font_vert = " class='vertical-text' ";} # 30% of the smaller ones are made vertical
				}
			}
			
			// exit if maximum list level is reached
			if ($cnt >= intval($this->list_max)){ break;} // stop 
			$cnt++;
			
			// create tag out link
			$html_url = ($this->return_url=="") ? $font_sel_s.$tab.$font_sel_e : "<a href='$new_url".urlencode($tab)."' $font_col>".$font_sel_s.$tab.$font_sel_e."</a>";
			
			// build tag output
			$html_out .= "<span $font_vert style='font-size: ".$font_size.";".$font_sel." ".$font_col."' >";
			$html_out .= $html_url;
			$html_out .= "</span>".$this->style_tag_separator;
		}

	return "<div align='center' class='tags_div'>".$html_out."</div>";
	}
}

//===============================================================

function SetURL(){

	// url to return link
	global $wp;

	$current_url =  sanitize_url($_SERVER['REQUEST_URI']) ;
	
	// remove any previous tag selections from the URL args
	$current_url = str_replace("&tagsel=","@@TAG SEL@@",$current_url);
	$current_url = str_replace("?tagsel=","@@TAG SEL@@",$current_url);
	$clean_url = explode("@@TAG SEL@@",$current_url);
	$current_url = $clean_url[0];

	$this->return_url = $current_url;
}

//===============================================================

function GetSelected(){   
	
	// populate the last selected tab
	if (isset($_REQUEST["tagsel"])){
		$this->selected_tag = sanitize_text_field($_REQUEST["tagsel"]);
		return $this->selected_tag;
	}
}

//===============================================================

function GetDefaults(){
// returns the factory default array (4th precedence)

	// Factory defaults
	$defaults = array(

		'style_box_color' => "LightGrey", 	// specify as text or hex
		'style_border_color' => "darkblue",	// specify as text or hex
		'style_border_width' => "1px",		// specify as pixels
		'style_box_width' => "75%",			// specify as % not pixels to support mobile
		'style_box_padding' => "2px",		// specify as pixels
		'style_font_color' => "#4F5AA1", 	// must be in hex if it needs to be randomised or shaded
		'style_min_font_size' => "10",
		'style_max_font_size' => "35", 		// Increasing this will make the text larger, decreasing will make it smaller
		'style_set_font' => "",				// fix a font or else it will be random
		'style_random' => "true",				// use random italics and bold

		'style_shade_spread' => "25", 		// if "random shade" is true then this is the random rgb value spread of the shading - this only works if color is specified in hex format
		'style_shade_random' => "true",	 	// randomise the text shade/tints
		'style_color_random' => "true", 		// choose random base color
		'style_multi_random' => "true", 		// choose random base color each time
		'style_tag_separator' => "&nbsp; &nbsp; &nbsp;",

		'list_random' => "true",				// list in a random order or in the array order
		'list_vertical' => "true",			// include vertical text
		'list_use_ratings' => "false",		// use ratings in an array to specify size (false = random)
		'list_max' => "100",					// max number of tags to be displayed
		
		'return_url' => "current",			// return page of links - blank for no link, "current" if return to current page
		'tag_list_mode' => ""   			// how to process the tag list (suggest this is done pre-class call
    );

	############################################################################################################################
	
	// get args from shortcode argument	(1st precedent) or URL POST (2nd precedent) or settings (3rd precedent) or factory default (4th precedent)
	$def_array = array_keys($defaults);
	foreach($def_array as $defitem){
	
		$def_out = "settings-tagcloud-".$defitem;
		
		// first get settings (3rd precedence) if using shared library for options
		if(function_exists("WlfC_GL_functions")) { 
			$defaults[$defitem]=GetParam($def_out);		
		}
		
		// then get POST (2nd precedence)
		if (isset($_REQUEST[$defitem])){
			$defaults[$defitem]=sanitize_text_field($_REQUEST[$defitem]);	
		}
	}
	return $defaults;
}

//------------------------------------------------------------------

function SetDefaults( $atts = array() ){
// populate the tag cloud with defaults according to settings or POST values, as well as factory defaults
// optional shortcode atts take 1st precedent

	// get args from shortcode argument	(1st precedent) or URL POST (2nd precedent) or settings (3rd precedent) or factory default (4th precedent)
	// set the default option in case shortcode is not populated
	$defaults = $this->GetDefaults();
    $atts = shortcode_atts( $defaults, $_atts );
	
	// validation is done through options and non-valid args will be ignored
	// get args from shortcode argument	(1st precedent) or default array (2nd precedent)
	$this->style_box_color = $this->color_name_to_hex($atts['style_box_color']);
	$this->style_border_color = $this->color_name_to_hex($atts['style_border_color']);	// specify as text or hex
	$this->style_border_width = $atts['style_border_width'];		// specify as pixels
	$this->style_box_width = $atts['style_box_width'];		// specify as pixels
	$this->style_box_padding = $atts['style_box_padding'];		// specify as pixels
	$this->style_font_color = $this->color_name_to_hex($atts['style_font_color']); 	// must be in hex if it needs to be randomised or shaded
	$this->style_min_font_size = $atts['style_min_font_size'];
	$this->style_max_font_size = $atts['style_max_font_size'];		// Increasing this will make the text larger, decreasing will make it smaller
	$this->style_set_font = $atts['style_set_font'];				// fix a font or else it will be random
	$this->style_random = $atts['style_random'];				// use random italics and bold

	$this->style_shade_spread = $atts['style_shade_spread']; 		// if "random shade" is true then this is the random rgb value spread of the shading - this only works if color is specified in hex format
	$this->style_shade_random = $atts['style_shade_random'];	 	// randomise the text shade/tints
	$this->style_color_random = $atts['style_color_random']; 		// choose random base color
	$this->style_multi_random = $atts['style_multi_random']; 		// choose random base color each time
	$this->style_tag_separator = $atts['style_tag_separator'];

	$this->list_random = $atts['list_random'];				// list in a random order or in the array order
	$this->list_vertical = $atts['list_vertical'];			// include vertical text
	$this->list_use_ratings = $atts['list_use_ratings'];		// use ratings in an array to specify size (false = random)
	$this->list_max = $atts['list_max'];					// max number of tags to be displayed
	
	$this->return_url = $atts['return_url'];			// return page of links - blank for no link, "current" if return to current page
	$this->tag_list_mode = $atts['tag_list_mode'];   			// how to process the tag list (suggest this is done pre-class call

	// determine return link if specified 
	if ($atts['return_url'] != ""){
		if (strtolower($atts['return_url']) == "current") {
			$this->SetURL();			// only set url to current page if default settings-tagcloud-return-url not set and links wanted to return to page - blank is no link setting
		} 
	}
	
	// get the selected tag in order to action locally
	$this->GetSelected();

}
//------------------------------------------------------------------

function AddTag($name, $rating="1", $link=""){

	// add a tag to the list	
	$sub_array = array();	

	$sub_array['link']= $link;
	$sub_array['rating']= $rating;
	$this->tag_list[$name]=$sub_array;

}

//===============================================================
// get wordpress tags and links

/*
	$tag_cloud->tag_list = array(
							'tag1'=>array('link'=>'http', 'rating'=>'10'),
							'tag2'=>array('link'=>'http', 'rating'=>'5'),
							'tag3'=>array('link'=>'http', 'rating'=>'9'),
							'tag4'=>array('link'=>'http', 'rating'=>'9')
							);

*/
function SetWPTags($taxon="post_tag"){

	// override default if the setting is set
	if ($this->tag_list_mode != ""){ $taxon = $this->tag_list_mode; }
	
	// get all tags - even unassigned
	$tags = get_tags(array('taxonomy' => $taxon,'hide_empty' => false));

	foreach ( $tags as $tag ) {
		$tag_link = get_tag_link( $tag->term_id );				 
		$sub_array = array();	

		$sub_array['link']= $tag_link;
		$sub_array['rating']= '1';
		$out_array[$tag->name]=$sub_array;

	}
	$this->tag_list = $out_array;		// reset the current tag list
	return $out_array;

}


//===============================================================
// UTILITY FUNCTIONS AVAILABLE WITHIN CLASS
//===============================================================

function random_color($hex_col, $spread=25){

	// exit if the colour is not specified in hex
	if (strlen(trim($hex_col)) != 7) { return $hex_col; }
	
	$font_color_r =hexdec($this->strRGB($hex_col,"r"));
	$font_color_g =hexdec($this->strRGB($hex_col,"g"));
	$font_color_b =hexdec($this->strRGB($hex_col,"b"));

	$r = rand($font_color_r-$spread, $font_color_r+$spread);
	$g = rand($font_color_g-$spread, $font_color_g+$spread);
	$b = rand($font_color_b-$spread, $font_color_b+$spread);    
	
	return "#".dechex($r).dechex($g).dechex($b);

}

//===============================================================

function strRGB($str_in, $colel="r"){

	if (strlen($str_in) != 7){ return 0; }
	$RGB_array = str_split($str_in);
	
	$cl = strtolower($colel);
	switch ($cl){
	
		case "r": return $RGB_array[1].$RGB_array[2]; break;		
		case "g": return $RGB_array[3].$RGB_array[4]; break;
		case "b": return $RGB_array[5].$RGB_array[6]; break;
		
		default: return 0;
	}

}

//===============================================================

function random_base_color($spread=25){

	// choose 3 random rgb values within spread
	for($c=0;$c<3;++$c) {
		$color[$c] = rand(0+$spread,255-$spread);
	}
	$font_color = "#".dechex($color[0]).dechex($color[1]).dechex($color[2]);
	
	return $font_color;

}

//===============================================================

// converts an html color name to a hex color value
// if the input is not a color name, the original value is returned

function color_name_to_hex($color_name)
{
	$hex_out = "";
	
    // standard 147 HTML color names
    $colors  =  array(
        'aliceblue'=>'F0F8FF',
        'antiquewhite'=>'FAEBD7',
        'aqua'=>'00FFFF',
        'aquamarine'=>'7FFFD4',
        'azure'=>'F0FFFF',
        'beige'=>'F5F5DC',
        'bisque'=>'FFE4C4',
        'black'=>'000000',
        'blanchedalmond '=>'FFEBCD',
        'blue'=>'0000FF',
        'blueviolet'=>'8A2BE2',
        'brown'=>'A52A2A',
        'burlywood'=>'DEB887',
        'cadetblue'=>'5F9EA0',
        'chartreuse'=>'7FFF00',
        'chocolate'=>'D2691E',
        'coral'=>'FF7F50',
        'cornflowerblue'=>'6495ED',
        'cornsilk'=>'FFF8DC',
        'crimson'=>'DC143C',
        'cyan'=>'00FFFF',
        'darkblue'=>'00008B',
        'darkcyan'=>'008B8B',
        'darkgoldenrod'=>'B8860B',
        'darkgray'=>'A9A9A9',
        'darkgreen'=>'006400',
        'darkgrey'=>'A9A9A9',
        'darkkhaki'=>'BDB76B',
        'darkmagenta'=>'8B008B',
        'darkolivegreen'=>'556B2F',
        'darkorange'=>'FF8C00',
        'darkorchid'=>'9932CC',
        'darkred'=>'8B0000',
        'darksalmon'=>'E9967A',
        'darkseagreen'=>'8FBC8F',
        'darkslateblue'=>'483D8B',
        'darkslategray'=>'2F4F4F',
        'darkslategrey'=>'2F4F4F',
        'darkturquoise'=>'00CED1',
        'darkviolet'=>'9400D3',
        'deeppink'=>'FF1493',
        'deepskyblue'=>'00BFFF',
        'dimgray'=>'696969',
        'dimgrey'=>'696969',
        'dodgerblue'=>'1E90FF',
        'firebrick'=>'B22222',
        'floralwhite'=>'FFFAF0',
        'forestgreen'=>'228B22',
        'fuchsia'=>'FF00FF',
        'gainsboro'=>'DCDCDC',
        'ghostwhite'=>'F8F8FF',
        'gold'=>'FFD700',
        'goldenrod'=>'DAA520',
        'gray'=>'808080',
        'green'=>'008000',
        'greenyellow'=>'ADFF2F',
        'grey'=>'808080',
        'honeydew'=>'F0FFF0',
        'hotpink'=>'FF69B4',
        'indianred'=>'CD5C5C',
        'indigo'=>'4B0082',
        'ivory'=>'FFFFF0',
        'khaki'=>'F0E68C',
        'lavender'=>'E6E6FA',
        'lavenderblush'=>'FFF0F5',
        'lawngreen'=>'7CFC00',
        'lemonchiffon'=>'FFFACD',
        'lightblue'=>'ADD8E6',
        'lightcoral'=>'F08080',
        'lightcyan'=>'E0FFFF',
        'lightgoldenrodyellow'=>'FAFAD2',
        'lightgray'=>'D3D3D3',
        'lightgreen'=>'90EE90',
        'lightgrey'=>'D3D3D3',
        'lightpink'=>'FFB6C1',
        'lightsalmon'=>'FFA07A',
        'lightseagreen'=>'20B2AA',
        'lightskyblue'=>'87CEFA',
        'lightslategray'=>'778899',
        'lightslategrey'=>'778899',
        'lightsteelblue'=>'B0C4DE',
        'lightyellow'=>'FFFFE0',
        'lime'=>'00FF00',
        'limegreen'=>'32CD32',
        'linen'=>'FAF0E6',
        'magenta'=>'FF00FF',
        'maroon'=>'800000',
        'mediumaquamarine'=>'66CDAA',
        'mediumblue'=>'0000CD',
        'mediumorchid'=>'BA55D3',
        'mediumpurple'=>'9370D0',
        'mediumseagreen'=>'3CB371',
        'mediumslateblue'=>'7B68EE',
        'mediumspringgreen'=>'00FA9A',
        'mediumturquoise'=>'48D1CC',
        'mediumvioletred'=>'C71585',
        'midnightblue'=>'191970',
        'mintcream'=>'F5FFFA',
        'mistyrose'=>'FFE4E1',
        'moccasin'=>'FFE4B5',
        'navajowhite'=>'FFDEAD',
        'navy'=>'000080',
        'oldlace'=>'FDF5E6',
        'olive'=>'808000',
        'olivedrab'=>'6B8E23',
        'orange'=>'FFA500',
        'orangered'=>'FF4500',
        'orchid'=>'DA70D6',
        'palegoldenrod'=>'EEE8AA',
        'palegreen'=>'98FB98',
        'paleturquoise'=>'AFEEEE',
        'palevioletred'=>'DB7093',
        'papayawhip'=>'FFEFD5',
        'peachpuff'=>'FFDAB9',
        'peru'=>'CD853F',
        'pink'=>'FFC0CB',
        'plum'=>'DDA0DD',
        'powderblue'=>'B0E0E6',
        'purple'=>'800080',
        'red'=>'FF0000',
        'rosybrown'=>'BC8F8F',
        'royalblue'=>'4169E1',
        'saddlebrown'=>'8B4513',
        'salmon'=>'FA8072',
        'sandybrown'=>'F4A460',
        'seagreen'=>'2E8B57',
        'seashell'=>'FFF5EE',
        'sienna'=>'A0522D',
        'silver'=>'C0C0C0',
        'skyblue'=>'87CEEB',
        'slateblue'=>'6A5ACD',
        'slategray'=>'708090',
        'slategrey'=>'708090',
        'snow'=>'FFFAFA',
        'springgreen'=>'00FF7F',
        'steelblue'=>'4682B4',
        'tan'=>'D2B48C',
        'teal'=>'008080',
        'thistle'=>'D8BFD8',
        'tomato'=>'FF6347',
        'turquoise'=>'40E0D0',
        'violet'=>'EE82EE',
        'wheat'=>'F5DEB3',
        'white'=>'FFFFFF',
        'whitesmoke'=>'F5F5F5',
        'yellow'=>'FFFF00',
        'yellowgreen'=>'9ACD32');

    $color_name = strtolower($color_name);
    if (isset($colors[$color_name]))
    {
        $hex_out = '#' . $colors[$color_name];
    }
    else
    {
        $hex_out = $color_name;
    }
	return $hex_out;
}

// -----------------------------------------------------------------------

function MaxRating($tabsarray){

	// default is 1 to avoid any division by 0
	$max_rating = 1;
	
	// only loop through if the ratings are actually used 
	if (strtolower($this->list_use_ratings) == "true"){
		foreach($tabsarray as $tab => $tabval){
			$rating = floatval($tabval["rating"])+0;
			if ($rating > $max_rating){ $max_rating=$rating; }
		}
	}
	return $max_rating;
}

// -----------------------------------------------------------------------

/* create tags code
$tags = array(
    array('name' => 'Beachfront Escapes', 'slug' => 'beachfront-escapes'),
    array('name' => 'Group Holidays', 'slug' => 'group-holidays'),
    array('name' => 'City Breaks', 'slug' => 'city-breaks')
);

foreach ($tags as $tag) {
    if (!term_exists($tag['name'], 'post_tag')) {
        wp_insert_term($tag['name'], 'post_tag', array('slug' => $tag['slug']));
    }
}
*/

// -----------------------------------------------------------------------

} // END OF CLASS

?>