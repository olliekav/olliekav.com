<?php
/*
Plugin Name: Last.Fm Records
Plugin URI: http://jeroensmeets.net/lastfmrecords/
Description: The Last.Fm Records plugin lets you show what you are listening to, with a little help from our friends at last.fm.
Author: Jeroen Smeets
Author URI: http://jeroensmeets.net/
Version: 1.6.2
License:  GPL2
*/

////////////////////////////////////
// Enqueue scripts and stylesheet //
////////////////////////////////////

function lfr_add_javascript() {
  wp_enqueue_script('lastfmrecords', plugins_url('/last.fm.records.js', __FILE__), 'jquery');
  // use wordpress offset
  $options = get_option('lastfm-records');
  $options['offset'] = get_option('gmt_offset');
  wp_localize_script('lastfmrecords', 'lfr_config', $options);
}

add_action('init', 'lfr_add_javascript');

function lfr_add_stylesheet() {
	$options = get_option('lastfm-records');
	$_stylesheet = (!$options['stylesheet']) ? 2 : $options['stylesheet'];

?>
  <script type='text/javascript'>
    jQuery(document).ready( function() {
      lastFmRecords.init(lfr_config);
    });
  </script>
<?php

	// display stylesheet? version 1.5.3 had 0 or 1, so we build from there
	switch($_stylesheet) {
		case 1:
?>
  <style type="text/css">
    #lastfmrecords        { padding: 0px; padding-bottom: 10px; }

    /* thx to http://cssglobe.com/lab/overflow_thumbs/ */
    #lastfmrecords ol,
      #lastfmrecords li        { margin: 0; padding: 0; list-style: none; }
    #lastfmrecords li          { float: left; margin: 0px 5px 5px 0px; }
    #lastfmrecords a           { display: block; float: left; width: <?php echo $options['imgwidth']; ?>px; height: <?php echo $options['imgwidth']; ?>px; line-height: <?php echo $options['imgwidth']; ?>px; overflow: hidden; position: relative; z-index: 1; }
    #lastfmrecords a img       { float: left; position: absolute; margin: auto; min-height: <?php echo $options['imgwidth']; ?>px; }
    /* mouse over */
    #lastfmrecords a:hover     { overflow:visible; z-index:1000; border:none; }
    #lastfmrecords a:hover img { border: 1px  solid #999; background: #fff; padding: 3px; margin-top: -20px; margin-left: -20px; min-height: <?php echo $options['imgwidth'] + 20; ?>px;  }
  </style>
<?php
		break;
		case 2:
?>
  <style type="text/css">
    #lastfmrecords             { padding: 0px; padding-bottom: 10px; }
    #lastfmrecords ol,
      #lastfmrecords li        { margin: 0; padding: 0; list-style: none; }
    #lastfmrecords li          { display: inline; margin: 0px 5px 5px 0px; }
    #lastfmrecords a img       { width: <?php echo $options['imgwidth']; ?>px; height: <?php echo $options['imgwidth']; ?>px; }
  </style>
<?php
		break;
		case 3:
		break;
	}
}

# add stylesheet and scripts to head
add_action('wp_head', 'lfr_add_stylesheet');

//////////////////////////////////////////////
// Add link to settings in 'Manage plugins' //
//////////////////////////////////////////////

function set_plugin_meta($links, $file) {
  $plugin = basename(__FILE__);
  // create link
  if (basename($file) == $plugin) {
    return array_merge(
      array('<a href="options-general.php?page=' . $plugin . '">' . __('Settings') . '</a>'),
      $links
    );
  }
  return $links;
}

add_filter('plugin_action_links', 'set_plugin_meta', 10, 2);

////////////
// Widget //
////////////

class LastFmRecordsWidget extends WP_Widget {

	function LastFmRecordsWidget() {
		parent::WP_Widget(false, $name = 'LastFmRecords');	
	}

	function widget($args, $instance) {		
		extract($args);
		$options = get_option('lastfm-records');

		echo "\n\n" . $before_widget . $before_title . $instance['title'] . $after_title . "\n";
		echo "<div id='lastfmrecords'></div>\n";
		echo $after_widget . "\n\n";
	}

	function update($new_instance, $old_instance) {				
		return $new_instance;
	}

	function form($instance) {				
		$title = esc_attr($instance['title']);
?>
            <p>
              <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
              </label>
            </p>
<?php 
  }
} // class LastFmRecordsWidget

// register LastFmRecords widget
add_action('widgets_init', create_function('', 'return register_widget("LastFmRecordsWidget");'));

