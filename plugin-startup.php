<?php
####################################################################################################
# This contains all of the wordpress front-end and wp-admin settings and the reference libraries   #
####################################################################################################

# create a unique slug for the plugin WOLFECANDY_[SLUG]_PLUGIN_VERSION - use this with all functions to ensure unique
define('WOLFECANDY_TAGCLOUD_PLUGIN_VERSION', '0.1.0');

#-------------------------------------
# CREATE ANY CUSTOM USER ROLES REQUIRED BY PLUGIN

function WOLFECANDY_TAGCLOUD_add_process_roles_on_plugin_activation() {

#add_role( string $role, string $display_name, array $capabilities = array() )

       #add_role( 'process-manager', 'Process X Manager', array( 'po' => true ) );
	   #add_role( 'process-admin', 'Process X Administrator', array( 'po' => true ) );
	   #...
	   #...
}
register_activation_hook( __FILE__, 'WOLFECANDY_TAGCLOUD_add_process_roles_on_plugin_activation' );


#-------------------------------------
# FUNCTIONS TO RUN AT START-UP

function WOLFECANDY_TAGCLOUD_run_process_startup_functions() {

	# e.g. create database tables
	#CreateTables();
	
 #       if ( check_role('Subscriber')  ){
                # ...
                # ...
 #       } else {  }

}

#-------------------------------------
# run on start up after the theme has been loaded
add_action( 'after_setup_theme', 'WOLFECANDY_TAGCLOUD_run_process_startup_functions' );

#-------------------------------------

############################ ADMIN DASHBOARD #######################

############################ SHOW METHOD

# add menu items via WordPress admin dashboard
# add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', string $icon_url = '', int $position = null )

# Required capabiliy is "edit-pages"

function WOLFECANDY_TAGCLOUD_my_add_menu_item_tasks(){

}
add_filter( 'admin_menu', 'WOLFECANDY_TAGCLOUD_my_add_menu_item_tasks' );

#############################################################################
#-------------------------------------
# ACTIVATE FUNCTIONS USING SHORTCODE

# SYNTAX: add_shortcode(  $tag,  $func ); 

# NB: shortcut code name, function name to include within pages or posts

#-------------------------------------------------
add_shortcode('tag-cloud', 'WlfC_ShowTagCloud');

#-------------------------------------------------

############################ WORDPRESS ACTION FUNCTIONS

function WOLFECANDY_TAGCLOUD_wptp_add_tags_to_attachments() {
        #register_taxonomy_for_object_type( 'post_tag', 'attachment' );
    }
add_action( 'init' , 'WOLFECANDY_TAGCLOUD_wptp_add_tags_to_attachments' );

#############################################################################
############################ BESPOKE FUNCTIONS

# continue in functions.php for generic calls and also create new [x].php files to include
require( 'tag-cloud.php' ); 

	
//----------------------------------------------------------------------
// SET DEFAULT OPTIONS VIA SETTINGS IF WOLFE CANDY TOOLKIT IS INSTALLED
//----------------------------------------------------------------------

