<?php

/*
Plugin Name: Kimili Flash Embed
Plugin URI: http://www.kimili.com/plugins/kml_flashembed
Description: Provides a full Wordpress interface for <a href="http://code.google.com/p/swfobject/">SWFObject</a> - the best way to embed Flash on your site.
Version: 2.2
Author: Michael Bester
Author URI: http://www.kimili.com
Update: http://www.kimili.com/plugins/kml_flashembed/wp
*/

/*
*
*	KIMILI FLASH EMBED
*
*	Copyright 2010 Michael Bester (http://www.kimili.com)
*	Released under the GNU General Public License (http://www.gnu.org/licenses/gpl.html)
*
*/

/**
* 
*/
class KimiliFlashEmbed
{
	
	var $version = '2.2';
	var $staticSwfs = array();
	var $dynamicSwfs = array();
	
	function KimiliFlashEmbed()
	{
		// Register Hooks
		if (is_admin()) {
			
			// Load up the localization file if we're using WordPress in a different language
			// Place it in this plugin's "langs" folder and name it "kimili-flash-embed-[value in wp-config].mo"
			load_plugin_textdomain( 'kimili-flash-embed', FALSE, 'kimili-flash-embed/langs/');
			
			// Default Options
			add_option('kml_flashembed_filename', 'untitled.swf');
			add_option('kml_flashembed_target_class', 'flashmovie');
			add_option('kml_flashembed_publish_method', '0');
			add_option('kml_flashembed_version_major', '8');
			add_option('kml_flashembed_version_minor', '0');
			add_option('kml_flashembed_version_revision', '0');
			add_option('kml_flashembed_alt_content', '<p><a href="http://adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>');
			add_option('kml_flashembed_reference_swfobject', '1');
			add_option('kml_flashembed_swfobject_source', '0');
			add_option('kml_flashembed_width', '400');
			add_option('kml_flashembed_height', '300');
			
			// Set up the options page
			add_action('admin_menu', array(&$this, 'options_menu'));
			
			// Add Quicktag
			if (current_user_can('edit_posts') || current_user_can('edit_pages') ) {
				add_action( 'edit_form_advanced', array(&$this, 'add_quicktags') );
				add_action( 'edit_page_form', array(&$this, 'add_quicktags') );
			}

			// Queue Embed JS
			add_action( 'admin_head', array(&$this, 'set_admin_js_vars'));
			wp_enqueue_script( 'kimiliflashembed', plugins_url('/kimili-flash-embed/js/kfe.js'), array(), $this->version );
			
			
		} else {
			// Front-end
			if ($this->is_feed()) {
				$this->doObStart();
			} else {
				add_action('wp_head', array(&$this, 'disableAutohide'), 9);
				add_action('wp_head', array(&$this, 'doObStart'));
				add_action('wp_head', array(&$this, 'addScriptPlaceholder'));
				add_action('wp_footer', array(&$this, 'doObEnd'));
			}
			
		}
		
		// Queue SWFObject
		if ( get_option('kml_flashembed_reference_swfobject') == '1') {
			// Let's override WP's bundled swfobject, cause as of WP 2.9, it's still using 2.1 
			wp_deregister_script('swfobject');
			// and register our own.
			if ( get_option('kml_flashembed_swfobject_source') == '0' ) {
				wp_register_script( 'swfobject', 'http' . (is_ssl() ? 's' : '') . '://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js', array(), '2.2' );
			} else {
				wp_register_script( 'swfobject', plugins_url('/kimili-flash-embed/js/swfobject.js'), array(), '2.2' );
			}
			wp_enqueue_script('swfobject');
		}
	}
	
	function parseShortcodes($content)
	{
		$pattern = '/(<p>[\s\n\r]*)?\[(kml_(flash|swf)embed)\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?([\s\n\r]*<\/p>)?/s';
		$temp 	= preg_replace_callback($pattern, array(&$this, 'processShortcode'), $content);
		$result = preg_replace_callback('/KML_FLASHEMBED_PROCESS_SCRIPT_CALLS/s', array(&$this, 'scriptSwfs'), $temp);
		return $result;
	}
	
	// Thanks to WP shortcode API Code
	function processShortcode($code)
	{
		$r	= "";

		$atts = $this->parseAtts($code[4]);
		$altContent = isset($code[6]) ? $code[6] : '';

		$attpairs	= preg_split('/\|/', $elements, -1, PREG_SPLIT_NO_EMPTY);

		if (isset($atts['movie'])) {
			
			$atts['height']				= (isset($atts['height'])) ? $atts['height'] : get_option('kml_flashembed_height');
			$atts['width']				= (isset($atts['width'])) ? $atts['width'] : get_option('kml_flashembed_width');
			$atts['fversion']			= (isset($atts['fversion'])) ? $atts['fversion'] : get_option('kml_flashembed_version_major').'.'.get_option('kml_flashembed_version_minor').'.'.get_option('kml_flashembed_version_revision');
			$atts['targetclass']		= (isset($atts['targetclass'])) ? $atts['targetclass'] : get_option('kml_flashembed_target_class');
			$atts['publishmethod']		= (isset($atts['publishmethod'])) ? $atts['publishmethod'] : (get_option('kml_flashembed_publish_method') ? 'dynamic' : 'static');
			$atts['useexpressinstall']	= (isset($atts['useexpressinstall'])) ? $atts['useexpressinstall'] : 'false';
			$atts['xiswf']				= plugins_url('/kimili-flash-embed/lib/expressInstall.swf');
			
			$rand	= mt_rand();  // For making sure this instance is unique

			// Extract the filename minus the extension...
			$swfname	= (strrpos($atts['movie'], "/") === false) ?
									$atts['movie'] :
									substr($atts['movie'], strrpos($atts['movie'], "/") + 1, strlen($atts['movie']));
			$swfname	= (strrpos($swfname, ".") === false) ?
									$swfname :
									substr($swfname, 0, strrpos($swfname, "."));
									
			// set an ID for the movie if necessary
			if (!isset($atts['fid'])) {
				// ... to use as a default ID if an ID is not defined.
				$atts['fid']	= "fm_" . $swfname . "_" . $rand;
			}
			
			if (!isset($atts['target'])) {
				// ... and a target ID if need be for the dynamic publishing method
				$atts['target']	= "so_targ_" . $swfname . "_" . $rand;
			}

			// Parse out the fvars
			if (isset($atts['fvars'])) {
				$fvarpair_regex		= "/(?<!([$|\?]\{))\s*;\s*(?!\})/";
				// Untexturize ampersands.
				$atts['fvars']		= preg_replace('/&amp;/', '&', $atts['fvars']);
				$atts['fvars']		= preg_split($fvarpair_regex, $atts['fvars'], -1, PREG_SPLIT_NO_EMPTY);
			}

			// Convert any quasi-HTML in alttext back into tags
			$atts['alttext']		= (isset($atts['alttext'])) ? preg_replace("/{(.*?)}/i", "<$1>", $atts['alttext']) : $altContent;
			
			// Strip leading </p> and trailing <p> - detritius from the way the tags are parsed out of the RTE
			$patterns = array(
				"/^[\s\n\r]*<\/p>/i",
				"/<p>[\s\n\r]*$/i"
			);
			$atts['alttext'] = preg_replace($patterns, "", $atts['alttext']);

			// If we're not serving up a feed, generate the script tags
			if (is_feed()) {
				$r	= $this->buildObjectTag($atts);
			} else {
				if ($atts['publishmethod'] == 'static') {
					$r = $this->publishStatic($atts);
				} else {
					$r = $this->publishDynamic($atts);
				}
			}
		}
		
	 	return $r;
	}
	
