<?php

/* Finding the path to the wp-admin folder */
$iswin = preg_match('/:\\\/', dirname(__file__));
$slash = ($iswin) ? "\\" : "/";

$wp_path = preg_split('/(?=((\\\|\/)wp-content)).*/', dirname(__file__));
$wp_path = (isset($wp_path[0]) && $wp_path[0] != "") ? $wp_path[0] : $_SERVER["DOCUMENT_ROOT"];

/** Load WordPress Administration Bootstrap */
require_once($wp_path . $slash . 'wp-load.php');
require_once($wp_path . $slash . 'wp-admin' . $slash . 'admin.php');

load_plugin_textdomain( 'kimili-flash-embed', FALSE, 'kimili-flash-embed/langs/');

$title = "Kimili Flash Embed";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php bloginfo('name') ?> &rsaquo; <?php echo esc_html( $title ); ?> &#8212; WordPress</title>
<?php

wp_admin_css( 'css/global' );
wp_admin_css();
wp_admin_css( 'css/colors' );
wp_admin_css( 'css/ie' );

$hook_suffix = '';
if ( isset($page_hook) )
	$hook_suffix = "$page_hook";
else if ( isset($plugin_page) )
	$hook_suffix = "$plugin_page";
else if ( isset($pagenow) )
	$hook_suffix = "$pagenow";

do_action("admin_print_styles-$hook_suffix");
do_action('admin_print_styles');
do_action("admin_print_scripts-$hook_suffix");
do_action('admin_print_scripts');
do_action("admin_head-$hook_suffix");
do_action('admin_head');


?>
<link rel="stylesheet" href="<?php echo plugins_url('/kimili-flash-embed/css/generator.css'); ?>?ver=<?php echo $KimiliFlashEmbed->version ?>" type="text/css" media="screen" title="no title" charset="utf-8" />
<script src="<?php echo plugins_url('/kimili-flash-embed/js/kfe.js'); ?>?ver=<?php echo $KimiliFlashEmbed->version ?>" type="text/javascript" charset="utf-8"></script>
<!--
	<?php echo esc_html($title." Tag Generator" ); ?> is heavily based on
	SWFObject 2 HTML and JavaScript generator v1.2 <http://code.google.com/p/swfobject/>
	Copyright (c) 2007-2008 Geoff Stearns, Michael Williams, and Bobby van der Sluis
	This software is released under the MIT License <http://www.opensource.org/licenses/mit-license.php>
-->