// check if this is to use the shared Wolfe Candy library - ie.e. has the WlfC_ shared library already loaded
if(function_exists("WlfC_GL_functions")) { 

	// only do once so options can be changed in the admin screens
	if (WlfC_GetParam("settings-tagcloud-firstrun") == ""){

		// list the configurable options settings
		WlfC_SetParam("settings-tagcloud-title", "Tag Cloud Generator Settings"); 
		WlfC_SetParam("settings-tagcloud-slug", "wolfe-candy-tag-cloud"); // use wp slug for links to wp repository
		WlfC_SetParam("settings-tagcloud-php", "wolfe-candy-tag-cloud.php"); // use wp slug as php name too (default is plugin.meta.php)
		WlfC_SetParam("settings-tagcloud-path", plugin_dir_path( __FILE__ )); 
		WlfC_SetParam("settings-tagcloud-manual", plugin_dir_path( __FILE__ )."tagcloud-manual.txt"); 
	
		// factory default settings
		WlfC_SetParam("settings-tagcloud-style_box_color", "LightGrey"); 	// specify as text or hex
		WlfC_SetParam("settings-tagcloud-style_border_color", "darkblue");	// specify as text or hex
		WlfC_SetParam("settings-tagcloud-style_border_width", "1px");		// specify as pixels
		WlfC_SetParam("settings-tagcloud-style_box_width", "750px");		// specify as pixels
		WlfC_SetParam("settings-tagcloud-style_box_padding", "2px");		// specify as pixels
		WlfC_SetParam("settings-tagcloud-style_font_color", "#4F5AA1"); 	// must be in hex if it needs to be randomised or shaded
		WlfC_SetParam("settings-tagcloud-style_min_font_size",  10);
		WlfC_SetParam("settings-tagcloud-style_max_font_size",  35); 		// Increasing this will make the text larger, decreasing will make it smaller
		WlfC_SetParam("settings-tagcloud-style_set_font",  "");				// fix a font or else it will be random
		WlfC_SetParam("settings-tagcloud-style_random",  true);				// use random italics and bold

		WlfC_SetParam("settings-tagcloud-style_shade_spread",  25); 		// if "random shade" is true then this is the random rgb value spread of the shading - this only works if color is specified in hex format
		WlfC_SetParam("settings-tagcloud-style_shade_random",  true);	 	// randomise the text shade/tints
		WlfC_SetParam("settings-tagcloud-style_color_random",  true); 		// choose random base color
		WlfC_SetParam("settings-tagcloud-style_multi_random",  true); 		// choose random base color each time
		WlfC_SetParam("settings-tagcloud-style_tag_separator",  "&nbsp; &nbsp; &nbsp;");

		WlfC_SetParam("settings-tagcloud-list_random",  true);				// list in a random order or in the array order
		WlfC_SetParam("settings-tagcloud-list_vertical",  true);			// include vertical text
		WlfC_SetParam("settings-tagcloud-list_use_ratings",  false);		// use ratings in an array to specify size (false = random)
		WlfC_SetParam("settings-tagcloud-list_max",  100);					// max number of tags to be displayed
		
		WlfC_SetParam("settings-tagcloud-return_url",  "current");			// return page of links - blank for no link, "current" if return to current page
		WlfC_SetParam("settings-tagcloud-tag_list_mode",  "");   			// how to process the tag list (suggest this is done pre-class call

		// set up the options table in the general settings tab

	#	WlfC_set_config($table_name='SYSTEM', $source='Default', $ctype = 'Format', $spconditions = '',	$cfield='', $initiate=true, $forder='1')
	# 		C-form SpConditions:	$DispTxt @@ $defaultval @@ $options @@ $special @@ $fhelp

		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'html', $spconditions = 'The following are settings relating to the overall cloud box area:<br>',	$cfield='html001',$initiate=true, $forder='00');

		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'color', $spconditions = 'Box Background Color: @@ #D3D3D3 @@  @@  @@ The background color of the cloud box.', $cfield='style_box_color',$initiate=true, $forder='01');
		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'color', $spconditions = 'Border Color: @@ #4F5AA1 @@  @@  @@ The outline border color of the cloud box.', $cfield='style_border_color',$initiate=true, $forder='02');
		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'text', $spconditions = 'Border Width: @@ 1px @@  @@  @@ The border thickness in pixels as [n]px', $cfield='style_border_width',$initiate=true, $forder='03');
		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'text', $spconditions = 'Box Width: @@ 750px @@  @@  @@ The total width of the box on the page in pixels as [n]px', $cfield='style_box_width',$initiate=true, $forder='04');
		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'text', $spconditions = 'Box Padding: @@ 2px @@  @@  @@ The box margin in pixels as [n]px', $cfield='style_box_padding',$initiate=true, $forder='05');

		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'html', $spconditions = '<br>The following are settings relating to the font style and size:<br>',	$cfield='html002',$initiate=true, $forder='06');

		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'number', $spconditions = 'Minimum Font Size: @@ 10 @@  @@  @@ In pixels', $cfield='style_min_font_size',$initiate=true, $forder='07');
		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'number', $spconditions = 'Maximum Font Size: @@ 35 @@  @@  @@ In pixels. Increasing this makes the spread of font sizes greater and decreasing vice versa.', $cfield='style_max_font_size',$initiate=true, $forder='08');
		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'text', $spconditions = 'Specific Set Font: @@  @@  @@  @@ Set a specific font to use - otherwise it will randomise with common browser supported fonts.', $cfield='style_set_font',$initiate=true, $forder='09');
		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'check', $spconditions = 'Randomise Style:  @@ true @@  @@  @@ Randomise normal, italics and bold.', $cfield='style_random',$initiate=true, $forder='10');
		
		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'html', $spconditions = '<br>The following are settings relating to the font color and variation:<br>',	$cfield='html003',$initiate=true, $forder='11');

		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'color', $spconditions = 'Font colour: @@ #4F5AA1 @@  @@  @@ The base color of the font that is to be used if not randomised or multi-color.', $cfield='style_font_color',$initiate=true, $forder='12');
		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'number', $spconditions = 'Maximum Shade Variance: @@ 25 @@  @@  @@ The maximum degree by which the shade is altered randonly if set.', $cfield='style_shade_spread',$initiate=true, $forder='13');
		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'check', $spconditions = 'Randomise Shading:  @@ true @@  @@  @@ Randomise shade color up to the maximum variance.', $cfield='style_shade_random',$initiate=true, $forder='14');
		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'check', $spconditions = 'Randomise Base Color:  @@ true @@  @@  @@ Randomise base color of font.', $cfield='style_color_random',$initiate=true, $forder='15');
		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'check', $spconditions = 'Randomise Multicolor:  @@ true @@  @@  @@ Randomise each tag color so it is a multi-color tag cloud.', $cfield='style_multi_random',$initiate=true, $forder='16');

		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'html', $spconditions = '<br>The following are settings relating to the way the cloud is built:<br>',	$cfield='html004',$initiate=true, $forder='17');

		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'check', $spconditions = 'Randomise List:  @@ true @@  @@  @@ Randomise the tag order each time.', $cfield='list_random',$initiate=true, $forder='18');
		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'check', $spconditions = 'Allow Vertical Tags:  @@ true @@  @@  @@ Randomly display vertical tags.', $cfield='list_vertical',$initiate=true, $forder='19');
		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'check', $spconditions = 'Use Tag Ratings:  @@ false @@  @@  @@ This uses ratings provided in the array to determine the size of a tag - otherwise it is random.', $cfield='list_use_ratings',$initiate=true, $forder='20');
		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'number', $spconditions = 'Maximum Number of Tags to Include: @@ 100 @@  @@  @@ The maximum number of tags - stops after it reaches the limit. This may be counter-productive if random lists are used.', $cfield='list_max',$initiate=true, $forder='21');

		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'html', $spconditions = '<br>The following are settings relating to the actions surrounding the tag cloud:<br>',	$cfield='html005',$initiate=true, $forder='22');
		
		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'text', $spconditions = 'Return URL: @@ current @@  @@  @@ The base URL to return the selected tag for processing. "Current" is the current page. Blank is no link (speficied by the array instead if required).', $cfield='return_url',$initiate=true, $forder='23');
		WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'text', $spconditions = 'Source Mode: @@  @@  @@  @@ The source of tags. Blank is default to posts. See manual for all other options.', $cfield='tag_list_mode',$initiate=true, $forder='24');


		// add shortcuts to toolbar
		#WlfC_set_config('settings-tagcloud', $source='Default', $ctype = 'toolbar', $spconditions = '[description] @@ @@ [icon] @@ [function to call] @@ ',	$cfield='tool1',$initiate=true, $forder='22');
	}

}