	// Thanks to WP shortcode API Code
	function parseAtts($text)
	{
		$atts = array();
		$pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
		$text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
		if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
			foreach ($match as $m) {
				if (!empty($m[1]))
					$atts[strtolower($m[1])] = stripcslashes($m[2]);
				elseif (!empty($m[3]))
					$atts[strtolower($m[3])] = stripcslashes($m[4]);
				elseif (!empty($m[5]))
					$atts[strtolower($m[5])] = stripcslashes($m[6]);
				elseif (isset($m[7]) and strlen($m[7]))
					$atts[] = stripcslashes($m[7]);
				elseif (isset($m[8]))
					$atts[] = stripcslashes($m[8]);
			}
		} else {
			$atts = ltrim($text);
		}
		return $atts;
	}
	
	function publishStatic($atts)
	{
		if (is_array($atts)) {
			extract($atts);
		}
		
		$this->staticSwfs[] = array(
			'id'					=> $fid,
			'version'				=> $fversion,
			'useexpressinstall'		=> $useexpressinstall,
			'xiswf'					=> $xiswf
		);
		
		return $this->buildObjectTag($atts);
	}
	
	function publishDynamic($atts)
	{
		if (is_array($atts)) {
			extract($atts);
		}
		
		$this->dynamicSwfs[] = $atts;
		
		$out = array();
		
		$out[]		= '<div id="' . $target . '" class="' . $targetclass . '">'.$alttext.'</div>';
		
		return join("\n", $out);
	}
	
	function addScriptPlaceholder()
	{
		echo 'KML_FLASHEMBED_PROCESS_SCRIPT_CALLS';
	}
	
	function disableAutohide()
	{
		// If we want to use autohide, or we don't have any swfs on the page, drop out.
		if (get_option('kml_flashembed_swfobject_use_autohide')) {
			return false;
		}
		
		// Otherwise build out the script.
		$out = array();
		
		$out[]	= '';
		$out[]	= '<script type="text/javascript" charset="utf-8">';
		$out[]	= '';
		$out[]	= '	/**';
		$out[]	= '	 * Courtesy of Kimili Flash Embed - Version ' . $this->version;
		$out[]	= '	 * by Michael Bester - http://kimili.com';
		$out[]	= '	 */';
		$out[]	= '';
		$out[]	= '	(function(){';
		$out[]	= '		try {';
		$out[]	= '			// Disabling SWFObject\'s Autohide feature';
		$out[]	= '			if (typeof swfobject.switchOffAutoHideShow === "function") {';
		$out[]	= '				swfobject.switchOffAutoHideShow();';
		$out[]	= '			}';
		$out[]	= '		} catch(e) {}';
		$out[]	= '	})();';
		$out[]	= '</script>';
		$out[]	= '';
		
		echo join("\n", $out);
	}
	
	function scriptSwfs()
	{
		// If we don't have any swfs on the page, drop out.
		if (count($this->staticSwfs) == 0 && count($this->dynamicSwfs) == 0) {
			return '';
		}
		
		// Otherwise build out the script.
		$out = array();	
		
		$out[]		= '';
		$out[]		= '<script type="text/javascript" charset="utf-8">';
		$out[]		= '';
		$out[]		= '	/**';
		$out[]		= '	 * Courtesy of Kimili Flash Embed - Version ' . $this->version;
		$out[]		= '	 * by Michael Bester - http://kimili.com';
		$out[]		= '	 */';
		$out[]		= '';
		$out[]		= '	(function(){';
		$out[]		= '		try {';
		if (count($this->staticSwfs) > 0) {
			$out[]	= '			// Registering Statically Published SWFs';
		}
		
		for ($i = 0; $i < count($this->staticSwfs); $i++) {
			$curr	= $this->staticSwfs[$i];
			$out[]	= '			swfobject.registerObject("' . $curr['id'] . '","' . $curr['version'] . '"'.(($curr['useexpressinstall'] == 'true') ? ',"'.$curr['xiswf'].'"' : '') . ');';
		}
		
		if (count($this->dynamicSwfs) > 0) {
			$out[]		= '';
			$out[]	= '			// Registering Dynamically Published SWFs';
		}
		for ($i = 0; $i < count($this->dynamicSwfs); $i++) {
			
			$curr		= $this->dynamicSwfs[$i];
			
			// Flashvars
			$flashvars	= $this->parseFvars($curr['fvars'],'object');
			
			// Parameters
			$params = array();			
			if (isset($curr['play']))				$params[] = '"play" : "' . $curr['play'] . '"';
			if (isset($curr['loop']))				$params[] = '"loop" : "' . $curr['loop'] . '"';
			if (isset($curr['menu'])) 				$params[] = '"menu" : "' . $curr['menu'] . '"';
			if (isset($curr['quality']))			$params[] = '"quality" : "' . $curr['quality'] . '"';
			if (isset($curr['scale'])) 				$params[] = '"scale" : "' . $curr['scale'] . '"';
			if (isset($curr['salign'])) 			$params[] = '"salign" : "' . $curr['salign'] . '"';
			if (isset($curr['wmode'])) 				$params[] = '"wmode" : "' . $curr['wmode'] . '"';
			if (isset($curr['bgcolor'])) 			$params[] = '"bgcolor" : "' . $curr['bgcolor'] . '"';
			if (isset($curr['base'])) 	   		 	$params[] = '"base" : "' . $curr['base'] . '"';
			if (isset($curr['swliveconnect']))		$params[] = '"swliveconnect" : "' . $curr['swliveconnect'] . '"';
			if (isset($curr['devicefont']))			$params[] = '"devicefont" : "' . $curr['devicefont'] . '"';
			if (isset($curr['allowscriptaccess']))	$params[] = '"allowscriptaccess" : "' . $curr['allowscriptaccess'] . '"';
			if (isset($curr['seamlesstabbing']))	$params[] = '"seamlesstabbing" : "' . $curr['seamlesstabbing'] . '"';
			if (isset($curr['allowfullscreen']))	$params[] = '"allowfullscreen" : "' . $curr['allowfullscreen'] . '"';
			if (isset($curr['allownetworking']))	$params[] = '"allownetworking" : "' . $curr['allownetworking'] . '"';
			
			// Attributes
			$attributes = array();
			if (isset($curr['align'])) 			$attributes[] = '"align" : "' . $curr['align'] . '"';  
			if (isset($curr['fid'])) 			$attributes[] = '"id" : "' . $curr['fid'] . '"';  
			if (isset($curr['fid'])) 	   		$attributes[] = '"name" : "' . $curr['fid'] . '"';
			if (isset($curr['targetclass']))	$attributes[] = '"styleclass" : "' . $curr['targetclass'] . '"';
			
			$out[]		= '			swfobject.embedSWF("'.$curr['movie'].'","'.$curr['target'].'","'.$curr['width'].'","'.$curr['height'].'","'.$curr['fversion'].'","'.(($curr['useexpressinstall'] == 'true') ? $curr['xiswf'] : '').'",{';
			for ($j = 0; $j < count($flashvars); $j++) {
				$out[]	= '				'.$flashvars[$j].(($j < count($flashvars) - 1) ? ',' : '');
			}
			$out[]	= '			},{';
			for ($j = 0; $j < count($params); $j++) {
				$out[]	= '				'.$params[$j].(($j < count($params) - 1) ? ',' : '');
			}
			$out[] = '			},{';
			for ($j = 0; $j < count($attributes); $j++) {
				$out[]	= '				'.$attributes[$j].(($j < count($attributes) - 1) ? ',' : '');
			}
			$out[] = '			});';
		}
		
		$out[]		= '		} catch(e) {}';
		$out[]		= '	})();';
		$out[]		= '</script>';
		$out[]		= '';
		
		return join("\n", $out);
	}
	
	function buildObjectTag($atts)
	{
		$out	= array();	
		if (is_array($atts)) {
			extract($atts);
		}

		// Build a query string based on the $fvars attribute
		$querystring = join("&amp;", $this->parseFvars($fvars));
		
										$out[] = '';    
										$out[] = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"';
		if (isset($fid))				$out[] = '			id="'.$fid.'"';
		if (isset($align)) 				$out[] = '			align="'.$align.'"';
										$out[] = '			class="'.$targetclass.'"';
										$out[] = '			width="'.$width.'"';
										$out[] = '			height="'.$height.'">';
										$out[] = '	<param name="movie" value="' . $movie . '" />';
		if (count($fvars) > 0)			$out[] = '	<param name="flashvars" value="' . $querystring . '" />';
		if (isset($play))				$out[] = '	<param name="play" value="' . $play . '" />';
		if (isset($loop))				$out[] = '	<param name="loop" value="' . $loop . '" />';
		if (isset($menu)) 				$out[] = '	<param name="menu" value="' . $menu . '" />';
		if (isset($quality))			$out[] = '	<param name="quality" value="' . $quality . '" />';
		if (isset($scale)) 				$out[] = '	<param name="scale" value="' . $scale . '" />';
		if (isset($salign)) 			$out[] = '	<param name="salign" value="' . $salign . '" />';
		if (isset($wmode)) 				$out[] = '	<param name="wmode" value="' . $wmode . '" />';
		if (isset($bgcolor)) 			$out[] = '	<param name="bgcolor" value="' . $bgcolor . '" />';
		if (isset($base)) 	   		 	$out[] = '	<param name="base" value="' . $base . '" />';
		if (isset($swliveconnect))		$out[] = '	<param name="swliveconnect" value="' . $swliveconnect . '" />';
		if (isset($devicefont))			$out[] = '	<param name="devicefont" value="' . $devicefont . '" />';
		if (isset($allowscriptaccess))	$out[] = '	<param name="allowscriptaccess" value="' . $allowscriptaccess . '" />';
		if (isset($seamlesstabbing))	$out[] = '	<param name="seamlesstabbing" value="' . $seamlesstabbing . '" />';
		if (isset($allowfullscreen))	$out[] = '	<param name="allowfullscreen" value="' . $allowfullscreen . '" />';
		if (isset($allownetworking))	$out[] = '	<param name="allownetworking" value="' . $allownetworking . '" />';
										$out[] = '	<!--[if !IE]>-->';
										$out[] = '	<object	type="application/x-shockwave-flash"';
										$out[] = '			data="'.$movie.'"'; 
		if (isset($fid))				$out[] = '			name="'.$fid.'"';
		if (isset($align)) 				$out[] = '			align="'.$align.'"';
										$out[] = '			width="'.$width.'"';
										$out[] = '			height="'.$height.'">';
		if (count($fvars) > 0)			$out[] = '		<param name="flashvars" value="' . $querystring . '" />';
		if (isset($play))				$out[] = '		<param name="play" value="' . $play . '" />';
		if (isset($loop))				$out[] = '		<param name="loop" value="' . $loop . '" />';
		if (isset($menu)) 				$out[] = '		<param name="menu" value="' . $menu . '" />';
		if (isset($quality))			$out[] = '		<param name="quality" value="' . $quality . '" />';
		if (isset($scale)) 				$out[] = '		<param name="scale" value="' . $scale . '" />';
		if (isset($salign)) 			$out[] = '		<param name="salign" value="' . $salign . '" />';
		if (isset($wmode)) 				$out[] = '		<param name="wmode" value="' . $wmode . '" />';
		if (isset($bgcolor)) 			$out[] = '		<param name="bgcolor" value="' . $bgcolor . '" />';
		if (isset($base)) 	   		 	$out[] = '		<param name="base" value="' . $base . '" />';
		if (isset($swliveconnect))		$out[] = '		<param name="swliveconnect" value="' . $swliveconnect . '" />';
		if (isset($devicefont))			$out[] = '		<param name="devicefont" value="' . $devicefont . '" />';
		if (isset($allowscriptaccess))	$out[] = '		<param name="allowscriptaccess" value="' . $allowscriptaccess . '" />';
		if (isset($seamlesstabbing))	$out[] = '		<param name="seamlesstabbing" value="' . $seamlesstabbing . '" />';
		if (isset($allowfullscreen))	$out[] = '		<param name="allowfullscreen" value="' . $allowfullscreen . '" />';
		if (isset($allownetworking))	$out[] = '		<param name="allownetworking" value="' . $allownetworking . '" />';
										$out[] = '	<!--<![endif]-->';
		if (isset($alttext))			$out[] = '		'.$alttext;
										$out[] = '	<!--[if !IE]>-->';
							  	  		$out[] = '	</object>';
										$out[] = '	<!--<![endif]-->';
		 								$out[] = '</object>';     

		$ret .= join("\n", $out);
		return $ret;
	}
	
	function parseFvars($fvars, $format='string')
	{
		$ret = array();
		
		for ($i = 0; $i < count($fvars); $i++) {
			$thispair	= trim($fvars[$i]);
			$nvpair		= explode("=",$thispair);
			$name		= trim($nvpair[0]);
			$value		= "";
			for ($j = 1; $j < count($nvpair); $j++) {			// In case someone passes in a fvars with additional "="
				$value		.= trim($nvpair[$j]);
				$value		= preg_replace('/&#038;/', '&', $value);
				if ((count($nvpair) - 1) != $j) {
					$value	.= "=";
				}
			}
			// Prune out JS or PHP values
			if (preg_match("/^\\$\\{.*\\}/i", $value)) { 		// JS
				$endtrim 	= strlen($value) - 3;
				$value		= substr($value, 2, $endtrim);
				$value		= str_replace(';', '', $value);
			} else if (preg_match("/^\\?\\{.*\\}/i", $value)) {	// PHP
				$endtrim 	= strlen($value) - 3;
				$value 		= substr($value, 2, $endtrim);
				$value 		= eval("return " . $value);
			}
			
			if ($format == 'string') {
				$ret[] = $name . '=' . $value;
			} else {
				$ret[] = $name . ' : "' . $value . '"';
			}
		}

		return $ret;
		
	}
	
	function doObStart()
	{
		ob_start(array(&$this, 'parseShortcodes'));
	}
	
	function doObEnd()
	{
		// Check the output buffer
		if (function_exists('ob_list_handlers')) {
			$active_handlers = ob_list_handlers();
		} else {
			$active_handlers = array();
		}
		if (sizeof($active_handlers) > 0 &&
			strtolower($active_handlers[sizeof($active_handlers) - 1]) ==
			strtolower('KimiliFlashEmbed::parseShortcodes')) {
			ob_end_flush();
		}
	}
	
	function is_feed()
	{
		return preg_match("/(\/\?feed=|\/feed)/i",$_SERVER['REQUEST_URI']);
	}

	function set_admin_js_vars()
	{
?>
<script type="text/javascript" charset="utf-8">
// <![CDATA[
	if (typeof Kimili !== 'undefined' && typeof Kimili.Flash !== 'undefined') {
		Kimili.Flash.configUrl = "<?php echo plugins_url('/kimili-flash-embed/admin/config.php'); ?>";
	}
// ]]>	
</script>
<?php
	}
	
	// Add a button to the quicktag view
	function add_quicktags()
	{
		$buttonshtml = '<input type="button" class="ed_button" onclick="Kimili.Flash.embed.apply(Kimili.Flash); return false;" title="Embed a Flash Movie in your post" value="Kimili Flash Embed" />';
?>
<script type="text/javascript" charset="utf-8">
// <![CDATA[
	(function(){
		
		if (typeof jQuery === 'undefined') {
			return;
		}
		
		jQuery(document).ready(function(){
			// Add the buttons to the HTML view
			jQuery("#ed_toolbar").append('<?php echo $buttonshtml; ?>');
		});
	}());
// ]]>
</script>
<?php	
	}
	
	// Set up the Plugin Options Page
	function options_menu() {
		add_options_page('Kimili Flash Embed Options', 'Kimili Flash Embed', 8, __FILE__, array(&$this, 'settings_page'));
	}
	
	// Render the settings page
	function settings_page() {
		
		$message = null;
		$message_updated = __("Kimili Flash Embed Options Updated.", 'kimili_flash_embed');
		
		// Create a link to the KFE JS
		wp_enqueue_script( 'kimiliflashembed', plugins_url('/kimili-flash-embed/js/kfe.js'), array(), $this->version );

		// update options
		if (isset($_POST['action']) && $_POST['action'] == 'kml_flashembed_update') {
			
			$filename				= preg_replace("/(^|&\S+;)|(<[^>]*>)/U", '', strip_tags($_POST['filename']));
			$target_class 			= preg_replace("/(^|&\S+;)|(<[^>]*>)/U", '', strip_tags($_POST['target_class']));
			$flash_id				= preg_replace("/(^|&\S+;)|(<[^>]*>)/U", '', strip_tags($_POST['flash_id']));
			
			$alt_content			= $_POST['alt_content'];
			$fvars					= $_POST['fvars'];
			
			$version_major 			= preg_replace("/\D/s", '', $_POST['version_major']);
			$version_minor 			= preg_replace("/\D/s", '', $_POST['version_minor']);
			$version_revision 		= preg_replace("/\D/s", '', $_POST['version_revision']);
			
			$width					= preg_replace("/[\D[^%]]/", '', $_POST['width']);
			$height					= preg_replace("/[\D[^%]]/", '', $_POST['height']);
			
			$bgcolor				= (preg_match("/^#?[0-9a-f]{6}$/i", $_POST['bgcolor'])) ? $_POST['bgcolor'] : "";
			$base					= preg_replace("/(^|&\S+;)|(<[^>]*>)/U", '', strip_tags($_POST['base']));
			
			if ($bgcolor != "" && !preg_match("/^#/", $bgcolor)) {
				$bgcolor = "#" . $bgcolor;
			}
			
			if (empty($version_major)) {
				$version_major = '8';
			}
						
			if (empty($version_minor)) {
				$version_minor = '0';
			}
			
			if (empty($version_revision)) {
				$version_revision = '0';
			}			
						
			if (empty($width)) {
				$width = '400';
			}		
								
			if (empty($height)) {
				$height = '300';
			}			
			
			$publish_method			= ($_POST['publish_method'] == '1') ? $_POST['publish_method'] : '0';
			$reference_swfobject 	= ($_POST['reference_swfobject'] == '0') ? $_POST['reference_swfobject'] : '1';
			$swfobject_source		= ($_POST['swfobject_source'] == '1') ? $_POST['swfobject_source'] : '0';
			$swfobject_use_autohide	= ($_POST['swfobject_use_autohide'] == '0') ? $_POST['swfobject_use_autohide'] : '1';
			$use_express_install	= ($_POST['use_express_install'] == '0') ? $_POST['use_express_install'] : '1';
			$dimensions_unit		= ($_POST['unit'] == 'percentage') ? $_POST['unit'] : 'pixels';
			
			$message = $message_updated;
			update_option('kml_flashembed_filename', $filename);
			update_option('kml_flashembed_target_class', $target_class);
			update_option('kml_flashembed_flash_id', $flash_id);
			update_option('kml_flashembed_publish_method', $publish_method);
			update_option('kml_flashembed_version_major', $version_major);
			update_option('kml_flashembed_version_minor', $version_minor);
			update_option('kml_flashembed_version_revision', $version_revision);
			update_option('kml_flashembed_alt_content', $alt_content);
			update_option('kml_flashembed_reference_swfobject', $reference_swfobject);
			update_option('kml_flashembed_swfobject_source', $swfobject_source);
			update_option('kml_flashembed_swfobject_use_autohide', $swfobject_use_autohide);
			update_option('kml_flashembed_width', $width);
			update_option('kml_flashembed_height', $height);
			update_option('kml_flashembed_dimensions_unit', $dimensions_unit);
			update_option('kml_flashembed_use_express_install', $use_express_install);
			update_option('kml_flashembed_align', $_POST['align']);
			update_option('kml_flashembed_play', $_POST['play']);
			update_option('kml_flashembed_loop', $_POST['loop']);
			update_option('kml_flashembed_menu', $_POST['menu']);
			update_option('kml_flashembed_quality', $_POST['quality']);
			update_option('kml_flashembed_scale', $_POST['scale']);
			update_option('kml_flashembed_salign', $_POST['salign']);
			update_option('kml_flashembed_wmode', $_POST['wmode']);
			update_option('kml_flashembed_bgcolor', $bgcolor);
			update_option('kml_flashembed_devicefont', $_POST['devicefont']);
			update_option('kml_flashembed_seamlesstabbing', $_POST['seamlesstabbing']);
			update_option('kml_flashembed_swliveconnect', $_POST['swliveconnect']);
			update_option('kml_flashembed_allowfullscreen', $_POST['allowfullscreen']);
			update_option('kml_flashembed_allowscriptaccess', $_POST['allowscriptaccess']);
			update_option('kml_flashembed_allownetworking', $_POST['allownetworking']);
			update_option('kml_flashembed_base', $base);
			update_option('kml_flashembed_fvars', $fvars);
			
			if (function_exists('wp_cache_flush')) {
				wp_cache_flush();
			}
		
		}
			
	?>
	
<?php if ($message) : ?>
<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
<?php endif; ?>

<style type="text/css" media="screen">
	h3 {
		background: #ddd;
		padding: 8px;
		margin: 2em 0 0;
		border-top: 1px solid #fff;
		border-bottom: 1px solid #aaa;
	}
	h2 + h3 {
		margin-top: 1em;
	}
	table.form-table {
		border-collapse: fixed;
	}
	table.form-table th[colspan] {
		background: #eee;
		border-top: 1px solid #fff;
		border-bottom: 1px solid #ccc;
		margin-top: 1em;
	}
	table.form-table th h4 {
		margin: 3px 0;
	}
	table.form-table th, 
	table.form-table td {
		padding: 5px 8px;
	}
	.info {
		border-bottom: 1px dotted #666;
		cursor: help;
	}
	
	/* Help */

	#publishingMethodHelp, #alternativeContentHelp, #replaceIdHelp, #SWFObjectReference, #toggleReplaceId, #isIdReq, #toggleAttsParamsContainer, #autohideHelp {
		display: none;
	}
	
	.help {
		margin: 0 0 10px;
		padding: 10px 10px 0;
		border: 1px solid #ccc;
		background-color: #ffc;
	}

	.help h4, .help h5, .help p, .help ul, .help li {
		margin: 0 !important;
		font-size: 11px;
		line-height: 14px;
	}

	.help h4, .help p, .help ul {
		padding-bottom: 10px;
	}

	.help h5 {
		color: #666;
	}
	