//////////////////
// Settings API //
//////////////////

class LastfmRecords {

  function init() {
    register_setting('Last_fm_Records', 'lastfm-records', array('LastfmRecords', 'validate'));

    // settings for Last.fm
    add_settings_section('lastfm-section', 'Last.fm' , array('LastfmRecords', 'section_lastfm'), basename(__FILE__));

    add_settings_field('username', 'Last.fm Username',  array('LastfmRecords', 'setting_username'), basename(__FILE__), 'lastfm-section');
    add_settings_field('period', 'Period to get data for',  array('LastfmRecords', 'setting_period'), basename(__FILE__), 'lastfm-section');

    // settings for displaying
    add_settings_section('visuals-section', 'Visuals' , array('LastfmRecords', 'section_visuals'), basename(__FILE__));

    add_settings_field('stylesheet', 'Add some style',  array('LastfmRecords', 'setting_stylesheet'), basename(__FILE__), 'visuals-section');
    add_settings_field('count', 'Number of covers',  array('LastfmRecords', 'setting_count'), basename(__FILE__), 'visuals-section');
    add_settings_field('imgwidth', 'Image width (pixels)',  array('LastfmRecords', 'setting_imgwidth'), basename(__FILE__), 'visuals-section');
    add_settings_field('defaultthumb', 'Default Thumbnail (url)', array('LastfmRecords', 'setting_defaultthumb'), basename(__FILE__), 'visuals-section');

    // optional settings
    add_settings_section('optional-section', 'Optional settings' , array('LastfmRecords', 'section_optional'), basename(__FILE__));

    add_settings_field('linknewscreen', 'Open links in new window',  array('LastfmRecords', 'setting_linknewscreen'), basename(__FILE__), 'optional-section');
    add_settings_field('refresh', 'Refresh covers every x minutes',  array('LastfmRecords', 'setting_refresh'), basename(__FILE__), 'optional-section');
    add_settings_field('offset', 'Your timezone',  array('LastfmRecords', 'setting_offset'), basename(__FILE__), 'optional-section');
    add_settings_field('debug', 'Show debug info',  array('LastfmRecords', 'setting_debug'), basename(__FILE__), 'optional-section');


  }

  function admin_menu() {
    if (!function_exists('current_user_can') || !current_user_can('manage_options')) {
      return;
    }

    if (function_exists('add_options_page')) {
      add_options_page('Last.fm Records', 'Last.fm Records', 'manage_options', basename(__FILE__), array('LastfmRecords', 'showform'));
    }
  }

  function showform() {
    $options = get_option('lastfm-records');
?>
        <div class="wrap">
          <?php screen_icon("options-general"); ?>
          <h2>Last.fm Records</h2>
          <form action="options.php" method="post">
            <?php settings_fields('Last_fm_Records'); ?>
            <?php do_settings_sections(basename(__FILE__)); ?>
            <p class="submit">
              <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
            </p>
          </form>
          <?php LastfmRecords::show_donate_button(); ?>
        </div> 
<?php 
  }

  function validate($input) {
    return $input;
  }

  function section_lastfm() {
    // echo "Please fill in your last.fm username and the period you want to show covers for. If you can want, you can overrule the image last.fm uses when no cover is available.";
  }

  function section_visuals() {
    // echo "Here you can specify how the covers will be displayed.";
  }

  function section_optional() {
    // echo "These settings are not necessary for the plugin to function. Yet, if you played with Lego when you were young, you might want to play with them.";
  }

  function setting_username() {
	$options = get_option('lastfm-records');
	echo "<input id='plugin_username' name='lastfm-records[username]' size='40' type='text' value='{$options['username']}' />";
  }

  function setting_defaultthumb() {
	$options = get_option('lastfm-records');
    $cover   = ('' != trim($options['defaultthumb'])) ? "<br /><img src='" . $options['defaultthumb'] . "' style='margin-top: 10px; max-height: 80px; border: 1px solid #ddd;' />" : "";

	echo "<input id='plugin_defaultthumb' name='lastfm-records[defaultthumb]' size='40' type='text' value='{$options['defaultthumb']}' />" . $cover;
  }

  function setting_count() {
	$options = get_option('lastfm-records');
	echo "<input id='plugin_count' name='lastfm-records[count]' size='10' type='text' value='{$options['count']}' />";
  }

  function setting_imgwidth() {
	$options = get_option('lastfm-records');
	echo "<input id='plugin_imgwidth' name='lastfm-records[imgwidth]' size='10' type='text' value='{$options['imgwidth']}' />";
  }