</head>
<body class="<?php echo apply_filters( 'admin_body_class', '' ); ?>">

	<div class="wrap" id="KFE_Generator">
	
		<h2><?php echo esc_html($title." ".__("Tag Generator",'kimili-flash-embed') ); ?></h2> 

		<div class="note"><?php _e('Asterisk (<span class="req">*</span>) indicates required field','kimili-flash-embed'); ?></div> 
		<fieldset> 
			<legend><?php _e("SWFObject Configuration",'kimili-flash-embed'); ?> [ <a id="toggle1" href="#">-</a> ]</legend> 
			<div id="toggleable1">
				<div class="col1"> 
					<label for="publishingMethod"><?php _e("Publish method",'kimili-flash-embed'); ?>:</label> <span class="req">*</span> 
				</div> 
				<div class="col2"> 
					<select id="publishingMethod" name="publishmethod"> 
						<option value="static" <?php if (!get_option('kml_flashembed_publish_method')) echo "selected=\"selected\""; ?>><?php _e("Static publishing",'kimili-flash-embed'); ?></option> 
						<option value="dynamic" <?php if (get_option('kml_flashembed_publish_method')) echo "selected=\"selected\""; ?>><?php _e("Dynamic publishing",'kimili-flash-embed'); ?></option> 
					</select> 
					<a id="togglePublishingMethodHelp" href="#"><?php _e("what is this?",'kimili-flash-embed'); ?></a> 
				</div> 
				<div class="clear">&nbsp;</div> 
				<div id="publishingMethodHelp" class="help"> 
					<h2><?php _e("Static publishing",'kimili-flash-embed'); ?></h2> 
					<h3><?php _e("Description",'kimili-flash-embed'); ?></h3> 
					<p><?php _e("Embed Flash content and alternative content using standards compliant markup, and use unobtrusive JavaScript to resolve the issues that markup alone cannot solve.",'kimili-flash-embed'); ?></p> 
					<h3><?php _e("Pros",'kimili-flash-embed'); ?></h3> 
					<p><?php _e("The embedding of Flash content does not rely on JavaScript and the actual authoring of standards compliant markup is promoted.",'kimili-flash-embed'); ?></p> 
					<h3><?php _e("Cons",'kimili-flash-embed'); ?></h3> 
					<p><?php _e("Does not solve 'click-to-activate' mechanisms in Internet Explorer 6+ and Opera 9+.",'kimili-flash-embed'); ?></p> 
					<h2><?php _e("Dynamic publishing",'kimili-flash-embed'); ?></h2> 
					<h3><?php _e("Description",'kimili-flash-embed'); ?></h3> 
					<p><?php _e("Create alternative content using standards compliant markup and embed Flash content with unobtrusive JavaScript.",'kimili-flash-embed'); ?></p> 
					<h3><?php _e("Pros",'kimili-flash-embed'); ?></h3> 
					<p><?php _e("Avoids 'click-to-activate' mechanisms in Internet Explorer 6+ and Opera 9+.",'kimili-flash-embed'); ?></p> 
					<h3><?php _e("Cons",'kimili-flash-embed'); ?></h3> 
					<p><?php _e("The embedding of Flash content relies on JavaScript, so if you have the Flash plug-in installed, but have JavaScript disabled or use a browser that doesn't support JavaScript, you will not be able to see your Flash content, however you will see alternative content instead. Flash content will also not be shown on a device like Sony PSP, which has very poor JavaScript support, and automated tools like RSS readers are not able to pick up Flash content.",'kimili-flash-embed'); ?></p> 
				</div> 
				<div class="col1"> 
					<label title="<?php _e("Flash version consists of major, minor and release version",'kimili-flash-embed'); ?>" class="info"><?php _e("Flash Version",'kimili-flash-embed'); ?></label>: <span class="req">*</span> 
				</div> 
				<div class="col2"> 
					<input type="text" id="major" name="major" value="<?php echo get_option('kml_flashembed_version_major'); ?>" size="4" maxlength="2" /> 
					.
					<input type="text" id="minor" name="minor" value="<?php echo get_option('kml_flashembed_version_minor'); ?>" size="4" maxlength="4" /> 
					.
					<input type="text" id="release" name="release" value="<?php echo get_option('kml_flashembed_version_revision'); ?>" size="4" maxlength="4" /> 
				</div> 
				<div class="clear">&nbsp;</div> 
				<div class="col1"> 
					<label for="expressInstall" title="<?php _e("Check checkbox to activate express install functionality on your swf.",'kimili-flash-embed'); ?>" class="info"><?php _e("Adobe Express Install",'kimili-flash-embed'); ?>:</label> 
				</div> 
				<div class="col2"> 
					<input type="checkbox" id="expressInstall" name="useexpressinstall" value="true" <?php if (get_option('kml_flashembed_use_express_install')) echo "checked=\"checked\""; ?> />
				</div> 
				<div class="clear">&nbsp;</div> 
				<div id="toggleReplaceId"> 
					<div class="col1"> 
						<label for="replaceId"><?php _e("HTML container ID",'kimili-flash-embed'); ?>:</label>
					</div> 
					<div class="col2"> 
						<input type="text" id="replaceId" name="replaceId" value="" size="20" /> 
						<a id="toggleReplaceIdHelp" href="#"><?php _e("what is this?",'kimili-flash-embed'); ?></a> 
					</div> 
					<div id="replaceIdHelp" class="help"> 
						<p><?php _e("Specifies the id attribute of the HTML container element that will be replaced with Flash content if enough JavaScript and Flash support is available.",'kimili-flash-embed'); ?></p> 
						<p><?php _e("This HTML container will be generated automatically and will embed your alternative HTML content as defined in the HTML section.",'kimili-flash-embed'); ?></p> 
						<p><?php _e("If you don't define an ID here, KFE will randomly generate an ID for you.",'kimili-flash-embed'); ?></p>
					</div> 
					<div class="clear">&nbsp;</div> 
				</div> 
			</div> 
		</fieldset> 
		<fieldset> 
			<legend><?php _e("SWF definition",'kimili-flash-embed'); ?> [ <a id="toggle2" href="#">-</a> ]</legend> 
			<div id="toggleable2"> 
				<div class="col1"> 
					<label for="swf" title="<?php _e("The relative or absolute path to your Flash content .swf file",'kimili-flash-embed'); ?>" class="info"><?php _e("Flash (.swf)",'kimili-flash-embed'); ?>:</label> <span class="req">*</span> 
				</div> 
				<div class="col2"> 
					<input type="text" id="swf" name="movie" value="<?php echo get_option('kml_flashembed_filename'); ?>" size="20" /> 
				</div> 
				<div class="clear">&nbsp;</div> 
				<div class="col1"> 
					<label title="<?php _e("Width &times; height (unit)",'kimili-flash-embed'); ?>" class="info"><?php _e("Dimensions",'kimili-flash-embed'); ?>:</label> <span class="req">*</span> 
				</div> 
				<div class="col2"> 
					<input type="text" id="width" name="width" value="<?php echo get_option('kml_flashembed_width'); ?>" size="5" maxlength="5" /> 
					&times;
					<input type="text" id="height" name="height" value="<?php echo get_option('kml_flashembed_height'); ?>" size="5" maxlength="5" /> 
					<select id="unit" name="unit"> 
						<option <?php if (get_option('kml_flashembed_dimensions_unit') == "pixels") echo "selected=\"selected\""; ?>  value="pixels"><?php _e("pixels",'kimili-flash-embed'); ?></option> 
						<option <?php if (get_option('kml_flashembed_dimensions_unit') == "percentage") echo "selected=\"selected\""; ?>  value="percentage"><?php _e("percentage",'kimili-flash-embed'); ?></option> 
					</select> 
				</div> 
				<div class="clear">&nbsp;</div> 
				<div id="toggleAttsParamsContainer">			
					<div class="col1"><label class="info" title="<?php _e("HTML object element attributes",'kimili-flash-embed'); ?>"><?php _e("Attributes",'kimili-flash-embed'); ?>:</label></div>
					<div class="col3">	
						<label for="attId" class="info" title="<?php _e("Uniquely identifies the Flash movie so that it can be referenced using a scripting language or by CSS",'kimili-flash-embed'); ?>"><?php _e("Flash content ID",'kimili-flash-embed'); ?></label>
					</div> 
					<div class="col4"> 
						<input type="text" id="attId" name="fid" value="<?php echo get_option('kml_flashembed_flash_id'); ?>" size="15" /> 
					</div> 
					<div class="clear">&nbsp;</div>
					<div class="col1">&nbsp;</div>
					<div class="col3"> 
						<label for="attClass" class="info" title="<?php _e("Classifies the Flash movie so that it can be referenced using a scripting language or by CSS",'kimili-flash-embed'); ?>">class</label> 
					</div> 
					<div class="col4"> 
						<input type="text" id="attClass" name="targetclass" value="<?php echo get_option('kml_flashembed_target_class'); ?>" size="15" /> 
					</div> 
					<div class="clear">&nbsp;</div> 
					<div class="col1">&nbsp;</div> 
					<div class="col3"> 
						<label for="align" class="info" title="<?php _e("HTML alignment of the object element. If this attribute is omitted, it by default centers the movie and crops edges if the browser window is smaller than the movie. NOTE: Using this attribute is not valid in XHTML 1.0 Strict.",'kimili-flash-embed'); ?>">align</label> 
					</div> 
					<div class="col4"> 
						<select id="align" name="align"> 
							<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option>
							<option <?php if (get_option('kml_flashembed_align') == "left") echo "selected=\"selected\""; ?> value="left">left</option> 
							<option <?php if (get_option('kml_flashembed_align') == "right") echo "selected=\"selected\""; ?> value="right">right</option> 
							<option <?php if (get_option('kml_flashembed_align') == "top") echo "selected=\"selected\""; ?> value="top">top</option> 
							<option <?php if (get_option('kml_flashembed_align') == "bottom") echo "selected=\"selected\""; ?> value="bottom">bottom</option> 
						</select> 
					</div> 
					<div class="clear">&nbsp;</div> 
					<div class="col1"> 
						<label class="info" title="<?php _e("HTML object element nested param elements",'kimili-flash-embed'); ?>"><?php _e("Parameters",'kimili-flash-embed'); ?>:</label> 
					</div> 
					<div class="col3"> 
						<label for="play" class="info" title="<?php _e("Specifies whether the movie begins playing immediately on loading in the browser. The default value is true if this attribute is omitted.",'kimili-flash-embed'); ?>">play</label> 
					</div> 
					<div class="col4"> 
						<select id="play" name="play"> 
							<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option> 
							<option <?php if (get_option('kml_flashembed_play') == "true") echo "selected=\"selected\""; ?> value="true">true</option> 
							<option <?php if (get_option('kml_flashembed_play') == "false") echo "selected=\"selected\""; ?> value="false">false</option> 
						</select> 
					</div> 
					<div class="col3"> 
						<label for="loop" class="info" title="<?php _e("Specifies whether the movie repeats indefinitely or stops when it reaches the last frame. The default value is true if this attribute is omitted",'kimili-flash-embed'); ?>.">loop</label> 
					</div> 
					<div class="col4"> 
						<select id="loop" name="loop"> 
							<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option> 
							<option <?php if (get_option('kml_flashembed_loop') == "true") echo "selected=\"selected\""; ?> value="true">true</option> 
							<option <?php if (get_option('kml_flashembed_loop') == "false") echo "selected=\"selected\""; ?> value="false">false</option> 
						</select> 
					</div> 
					<div class="clear">&nbsp;</div> 
					<div class="col1">&nbsp;</div> 
					<div class="col3"> 
						<label for="menu" class="info" title="<?php _e("Shows a shortcut menu when users right-click (Windows) or control-click (Macintosh) the SWF file. To show only About Flash in the shortcut menu, deselect this option. By default, this option is set to true.",'kimili-flash-embed'); ?>">menu</label> 
					</div> 
					<div class="col4"> 
						<select id="menu" name="menu"> 
							<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option> 
							<option <?php if (get_option('kml_flashembed_menu') == "true") echo "selected=\"selected\""; ?> value="true">true</option> 
							<option <?php if (get_option('kml_flashembed_menu') == "false") echo "selected=\"selected\""; ?> value="false">false</option> 
						</select> 
					</div> 
					<div class="col3"> 
						<label for="quality" class="info" title="<?php _e("Specifies the trade-off between processing time and appearance. The default value is 'high' if this attribute is omitted.",'kimili-flash-embed'); ?>">quality</label> 
					</div> 
					<div class="col4"> 
						<select id="quality" name="quality"> 
							<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option> 
							<option <?php if (get_option('kml_flashembed_quality') == "best") echo "selected=\"selected\""; ?> value="best">best</option> 
							<option <?php if (get_option('kml_flashembed_quality') == "high") echo "selected=\"selected\""; ?> value="high">high</option> 
							<option <?php if (get_option('kml_flashembed_quality') == "medium") echo "selected=\"selected\""; ?> value="medium">medium</option> 
							<option <?php if (get_option('kml_flashembed_quality') == "autohigh") echo "selected=\"selected\""; ?> value="autohigh">autohigh</option> 
							<option <?php if (get_option('kml_flashembed_quality') == "autolow") echo "selected=\"selected\""; ?> value="autolow">autolow</option> 
							<option <?php if (get_option('kml_flashembed_quality') == "low") echo "selected=\"selected\""; ?> value="low">low</option> 
						</select> 
					</div> 
					<div class="clear">&nbsp;</div> 
					<div class="col1">&nbsp;</div> 
					<div class="col3"> 
						<label for="scale" class="info" title="<?php _e("Specifies scaling, aspect ratio, borders, distortion and cropping for if you have changed the document's original width and height.",'kimili-flash-embed'); ?>">scale</label> 
					</div> 
					<div class="col4"> 
						<select id="scale" name="scale"> 
							<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option> 
							<option <?php if (get_option('kml_flashembed_scale') == "showall") echo "selected=\"selected\""; ?> value="showall">showall</option> 
							<option <?php if (get_option('kml_flashembed_scale') == "noborder") echo "selected=\"selected\""; ?> value="noborder">noborder</option> 
							<option <?php if (get_option('kml_flashembed_scale') == "exactfit") echo "selected=\"selected\""; ?> value="exactfit">exactfit</option> 
							<option <?php if (get_option('kml_flashembed_scale') == "noscale") echo "selected=\"selected\""; ?> value="noscale">noscale</option> 
						</select> 
					</div> 
					<div class="col3"> 
						<label for="salign" class="info" title="<?php _e("Specifies where the content is placed within the application window and how it is cropped.",'kimili-flash-embed'); ?>">salign</label> 
					</div> 
					<div class="col4"> 
						<select id="salign" name="salign"> 
							<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option> 
							<option <?php if (get_option('kml_flashembed_salign') == "tl") echo "selected=\"selected\""; ?> value="tl">tl</option> 
							<option <?php if (get_option('kml_flashembed_salign') == "tr") echo "selected=\"selected\""; ?> value="tr">tr</option> 
							<option <?php if (get_option('kml_flashembed_salign') == "bl") echo "selected=\"selected\""; ?> value="bl">bl</option> 
							<option <?php if (get_option('kml_flashembed_salign') == "br") echo "selected=\"selected\""; ?> value="br">br</option> 
							<option <?php if (get_option('kml_flashembed_salign') == "l") echo "selected=\"selected\""; ?> value="l">l</option> 
							<option <?php if (get_option('kml_flashembed_salign') == "t") echo "selected=\"selected\""; ?> value="t">t</option> 
							<option <?php if (get_option('kml_flashembed_salign') == "r") echo "selected=\"selected\""; ?> value="r">r</option> 
							<option <?php if (get_option('kml_flashembed_salign') == "b") echo "selected=\"selected\""; ?> value="b">b</option> 
						</select> 
					</div> 
					<div class="clear">&nbsp;</div> 
					<div class="col1">&nbsp;</div> 
					<div class="col3"> 
						<label for="wmode" class="info" title="<?php _e("Sets the Window Mode property of the Flash movie for transparency, layering, and positioning in the browser. The default value is 'window' if this attribute is omitted.",'kimili-flash-embed'); ?>">wmode</label> 
					</div> 
					<div class="col4"> 
						<select id="wmode" name="wmode"> 
							<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option> 
							<option <?php if (get_option('kml_flashembed_wmode') == "window") echo "selected=\"selected\""; ?> value="window">window</option> 
							<option <?php if (get_option('kml_flashembed_wmode') == "opaque") echo "selected=\"selected\""; ?> value="opaque">opaque</option> 
							<option <?php if (get_option('kml_flashembed_wmode') == "transparent") echo "selected=\"selected\""; ?> value="transparent">transparent</option> 
							<option <?php if (get_option('kml_flashembed_wmode') == "direct") echo "selected=\"selected\""; ?> value="direct">direct</option> 
							<option <?php if (get_option('kml_flashembed_wmode') == "gpu") echo "selected=\"selected\""; ?> value="gpu">gpu</option>
						</select> 
					</div> 
					<div class="col3"> 
						<label for="bgcolor" class="info" title="<?php _e("Hexadecimal RGB value in the format #RRGGBB, which specifies the background color of the movie, which will override the background color setting specified in the Flash file.",'kimili-flash-embed'); ?>">bgcolor</label> 
					</div> 
					<div class="col4"> 
						<input type="text" id="bgcolor" name="bgcolor" value="<?php echo get_option('kml_flashembed_bgcolor'); ?>" size="15" maxlength="7" /> 
					</div> 
					<div class="clear">&nbsp;</div> 
					<div class="col1">&nbsp;</div> 
					<div class="col3"> 
						<label for="devicefont" class="info" title="<?php _e("Specifies whether static text objects that the Device Font option has not been selected for will be drawn using device fonts anyway, if the necessary fonts are available from the operating system.",'kimili-flash-embed'); ?>">devicefont</label> 
					</div> 
					<div class="col4"> 
						<select id="devicefont" name="devicefont"> 
							<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option> 
							<option <?php if (get_option('kml_flashembed_devicefont') == "true") echo "selected=\"selected\""; ?> value="true">true</option> 
							<option <?php if (get_option('kml_flashembed_devicefont') == "false") echo "selected=\"selected\""; ?> value="false">false</option> 
						</select> 
					</div> 
					<div class="col3"> 
						<label for="seamlesstabbing" class="info" title="<?php _e("Specifies whether users are allowed to use the Tab key to move keyboard focus out of a Flash movie and into the surrounding HTML (or the browser, if there is nothing focusable in the HTML following the Flash movie). The default value is true if this attribute is omitted.",'kimili-flash-embed'); ?>">seamlesstabbing</label> 
					</div> 
					<div class="col4"> 
						<select id="seamlesstabbing" name="seamlesstabbing"> 
							<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option> 
							<option <?php if (get_option('kml_flashembed_seamlesstabbing') == "true") echo "selected=\"selected\""; ?> value="true">true</option> 
							<option <?php if (get_option('kml_flashembed_seamlesstabbing') == "false") echo "selected=\"selected\""; ?> value="false">false</option> 
						</select> 
					</div> 
					<div class="clear">&nbsp;</div> 
					<div class="col1">&nbsp;</div> 
					<div class="col3"> 
						<label for="swliveconnect" class="info" title="<?php _e("Specifies whether the browser should start Java when loading the Flash Player for the first time. The default value is false if this attribute is omitted. If you use JavaScript and Flash on the same page, Java must be running for the FSCommand to work.",'kimili-flash-embed'); ?>">swliveconnect</label> 
					</div> 
					<div class="col4"> 
						<select id="swliveconnect" name="swliveconnect"> 
							<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option> 
							<option <?php if (get_option('kml_flashembed_swliveconnect') == "true") echo "selected=\"selected\""; ?> value="true">true</option> 
							<option <?php if (get_option('kml_flashembed_swliveconnect') == "false") echo "selected=\"selected\""; ?> value="false">false</option> 
						</select> 
					</div> 
					<div class="col3"> 
						<label for="allowfullscreen" class="info" title="<?php _e("Enables full-screen mode. The default value is false if this attribute is omitted. You must have version 9,0,28,0 or greater of Flash Player installed to use full-screen mode.",'kimili-flash-embed'); ?>">allowfullscreen</label> 
					</div> 
					<div class="col4"> 
						<select id="allowfullscreen" name="allowfullscreen"> 
							<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option> 
							<option <?php if (get_option('kml_flashembed_allowfullscreen') == "true") echo "selected=\"selected\""; ?> value="true">true</option> 
							<option <?php if (get_option('kml_flashembed_allowfullscreen') == "false") echo "selected=\"selected\""; ?> value="false">false</option> 
						</select> 
					</div> 
					<div class="clear">&nbsp;</div> 
					<div class="col1">&nbsp;</div> 
					<div class="col3"> 
						<label for="allowscriptaccess" class="info" title="<?php _e("Controls the ability to perform outbound scripting from within a Flash SWF. The default value is 'always' if this attribute is omitted.",'kimili-flash-embed'); ?>">allowscriptaccess</label> 
					</div> 
					<div class="col4"> 
						<select id="allowscriptaccess" name="allowscriptaccess"> 
							<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option> 
							<option <?php if (get_option('kml_flashembed_allowscriptaccess') == "always") echo "selected=\"selected\""; ?> value="always">always</option> 
							<option <?php if (get_option('kml_flashembed_allowscriptaccess') == "sameDomain") echo "selected=\"selected\""; ?> value="sameDomain">sameDomain</option> 
							<option <?php if (get_option('kml_flashembed_allowscriptaccess') == "never") echo "selected=\"selected\""; ?> value="never">never</option> 
						</select> 
					</div> 
					<div class="col3"> 
						<label for="allownetworking" class="info" title="<?php _e("Controls a SWF file's access to network functionality. The default value is 'all' if this attribute is omitted.",'kimili-flash-embed'); ?>">allownetworking</label> 
					</div> 
					<div class="col4"> 
						<select id="allownetworking" name="allownetworking"> 
							<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option> 
							<option <?php if (get_option('kml_flashembed_allownetworking') == "all") echo "selected=\"selected\""; ?> value="all">all</option> 
							<option <?php if (get_option('kml_flashembed_allownetworking') == "internal") echo "selected=\"selected\""; ?> value="internal">internal</option> 
							<option <?php if (get_option('kml_flashembed_allownetworking') == "none") echo "selected=\"selected\""; ?> value="none">none</option>
						</select> 
					</div> 
					<div class="clear">&nbsp;</div> 
					<div class="col1">&nbsp;</div> 
					<div class="col3"> 
						<label for="base" class="info" title="<?php _e("Specifies the base directory or URL used to resolve all relative path statements in the Flash Player movie. This attribute is helpful when your Flash Player movies are kept in a different directory from your other files.",'kimili-flash-embed'); ?>">base</label> 
					</div> 
					<div class="col5"> 
						<input type="text" id="base" name="base" value="<?php echo get_option('kml_flashembed_base'); ?>" size="15" /> 
					</div> 
					<div class="clear">&nbsp;</div> 
					<div class="col1"> 
						<label class="info" title="<?php _e("Method to pass variables to a Flash movie. You need to separate individual name/variable pairs with a semicolon (i.e. name=John Doe ; count=3).",'kimili-flash-embed'); ?>">fvars:</label>
					</div> 
					<div class="col2"> 
						<textarea name="fvars" id="fvars" rows="4" cols="40"><?php echo stripcslashes(get_option('kml_flashembed_fvars')); ?></textarea>
					</div> 
				</div>				
				<div class="clear">&nbsp;</div> 
				<div class="col1"><a id="toggleAttsParams" href="#"><?php _e("more",'kimili-flash-embed'); ?></a></div> 
				<div class="clear">&nbsp;</div> 
			</div> 
		</fieldset> 
		<fieldset>
			<legend><?php _e("Alternative Content",'kimili-flash-embed'); ?> [ <a id="toggle3" href="#">-</a> ]</legend>
			<div id="toggleable3">
				<div class="col2">
					<label for="alternativeContent"><?php _e("Alternative content",'kimili-flash-embed'); ?>:</label>
					<a id="toggleAlternativeContentHelp" href="#alternativeContentHelp"><?php _e("what is this",'kimili-flash-embed'); ?>?</a>
				</div>
				<div id="alternativeContentHelp" class="help">
					<p>
						<?php _e("The object element allows you to nest alternative HTML content inside of it, which will be displayed if Flash is not installed or supported. 
						This content will also be picked up by search engines, making it a great tool for creating search-engine-friendly content.",'kimili-flash-embed'); ?>
					</p>
					<p><?php _e("Summarized, you should use alternative content for the following:",'kimili-flash-embed'); ?></p>
					<ul>
						<li><?php _e("When you like to create content that is accessible for people who browse the Web without plugins",'kimili-flash-embed'); ?></li>
						<li><?php _e("When you like to create search-engine-friendly content",'kimili-flash-embed'); ?></li>
						<li><?php _e("To tell visitors that they can have a richer user experience by downloading the Flash plugin",'kimili-flash-embed'); ?></li>
					</ul>
				</div>
				<div class="clear"> </div>
				<div class="col2">
					<textarea id="alternativeContent" name="alternativeContent" rows="6" cols="10"><?php echo stripcslashes(get_option('kml_flashembed_alt_content')); ?></textarea>
				</div>
				<div class="clear"> </div>
			</div>
		</fieldset>
		<div class="col1"> 
			<input type="button" class="button" id="generate" name="generate" value="<?php _e("Generate",'kimili-flash-embed'); ?>" />
		</div> 
		
	</div>
	
	<script type="text/javascript" charset="utf-8">
		// <![CDATA[
		jQuery(document).ready(function(){
			try {
				Kimili.Flash.Generator.initialize();
				Kimili.Flash.Generator.i18n = {
					more : "<?php _e("more",'kimili-flash-embed'); ?>",
					less : "<?php _e("less",'kimili-flash-embed'); ?>"
				};
			} catch (e) {
				throw "<?php _e("Kimili is not defined. This generator isn't going to put a KFE tag in your code.",'kimili-flash-embed'); ?>";
			}
		});
		// ]]>
	</script>

</body>
</html>