</style>

<script type="text/javascript" charset="utf-8">
	jQuery(document).ready(function(){
		try {
			Kimili.Flash.Generator.initialize();
		} catch(e) {}
	})
</script>
	
<form action="" method="post" accept-charset="utf-8">
	<div class="wrap">
		<h2><?php _e("Kimili Flash Embed Preferences", 'kimili-flash-embed'); ?></h2>

		<h3><?php _e("SWFObject Configuration Defaults", 'kimili-flash-embed'); ?></h3> 
		
		<table class="form-table">
			<tr>
				<th scope="row" style="vertical-align:top;"><?php _e("Publish Method", 'kimili-flash-embed'); ?></th>
				<td>
					<input type="radio" id="publish_method-0" name="publish_method" value="0" class="radio" <?php if (!get_option('kml_flashembed_publish_method')) echo "checked=\"checked\""; ?> /><label for="publish_method-0"><?php _e("Static Publishing", 'kimili-flash-embed'); ?></label>
					<input type="radio" id="publish_method-1" name="publish_method" value="1" class="radio" <?php if (get_option('kml_flashembed_publish_method')) echo "checked=\"checked\""; ?> /><label for="publish_method-1"><?php _e("Dynamic Publishing", 'kimili-flash-embed'); ?></label>
					<br />
					<a id="togglePublishingMethodHelp" href="#"><?php _e("what is this?",'kimili-flash-embed'); ?></a>
					<div id="publishingMethodHelp" class="help"> 
						<h4><?php _e("Static publishing",'kimili-flash-embed'); ?></h4> 
						<h5><?php _e("Description",'kimili-flash-embed'); ?></h5> 
						<p><?php _e("Embed Flash content and alternative content using standards compliant markup, and use unobtrusive JavaScript to resolve the issues that markup alone cannot solve.",'kimili-flash-embed'); ?></p> 
						<h5><?php _e("Pros",'kimili-flash-embed'); ?></h5> 
						<p><?php _e("The embedding of Flash content does not rely on JavaScript and the actual authoring of standards compliant markup is promoted.",'kimili-flash-embed'); ?></p> 
						<h5><?php _e("Cons",'kimili-flash-embed'); ?></h5> 
						<p><?php _e("Does not solve 'click-to-activate' mechanisms in Internet Explorer 6+ and Opera 9+.",'kimili-flash-embed'); ?></p> 
						<h4><?php _e("Dynamic publishing",'kimili-flash-embed'); ?></h4> 
						<h5><?php _e("Description",'kimili-flash-embed'); ?></h5> 
						<p><?php _e("Create alternative content using standards compliant markup and embed Flash content with unobtrusive JavaScript.",'kimili-flash-embed'); ?></p> 
						<h5><?php _e("Pros",'kimili-flash-embed'); ?></h5> 
						<p><?php _e("Avoids 'click-to-activate' mechanisms in Internet Explorer 6+ and Opera 9+.",'kimili-flash-embed'); ?></p> 
						<h5><?php _e("Cons",'kimili-flash-embed'); ?></h5> 
						<p><?php _e("The embedding of Flash content relies on JavaScript, so if you have the Flash plug-in installed, but have JavaScript disabled or use a browser that doesn't support JavaScript, you will not be able to see your Flash content, however you will see alternative content instead. Flash content will also not be shown on a device like Sony PSP, which has very poor JavaScript support, and automated tools like RSS readers are not able to pick up Flash content.",'kimili-flash-embed'); ?></p> 
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;">
					<label title="<?php _e("Flash version consists of major, minor and release version",'kimili-flash-embed'); ?>" class="info"><?php _e("Flash Version",'kimili-flash-embed'); ?></label>
				</th>
				<td>
					<input type="text" name="version_major" value="<?php echo get_option('kml_flashembed_version_major'); ?>" size="2" title="Major Version" />.
					<input type="text" name="version_minor" value="<?php echo get_option('kml_flashembed_version_minor'); ?>" size="2" title="Minor Version" />.
					<input type="text" name="version_revision" value="<?php echo get_option('kml_flashembed_version_revision'); ?>" size="3" title="Version Revision Number" />
				</td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;">
					<label for="expressInstall" title="<?php _e("Check checkbox to activate express install functionality on your swf.",'kimili-flash-embed'); ?>" class="info"><?php _e("Adobe Express Install",'kimili-flash-embed'); ?>:</label>
				</th>
				<td>
					<input type="radio" id="use_express_install-0" name="use_express_install" value="0" class="radio" <?php if (!get_option('kml_flashembed_use_express_install')) echo "checked=\"checked\""; ?> /><label for="use_express_install-0"><?php _e("No", 'kimili-flash-embed'); ?></label>
					<input type="radio" id="use_express_install-1" name="use_express_install" value="1" class="radio" <?php if (get_option('kml_flashembed_use_express_install')) echo "checked=\"checked\""; ?> /><label for="use_express_install-1"><?php _e("Yes", 'kimili-flash-embed'); ?></label>
				</td>
			</tr>
		</table>
		
		<h3><?php _e("SWF Definition Defaults",'kimili-flash-embed'); ?></h3>
		
		<table class="form-table">	
			<tr>
				<th scope="row" style="vertical-align:top;"><label for="swf" title="<?php _e("The relative or absolute path to your Flash content .swf file",'kimili-flash-embed'); ?>" class="info"><?php _e("Flash (.swf)",'kimili-flash-embed'); ?></label></th>
				<td><input type="text" name="filename" value="<?php echo get_option('kml_flashembed_filename'); ?>" /></td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;"><label title="<?php _e("Width &times; height (unit)",'kimili-flash-embed'); ?>" class="info"><?php _e("Dimensions",'kimili-flash-embed'); ?></label></th>
				<td>
					<input type="text" name="width" value="<?php echo get_option('kml_flashembed_width'); ?>" size="4" title="Width" />&times;
					<input type="text" name="height" value="<?php echo get_option('kml_flashembed_height'); ?>" size="4" title="Height" />
					<select id="unit" name="unit"> 
						<option <?php if (get_option('kml_flashembed_dimensions_unit') == "pixels") echo "selected=\"selected\""; ?>  value="pixels"><?php _e("pixels",'kimili-flash-embed'); ?></option> 
						<option <?php if (get_option('kml_flashembed_dimensions_unit') == "percentage") echo "selected=\"selected\""; ?>  value="percentage"><?php _e("percentage",'kimili-flash-embed'); ?></option> 
					</select>
				</td>
			</tr>
			<tr>
				<th colspan="2">
					<h4><?php _e("Attributes",'kimili-flash-embed'); ?></h4>
				</th>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;"><label for="flash_id" class="info" title="<?php _e("Uniquely identifies the Flash movie so that it can be referenced using a scripting language or by CSS",'kimili-flash-embed'); ?>"><?php _e("Flash content ID",'kimili-flash-embed'); ?></label></th>
				<td><input type="text" id="flash_id" name="flash_id" value="<?php echo get_option('kml_flashembed_flash_id'); ?>" /></td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;"><label for="target_class" class="info" title="<?php _e("Classifies the Flash movie so that it can be referenced using a scripting language or by CSS",'kimili-flash-embed'); ?>"><?php _e("class", 'kimili-flash-embed'); ?></label></th>
				<td><input type="text" id="target_class" name="target_class" value="<?php echo get_option('kml_flashembed_target_class'); ?>" /></td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;"><label for="align" class="info" title="<?php _e("HTML alignment of the object element. If this attribute is omitted, it by default centers the movie and crops edges if the browser window is smaller than the movie. NOTE: Using this attribute is not valid in XHTML 1.0 Strict.",'kimili-flash-embed'); ?>">align</label></th>
				<td>
					<select id="align" name="align"> 
						<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option>
		  				<option <?php if (get_option('kml_flashembed_align') == "left") echo "selected=\"selected\""; ?> value="left" >left</option> 
						<option <?php if (get_option('kml_flashembed_align') == "right") echo "selected=\"selected\""; ?> value="right">right</option> 
						<option <?php if (get_option('kml_flashembed_align') == "top") echo "selected=\"selected\""; ?> value="top">top</option> 
						<option <?php if (get_option('kml_flashembed_align') == "bottom") echo "selected=\"selected\""; ?> value="bottom">bottom</option> 
					</select>
				</td>
			</tr>
			
			<tr>
				<th colspan="2">
					<h4><?php _e("Parameters",'kimili-flash-embed'); ?></h4>
				</th>
			</tr>
			
			<tr>
				<th scope="row" style="vertical-align:top;"><label for="play" class="info" title="<?php _e("Specifies whether the movie begins playing immediately on loading in the browser. The default value is true if this attribute is omitted.",'kimili-flash-embed'); ?>">play</label></th>
				<td>
					<select id="play" name="play"> 
						<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option>
		  				<option <?php if (get_option('kml_flashembed_play') == "true") echo "selected=\"selected\""; ?> value="true" >true</option> 
						<option <?php if (get_option('kml_flashembed_play') == "false") echo "selected=\"selected\""; ?> value="false">false</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;"><label for="loop" class="info" title="<?php _e("Specifies whether the movie repeats indefinitely or stops when it reaches the last frame. The default value is true if this attribute is omitted",'kimili-flash-embed'); ?>.">loop</label></th>
				<td>
					<select id="loop" name="loop"> 
						<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option>
		  				<option <?php if (get_option('kml_flashembed_loop') == "true") echo "selected=\"selected\""; ?> value="true" >true</option> 
						<option <?php if (get_option('kml_flashembed_loop') == "false") echo "selected=\"selected\""; ?> value="false">false</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;"><label for="menu" class="info" title="<?php _e("Shows a shortcut menu when users right-click (Windows) or control-click (Macintosh) the SWF file. To show only About Flash in the shortcut menu, deselect this option. By default, this option is set to true.",'kimili-flash-embed'); ?>">menu</label></th>
				<td>
					<select id="menu" name="menu"> 
						<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option>
		  				<option <?php if (get_option('kml_flashembed_menu') == "true") echo "selected=\"selected\""; ?> value="true" >true</option> 
						<option <?php if (get_option('kml_flashembed_menu') == "false") echo "selected=\"selected\""; ?> value="false">false</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;"><label for="quality" class="info" title="<?php _e("Specifies the trade-off between processing time and appearance. The default value is 'high' if this attribute is omitted.",'kimili-flash-embed'); ?>">quality</label></th>
				<td>
					<select id="quality" name="quality"> 
						<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option>
						<option <?php if (get_option('kml_flashembed_quality') == "best") echo "selected=\"selected\""; ?> value="best">best</option> 
		  				<option <?php if (get_option('kml_flashembed_quality') == "high") echo "selected=\"selected\""; ?> value="high">high</option> 
						<option <?php if (get_option('kml_flashembed_quality') == "medium") echo "selected=\"selected\""; ?> value="medium">medium</option> 
						<option <?php if (get_option('kml_flashembed_quality') == "autohigh") echo "selected=\"selected\""; ?> value="autohigh">autohigh</option> 
						<option <?php if (get_option('kml_flashembed_quality') == "autolow") echo "selected=\"selected\""; ?> value="autolow">autolow</option> 
						<option <?php if (get_option('kml_flashembed_quality') == "low") echo "selected=\"selected\""; ?> value="low">low</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;"><label for="scale" class="info"  title="<?php _e("Specifies scaling, aspect ratio, borders, distortion and cropping for if you have changed the document's original width and height.",'kimili-flash-embed'); ?>">scale</label></th>
				<td>
					<select id="scale" name="scale"> 
						<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option>
						<option <?php if (get_option('kml_flashembed_scale') == "showall") echo "selected=\"selected\""; ?> value="showall">showall</option> 
		  				<option <?php if (get_option('kml_flashembed_scale') == "noborder") echo "selected=\"selected\""; ?> value="noborder">noborder</option> 
						<option <?php if (get_option('kml_flashembed_scale') == "exactfit") echo "selected=\"selected\""; ?> value="exactfit">exactfit</option> 
						<option <?php if (get_option('kml_flashembed_scale') == "noscale") echo "selected=\"selected\""; ?> value="noscale">noscale</option> 
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;"><label for="salign" class="info" title="<?php _e("Specifies where the content is placed within the application window and how it is cropped.",'kimili-flash-embed'); ?>">salign</label></th>
				<td>
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
				</td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;"><label for="wmode" class="info" title="<?php _e("Sets the Window Mode property of the Flash movie for transparency, layering, and positioning in the browser. The default value is 'window' if this attribute is omitted.",'kimili-flash-embed'); ?>">wmode</label></th>
				<td>
					<select id="wmode" name="wmode"> 
						<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option>
						<option <?php if (get_option('kml_flashembed_wmode') == "window") echo "selected=\"selected\""; ?> value="window">window</option> 
			  			<option <?php if (get_option('kml_flashembed_wmode') == "opaque") echo "selected=\"selected\""; ?> value="opaque">opaque</option> 
						<option <?php if (get_option('kml_flashembed_wmode') == "transparent") echo "selected=\"selected\""; ?> value="transparent">transparent</option> 
						<option <?php if (get_option('kml_flashembed_wmode') == "direct") echo "selected=\"selected\""; ?> value="direct">direct</option> 
						<option <?php if (get_option('kml_flashembed_wmode') == "gpu") echo "selected=\"selected\""; ?> value="gpu">gpu</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;"><label for="bgcolor" class="info" title="<?php _e("Hexadecimal RGB value in the format #RRGGBB, which specifies the background color of the movie, which will override the background color setting specified in the Flash file.",'kimili-flash-embed'); ?>">bgcolor</label></th>
				<td><input type="text" id="bgcolor" name="bgcolor" value="<?php echo get_option('kml_flashembed_bgcolor'); ?>" /></td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;"><label for="devicefont" class="info" title="<?php _e("Specifies whether static text objects that the Device Font option has not been selected for will be drawn using device fonts anyway, if the necessary fonts are available from the operating system.",'kimili-flash-embed'); ?>">devicefont</label></th>
				<td>
					<select id="devicefont" name="devicefont"> 
						<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option>
		  				<option <?php if (get_option('kml_flashembed_devicefont') == "true") echo "selected=\"selected\""; ?> value="true" >true</option> 
						<option <?php if (get_option('kml_flashembed_devicefont') == "false") echo "selected=\"selected\""; ?> value="false">false</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;"><label for="seamlesstabbing" class="info" title="<?php _e("Specifies whether users are allowed to use the Tab key to move keyboard focus out of a Flash movie and into the surrounding HTML (or the browser, if there is nothing focusable in the HTML following the Flash movie). The default value is true if this attribute is omitted.",'kimili-flash-embed'); ?>">seamlesstabbing</label></th>
				<td>
					<select id="seamlesstabbing" name="seamlesstabbing"> 
						<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option>
		  				<option <?php if (get_option('kml_flashembed_seamlesstabbing') == "true") echo "selected=\"selected\""; ?> value="true" >true</option> 
						<option <?php if (get_option('kml_flashembed_seamlesstabbing') == "false") echo "selected=\"selected\""; ?> value="false">false</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;"><label for="seamlesstabbing" class="info" title="<?php _e("Specifies whether the browser should start Java when loading the Flash Player for the first time. The default value is false if this attribute is omitted. If you use JavaScript and Flash on the same page, Java must be running for the FSCommand to work.",'kimili-flash-embed'); ?>">swliveconnect</label></th>
				<td>
					<select id="swliveconnect" name="swliveconnect"> 
						<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option>
						<option <?php if (get_option('kml_flashembed_swliveconnect') == "true") echo "selected=\"selected\""; ?> value="true">true</option> 
		  				<option <?php if (get_option('kml_flashembed_swliveconnect') == "false") echo "selected=\"selected\""; ?> value="false">false</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;"><label for="allowfullscreen" class="info" title="<?php _e("Enables full-screen mode. The default value is false if this attribute is omitted. You must have version 9,0,28,0 or greater of Flash Player installed to use full-screen mode.",'kimili-flash-embed'); ?>">allowfullscreen</label></th>
				<td>
					<select id="allowfullscreen" name="allowfullscreen"> 
						<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option>
		  				<option <?php if (get_option('kml_flashembed_allowfullscreen') == "true") echo "selected=\"selected\""; ?> value="true" >true</option> 
						<option <?php if (get_option('kml_flashembed_allowfullscreen') == "false") echo "selected=\"selected\""; ?> value="false">false</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;"><label for="allowscriptaccess" class="info" title="<?php _e("Controls the ability to perform outbound scripting from within a Flash SWF. The default value is 'always' if this attribute is omitted.",'kimili-flash-embed'); ?>">allowscriptaccess</label></th>
				<td>
					<select id="allowscriptaccess" name="allowscriptaccess"> 
						<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option>
						<option <?php if (get_option('kml_flashembed_allowscriptaccess') == "always") echo "selected=\"selected\""; ?> value="always">always</option> 
						<option <?php if (get_option('kml_flashembed_allowscriptaccess') == "sameDomain") echo "selected=\"selected\""; ?> value="sameDomain">sameDomain</option> 
		  				<option <?php if (get_option('kml_flashembed_allowscriptaccess') == "never") echo "selected=\"selected\""; ?> value="never">never</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;"><label for="allownetworking" class="info" title="<?php _e("Controls a SWF file's access to network functionality. The default value is 'all' if this attribute is omitted.",'kimili-flash-embed'); ?>">allownetworking</label></th>
				<td>
					<select id="allownetworking" name="allownetworking"> 
						<option value=""><?php _e("Choose",'kimili-flash-embed'); ?>...</option>
						<option <?php if (get_option('kml_flashembed_allownetworking') == "all") echo "selected=\"selected\""; ?> value="all">all</option> 
						<option <?php if (get_option('kml_flashembed_allownetworking') == "internal") echo "selected=\"selected\""; ?> value="internal">internal</option> 
		  				<option <?php if (get_option('kml_flashembed_allownetworking') == "none") echo "selected=\"selected\""; ?> value="none">none</option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;"><label for="base" class="info" title="<?php _e("Specifies the base directory or URL used to resolve all relative path statements in the Flash Player movie. This attribute is helpful when your Flash Player movies are kept in a different directory from your other files.",'kimili-flash-embed'); ?>">base</label></th>
				<td><input type="text" id="base" name="base" value="<?php echo get_option('kml_flashembed_base'); ?>" /></td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;"><label for="fvars" class="info" title="<?php _e("Method to pass variables to a Flash movie. You need to separate individual name/variable pairs with a semicolon (i.e. name=John Doe ; count=3).",'kimili-flash-embed'); ?>">fvars</label></th>
				<td><textarea name="fvars" id="fvars" cols="50" rows="4"><?php echo stripcslashes(get_option('kml_flashembed_fvars')); ?></textarea></td>
			</tr>
		</table>
		
		<h3><?php _e("Alternative Content Default", 'kimili-flash-embed'); ?></h3> 
		
		<table class="form-table">
			<tr>
				<th scope="row" style="vertical-align:top;"><?php _e("Alternate Content", 'kimili-flash-embed'); ?></th>
				<td>
					<textarea name="alt_content" cols="50" rows="4"><?php echo stripcslashes(get_option('kml_flashembed_alt_content')); ?></textarea>
					<br />
					<a id="toggleAlternativeContentHelp" href="#alternativeContentHelp"><?php _e("what is this",'kimili-flash-embed'); ?>?</a>
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
				</td>
			</tr>
		</table>

		<h3><?php _e("Javascript Options", 'kimili-flash-embed'); ?></h3> 
		
		<table class="form-table">
			<tr>
				<th scope="row" style="vertical-align:top;"><?php _e("Create a reference to SWFObject.js?", 'kimili-flash-embed'); ?></th>
				<td>
					<input type="radio" id="reference_swfobject-0" name="reference_swfobject" value="0" class="radio" <?php if (!get_option('kml_flashembed_reference_swfobject')) echo "checked=\"checked\""; ?> /><label for="reference_swfobject-0"><?php _e("No", 'kimili-flash-embed'); ?></label>
					<input type="radio" id="reference_swfobject-1" name="reference_swfobject" value="1" class="radio" <?php if (get_option('kml_flashembed_reference_swfobject')) echo "checked=\"checked\""; ?> /><label for="reference_swfobject-1"><?php _e("Yes", 'kimili-flash-embed'); ?></label>
				</td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;"><?php _e("Where do you want to reference SWFObject.js from?", 'kimili-flash-embed'); ?></th>
				<td>
					<input type="radio" id="swfobject_source-0" name="swfobject_source" value="0" class="radio" <?php if (!get_option('kml_flashembed_swfobject_source')) echo "checked=\"checked\""; ?> /><label for="swfobject_source-0"><?php _e("Google Ajax Library", 'kimili-flash-embed'); ?></label>
					<input type="radio" id="swfobject_source-1" name="swfobject_source" value="1" class="radio" <?php if (get_option('kml_flashembed_swfobject_source')) echo "checked=\"checked\""; ?> /><label for="swfobject_source-1"><?php _e("Internal", 'kimili-flash-embed'); ?></label>
					<br />
					<a id="toggleSWFObjectReference" href="#SWFObjectReference"><?php _e("what is this",'kimili-flash-embed'); ?>?</a>
					<div id="SWFObjectReference" class="help">
						<p>
							<?php _e("If you choose to use Kimili Flash Embed to create a reference to swfobject.js (which is necessary for KFE to function properly), you have two options from where to reference the file:",'kimili-flash-embed'); ?>
						</p>
						<h4><?php _e("Google Ajax Library", 'kimili-flash-embed'); ?></h4>
						<p><?php _e("The Google Ajax Library is a content distribution network the most popular open source JavaScript libraries, including SWFObject. Google hosts these libraries, correctly sets cache headers, and stays up to date with the most recent release versions.", 'kimili-flash-embed'); ?></p>
						<p><?php _e("Choosing this option offers fast, reliable access to the SWFObject code. It also increases the chances that your users may already have SWFObject cached in their browsers if they have visited other sites that also utilize the Google hosted copy of SWFObject, making your site load even faster.", 'kimili-flash-embed'); ?></p>
						<h4><?php _e("Internal", 'kimili-flash-embed'); ?></h4>
						<p><?php _e("If you'd rather not rely on an external service to serve SWFObject to your users, you can choose to reference a copy of SWFObject which comes bundles with Kimili Flash Embed so it is served from the same server as the rest of your website.", 'kimili-flash-embed'); ?></p>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row" style="vertical-align:top;">
					<?php _e("Do you want to use SWFObject's autohide function?", 'kimili-flash-embed'); ?></th>
				<td>
					<input type="radio" id="swfobject_use_autohide-0" name="swfobject_use_autohide" value="0" class="radio" <?php if (!get_option('kml_flashembed_swfobject_use_autohide')) echo "checked=\"checked\""; ?> /><label for="swfobject_use_autohide-0"><?php _e("No", 'kimili-flash-embed'); ?></label>
					<input type="radio" id="swfobject_use_autohide-1" name="swfobject_use_autohide" value="1" class="radio" <?php if (get_option('kml_flashembed_swfobject_use_autohide')) echo "checked=\"checked\""; ?> /><label for="swfobject_use_autohide-1"><?php _e("Yes", 'kimili-flash-embed'); ?></label>
					<br />
					<a id="toggleAutohideHelp" href="#autohideHelp"><?php _e("what is this",'kimili-flash-embed'); ?>?</a>
					<div id="autohideHelp" class="help">
						<p><?php _e("By default, SWFObject temporarily hides your SWF or alternative content until the library has decided which content to display. This option allows you to disable that behavior.", 'kimili-flash-embed'); ?></p>
					</div>
				</td>
			</tr>
		</table>
		
		<p class="submit">
			<input type="hidden" name="action" value="kml_flashembed_update" /> 
			<input type="submit" name="Submit" value="<?php _e("Update Options", 'kimili-flash-embed'); ?> &raquo;" /> 
		</p>

	</div>
	