  function setting_period() {
	$options = get_option('lastfm-records');
	$items = array(

	  array('recenttracks', 'Recent tracks'),

	  array('lovedtracks', 'Loved tracks'),

	  array('tracks7day', 'Tracks -- last 7 days'),
	  array('tracks3month', 'Tracks -- last 3 months'),
	  array('tracks6month', 'Tracks -- last 6 months'),
	  array('tracks12month', 'Tracks -- last 12 months'),
	  array('tracksoverall', 'Tracks -- all time'),

	  array('topalbums7day', 'Albums -- last 7 days'),
	  array('topalbums3month', 'Albums -- last 3 months'),
	  array('topalbums6month', 'Albums -- last 6 months'),
	  array('topalbums12month', 'Albums -- last 12 months'),
	  array('topalbumsoverall', 'Top albums -- all time')
	);
	echo "<select id='plugin_period' name='lastfm-records[period]'>\n";
	foreach($items as $item) {
		$selected = ($options['period'] == $item[0]) ? 'selected="selected"' : '';
		echo "<option value='" . $item[0] . "' " . $selected . ">" . $item[1] . "</option>\n";
	}
	echo "</select>\n";
  }

  function setting_stylesheet() {
	$options = get_option('lastfm-records');
	$_stylesheet = (!$options['stylesheet']) ? 2 : $options['stylesheet'];

	$items = array(
	  array('0', 'None'),
	  array('2', 'Plain and simple'),
	  array('1', 'Fancy hovering effect')
	);
	echo "<select id='plugin_stylesheet' name='lastfm-records[stylesheet]'>\n";
	foreach($items as $item) {
		$selected = ($_stylesheet == $item[0]) ? 'selected="selected"' : '';
		echo "<option value='" . $item[0] . "' " . $selected . ">" . $item[1] . "</option>\n";
	}
	echo "</select>\n";  }

  function setting_debug() {
	$options = get_option('lastfm-records');
	$items = array(
	  array('0', 'No'),
	  array('1', 'Yes')
	);
	echo "<select id='plugin_debug' name='lastfm-records[debug]'>\n";
	foreach($items as $item) {
		$selected = ($options['debug'] == $item[0]) ? 'selected="selected"' : '';
		echo "<option value='" . $item[0] . "' " . $selected . ">" . $item[1] . "</option>\n";
	}
	echo "</select>\n";
	echo "<br /><i>If your browser supports it, you can view debug info in the javascript console. For a slightly better performance, keep this set to 'No'.</i>";
  }

  function setting_linknewscreen() {
	$options = get_option('lastfm-records');
	$items = array(
	  array('0', 'No'),
	  array('1', 'Yes')
	);
	echo "<select id='plugin_linknewscreen' name='lastfm-records[linknewscreen]'>\n";
	foreach($items as $item) {
		$selected = ($options['linknewscreen'] == $item[0]) ? 'selected="selected"' : '';
		echo "<option value='" . $item[0] . "' " . $selected . ">" . $item[1] . "</option>\n";
	}
	echo "</select>\n";
  }

  function setting_refresh() {
	$options = get_option('lastfm-records');
	echo "<input id='plugin_refresh' name='lastfm-records[refresh]' size='10' type='text' value='{$options['refresh']}' /><br /><i>This setting only works when 'period' is set to Recent Tracks.</i>";
  }

  function setting_offset() {
    if (get_option('gmt_offset') < 0) {
      echo "gmt -" . get_option('gmt_offset');
    } else if (get_option('gmt_offset') > 0) {
      echo "gmt +" . get_option('gmt_offset');
    } else {
      echo "You're on gmt";
    }
    echo "<br /><i>The plugin uses the <a href='" . get_admin_url() . "options-general.php'>WordPress setting</a>.</i>";
    
  }

  function show_donate_button() {
?>
<div class="updated">
  <p>
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float: right; margin: 0px 20px;">
      <input type="hidden" name="cmd" value="_s-xclick">
      <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
      <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
      <img alt="" border="0" src="https://www.paypal.com/nl_NL/i/scr/pixel.gif" width="1" height="1">
    </form>
  If you really like this plugin, you can send me some Amazon spending money through Paypal. Or better: design or develop something great for Wordpress! All it takes is an idea.
  </p>
</div>
<?php
  }

  // class is over
}
 
add_action('admin_init', array('LastfmRecords', 'init'));
add_action('admin_menu', array('LastfmRecords', 'admin_menu'));

?>