//----------------------------------------------------------------------

function show_notices_for_WOLFECANDY_TAGCLOUD() {
    // We attempt to fetch our transient that we stored earlier.
    // If the transient has expired, we'll get back a boolean false
    $message = get_transient( 'WOLFECANDY_TAGCLOUD_activation_error_message' );
	delete_transient ('WOLFECANDY_TAGCLOUD_activation_error_message');

    if ( ! empty( $message ) ) {
        esc_html_e ("<div class='notice notice-error is-dismissible'>
            <p>$message</p>
        </div>");
    }
}

add_action( 'admin_notices', 'show_notices_for_WOLFECANDY_TAGCLOUD' );

//------------------------------------------------------------------

// short code entry point
function WlfC_ShowTagCloud($_atts){

	// create tag instance
	$tag_cloud = new WlfC_TagCloud;
	
	// set the defaults according to precedence
	$tag_cloud->SetDefaults($_atts);
	
	// manual tag list (sample code)
#	$tag_cloud->tag_list = array(
#		'tag1'=>array('link'=>'http', 'rating'=>'10'),
#		'tag2'=>array('link'=>'http', 'rating'=>'5'),
#		'tag3'=>array('link'=>'http', 'rating'=>'9'),
#		'tag4'=>array('link'=>'http', 'rating'=>'9')
#	);

	// manual tag list (sample code)
/*	$tag_cloud->tag_list = array(
		'USA'=>array('link'=>'http', 'rating'=>'10'),
		'United Kingdom'=>array('link'=>'http', 'rating'=>'5'),
		'England'=>array('link'=>'http', 'rating'=>'9'),
		'Ireland'=>array('link'=>'http', 'rating'=>'8'),
		'Nordics'=>array('link'=>'http', 'rating'=>'1'),
		'Wales'=>array('link'=>'http', 'rating'=>'2'),
		'Jersey'=>array('link'=>'http', 'rating'=>'4'),
		'Spain'=>array('link'=>'http', 'rating'=>'5'),
		'France'=>array('link'=>'http', 'rating'=>'6'),
		'Germany'=>array('link'=>'http', 'rating'=>'9'),
		'Italy'=>array('link'=>'http', 'rating'=>'7'),
		'Switzerland'=>array('link'=>'http', 'rating'=>'2'),
		'Austria'=>array('link'=>'http', 'rating'=>'2'),
		'Romainia'=>array('link'=>'http', 'rating'=>'2'),
		'Ukraine'=>array('link'=>'http', 'rating'=>'8'),
		'Turkey'=>array('link'=>'http', 'rating'=>'8'),
		'Malta'=>array('link'=>'http', 'rating'=>'8'),
		'Belgium'=>array('link'=>'http', 'rating'=>'9'),
		'Netherlands'=>array('link'=>'http', 'rating'=>'9'),
		'Croatia'=>array('link'=>'http', 'rating'=>'9'),
		'Cyprus'=>array('link'=>'http', 'rating'=>'9'),
		'Greece'=>array('link'=>'http', 'rating'=>'3'),
		'Scotland'=>array('link'=>'http', 'rating'=>'9')
	);
*/
	// use wordpress tags as default (sample code)
#	$tag_cloud->SetWPTags("post_tag");
#	$tag_cloud->SetWPTags("category");

	// add more tags (sample code)
#	$tag_cloud->AddTag('tag6','10','www.');
#	$tag_cloud->AddTag('tag7','20','www.');
									
	return $tag_cloud->GetHTML();

}


//------------------------------------------------------------------

?>