</form>
	<?php
		
	}
	
}



// Start it up - on template_redirect for feeds, plugins_loaded for everything else.
add_action( (preg_match("/(\/\?feed=|\/feed)/i",$_SERVER['REQUEST_URI'])) ? 'template_redirect' : 'plugins_loaded', 'KimiliFlashEmbed' );

function KimiliFlashEmbed() {
	global $KimiliFlashEmbed;
	$KimiliFlashEmbed = new KimiliFlashEmbed();
}

/*
	Adding the KFE button to the MCE toolbar. For some reason, WP 2.6 doesn't allow me to do this from within the KimiliFlashEmbed class.
*/
add_action( 'init', 'kml_flashembed_addbuttons');

function kml_flashembed_addbuttons() {
	if (!current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
		return;
	}
	if ( get_user_option('rich_editing') == 'true') {
		add_filter( 'tiny_mce_version', 'tiny_mce_version', 0 );
		add_filter( 'mce_external_plugins', 'kml_flashembed_plugin', 0 );
		add_filter( 'mce_buttons', 'kml_flashembed_button', 0);
	}
}

// Break the browser cache of TinyMCE
function tiny_mce_version( $version ) {
	global $KimiliFlashEmbed;
	return $version . '-kfe' . $KimiliFlashEmbed->version;
}

// Load the custom TinyMCE plugin
function kml_flashembed_plugin( $plugins ) {
	$plugins['kimiliflashembed'] = plugins_url('/kimili-flash-embed/lib/tinymce3/editor_plugin.js');
	return $plugins;
}

function kml_flashembed_button( $buttons ) {
	array_push( $buttons, 'separator', 'kimiliFlashEmbed' );
	return $buttons;
}

?>
